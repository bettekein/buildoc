<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>車両届 (全建統一様式第3号)</title>
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
    <div class="title">工事用車両届 (全建統一様式第3号)</div>
    
    <table>
        <tr>
            <td class="left" width="50%">
                事業所の名称: {{ $tenant->company_name }}<br>
                現場責任者: {{ $project->site_agent ?? '' }}
            </td>
            <td class="left" width="50%">
                一次下請負人: <br>
                (提出先現場名): {{ $project->name }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">持ち込み年月日</th>
                <th rowspan="2">車両番号</th>
                <th rowspan="2">車種</th>
                <th rowspan="2">運転者氏名</th>
                <th rowspan="2">所有者氏名</th>
                <th colspan="3">自賠責保険</th>
                <th colspan="3">任意保険</th>
            </tr>
            <tr>
                <th>会社名</th>
                <th>期間</th>
                <th>証券No</th>
                <th>会社名</th>
                <th>期間</th>
                <th>証券No</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $index => $vehicle)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ now()->format('Y/m/d') }}</td> <!-- Default to today -->
                <td>{{ $vehicle->plate_number }}</td>
                <td>{{ $vehicle->model_name }}</td>
                <td></td> <!-- Driver Name often variable -->
                <td>{{ $vehicle->owner_name }}</td>
                <!-- Simplified Logic here as insurance_info is JSON -->
                <td>{{ $vehicle->insurance_info['company'] ?? '' }}</td>
                <td>{{ $vehicle->insurance_info['expiry'] ?? '' }}</td>
                <td>{{ $vehicle->insurance_info['number'] ?? '' }}</td>
                <td>{{ $vehicle->insurance_info['company'] ?? '' }}</td>
                <td>{{ $vehicle->insurance_info['expiry'] ?? '' }}</td>
                <td>{{ $vehicle->insurance_info['number'] ?? '' }}</td>
            </tr>
            @endforeach
            @for($i = count($vehicles); $i < 5; $i++)
            <tr>
               <td>{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>
