<htmlpagefooter name="page-footer">
    <table>
        <tbody>
        <tr style="font-size: 11px !important;">
            <td><em>Printing on: <strong>{{ Carbon\Carbon::now()->format('m-d-Y h:i a') }}</strong></em></td>
            <td style="text-align:right"><em>Page {PAGENO} out of {nbpg}</em></td>
        </tr>
        </tbody>
    </table>
</htmlpagefooter>
