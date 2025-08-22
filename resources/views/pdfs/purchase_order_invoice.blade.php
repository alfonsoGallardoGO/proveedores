<!DOCTYPE html>
<html>
<head>
    <title>Orden de Compra</title>
    <style>
        body { font-family: sans-serif; }
        h1 { color: #333; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Orden de Compra</h1>
    <p>Número de orden: {{ $orderNumber }}</p>
    
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Identificación</th>
                <th>Descripción</th>
                <th>Cantidad solicitada</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->amount, 2, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>