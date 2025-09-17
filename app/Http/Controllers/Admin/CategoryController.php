<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\RestfulTrait;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Admin\CategoryAddEditRequest;
use App\Models\Admin\Category;
use App\Traits\ImageUpload;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use RestfulTrait, ImageUpload;

    public function __construct()
    {
        $this->view = 'Admin.';
    }


    /**
     * Display a listing of the category.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $category = Category::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $category = $category->trashed();
        } else {
            $category = $category->notTrashed();
        }
        $category = $category->orderBy('id', 'DESC')->get();
        return DataTables::of($category)
            ->addColumn('srno', function ($category) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $category->id .  '"/> ' . $category->rownum;
            })
            ->editColumn('name', function ($category) {
                return '<img src="' . $category->image_url .  '" style="width:30px;height:30px;border-radius: 67%;"/> '.$category->name;
            })
            
            ->editColumn('status', function ($category) {
                if ($category->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })
            ->addColumn('actions', function ($category) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $category->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $category->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'status', 'actions', 'name'])
            ->make(true);
    }

    public function manageCategory(Request $request)
    {
        $All = Category::notTrashed()->count();
        $Trashed = Category::trashed()->count();
        return view(
            $this->view . 'category',
            compact(['All', 'Trashed'])
        );
    }
    /**
     * Store a newly created category data.
     *
     * @param  App\Http\Requests\Admin\CategoryAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryAddEditRequest $request)
    {
        $data     =   $request->all();
        $slug     =  Str::slug($request->name);
        $slugCount = Category::slug($slug)->count();
        $data['slug'] = ($slugCount == 0 ? $slug : $slug . '_' . $slugCount + 1);

        if (
            $request->hasFile('image')
            && $request->file('image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . 'PK_';
            $data['icon'] = $this->imageUpload(
                $request->file('image'),
                '/uploads/category/',
                $fileName
            );
        }

        $category    =   new Category();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $category->fill($data);
        $category->save();
        return $this->createdResponse(
            $category,
            trans('category.created')
        );
    }
    /**
     * get selected category data.
     *
     * @param  App\Http\Requests\Admin\CategoryAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $category    =   Category::find($id);
        if (!$category instanceof Category) {
            return $category;
        }
        return $this->showSuccessResponse($category);
    }

    /**
     * Update a category
     *
     * @param App\Http\Requests\CategoryAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $category    =   $this->checkResource($id);
        if (!$category instanceof Category) {
            return $category;
        }
        $data['updated_by'] = Auth::guard('admin')->user()->id;

        if (
            $request->hasFile('image')
            && $request->file('image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . 'MT_';
            $data['icon'] = $this->imageUpload(
                $request->file('image'),
                '/uploads/category/',
                $fileName
            );
        }

        $category->fill($data);
        $category->save();
        return $this->createdResponse(
            $category,
            trans('category.updated')
        );
    }

    /**
     * Enable or Disable the category
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableCategory(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('category.active') : trans('category.in-active');
        $category = Category::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $category,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the category
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroycategory(
        Request $request
    ) {
        $data = $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('category.delete') : trans('category.restore');
        $category = Category::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $category,
            $statusMsg
        );
    }

    /**
     * Check for the category exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\Category
     */
    public function checkResource($id)
    {
        $package = Category::find($id);
        if (!$package) {
            return $this->notFoundResponse(
                trans('category.not_found')
            );
        }
        return $package;
    }
}
