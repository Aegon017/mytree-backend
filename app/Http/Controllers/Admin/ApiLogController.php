<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ApiLogController extends Controller
{
    /**
     * Display a listing of the API logs.
     */
    public function index(Request $request)
    {
        // Fetch logs with pagination
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')
            ->paginate(10); // Change the pagination count if needed

        return view('Admin.api-logs.index', compact('logs'));
    }
}
