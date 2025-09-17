<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Tree;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;


use App\Models\User;
use App\Traits\ApiResponser;
use App\Traits\ImageUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @category	Controller
 * @package		Tree Controller
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */

/**
 * @OA\Tag(name="Feed Tree", description="Operations related to Feed Tree")
 */

class FeedTreeController extends Controller
{
    use ApiResponser,ImageUpload;     

     /**
     * @OA\Get(
     *     path="/api/feed-trees",
     *     summary="Get a list of feed trees",
     *     tags={"Feed Trees"},
     *     security={{"bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of feed trees",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found"),
     * )
     */

    // public function index(Request $request)
    // {
    //     $trees = Campaign::active()->notTrashed()->with(['city', 'state'])->orderBy('id', 'DESC')->get();
    //     return $this->success($trees,trans('user.success'),Response::HTTP_OK);
    // }

    public function index(Request $request)
    {
        $trees = Campaign::active()
            ->notTrashed()
            ->with(['city', 'state'])
            ->where(function ($query) {
                $query
                //->whereColumn('raised_amount', '>=', 'goal_amount') // Goal amount reached
                    ->orWhere('expiration_date', '>=', now()); // Not expired
            })
            ->orderBy('id', 'DESC')
            ->get();

        return $this->success($trees, trans('user.success'), Response::HTTP_OK);
    }
     /**
     * @OA\Get(
     *     path="/api/feed-tree/{id}",
     *     summary="Get a tree by ID",
     *     tags={"Feed Trees"},
     *     security={{"bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the campaign",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Campaign details",
     *         @OA\JsonContent(
     *             @OA\Property(property="campaign_id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="raised_amount", type="number", format="float"),
     *             @OA\Property(property="pending_amount", type="number", format="float"),
     *             @OA\Property(property="target_amount", type="number", format="float"),
     *             @OA\Property(property="donors", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="donor_name", type="string"),
     *                 @OA\Property(property="amount", type="number", format="float")
     *             )),
     *         )
     *     ),
     *     @OA\Response(response=404, description="Campaign not found"),
     * )
     */

    public function show($id)
    {
        try {
            // $tree = Campaign::with(['city', 'state'])->findOrFail($id);
            // return $this->success($tree,trans('user.success'),Response::HTTP_OK);
             // Fetch the campaign
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

            // Prepare the response data
            $responseData = [
                'campaign_id' => $campaign->id,
                'title' => $campaign->name,
                'campaign_details' => $campaign,
                'raised_amount' => $raisedAmount,
                'pending_amount' => $pendingAmount,
                'target_amount' => $campaign->target_amount,
                'donors' => $donors
            ];

            return $this->success($responseData, 'Campaign details fetched successfully', 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('tree.not_found'),Response::HTTP_BAD_REQUEST);
        }
    }
}
