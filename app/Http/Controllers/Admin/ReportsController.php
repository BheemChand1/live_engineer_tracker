<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Engineer;
use App\Models\Task;
use App\Models\EngineerLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Generate engineer attendance report.
     */
    public function engineerAttendance(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $engineers = Engineer::with(['logs' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('login_at', [$startDate, $endDate]);
        }])->get();

        $reportData = $engineers->map(function($engineer) {
            $totalHours = 0;
            $workingDays = 0;

            foreach($engineer->logs as $log) {
                $workingDays++;
                $logout = $log->logout_at ?: now();
                $totalHours += $log->login_at->diffInHours($logout);
            }

            return [
                'engineer' => $engineer,
                'total_hours' => round($totalHours, 2),
                'working_days' => $workingDays,
                'avg_hours_per_day' => $workingDays > 0 ? round($totalHours / $workingDays, 2) : 0
            ];
        });

        if ($request->get('export') === 'csv') {
            return $this->exportAttendanceCSV($reportData, $startDate, $endDate);
        }

        return view('admin.reports.attendance', compact('reportData', 'startDate', 'endDate'));
    }

    /**
     * Generate task completion report.
     */
    public function taskCompletion(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $tasks = Task::with('engineer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $reportData = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'completed')->count(),
            'pending_tasks' => $tasks->where('status', 'pending')->count(),
            'in_progress_tasks' => $tasks->where('status', 'in-progress')->count(),
            'overdue_tasks' => $tasks->filter(function($task) {
                return $task->isOverdue();
            })->count(),
            'tasks_by_engineer' => $tasks->groupBy('engineer_id')->map(function($engineerTasks) {
                return [
                    'engineer' => $engineerTasks->first()->engineer,
                    'total' => $engineerTasks->count(),
                    'completed' => $engineerTasks->where('status', 'completed')->count(),
                    'completion_rate' => $engineerTasks->count() > 0 
                        ? round(($engineerTasks->where('status', 'completed')->count() / $engineerTasks->count()) * 100, 2) 
                        : 0
                ];
            })
        ];

        if ($request->get('export') === 'csv') {
            return $this->exportTaskCompletionCSV($reportData, $startDate, $endDate);
        }

        return view('admin.reports.tasks', compact('reportData', 'startDate', 'endDate'));
    }

    /**
     * Export attendance report as CSV.
     */
    private function exportAttendanceCSV($reportData, $startDate, $endDate)
    {
        $filename = 'engineer_attendance_' . Carbon::parse($startDate)->format('Y-m-d') . '_to_' . Carbon::parse($endDate)->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Engineer Name', 'Email', 'Total Hours', 'Working Days', 'Avg Hours/Day']);

            foreach ($reportData as $data) {
                fputcsv($file, [
                    $data['engineer']->name,
                    $data['engineer']->email,
                    $data['total_hours'],
                    $data['working_days'],
                    $data['avg_hours_per_day']
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export task completion report as CSV.
     */
    private function exportTaskCompletionCSV($reportData, $startDate, $endDate)
    {
        $filename = 'task_completion_' . Carbon::parse($startDate)->format('Y-m-d') . '_to_' . Carbon::parse($endDate)->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Engineer Name', 'Total Tasks', 'Completed Tasks', 'Completion Rate %']);

            foreach ($reportData['tasks_by_engineer'] as $data) {
                if ($data['engineer']) {
                    fputcsv($file, [
                        $data['engineer']->name,
                        $data['total'],
                        $data['completed'],
                        $data['completion_rate']
                    ]);
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
