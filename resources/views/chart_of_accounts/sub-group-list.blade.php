<ul class="tree">
    @foreach ($subcharts as $row)
        <li>
            <span class="{{ $row->type == 'group' ? 'branch' : 'Leaf' }}"
                onclick="changeChart({{ $row->id }})" class="{{ $row->type == 'item' ? 'text-danger' : '' }}"><i
                    class="fa {{ $row->type == 'group' ? 'fa-folder' : 'fa-italic' }} "></i>
                {{ $row->name }}
            </span>
            @if (count($row->childrens))
                @include('chart_of_accounts.sub-group-list', ['subcharts' => $row->childrens])
            @endif
        </li>
    @endforeach
</ul>
