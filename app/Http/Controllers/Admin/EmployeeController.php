<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\RestfulTrait;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Admin\EmployeeAddEditRequest;
use App\Models\Admin\Admin;
use App\Traits\ImageUpload;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    use RestfulTrait, ImageUpload;

    public function __construct()
    {
        $this->view = 'Admin.';
    }


    /**
     * Display a listing of the Employee.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $employee = Admin::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $employee = $employee->trashed();
        } else {
            $employee = $employee->notTrashed();
        }
        if (isset($request->role)) {
            $employee = $employee->where('role',$request->role);
        }
        $employee = $employee->orderBy('id', 'DESC')->get();
        return DataTables::of($employee)
            ->addColumn('srno', function ($employee) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $employee->id .  '"/> ' . $employee->rownum;
            })
            ->editColumn('empId', function ($employee) {
                return $employee->emp_ref;
            })
            ->editColumn('name', function ($employee) {
                return '<img src="' . $employee->image_url .  '" style="width:30px;height:30px;border-radius: 67%;"/> '.$employee->name;
            })
            
            ->editColumn('login_location', function ($employee) {
                return   '<a target="_blank" title="click on to view location on map ('.$employee->latitude.'-'.$employee->longitude .')" href="https://maps.google.com/?q='.$employee->latitude.','.$employee->longitude.'"><svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg></a>';
                
            })
            ->editColumn('status', function ($employee) {
                if ($employee->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })
            ->addColumn('actions', function ($employee) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $employee->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $employee->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'status', 'actions', 'name','login_location'])
            ->make(true);
    }

    public function manageEmployee(Request $request)
    {
        $roleName = $request->role;
        $roleId = $request->roleId;
        $All = Admin::notTrashed()->count();
        $Trashed = Admin::trashed()->count();
        return view(
            $this->view . 'employee',
            compact(['All', 'Trashed','roleName','roleId'])
        );
    }


    public function empIdGen()
    {
        //sequences
        $datenow = date("Y-m-d");
        $sequencedToday = Admin::whereDate('created_at', $datenow)->count();
        $code = 'MT';
        $ymd = date('ymd');
        $squence = $sequencedToday + 1;
        $squence = str_pad($squence, 4, 0, STR_PAD_LEFT);
        return  $code . $ymd . $squence;
    }
    /**
     * Store a newly created employee data.
     *
     * @param  App\Http\Requests\Admin\EmployeeAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EmployeeAddEditRequest $request)
    {
        $data     =   $request->all();
        if (
            $request->hasFile('image')
            && $request->file('image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . 'PK_';
            $data['image'] = $this->imageUpload(
                $request->file('image'),
                '/uploads/employee/',
                $fileName
            );
        }



        $employee    =   new Admin();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $data['password'] =  bcrypt($request->password);
        $data['emp_ref'] =  $this->empIdGen();
        $data['role'] =  $request->role;
        $employee->fill($data);
        $employee->save();

        $role = Role::find($request->role); 
        $employee->assignRole($role->name);

        return $this->createdResponse(
            $employee,
            trans('employee.created')
        );
    }
    /**
     * get selected Employee data.
     *
     * @param  App\Http\Requests\Admin\EmployeeAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $employee    =   Admin::find($id);
        if (!$employee instanceof Admin) {
            return $employee;
        }
        return $this->showSuccessResponse($employee);
    }

    /**
     * Update a employee
     *
     * @param App\Http\Requests\Admin\EmployeeAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EmployeeAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $employee    =   $this->checkResource($id);
        if (!$employee instanceof Admin) {
            return $employee;
        }
        $data['updated_by'] = Auth::guard('admin')->user()->id;

        if (
            $request->hasFile('image')
            && $request->file('image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . 'PK_';
            $data['image'] = $this->imageUpload(
                $request->file('image'),
                '/uploads/employee/',
                $fileName
            );
        }

        $employee->fill($data);
        $employee->save();
        return $this->createdResponse(
            $employee,
            trans('employee.updated')
        );
    }

    /**
     * Enable or Disable the Employee
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableEmployee(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('employee.active') : trans('employee.in-active');
        $employee = Admin::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $employee,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the Employee
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyEmployee(
        Request $request
    ) {
        $data = $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('employee.delete') : trans('employee.restore');
        $employee = Admin::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $employee,
            $statusMsg
        );
    }

    /**
     * Check for the Employee exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\Admin
     */
    public function checkResource($id)
    {
        $package = Admin::find($id);
        if (!$package) {
            return $this->notFoundResponse(
                trans('employee.not_found')
            );
        }
        return $package;
    }
}
