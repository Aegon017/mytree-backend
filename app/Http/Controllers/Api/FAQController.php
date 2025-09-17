<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;

/**
 * @category	Controller
 * @package		FAQ Controller
 * @OA\Tag(name="FAQ", description="Operations related to FAQs")
 */
class FAQController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     path="/api/faqs",
     *     summary="Get a list of all FAQs",
     *     tags={"FAQ"},
     *     @OA\Response(
     *         response=200,
     *         description="List of FAQs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="question", type="string"),
     *                     @OA\Property(property="answer", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="No FAQs found"),
     * )
     */
    public function index()
    {
        // Fetch all FAQs from the database
        $faqs = FAQ::all();

        // Check if FAQs are found and return the response
        if ($faqs->isEmpty()) {
            return $this->error('No FAQs found.', Response::HTTP_NOT_FOUND);
        }

        // Return the list of FAQs
        return $this->success($faqs, 'FAQs fetched successfully.', Response::HTTP_OK);
    }
}
