<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura - {{ $dispatch->order_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-section div {
            width: 48%;
        }
        .info-section p {
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .total-row td {
            font-weight: bold;
            text-align: right;
        }
        .total-row td:last-child {
            background-color: #eaf5ea;
        }
    </style>
</head>
<body>
    <h2>Factura de Despacho</h2>

    <div class="info-section">
        <div>
            <p><strong>Cliente:</strong> {{ $dispatch->customer->name ?? 'N/A' }}</p>
            <p><strong>Vendedor:</strong> {{ $dispatch->seller->name ?? 'N/A' }}</p>
            <p><strong>Notas:</strong> {{ $dispatch->notes ?? '-' }}</p>
        </div>
        <div>
            <p><strong>No. Orden:</strong> {{ $dispatch->order_number }}</p>
            <p><strong>Fecha:</strong> {{ $dispatch->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($dispatch->status) }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dispatch->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name ?? 'Producto eliminado' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Q{{ number_format($item->unit_price, 2) }}</td>
                    <td>Q{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td>Q{{ number_format($dispatch->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>


