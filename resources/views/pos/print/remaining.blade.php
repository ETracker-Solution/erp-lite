<table class="table table-bordered">
    <thead class="bg-secondary">
    <tr>
        <th style="width: 10px">#</th>
        <th>Item</th>
        <th style="width: 50px">Unit</th>
        <th style="width: 50px">Stock</th>
        <th style="width: 180px">Cost Qty</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rmItems as $row)

        <tr @if (!$row['in_stock']) style="background: red" @endif>

            <td>
                {{ $loop->iteration }}
            </td>
            <td>
                {{ $row['rm_name'] }}
            </td>
            <td>
                {{ $row['unit'] }}
            </td>
            <td>
                {{ $row['current_stock'] }}
            </td>
            <td>
                {{ $row['total_qty'] }}
            </td>
        </tr>
    @endforeach
    </tbody>

</table>
