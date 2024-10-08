<?php

return [
    'employee' => 'Employee',
    'employees' => 'Employees',
    'module_title' => 'Manage all employees',
    'module_quote' => 'Take care of your Employees & they will take care of your Business.',
    'module_description' => 'Employees are your asset. You can manage all your employee records here.',
    'exists' => 'Duplicate employee details found.',
    'permission_denied' => 'You don\'t have permission to access this employee records.',
    'no_employee_associated' => 'No employee record associated with this user.',
    'joining_date_less_than_leaving_date' => 'Employee joining date cannot be less than :attribute.',
    'audience_types' => [
        'all' => 'All Employee',
        'department_wise' => 'Department Wise Employee',
        'designation_wise' => 'Designation Wise Employee',
    ],
    'department' => [
        'department' => 'Department',
        'departments' => 'Departments',
        'module_title' => 'Manage all Departments',
        'module_description' => 'Departments are division of your company dealing with a specific area of activity.',
        'module_example' => 'Admin, Finance, Human Resource are some examples of Departments.',
        'props' => [
            'name' => 'Name',
            'alias' => 'Alias',
            'description' => 'Description',
        ],
    ],
    'designation' => [
        'designation' => 'Designation',
        'designations' => 'Designations',
        'module_title' => 'Manage all Designations',
        'module_description' => 'Designations are the official job titles given to employees of your company.',
        'module_example' => 'Chief Executive Officer, Director, Manager are some examples of Designations.',
        'props' => [
            'name' => 'Name',
            'alias' => 'Alias',
            'parent' => 'Parent',
            'description' => 'Description',
        ],
    ],
    'status' => 'Status',
    'statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    'type' => 'Type',
    'types' => [
        'administrative' => 'Administrative Staff',
        'teaching' => 'Teaching Staff',
        'support' => 'Support Staff',
    ],
    'config' => [
        'config' => 'Config',
        'props' => [
            'number_prefix' => 'Code Number Prefix',
            'number_suffix' => 'Code Number Suffix',
            'number_digit' => 'Code Number Digit',
            'unique_id_number1_label' => 'Unique ID 1 Label',
            'unique_id_number2_label' => 'Unique ID 2 Label',
            'unique_id_number3_label' => 'Unique ID 3 Label',
            'unique_id_number1_required' => 'Unique ID 1 Required',
            'unique_id_number2_required' => 'Unique ID 2 Required',
            'unique_id_number3_required' => 'Unique ID 3 Required',
            'allow_employee_to_submit_contact_edit_request' => 'Allow employee to submit contact edit request',
        ],
    ],
    'props' => [
        'name' => 'Employee Name',
        'number' => 'Code Digit',
        'code_number' => 'Employee Code',
        'joining_date' => 'Date of Joining',
        'leaving_date' => 'Date of Leaving',
    ],
    'employment_status' => [
        'employment_status' => 'Employment Status',
        'module_title' => 'Manage all Employment Status',
        'module_description' => 'Employment Status is a relationship between your company & your employee.',
        'module_example' => 'Permanent, Probation, Contract are some examples of Employment Status.',
        'props' => [
            'name' => 'Name',
            'description' => 'Description',
        ],
    ],
    'employment_type' => [
        'employment_type' => 'Employment Type',
        'module_title' => 'Manage all Employment Types',
        'module_description' => 'Employment Types are the nature of employment your company offers to your employees.',
        'module_example' => 'Full Time, Part Time, Interns are some examples of Employment Types.',
        'props' => [
            'name' => 'Name',
            'description' => 'Description',
        ],
    ],
    'document_type' => [
        'document_type' => 'Document Type',
        'document_types' => 'Document Types',
        'module_title' => 'List all Document Types',
        'module_description' => 'Manage all Document Types',
        'props' => [
            'name' => 'Name',
            'description' => 'Description',
        ],
    ],
    'qualification_level' => [
        'qualification_level' => 'Qualification Level',
        'module_title' => 'Manage all Qualification Levels',
        'module_description' => 'Qualification levels are the standards of different programs the institutes can offer.',
        'module_example' => 'Graduate, Post Graduate, Doctorate are some examples of Qualification Levels.',
        'props' => [
            'name' => 'Name',
            'description' => 'Description',
        ],
    ],
    'qualification' => [
        'qualification' => 'Qualification',
        'module_title' => 'Manage all Employee Qualification Records',
        'module_description' => 'Keep the documents related to your employee\'s qualification.',
        'props' => [
            'course' => 'Course',
            'institute' => 'Institute',
            'type' => 'Type',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'affiliated_to' => 'Affiliated To',
            'result' => 'Result',
        ],
    ],
    'document_type' => [
        'document_type' => 'Document Type',
        'module_title' => 'Manage all Document Types',
        'module_description' => 'Document Types are the different categories of documents your company wish to store for the employees.',
        'module_example' => 'Resume, Salary Certificate, Experience Certificates are some examples of Document Types.',
        'props' => [
            'name' => 'Name',
            'description' => 'Description',
        ],
    ],
    'document' => [
        'document' => 'Document',
        'module_title' => 'Manage all Employee Documents',
        'module_description' => 'Categorize the documents related to your employees.',
        'expired' => 'Expired',
        'expiring_in_days' => 'Expiring in :attribute day(s)',
        'props' => [
            'title' => 'Title',
            'start_date' => 'Validity Start',
            'end_date' => 'Validity End',
            'description' => 'Description',
        ],
    ],
    'experience' => [
        'experience' => 'Experience',
        'module_title' => 'Manage all Employee Experience Records',
        'module_description' => 'Keep records of your employee\'s previous experience.',
        'props' => [
            'headline' => 'Headline',
            'title' => 'Title',
            'location' => 'Location',
            'organization_name' => 'Organization Name',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'job_profile' => 'Job Profile',
        ],
    ],
    'could_not_edit_self_service_upload' => 'Could not edit self service upload record.',
    'record' => [
        'record' => 'Employment',
        'module_title' => 'Manage all Employment Records',
        'module_description' => 'Keep records of promotion, transfer of your employees.',
        'end_date_lt_start_date' => 'End date must be greater than start date.',
        'start_date_lt_previous_start_date' => 'Employment start date must be greater than previous start date.',
        'could_not_perform_if_no_change' => 'Could not perform this operation as there is no change in employment record.',
        'could_not_perform_if_employment_ended' => 'Could not perform this operation as employment has already ended.',
        'props' => [
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'period' => 'Period',
            'duration' => 'Duration',
            'remarks' => 'Remarks',
            'end' => 'End Employment',
        ],
    ],
    'account' => [
        'account' => 'Account',
        'accounts' => 'Accounts',
        'module_title' => 'Manage all Account Records',
        'module_description' => 'Keep all account related information of your employees.',
    ],
    'leave' => [
        'leave' => 'Leave',
        'on_leave' => 'On Leave',
        'leave_without_pay' => 'Leave Without Pay',
        'leave_without_pay_short' => 'LWP',
        'request' => [
            'request' => 'Leave Request',
            'module_title' => 'Manage all Leave Requests',
            'module_description' => 'Leave requests submitted by your employees can be approved, rejected by authorized employee.',
            'action' => 'Action',
            'status_is_not_requested' => 'Could not update leave request if status is not requested.',
            'range_exists' => 'Leave request for the employee already exists between :start and :end.',
            'could_not_perform_if_status_updated' => 'Could not perform this operation if status is already updated.',
            'could_not_perform_if_payroll_generated' => 'Could not perform this operation if payroll is generated for this duration.',
            'statuses' => [
                'requested' => 'Requested',
                'rejected' => 'Rejected',
                'approved' => 'Approved',
                'withdrawn' => 'Withdrawn',
            ],
            'props' => [
                'start_date' => 'Start Date',
                'end_date' => 'End Date',
                'status' => 'Status',
                'reason' => 'Reason',
                'requester' => 'Requester',
                'approver' => 'Approver',
                'comment' => 'Comment',
                'period' => 'Period',
                'duration' => 'Duration',
            ],
        ],
        'allocation' => [
            'allocation' => 'Leave Allocation',
            'module_title' => 'Manage all Leave Allocation',
            'module_description' => 'Assign leave allocation to your employees for a particular duration.',
            'range_exists' => 'Leave allocation for the employee already exists between :start and :end.',
            'use_count_gt_allocated' => 'Allotted leave :allotted cannot less than use count :used and pending request count.',
            'start_date_gt_first_leave_request_date' => 'Start date cannot greater than first leave request date :date.',
            'end_date_lt_last_leave_request_date' => 'End date cannot less than last leave request date :date.',
            'could_not_perform_if_leave_requested' => 'Could not perform this operation if leave request is already made.',
            'could_not_perform_if_leave_utilized' => 'Could not perform this operation if leave is already utilized.',
            'props' => [
                'start_date' => 'Start Date',
                'end_date' => 'End Date',
                'description' => 'Description',
                'allotted' => 'Leaves Allotted',
                'used' => 'Leaves Used',
            ],
        ],
        'config' => [
            'config' => 'Config',
            'props' => [
                'allow_employee_request_leave_with_exhausted_credit' => 'Allow Employees to Request Leave with Exhausted Credit',
            ],
        ],
        'type' => [
            'type' => 'Leave Type',
            'module_title' => 'Manage all Leave Types',
            'module_description' => 'Leave Type defines the category of each type of leave available to employees of your company.',
            'module_example' => 'Sick leave, Casual leave, Maternity leave are some examples of Leave Types.',
            'no_allocation_found' => 'Could not find any leave allocation for this leave type.',
            'balance_exhausted' => 'Available leave balance is :balance, cannot request for :duration day(s) leave.',
            'props' => [
                'name' => 'Name',
                'code' => 'Code',
                'alias' => 'Alias',
                'description' => 'Description',
            ],
        ],
    ],
    'attendance' => [
        'attendance' => 'Attendance',
        'attendances' => 'Attendances',
        'employee_attendance' => 'Employee Attendance',
        'module_title' => 'List all Employee Attendance',
        'module_description' => 'Filter & get your employee\'s monthly attendance record either day wise or attendance head wise.',
        'is_time_based' => 'Time based Attendance',
        'mark' => 'Mark Attendance',
        'mark_production' => 'Mark Production based Attendance',
        'filter_record' => 'Filter records to get list of employee.',
        'not_marked' => 'Attendance not marked for given data.',
        'could_not_perform_if_payroll_generated' => 'Could not perform this operation as payroll is generated.',
        'could_not_perform_if_attendance_synched' => 'Could not remove attendance as attendance is synched.',
        'day_wise' => 'Day Wise',
        'config' => [
            'config' => 'Config',
            'props' => [
                'allow_employee_clock_in_out' => 'Allow Employees to Clock In/Out',
                'duration_between_clock_request' => 'Duration between Clock In/Out Request',
                'allow_employee_clock_in_out_via_device' => 'Allow Employees to Clock In/Out via Device',
                'late_grace_period' => 'Grace Period (Late)',
                'late_grace_period_tip' => 'Arrival time in minutes after which attendance is considered as late',
                'early_leaving_grace_period' => 'Grace Period (Early Leaving)',
                'early_leaving_grace_period_tip' => 'Leaving time in minutes before which attendance is considered as early leaving',
                'present_grace_period' => 'Grace Period (Overall)',
                'present_grace_period_tip' => 'Working time in minutes before which attendance is considered as present',
                'enable_geolocation_timesheet' => 'Enable Geolocation Timesheet',
                'geolocation_latitude' => 'Geolocation Latitude',
                'geolocation_longitude' => 'Geolocation Longitude',
                'geolocation_radius' => 'Geolocation Radius',
            ],
        ],
        'props' => [
            'date' => 'Date of Attendance',
            'remarks' => 'Remarks',
            'value' => 'Value',
        ],
        'categories' => [
            'present' => 'Present',
            'holiday' => 'Holiday',
            'absent' => 'Absent',
            'leave' => 'Leave',
            'half_day' => 'Half Day',
            'production_based_earning' => 'Production based Earning',
            'production_based_deduction' => 'Production based Deduction',
        ],
        'sub_categories' => [
            'late' => 'Late',
            'early_leaving' => 'Early Leaving',
            'overtime' => 'Overtime',
        ],
        'production_units' => [
            'hourly' => 'Hourly',
        ],
        'type' => [
            'type' => 'Attendance Type',
            'types' => 'Attendance Types',
            'module_title' => 'Manage all Attendance Types',
            'module_description' => 'Attendance Type defines the category of each type of attendance available to employees of your company.',
            'module_example' => 'Present, Late, Absent are some examples of Attendance Types.',
            'could_not_perform_if_attendance_is_marked' => 'Could not perform this operation if attendance is marked.',
            'props' => [
                'name' => 'Name',
                'alias' => 'Alias',
                'code' => 'Code',
                'color' => 'Color',
                'category' => 'Category',
                'unit' => 'Unit',
                'description' => 'Description',
            ],
        ],
        'timesheet' => [
            'timesheet' => 'Timesheet',
            'timesheets' => 'Timesheets',
            'recently_marked' => 'You have recently marked attendance.',
            'could_not_perform_without_work_shift' => 'Could not find any work shift.',
            'start_time_should_less_than_end_time' => 'Start time should less than end time.',
            'overnight_start_time_should_greater_than_end_time' => 'Start time should greater than end time for overnight shift.',
            'range_exists' => 'Timesheet already exists between :start and :end.',
            'could_not_perform_if_empty_out_at' => 'Could not perform this operation if any timesheet out time is empty.',
            'could_not_perform_if_attendance_synched' => 'Could not perform this operation as attendance is already synched.',
            'max_sync_count_limit_exceed' => 'Max sync count limit exceed.',
            'choose_date_range_to_sync' => 'Filter by date range to sync timesheet.',
            'already_synched' => 'Timesheet already synched for given employee on date.',
            'module_title' => 'Manage all Timesheets',
            'module_description' => 'Timesheet is the record of employee\'s attendance for a particular day.',
            'geolocation_not_supported' => 'Sorry! Your browser doesn\'t support Geolocation.',
            'unable_to_detect_geolocation' => 'Unable to detect your current location.',
            'could_not_mark_attendance_outside_geolocation' => 'You are :distance mtr away. Could not mark attendance outside geolocation.',
            'minimum_diff_between_clock_in_out' => 'Minimum difference between clock in and clock out time should be :attribute minutes.',
            'statuses' => [
                'ok' => 'OK',
                'missing_attendance_type' => 'Missing Attendance Type',
                'manual_attendance' => 'Manual Attendance',
                'already_synched' => 'Already Synched',
            ],
            'props' => [
                'manual' => 'Manual',
                'clock_in' => 'Clock In',
                'clock_out' => 'Clock Out',
                'in_at' => 'In at',
                'out_at' => 'Out at',
                'date' => 'Date',
                'duration' => 'Duration',
                'remarks' => 'Remarks',
            ],
        ],
        'work_shift' => [
            'work_shift' => 'Work Shift',
            'work_shifts' => 'Work Shifts',
            'module_title' => 'Manage all Work Shifts',
            'module_description' => 'Work Shifts are the durations of time in which employees are expected to work.',
            'start_time_should_less_than_end_time' => 'Start time should less than end time.',
            'overnight_start_time_should_greater_than_end_time' => 'Start time should greater than end time for overnight shift.',
            'all_days_should_be_filled' => 'There are some missing or mismatch days in records.',
            'range_exists' => 'Work Shift for the employee already exists between :start and :end.',
            'assign' => 'Assign Work Shift',
            'props' => [
                'name' => 'Name',
                'code' => 'Code',
                'start_time' => 'Start Time',
                'end_time' => 'End Time',
                'start_date' => 'Start Date',
                'end_date' => 'End Date',
                'is_holiday' => 'Holiday',
                'is_overnight' => 'Overnight',
                'description' => 'Description',
                'remarks' => 'Remarks',
            ],
        ],
    ],
    'payroll' => [
        'payroll' => 'Payroll',
        'module_title' => 'Manage all Payrolls',
        'module_description' => 'Generate employee\'s Payroll based on the Attendance & Leave records for a particular duration.',
        'could_not_perform_if_payroll_generated_for_later_date' => 'Could not perform this operation if payroll already generated for later date.',
        'range_exists' => 'Payroll for the employee already generated between :start and :end.',
        'salary_slip' => 'Salary Slip',
        'authorized_signatory' => 'Authorized Signatory',
        'footer_info' => 'This is a computer generated document and does not require any signature.',
        'props' => [
            'code_number' => 'Payroll #',
            'amount' => 'Amount',
            'total' => 'Total',
            'paid' => 'Paid',
            'balance' => 'Balance',
            'status' => 'Status',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'remarks' => 'Remarks',
            'period' => 'Period',
            'duration' => 'Duration',
        ],
        'transaction' => [
            'transaction' => 'Transaction',
            'props' => [
                'amount' => 'Amount',
            ],
        ],
        'variables' => [
            'working_days' => 'Working Days',
            'monthly_days' => 'Monthly Days',
            'gross_earning' => 'Gross Earning',
            'gross_deduction' => 'Gross Deduction',
            'employee_contribution' => 'Employee Contribution',
            'employer_contribution' => 'Employer Contribution',
            'earning_component' => 'Earning Component',
            'deduction_component' => 'Deduction Component',
        ],
        'pay_head' => [
            'pay_head' => 'Pay Head',
            'module_title' => 'Manage all Pay Heads',
            'module_description' => 'Pay Head defines the components constituting salary structure of the employees of your company.',
            'module_example' => 'Basic Salary, Dearness Allowance, Provident Fund are some examples of Pay Heads.',
            'could_not_perform_if_associated_with_salary_template' => 'Could not perform this operation if pay head is associated with salary template.',
            'props' => [
                'name' => 'Name',
                'code' => 'Code',
                'alias' => 'Alias',
                'category' => 'Category',
                'description' => 'Description',
            ],
            'categories' => [
                'earning' => 'Earning',
                'deduction' => 'Deduction',
                'employee_contribution' => 'Employee Contribution',
                'employer_contribution' => 'Employer Contribution',
            ],
            'types' => [
                'not_applicable' => 'Not Applicable',
                'attendance_based' => 'Attendance Based',
                'flat_rate' => 'Flat Rate',
                'user_defined' => 'User Defined',
                'computation' => 'Computation',
                'production_based' => 'Production Based',
            ],
        ],
        'salary' => 'Salary',
        'salary_template' => [
            'salary_template' => 'Salary Template',
            'module_title' => 'Manage all Salary Templates',
            'module_description' => 'Salary Templates are predefined format of different pay heads used to create salary structure for your employees.',
            'invalid_computation' => 'Invalid computation found.',
            'hourly_payroll_info' => 'This salary template is used for hourly payroll.',
            'conditional_pay_head_info' => 'This pay head will be calculated based on the condition.',
            'earning_component_cannot_be_referenced_in_earning_pay_head' => 'Earning component cannot be referenced in earning pay head.',
            'deduction_component_cannot_be_referenced_in_earning_pay_head' => 'Deduction component cannot be referenced in earning pay head.',
            'deduction_component_cannot_be_referenced_in_deduction_pay_head' => 'Deduction component cannot be referenced in deduction pay head.',
            'computation_contains_self_pay_head' => 'Computation contains self pay head.',
            'conditional_formula_if' => 'If :pay_head is :condition :value',
            'conditional_formula_then' => 'then :formula',
            'variable_info' => 'You can use variables in the formula field. Variables are : :variable',
            'props' => [
                'name' => 'Name',
                'alias' => 'Alias',
                'description' => 'Description',
                'type' => 'Type',
                'hourly_payroll' => 'Hourly Payroll',
                'computation' => 'Computation',
                'min_value' => 'Min Value',
                'max_value' => 'Max Value',
                'has_range' => 'Has Range',
                'doesnt_have_range' => 'Does not have range',
                'has_condition' => 'Conditional',
                'operator' => 'Operator',
                'value' => 'Value',
                'condition' => 'Condition',
                'reference_pay_head' => 'Reference Pay Head',
                'conditional_value' => 'Conditional Value or Formula',
            ],
        ],
        'salary_structure' => [
            'salary_structure' => 'Salary Structure',
            'module_title' => 'Manage all Salary Structures',
            'module_description' => 'Salary structure is the details of the salary being offered, in terms of the breakup of the different components constituting the compensation.',
            'could_not_perform_if_defined_for_later_date' => 'Could not perform this operation if salary structure is already defined for later date.',
            'could_not_perform_if_payroll_generated' => 'Could not perform this operation if payroll already generated before this date.',
            'could_not_perform_if_payroll_generated_with_salary_structure' => 'Could not perform this operation if payroll already generated with this salary structure.',
            'hourly_pay_amount' => 'Hourly Pay :attribute',
            'hourly_payroll_info' => 'This salary structure has hourly pay of :attribute.',
            'working_hours' => 'Working Hours',
            'units' => [
                'monthly' => 'Monthly',
                'hourly' => 'Hourly',
            ],
            'props' => [
                'effective_date' => 'Effective Date',
                'hourly_pay' => 'Hourly Pay',
                'net_earning' => 'Net Earning',
                'net_deduction' => 'Net Deduction',
                'net_employee_contribution' => 'Employee Contribution',
                'net_employer_contribution' => 'Employer Contribution',
                'net_salary' => 'Net Salary',
                'amount' => 'Amount',
                'description' => 'Description',
            ],
        ],
        'config' => [
            'props' => [
                'number_prefix' => 'Code Number Prefix',
                'number_suffix' => 'Code Number Suffix',
                'number_digit' => 'Code Number Digit',
            ],
        ],
    ],
    'incharge' => [
        'incharge' => 'Incharge',
        'incharges' => 'Incharges',
        'module_title' => 'List all Incharges',
        'module_description' => 'Manage all Incharges',
        'duplicate' => 'Duplicate record found.',
        'period_not_ended' => 'Previous incharge period not yet ended.',
        'overlapping_period' => 'Previous incharge period is overlapping with given period.',
        'props' => [
            'period' => 'Period',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'remarks' => 'Remarks',
        ],
    ],
    'edit_info' => 'If you want to edit any information, you can submit a request here.',
    'upload_document_info' => 'You can also upload document for proof of your edit request.',
    'edit_request' => [
        'edit_request' => 'Edit Request',
        'module_title' => 'List all Edit Requests',
        'module_description' => 'Manage all Edit Requests for Employee Information',
        'edit_info' => 'If you want to edit any information, you can submit a request here.',
        'upload_document_info' => 'You can also upload document for proof of your edit request.',
        'submitted' => 'Your request for edit information is submitted and applied after approval.',
        'already_pending' => 'You have already submitted a request for edit information.',
        'request_by' => 'Request By',
        'request_by_name' => 'Request By :attribute',
        'already_processed' => 'This request is already processed.',
        'statuses' => [
            'approve' => 'Approve',
            'reject' => 'Reject',
        ],
        'props' => [
            'action' => 'Action',
            'comment' => 'Comment',
            'status' => 'Status',
        ],
    ],
];
