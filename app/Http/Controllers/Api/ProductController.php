<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Category;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\ProductReview;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @category    Controller
 * @package     Product Controller
 * @author      Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */

/**
 * @OA\Tag(name="Ecommerce", description="Operations related to Ecommerce")
 */

class ProductController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get a list of products with filters, sorting, search, and pagination",
     *     tags={"Ecommerce"},
     *     @OA\Parameter(name="category_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string", enum={"price_asc", "price_desc", "newest", "oldest", "top_rated"})),
     *     @OA\Parameter(name="min_price", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="max_price", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Response(response=200, description="Product list", @OA\JsonContent()),
     *     @OA\Response(response=404, description="No products found"),
     *     security={{"bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        // print_r($request->category_id);
        $query = Product::active()->notTrashed();

        // 🔍 Search by name, botanical_name, or nick_name
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('botanical_name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('nick_name', 'LIKE', "%{$searchTerm}%");
            });
        }

        // // 🔖 Filter by category
        // if ($request->has('category_id') && !empty($request->category_id) && $request->category_id != 0) {
        //     $query->where('category_id', $request->category_id);
        // }
        // 🔖 Filter by category (but handle category_id = 0 as special case)
        if ($request->has('category_id')) {
            if ($request->category_id != 0) {
                $query->where('category_id', $request->category_id);
            } else {
                // Fetch random or recommended products when category_id is 0
                $query = $query->inRandomOrder();
            }
        }

        // 💲 Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 📊 Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'top_rated':
                    $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                    break;
            }
        } else {
            $query->orderBy('id', 'desc'); // Default: Newest first
        }

        // 📥 Load More (Pagination)
        $perPage = 10;
        $products = $query->with(['category', 'reviews'])->paginate($perPage);

        // // Optionally, you can transform the products to add the wishlist_tag field
        // $products->getCollection()->transform(function($product) {
        //     // Add the 'wishlist_tag' field dynamically
        //     $product->wishlist_tag = $product->wishlist_tag;
        //     return $product;
        // });

        // Optionally, you can transform the products to add the wishlist_tag field
        $products->getCollection()->transform(function($product) {
            // Assuming you have a function to get the current authenticated user
            $user = auth()->user();
            
            // Add the 'wishlist_tag' field dynamically (true or false)
            $product->wishlist_tag = $product->wishLists()->where('user_id', $user->id)->exists();

            return $product;
        });

        // 📦 API Response
        $resArray = [
            'success' => true,
            'message' => 'Product list fetched successfully',
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'total_pages' => $products->lastPage(),
                'total_products' => $products->total(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ]
        ];
        
        return $this->success($resArray, trans('product.success'), Response::HTTP_OK);
    }

     
    /**
     * @OA\Get(
     *     path="/api/product/{id}",
     *     summary="Get details of a single product",
     *     tags={"Ecommerce"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Product not found"),
     *     security={{"bearer": {}}} 
     * )
     */
    public function show($id)
    {
        try {
            $product = Product::with([
                'category' => function ($query) {
                    $query->select('id', 'name');
                },
                // 'reviews' => function ($query) {
                //     $query->select('id', 'product_id', 'user_id', 'rating', 'review', 'created_at');
                // }
            ])
            // ->withAvg('reviews', 'rating') // Get average rating
            ->findOrFail($id);

            return $this->success($product, 'Product details fetched successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('product.not_found'), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/product/{id}/ratings",
     *     summary="Get ratings and review statistics for a product",
     *     tags={"Ecommerce"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rating statistics",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="Product not found"),
     *     security={{"bearer": {}}}
     * )
     */
    public function getRatings($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Fetch all reviews for the product
            $ratings = ProductReview::where('product_id', $id)->get();

            // Calculate ratings breakdown
            $ratingStats = [
                'average_rating' => round($ratings->avg('rating'), 1),
                'total_ratings' => $ratings->count(),
                'ratings_count' => [
                    '1_star' => $ratings->where('rating', 1)->count(),
                    '2_star' => $ratings->where('rating', 2)->count(),
                    '3_star' => $ratings->where('rating', 3)->count(),
                    '4_star' => $ratings->where('rating', 4)->count(),
                    '5_star' => $ratings->where('rating', 5)->count(),
                ],
            ];

            return $this->success($ratingStats, 'Rating statistics fetched successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('product.not_found'), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/product/{id}/reviews",
     *     summary="Get paginated list of reviews for a product",
     *     tags={"Ecommerce"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of reviews",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="Product not found"),
     *     security={{"bearer": {}}}
     * )
     */
    public function getReviews($id, Request $request)
    {
        try {
            $product = Product::findOrFail($id);

            $perPage = 10; // Number of reviews per page
            $reviews = ProductReview::with([
                'user:id,name,profile'
            ])->where('product_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $response = [
                'success' => true,
                'message' => 'Reviews fetched successfully',
                'data' => $reviews->items(), // Only return reviews
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'total_pages' => $reviews->lastPage(),
                    'total_reviews' => $reviews->total(),
                    'next_page_url' => $reviews->nextPageUrl(),
                    'prev_page_url' => $reviews->previousPageUrl(),
                ]
            ];

            return $this->success($response, trans('review.success'), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('product.not_found'), Response::HTTP_NOT_FOUND);
        }
    }




    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get a list of active categories",
     *     tags={"Ecommerce"},
     *     @OA\Response(response=200, description="Categories list", @OA\JsonContent()),
     *     @OA\Response(response=404, description="No categories found"),
     *     security={{"bearer": {}}}
     * )
     */
    public function getCategories()
    {
        $categories = Category::active()->notTrashed()
                            ->orderBy('name', 'asc')
                            ->get();

        if ($categories->isEmpty()) {
            return $this->error(trans('category.not_found'), Response::HTTP_NOT_FOUND);
        }

        return $this->success($categories, 'Categories fetched successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/product/{id}/reviews",
     *     summary="Write a review for a product",
     *     tags={"Ecommerce"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"rating", "review"},
     *                 @OA\Property(property="rating", type="integer", example=5),
     *                 @OA\Property(property="review", type="string", example="Great product!")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review added successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Product not found or user hasn't purchased this product"),
     *     security={{"bearer": {}}}
     * )
     */
    public function writeReview(Request $request, $id)
    {
         // Validate the input
         $request->validate([
            'rating' => 'required|integer|between:1,5', // Rating must be between 1 and 5
            'review' => 'required|string|min:10', // Review must be at least 10 characters
        ]);

        // Get the authenticated user
        $userId = auth()->id();

        // Check if the product exists
        $product = Product::find($id);

        if (!$product) {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
        }

        // Check if the user has purchased the product
        $hasPurchased = Order::where('user_id', $userId)
                             ->whereHas('orderLogs', function($query) use ($id) {
                                 $query->where('tree_id', $id);
                             })
                             ->exists();

        if (!$hasPurchased) {
            return $this->error('You must have purchased this product to leave a review', Response::HTTP_FORBIDDEN);
        }

        // Check if the user has already reviewed this product
        $existingReview = ProductReview::where('product_id', $id)
                                      ->where('user_id', $userId)
                                      ->first();

        if ($existingReview) {
            return $this->error('You have already reviewed this product', Response::HTTP_CONFLICT);
        }

        // Create the review
        $review = new ProductReview();
        $review->product_id = $id;
        $review->user_id = $userId;
        $review->rating = $request->rating;
        $review->review = $request->review;

        // Save the review
        $review->save();

        // Return a success response
        return $this->success($review, 'Review added successfully', Response::HTTP_CREATED);
    
    }
     /**
     * @OA\Put(
     *     path="/api/product/{id}/reviews",
     *     summary="Update a review for a product",
     *     tags={"Ecommerce"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"rating", "review"},
     *                 @OA\Property(property="rating", type="integer", example=5),
     *                 @OA\Property(property="review", type="string", example="Great product!")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review updated successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="No review found to update"),
     *     security={{"bearer": {}}}
     * )
     */
    public function updateReview(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'rating' => 'required|integer|between:1,5', // Rating must be between 1 and 5
            'review' => 'required|string|min:3', // Review must be at least 3 characters
        ]);

        // Get the authenticated user
        $userId = auth()->id();

        // Check if the product exists
        $product = Product::find($id);

        if (!$product) {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
        }

        // Check if the user has already reviewed this product
        $existingReview = ProductReview::where('product_id', $id)
                                      ->where('user_id', $userId)
                                      ->first();

        if (!$existingReview) {
            return $this->error('No review found to update', Response::HTTP_NOT_FOUND);
        }

        // Update the review
        $existingReview->rating = $request->rating;
        $existingReview->review = $request->review;

        // Save the updated review
        $existingReview->save();

        // Return a success response
        return $this->success($existingReview, 'Review updated successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/product/{id}/can-review",
     *     summary="Check if the user can write a review for a product",
     *     tags={"Ecommerce"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User is eligible to write a review",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=403, description="User has not purchased the product"),
     *     security={{"bearer": {}}}
     * )
     */
    public function canReview($id)
    {
        // Check if the product exists
        $product = Product::find($id);

        if (!$product) {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
        }

        // Check if the user has purchased the product
        $hasPurchased = Order::where('user_id', auth()->id())
                             ->whereHas('orderLogs', function($query) use ($id) {
                                 $query->where('tree_id', $id);
                             })
                             ->exists();

        if (!$hasPurchased) {
            return $this->error('You must have purchased this product to leave a review', Response::HTTP_FORBIDDEN);
        }

        // Check if the user has already reviewed the product
        $existingReview = ProductReview::where('product_id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if ($existingReview) {
        return $this->success([
            'can_review' => false,
            'reviewed' => true,
            'review' => $existingReview
        ], 'You have already reviewed this product', Response::HTTP_OK);
    }

    return $this->success([
        'can_review' => true,
        'reviewed' => false,
        'review' => null
    ], 'User is eligible to write a review', Response::HTTP_OK);
        // return $this->success(null, 'User is eligible to write a review', Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/coupons",
     *     summary="Get a list of active coupons",
     *     tags={"Ecommerce"},
     *     @OA\Response(response=200, description="coupons list", @OA\JsonContent()),
     *     @OA\Response(response=404, description="No coupons found"),
     *     security={{"bearer": {}}}
     * )
     */
    public function getCoupons()
    {
        $coupons = Coupon::active()->notTrashed()
                            ->get();

        if ($coupons->isEmpty()) {
            return $this->error(trans('coupons.not_found'), Response::HTTP_NOT_FOUND);
        }

        return $this->success($coupons, 'coupons fetched successfully', Response::HTTP_OK);
    }


    /**
     * @OA\Get(
     *     path="/api/products/recommendation",
     *     summary="Get list of categories each with 5 latest products",
     *     tags={"Ecommerce"},
     *     @OA\Response(
     *         response=200,
     *         description="Categories with products fetched successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="category_name", type="string", example="Indoor Plants"),
     *                 @OA\Property(property="slug", type="string", example="indoor-plants"),
     *                 @OA\Property(property="icon", type="string", example="icon.png"),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=101),
     *                         @OA\Property(property="name", type="string", example="Snake Plant"),
     *                         @OA\Property(property="price", type="number", format="float", example=299.99),
     *                         @OA\Property(property="discount_price", type="number", format="float", example=249.99),
     *                         @OA\Property(property="main_image", type="string", example="snake-plant.jpg"),
     *                         @OA\Property(property="category_id", type="integer", example=1)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No categories or products found"
     *     ),
     *      security={{"bearer": {}}}
     * )
     */

     public function getCategoriesWiseProducts()
     {
         // Fetch active & non-trashed categories
         $categories = Category::active()->notTrashed()
             ->orderBy('name')
             ->get(['id', 'name', 'slug', 'icon']);
     
         // Map each category and fetch 5 products for each
         $result = $categories->map(function ($category) {
             $products = Product::active()->notTrashed()
                 ->where('category_id', $category->id)
                 ->orderBy('id', 'desc')
                 ->take(5)
                 ->get();
     
             $user = auth()->user();
     
             $products->transform(function ($product) use ($user) {
                 $product->wishlist_tag = $user ? $product->wishLists()->where('user_id', $user->id)->exists() : false;
                 return $product;
             });
     
             return [
                 'category_id' => $category->id,
                 'category_name' => $category->name,
                 'slug' => $category->slug,
                 'icon' => $category->icon,
                 'products' => $products
             ];
         });
     
         return $this->success($result, 'Recommended products fetched successfully', Response::HTTP_OK);
     }
     


}
