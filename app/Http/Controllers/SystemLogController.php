<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SystemLogController extends Controller
{
    /**
     * Display a listing of the logs.
     *
     * @param Request $request
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Only administrators can view logs
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $query = SystemLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by action if provided
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by table if provided
        if ($request->has('table') && $request->table) {
            $query->where('target_table', $request->table);
        }

        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        
        // Ensure user relationships are loaded for paginated results
        $logs->load('user');

        // Get distinct action types for filter dropdown
        $actions = SystemLog::distinct()->pluck('action');
        
        // Get distinct tables for filter dropdown
        $tables = SystemLog::distinct()->pluck('target_table');

        return Inertia::render('Settings/SystemLogs', [
            'logs' => $logs,
            'filters' => [
                'actions' => $actions,
                'tables' => $tables,
            ],
            'currentFilters' => $request->only(['action', 'table', 'user_id', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show the details of a specific log.
     *
     * @param SystemLog $log
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function show(SystemLog $log)
    {
        // Only administrators can view log details
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $log = SystemLog::with('user')->find($log->log_id);

        return Inertia::render('Settings/SystemLogDetails', [
            'log' => $log,
            'canRollback' => $log->action === 'delete' && !empty($log->old_values),
        ]);
    }

    /**
     * Roll back a deletion.
     *
     * @param SystemLog $log
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rollback(SystemLog $log)
    {
        // Only administrators can perform rollbacks
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Check if this log represents a deletion
        if ($log->action !== 'delete') {
            return redirect()->back()->with('error', 'Only deletion actions can be rolled back');
        }

        // Attempt to rollback
        $result = LoggingService::rollbackDelete($log);

        if ($result) {
            return redirect()->back()->with('success', 'Record successfully restored');
        }

        return redirect()->back()->with('error', 'Failed to restore record');
    }
}