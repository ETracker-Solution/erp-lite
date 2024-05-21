<htmlpagefooter name="page-footer">
    <table>
        <tr>
            <td><em>Printing on: <strong>{{ Carbon\Carbon::now()->format('m-d-Y h:i a') }}</strong></em></td>
            <td style="text-align:right"><em>Page {PAGENO} out of {nbpg}</em></td>
        </tr>
    </table>
</htmlpagefooter>
