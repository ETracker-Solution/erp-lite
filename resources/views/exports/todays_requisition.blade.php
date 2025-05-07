<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th {
        background-color: #c4c7c7;
    }

    .headers {
        text-align: center
    }

    .signature-section {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }

    .signature-section div {
        text-align: center;
        font-weight: bold;
        width: 32%;
    }
</style>

@if(\Illuminate\Support\Facades\Route::currentRouteName() == 'today.requisitions.export')
    <table style="width: 100% ; border: unset !important;">
        <tbody>
{{--        @include('common.report_header')--}}
        </tbody>
    </table>
    <table style="margin-top: 70px; width:100%; text-align: center !important;border: unset !important;">
        <tr style="text-align: center !important;border: unset !important;">
            <td style="border: unset !important;"><strong
                    style="border-top: 1px solid black;text-align: end !important;">Prepared
                    By</strong></td>
            <td style="border: unset !important;"><strong
                    style="border-top: 1px solid black;text-align: center !important; ">Production
                    Manager/Staff</strong>
            </td>
        </tr>
    </table>
    <hr>
@endif
<table style="width: 100%">
    <thead>
    <tr>
        @foreach($headers as $header)
            <th>{{$header}}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($values as $key=>$value)
        <tr>
            @foreach(array_keys($value) as $mainKey)
                @if(!is_array($value[$mainKey]))
                    <td style="text-align: center">{{$value[$mainKey]}}</td>
                @else
                    @foreach($value[$mainKey] as $item)
                        <td style="text-align: center">{{$item}}</td>
                    @endforeach
                @endif
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
