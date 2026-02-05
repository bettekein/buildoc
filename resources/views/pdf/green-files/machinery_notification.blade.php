<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>持込機械届 (全建統一様式第2号)</title>
    <style>
        @font-face {
            font-family: 'Japanese';
            src: url('{{ storage_path('fonts/ipaexg.ttf') }}');
        }
        body { font-family: 'Japanese', sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 15px; text-align: center; }
        .left { text-align: left; }
    </style>
</head>
<body>
    <div class="title">持込機械等（電動工具・電気溶接機・照明器具）使用届 (Form 2)</div>
    
    <table>
        <tr>
            <td class="left" width="50%">会社名: {{ $tenant->company_name }}</td>
            <td class="left" width="50%">現場名: {{ $project->name }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>機械名</th>
                <th>規格・性能</th>
                <th>管理番号</th>
                <th>持込年月日</th>
                <th>社内点検年月日</th>
                <th>管理者</th>
                <th>備考</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tools as $index => $tool)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tool->name }}</td>
                <td>{{ $tool->specification }}</td>
                <td>{{ $tool->management_no }}</td>
                <td>{{ now()->format('Y/m/d') }}</td>
                <td>{{ $tool->last_inspection_date ? $tool->last_inspection_date->format('Y/m/d') : '' }}</td>
                <td>{{ $tenant->representative_name ?? '' }}</td>
                <td></td>
            </tr>
            @endforeach
             @for($i = count($tools); $i < 5; $i++)
            <tr>
               <td>{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>
