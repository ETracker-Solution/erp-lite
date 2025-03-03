<table>
    <tr>
        <th>Liabilities</th>
        <th>Assets</th>
    </tr>
    <tr>
        <td style="vertical-align: top;">
            <table>
                @foreach ($liabilities as $liability)
                    <tr>
                        <td>
                            <strong>{{ $liability->name }} </strong>

                        </td>
                        <td>
                            {{ number_format($liability->total_balance, 2) }}
                        </td>
                    </tr>
                    @foreach ($liability->childrens as $child)
                        <tr>
                            <td>
                                <p> {{ $child->name }} </p>

                            </td>
                            <td>
                                {{ number_format($child->total_balance, 2) }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </table>

        </td>
        <td style="vertical-align: top;">
            <table>
                @foreach ($assets as $asset)
                    <tr>
                        <td>
                            <strong>{{ $asset->name }} </strong>

                        </td>
                        <td>
                            <strong>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($asset->total_balance, 2) }}
                            </strong>
                        </td>
                    </tr>
                    @foreach ($asset->childrens as $child)
                        <tr>
                            <td>
                                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $child->name }} - </p>
                            </td>
                            <td>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($child->total_balance, 2) }}
                            </td>
                        </tr>
                        @foreach ($child->childrens as $child2)
                            <tr>
                                <td>
                                    <p>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $child2->name }} </p>
                                </td>
                                <td>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($child2->total_balance, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </table>
        </td>
    </tr>
</table>
