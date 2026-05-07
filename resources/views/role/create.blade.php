    {{ Form::open(['url' => 'roles', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Role Name')]) }}
                </div>
                @error('name')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>


            <div class="form-group">
                @if (!empty($permissions))
                    <h6 class="my-3">{{ __('Assign Permission to Roles') }} </h6>
                    <table class="table  mb-0" id="dataTable-1">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="align-middle checkbox_middle form-check-input"
                                        name="checkall" id="checkall">
                                </th>
                                <th>{{ __('Module') }} </th>
                                <th>{{ __('Permissions') }} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php

                                $modules = [
                                    'Report',
                                    'User',
                                    'Role',
                                    'Employee Profile',
                                    'Employee Last Login',
                                    'Attendance Request',
                                    'Employee',
                                    'Set Salary',
                                    'Pay Slip',
                                    'TimeSheet',
                                    'Leave',
                                    'Attendance',
                                    'Indicator',
                                    'Appraisal',
                                    'Goal Tracking',
                                    'Account List',
                                    'Payee',
                                    'Payer',
                                    'Deposit',
                                    'Expense',
                                    'Transfer Balance',
                                    'Training',
                                    'Trainer',
                                    'Award',
                                    'Transfer',
                                    'Resignation',
                                    'Travel',
                                    'Promotion',
                                    'Complaint',
                                    'Warning',
                                    'Termination',
                                    'Announcement',
                                    'Holiday',
                                    'Job',
                                    'Job Application',
                                    'Job OnBoard',
                                    'Interview Schedule',
                                    'Custom Question',
                                    'Career',
                                    'Contract',
                                    'Ticket',
                                    'Event',
                                    'Meeting',
                                    'Assets',
                                    'Document',
                                    'Company Policy',
                                    'Branch',
                                    'Department',
                                    'Subdepartment',
                                    'Designation',
                                    'Wages',
                                    'Shift',
                                    'Leave Type',
                                    'Document Type',
                                    'Payslip Type',
                                    'Allowance',
                                    'Allowance Option',
                                    'Loan',
                                    'Loan Option',
                                    'Training Type',
                                    'Award Type',
                                    'Termination Type',
                                    'Job Category',
                                    'Job Stage',
                                    'Performance Type',
                                    'Competencies',
                                    'Expense Type',
                                    'Income Type',
                                    'Payment Type',
                                    'Contract Type',
                                    'Plan',
                                    'Deduction Option',
                                    'Commission',
                                    'Saturation Deduction',
                                    'Other Payment',
                                    'Bonous',
                                    'Peark',
                                    'Pension',
                                    'Overtime',
                                    'Goal Type',
                                    'Job Application Note',
                                    'Job Application Skill',
                                    'Zoom meeting',
                                ];
                                if (Auth::user()->type == 'super admin') {
                                    $modules[] = 'Language';
                                }

                                $permissionTypes = [
                                    'Manage',
                                    'Create',
                                    'Edit',
                                    'Reset Password',
                                    'Login Enable Disable',
                                    'Delete',
                                    'Show',
                                    'Move',
                                    'client permission',
                                    'invite user',
                                    'buy',
                                    'Add',
                                    'Approve',
                                    'Decline',
                                ];
                            @endphp
                            @foreach ($modules as $module)
                                @php
                                    $hasPermission = false;
                                    foreach ($permissionTypes as $type) {
                                        if (in_array($type . ' ' . $module, (array) $permissions)) {
                                            $hasPermission = true;
                                            break;
                                        }
                                    }
                                @endphp

                                @if ($hasPermission)
                                    <tr>
                                        <td><input type="checkbox" class="align-middle ischeck form-check-input"
                                                name="checkall" data-id="{{ str_replace(' ', '', $module) }}"></td>
                                        <td><label class="ischeck form-label"
                                                data-id="{{ str_replace(' ', '', $module) }}">{{ ucfirst($module) }}</label>
                                        </td>
                                        <td>
                                            <div class="row">
                                                @foreach ($permissionTypes as $type)
                                                    @php
                                                        $permissionKey = array_search(
                                                            $type . ' ' . $module,
                                                            (array) $permissions,
                                                        );
                                                    @endphp
                                                    @if ($permissionKey !== false)
                                                        <div class="col-md-3 custom-control custom-checkbox">
                                                            {{ Form::checkbox('permissions[]', $permissionKey, null, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $permissionKey]) }}
                                                            {{ Form::label('permission' . $permissionKey, ucfirst($type), ['class' => 'form-label font-weight-500', 'style' => 'white-space: normal;']) }}
                                                            <br>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
    {{ Form::close() }}

    <script>
        $(document).ready(function() {
            $("#checkall").click(function() {
                $('input:checkbox').not(this).prop('checked', this.checked);
            });
            $(".ischeck").click(function() {
                var ischeck = $(this).data('id');
                $('.isscheck_' + ischeck).prop('checked', this.checked);
            });
        });
    </script>
