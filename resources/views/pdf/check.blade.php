<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Чек №{{ $order->id }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .receipt-box { max-width: 800px; margin: auto; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); padding: 30px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        .info { text-align: right; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .details-table th { background: #f3f4f6; text-align: left; padding: 10px; border: 1px solid #ddd; }
        .details-table td { padding: 10px; border: 1px solid #ddd; }
        .total { margin-top: 30px; text-align: right; font-size: 18px; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="header">
            <div class="logo">Online Gas Station</div>
            <div class="info">
                <div>Заказ №: <strong>{{ $order->id }}</strong></div>
                <div>Дата: {{ $order->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>

        <h3>Детали заказа</h3>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Тип топлива</th>
                    <th>Цена за литр</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->fuel_name }}</td>
                    <td>{{ number_format($order->cost_in_time, 2) }} руб.</td>
                    <td>{{ $order->quantity }} л.</td>
                    <td>{{ number_format($order->cost, 2) }} руб.</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            ИТОГО: {{ number_format($order->cost, 2) }} руб.
        </div>

        <div class="footer">
            Спасибо за покупку!<br>
            ООО "Онлайн Заправка", 2026
        </div>
    </div>
</body>
</html>
