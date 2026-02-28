<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido recebido</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #4f46e5; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; }
        .summary { background: #f9f9f9; border-radius: 6px; padding: 16px; margin: 24px 0; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 6px 0; font-size: 14px; }
        .summary td:last-child { text-align: right; font-weight: bold; }
        .total { font-size: 16px; border-top: 2px solid #e5e7eb; padding-top: 8px; margin-top: 8px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: bold; background: #e0e7ff; color: #4338ca; }
        .footer { padding: 16px 32px; font-size: 12px; color: #9ca3af; border-top: 1px solid #f0f0f0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Pedido recebido ✓</h1>
    </div>
    <div class="body">
        <p>Olá, <strong>{{ $order['userId'] }}</strong>!</p>
        <p>Seu pedido foi criado com sucesso e está aguardando pagamento.</p>

        <div class="summary">
            <p style="margin:0 0 12px;font-weight:bold;">Resumo do pedido</p>
            <table>
                <tr>
                    <td>Número do pedido</td>
                    <td><span class="badge">{{ substr($order['id'], 0, 8) }}…</span></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>{{ $order['status'] }}</td>
                </tr>
                @foreach($order['items'] as $item)
                <tr>
                    <td>{{ $item['productId'] }} × {{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['unitPrice']['amount'] / 100, 2, ',', '.') }} {{ $item['unitPrice']['currency'] }}</td>
                </tr>
                @endforeach
                <tr class="total">
                    <td><strong>Total</strong></td>
                    <td>{{ number_format($order['total']['amount'] / 100, 2, ',', '.') }} {{ $order['total']['currency'] }}</td>
                </tr>
            </table>
        </div>

        <p>Finalize seu pagamento para que seu pedido seja processado.</p>
    </div>
    <div class="footer">
        Ordem System · Este é um e-mail automático, não responda.
    </div>
</div>
</body>
</html>
