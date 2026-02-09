<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>請求書</title>
    <style>
        body {
            font-family: "Yu Gothic", "Meiryo", sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 5px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .customer-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .company-info {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .customer-name {
            font-size: 18px;
            border-bottom: 1px solid #333;
            display: inline-block;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .amount-box {
            text-align: center;
            border: 2px solid #333;
            padding: 10px;
            margin: 20px 0;
            font-size: 18px;
            background-color: #f9f9f9;
        }

        .amount-label {
            font-size: 14px;
            margin-right: 10px;
        }

        .amount-value {
            font-size: 24px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: #eee;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .invoice-number {
            margin-top: 5px;
            font-size: 12px;
        }

        .summary-table {
            width: 60%;
            margin-left: auto;
        }

        .summary-table th {
            text-align: right;
            padding-right: 10px;
        }
    </style>
</head>

<body>
    <div class="title">請 求 書</div>

    <div class="info-row">
        <div class="customer-info">
            <div class="customer-name">{{ $customer->name ?? '顧客名未設定' }} 御中</div>
            <div>件名: {{ $project->name }}</div>
            <div>請求日: {{ $billing->billing_date?->format('Y年m月d日') }}</div>
            <div>請求番号: {{ $billing->billing_number }}</div>
        </div>
        <div class="company-info">
            <div style="font-size: 16px; font-weight: bold;">{{ $tenant->company_name }}</div>
            <div>〒{{ $tenant->zip_code }}</div>
            <div>{{ $tenant->address }}</div>
            <div>TEL: {{ $tenant->phone }}</div>
            <div>{{ $tenant->representative_title }} {{ $tenant->representative_name }}</div>
            @if($tenant->invoice_registration_number)
                <div class="invoice-number">登録番号: {{ $tenant->invoice_registration_number }}</div>
            @endif
        </div>
    </div>

    <div class="amount-box">
        <span class="amount-label">ご請求金額</span>
        <span class="amount-value">¥{{ number_format($billing->final_billing_amount) }} -</span>
        <div style="font-size: 12px; text-align: right;">(税込)</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>内容</th>
                <th style="width: 100px;">金額 (税抜)</th>
                <th style="width: 100px;">消費税</th>
                <th style="width: 100px;">備考</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>工事代金（出来高 {{ $billing->progress_rate }}%）</td>
                <td class="text-right">¥{{ number_format($billing->amount_this_time) }}</td>
                <td class="text-right">¥{{ number_format($billing->tax_amount) }}</td>
                <td>{{ $billing->note }}</td>
            </tr>
            <!-- 空行を追加して見た目を整える -->
            @for($i = 0; $i < 4; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <th>今回出来高 (税抜)</th>
            <td class="text-right" style="width: 120px;">¥{{ number_format($billing->amount_this_time) }}</td>
        </tr>
        <tr>
            <th>消費税 (10%)</th>
            <td class="text-right">¥{{ number_format($billing->tax_amount) }}</td>
        </tr>
        <tr>
            <th>今回合計 (税込)</th>
            <td class="text-right">¥{{ number_format($billing->gross_billing_amount) }}</td>
        </tr>
        @if($billing->retention_money > 0)
            <tr>
                <th>保留金控除</th>
                <td class="text-right">- ¥{{ number_format($billing->retention_money) }}</td>
            </tr>
        @endif
        @if($billing->offset_amount > 0)
            <tr>
                <th>相殺金</th>
                <td class="text-right">- ¥{{ number_format($billing->offset_amount) }}</td>
            </tr>
        @endif
        @if($billing->retention_release_amount > 0)
            <tr>
                <th>保留金戻入</th>
                <td class="text-right">+ ¥{{ number_format($billing->retention_release_amount) }}</td>
            </tr>
        @endif
        <tr>
            <th style="background-color: #ddd;">今回ご請求額</th>
            <td class="text-right" style="font-weight: bold;">¥{{ number_format($billing->final_billing_amount) }}</td>
        </tr>
    </table>

    <!-- インボイス対応 内訳 -->
    <div style="margin-top: 20px; border-top: 1px dotted #ccc; padding-top: 10px; width: 60%; margin-left: auto;">
        <div style="font-size: 11px; margin-bottom: 5px;">【消費税計算（インボイス対応）】</div>
        <table style="width: 100%; font-size: 11px; margin-bottom: 0;">
            <tr>
                <th style="background-color: #fff; border: none; text-align: left;">10%対象額</th>
                <td style="border: none; text-align: right;">¥{{ number_format($billing->amount_this_time) }}</td>
                <th style="background-color: #fff; border: none; text-align: left; padding-left: 20px;">消費税額</th>
                <td style="border: none; text-align: right;">¥{{ number_format($billing->tax_amount) }}</td>
            </tr>
        </table>
    </div>
</body>

</html>