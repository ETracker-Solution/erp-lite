<?php

namespace Tests\Feature;

use App\Exports\DeliveryRequisitionExport;
use App\Exports\FactoryDeliveryExport;
use App\Exports\FactoryReceiveExport;
use App\Exports\FinishRequisitionExport;
use App\Exports\GlobalExports;
use App\Exports\OutletReceiveExport;
use App\Exports\OutletRequisitionExport;
use App\Exports\RawRequisitionExport;
use App\Http\Middleware\VerifyCsrfToken;
use App\Jobs\SendPromoCodeJob;
use App\Models\Outlet;
use App\Models\PromoCode;
use App\Services\AdminDashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use ReflectionClass;
use Tests\TestCase;
use Yajra\DataTables\Facades\DataTables;

class UpgradeCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_csrf_and_permission_middleware_are_registered(): void
    {
        $this->assertTrue(is_a(VerifyCsrfToken::class, PreventRequestForgery::class, true));

        $middleware = app('router')->getMiddleware();

        $this->assertSame(\Spatie\Permission\Middleware\RoleMiddleware::class, $middleware['role']);
        $this->assertSame(\Spatie\Permission\Middleware\PermissionMiddleware::class, $middleware['permission']);
        $this->assertSame(\Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class, $middleware['role_or_permission']);
    }

    public function test_dashboard_outlet_cache_contains_only_arrays(): void
    {
        Outlet::create([
            'name' => 'Compatibility Outlet',
            'address' => 'Test address',
            'status' => 'active',
        ]);

        $method = (new ReflectionClass(AdminDashboardService::class))->getMethod('todayMetrics');
        $method->invoke(app(AdminDashboardService::class), now()->toDateString());

        $cachedOutlets = Cache::get('admin_dashboard:active_outlets');

        $this->assertIsArray($cachedOutlets);
        $this->assertSame(
            ['id' => $cachedOutlets[0]['id'], 'name' => 'Compatibility Outlet'],
            $cachedOutlets[0],
        );
    }

    public function test_datatables_returns_the_expected_json_shape(): void
    {
        $response = DataTables::collection(collect([
            ['id' => 1, 'name' => 'Compatibility Row'],
        ]))->toJson();

        $payload = $response->getData(true);

        $this->assertSame(1, $payload['recordsTotal']);
        $this->assertSame(1, $payload['recordsFiltered']);
        $this->assertSame('Compatibility Row', $payload['data'][0]['name']);
    }

    public function test_excel_exports_have_v4_types_and_generate_xlsx(): void
    {
        $collectionExports = [
            DeliveryRequisitionExport::class,
            FactoryDeliveryExport::class,
            FactoryReceiveExport::class,
            FinishRequisitionExport::class,
            OutletReceiveExport::class,
            OutletRequisitionExport::class,
            RawRequisitionExport::class,
        ];

        foreach ($collectionExports as $export) {
            $this->assertSame('Illuminate\\Support\\Collection', (string) (new ReflectionClass($export))->getMethod('collection')->getReturnType());
            $this->assertStringStartsWith('PK', Excel::raw(new $export(collect()), ExcelFormat::XLSX));
        }

        foreach ([...$collectionExports, GlobalExports::class] as $export) {
            $this->assertSame('array', (string) (new ReflectionClass($export))->getMethod('styles')->getReturnType());
        }

        $contents = Excel::raw(new class implements FromArray
        {
            public function array(): array
            {
                return [['Laravel', '13.38.0']];
            }
        }, ExcelFormat::XLSX);

        $this->assertStringStartsWith('PK', $contents);
    }

    public function test_pdf_rendering_and_queue_dispatch_do_not_call_external_services(): void
    {
        $fontDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'erp-lite-dompdf-tests';
        app('files')->ensureDirectoryExists($fontDirectory);
        config([
            'dompdf.options.font_dir' => $fontDirectory,
            'dompdf.options.font_cache' => $fontDirectory,
        ]);

        $this->assertStringStartsWith('%PDF-', Pdf::loadHTML('<h1>ERP Lite</h1>')->output());

        Queue::fake();

        SendPromoCodeJob::dispatch(new PromoCode(['code' => 'UPGRADE-TEST']), ['01700000000']);

        Queue::assertPushed(SendPromoCodeJob::class, fn (SendPromoCodeJob $job) => $job->numbers === ['01700000000']);
    }
}
