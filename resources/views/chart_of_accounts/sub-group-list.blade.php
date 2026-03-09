<ul class="tree">
    @foreach ($subcharts as $row)
        <li>
            <span class="{{ $row->type == 'group' ? 'branch' : 'Leaf' }} {{ $row->type == 'item' ? 'text-danger' : '' }}"
                onclick="changeChart({{ $row->id }})" id="node-{{ $row->id }}"><i
                    class="fa {{ $row->type == 'group' ? 'fa-folder' : 'fa-italic' }} "></i>
                <span class="node-name">{{ $row->name }}</span>
            </span>
            @if (count($row->childrens))
                @include('chart_of_accounts.sub-group-list', ['subcharts' => $row->childrens])
            @endif
        </li>
    @endforeach
</ul>
