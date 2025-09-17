<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserEditRequest;
use Illuminate\Http\Request;

use App\Models\User;
use App\Traits\ApiResponser;
use App\Traits\ImageUpload;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @category	Controller
 * @package		Auth Controller
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */



class UserController extends Controller
{
    use ApiResponser,ImageUpload;

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get user profile",
     *     tags={"User"},
     * security={{"bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function show(Request $request)
    {
        return $this->success([$request->user()],trans('user.success'),Response::HTTP_OK);
    }


    /**
     * @OA\Put(
     *     path="/api/user",
     *     summary="Update user profile",
     *     tags={"User"},
     * security={{"bearer": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="profile", type="string", format="binary", example="file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User profile updated successfully",
     *         @OA\JsonContent(
     *            
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Update user details
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if (
            $request->hasFile('profile')
            && $request->file('profile')->isValid()
        ) {
            $fileName = date("Ymd_His") . rand(0000, 9999) . '_' . $user->name ?? 'profile';
            $user->profile = $this->imageUpload(
                $request->file('profile'),
                env('USER_PROFILE_PATH'),
                $fileName
            );
        }
        $user->save();

        return $this->success([$user],trans('user.profile'),Response::HTTP_OK);
    }

/**
     * @OA\Get(
     *     path="/api/user/referrals",
     *     summary="Get user referrals list",
     *     tags={"User"},
     * security={{"bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User referrals retrieved successfully",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

     public function referrals(Request $request)
     {
        $user = Auth::user();
        $referrals = $user->referrals; // Get all users
        return $this->success([$referrals],trans('user.success'),Response::HTTP_OK);
     }
}

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="profile", type="string", example="http://example.com/profile.jpg"),
 * )
 */
