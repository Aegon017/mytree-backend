<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\RestfulTrait;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Admin\CityAddEditRequest;
use App\Models\Admin\City;
use App\Models\Admin\State;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CityController extends Controller
{
    use RestfulTrait;

    public function __construct()
    {
        $this->view = 'Admin.';
    }
    
    public function getCities($stateId)
    {
        $area = City::getState($stateId)->active()->notTrashed()->get();
        return $this->showSuccessResponse($area);
    }

    /**
     * Display a listing of the City.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $city = City::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $city = $city->trashed();
        } else {
            $city = $city->notTrashed();
        }

        $city = $city->with(['state'])->orderBy('id', 'DESC')->get();

        return DataTables::of($city)
            ->addColumn('srno', function ($city) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $city->id .  '"/> ' . $city->rownum;
            })

            ->editColumn('name', function ($city) {
                return $city->name;
            })
            ->editColumn('city', function ($city) {
                return $city->state->name;
            })
            ->editColumn('status', function ($city) {
                if ($city->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })

            ->addColumn('actions', function ($city) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $city->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $city->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'main_img', 'status', 'actions'])
            ->make(true);
    }

    public function manageCity()
    {
        $states = State::active()->notTrashed()->get();
        $All = City::notTrashed()->count();
        $Trashed = City::trashed()->count();
        return view(
            $this->view . 'city',
            compact(['All', 'Trashed', 'states'])
        );
    }

    /**
     * Store a newly created City data.
     *
     * @param  App\Http\Requests\Admin\CityAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CityAddEditRequest $request)
    {
        $data     =   $request->all();
        $slug     =  Str::slug($request->name);

        $slugCount = City::slug($slug)->count();
        $data['slug'] = ($slugCount == 0 ? $slug : $slug . '_' . $slugCount + 1);
        
        $city    =   new City();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $city->fill($data);
        $city->save();


        return $this->createdResponse(
            $city,
            trans('city.created')
        );
    }
    /**
     * get selected City data.
     *
     * @param  App\Http\Requests\Admin\CityAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $city    =   City::with(['state'])->find($id);
        if (!$city instanceof City) {
            return $city;
        }
        return $this->showSuccessResponse($city);
    }

    /**
     * Update a city
     *
     * @param App\Http\Requests\Admin\CityAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CityAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $city    =   $this->checkResource($id);
        if (!$city instanceof City) {
            return $city;
        }
        $data['updated_by'] = Auth::guard('admin')->user()->id;

        $city->fill($data);
        $city->save();
        return $this->createdResponse(
            $city,
            trans('city.updated')
        );
    }

    /**
     * Enable or Disable the City
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableCity(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('city.active') : trans('city.in-active');
        $city = City::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $city,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the City
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyCity(
        Request $request
    ) {
        $data = $request->all();
        //dd($data);
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('city.delete') : trans('city.restore');
        $city = City::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $city,
            $statusMsg
        );
    }

    /**
     * Check for the City exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\city
     */
    public function checkResource($id)
    {
        $city = City::find($id);
        if (!$city) {
            return $this->notFoundResponse(
                trans('city.not_found')
            );
        }
        return $city;
    }
}
