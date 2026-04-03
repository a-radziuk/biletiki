<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $event->name }} — {{ $section->name }}</title>
    <style>
        @page { margin: 36px 42px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            color: #1e1b4b;
            line-height: 1.45;
            margin: 0;
        }
        .banner {
            background: #5b21b6;
            color: #ffffff;
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .banner small {
            display: block;
            font-size: 9pt;
            opacity: 0.92;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .banner h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .meta-table td {
            padding: 6px 0;
            vertical-align: top;
        }
        .meta-table td.label {
            width: 22%;
            color: #64748b;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .meta-table td.value {
            font-weight: 600;
            color: #0f172a;
        }
        .desc {
            font-size: 9.5pt;
            color: #475569;
            margin: 0 0 18px 0;
            padding: 12px 14px;
            background: #f8fafc;
            border-left: 3px solid #a78bfa;
            border-radius: 0 4px 4px 0;
        }
        .category-box {
            border: 1px solid #c4b5fd;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 18px;
            background: #faf5ff;
        }
        .category-box .label {
            font-size: 9pt;
            color: #6d28d9;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .category-box .name {
            font-size: 14pt;
            font-weight: bold;
            color: #4c1d95;
        }
        .price {
            font-size: 11pt;
            color: #334155;
            margin-top: 6px;
        }
        .qr-wrap {
            text-align: center;
            margin: 22px 0 12px 0;
        }
        .qr-wrap img {
            width: 200px;
            height: 200px;
        }
        .code {
            text-align: center;
            font-family: DejaVu Sans Mono, monospace;
            font-size: 9pt;
            color: #0f172a;
            letter-spacing: 0.04em;
            word-break: break-all;
            padding: 10px 12px;
            background: #f1f5f9;
            border-radius: 4px;
            margin: 0 auto;
            max-width: 100%;
        }
        .footnote {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 8pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="banner">
        <small>{{ config('app.name') }}</small>
        <h1>{{ $event->name }}</h1>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">When</td>
            <td class="value">{{ $event->starts_at->timezone(config('app.timezone'))->format('l, F j, Y \a\t g:i A') }}</td>
        </tr>
        <tr>
            <td class="label">Where</td>
            <td class="value">{{ $event->location }}</td>
        </tr>
    </table>

    @if ($descriptionPlain !== '')
        <p class="desc">{{ $descriptionPlain }}</p>
    @endif

    <div class="category-box">
        <div class="label">Ticket category</div>
        <div class="name">{{ $section->name }}</div>
        <div class="price">Price: {{ $formattedPrice }}</div>
    </div>

    <p style="text-align:center;font-size:9pt;color:#64748b;margin:0 0 8px 0;">
        Present this QR code at the entrance
    </p>
    <div class="qr-wrap">
        <img src="{{ $qrDataUri }}" alt="Ticket QR" width="200" height="200">
    </div>
    <div class="code">{{ $ticket->public_code }}</div>

    <div class="footnote">
        Order reference: {{ $order->uuid }} &middot; {{ $order->customer_name }}
    </div>
</body>
</html>
