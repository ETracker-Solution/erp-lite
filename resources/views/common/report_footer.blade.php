<htmlpagefooter name="page-footer">
    <table width="100%">
        <tr>
            <td>
                <p style="font-size: 8px; margin: 0;"><em>Printing on: {{ Carbon\Carbon::now()->format('m-d-Y h:i a') }}</em></p>
            </td>
            <td style="text-align:right">
                <p style="font-size: 8px; text-align:right; margin: 0;"><em>Page {PAGENO} out of {nbpg}</em></p>
            </td>
        </tr>
    </table>
</htmlpagefooter>
