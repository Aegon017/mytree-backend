<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogAddEditRequest;
use App\Models\Blog;
use App\Traits\ImageUpload;
use App\Traits\RestfulTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @category	Controller
 * @package		Blog
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */
class BlogController extends Controller
{
    use RestfulTrait, ImageUpload;

    public function __construct()
    {
        $this->view = 'Admin.';
    }

    /**
     * Display a listing of the Blog.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $blogs = Blog::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $blogs = $blogs->trashed();
        } else {
            $blogs = $blogs->notTrashed();
        }
        $blogs = $blogs->orderBy('id', 'DESC')->get();
        return DataTables::of($blogs)
            ->addColumn('srno', function ($blogs) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $blogs->id .  '"/> ' . $blogs->rownum;
            })
            ->editColumn('title', function ($blogs) {
                return '<img src="' . $blogs->main_image_url .  '" style="width:30px;height:30px;border-radius: 67%;"/> 
                <a style="color:green;" href="' . route('blogs.show',$blogs->id) . '" target="_blank">
                <u>'.$blogs->title.'</u></a>
                ';
            })
            
            ->editColumn('status', function ($blogs) {
                if ($blogs->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
                }
            })
            ->addColumn('actions', function ($blogs) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $blogs->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $blogs->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'status', 'actions','title'])
            ->make(true);
    }

    public function manageBlog()
    {
        $All = Blog::notTrashed()->count();
        $Trashed = Blog::trashed()->count();
        return view(
            $this->view . 'blogs',
            compact(['All', 'Trashed'])
        );
    }

    /**
     * Store a newly created Blog data.
     *
     * @param  App\Http\Requests\Admin\BlogAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BlogAddEditRequest $request)
    {
        $data     =   $request->all();
        $slug     =  Str::slug($request->name);
        $slugCount = Blog::slug($slug)->count();
        $data['slug'] = ($slugCount == 0 ? $slug : $slug . '_' . $slugCount + 1);
        if (
            $request->hasFile('main_image')
            && $request->file('main_image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $data['slug'];
            $data['main_image'] = $this->imageUpload(
                $request->file('main_image'),
                env('BLOG_UPLOAD_PATH'),
                $fileName
            );
        }

        $blog    =   new Blog();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $blog->fill($data);
        $blog->save();

        return $this->createdResponse(
            $blog,
            trans('blog.created')
        );
    }

    /**
     * get selected  blog.
     *
     * @param 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $blog    =   Blog::find($id);
        if (!$blog instanceof Blog) {
            return $blog;
        }
        return $this->showSuccessResponse($blog);
    }

    public function show($id)
    {
        $blog = Blog::find($id);
        return view(
            $this->view . 'blogDetails',
            compact(['blog'])
        );
    }

    /**
     * Update a blog
     *
     * @param App\Http\Requests\Admin\BlogAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BlogAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $blog    =   $this->checkResource($id);
        if (!$blog instanceof Blog) {
            return $blog;
        }
        if (
            $request->hasFile('main_image')
            && $request->file('main_image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $blog->slug;
            $data['main_image'] = $this->imageUpload(
                $request->file('main_image'),
                env('BLOG_UPLOAD_PATH'),
                $fileName
            );
        }

        $data['updated_by'] = Auth::guard('admin')->user()->id;
        $blog->fill($data);
        $blog->save();

        return $this->createdResponse(
            $blog,
            trans('blog.updated')
        );
    }

   
    /**
     * Enable or Disable the blog
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableBlog(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('blog.active') : trans('blog.in-active');
        $blog = Blog::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $blog,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the blog
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyBlog(
        Request $request
    ) {
        $data = $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('blog.delete') : trans('blog.restore');
        $blog = Blog::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $blog,
            $statusMsg
        );
    }



    /**
     * Check for the blog exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\Blog
     */
    public function checkResource($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return $this->notFoundResponse(
                trans('blog.not_found')
            );
        }
        return $blog;
    }
}
