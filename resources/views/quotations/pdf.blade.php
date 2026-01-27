<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>御見積書</title>
    <style>
        @font-face {
            font-family: 'Japanese';
            src: url('{{ storage_path('fonts/ipaexg.ttf') }}');
        }
        body, h1, h2, h3, h4, h5, h6, table, th, td, p, div, span {
            font-family: 'Japanese', sans-serif;
        }
        body {
            font-size: 13px;
            color: #333;
            line-height: 1.4;
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
        .info-row {
            margin-bottom: 20px;
        }
        .total-section {
            margin: 20px 0;
            text-align: center;
        }
        .quotation-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .quotation-details th, .quotation-details td {
            border: 1px solid #999;
            padding: 6px;
        }
        .quotation-details th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .item-header td {
            background-color: #e6e6e6;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="title">御 見 積 書</div>

    <div class="header">
        <div class="customer-info">
            <h3>{{ $project->customer->name ?? '顧客名未設定' }} 御中</h3>
            <p>案件名: {{ $project->name }}</p>
            <p>工期: {{ $project->period_start?->format('Y年m月d日') }} 〜 {{ $project->period_end?->format('Y年m月d日') }}</p>
        </div>
        <div class="company-info">
            <h3>{{ $project->tenant->company_name ?? '自社名未設定' }}</h3>
            <p>{{ $project->tenant->address ?? '' }}</p>
            <p>担当: {{ auth()->user()->name }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="info-row">
        <p>下記の通り御見積申し上げます。</p>
        <p>見積有効期限: 発行より3ヶ月</p>
    </div>

    @php
        $grandTotal = 0;
        foreach($project->quotationItems as $item) {
            $grandTotal += $item->total_amount;
        }
        $taxAmount = floor($grandTotal * 0.1); 
    @endphp

    <div class="total-section">
        <h2 style="text-align: center; border-bottom: 1px solid #000; display: inline-block; padding: 0 20px;">
            御見積金額: ¥{{ number_format($grandTotal + $taxAmount) }}- <span style="font-size: 14px; font-weight: normal;">(税込)</span>
        </h2>
    </div>

    <table class="quotation-details">
        <thead>
            <tr>
                <th>名称</th>
                <th>規格・仕様</th>
                <th style="width: 50px;">数量</th>
                <th style="width: 40px;">単位</th>
                <th style="width: 80px;">単価</th>
                <th style="width: 90px;">金額</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($project->quotationItems->sortBy('sort_order') as $item)
                <tr class="item-header">
                    <td colspan="5">{{ $item->name }}</td>
                    <td class="text-right">¥{{ number_format($item->total_amount) }}</td>
                </tr>
                @foreach ($item->details as $detail)
                    <tr>
                        <td style="padding-left: 15px;">{{ $detail->name }}</td>
                        <td>{{ $detail->specification }}</td>
                        <td class="text-right">{{ $detail->quantity }}</td>
                        <td class="text-center">{{ $detail->unit }}</td>
                        <td class="text-right">¥{{ number_format($detail->unit_price) }}</td>
                        <td class="text-right">¥{{ number_format($detail->total_price) }}</td>
                    </tr>
                @endforeach
            @endforeach
            
            <!-- Space filler -->
             <tr><td colspan="6" style="height: 20px; border-left: 1px solid #999; border-right: 1px solid #999; border-bottom: none;"></td></tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="background-color: #eee; border:none;"></td>
                <td class="text-right" style="background-color: #eee;">小計</td>
                <td class="text-right">¥{{ number_format($grandTotal) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="background-color: #eee; border:none;"></td>
                <td class="text-right" style="background-color: #eee;">消費税 (10%)</td>
                <td class="text-right">¥{{ number_format($taxAmount) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="background-color: #eee; border:none;"></td>
                <td class="text-right" style="background-color: #eee; font-weight: bold;">合計</td>
                <td class="text-right" style="font-weight: bold;">¥{{ number_format($grandTotal + $taxAmount) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="notes" style="margin-top: 30px; border: 1px solid #ccc; padding: 10px; font-size: 12px;">
        <strong>備考:</strong><br>
        本見積に含まれない工事が発生した場合は別途申し受けます。<br>
        発生材処分費は含みません。
    </div>

</body>
</html>
