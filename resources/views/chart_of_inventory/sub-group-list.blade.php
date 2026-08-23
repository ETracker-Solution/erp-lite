<ul class="erp-tree">
    @foreach ($subcharts as $row)
        @php
            $isBranch = in_array($row->type, ['fixed', 'group'], true);
            $icon = $row->type === 'item' ? 'fa-cube' : 'fa-folder';
        @endphp
        <li class="erp-tree-node" data-name="{{ strtolower($row->name) }}">
            <span class="erp-tree-label {{ $isBranch ? 'branch' : 'Leaf' }} {{ $row->type === 'item' ? 'is-item' : '' }} {{ ($row->status ?? '') === 'inactive' ? 'is-inactive' : '' }}"
                  data-id="{{ $row->id }}"
                  id="coi-{{ $row->id }}"
                  onclick="changeChart({{ $row->id }})">
                <i class="fa {{ $icon }}"></i>
                <span class="erp-tree-name">{{ $row->name }}</span>
                <span class="erp-tree-type">{{ $row->type }}</span>
            </span>
            @if ($row->subChartOfInventories && count($row->subChartOfInventories))
                @include('chart_of_inventory.sub-group-list', ['subcharts' => $row->subChartOfInventories])
            @endif
        </li>
    @endforeach
</ul>
