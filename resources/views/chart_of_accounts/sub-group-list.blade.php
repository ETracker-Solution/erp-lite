<ul class="tree">
    @foreach ($subcharts as $row)
        <li>
            <span class="{{ count($row->childrens) > 0 ? 'branch' : 'Leaf' }}"
                onclick="changeChart({{ $row->id }})" class="{{ $row->type == 'item' ? 'text-danger' : '' }}"><i
                    class="fa {{ count($row->childrens) > 0 ? 'fa-folder' : 'fa-italic' }} "></i>
                {{ $row->name }}
            </span>
            @if (count($row->childrens))
                @include('chart_of_accounts.sub-group-list', ['subcharts' => $row->childrens])
            @endif
        </li>
    @endforeach
</ul>
