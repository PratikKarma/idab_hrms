<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\AttendanceRequest;
use App\Models\Employee;
use App\Models\User;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceRequestController extends Controller
{
    public function index(Request $request)
    {
        if (!\Auth::user()->can('Manage Attendance Request')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $companySettings = Utility::settings();
        $date = Carbon::createFromFormat($companySettings['site_date_format'], $request->date)->format('Y-m-d');
        $type = $request->input('type', 'monthly');

        $query = AttendanceRequest::with([
            'employee.user:id,name',
            'approver:id,name'
        ])
            ->where('created_by', Auth::user()->creatorId())
            ->latest();

        if ($type == 'daily' && $request->filled('date')) {
            $query->whereDate('requested_at', $date);
        } elseif ($type == 'monthly' && $request->filled('month')) {
            $month = date('m', strtotime($request->month));
            $year  = date('Y', strtotime($request->month));
            $query->whereYear('requested_at', $year)->whereMonth('requested_at', $month);
        }

        $requests = $query->get();

        $statsQuery = clone $query;
        $stats = [
            'total'       => $statsQuery->count(),
            'this_month'  => (clone $statsQuery)->whereMonth('requested_at', now()->month)->count(),
            'this_week'   => (clone $statsQuery)->whereBetween('requested_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'last_30days' => (clone $statsQuery)->where('requested_at', '>=', now()->subDays(30))->count(),
        ];

        return view('attendance_requests.index', [
            'requests' => $requests,
            'stats'    => $stats,
            'type'     => $type,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'   => 'required|in:clock_in,clock_out',
            'reason' => 'nullable|string|max:255',
        ]);

        $employee = Employee::where('user_id', Auth::user()->id)->firstOrFail();

        $date = date("Y-m-d");
        $time = date("H:i:s");

        $attendanceRequest = AttendanceRequest::create([
            'employee_id'  => $employee->id,
            'type'         => $request->type,
            'reason'       => $request->reason ?? null,
            'status'       => 'pending',
            'requested_at' => $date . ' ' . $time,
            'created_by'   => Auth::user()->creatorId(),
        ]);
        $settings = Utility::settings();

        if (isset($settings['attendance_request']) && $settings['attendance_request'] == 1) {
            $companyOwner = User::find(Auth::user()->creatorId());

            $hrUsers = $companyOwner->companyHrs();
            $hrEmails = $hrUsers->pluck('email')->toArray();

            // Map clock_in / clock_out → readable text
            $typeReadable = $request->type === 'clock_in' ? 'Clock In' : 'Clock Out';

            // Format date/time for email only
            $emailDate = date($settings['site_date_format'] ?? 'Y-m-d');
            $emailTime = date($settings['site_time_format'] ?? 'H:i');

            // Mail variables
            $uArr = [
                'company_name'   => $companyOwner->name ?? 'Company',
                'employee_name'  => $employee->name,
                'employee_email' => $employee->email ?? '',
                'type'           => $typeReadable,
                'date'           => $emailDate,
                'time'           => $emailTime,
                'reason'         => $request->reason ?? 'N/A',
                'url'            => route('attendance-requests.index'),
            ];

            // Combine recipients: company owner + HRs
            $recipients = array_merge([$companyOwner->email], $hrEmails);

            // Send to each recipient
            foreach ($recipients as $recipient) {
                Utility::sendEmailTemplate('attendance_request', $recipient, $uArr);
            }
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => __('Your request has been sent and is pending approval.'),
                'data' => $attendanceRequest,
            ], 201);
        }

        return redirect()->back()->with('success', __('Your request has been sent and is pending approval.'));
    }

    public function approve(AttendanceRequest $attendanceRequest)
    {
        // Permission check
        if (!\Auth::user()->can('Approve Attendance Request')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        // dd($attendanceRequest);
        if ($attendanceRequest->type == "clock_out") {
            $companySettings = Utility::settings();
            $date = Carbon::createFromFormat($companySettings['site_date_format'], $attendanceRequest->requested_at)->format('Y-m-d');
            $pastAttendanceRequest = AttendanceRequest::whereDate('requested_at',$date)
                ->where('employee_id', $attendanceRequest->employee_id)
                ->where('type','clock_in')
                ->where('status', 'pending')
                ->first();
                if($pastAttendanceRequest)
                {
                    return redirect()->back()->with('error', __('Please change status first clock in request.'));
                }
        }
        // Approve the request
        $attendanceRequest->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        $requestedAt = \Carbon\Carbon::parse($attendanceRequest->requested_at);

        $employeeId = $attendanceRequest->employee_id;
        $date = $requestedAt->toDateString();
        $time = $requestedAt->toTimeString();

        $employee = Employee::find($employeeId);

        if ($employee && $employee->company_start_time && $employee->company_end_time) {
            $startTime = $employee->company_start_time;
            $endTime   = $employee->company_end_time;
        } else {
            $startTime  = Utility::getValByName('company_start_time');
            $endTime    = Utility::getValByName('company_end_time');
        }

        if ($attendanceRequest->type === 'clock_in') {
            // ---- CLOCK IN REQUEST ----
            $checkDb = AttendanceEmployee::where('employee_id', $employeeId)
                ->where('date', $date)
                ->where('clock_out', '00:00:00')
                ->first();

            if (empty($checkDb)) {
                $expectedStartTime = $date . ' ' . $startTime;

                $totalLateSeconds = strtotime($date . ' ' . $time) - strtotime($expectedStartTime);
                $totalLateSeconds = max($totalLateSeconds, 0);

                $hours = abs(floor($totalLateSeconds / 3600));
                $mins  = abs(floor($totalLateSeconds / 60 % 60));
                $secs  = abs(floor($totalLateSeconds % 60));
                $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                $employeeAttendance                = new AttendanceEmployee();
                $employeeAttendance->employee_id   = $employeeId;
                $employeeAttendance->date          = $date;
                $employeeAttendance->status        = 'Present';
                $employeeAttendance->clock_in      = $time;
                $employeeAttendance->clock_out     = '00:00:00';
                $employeeAttendance->late          = $late;
                $employeeAttendance->early_leaving = '00:00:00';
                $employeeAttendance->overtime      = '00:00:00';
                $employeeAttendance->breaks        = $attendanceRequest->breaks;
                $employeeAttendance->total_break   = $attendanceRequest->total_break;
                $employeeAttendance->total_rest    = $attendanceRequest->total_rest;
                $employeeAttendance->created_by    = Auth::id();

                if(!is_null($attendanceRequest->clock_out)){
                    $employeeAttendance->clock_out = $attendanceRequest->clock_out;
                    // Calculate early leaving and overtime
                    $expectedEndTime = strtotime($date . ' ' . $endTime);
                    $actualEndTime   = strtotime($date . ' ' . $attendanceRequest->clock_out);

                    if ($actualEndTime < $expectedEndTime) {
                        $earlyLeaving = $expectedEndTime - $actualEndTime;
                        $employeeAttendance->early_leaving = gmdate('H:i:s', $earlyLeaving);
                        $employeeAttendance->overtime = '00:00:00';
                    } else {
                        $overtime = $actualEndTime - $expectedEndTime;
                        $employeeAttendance->overtime = gmdate('H:i:s', $overtime);
                        $employeeAttendance->early_leaving = '00:00:00';
                    }
                }
                $employeeAttendance->save();
            }
        } elseif ($attendanceRequest->type === 'clock_out') {
            // ---- CLOCK OUT REQUEST ----
            $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                ->where('date', $date)
                ->where('clock_out', '00:00:00')
                ->first();

            if ($attendance) {
                $attendance->clock_out = $time;

                // Calculate early leaving and overtime
                $expectedEndTime = strtotime($date . ' ' . $endTime);
                $actualEndTime   = strtotime($date . ' ' . $time);

                if ($actualEndTime < $expectedEndTime) {
                    $earlyLeaving = $expectedEndTime - $actualEndTime;
                    $attendance->early_leaving = gmdate('H:i:s', $earlyLeaving);
                    $attendance->overtime = '00:00:00';
                } else {
                    $overtime = $actualEndTime - $expectedEndTime;
                    $attendance->overtime = gmdate('H:i:s', $overtime);
                    $attendance->early_leaving = '00:00:00';
                }

                $attendance->save();
            }
        }

        return redirect()->back()->with('success', __('Request approved successfully and attendance updated.'));
    }

    public function decline(AttendanceRequest $attendanceRequest)
    {
        // Permission check
        if (!\Auth::user()->can('Decline Attendance Request')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $attendanceRequest->update([
            'status' => 'declined',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        return back()->with('error', __('Request declined.'));
    }

    public function destroy(AttendanceRequest $attendanceRequest)
    {
        // Permission check
        if (!\Auth::user()->can('Delete Attendance Request')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $attendanceRequest->delete();
        return redirect()->route('attendance-requests.index')->with('success', __('Request deleted successfully.'));
    }

    public function clockOut(Request $request, $id)
    {
        $attendanceRequestData = AttendanceRequest::where('id', $id)->first();
        if ($attendanceRequestData) {
            AttendanceRequest::where('id', $attendanceRequestData->id)->update(['clock_out' => date("H:i:s")]);
            return redirect()->route('dashboard')->with('success', __('Employee successfully clock Out.'));
        }
        return redirect()->route('dashboard')->with('success', __('Request Not Found.'));
    }

    // public function storeFromAnotherPlatform(Request $request)
    // {
    //     $request->validate([
    //         'type'   => 'required|in:clock_in,clock_out',
    //         'reason' => 'nullable|string|max:255',
    //     ]);

    //     $user = Auth::user();
    //     $employee = Employee::where('user_id', $user->id)->firstOrFail();
    //     $date = date("Y-m-d");
    //     $time = date("H:i:s");

    //     $attendanceRequest = AttendanceRequest::create([
    //         'employee_id'  => $employee->id,
    //         'type'         => $request->type,
    //         'reason'       => $request->reason ?? null,
    //         'status'       => 'pending',
    //         'requested_at' => $date . ' ' . $time,
    //         'created_by'   => Auth::user()->creatorId(),
    //     ]);


    //     $settings = Utility::settings();
    //     if (isset($settings['attendance_request']) && $settings['attendance_request'] == 1) {
    //         $companyOwner = User::find(Auth::user()->creatorId());

    //         $hrUsers = $companyOwner->companyHrs();
    //         $hrEmails = $hrUsers->pluck('email')->toArray();

    //         // Map clock_in / clock_out → readable text
    //         $typeReadable = $request->type === 'clock_in' ? 'Clock In' : 'Clock Out';

    //         // Format date/time for email only
    //         $emailDate = date($settings['site_date_format'] ?? 'Y-m-d');
    //         $emailTime = date($settings['site_time_format'] ?? 'H:i');

    //         // Mail variables
    //         $uArr = [
    //             'company_name'   => $companyOwner->name ?? 'Company',
    //             'employee_name'  => $employee->name,
    //             'employee_email' => $employee->email ?? '',
    //             'type'           => $typeReadable,
    //             'date'           => $emailDate,
    //             'time'           => $emailTime,
    //             'reason'         => $request->reason ?? 'N/A',
    //             'url'            => route('attendance-requests.index'),
    //         ];

    //         // Combine recipients: company owner + HRs
    //         $recipients = array_merge([$companyOwner->email], $hrEmails);

    //         // Send to each recipient
    //         foreach ($recipients as $recipient) {
    //             Utility::sendEmailTemplate('attendance_request', $recipient, $uArr);
    //         }
    //     }

    //     return redirect()->back()->with('success', __('Your request has been sent and is pending approval.'));
    // }
}
