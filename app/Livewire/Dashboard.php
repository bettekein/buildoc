<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProgressBilling;
use Livewire\Component;
use Illuminate\Support\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Stats
        $activeProjectsCount = Project::whereIn('status', ['受注', '施工中'])->count();
        
        $monthlyBillingTotal = ProgressBilling::whereBetween('billing_date', [$startOfMonth, $endOfMonth])
            ->sum('amount_this_time');
            
        $monthlyUnbilledTotal = ProgressBilling::whereBetween('billing_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'unbilled')
            ->sum('amount_this_time');

        // Recent Projects
        $recentProjects = Project::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming Billings (Next 30 days)
        $upcomingBillings = ProgressBilling::with('project.customer')
            ->where('status', 'unbilled')
            ->where('billing_date', '>=', $now)
            ->where('billing_date', '<=', $now->copy()->addDays(30))
            ->orderBy('billing_date')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'activeProjectsCount' => $activeProjectsCount,
            'monthlyBillingTotal' => $monthlyBillingTotal,
            'monthlyUnbilledTotal' => $monthlyUnbilledTotal,
            'recentProjects' => $recentProjects,
            'upcomingBillings' => $upcomingBillings,
        ])->layout('layouts.app');
    }
}
