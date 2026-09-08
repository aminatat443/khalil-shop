<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande {{ $order->order_number }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5; font-family: Helvetica, Arial, sans-serif; color:#213737;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5; padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; max-width:600px; width:100%;">

                    <tr>
                        <td style="background-color:#2F4F4F; padding:28px 32px;">
                            <span style="font-size:20px; font-weight:bold; color:#ffffff;">KhalilShop</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">
                            <p style="font-size:16px; margin:0 0 8px;">Merci {{ $order->customer_name }} !</p>
                            <p style="font-size:14px; color:#555555; margin:0 0 24px;">
                                Votre commande <strong>{{ $order->order_number }}</strong> a bien été reçue et est en cours de traitement. Vous trouverez votre facture en pièce jointe.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:16px;">
                                <thead>
                                    <tr>
                                        <td style="background:#f5f5f5; padding:8px 12px; font-size:11px; text-transform:uppercase; color:#777777;">Article</td>
                                        <td style="background:#f5f5f5; padding:8px 12px; font-size:11px; text-transform:uppercase; color:#777777; text-align:right;">Total</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td style="padding:8px 12px; font-size:13px; border-bottom:1px solid #eeeeee;">
                                                {{ $item->product_name }}
                                                @if($item->variant_label)
                                                    <br><span style="color:#999999; font-size:11px;">{{ $item->variant_label }}</span>
                                                @endif
                                                <br><span style="color:#999999; font-size:11px;">Qté : {{ $item->quantity }}</span>
                                            </td>
                                            <td style="padding:8px 12px; font-size:13px; text-align:right; border-bottom:1px solid #eeeeee;">
                                                {{ number_format($item->subtotal, 0, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; color:#555555;">
                                <tr>
                                    <td style="padding:2px 12px;">Sous-total</td>
                                    <td style="padding:2px 12px; text-align:right;">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td style="padding:2px 12px;">Livraison</td>
                                    <td style="padding:2px 12px; text-align:right;">{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 0, ',', ' ').' FCFA' : 'À confirmer sur WhatsApp' }}</td>
                                </tr>
                                @if($order->discount > 0)
                                    <tr>
                                        <td style="padding:2px 12px; color:#d77a61;">Réduction {{ $order->coupon?->code }}</td>
                                        <td style="padding:2px 12px; text-align:right; color:#d77a61;">-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:10px 12px 2px; border-top:1px solid #eeeeee; font-weight:bold; color:#213737;">Total</td>
                                    <td style="padding:10px 12px 2px; border-top:1px solid #eeeeee; text-align:right; font-weight:bold; color:#213737;">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </table>

                            <div style="margin-top:24px; padding:16px; background:#f5f5f5; font-size:13px; color:#555555;">
                                <strong>Livraison à :</strong> {{ $order->delivery_address }}, {{ $order->delivery_city }}, {{ $order->delivery_region }}<br>
                                <strong>Paiement :</strong> {{ $order->payment_method === 'cod' ? 'À la livraison' : $order->payment_method }}
                            </div>

                            <p style="font-size:12px; color:#999999; margin-top:24px;">
                                Une question ? Répondez simplement à cet email ou contactez-nous sur WhatsApp.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px; background-color:#f5f5f5; font-size:11px; color:#999999; text-align:center;">
                            © {{ date('Y') }} KhalilShop — Mode & Lifestyle, Sénégal
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
