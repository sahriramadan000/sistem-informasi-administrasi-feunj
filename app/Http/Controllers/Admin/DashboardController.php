<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        
        $totalActivityLogs = 0;
        if(class_exists('\App\Models\ActivityLog')) {
             $totalActivityLogs = \App\Models\ActivityLog::count();
        } elseif(class_exists('\Spatie\Activitylog\Models\Activity')) {
             $totalActivityLogs = \Spatie\Activitylog\Models\Activity::count();
        }
        
        $totalErrorLogs = 0;
        if(class_exists('\App\Models\ErrorLog')) {
             $totalErrorLogs = \App\Models\ErrorLog::count();
        }
        
        return view('admin.dashboard', compact('totalUsers', 'totalActivityLogs', 'totalErrorLogs'));
    }
}
