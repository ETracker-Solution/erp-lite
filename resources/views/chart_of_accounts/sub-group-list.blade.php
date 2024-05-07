<ul class="tree">
    @foreach ($subcharts as $row)
        <li>
            <span class="{{ count($row->subChartOfInventories) > 0 ? 'branch' : 'Leaf' }}" id="{{ $row->id }}"
                onclick="changeChart()" class="{{ $row->type == 'item' ? 'text-danger' : '' }}"><i
                    class="fa {{ count($row->subChartOfInventories) > 0 ? 'fa-folder-o' : 'fa-file-o' }} "></i>
                {{ $row->name }}
            </span>
            @if (count($row->subChartOfInventories))
                @include('chart_of_inventory.sub-group-list', ['subcharts' => $row->subChartOfInventories])
            @endif
        </li>
    @endforeach
</ul>
