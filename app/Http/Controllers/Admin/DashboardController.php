<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:dashboard_view', ['only' => ['index']]);
    }
    public function index()
    {
        // Fetching stats for cards
        $totalLeads = Lead::count();
        $newLeads = Lead::where('created_at', '>=', now()->startOfMonth())->count();
        $totalUsers = User::count();
        $qualifiedLeads = Lead::where('lead_status', 'Qualified')->count();

        // Fetching data for the donut chart
        $leadsByStatus = Lead::selectRaw('lead_status, COUNT(*) as count')
            ->groupBy('lead_status')
            ->pluck('count', 'lead_status')
            ->toArray();

        return view('Admin.Dashboard.index', compact(
            'totalLeads', 
            'newLeads', 
            'totalUsers', 
            'qualifiedLeads',
            'leadsByStatus'
        ));
    }
}