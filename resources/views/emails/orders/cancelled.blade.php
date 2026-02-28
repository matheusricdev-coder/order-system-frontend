<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido cancelado</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #dc2626; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; }
        .summary { background: #fef2f2; border-radius: 6px; padding: 16px; margin: 24px 0; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 6px 0; font-size: 14px; }
        .summary td:last-child { text-align: right; font-weight: bold; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: bold; background: #fee2e2; color: #991b1b; }
        .footer { padding: 16px 32px; font-size: 12px; color: #9ca3af; border-top: 1px solid #f0f0f0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Pedido cancelado</h1>
    </div>
    <div class="body">
        <p>Seu pedido foi cancelado. Se você não solicitou o cancelamento ou houve um problema no pagamento, entre em contato com nosso suporte.</p>

        <div class="summary">
            <p style="margin:0 0 12px;font-weight:bold;">Pedido cancelado</p>
            <table>
                <tr>
                    <td>Número do pedido</td>
                    <td><span class="badge">{{ substr($order['id'], 0, 8) }}…</span></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><span class="badge">CANCELADO</span></td>
                </tr>
            </table>
        </div>

        <p>O estoque reservado foi liberado automaticamente.</p>
    </div>
    <div class="footer">
        Ordem System · Este é um e-mail automático, não responda.
    </div>
</div>
</body>
</html>
