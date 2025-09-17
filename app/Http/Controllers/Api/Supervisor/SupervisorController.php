<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SupervisorLoginOtpRequest;
use App\Models\Admin\Admin;
use App\Models\Order;
use App\Models\TreePlantation;
use App\Models\Admin\Tree;
use App\Models\TreePlantationImage;
use App\Models\UserTreeRelation;
use App\Notifications\SendOtpNotification;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Traits\ImageUpload;



/**
 * @category	Controller
 * @package		Auth Controller
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */

 /**
 * @OA\Tag(name="Supervisor Auth", description="Operations related to Auth")
 */

class SupervisorController extends Controller
{
    use ApiResponser,ImageUpload;

     /**
      * @OA\Post(
      *     path="/api/supervisor/signin",
      *     summary="Supervisor Sign In",
      *     tags={"Supervisor"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"mobile"},
      *             @OA\Property(property="mobile", type="string", example="1234567890"),
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="OTP sent successfully",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="OTP sent"),
      *         )
      *     ),
      *     @OA\Response(response=422, description="Validation Error")
      * )
      */

     public function signIn(SupervisorLoginOtpRequest $request)
     {
         $user = Admin::supervisor()->active()->notTrashed()->where('mobile', $request->mobile)->first();
         if (!$user) {
             return $this->error(trans('user.not_found'),Response::HTTP_UNPROCESSABLE_ENTITY);
         }

         return $this->sendOtp($user);
     }


     private function sendOtp($user)
     {
         // $otp = 123456;
         $otp = rand(100000, 999999); // Generate a 6-digit OTP
         //rand(100000, 999999);
         //Str::random(6); // Generate a 6-digit OTP
         $user->otp = $otp;
         $user->otp_expires_at = now()->addMinutes(5);
         $user->save();
         $templateid = '1207169355519345595';
         $message = "Thank you for registering with SOWJANYA STUDIOS. Your one-time password for registering with us is {$otp}.";

         $user->notify(new SendOtpNotification($otp, $message, $templateid));
         // $user->notify(new SendOtpNotification($otp,$message,$templateid));
         // Notification::send($user, new SendOtpNotification($otp));
         return $this->success([],trans('user.otp_sent'),Response::HTTP_OK);
     }


     /**
      * @OA\Post(
      *     path="/api/supervisor/resend-otp",
      *     summary="Resend OTP",
      *     tags={"Supervisor"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"mobile"},
      *             @OA\Property(property="mobile", type="string", example="1234567890"),
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="OTP sent successfully",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="OTP sent"),
      *         )
      *     ),
      *     @OA\Response(response=422, description="Validation Error")
      * )
      */

     public function resendOtp(SupervisorLoginOtpRequest $request)
     {
         $user = Admin::supervisor()->active()->notTrashed()->where('mobile', $request->mobile)->first();
         if (!$user) {
             return $this->error(trans('user.not_found'),Response::HTTP_UNPROCESSABLE_ENTITY);
         }

         // Generate a new OTP
         return $this->sendOtp($user);
     }


 /**
      * @OA\Post(
      *     path="/api/supervisor/verify-otp",
      *     summary="Verify OTP",
      *     tags={"Supervisor"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"mobile", "otp"},
      *             @OA\Property(property="mobile", type="string", example="1234567890"),
      *             @OA\Property(property="otp", type="string", example="123456"),
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="OTP verified successfully",
      *         @OA\JsonContent(
      *             @OA\Property(property="token", type="string", example="your_token_here"),
      *             @OA\Property(property="message", type="string", example="Success"),
      *         )
      *     ),
      *     @OA\Response(response=400, description="Invalid OTP")
      * )
      */
     public function verifyOtp(Request $request)
     {
         $request->validate([
             'mobile' => 'required',
             'otp' => 'required|string',
         ]);

         $user = Admin::supervisor()->active()->notTrashed()->where('mobile', $request->mobile)->first();
         if (!$user || $user->otp !== $request->otp) {
         // if (!$user || $user->otp !== $request->otp || now()->isPast($user->otp_expires_at)) {
             return $this->error(trans('user.otp_invalid'),Response::HTTP_BAD_REQUEST);
         }

         // Clear OTP
         $user->otp = null;
         $user->otp_expires_at = null;
         $user->save();

         // Create a token
         $token = $user->createToken('authToken')->plainTextToken;
         return $this->success($token,trans('user.success'),Response::HTTP_OK);
     }

     /**
      * @OA\Post(
      *     path="/api/supervisor/logout",
      *     summary="Logout Supervisor and invalidate token",
      *     tags={"Supervisor"},
      *     @OA\Response(
      *         response=200,
      *         description="Supervisor logged out successfully",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Successfully logged out.")
      *         )
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthorized - Token missing or invalid",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Unauthorized")
      *         )
      *     ),
      *      security={{"bearer": {}}}
      * )
      */
     public function logout(Request $request)
     {
         // Revoke the user's current token
         $request->user()->currentAccessToken()->delete();

         return $this->success([],trans('user.logout'),Response::HTTP_OK);
     }


     /**
     * @OA\Get(
     *     path="/api/supervisor/orders",
     *     summary="Get a list of orders",
     *     tags={"Supervisor"},
     *     security={{"bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of orders",
     *         @OA\JsonContent(
     *
     *         )
     *     ),
     *     @OA\Response(response=404, description="Not found"),
     * )
     */

     public function orders(){
        $admin = auth()->user();
        $trees = UserTreeRelation::
        with(['order','adoptedTree',
        // 'adoptedTree:id,name' ,
        'adoptedTree.state:id,name',
        'adoptedTree.city:id,name',
         'adoptedTree.area:id,name','plantationDetails'
         ]) // Load only the needed relationships
        ->join('order_assignments', 'user_tree_relations.order_id', '=', 'order_assignments.order_id')
        ->join('order_logs', 'order_logs.order_id', '=', 'order_assignments.order_id')
        ->where('order_assignments.admin_id', $admin->id)
        ->get();
        return $this->success([$trees],trans('user.success'),Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/supervisor/tree-plantation/update",
     *     tags={"Supervisor"},
     *     summary="Update Tree Plantation Details",
     *     description="Update the tree plantation details including supervisor, location, description, etc.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"supervisor_id", "latitude", "longitude", "geoId"},
     *             @OA\Property(property="supervisor_id", type="integer", example="1"),
     *             @OA\Property(property="order_id", type="integer", example="1"),
     *             @OA\Property(property="tree_id", type="integer", example="1"),
     *             @OA\Property(property="latitude", type="number", format="float", example="12.9716"),
     *             @OA\Property(property="longitude", type="number", format="float", example="77.5946"),
     *             @OA\Property(property="geoId", type="string", example="IN-KA"),
     *             @OA\Property(property="description", type="string", example="Tree plantation in the park."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tree plantation details updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tree plantation updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data"
     *     ),
     *     security={{"bearer": {}}}
     * )
     */
    public function updateTreePlantation(Request $request)
    {
        $admin = auth()->user();
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            // 'supervisor_id' => 'required|integer|exists:admins,id',
            'order_id' => 'required|integer|exists:orders,id',
            'tree_id' => 'required|integer|exists:trees,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'geoId' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', Response::HTTP_BAD_REQUEST, $validator->errors());
        }

        // Check if a record exists with the given `id` or `order_id` or create a new one
        $treePlantation = TreePlantation::updateOrCreate(
            ['tree_id' => $request->tree_id], // search condition (you can change this to `order_id` or other fields)
            [
                'supervisor_id' => $admin->id,
                'order_id' => $request->order_id,
                'tree_id' => $request->tree_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'geoId' => $request->geoId,
                'description' => $request->description ?? '', // provide default value if null
            ]
        );

        $tree = Tree::find($request->tree_id);

        if ($tree) {
            $tree->update([
                'plantation_status' => 1,
            ]);
        }

        // Return success response with the updated or inserted data
        return $this->success([$treePlantation], 'Tree plantation details updated successfully.', Response::HTTP_OK);
    }


    /**
     * @OA\Get(
     *     path="/api/supervisor/tree-plantation/details/{tree_id}",
     *     tags={"Supervisor"},
     *     summary="Get Tree Plantation Details by Tree ID",
     *     description="Fetch details of a tree plantation by its Tree ID.",
     *     @OA\Parameter(
     *         name="tree_id",
     *         in="path",
     *         required=true,
     *         description="The Tree ID of the plantation to fetch.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tree plantation details fetched successfully"
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tree plantation not found"
     *     ),
     *     security={{"bearer": {}}}
     * )
     */
    public function getTreePlantationDetailsByTreeId($tree_id)
    {
        // Fetch tree plantation by tree_id
        $treePlantation = TreePlantation::with('images')->where('tree_id', $tree_id)->first();

        // Check if plantation with the provided tree_id exists
        if (!$treePlantation) {
            return $this->error('Tree plantation not found.', Response::HTTP_NOT_FOUND);
        }

        return $this->success([$treePlantation], 'Tree plantation details fetched successfully.', Response::HTTP_OK);
    }

     /**
     * @OA\Post(
     *     path="/api/supervisor/tree-plantation/upload-images/{tree_plantation_id}",
     *     tags={"Supervisor"},
     *     summary="Upload multiple images for a tree plantation",
     *     description="This API allows uploading multiple images for a specific tree plantation.",
     *     @OA\Parameter(
     *         name="tree_plantation_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the tree plantation for which images are being uploaded.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"images"},
     *             @OA\Property(property="images", type="array", items={
     *                 @OA\Items(type="string", format="binary")
     *             })
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Images uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Images uploaded successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function uploadImages(Request $request, $tree_plantation_id)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'images' => 'required|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
    ]);

    if ($validator->fails()) {
        return $this->error('Validation error.', Response::HTTP_BAD_REQUEST, $validator->errors());
    }

    // Check if the tree plantation exists
    $treePlantation = TreePlantation::find($tree_plantation_id);
    if (!$treePlantation) {
        return $this->error('Tree plantation not found.', Response::HTTP_NOT_FOUND);
    }

    $uploadedImages = [];

    // Ensure the images exist and are properly handled
    if ($request->hasFile('images')) {
        $files = $request->file('images');

        foreach ($files as $file) {
            if ($file->isValid()) {
                $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $file->getClientOriginalName();
                $uploadedImg = $this->imageUpload(
                    $file,
                    env('TREE_PLANTATION_PATH'),
                    $fileName
                );

                // Create a record for the uploaded image
                $treePlantationImage = new TreePlantationImage();
                $treePlantationImage->tree_plantation_id = $tree_plantation_id;
                $treePlantationImage->image = $uploadedImg; // Store the file path
                $treePlantationImage->save();

                // Collect the uploaded image details
                $uploadedImages[] = $treePlantationImage;
            }
        }
    }

    // Return success response with uploaded images
    return $this->success($uploadedImages, 'Images uploaded successfully.', Response::HTTP_OK);
}

    public function uploadImages55(Request $request, $tree_plantation_id)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error.', Response::HTTP_BAD_REQUEST, $validator->errors());
        }

        // Get the authenticated user (optional)
        // $user = Auth::user();

        // Check if the tree plantation exists
        $treePlantation = TreePlantation::find($tree_plantation_id);
        if (!$treePlantation) {
            return $this->error('Tree plantation not found.', Response::HTTP_NOT_FOUND);
        }

        $uploadedImages = [];
        // dd($request->hasFile('images'));

        if (
            $request->hasFile('images')
        ) {
            if ($files = $request->file('images')) {
                dd($files);
                foreach ($files as $file) {
                    $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $file->getClientOriginalName();
                    $uploadedImg = $this->imageUpload(
                        $file,
                        env('TREE_PLANTATION_PATH'),
                        $fileName
                    );
                    // Create a record for the uploaded image
                    $treePlantationImage = new TreePlantationImage();
                    $treePlantationImage->tree_plantation_id = $tree_plantation_id;
                    $treePlantationImage->image = $uploadedImg;//$filePath; // Store the file path
                    $treePlantationImage->save();

                    // Collect the uploaded image details
                    $uploadedImages[] = $treePlantationImage;
                    // $imgData[] = [
                    //     'image' =>  $uploadedImg,
                    //     'tree_id' => $tree->id,
                    // ];
                }
            }

            // TreeImage::insert($imgData);
        }

        // Loop through each image file and upload it
        // foreach ($request->file('images') as $image) {
        //     dd(444);
        //     // Generate a unique filename
        //     $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $image->getClientOriginalName();

        //     // Save the image to storage
        //     // $filePath = $image->storeAs('public/tree_plantation_images', $fileName);


        //     // $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $user->name ?? 'profile';
        //     $imageUpload = $this->imageUpload(
        //         $image,
        //         env('TREE_PLANTATION_PATH'),
        //         $fileName
        //     );
        //     // Create a record for the uploaded image
        //     $treePlantationImage = new TreePlantationImage();
        //     $treePlantationImage->tree_plantation_id = $tree_plantation_id;
        //     $treePlantationImage->image = $imageUpload;//$filePath; // Store the file path
        //     $treePlantationImage->save();

        //     // Collect the uploaded image details
        //     $uploadedImages[] = $treePlantationImage;
        // }

        return $this->success($uploadedImages, 'Images uploaded successfully.', Response::HTTP_OK);
    }


}

/**
 * @OA\Schema(
 *     schema="TreePlantation",
 *     type="object",
 *     required={"id", "supervisor_id", "order_id", "latitude", "longitude", "geoId"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="supervisor_id", type="integer", example=2),
 *     @OA\Property(property="order_id", type="integer", example=5),
 *     @OA\Property(property="latitude", type="number", format="float", example=12.1234),
 *     @OA\Property(property="longitude", type="number", format="float", example=34.5678),
 *     @OA\Property(property="geoId", type="string", example="XYZ123"),
 *     @OA\Property(property="description", type="string", example="This is a description."),
 *     @OA\Property(property="images", type="array", @OA\Items(ref="#/components/schemas/TreePlantationImage"))
 * )
 */
/**
 * @OA\Schema(
 *     schema="TreePlantationImage",
 *     type="object",
 *     required={"id", "tree_plantation_id", "image"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="tree_plantation_id", type="integer", example=10),
 *     @OA\Property(property="image", type="string", example="path/to/image.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-01T12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-01T12:00:00")
 * )
 */
