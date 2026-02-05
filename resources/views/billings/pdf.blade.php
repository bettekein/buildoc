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
            font-size: 11px;
        }
        .header-section { margin-bottom: 20px; }
        .title { text-align: center; font-size: 20px; font-weight: bold; border-bottom: 3px double #000; padding: 5px; width: 60%; margin: 0 auto 20px auto; }
        .customer-block { float: left; width: 55%; }
        .company-block { float: right; width: 40%; text-align: right; }
        .clear { clear: both; }
        
        .main-amount-box { border: 2px solid #000; padding: 10px; margin: 15px 0; text-align: center; font-size: 16px; font-weight: bold; background: #eee; }
        
        .details-table, .calc-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .details-table th, .details-table td, .calc-table th, .calc-table td { border: 1px solid #555; padding: 6px; }
        .details-table th { background: #f0f0f0; text-align: center; }
        .details-table td.amount { text-align: right; }
        .details-table td.center { text-align: center; }

        .calc-table th { background: #e0e0e0; width: 25%; }
        .calc-table td { text-align: right; width: 25%; }

        .invoice-box { margin-top: 20px; border: 1px solid #777; padding: 10px; width: 40%; float: right; font-size: 10px; }
        .bank-box { margin-top: 20px; border: 1px solid #777; padding: 10px; float: left; width: 50%; font-size: 10px; }
    </style>
</head>
<body>
    <div class="title">出来高 請求書</div>

    <div class="header-section">
        <div class="customer-block">
            <h2 style="font-size: 16px; border-bottom: 1px solid #000; display: inline-block;">{{ $project->customer->name }} 御中</h2>
            @if($project->customer->zip_code)
            <p>〒{{ $project->customer->zip_code }}<br>{{ $project->customer->address }}</p>
            @endif
            <p>工事名称: {{ $project->name }}<br>工事場所: {{ $project->site_address ?? '同上' }}</p>
            <p>請求番号: {{ $billing->billing_number }}</p>
            <p>請求日: {{ $billing->billing_date->format('Y年m月d日') }}</p>
        </div>
        <div class="company-block">
            <h3 style="font-size: 14px;">{{ $project->tenant->company_name }}</h3>
            <p>〒{{ $project->tenant->zip_code }}<br>{{ $project->tenant->address }}<br>
            TEL: {{ $project->tenant->phone }} / FAX: {{ $project->tenant->fax }}</p>
            <p>適格請求書発行事業者登録番号: {{ $project->tenant->invoice_registration_number }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Main Amount -->
    <div class="main-amount-box">
        今回ご請求金額: ¥{{ number_format($billing->gross_billing_amount - $billing->retention_money + $billing->retention_release_amount - $billing->offset_amount) }}-
        <span style="font-size: 10px; font-weight: normal;">(税込)</span>
    </div>

    <!-- Calculation Breakdown Table -->
    <table class="calc-table">
        <tr>
            <th>契約金額</th>
            <td>¥{{ number_format($project->contract_amount) }}</td>
            <th>今回進捗率 / 累計</th>
            <td>{{ $billing->progress_rate }}%</td>
        </tr>
        <tr>
            <th>前回までの出来高累計</th>
            <td>¥{{ number_format($billing->previous_billed_amount) }}</td>
            <th>今回の出来高累計</th>
            <td>¥{{ number_format($billing->cumulative_amount) }}</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>項目</th>
                <th>金額 (円)</th>
                <th>備考</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>今回出来高金額 (税抜)</td>
                <td class="amount">{{ number_format($billing->amount_this_time) }}</td>
                <td>今回対象額</td>
            </tr>
            <tr>
                <td>消費税額 (10%)</td>
                <td class="amount">{{ number_format($billing->tax_amount) }}</td>
                <td></td>
            </tr>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td>今回出来高計 (税込)</td>
                <td class="amount">{{ number_format($billing->gross_billing_amount) }}</td>
                <td></td>
            </tr>
            <tr>
                <td>今回保留金 (控除)</td>
                <td class="amount" style="color: red;">- {{ number_format($billing->retention_money) }}</td>
                <td>保留率 {{ $project->retention_rate }}%</td>
            </tr>
             @if($billing->retention_release_amount > 0)
            <tr>
                <td>保留金戻入</td>
                <td class="amount">{{ number_format($billing->retention_release_amount) }}</td>
                <td></td>
            </tr>
            @endif
            @if($billing->offset_amount > 0)
            <tr>
                <td>相殺金・協力会費</td>
                <td class="amount" style="color: red;">- {{ number_format($billing->offset_amount) }}</td>
                <td>安全協力費等</td>
            </tr>
            @endif
             <tr style="background-color: #eee; font-weight: bold; font-size: 14px;">
                <td>差引請求額</td>
                <td class="amount">{{ number_format($billing->gross_billing_amount - $billing->retention_money + $billing->retention_release_amount - $billing->offset_amount) }}</td>
                <td>支払期日: {{ $billing->payment_date ? $billing->payment_date->format('Y年m月d日') : '別途' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Invoice Details -->
    <div class="invoice-box">
        <strong>【消費税計算区分】</strong><br>
        10%対象額: ¥{{ number_format($billing->gross_billing_amount - $billing->tax_amount) }}<br>
        消費税額(10%): ¥{{ number_format($billing->tax_amount) }}
    </div>

    <div class="bank-box">
        <strong>【振込先】</strong><br>
        {{ $project->tenant->bank_details ?? '（銀行口座情報を管理画面で登録してください）' }}<br>
        <br>
        ※ 振込手数料は貴社にてご負担願います。
    </div>
    <div class="clear"></div>

    @if($billing->note)
    <div style="margin-top: 10px; border: 1px dotted #999; padding: 5px;">
        備考: {{ $billing->note }}
    </div>
    @endif

</body>
</html>
