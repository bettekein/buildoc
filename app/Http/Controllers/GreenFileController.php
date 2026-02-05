<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Exports\WorkerRosterExport;
use App\Exports\VehicleMachineryExport;
use App\Exports\ToolEquipmentExport;
use App\Exports\SocialInsuranceExport;
use App\Exports\NewWorkerSurveyExport;
use Maatwebsite\Excel\Facades\Excel;

class GreenFileController extends Controller
{
    public function workerRoster(Project $project)
    {
        $filename = "作業員名簿_{$project->name}_" . date('Ymd') . ".xlsx";
        return Excel::download(new WorkerRosterExport($project), $filename);
    }

    public function vehicleMachinery(Project $project)
    {
        $filename = "持込機械届_車両_{$project->name}_" . date('Ymd') . ".xlsx";
        return Excel::download(new VehicleMachineryExport($project), $filename);
    }

    public function toolEquipment(Project $project)
    {
        $filename = "持込機械届_工具_{$project->name}_" . date('Ymd') . ".xlsx";
        return Excel::download(new ToolEquipmentExport($project), $filename);
    }

    public function socialInsurance(Project $project)
    {
        $filename = "社会保険加入状況_{$project->name}_" . date('Ymd') . ".xlsx";
        return Excel::download(new SocialInsuranceExport($project), $filename);
    }

    public function newWorkerSurvey(Project $project)
    {
        $filename = "新規入場者調査票_{$project->name}_" . date('Ymd') . ".xlsx";
        return Excel::download(new NewWorkerSurveyExport($project), $filename);
    }
}

