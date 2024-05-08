<style>
    body {
        background: #fff;
        font-family: arial;
        color: #333;
    }

    .menu {
        width: 300px;
        margin: auto;
        margin-bottom: 50px;

    }

    #inventoryItems{
        max-height: 70vh;
        overflow-y: auto;
    }

    .tree {
        list-style: none;
        padding-left: 20px;
        position: relative;
        color: #333;
    }

    .tree:before {
        content: "";
        width: 2px;
        background: #a6d2ff;
        top: 0;
        bottom: 3px;
        left: 0;
        position: absolute;
    }

    .tree li {
        position: relative;
    }

    .tree li:before {
        content: "";
        width: 20px;
        height: 1px;
        position: absolute;
        font-family: "FontAwesome";
        top: 12px;
        left: -20px;
        position: absolute;
    }

    .tree li:hover,
    .tree li:focus {
        color: #333;
        cursor: pointer;
    }

    .tree .tree {
        display: none;
    }

    .fa {
        padding-right: 5px;
        margin-top: 10px;
    }
</style>
<div class="menu">
    <ul class="tree">
        @if (isset($allChartOfInventories))
            @foreach ($allChartOfInventories as $row)
                <li>
                                    <span class="branch" onclick="changeChart({{$row->id}})" id="{{ $row->id }}"><i
                                            class="fa fa-folder"></i>
                                        {{ $row->name }}
                                    </span>

                    @if (count($row->subChartOfInventories))
                        @include('chart_of_inventory.sub-group-list', [
                        'subcharts' => $row->subChartOfInventories,
                        ])
                    @endif

                </li>
            @endforeach
        @endif
    </ul>

</div>
<script>
    $('.branch').click(function () {
        $(this).children().toggleClass('fa-folder-open');
        $(this).next().slideToggle();

    });
</script>
