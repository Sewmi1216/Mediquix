<!DOCTYPE html>
<html>
<head>
    <title>Quotation Created</title>
</head>
<body>
    <h2>Hello {{ $quotation->user->name }},</h2>
    <p>Your quotation has been successfully created.</p>
    <p><strong>Status: </strong>{{ $quotation->status }}</p>

    <h4>Drugs:</h4>
    <ul>
        @foreach ($quotation->drugs as $drug)
            <li>{{ $drug->drug_name }} ({{ $drug->quantity }}) - Rs .{{ number_format($drug->amount, 2) }}</li>
        @endforeach
    </ul>
    <p><strong>Total Amount:</strong> Rs. {{ number_format($quotation->total_amount, 2) }}</p>

    <p>Please make sure to accept the quotation. Thank you for using MediQuix.</p>
</body>
</html>
