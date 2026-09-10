<div class="mb-3 d-flex align-items-center flex-wrap">
    <span class="small text-muted mr-2 mb-1">Export as:</span>
    <div class="btn-group btn-group-sm mb-1" role="group" aria-label="Export format">
        <button type="button"
                class="btn"
                :class="export_format === 'pdf' ? 'btn-danger' : 'btn-outline-secondary'"
                @click="export_format = 'pdf'">
            <i class="fa fa-file-pdf"></i> PDF
        </button>
        <button type="button"
                class="btn"
                :class="export_format === 'xlsx' ? 'btn-success' : 'btn-outline-secondary'"
                @click="export_format = 'xlsx'">
            <i class="fa fa-file-excel"></i> Excel
        </button>
    </div>
</div>
