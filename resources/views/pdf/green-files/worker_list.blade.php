<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>作業員名簿 (全建統一様式第5号)</title>
    <style>
        @font-face {
            font-family: 'Japanese';
            src: url('{{ storage_path('fonts/ipaexg.ttf') }}');
        }
        body { font-family: 'Japanese', sans-serif; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 2px; text-align: center; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 10px; text-align: center; }
        .left { text-align: left; }
    </style>
</head>
<body>
    <div class="title">作業員名簿 (全建統一様式第5号)</div>
    
    <table>
        <tr>
            <td class="left" width="20%">事業所の名称: {{ $tenant->company_name }}</td>
            <td class="left" width="20%">所長名: {{ $tenant->representative_name ?? '' }}</td>
            <td class="left" width="20%">現場代理人: {{ $project->site_agent ?? '' }}</td>
            <td class="left" width="40%">工事名称: {{ $project->name }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">氏 名<br>(ふりがな)</th>
                <th rowspan="2">職種</th>
                <th rowspan="2">生年月日<br>(年齢)</th>
                <th rowspan="2">経験<br>年数</th>
                <th colspan="2">健康診断</th>
                <th rowspan="2">血圧</th>
                <th colspan="3">保険加入状況</th>
                <th rowspan="2">教育<br>受講</th>
                <th rowspan="2">入場<br>年月日</th>
            </tr>
            <tr>
                <th>日</th>
                <th>血圧</th>
                <th>健</th>
                <th>厚</th>
                <th>雇</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffMembers as $index => $staff)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left">
                    {{ $staff->furigana }}<br>
                    {{ $staff->name }}
                </td>
                <td>{{ $staff->job_type }}</td>
                <td>
                    {{ $staff->birthday ? $staff->birthday->format('Y/m/d') : '' }}<br>
                    ({{ $staff->birthday ? $staff->birthday->age : '' }})
                </td>
                <td>{{ $staff->experience_years }}年</td>
                <td>{{ $staff->health_info['checkup_date'] ?? '' }}</td>
                <td>
                    {{ $staff->health_info['blood_pressure_max'] ?? '' }}/{{ $staff->health_info['blood_pressure_min'] ?? '' }}
                </td>
                <td>{{ $staff->blood_type }}</td>
                <td>{{ !empty($staff->insurance_details['health_type']) ? '○' : '×' }}</td>
                <td>{{ !empty($staff->insurance_details['pension_type']) ? '○' : '×' }}</td>
                <td>{{ !empty($staff->insurance_details['employment_number']) ? '○' : '×' }}</td>
                <td>済</td>
                <td>{{ $staff->hiring_date ? $staff->hiring_date->format('Y/m/d') : '' }}</td>
            </tr>
            @endforeach
            @for($i = count($staffMembers); $i < 10; $i++)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>
    
    <div class="left" style="font-size: 8px;">
        ※ 個人情報保護法に基づき、本名簿は作業員の安全管理および労務管理の目的にのみ使用します。
    </div>
</body>
</html>
