<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\RestfulTrait;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Admin\Area;
use App\Http\Requests\Admin\LocationsAddEditRequest;
use App\Models\Admin\City;
use App\Models\Admin\State;
use App\Models\TreeLocation;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationsController extends Controller
{
    use RestfulTrait;

    public function __construct()
    {
        $this->view = 'Admin.';
    }

    /**
     * Display a listing of the Locations.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $location = TreeLocation::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $location = $location->trashed();
        } else {
            $location = $location->notTrashed();
        }

        $location = $location->with(['area','city','state'])->orderBy('id', 'DESC')->get();
        return DataTables::of($location)
            ->addColumn('srno', function ($location) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $location->id .  '"/> ' . $location->rownum;
            })

            ->editColumn('area', function ($location) {
                return $location->area->name ?? '';
            })
            ->editColumn('city', function ($location) {
                return $location->city->name ?? '';
            })
            ->editColumn('state', function ($location) {
                return $location->state->name ?? '';
            })
            ->editColumn('status', function ($location) {
                if ($location->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })

            ->addColumn('actions', function ($location) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $location->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $location->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'main_img', 'status', 'actions'])
            ->make(true);
    }

    public function manageLocations()
    {
        $states = State::active()->notTrashed()->get();
        $cities = City::active()->notTrashed()->get();
        $All = TreeLocation::notTrashed()->count();
        $Trashed = TreeLocation::trashed()->count();
        return view(
            $this->view . 'treeLocation',
            compact(['All', 'Trashed','states', 'cities'])
        );
    }

    /**
     * Store a newly created Locations data.
     *
     * @param  App\Http\Requests\Admin\LocationsAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LocationsAddEditRequest $request)
    {
        $data     =   $request->all();
        $location    =   new TreeLocation();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $location->fill($data);
        $location->save();


        return $this->createdResponse(
            $location,
            trans('locations.created')
        );
    }
    /**
     * get selected Locations data.
     *
     * @param  App\Http\Requests\Admin\LocationsAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $location    =   TreeLocation::with(['city'])->find($id);
        if (!$location instanceof TreeLocation) {
            return $location;
        }
        return $this->showSuccessResponse($location);
    }

    /**
     * Update a Location
     *
     * @param App\Http\Requests\Admin\LocationsAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(LocationsAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $location    =   $this->checkResource($id);
        if (!$location instanceof TreeLocation) {
            return $location;
        }
        $data['updated_by'] = Auth::guard('admin')->user()->id;

        $location->fill($data);
        $location->save();
        return $this->createdResponse(
            $location,
            trans('locations.updated')
        );
    }

    /**
     * Enable or Disable the Locations
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableLocation(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('locations.active') : trans('locations.in-active');
        $location = TreeLocation::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $location,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the Locations
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyLocation(
        Request $request
    ) {
        $data = $request->all();
        //dd($data);
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('locations.delete') : trans('locations.restore');
        $location = TreeLocation::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $location,
            $statusMsg
        );
    }

    /**
     * Check for the Locations exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\Location
     */
    public function checkResource($id)
    {
        $location = TreeLocation::find($id);
        if (!$location) {
            return $this->notFoundResponse(
                trans('locations.not_found')
            );
        }
        return $location;
    }
}
