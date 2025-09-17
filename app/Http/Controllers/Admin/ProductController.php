<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductAddEditRequest;
use App\Models\Admin\Area;
use App\Models\Admin\Category;
use App\Models\Admin\City;
use App\Models\Admin\State;
use App\Models\Product;
use App\Models\ProductImage;
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
 * @product		product
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */
class ProductController extends Controller
{
    use RestfulTrait, ImageUpload;

    public function __construct()
    {
        $this->view = 'Admin.';
    }

    
    /**
     * Display a listing of the product.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        // DB::statement(DB::raw('set @rownum=0'));
        // DB::raw('@rownum  := @rownum  + 1 AS rownum')
        $products = Product::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $products = $products->trashed();
        } else {
            $products = $products->notTrashed();
        }
        $products = $products->with(['category'])->orderBy('id', 'DESC')->get();

        return DataTables::of($products)
            ->addColumn('srno', function ($products) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $products->id .  '"/> ' . $products->rownum;
            })
            ->addColumn('sku', function ($products) {
                return '<a style="color:green;" href="' . route('product.show',$products->id) . '" target="_blank">
                <u>'.$products->sku.'</u></a>';
            })
            ->editColumn('name', function ($products) {
                return '<img src="' . $products->main_image_url .  '" style="width:30px;height:30px;border-radius: 67%;"/> '.$products->name;
            })
            ->editColumn('category', function ($products) {
                return $products->category->name ?? '';
            })
            ->editColumn('price', function ($products) {
                return $products->price;
            })
            ->editColumn('quantity', function ($products) {
                return $products->quantity;
            })
            ->editColumn('status', function ($products) {
                if ($products->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>' .'&nbsp;<span class="badge bg-success">'.($products->adopted_status==1 ? 'Adopted':'').'</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>'.'&nbsp;<span class="badge bg-success">'.($products->adopted_status==1 ? 'Adopted':'').'</span>';
                }
            })
            ->addColumn('actions', function ($products) {
                return '<a href="javascript:void(0)"</a><div class="action-drp-dwn action-btns"><button id="reg-user_ ' . $products->id . '"
                                         class="btn btn-success mb-3" onclick="editItem(' . $products->id . ')">
                                         <i class="la la-ellipsis-v"></i>Edit
                                        </button>
                                            </div>';
            })
            ->rawColumns(['srno', 'status', 'actions','name','sku'])
            ->make(true);
    }

    public function manageProduct(Request $request)
    {
        $categories = Category::active()->notTrashed()->get();
        $All = Product::notTrashed()->count();
        $Trashed = Product::trashed()->count();
        return view(
            $this->view . 'product',
            compact(['All', 'Trashed', 'categories'])
        );
    }

    /**
     * Store a newly created product data.
     *
     * @param  App\Http\Requests\Admin\ProductAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductAddEditRequest $request)
    {
        $data     =   $request->all();
        $slug     =  Str::slug($request->name);
        $slugCount = Product::slug($slug)->count();
        $data['slug'] = ($slugCount == 0 ? $slug : $slug . '_' . $slugCount + 1);
        $data['sku'] = $this->skuGen();
        if (
            $request->hasFile('main_image')
            && $request->file('main_image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $data['slug'];
            $data['main_image'] = $this->imageUpload(
                $request->file('main_image'),
                env('PRODUCT_UPLOAD_PATH'),
                $fileName
            );
        }

        $product    =   new Product();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $product->fill($data);
        $product->save();
        // $this->addPrice($request,$product->id);
        $this->moreImgUpload($request, $data['slug'], $product);
        return $this->createdResponse(
            $product,
            trans('product.created')
        );
    }

    public function skuGen()
    {
        //sequences
        $datenow = date("Y-m-d");
        $sequencedToday = Product::whereDate('created_at', $datenow)->count();
        $code = 'SKUMTPRD';
        $ymd = date('ymd');
        $squence = $sequencedToday + 1;
        $squence = str_pad($squence, 4, 0, STR_PAD_LEFT);
        return  $code . $ymd . $squence;
    }
    
    /**
     * get selected product data.
     *
     * @param  App\Http\Requests\Admin\ProductAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $product    =   Product::with(['images', 'category'])->find($id);
        if (!$product instanceof Product) {
            return $product;
        }
        return $this->showSuccessResponse($product);
    }

    public function show($id)
    {
        $product = Product::with(['images', 'category'])->findOrFail($id);
        return view(
            $this->view . 'productDetails',
            compact(['product'])
        );
    }


    /**
     * Update a product
     *
     * @param App\Http\Requests\Admin\ProductAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $product    =   $this->checkResource($id);
        if (!$product instanceof Product) {
            return $product;
        }
        if (
            $request->hasFile('main_image')
            && $request->file('main_image')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $product->slug;
            $data['main_image'] = $this->imageUpload(
                $request->file('main_image'),
                env('PRODUCT_UPLOAD_PATH'),
                $fileName
            );
        }

        $data['updated_by'] = Auth::guard('admin')->user()->id;
        $product->fill($data);
        $product->save();
        // ProductPrice::where('product_id',$product->id)->delete();
        // $this->addPrice($request,$product->id);
        $this->moreImgUpload($request, $product->slug, $product);

        return $this->createdResponse(
            $product,
            trans('product.updated')
        );
    }
    public function moreImgUpload($request, $slug, $product)
    {
        if (
            $request->hasFile('more_imgs')
        ) {
            if ($files = $request->file('more_imgs')) {
                foreach ($files as $file) {
                    $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $slug;
                    $uploadedImg = $this->imageUpload(
                        $file,
                        env('PRODUCT_UPLOAD_PATH'),
                        $fileName
                    );
                    $imgData[] = [
                        'image' =>  $uploadedImg,
                        'product_id' => $product->id,
                    ];
                }
            }

            ProductImage::insert($imgData);
        }
    }
    /**
     * Enable or Disable the product
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableproduct(
        Request $request
    ) {
        $data =  $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('product.active') : trans('product.in-active');
        $product = Product::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

        return $this->showSuccessResponse(
            $product,
            $statusMsg
        );
    }


    /**
     * Enable or Disable the product
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyProduct(
        Request $request
    ) {
        $data = $request->all();
        $update_ids = $data['updatelist'];
        $status     = $data['activity'];
        $statusMsg  = $status ? trans('product.delete') : trans('product.restore');
        $product = Product::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
        return $this->showSuccessResponse(
            $product,
            $statusMsg
        );
    }



    /**
     * Check for the product exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\product
     */
    public function checkResource($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->notFoundResponse(
                trans('product.not_found')
            );
        }
        return $product;
    }
    public function deleteImage(Request $request)
    {
        $image = ProductImage::find($request->image_id);
        if (!$image) {
            return response()->json([
                'code' => 404,
                'message' => 'Image not found.',
            ]);
        }

        // Optional: delete from disk
        if ($image->image && file_exists(public_path(env('PRODUCT_UPLOAD_PATH') . '/' . $image->image))) {
            @unlink(public_path(env('PRODUCT_UPLOAD_PATH') . '/' . $image->image));
        }

        $image->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Image deleted successfully.',
        ]);
    }

}
