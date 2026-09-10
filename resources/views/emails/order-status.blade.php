<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande {{ $order->order_number }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f2f2f2; font-family: Helvetica, Arial, sans-serif; color:#213737;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f2f2; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; max-width:600px; width:100%;">

                    {{-- Bandeau logo --}}
                    <tr>
                        <td style="background-color:#2F4F4F; padding:22px 32px;">
                            <span style="font-size:20px; font-weight:bold; color:#ffffff;">KhalilShop</span>
                        </td>
                    </tr>

                    {{-- Suivi de commande — même timeline que sur le site (compte client) --}}
                    @if($order->status === 'annulee')
                        <tr>
                            <td style="background-color:#fef2f2; padding:22px 32px; text-align:center;">
                                <span style="font-size:26px;">✕</span>
                                <p style="margin:8px 0 0; font-size:16px; font-weight:bold; color:#b91c1c; text-transform:uppercase; letter-spacing:0.5px;">
                                    Commande annulée
                                </p>
                            </td>
                        </tr>
                    @else
                        @php
                            $steps = ['recue' => 'Reçue', 'confirmee' => 'Confirmée', 'en_preparation' => 'En préparation', 'expediee' => 'Expédiée', 'livree' => 'Livrée'];
                            $stepKeys = array_keys($steps);
                            $currentIndex = array_search($order->status, $stepKeys);
                            $currentIndex = $currentIndex === false ? 0 : $currentIndex;
                            $lastIndex = count($steps) - 1;
                        @endphp
                        <tr>
                            <td style="padding:26px 24px 6px;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        @foreach($steps as $key => $label)
                                            @php
                                                $i = $loop->index;
                                                $reached = $i <= $currentIndex;
                                                $passed = $i < $currentIndex;
                                                $circleColor = $reached ? '#d77a61' : '#e0e0e0';
                                                $textColor = $reached ? '#213737' : '#999999';
                                                $leftLineColor = $i === 0 ? '#ffffff' : ($i <= $currentIndex ? '#d77a61' : '#e0e0e0');
                                                $rightLineColor = $i === $lastIndex ? '#ffffff' : ($i < $currentIndex ? '#d77a61' : '#e0e0e0');
                                            @endphp
                                            <td style="width:{{ round(100 / count($steps)) }}%; text-align:center;">
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
                                                    <td style="width:50%; height:2px; background-color:{{ $leftLineColor }}; font-size:0; line-height:0;">&nbsp;</td>
                                                    <td style="width:22px;">
                                                        <table role="presentation" cellpadding="0" cellspacing="0" align="center"><tr>
                                                            <td width="22" height="22" style="width:22px; height:22px; min-width:22px; border-radius:11px; background-color:{{ $circleColor }}; color:#ffffff; font-size:10px; font-weight:bold; text-align:center; vertical-align:middle;">
                                                                {{ $passed ? '✓' : $i + 1 }}
                                                            </td>
                                                        </tr></table>
                                                    </td>
                                                    <td style="width:50%; height:2px; background-color:{{ $rightLineColor }}; font-size:0; line-height:0;">&nbsp;</td>
                                                </tr></table>
                                                <p style="margin:6px 0 0; font-size:8px; text-transform:uppercase; letter-spacing:0.3px; color:{{ $textColor }};">{{ $label }}</p>
                                            </td>
                                        @endforeach
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:28px 32px 8px;">
                            <p style="font-size:15px; margin:0 0 4px;">Bonjour {{ $order->customer_name }},</p>
                            <p style="font-size:13px; color:#555555; margin:0;">
                                @if($order->status === 'confirmee')
                                    Votre commande a été confirmée et est en cours de préparation.
                                @elseif($order->status === 'en_preparation')
                                    Votre commande est en cours de préparation dans notre atelier.
                                @elseif($order->status === 'expediee')
                                    Bonne nouvelle : votre colis est en route !
                                @elseif($order->status === 'livree')
                                    Votre commande a été livrée. Un souci avec un article ? Vous pouvez demander un retour depuis votre espace client, dans les 7 jours suivant la commande.
                                @elseif($order->status === 'annulee')
                                    Cette commande a été annulée. Si un paiement avait déjà été effectué, il sera remboursé.
                                @else
                                    Le statut de votre commande <strong>{{ $order->order_number }}</strong> vient d'être mis à jour.
                                @endif
                            </p>
                        </td>
                    </tr>

                    {{-- Récapitulatif commande --}}
                    <tr>
                        <td style="padding:16px 32px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f9f9f9; font-size:12px; color:#555555;">
                                <tr>
                                    <td style="padding:14px 16px;">N° de commande<br><strong style="color:#213737; font-size:13px;">{{ $order->order_number }}</strong></td>
                                    <td style="padding:14px 16px;">Date<br><strong style="color:#213737; font-size:13px;">{{ $order->created_at->format('d/m/Y') }}</strong></td>
                                    <td style="padding:14px 16px;">Paiement<br><strong style="color:#213737; font-size:13px;">{{ $order->payment_method === 'cod' ? 'À la livraison' : $order->payment_method }}</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Articles --}}
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <p style="margin:0 0 12px; font-size:11px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px; color:#999999;">Votre commande</p>

                            @foreach($order->items as $item)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px; border:1px solid #eeeeee;">
                                    <tr>
                                        <td width="72" style="padding:0;">
                                            @if($image = $item->product?->images->first()?->url)
                                                <img src="{{ img_url($image, 144, 144) }}" alt="{{ $item->product_name }}" width="72" style="display:block; width:72px; height:72px; object-fit:cover;">
                                            @else
                                                <div style="width:72px; height:72px; background:#f5f5f5;"></div>
                                            @endif
                                        </td>
                                        <td style="padding:10px 14px; vertical-align:middle;">
                                            <p style="margin:0 0 4px; font-size:13px; font-weight:bold; color:#213737;">{{ $item->product_name }}</p>
                                            @if($item->variant_label)
                                                <p style="margin:0 0 4px; font-size:11px; color:#999999;">{{ $item->variant_label }}</p>
                                            @endif
                                            <p style="margin:0; font-size:11px; color:#999999;">Qté : {{ $item->quantity }}</p>
                                        </td>
                                        <td style="padding:10px 14px; text-align:right; vertical-align:middle; white-space:nowrap;">
                                            <span style="font-size:13px; font-weight:bold; color:#213737;">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</span>
                                        </td>
                                    </tr>
                                </table>
                            @endforeach
                        </td>
                    </tr>

                    {{-- Totaux --}}
                    <tr>
                        <td style="padding:8px 32px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; color:#555555;">
                                <tr>
                                    <td style="padding:2px 0;">Sous-total</td>
                                    <td style="padding:2px 0; text-align:right;">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td style="padding:2px 0;">Livraison</td>
                                    <td style="padding:2px 0; text-align:right;">{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 0, ',', ' ').' FCFA' : 'À confirmer sur WhatsApp' }}</td>
                                </tr>
                                @if($order->discount > 0)
                                    <tr>
                                        <td style="padding:2px 0; color:#d77a61;">Réduction</td>
                                        <td style="padding:2px 0; text-align:right; color:#d77a61;">-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:10px 0 2px; border-top:1px solid #eeeeee; font-weight:bold; color:#213737;">Total</td>
                                    <td style="padding:10px 0 2px; border-top:1px solid #eeeeee; text-align:right; font-weight:bold; color:#213737;">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Livraison --}}
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <div style="padding:16px; background:#f9f9f9; font-size:12px; color:#555555;">
                                <strong style="color:#213737;">Livraison à :</strong> {{ $order->delivery_address }}{{ $order->delivery_quartier ? ', '.$order->delivery_quartier : '' }}, {{ $order->delivery_city }}, {{ $order->delivery_region }}
                            </div>
                        </td>
                    </tr>

                    {{-- CTA --}}
                    <tr>
                        <td style="padding:28px 32px; text-align:center;">
                            <a href="{{ route('account.orders.show', $order) }}" style="display:inline-block; padding:14px 32px; background:#2F4F4F; color:#ffffff; font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px; text-decoration:none;">
                                Voir ma commande
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 24px; text-align:center;">
                            <p style="font-size:11px; color:#999999; margin:0;">
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
