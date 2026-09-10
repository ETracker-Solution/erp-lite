<script>
    window.handleReportBlobResponse = function (response, exportFormat, fallbackName) {
        var contentType = (response.headers && response.headers['content-type']) || '';
        if (contentType.indexOf('application/json') !== -1) {
            return response.data.text().then(function (text) {
                try {
                    var json = JSON.parse(text);
                    throw new Error(json.message || 'Unable to generate report');
                } catch (e) {
                    if (e.message && e.message !== 'Unexpected end of JSON input') {
                        throw e;
                    }
                    throw new Error('Unable to generate report');
                }
            });
        }

        var isExcel = exportFormat === 'xlsx' || contentType.indexOf('spreadsheet') !== -1;
        var blob = new Blob([response.data], {
            type: isExcel
                ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                : 'application/pdf'
        });
        var url = window.URL.createObjectURL(blob);
        if (isExcel) {
            var a = document.createElement('a');
            a.href = url;
            a.download = (fallbackName || 'report') + '.xlsx';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { window.URL.revokeObjectURL(url); }, 1000);
        } else {
            window.open(url, '_blank');
        }
        return Promise.resolve();
    };
</script>
