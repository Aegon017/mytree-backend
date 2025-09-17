<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CMSPage;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Tag(name="CMS", description="Operations related to CMS Pages")
 */
class CMSPageController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     path="/api/about-app",
     *     summary="Get About App content",
     *     tags={"CMS"},
     *     @OA\Response(
     *         response=200,
     *         description="CMS content details",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="CMS page not found"),
     * )
     */
    public function aboutApp()
    {
        return $this->getPageContent('about-app');
    }

    /**
     * @OA\Get(
     *     path="/api/privacy-policy",
     *     summary="Get Privacy Policy content",
     *     tags={"CMS"},
     *     @OA\Response(
     *         response=200,
     *         description="CMS content details",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="CMS page not found"),
     * )
     */
    public function privacyPolicy()
    {
        return $this->getPageContent('privacy-policy');
    }

    /**
     * @OA\Get(
     *     path="/api/terms-of-use",
     *     summary="Get Terms of Use content",
     *     tags={"CMS"},
     *     @OA\Response(
     *         response=200,
     *         description="CMS content details",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="CMS page not found"),
     * )
     */
    public function termsOfUse()
    {
        return $this->getPageContent('terms-of-use');
    }

    /**
     * @OA\Get(
     *     path="/api/quick-start",
     *     summary="Get Quick Start content",
     *     tags={"CMS"},
     *     @OA\Response(
     *         response=200,
     *         description="CMS content details",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="CMS page not found"),
     * )
     */
    public function quickStart()
    {
        return $this->getPageContent('quick-start');
    }

    private function getPageContent($slug)
    {
        try {
            $page = CMSPage::where('slug', $slug)->firstOrFail();
            return $this->success($page, trans('cms.success'), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('cms.not_found'), Response::HTTP_NOT_FOUND);
        }
    }
}
