<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Slider;
use Illuminate\Http\Request;


use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @category	Controller
 * @package		Blog Controller
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */

/**
 * @OA\Tag(name="Blog", description="Operations related to Blog")
 */

class BlogController extends Controller
{
    use ApiResponser;     

     /**
     * @OA\Get(
     *     path="/api/blogs",
     *     summary="Get a list of blogs",
     *     tags={"Blogs"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of blogs",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found"),
     * )
     */

    public function index(Request $request)
    {
        $blogs = Blog::active()->notTrashed()->orderBy('id', 'DESC')->get();
        return $this->success($blogs,trans('user.success'),Response::HTTP_OK);
    }

     /**
     * @OA\Get(
     *     path="/api/blog/{id}",
     *     summary="Get a blog by ID",
     *     tags={"Blogs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="blog details",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="Blog not found"),
     * )
     */

    public function show($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return $this->success($blog,trans('user.success'),Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('blog.not_found'),Response::HTTP_BAD_REQUEST);

        }
    }

     /**
     * @OA\Get(
     *     path="/api/sliders",
     *     summary="Get a list of sliders",
     *     tags={"Slider"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of sliders",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found"),
     * )
     */

     public function getSliders(Request $request)
     {
         $blogs = Slider::active()->notTrashed()->orderBy('id', 'DESC')->get();
         return $this->success($blogs,trans('user.success'),Response::HTTP_OK);
     }
}
