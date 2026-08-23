<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h4 { margin: 0; font-size: 13px; }
        p { margin: 2px 0 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #dfdfdf; text-align: left; padding: 4px; border-bottom: 1px solid #ccc; }
        td { padding: 3px 4px; border-bottom: 1px solid #eee; vertical-align: top; }
        .center { text-align: center; }
        .num { text-align: right; }
    </style>
    <title>{{ $report_header }}</title>
</head>
<body>
    <div class="center">
        <h4>{{ $report_header }}</h4>
        <p>{{ $dateRange }}</p>
        <p>{{ $page_title }}</p>
    </div>
    <table>
        <thead>
        <tr>
            @foreach($columns as $column)
                <th>{{ $column }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                @foreach($columns as $column)
                    @php
                        $value = is_object($item) ? ($item->$column ?? '') : ($item[$column] ?? '');
                        $isNum = in_array($column, ['Debit', 'Credit', 'Balance'], true);
                    @endphp
                    <td class="{{ $isNum ? 'num' : '' }}">{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
