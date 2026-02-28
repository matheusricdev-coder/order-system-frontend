<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento confirmado</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #16a34a; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; }
        .summary { background: #f0fdf4; border-radius: 6px; padding: 16px; margin: 24px 0; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 6px 0; font-size: 14px; }
        .summary td:last-child { text-align: right; font-weight: bold; }
        .total { font-size: 16px; border-top: 2px solid #bbf7d0; padding-top: 8px; margin-top: 8px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: bold; background: #dcfce7; color: #15803d; }
        .footer { padding: 16px 32px; font-size: 12px; color: #9ca3af; border-top: 1px solid #f0f0f0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Pagamento confirmado 🎉</h1>
    </div>
    <div class="body">
        <p>Seu pagamento foi aprovado com sucesso!</p>

        <div class="summary">
            <p style="margin:0 0 12px;font-weight:bold;">Seu pedido</p>
            <table>
                <tr>
                    <td>Número do pedido</td>
                    <td><span class="badge">{{ substr($order['id'], 0, 8) }}…</span></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><span class="badge">PAGO</span></td>
                </tr>
                @foreach($order['items'] as $item)
                <tr>
                    <td>{{ $item['productId'] }} × {{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['unitPrice']['amount'] / 100, 2, ',', '.') }} {{ $item['unitPrice']['currency'] }}</td>
                </tr>
                @endforeach
                <tr class="total">
                    <td><strong>Total pago</strong></td>
                    <td>{{ number_format($order['total']['amount'] / 100, 2, ',', '.') }} {{ $order['total']['currency'] }}</td>
                </tr>
            </table>
        </div>

        <p>Obrigado por sua compra!</p>
    </div>
    <div class="footer">
        Ordem System · Este é um e-mail automático, não responda.
    </div>
</div>
</body>
</html>
