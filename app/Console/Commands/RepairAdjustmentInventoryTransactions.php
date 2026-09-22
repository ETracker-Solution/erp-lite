<?php

namespace App\Console\Commands;

use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairAdjustmentInventoryTransactions extends Command
{
    protected $signature = 'inventory:repair-adjustment-transactions
                            {--type=FG : Adjustment type FG or RM}
                            {--dry-run : List mismatches only (default behavior unless --fix)}
                            {--fix : Delete wrong inventory_transactions and recreate from items}
                            {--force : Skip confirmation when using --fix}
                            {--id= : Repair a single inventory_adjustments.id}
                            {--from-date= : Only adjustments on/after this date (Y-m-d)}
                            {--to-date= : Only adjustments on/before this date (Y-m-d)}';

    protected $description = 'Find multi-item FG/RM adjustments whose inventory_transactions do not match items, and optionally repair them';

    public function handle(): int
    {
        $type = strtoupper((string) $this->option('type'));
        if (! in_array($type, ['FG', 'RM'], true)) {
            $this->error('Invalid --type. Use FG or RM.');

            return self::FAILURE;
        }

        $docType = $type === 'FG' ? 'FGIA' : 'RMIA';
        $shouldFix = (bool) $this->option('fix');
        // Default is dry-run unless --fix is passed.
        $dryRun = ! $shouldFix || (bool) $this->option('dry-run');

        $query = InventoryAdjustment::query()
            ->with(['items:id,inventory_adjustment_id,coi_id,quantity,rate'])
            ->where('type', $type)
            ->where('status', '!=', 'cancelled')
            ->has('items', '>=', 2);

        if ($this->option('id')) {
            $query->where('id', (int) $this->option('id'));
        }
        if ($this->option('from-date')) {
            $query->whereDate('date', '>=', $this->option('from-date'));
        }
        if ($this->option('to-date')) {
            $query->whereDate('date', '<=', $this->option('to-date'));
        }

        $adjustments = $query->orderBy('id')->get();
        $broken = [];

        foreach ($adjustments as $adjustment) {
            $items = $adjustment->items;
            if ($items->count() < 2) {
                continue;
            }

            $transactions = InventoryTransaction::query()
                ->where('doc_type', $docType)
                ->where('doc_id', $adjustment->id)
                ->orderBy('id')
                ->get(['id', 'coi_id', 'quantity', 'rate', 'amount', 'type']);

            if ($this->isMismatch($items, $transactions)) {
                $broken[] = [
                    'adjustment' => $adjustment,
                    'items' => $items,
                    'transactions' => $transactions,
                ];
            }
        }

        if ($broken === []) {
            $this->info("No mismatched {$type} adjustments found.");

            return self::SUCCESS;
        }

        $this->warn('Found '.count($broken)." mismatched {$type} adjustment(s):");
        $rows = [];
        foreach ($broken as $entry) {
            /** @var InventoryAdjustment $adj */
            $adj = $entry['adjustment'];
            $itemSig = $entry['items']->map(fn ($i) => $i->coi_id.':'.$i->quantity)->implode(' | ');
            $txSig = $entry['transactions']->map(fn ($t) => $t->coi_id.':'.$t->quantity)->implode(' | ');
            $rows[] = [
                $adj->id,
                $adj->uid,
                $adj->date,
                $adj->store_id,
                $adj->transaction_type,
                $entry['items']->count(),
                $entry['transactions']->count(),
                $itemSig,
                $txSig,
            ];
        }
        $this->table(
            ['ID', 'UID', 'Date', 'Store', 'TxnType', 'Items', 'Txs', 'Items (coi:qty)', 'Txs (coi:qty)'],
            $rows
        );

        if ($dryRun) {
            $this->comment('Dry-run only. Re-run with --fix to rebuild inventory_transactions from items.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Rebuild inventory_transactions for these adjustments from items?', false)) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        $fixed = 0;
        $storeIds = [];

        DB::transaction(function () use ($broken, $docType, &$fixed, &$storeIds) {
            foreach ($broken as $entry) {
                /** @var InventoryAdjustment $adjustment */
                $adjustment = $entry['adjustment'];
                $stockType = $adjustment->transaction_type === 'increase' ? 1 : -1;

                InventoryTransaction::query()
                    ->where('doc_type', $docType)
                    ->where('doc_id', $adjustment->id)
                    ->delete();

                foreach ($entry['items'] as $item) {
                    $qty = (float) $item->quantity;
                    $rate = (float) $item->rate;
                    InventoryTransaction::query()->create([
                        'store_id' => $adjustment->store_id,
                        'doc_type' => $docType,
                        'doc_id' => $adjustment->id,
                        'quantity' => $qty,
                        'rate' => $rate,
                        'amount' => $qty * $rate,
                        'date' => $adjustment->date,
                        'type' => $stockType,
                        'coi_id' => (int) $item->coi_id,
                    ]);
                }

                $storeIds[(int) $adjustment->store_id] = true;
                $fixed++;
            }
        });

        foreach (array_keys($storeIds) as $storeId) {
            forgetPosTransactionAbleStockMap((int) $storeId);
        }

        $this->info("Repaired {$fixed} adjustment(s). Cleared POS stock cache for ".count($storeIds).' store(s).');
        $this->comment('Note: account_transactions were not changed. Review GL separately if needed.');

        return self::SUCCESS;
    }

    private function isMismatch($items, $transactions): bool
    {
        if ($transactions->count() !== $items->count()) {
            return true;
        }

        $itemBag = $items
            ->map(fn ($i) => ((int) $i->coi_id).'|'.round((float) $i->quantity, 2).'|'.round((float) $i->rate, 2))
            ->sort()
            ->values()
            ->all();

        $txBag = $transactions
            ->map(fn ($t) => ((int) $t->coi_id).'|'.round((float) $t->quantity, 2).'|'.round((float) $t->rate, 2))
            ->sort()
            ->values()
            ->all();

        return $itemBag !== $txBag;
    }
}
