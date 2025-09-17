<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SliderAddEditRequest;
use App\Traits\ImageUpload;
use App\Traits\RestfulTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Slider;


/**
 * @category	Controller
 * @package		Slider
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */
class SliderController extends Controller
{
    
    use RestfulTrait, ImageUpload;

    public function __construct()
    {
        $this->view = 'Admin.';
    }

    /**
     * Display a listing of the slider.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $sliders = Slider::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $sliders = $sliders->trashed();
        } else {
            $sliders = $sliders->notTrashed();
        }
        $sliders = $sliders->orderBy('id', 'DESC')->get();
        return DataTables::of($sliders)
            ->addColumn('srno', function ($sliders) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $sliders->id .  '"/> ' . $sliders->rownum;
            })
            ->editColumn('title', function ($sliders) {
                return '<img src="' . $sliders->main_image_url .  '" style="width:30px;height:30px;border-radius: 67%;"/> 
                <a style="color:green;" href="' . route('sliders.show',$sliders->id) . '" target="_blank">
                <u>'.$sliders->title.'</u></a>
                ';
            })
            
            ->editColumn('status', function ($sliders) {
                if ($sliders->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })
            ->addColumn('actions', function ($sliders) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $sliders->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $sliders->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'status', 'actions','title'])
            ->make(true);
    }

    public function manageSlider()
    {
        $All = Slider::notTrashed()->count();
        $Trashed = Slider::trashed()->count();
        return view(
            $this->view . 'sliders',
            compact(['All', 'Trashed'])
        );
    }

    /**
     * Store a newly created slider data.
     *
     * @param  App\Http\Requests\Admin\SliderAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SliderAddEditRequest $request)
    {
        $data     =   $request->all();
        if (
            $request->hasFile('main_image')
            && $request->file('main_image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999);
            $data['main_image'] = $this->imageUpload(
                $request->file('main_image'),
                env('SLIDER_UPLOAD_PATH'),
                $fileName
            );
        }

        $slider    =   new Slider();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $slider->fill($data);
        $slider->save();

        return $this->createdResponse(
            $slider,
            trans('slider.created')
        );
    }

    /**
     * get selected  slider.
     *
     * @param 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $slider    =   Slider::find($id);
        if (!$slider instanceof Slider) {
            return $slider;
        }
        return $this->showSuccessResponse($slider);
    }

    public function show($id)
    {
       
    }

    /**
     * Update a slider
     *
     * @param App\Http\Requests\Admin\SliderAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SliderAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $slider    =   $this->checkResource($id);
        if (!$slider instanceof Slider) {
            return $slider;
        }
        if (
            $request->hasFile('main_image')
            && $request->file('main_image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999);
            $data['main_image'] = $this->imageUpload(
                $request->file('main_image'),
                env('SLIDER_UPLOAD_PATH'),
                $fileName
            );
        }

        $data['updated_by'] = Auth::guard('admin')->user()->id;
        $slider->fill($data);
        $slider->save();

        return $this->createdResponse(
            $slider,
            trans('slider.updated')
        );
    }

   
    /**
     * Enable or Disable the slider
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableSlider(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('slider.active') : trans('slider.in-active');
        $slider = Slider::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $slider,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the slider
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroySlider(
        Request $request
    ) {
        $data = $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('slider.delete') : trans('slider.restore');
        $slider = Slider::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $slider,
            $statusMsg
        );
    }



    /**
     * Check for the slider exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\Slider
     */
    public function checkResource($id)
    {
        $slider = Slider::find($id);
        if (!$slider) {
            return $this->notFoundResponse(
                trans('slider.not_found')
            );
        }
        return $slider;
    }
}
