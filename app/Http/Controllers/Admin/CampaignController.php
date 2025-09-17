<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CampaignAddEditRequest;
use App\Http\Requests\Admin\TreeAddEditRequest;
use App\Models\Admin\Area;
use App\Models\Admin\City;
use App\Models\Admin\State;
use App\Models\Admin\Tree;
use App\Models\Admin\TreeImage;
use App\Models\Campaign;
use App\Models\Donation;
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
 * @tree		Campaign
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */
class CampaignController extends Controller
{
    use RestfulTrait, ImageUpload;

    public function __construct()
    {
        $this->view = 'Admin.';
    }

    // public function getCities($stateId)
    // {
    //     $area = City::getState($stateId)->active()->notTrashed()->get();
    //     return $this->showSuccessResponse($area);
    // }
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
        $trees = Campaign::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $trees = $trees->trashed();
        } else {
            $trees = $trees->notTrashed();
        }
        $trees = $trees->with(['city', 'state'])->orderBy('id', 'DESC')->get();
        // dd($trees);
        return DataTables::of($trees)
            ->addColumn('srno', function ($trees) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $trees->id .  '"/> ' . $trees->rownum;
            })
            ->addColumn('sku', function ($trees) {
                return '<a style="color:green;" href="' . route('campaign.show',$trees->id) . '" target="_blank">
                <u>'.$trees->sku.'</u></a>';
            })
            ->editColumn('name', function ($trees) {
                return '<img src="' . $trees->main_image_url .  '" style="width:30px;height:30px;border-radius: 67%;"/> '.$trees->name;
            })
            ->editColumn('state', function ($trees) {
                return $trees->state->name;
            })
            ->editColumn('city', function ($trees) {
                return $trees->city->name;
            })
            ->editColumn('area', function ($trees) {
                return $trees->area;
            })
            ->editColumn('goal_amount', function ($trees) {
                return $trees->goal_amount;
            })
            ->editColumn('raised_amount', function ($trees) {
                return $trees->raised_amount;
            })
            ->editColumn('expiration_date', function ($trees) {
                return $trees->expiration_date;
            })
            ->editColumn('status', function ($trees) {
                if ($trees->status == 1) {
                    return   '<span class="fw-bold text-success">' . "Active" . '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . "In-Active" . '</span>';
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

    public function manageTree()
    {
        $states = State::active()->notTrashed()->get();
        $All = Campaign::notTrashed()->count();
        $Trashed = Campaign::trashed()->count();
        return view(
            $this->view . 'treeFund',
            compact(['All', 'Trashed', 'states'])
        );
    }

    /**
     * Store a newly created Tree data.
     *
     * @param  App\Http\Requests\Admin\CampaignAddEditRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CampaignAddEditRequest $request)
    {
        $data     =   $request->all();
        $slug     =  Str::slug($request->name);
        $slugCount = Campaign::slug($slug)->count();
        $data['slug'] = ($slugCount == 0 ? $slug : $slug . '_' . $slugCount + 1);
        $data['sku'] = $this->skuGen();
        $data['expiration_date'] = date('Y-m-d', strtotime($request->expiration_date));
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

        $tree    =   new Campaign();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $tree->fill($data);
        $tree->save();

        // $this->moreImgUpload($request, $data['slug'], $tree);
        return $this->createdResponse(
            $tree,
            trans('tree.created')
        );
    }

    public function skuGen()
    {
        //sequences
        $datenow = date("Y-m-d");
        $sequencedToday = Campaign::whereDate('created_at', $datenow)->count();
        $code = 'SKUGFTR';
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
        $tree    =   Campaign::with(['city', 'state'])->find($id);
        if (!$tree instanceof Campaign) {
            return $tree;
        }
        return $this->showSuccessResponse($tree);
    }

    public function show($id)
    {
        $campaign = Campaign::with(['city', 'state'])->find($id);

            if (!$campaign) {
                return $this->error('Campaign not found', 404);
            }

            // Calculate raised amount
            $raisedAmount = $campaign->donations->sum('amount');

            // Calculate pending amount (target_amount - raised_amount)
            $pendingAmount = max(0, $campaign->goal_amount - $raisedAmount);

            // Get the list of donors
            $donors = Donation::where('campaign_id', $campaign->id)
                ->select('donor_name', 'amount')
                ->get();
$target_amount=$campaign->target_amount;

        return view(
            $this->view . 'treeFundDetails',
            compact(['campaign','donors','target_amount','pendingAmount','raisedAmount'])
        );
    }

    /**
     * Update a tree
     *
     * @param App\Http\Requests\Admin\CampaignAddEditRequest $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CampaignAddEditRequest $request, $id)
    {
        $data       =   $request->all();
        $tree    =   $this->checkResource($id);
        if (!$tree instanceof Campaign) {
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

        // $this->moreImgUpload($request, $tree->slug, $tree);

        return $this->createdResponse(
            $tree,
            trans('tree.updated')
        );
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
        $tree = Campaign::whereIn('id', explode(",", $update_ids))->update(['status' => $status]);

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
        $tree = Campaign::whereIn('id', explode(",", $update_ids))->update(['trash' => $status]);
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
        $tree = Campaign::find($id);
        if (!$tree) {
            return $this->notFoundResponse(
                trans('tree.not_found')
            );
        }
        return $tree;
    }
}
