<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\RestfulTrait;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Admin\CouponAddEditRequest;
use App\Models\Coupon;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class couponController extends Controller
{
    use RestfulTrait;
    public function __construct()
    {
        $this->view = 'Admin.';
    }


    /**
     * Display a listing of the coupon.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $coupon = Coupon::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $coupon = $coupon->trashed();
        } else {
            $coupon = $coupon->notTrashed();
        }
        $coupon = $coupon->orderBy('id', 'DESC')->get();
        return DataTables::of($coupon)
            ->addColumn('srno', function ($coupon) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $coupon->id .  '"/> ' . $coupon->rownum;
            })
            ->editColumn('name', function ($coupon) {
                return $coupon->name;
            })
            
            ->editColumn('status', function ($coupon) {
                if ($coupon->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })
            ->addColumn('actions', function ($coupon) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $coupon->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $coupon->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'status', 'actions', 'name'])
            ->make(true);
    }

    public function manageCoupon(Request $request)
    {
        $All = Coupon::notTrashed()->count();
        $Trashed = Coupon::trashed()->count();
        return view(
            $this->view . 'coupon',
            compact(['All', 'Trashed'])
        );
    }
    /**
     * Store a newly created coupon data.
     *
     * @param  App\Http\Requests\Admin\CouponAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CouponAddEditRequest $request)
    {
        $data     =   $request->all();
        $coupon    =   new Coupon();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $coupon->fill($data);
        $coupon->save();
        return $this->createdResponse(
            $coupon,
            trans('coupon.created')
        );
    }
    /**
     * get selected coupon data.
     *
     * @param  App\Http\Requests\Admin\CouponAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $coupon    =   Coupon::find($id);
        if (!$coupon instanceof Coupon) {
            return $coupon;
        }
        return $this->showSuccessResponse($coupon);
    }

    /**
     * Update a coupon
     *
     * @param App\Http\Requests\CouponAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CouponAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $coupon    =   $this->checkResource($id);
        if (!$coupon instanceof Coupon) {
            return $coupon;
        }
        $data['updated_by'] = Auth::guard('admin')->user()->id;


        $coupon->fill($data);
        $coupon->save();
        return $this->createdResponse(
            $coupon,
            trans('coupon.updated')
        );
    }

    /**
     * Enable or Disable the coupon
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableCoupon(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('coupon.active') : trans('coupon.in-active');
        $coupon = Coupon::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $coupon,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the coupon
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyCoupon(
        Request $request
    ) {
        $data = $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('coupon.delete') : trans('coupon.restore');
        $coupon = Coupon::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $coupon,
            $statusMsg
        );
    }

    /**
     * Check for the coupon exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\coupon
     */
    public function checkResource($id)
    {
        $package = Coupon::find($id);
        if (!$package) {
            return $this->notFoundResponse(
                trans('coupon.not_found')
            );
        }
        return $package;
    }
}
