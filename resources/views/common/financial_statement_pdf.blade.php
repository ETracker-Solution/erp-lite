<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #222; }
        table { width: 100%; border-collapse: collapse; margin-top: 2px; }
        th {
            background: #d9d9d9;
            text-align: left;
            padding: 5px 6px;
            border-bottom: 1px solid #888;
            font-size: 10px;
        }
        th.num { text-align: right; }
        td {
            padding: 3px 6px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
            font-size: 9.5px;
        }
        td.num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
        tr.section td {
            font-weight: bold;
            font-size: 11px;
            background: #333;
            color: #fff;
            border-bottom: none;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        tr.group td {
            font-weight: bold;
            background: #f0f0f0;
            border-bottom: 1px solid #ccc;
        }
        tr.ledger td {
            font-weight: normal;
            background: #fff;
            color: #333;
        }
        tr.total td {
            font-weight: bold;
            border-top: 1px solid #222;
            border-bottom: 2px solid #222;
            background: #e8e8e8;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .tree-mark { color: #999; font-size: 8px; }
        .hint { font-size: 8px; color: #777; margin-top: 10px; }
    </style>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th style="width: {{ !empty($showDebitCredit) ? '70%' : '72%' }};">Account</th>
            @if(!empty($showDebitCredit))
                <th class="num" style="width: 15%;">Debit</th>
                <th class="num" style="width: 15%;">Credit</th>
            @else
                <th class="num" style="width: 28%;">Amount</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @forelse($getData as $row)
            @php
                $level = (int) ($row['level'] ?? 0);
                $isSection = !empty($row['is_section']);
                $isGroup = !empty($row['is_group']) && !$isSection;
                $isTotal = !empty($row['is_total']);
                $pad = $isSection ? 6 : (6 + ($level * 16));

                if ($isSection) {
                    $rowClass = 'section';
                } elseif ($isTotal) {
                    $rowClass = 'total';
                } elseif ($isGroup) {
                    $rowClass = 'group';
                } else {
                    $rowClass = 'ledger';
                }

                $prefix = '';
                if (!$isSection && !$isTotal && $level > 0) {
                    $prefix = str_repeat('— ', min($level, 4));
                }
            @endphp
            <tr class="{{ $rowClass }}">
                <td style="padding-left: {{ $pad }}px;">
                    @if($prefix !== '')
                        <span class="tree-mark">{{ $prefix }}</span>
                    @endif
                    {{ $row['name'] ?? '' }}
                </td>
                @if(!empty($showDebitCredit))
                    <td class="num">
                        @if(isset($row['debit']) && $row['debit'] !== null && (float) $row['debit'] != 0)
                            {{ number_format((float) $row['debit'], 2) }}
                        @elseif($isTotal)
                            {{ number_format((float) ($row['debit'] ?? 0), 2) }}
                        @endif
                    </td>
                    <td class="num">
                        @if(isset($row['credit']) && $row['credit'] !== null && (float) $row['credit'] != 0)
                            {{ number_format((float) $row['credit'], 2) }}
                        @elseif($isTotal)
                            {{ number_format((float) ($row['credit'] ?? 0), 2) }}
                        @endif
                    </td>
                @else
                    <td class="num">
                        @if(!$isSection && array_key_exists('amount', $row) && $row['amount'] !== null)
                            {{ number_format((float) $row['amount'], 2) }}
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ !empty($showDebitCredit) ? 3 : 2 }}" style="text-align:center;padding:16px;color:#666;">
                    No balances found for the selected date.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <p class="hint">
        Dark bars = sections &nbsp;|&nbsp; Bold shaded = parent (group) &nbsp;|&nbsp; Indented = child ledger
    </p>
</body>
</html>
