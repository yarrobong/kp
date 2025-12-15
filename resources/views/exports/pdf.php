<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            margin: 5px 0;
        }
        .total-final {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }
        .content {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $proposal->title }}</h1>
    </div>

    <div class="info">
        <p><strong>Номер предложения:</strong> {{ $proposal->offer_number ?: 'N/A' }}</p>
        <p><strong>Дата:</strong> {{ $proposal->offer_date->format('d.m.Y') }}</p>
        @if($proposal->seller_info)
            <p><strong>Продавец:</strong> {{ $proposal->seller_info }}</p>
        @endif
        @if($proposal->buyer_info)
            <p><strong>Покупатель:</strong> {{ $proposal->buyer_info }}</p>
        @endif
    </div>

    <div class="content">
        {!! $proposal->body_html !!}
    </div>

    @if($proposal->items->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Наименование</th>
                    <th>Кол-во</th>
                    <th>Ед.</th>
                    <th>Цена</th>
                    <th>Скидка %</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proposal->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ number_format($item->price, 2) }} {{ $proposal->currency }}</td>
                        <td>{{ $item->discount }}%</td>
                        <td>{{ number_format($item->total, 2) }} {{ $proposal->currency }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Итого без НДС:</span>
                <strong>{{ number_format($totals['subtotal'], 2) }} {{ $proposal->currency }}</strong>
            </div>
            <div class="total-row">
                <span>НДС ({{ $proposal->vat_rate }}%):</span>
                <strong>{{ number_format($totals['vat'], 2) }} {{ $proposal->currency }}</strong>
            </div>
            <div class="total-row total-final">
                <span>Всего к оплате:</span>
                <strong>{{ number_format($totals['total'], 2) }} {{ $proposal->currency }}</strong>
            </div>
        </div>
    @endif

    @if($proposal->terms)
        <div class="info">
            <p><strong>Условия:</strong> {{ $proposal->terms }}</p>
        </div>
    @endif
</body>
</html>



