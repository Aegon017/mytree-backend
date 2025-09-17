<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Tree;
use App\Models\Product;
use App\Models\WishList;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="WishList",
 *     description="API endpoints for managing wish lists"
 * )
 */
class WishListController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Post(
     *     path="/api/wishlist/add/{tree}",
     *     tags={"WishList"},
     *      security={{"bearer": {}}},
     *     summary="Add a tree to the wish list",
     *     @OA\Parameter(
     *         name="tree",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the tree to add"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tree added to wish list",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Tree added to wish list!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function add($id)
    {
        $tree    =   $this->checkResourceProduct($id);
        if (!$tree instanceof Product) {
            return $tree;
        }

        $wishList = new WishList();
        $wishList->user_id = auth()->id();
        $wishList->tree_id = $tree->id;
        $wishList->product_type = 2;
        $wishList->save();
        return $this->success($tree, 'Tree added to wish list!', Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/wishlist/remove/{tree}",
     *     tags={"WishList"},
     *      security={{"bearer": {}}},
     *     summary="Remove a tree from the wish list",
     *     @OA\Parameter(
     *         name="tree",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the tree to remove"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tree removed from wish list",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Tree removed from wish list!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function remove($id)
    {
        $tree    =   $this->checkResourceProduct($id);
        if (!$tree instanceof Product) {
            return $tree;
        }
        $wishList = WishList::where('user_id', auth()->id())
            ->where('tree_id', $tree->id)
            ->first();
        
        if ($wishList) {
            $wishList->delete();
        }
        return $this->success($tree, 'Tree removed from wish list!', Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/wishlist",
     *     tags={"WishList"},
     *      security={{"bearer": {}}},
     *     summary="Get all trees in the wish list",
     *     @OA\Response(
     *         response=200,
     *         description="List of trees in the wish list",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="Success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        $wishLists = WishList::with('product')->where('user_id', auth()->id())->get();
        return $this->success($wishLists, trans('user.success'), Response::HTTP_OK);
    }

    public function checkResource($id)
    {
        $tree = Tree::find($id);
        if (!$tree) {
            return $this->error(trans('tree.not_found'),Response::HTTP_BAD_REQUEST);
        }
        return $tree;
    }

    public function checkResourceProduct($id)
    {
        $tree = Product::find($id);
        if (!$tree) {
            return $this->error(trans('tree.not_found'),Response::HTTP_BAD_REQUEST);
        }
        return $tree;
    }
}
