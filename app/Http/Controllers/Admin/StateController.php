<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\RestfulTrait;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Admin\State;
use App\Http\Requests\Admin\StateAddEditRequest;
use App\Models\Admin\City;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StateController extends Controller
{
    use RestfulTrait;

    public function __construct()
    {
        $this->view = 'Admin.';
    }
    

    /**
     * Display a listing of the State.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $state = State::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $state = $state->trashed();
        } else {
            $state = $state->notTrashed();
        }

        $state = $state->orderBy('id', 'DESC')->get();

        return DataTables::of($state)
            ->addColumn('srno', function ($state) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $state->id .  '"/> ' . $state->rownum;
            })

            ->editColumn('name', function ($state) {
                return $state->name;
            })
            ->editColumn('status', function ($state) {
                if ($state->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })

            ->addColumn('actions', function ($state) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $state->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $state->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'main_img', 'status', 'actions'])
            ->make(true);
    }

    public function manageState()
    {
        $All = State::notTrashed()->count();
        $Trashed = State::trashed()->count();
        return view(
            $this->view . 'state',
            compact(['All', 'Trashed'])
        );
    }

    /**
     * Store a newly created State data.
     *
     * @param  App\Http\Requests\Admin\StateAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StateAddEditRequest $request)
    {
        $data     =   $request->all();
        $slug     =  Str::slug($request->name);

        $slugCount = State::slug($slug)->count();
        $data['slug'] = ($slugCount == 0 ? $slug : $slug . '_' . $slugCount + 1);
        
        $state    =   new State();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $state->fill($data);
        $state->save();


        return $this->createdResponse(
            $state,
            trans('state.created')
        );
    }
    /**
     * get selected State data.
     *
     * @param  App\Http\Requests\Admin\StateAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $state    =   State::find($id);
        if (!$state instanceof State) {
            return $state;
        }
        return $this->showSuccessResponse($state);
    }

    /**
     * Update a state
     *
     * @param App\Http\Requests\Admin\StateAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StateAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $state    =   $this->checkResource($id);
        if (!$state instanceof State) {
            return $state;
        }
        $data['updated_by'] = Auth::guard('admin')->user()->id;

        $state->fill($data);
        $state->save();
        return $this->createdResponse(
            $state,
            trans('state.updated')
        );
    }

    /**
     * Enable or Disable the State
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableState(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('state.active') : trans('state.in-active');
        $state = State::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $state,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the State
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyState(
        Request $request
    ) {
        $data = $request->all();
        //dd($data);
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('state.delete') : trans('state.restore');
        $state = State::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $state,
            $statusMsg
        );
    }

    /**
     * Check for the State exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\state
     */
    public function checkResource($id)
    {
        $state = State::find($id);
        if (!$state) {
            return $this->notFoundResponse(
                trans('state.not_found')
            );
        }
        return $state;
    }
}
