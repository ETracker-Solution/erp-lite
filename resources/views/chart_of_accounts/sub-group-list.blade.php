<ul class="erp-tree">
    @foreach ($subcharts as $row)
        <li class="erp-tree-node" data-name="{{ strtolower($row->name) }}">
            <span class="erp-tree-label {{ $row->type === 'group' ? 'branch' : 'Leaf' }} {{ $row->type === 'ledger' ? 'is-ledger' : '' }}"
                  data-id="{{ $row->id }}"
                  onclick="changeChart({{ $row->id }})">
                <i class="fa {{ $row->type === 'group' ? 'fa-folder' : 'fa-file-text-o' }}"></i>
                <span class="erp-tree-name">{{ $row->name }}</span>
                <span class="erp-tree-type">{{ $row->type }}</span>
            </span>
            @if ($row->childrens && count($row->childrens))
                @include('chart_of_accounts.sub-group-list', ['subcharts' => $row->childrens])
            @endif
        </li>
    @endforeach
</ul>
