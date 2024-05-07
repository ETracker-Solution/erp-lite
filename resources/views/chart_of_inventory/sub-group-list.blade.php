<ul class="tree">
    @foreach ($subcharts as $row)
        <li>
            <span class="{{ count($row->subChartOfInventories) > 0 ? 'branch' : 'Leaf' }}"
                onclick="changeChart({{ $row->id }})" class="{{ $row->type == 'item' ? 'text-danger' : '' }}"><i
                    class="fa {{ count($row->subChartOfInventories) > 0 ? 'fa-folder' : 'fa-leaf' }} "></i>
                {{ $row->name }}
            </span>
            @if (count($row->subChartOfInventories))
                @include('chart_of_inventory.sub-group-list', ['subcharts' => $row->subChartOfInventories])
            @endif
        </li>
    @endforeach
</ul>
