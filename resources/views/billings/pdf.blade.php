<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>請求書</title>
    <style>
        @font-face {
            font-family: 'Japanese';
            src: url('{{ storage_path('fonts/ipaexg.ttf') }}');
        }
        body, h1, h2, h3, h4, h5, h6, table, th, td, p, div, span {
            font-family: 'Japanese', sans-serif;
        }
        body {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
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
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .customer-info {
            float: left;
            width: 50%;
        }
        .company-info {
            float: right;
            width: 40%;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .billing-details {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }
        .billing-details th, .billing-details td {
            border: 1px solid #999;
            padding: 8px;
        }
        .billing-details th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .amount-summary {
            margin-top: 20px;
            text-align: right;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
        }
        .notes {
            margin-top: 40px;
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 50px;
        }
    </style>
</head>
<body>
    <div class="title">請 求 書</div>

    <div class="header">
        <div class="customer-info">
            <h3>{{ $project->customer->name ?? '顧客名未設定' }} 御中</h3>
            <p>案件名: {{ $project->name }}</p>
        </div>
        <div class="company-info">
            <h3>{{ $project->tenant->company_name ?? '自社名未設定' }}</h3>
            <p>{{ $project->tenant->address ?? '' }}</p>
            <p>登録番号: {{ $project->tenant->invoice_registration_number ?? '' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="info-row">
        <p>下記のとおりご請求申し上げます。</p>
        <p>請求日: {{ $billing->billing_date ? $billing->billing_date->format('Y年m月d日') : '未定' }}</p>
        <p>請求番号: {{ $billing->billing_number }}</p>
    </div>

    <div class="total-section">
        <h2 style="text-align: center; border-bottom: 1px solid #000; display: inline-block;">
            ご請求金額: ¥{{ number_format($billing->amount_this_time + $billing->tax_amount) }}-
        </h2>
    </div>

    <table class="billing-details">
        <thead>
            <tr>
                <th>内容</th>
                <th style="width: 150px;">金額 (税抜)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $billing->note ?: '工事代金として (第' . $billing->billing_round . '回出来高)' }}
                </td>
                <td style="text-align: right;">
                    ¥{{ number_format($billing->amount_this_time) }}
                </td>
            </tr>
            <!-- Empty rows to fill space if needed -->
            <tr><td colspan="2" style="height: 20px; border:none;"></td></tr>
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align: right; background-color: #eee;">小計</td>
                <td style="text-align: right;">¥{{ number_format($billing->amount_this_time) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; background-color: #eee;">消費税 (10%)</td>
                <td style="text-align: right;">¥{{ number_format($billing->tax_amount) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; background-color: #eee; font-weight: bold;">合計</td>
                <td style="text-align: right; font-weight: bold;">¥{{ number_format($billing->amount_this_time + $billing->tax_amount) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="notes">
        <strong>備考:</strong><br>
        {{ $billing->note }}
    </div>

    <div class="bank-info" style="margin-top: 30px;">
        <strong>振込先:</strong><br>
        <!-- Hardcoded or from Tenant model if available -->
        {{ $project->tenant->bank_details ?? '（振込先口座情報をここに表示）' }}
    </div>

</body>
</html>
