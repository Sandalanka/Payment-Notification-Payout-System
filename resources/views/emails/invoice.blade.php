@php
    $totalUsd = number_format($totalUsd, 4);
@endphp

<div style="font-family: Arial, sans-serif; max-width: 700px; margin: auto;">
    <h2>Invoice for {{ $customerName }}</h2>
    <p>Invoice date: {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</p>

    <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Payment date</th>
                <th>Reference</th>
                <th>Original amount</th>
                <th>Currency</th>
                <th>Amount (USD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
                <tr>
                    <td>{{ optional($p->created_at)->format('Y-m-d') }}</td>
                    <td>{{ $p->reference_no }}</td>
                    <td style="text-align:right">{{ number_format($p->original_amount,4) }}</td>
                    <td>{{ $p->original_currency }}</td>
                    <td style="text-align:right">{{ number_format($p->amount_usd,4) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align:right"><strong>Total USD</strong></td>
                <td style="text-align:right"><strong>{{ $totalUsd }}</strong></td>
            </tr>
        </tbody>
    </table>

    <p>Thank you.</p>
</div>
