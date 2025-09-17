<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\RestfulTrait;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Admin\Area;
use App\Http\Requests\Admin\AreasAddEditRequest;
use App\Models\Admin\City;
use App\Models\Admin\State;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AreasController extends Controller
{
    use RestfulTrait;

    public function __construct()
    {
        $this->view = 'Admin.';
    }
    public function getAreas($cityId)
    {
        $area = Area::active()->getCity($cityId)->notTrashed()->get();
        return $this->showSuccessResponse($area);
    }

    /**
     * Display a listing of the areas.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $area = Area::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $area = $area->trashed();
        } else {
            $area = $area->notTrashed();
        }

        $area = $area->with(['city','city.state'])->orderBy('id', 'DESC')->get();

        return DataTables::of($area)
            ->addColumn('srno', function ($area) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $area->id .  '"/> ' . $area->rownum;
            })

            ->editColumn('name', function ($area) {
                return $area->name;
            })
            ->editColumn('city', function ($area) {
                return $area->city->name;
            })
            ->editColumn('state', function ($area) {
                return $area->city->state->name;
            })
            ->editColumn('status', function ($area) {
                if ($area->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })

            ->addColumn('actions', function ($area) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $area->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $area->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'main_img', 'status', 'actions'])
            ->make(true);
    }

    public function manageAreas()
    {
        $states = State::active()->notTrashed()->get();
        $cities = City::active()->notTrashed()->get();
        $All = Area::notTrashed()->count();
        $Trashed = Area::trashed()->count();
        return view(
            $this->view . 'area',
            compact(['All', 'Trashed','states', 'cities'])
        );
    }

    /**
     * Store a newly created areas data.
     *
     * @param  App\Http\Requests\Admin\AreasAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AreasAddEditRequest $request)
    {
        $data     =   $request->all();
        $slug     =  Str::slug($request->name);

        $slugCount = Area::slug($slug)->count();
        $data['slug'] = ($slugCount == 0 ? $slug : $slug . '_' . $slugCount + 1);
        
        $area    =   new Area();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $area->fill($data);
        $area->save();


        return $this->createdResponse(
            $area,
            trans('area.created')
        );
    }
    /**
     * get selected areas data.
     *
     * @param  App\Http\Requests\Admin\AreasAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $area    =   Area::with(['city'])->find($id);
        if (!$area instanceof area) {
            return $area;
        }
        return $this->showSuccessResponse($area);
    }

    /**
     * Update a area
     *
     * @param App\Http\Requests\Admin\AreasAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AreasAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $area    =   $this->checkResource($id);
        if (!$area instanceof Area) {
            return $area;
        }
        $data['updated_by'] = Auth::guard('admin')->user()->id;

        $area->fill($data);
        $area->save();
        return $this->createdResponse(
            $area,
            trans('area.updated')
        );
    }

    /**
     * Enable or Disable the areas
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableAreas(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('area.active') : trans('area.in-active');
        $area = Area::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $area,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the Areas
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAreas(
        Request $request
    ) {
        $data = $request->all();
        //dd($data);
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('area.delete') : trans('area.restore');
        $area = Area::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $area,
            $statusMsg
        );
    }

    /**
     * Check for the areas exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\Area
     */
    public function checkResource($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return $this->notFoundResponse(
                trans('area.not_found')
            );
        }
        return $area;
    }
}
