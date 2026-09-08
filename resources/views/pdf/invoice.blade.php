<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #213737; font-size: 12px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #2F4F4F; padding-bottom: 16px; margin-bottom: 24px; }
        .shop-name { font-size: 22px; font-weight: bold; color: #2F4F4F; }
        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 18px; margin: 0; color: #d77a61; text-transform: uppercase; letter-spacing: 1px; }
        .doc-title p { margin: 4px 0 0; color: #555; }
        table.meta { width: 100%; margin-bottom: 24px; }
        table.meta td { vertical-align: top; width: 50%; }
        table.meta h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin: 0 0 6px; }
        table.meta p { margin: 0 0 3px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        table.items th { background: #f5f5f5; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #555; }
        table.items td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 12px; }
        table.items td.num, table.items th.num { text-align: right; }
        table.totals { width: 260px; margin-left: auto; }
        table.totals td { padding: 4px 0; }
        table.totals td.label { color: #555; }
        table.totals td.value { text-align: right; }
        table.totals tr.total td { border-top: 2px solid #2F4F4F; padding-top: 8px; font-size: 14px; font-weight: bold; color: #2F4F4F; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #eee; font-size: 10px; color: #999; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <div class="shop-name">KhalilShop</div>
        <div class="doc-title">
            <h1>Facture</h1>
            <p>N° {{ $order->order_number }}</p>
            <p>{{ $order->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <table class="meta">
        <tr>
            <td>
                <h3>Client</h3>
                <p>{{ $order->customer_name }}</p>
                <p>{{ $order->customer_phone }}</p>
                <p>{{ $order->customer_email }}</p>
            </td>
            <td>
                <h3>Livraison</h3>
                <p>{{ $order->delivery_address }}, {{ $order->delivery_quartier }}</p>
                <p>{{ $order->delivery_city }}, {{ $order->delivery_region }}</p>
                @if($order->delivery_instructions)
                    <p>{{ $order->delivery_instructions }}</p>
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Article</th>
                <th class="num">Qté</th>
                <th class="num">Prix unitaire</th>
                <th class="num">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->variant_label)
                            <br><span style="color:#999;font-size:10px;">{{ $item->variant_label }}</span>
                        @endif
                    </td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td class="num">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Sous-total</td>
            <td class="value">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td class="label">Livraison</td>
            <td class="value">{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if($order->discount > 0)
            <tr>
                <td class="label">Réduction {{ $order->coupon?->code }}</td>
                <td class="value">-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</td>
            </tr>
        @endif
        <tr class="total">
            <td class="label">Total</td>
            <td class="value">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    <p style="margin-top:24px;">
        <strong>Paiement :</strong> {{ $order->payment_method === 'cod' ? 'À la livraison' : $order->payment_method }}
    </p>

    <div class="footer">
        Ce document est un reçu de commande généré automatiquement et ne constitue pas une facture fiscale formelle.
    </div>

</body>
</html>
