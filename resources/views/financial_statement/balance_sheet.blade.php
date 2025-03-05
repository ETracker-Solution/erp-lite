<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1, .header h2, .header p {
            margin: 2px 0;
        }

        .container {
            width: 100%;
            overflow: hidden; /* Clear floats */
        }

        .column {
            width: 50%;
            float: left;
            box-sizing: border-box;
        }

        .column:last-child {
            border-left: 1px solid black; /* Add a border between columns */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 5px;
        }

        th {
            background-color: #f2f2f2;
        }

        .amount {
            text-align: right;
        }

        .totals {
            clear: both;
            border-top: 2px solid black;
            padding: 8px;
        }

        .totals div {
            width: 50%;
            float: left;
        }

        .totals p {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Welkin Pastry Ltd.</h1>
    <p>1182/A Nurani Para, East Monipur,<br>Mirpur-2, Dhaka</p>
    <p>Email: welkinpastry@gmail.com</p>
    <h2>Balance Sheet</h2>
    @if(request()->has('asOnDate'))
        <p>As on {{ \Carbon\Carbon::parse($asOnDate)->format('d-M-y') }}</p>
    @endif
</div>

<div class="container">
    <!-- Liabilities Column -->
    <div class="column">
        <table>
            <thead>
            <tr>
                <th>Liabilities</th>
                <th class="amount">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($liabilities as $liability)
                <tr>
                    <td>
                        <strong>{{ $liability->name }}</strong>
                    </td>
                    <td class="amount">
                        {{ number_format($liability->getTotalBalanceAttribute($asOnDate), 2) }}
                    </td>
                </tr>
                @foreach ($liability->childrens as $child)
                    <tr>
                        <td style="padding-left: 20px;">
                            {{ $child->name }}
                        </td>
                        <td class="amount">
                            {{ number_format($child->getTotalBalanceAttribute($asOnDate), 2) }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Assets Column -->
    <div class="column">
        <table>
            <thead>
            <tr>
                <th>Assets</th>
                <th class="amount">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($assets as $asset)
                <tr>
                    <td>
                        <strong>{{ $asset->name }}</strong>
                    </td>
                    <td class="amount">
                        {{ number_format($asset->getTotalBalanceAttribute($asOnDate), 2) }}
                    </td>
                </tr>
                @foreach ($asset->childrens as $child)
                    <tr>
                        <td style="padding-left: 20px;">
                            {{ $child->name }}
                        </td>
                        <td class="amount">
                            {{ number_format($child->getTotalBalanceAttribute($asOnDate), 2) }}
                        </td>
                    </tr>
                    @foreach ($child->childrens as $child2)
                        <tr>
                            <td style="padding-left: 40px;">
                                {{ $child2->name }}
                            </td>
                            <td class="amount">
                                {{ number_format($child2->getTotalBalanceAttribute($asOnDate), 2) }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Totals Row -->
<div class="totals">
    <div>
        <p>Total Liabilities: {{ number_format($totalLiability, 2) }}</p>
    </div>
    <div>
        <p>Total Assets: {{ number_format($totalAsset, 2) }}</p>
    </div>
</div>
</body>
</html>
