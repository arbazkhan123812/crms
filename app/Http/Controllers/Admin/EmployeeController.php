<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }


    public function index(Request $request)
    {
        $query = Employee::with(['company', 'department', 'designation', 'user', 'reportingTo']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(15);

        $companies = Company::all();
        $departments = Department::all();
        $designations = Designation::all();
        $employmentTypes = Employee::distinct('employment_type')->pluck('employment_type');
        $statuses = ['active', 'inactive', 'suspended', 'terminated'];

        return view('admin.employees.index', compact(
            'employees',
            'companies',
            'departments',
            'designations',
            'employmentTypes',
            'statuses'
        ));
    }

    public function create(Request $request)
    {
        $companies = Company::all();
        $departments = collect();
        $designations = collect();
        $managers = collect();

        $departments = Department::where('is_active', true)
            ->get();

        $designations = Designation::where('is_active', true)
            ->get();

        $managers = Employee::where('is_reporting_manager', true)
            ->get();


        return view('admin.employees.create', compact('companies', 'departments', 'designations', 'managers'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic Information
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'personal_email' => 'required|email|unique:employees,personal_email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female',
            'photo' => 'nullable|image|max:2048',

            // Work Information
            'email' => 'required|email|unique:employees,email',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'reporting_to_id' => 'nullable|exists:employees,id',
            'is_reporting_manager' => 'nullable|boolean',
            'employee_type' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'employee_level' => 'nullable|string',
            'joining_date' => 'required|date',
            'ctc' => 'nullable|numeric',
            'seat_location' => 'nullable|string',
            'extension' => 'nullable|string',
            'experience_status' => 'required|in:fresher,experienced',

            // Personal Information
            'birth_date' => 'required|date',
            'present_address_line1' => 'nullable|string',
            'present_address_line2' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'permanent_address_line1' => 'nullable|string',
            'permanent_address_line2' => 'nullable|string',
            'permanent_city' => 'nullable|string',
            'permanent_state' => 'nullable|string',
            'permanent_country' => 'nullable|string',
            'permanent_postal_code' => 'nullable|string',
            'father_name' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'hobbies' => 'nullable|string',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'blood_group' => 'required|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_relation' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
            'religion' => 'nullable|string',
            'nationality' => 'nullable|string',

            // Bank Information
            'bank_name' => 'nullable|string',
            'account_title' => 'nullable|string',
            'account_number' => 'nullable|string',
            'iban' => 'nullable|string',
            'basic_salary' => 'nullable|numeric',
            'hra' => 'nullable|numeric',
            'da' => 'nullable|numeric',
            'conveyance' => 'nullable|numeric',
            'medical_allowance' => 'nullable|numeric',
            'special_allowance' => 'nullable|numeric',
            'probation_period' => 'nullable|integer',
            'notice_period' => 'nullable|integer',

            // Education (dynamic)
            'education' => 'nullable|array',
            'education.*.course' => 'required_with:education|string',
            'education.*.institution' => 'required_with:education|string',
            'education.*.marks' => 'required_with:education|numeric',
            'education.*.year' => 'required_with:education|string',

            // Experience (dynamic)
            'experience' => 'nullable|array',
            'experience.*.company' => 'required_with:experience|string',
            'experience.*.designation' => 'required_with:experience|string',
            'experience.*.from' => 'required_with:experience|date',
            'experience.*.to' => 'nullable|date',


        ]);

        DB::beginTransaction();

        try {
            $company = Company::first(); 

            $employeeData = [
                'company_id' => $company->id,
                'cnic' => $request->cnic,
                'password' => $request->password,
                'employee_code' => $this->employeeService->generateEmployeeCode($company),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'full_name' => $request->first_name . ' ' . $request->last_name,
                'personal_email' => $request->personal_email,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'reporting_to_id' => $request->reporting_to_id,
                'is_reporting_manager' => $request->is_reporting_manager ? true : false,
                'employment_type' => $request->employee_type ?? 'permanent',
                'employee_level' => $request->employee_level,
                'joining_date' => $request->joining_date,
                'ctc' => $request->ctc,
                'seat_location' => $request->seat_location,
                'extension' => $request->extension,
                'experience_status' => $request->experience_status,
                'birth_date' => $request->birth_date,
                'present_address_line1' => $request->present_address_line1,
                'present_address_line2' => $request->present_address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country ?? 'Pakistan',
                'postal_code' => $request->postal_code,
                'permanent_address_line1' => $request->permanent_address_line1,
                'permanent_address_line2' => $request->permanent_address_line2,
                'permanent_city' => $request->permanent_city,
                'permanent_state' => $request->permanent_state,
                'permanent_country' => $request->permanent_country ?? 'Pakistan',
                'permanent_postal_code' => $request->permanent_postal_code,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'hobbies' => $request->hobbies,
                'marital_status' => $request->marital_status,
                'blood_group' => $request->blood_group,
                'confirmation_date' => $request->confirmation_date,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_relation' => $request->emergency_relation,
                'emergency_phone' => $request->emergency_phone,
                'religion' => $request->religion,
                'nationality' => $request->nationality ?? 'Pakistani',

                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_title,
                'account_number' => $request->account_number,
                'iban' => $request->iban,
                'basic_salary' => $request->basic_salary,
                'hra' => $request->hra,
                'da' => $request->da,
                'conveyance' => $request->conveyance,
                'medical_allowance' => $request->medical_allowance,
                'special_allowance' => $request->special_allowance,
                'probation_period' => $request->probation_period ?? 6,
                'notice_period' => $request->notice_period ?? 30,
                'status' => 'active'
            ];

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('uploads', 'public');
                $employeeData['profile_image'] = $path;
            }

            $employee = Employee::create($employeeData);

            if ($request->has('education')) {
                foreach ($request->education as $edu) {
                    if (!empty($edu['course'])) {
                        $employee->education()->create([
                            'course' => $edu['course'],
                            'institution' => $edu['institution'],
                            'marks' => $edu['marks'],
                            'year' => $edu['year']
                        ]);
                    }
                }
            }

            if ($request->has('experience')) {
                foreach ($request->experience as $exp) {
                    if (!empty($exp['company'])) {
                        $employee->experience()->create([
                            'company' => $exp['company'],
                            'designation' => $exp['designation'],
                            'from_date' => $exp['from'],
                            'to_date' => $exp['to'] ?? null,
                            'is_current' => empty($exp['to']) ? true : false
                        ]);
                    }
                }
            }

            if ($request->has('assets')) {
                foreach ($request->assets as $asset) {
                    if (!empty($asset['name'])) {
                        $employee->assets()->create([
                            'asset_type' => $asset['type'],
                            'asset_name' => $asset['name'],
                            'serial_no' => $asset['serial_no'],
                            'status' => $asset['status'] ?? 'assigned',
                            'given_on' => $asset['given_on'] ?? now()
                        ]);
                    }
                }
            }



            $result =  $this->employeeService->createUserAccount($employee, $request->password ?? 'password');

            
            DB::commit();
            

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee created successfully. Employee Code: ' . $request->employee_code);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee creation failed: ' . $e->getMessage());
            return back()->with('error', 'Error creating employee: ' . $e->getMessage())->withInput();
        }
    }


    public function show(Employee $employee)
    {
        $employee->load([
            'company',
            'department',
            'designation',
            'user',
            'reportingTo',
            'subordinates',
            'education',
            'experience',
            'assets'
        ]);

        $age = $employee->birth_date ? $employee->birth_date->age : null;
        $totalExperience = $employee->total_experience;

        return response()->json([
            'employee' => $employee,
            'age' => $age,
            'totalExperience' => $totalExperience,
            'education' => $employee->education,
            'experience' => $employee->experience,
            'assets' => $employee->assets
        ]);
    }


    public function edit(Employee $employee)
    {
        $companies = Company::all();
        $departments = Department::where('is_active', true)
            ->get();

        $designations = Designation::where('is_active', true)
            ->get();

        $managers = Employee::where('company_id', $employee->company_id)
            ->where('status', 'active')
            ->where('id', '!=', $employee->id)
            ->get();

        return view('admin.employees.edit', compact('employee', 'companies', 'departments', 'designations', 'managers'));
    }


    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            // Basic Information
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'personal_email' => 'required|email|unique:employees,personal_email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female',
            'photo' => 'nullable|image|max:2048',

            // Work Information
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'work_location' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'reporting_to_id' => 'nullable|exists:employees,id',
            'is_reporting_manager' => 'nullable|boolean',
            'employee_type' => 'nullable|string|in:permanent,contract,intern,trainee,consultant',
            'joining_date' => 'required|date',
            'confirmation_date' => 'nullable|date|after_or_equal:joining_date',
            'probation_period' => 'nullable|integer|min:1|max:24',
            'notice_period' => 'nullable|integer|min:1|max:180',
            'ctc' => 'nullable|numeric|min:0',
            'seat_location' => 'nullable|string|max:255',
            'extension' => 'nullable|string|max:50',
            'experience_status' => 'required|in:fresher,experienced',

            // Personal Information
            'birth_date' => 'required|date',
            'password' => 'required|integer|min:8',
            'cnic' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'religion' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',

            'present_address_line1' => 'nullable|string|max:255',
            'present_address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',

            'permanent_address_line1' => 'nullable|string|max:255',
            'permanent_address_line2' => 'nullable|string|max:255',
            'permanent_city' => 'nullable|string|max:100',
            'permanent_state' => 'nullable|string|max:100',
            'permanent_country' => 'nullable|string|max:100',
            'permanent_postal_code' => 'nullable|string|max:20',

            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'hobbies' => 'nullable|string|max:255',

            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_relation' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',

            // Bank Information
            'bank_name' => 'nullable|string|max:255',
            'account_title' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'basic_salary' => 'nullable|numeric|min:0',
            'hra' => 'nullable|numeric|min:0',
            'da' => 'nullable|numeric|min:0',
            'conveyance' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'special_allowance' => 'nullable|numeric|min:0',

            // Status
            'status' => 'required|in:active,inactive,suspended,terminated',

            // Education (dynamic)
            'education' => 'nullable|array',
            'education.*.course' => 'required_with:education|string|max:255',
            'education.*.institution' => 'required_with:education|string|max:255',
            'education.*.marks' => 'nullable|numeric|min:0|max:100',
            'education.*.year' => 'nullable|string|max:10',

            // Experience (dynamic)
            'experience' => 'nullable|array',
            'experience.*.company' => 'required_with:experience|string|max:255',
            'experience.*.designation' => 'required_with:experience|string|max:255',
            'experience.*.from' => 'required_with:experience|date',
            'experience.*.to' => 'nullable|date',

            // Assets (dynamic)
            'assets' => 'nullable|array',
            'assets.*.type' => 'required_with:assets|string|max:100',
            'assets.*.name' => 'required_with:assets|string|max:255',
            'assets.*.serial_no' => 'nullable|string|max:100',
            'assets.*.status' => 'nullable|in:assigned,returned,damaged',
            'assets.*.given_on' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            // Prepare employee data with ALL fields
            $employeeData = [
                // Basic Information
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'full_name' => trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name),
                'personal_email' => $request->personal_email,
                'email' => $request->email,
                'password' => $request->password,

                'phone' => $request->phone,
                'alternate_phone' => $request->alternate_phone,
                'whatsapp_number' => $request->whatsapp_number,
                'gender' => $request->gender,
                'cnic' => $request->cnic,

                // Work Information
                'work_location' => $request->work_location,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'reporting_to_id' => $request->reporting_to_id,
                'is_reporting_manager' => $request->is_reporting_manager ? true : false,
                'employment_type' => $request->employee_type ?? 'permanent',
                'employee_level' => $request->employee_level,
                'joining_date' => $request->joining_date,
                'confirmation_date' => $request->confirmation_date,
                'probation_period' => $request->probation_period ?? 6,
                'notice_period' => $request->notice_period ?? 30,
                'ctc' => $request->ctc,
                'seat_location' => $request->seat_location,
                'extension' => $request->extension,
                'experience_status' => $request->experience_status,

                // Personal Information
                'birth_date' => $request->birth_date,
                'blood_group' => $request->blood_group,
                'marital_status' => $request->marital_status,
                'religion' => $request->religion,  // FIXED: was 'riligion'
                'nationality' => $request->nationality ?? 'Pakistani',

                // Present Address
                'present_address_line1' => $request->present_address_line1,
                'present_address_line2' => $request->present_address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country ?? 'Pakistan',
                'postal_code' => $request->postal_code,

                // Permanent Address
                'permanent_address_line1' => $request->permanent_address_line1,
                'permanent_address_line2' => $request->permanent_address_line2,
                'permanent_city' => $request->permanent_city,
                'permanent_state' => $request->permanent_state,
                'permanent_country' => $request->permanent_country ?? 'Pakistan',
                'permanent_postal_code' => $request->permanent_postal_code,

                // Family Information
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'hobbies' => $request->hobbies,

                // Emergency Contact
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_relation' => $request->emergency_relation,
                'emergency_phone' => $request->emergency_phone,

                // Bank Information
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_title,
                'account_number' => $request->account_number,
                'iban' => $request->iban,

                // Salary Components
                'basic_salary' => $request->basic_salary,
                'hra' => $request->hra,
                'da' => $request->da,
                'conveyance' => $request->conveyance,
                'medical_allowance' => $request->medical_allowance,
                'special_allowance' => $request->special_allowance,

                // Status
                'status' => $request->status,
            ];

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists

                $path = $request->file('photo')->store('uploads', 'public');
                $employeeData['profile_image'] = $path;
            }

            // Update employee
            $employee->update($employeeData);

            // Update Education (delete old and add new)
            $employee->education()->delete();
            if ($request->has('education')) {
                foreach ($request->education as $edu) {
                    if (!empty($edu['course']) && !empty($edu['institution'])) {
                        $employee->education()->create([
                            'course' => $edu['course'],
                            'institution' => $edu['institution'],
                            'marks' => $edu['marks'],
                            'year' => $edu['year']
                        ]);
                    }
                }
            }

            // Update Experience
            $employee->experience()->delete();
            if ($request->has('experience')) {
                foreach ($request->experience as $exp) {
                    if (!empty($exp['company']) && !empty($exp['designation'])) {
                        $employee->experience()->create([
                            'company' => $exp['company'],
                            'designation' => $exp['designation'],
                            'from_date' => $exp['from'],
                            'to_date' => $exp['to'] ?? null,
                            'is_current' => empty($exp['to']) ? true : false
                        ]);
                    }
                }
            }

            // Update Assets
            $employee->assets()->delete();
            if ($request->has('assets')) {
                foreach ($request->assets as $asset) {
                    if (!empty($asset['name'])) {
                        $employee->assets()->create([
                            'asset_type' => $asset['type'],
                            'asset_name' => $asset['name'],
                            'serial_no' => $asset['serial_no'],
                            'status' => $asset['status'] ?? 'assigned',
                            'given_on' => $asset['given_on'] ?? now()
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee updated successfully. Code: ' . $employee->employee_code);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee update failed: ' . $e->getMessage());
            return back()->with('error', 'Error updating employee: ' . $e->getMessage())->withInput();
        }
    }



    public function destroy(Employee $employee)
    {
        // Check if employee has user account
        if ($employee->user) {
            $employee->user->delete();
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully');
    }


    public function uploadDocument(Request $request, Employee $employee)
    {
        $request->validate([
            'document' => 'required|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
            'type' => 'required|string|max:50'
        ]);

        $path = $this->employeeService->uploadDocument(
            $employee,
            $request->file('document'),
            $request->type
        );

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'path' => $path
        ]);
    }


    public function deleteDocument(Employee $employee, $documentIndex)
    {
        $documents = $employee->documents ?? [];

        if (isset($documents[$documentIndex])) {
            // Delete file from storage
            $path = $documents[$documentIndex]['path'];
            // if (Storage::disk('public')->exists($path)) {
            //     \Storage::disk('public')->delete($path);
            // }

            // Remove from array
            unset($documents[$documentIndex]);
            $documents = array_values($documents); // Reindex

            $employee->update(['documents' => $documents]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }


    public function activate(Employee $employee)
    {
        $employee->update(['status' => 'active']);

        if ($employee->user) {
            $employee->user->update(['is_active' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee activated successfully'
        ]);
    }

    public function deactivate(Employee $employee)
    {
        $employee->update(['status' => 'inactive']);

        if ($employee->user) {
            $employee->user->update(['is_active' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee deactivated successfully'
        ]);
    }

    public function terminate(Request $request, Employee $employee)
    {
        $request->validate([
            'exit_date' => 'required|date',
            'exit_reason' => 'required|string'
        ]);

        $employee->update([
            'status' => 'terminated',
            'exit_date' => $request->exit_date,
            'exit_reason' => $request->exit_reason
        ]);

        if ($employee->user) {
            $employee->user->update(['is_active' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee terminated successfully'
        ]);
    }

    public function team(Employee $employee)
    {
        $team = $this->employeeService->getTeamMembers($employee);

        return view('admin.employees.team', compact('employee', 'team'));
    }

    public function reportingChain(Employee $employee)
    {
        $chain = $this->employeeService->getReportingHierarchy($employee);

        return view('admin.employees.reporting-chain', compact('employee', 'chain'));
    }


    public function searchAutocomplete(Request $request)
    {
        $search = $request->get('q');

        $employees = Employee::where(function ($query) use ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('employee_code', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->where('status', 'active')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'employee_code', 'email']);

        return response()->json($employees);
    }


    public function getList(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        $employees = $query->select('id', 'first_name', 'last_name', 'employee_code')
            ->get()
            ->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->full_name . ' (' . $emp->employee_code . ')'
                ];
            });

        return response()->json($employees);
    }

    public function getByDepartment(Request $request, $departmentId)
    {
        $employees = Employee::where('department_id', $departmentId)
            ->where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get()
            ->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'text' => $emp->full_name . ' (' . $emp->employee_code . ')'
                ];
            });

        return response()->json($employees);
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        return view('admin.employees.import');
    }

    /**
     * Import employees from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        // Excel import logic here
        // You'll need to install maatwebsite/excel package

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employees imported successfully');
    }


    public function export(Request $request)
    {
        // Excel export logic here
        // You'll need to install maatwebsite/excel package

        // return Excel::download(new EmployeesExport($request->all()), 'employees.xlsx');
    }


    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:employees,id'
        ]);

        Employee::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employees deleted successfully'
        ]);
    }
    public function delete($id)
    {


        Employee::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }

    public function getDesignationsByDepartment($departmentId)
    {
        $designations = Designation::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get(['id', 'title']);

        return response()->json($designations);
    }

    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:employees,id',
            'status' => 'required|in:active,inactive,suspended,terminated'
        ]);

        Employee::whereIn('id', $request->ids)->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee status updated successfully'
        ]);
    }
}
