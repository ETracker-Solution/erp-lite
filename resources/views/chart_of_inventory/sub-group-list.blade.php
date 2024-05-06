<ul class="tree">
    @foreach ($subcharts as $row)
        <li>
            <span class="{{ count($row->subChartOfInventories) > 0 ? 'branch' : 'Leaf' }}"><i
                    class="fa {{ count($row->subChartOfInventories) > 0 ? 'fa-folder-o' : 'fa-file-o' }} "></i>
                <a href="#" id="{{ $row->id }}"
                    @if ($row->type != 'item') onclick="changeChart()" @endif
                    class="{{ $row->type == 'item' ? 'text-danger' : '' }}"> <span>{{ $row->name }}</span></a>
            </span>

            @if (count($row->subChartOfInventories))
                @include('chart_of_inventory.sub-group-list', ['subcharts' => $row->subChartOfInventories])
            @endif
        </li>
    @endforeach
</ul>
