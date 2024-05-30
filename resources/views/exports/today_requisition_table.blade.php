<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    th {
        background-color: #c4c7c7;
    }
</style>
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
                    <td>{{$value[$mainKey]}}</td>
                @else
                    @foreach($value[$mainKey] as $item)
                        <td>{{$item}}</td>
                    @endforeach
                @endif
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
