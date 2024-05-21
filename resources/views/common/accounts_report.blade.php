<table class="w-full">
    <tr>
        <td class="w-full headers">
            <h3>{{ $report_header }}</h3>
            <span>{{ $dateRange }}</span>
        </td>
    </tr>
</table>
<table  class="w-full">
    <tr>
        <td>{{ $page_title }}</td>
    </tr>
</table>
<div class="margin-top">
    <table class="products">
        <tr>
            @foreach($columns as $column)
                <th>{{$column}}</th>
            @endforeach
        </tr>
        @foreach($data as $key=>$item)
            <tr class="items">
                @foreach($columns as $column)
                    <td style="white-space: pre">{!!  str_replace(' ',"&nbsp;",$item->$column) !!}</td>
{{--            @if($key==4) <?php  dd($item) ?> @endif--}}
        @endforeach
            </tr>
        @endforeach
    </table>
</div>
