<ul class="tree">
    @foreach ($subcharts as $row)
        <li>
            <span class="{{ $row->type == 'group' ? 'branch' : 'Leaf' }}" id="{{ $row->id }}"
                onclick="changeChart({{ $row->id }})" class="{{ $row->type == 'item' ? 'text-danger' : '' }}"><i
                    class="fa {{ $row->type == 'group' ? 'fa-folder' : 'fa-leaf' }} "></i>
                {{ $row->name }}
            </span>
            @if (count($row->subChartOfInventories))
                @include('chart_of_inventory.sub-group-list', ['subcharts' => $row->subChartOfInventories])
            @endif
        </li>
    @endforeach
</ul>
