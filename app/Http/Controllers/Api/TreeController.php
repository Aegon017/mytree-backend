<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Tree;
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
 * @OA\Tag(name="Tree", description="Operations related to Tree")
 */

class TreeController extends Controller
{
    use ApiResponser,ImageUpload;     

     /**
     * @OA\Get(
     *     path="/api/trees",
     *     summary="Get a list of trees",
     *     tags={"Trees"},
     *     security={{"bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of trees",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found"),
     * )
     */

    public function index(Request $request)
    {
        $trees = Tree::sponsor()->active()->notTrashed()->with(['city', 'state','price'])->orderBy('id', 'DESC')->get();
        return $this->success($trees,trans('user.success'),Response::HTTP_OK);
    }

     /**
     * @OA\Get(
     *     path="/api/adopt-trees",
     *     summary="Get a list of adopt trees",
     *     tags={"Trees"},
     *     security={{"bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of adopt trees",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found"),
     * )
     */

     public function adoptTrees(Request $request)
     {
         $trees = Tree::adopt()->active()->notTrashed()->with(['city', 'state','price'])->orderBy('id', 'DESC')->get();
         return $this->success($trees,trans('user.success'),Response::HTTP_OK);
     }

     /**
     * @OA\Get(
     *     path="/api/tree/{id}",
     *     summary="Get a tree by ID",
     *     tags={"Trees"},
     *     security={{"bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tree details",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="Tree not found"),
     * )
     */

    public function show($id)
    {
        try {
            $tree = Tree::with(['images', 'city', 'state','price'])->findOrFail($id);
            return $this->success($tree,trans('user.success'),Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('tree.not_found'),Response::HTTP_BAD_REQUEST);

        }
    }
}

/**
 * @OA\Schema(
 *     schema="Tree",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Oak"),
 *     @OA\Property(property="city", ref="#/components/schemas/City"),
 *     @OA\Property(property="state", ref="#/components/schemas/State"),
 *     @OA\Property(property="images", type="array",
 *         @OA\Items(ref="#/components/schemas/Image")
 *     ),
 * )
 */

/**
 * @OA\Schema(
 *     schema="Image",
 *     type="object",
 *     @OA\Property(property="url", type="string", example="http://example.com/image.jpg"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="City",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="New York"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="State",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="New York"),
 * )
 */
