<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>施工体制台帳 (全建統一様式第1号甲)</title>
    <style>
        @font-face {
            font-family: 'Japanese';
            src: url('{{ storage_path('fonts/ipaexg.ttf') }}');
        }
        body { font-family: 'Japanese', sans-serif; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 2px; }
        th { background: #eee; text-align: center; }
        .title { font-size: 14px; font-weight: bold; text-align: center; margin-bottom: 5px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="title">施工体制台帳 (作成建設業者) (全建統一様式第1号甲)</div>

    <table>
        <tr>
            <th width="15%">工事名称</th>
            <td colspan="3">{{ $project->name }}</td>
            <th width="15%">発注者名</th>
            <td colspan="3">{{ $project->customer->name ?? '' }}</td>
        </tr>
        <tr>
            <th>工事場所</th>
            <td colspan="7">{{ $project->site_address ?? '' }}</td>
        </tr>
        <tr>
            <th>工期</th>
            <td colspan="7">
                自 {{ $project->period_start ? $project->period_start->format('Y年m月d日') : '' }}<br>
                至 {{ $project->period_end ? $project->period_end->format('Y年m月d日') : '' }}
            </td>
        </tr>
        
        <tr>
            <th rowspan="4" width="5%">会<br>社<br>情<br>報</th>
            <th>商号又は名称</th>
            <td colspan="3">{{ $tenant->company_name }}</td>
            <th>代表者氏名</th>
            <td colspan="3">{{ $tenant->representative_title }} {{ $tenant->representative_name }}</td>
        </tr>
        <tr>
             <th>所在地</th>
             <td colspan="3">{{ $tenant->address }}</td>
             <th>電話番号</th>
             <td colspan="3">{{ $tenant->phone }} (FAX: {{ $tenant->fax }})</td>
        </tr>
        <tr>
            <th>建設業の許可</th>
            <td colspan="7">
                <!-- Assuming license_details is array of license objects -->
                @if(!empty($tenant->license_details))
                    @foreach($tenant->license_details as $license)
                        {{ $license['type'] ?? '' }}: {{ $license['category'] ?? '' }} {{ $license['classification'] ?? '' }} 第{{ $license['number'] ?? '' }}号 ({{ $license['date'] ?? '' }})<br>
                    @endforeach
                @endif
            </td>
        </tr>
    </table>
    
    <div style="margin-top: 10px;">
        ※ 本データはシステムより簡易出力されたものです。詳細な記述が必要な場合は手書き等で補足してください。
    </div>
</body>
</html>
