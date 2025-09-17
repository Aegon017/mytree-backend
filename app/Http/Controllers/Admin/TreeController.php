<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TreeAddEditRequest;
use App\Models\Admin\Area;
use App\Models\Admin\City;
use App\Models\Admin\State;
use App\Models\Admin\Tree;
use App\Models\Admin\TreeImage;
use App\Models\TreePrice;
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
 * @tree		Tree
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */
class TreeController extends Controller
{
    use RestfulTrait, ImageUpload;

    public function __construct()
    {
        $this->view = 'Admin.';
    }

    
    /**
     * Display a listing of the Tree.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $trees = Tree::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $trees = $trees->trashed();
        } else {
            $trees = $trees->notTrashed();
        }
        // if (isset($request->type)) {
        //     $trees = $trees->where('type',$request->type)->where('adopted_status',0);
        // }
        if (isset($request->treeId)) {
            $trees = $trees->where('id',$request->treeId);
        }
        if (isset($request->adoptedStatus)) {
            $trees = $trees->where('adopted_status',$request->adoptedStatus);
        }else{
            $trees = $trees->where('type',$request->type)->where('adopted_status',0);
        }
        
        $trees = $trees->with(['area','city', 'state'])->orderBy('id', 'DESC')->get();
        // dd($trees);
        return DataTables::of($trees)
            ->addColumn('srno', function ($trees) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $trees->id .  '"/> ' . $trees->rownum;
            })
            ->addColumn('sku', function ($trees) {
                return '<a style="color:green;" href="' . route('tree.show',$trees->id) . '" target="_blank">
                <u>'.$trees->sku.'</u></a>';
            })
            ->editColumn('name', function ($trees) {
                return '<img src="' . $trees->main_image_url .  '" style="width:30px;height:30px;border-radius: 67%;"/> '.$trees->name;
            })
            ->editColumn('state', function ($trees) {
                return $trees->state->name ?? '';
            })
            ->editColumn('city', function ($trees) {
                return $trees->city->name ?? '';
            })
            ->editColumn('area', function ($trees) {
                return $trees->area->name ?? '';
            })
            ->editColumn('quantity', function ($trees) {
                return $trees->quantity;
            })
            ->editColumn('status', function ($trees) {
                if ($trees->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>' .'&nbsp;<span class="badge bg-success">'.($trees->adopted_status==1 ? 'Adopted':'').'</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>'.'&nbsp;<span class="badge bg-success">'.($trees->adopted_status==1 ? 'Adopted':'').'</span>';
                }
            })
            ->addColumn('actions', function ($trees) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $trees->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $trees->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'status', 'actions','name','sku'])
            ->make(true);
    }

    public function manageTree(Request $request)
    {
        $typeName = $request->type;
        $typeId = $request->typeId;
        $treeId = $request->treeId;
        $adoptedStatus = $request->adoptedStatus;
        
        $states = State::active()->notTrashed()->get();
        $All = Tree::notTrashed()->count();
        $Trashed = Tree::trashed()->count();
        return view(
            $this->view . 'tree',
            compact(['All', 'Trashed', 'states','typeName','typeId','treeId','adoptedStatus'])
        );
    }

    /**
     * Store a newly created Tree data.
     *
     * @param  App\Http\Requests\Admin\TreeAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TreeAddEditRequest $request)
    {
        $data     =   $request->all();
        $slug     =  Str::slug($request->name);
        $slugCount = Tree::slug($slug)->count();
        $data['slug'] = ($slugCount == 0 ? $slug : $slug . '_' . $slugCount + 1);
        $data['sku'] = $this->skuGen();
        if (
            $request->hasFile('main_image')
            && $request->file('main_image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $data['slug'];
            $data['main_image'] = $this->imageUpload(
                $request->file('main_image'),
                env('TREE_UPLOAD_PATH'),
                $fileName
            );
        }

        $tree    =   new Tree();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $tree->fill($data);
        $tree->save();
        $this->addPrice($request,$tree->id);
        $this->moreImgUpload($request, $data['slug'], $tree);
        return $this->createdResponse(
            $tree,
            trans('tree.created')
        );
    }

    public function skuGen()
    {
        //sequences
        $datenow = date("Y-m-d");
        $sequencedToday = Tree::whereDate('created_at', $datenow)->count();
        $code = 'SKUTR';
        $ymd = date('ymd');
        $squence = $sequencedToday + 1;
        $squence = str_pad($squence, 4, 0, STR_PAD_LEFT);
        return  $code . $ymd . $squence;
    }
    
    /**
     * get selected Tree data.
     *
     * @param  App\Http\Requests\Admin\TreeAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $tree    =   Tree::with(['images', 'city', 'state','price'])->find($id);
        if (!$tree instanceof tree) {
            return $tree;
        }
        return $this->showSuccessResponse($tree);
    }

    public function show($id)
    {
        $tree = Tree::with(['images', 'city', 'state', 'price', 'userRelations','userRelations.user','userRelations.order'])->findOrFail($id);
        return view(
            $this->view . 'treeDetails',
            compact(['tree'])
        );
    }


    /**
     * Update a tree
     *
     * @param App\Http\Requests\Admin\TreeAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TreeAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $tree    =   $this->checkResource($id);
        if (!$tree instanceof Tree) {
            return $tree;
        }
        if (
            $request->hasFile('main_image')
            && $request->file('main_image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $tree->slug;
            $data['main_image'] = $this->imageUpload(
                $request->file('main_image'),
                env('TREE_UPLOAD_PATH'),
                $fileName
            );
        }

        $data['updated_by'] = Auth::guard('admin')->user()->id;
        $tree->fill($data);
        $tree->save();
        TreePrice::where('tree_id',$tree->id)->delete();
        $this->addPrice($request,$tree->id);
        $this->moreImgUpload($request, $tree->slug, $tree);

        return $this->createdResponse(
            $tree,
            trans('tree.updated')
        );
    }
 public function addPrice($request,$treeId){
     // Loop through the durations and prices arrays and store them
    $durations = $request->durations;
    $prices = $request->prices;
     foreach ($durations as $index => $duration) {
        // Create a new price entry
        $price = new TreePrice([
            'duration' => $duration,
            'price' => $prices[$index],
            'tree_id' => $treeId, 
        ]);

        // Save the price
        $price->save();
    }
 }
    public function moreImgUpload($request, $slug, $tree)
    {
        if (
            $request->hasFile('more_imgs')
        ) {
            if ($files = $request->file('more_imgs')) {
                foreach ($files as $file) {
                    $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $slug;
                    $uploadedImg = $this->imageUpload(
                        $file,
                        env('TREE_UPLOAD_PATH'),
                        $fileName
                    );
                    $imgData[] = [
                        'image' =>  $uploadedImg,
                        'tree_id' => $tree->id,
                    ];
                }
            }

            TreeImage::insert($imgData);
        }
    }
    /**
     * Enable or Disable the tree
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableTree(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('tree.active') : trans('tree.in-active');
        $tree = Tree::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $tree,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the tree
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyTree(
        Request $request
    ) {
        $data = $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('tree.delete') : trans('tree.restore');
        $tree = Tree::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $tree,
            $statusMsg
        );
    }



    /**
     * Check for the tree exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\tree
     */
    public function checkResource($id)
    {
        $tree = Tree::find($id);
        if (!$tree) {
            return $this->notFoundResponse(
                trans('tree.not_found')
            );
        }
        return $tree;
    }

    public function deleteImage(Request $request)
    {
        $image = TreeImage::find($request->image_id);
        if (!$image) {
            return response()->json([
                'code' => 404,
                'message' => 'Image not found.',
            ]);
        }

        // Optional: delete from disk
        if ($image->image && file_exists(public_path(env('TREE_UPLOAD_PATH') . '/' . $image->image))) {
            @unlink(public_path(env('TREE_UPLOAD_PATH') . '/' . $image->image));
        }

        $image->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Image deleted successfully.',
        ]);
    }
}
