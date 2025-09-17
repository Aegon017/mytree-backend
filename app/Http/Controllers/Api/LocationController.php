<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Area;
use App\Models\Admin\City;
use App\Models\Admin\State;
use App\Models\TreeLocation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    use ApiResponser;  
    /**
     * Get all states.
     *
     * @OA\Get(
     *     path="/api/states",
     *     summary="Get all states",
     *     tags={"Locations"},
     *     @OA\Response(response=200, description="List of states"),
     *     @OA\Response(response=404, description="States not found")
     * )
     */
    public function getStates()
    {
        $states = State::active()
        ->notTrashed()->get();
        return $this->success($states, trans('user.success'), Response::HTTP_OK);
    }

    /**
     * Get cities by state ID.
     *
     * @OA\Get(
     *     path="/api/cities/{state_id}",
     *     summary="Get cities by state ID",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="state_id",
     *         in="path",
     *         required=true,
     *         description="ID of the state",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of cities"),
     *     @OA\Response(response=404, description="Cities not found")
     * )
     */
    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->active()
        ->notTrashed()->get();
        return $this->success($cities, trans('user.success'), Response::HTTP_OK);
    }

    /**
     * Get areas by city ID.
     *
     * @OA\Get(
     *     path="/api/areas/{city_id}",
     *     summary="Get areas by city ID",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="path",
     *         required=true,
     *         description="ID of the city",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of areas"),
     *     @OA\Response(response=404, description="Areas not found")
     * )
     */
    public function getAreas($city_id)
    {
        $areas = Area::where('city_id', $city_id)->active()
        ->notTrashed()->get();
        return $this->success($areas, trans('user.success'), Response::HTTP_OK);

    }

    /**
     * Get tree locations.
     *
     * @OA\Get(
     *     path="/api/tree-locations",
     *     summary="Get tree locations",
     *     tags={"Locations"},
     *     @OA\Response(response=200, description="List of tree locations"),
     *     @OA\Response(response=404, description="Tree locations not found")
     * )
     */
    public function getTreeLocations()
    {
        $treeLocations = TreeLocation::with(['state', 'city', 'area'])
            ->active()
            ->notTrashed()
            ->get();

        return $this->success($treeLocations, trans('user.success'), Response::HTTP_OK);
    }

        /**
         * Get Tree Available states.
         *
         * @OA\Get(
         *     path="/api/tree-locations/states",
         *     summary="Get Tree Available states",
         *     tags={"Locations"},
         *     @OA\Response(response=200, description="List of states"),
         *     @OA\Response(response=404, description="States not found")
         * )
         */
        public function getTreeStates()
        {
            $states = TreeLocation::select('state_id as id', 'name as name')
                ->distinct()
                ->join('states', 'tree_locations.state_id', '=', 'states.id')
                ->where('tree_locations.trash', 0)
                ->get();

            if ($states->isEmpty()) {
                return $this->error('States not found', Response::HTTP_NOT_FOUND);
            }
            return $this->success($states, trans('user.success'), Response::HTTP_OK);
        }
        /**
         * Get areas for a state.
         *
         * @OA\Get(
         *     path="/api/tree-locations/states/{stateId}/areas",
         *     summary="Get areas of a state",
         *     tags={"Locations"},
         *     @OA\Parameter(
         *         name="stateId",
         *         in="path",
         *         description="State ID",
         *         required=true,
         *         @OA\Schema(type="integer")
         *     ),
         *     @OA\Response(response=200, description="List of areas in the state"),
         *     @OA\Response(response=404, description="Areas not found")
         * )
         */
        public function getTreeAreasByState($stateId)
        {
            $areas = TreeLocation::select('tree_locations.id as location_id','areas.id as area_id', DB::raw("CONCAT(areas.name, '(', cities.name, ')') as area_name"))
                ->join('cities', 'tree_locations.city_id', '=', 'cities.id')
                ->join('areas', 'tree_locations.area_id', '=', 'areas.id')
                ->where('tree_locations.state_id', $stateId)
                ->where('tree_locations.trash', 0)
                // ->distinct()
                ->get();

            if ($areas->isEmpty()) {
                return $this->error('Areas not found', Response::HTTP_NOT_FOUND);
            }
            return $this->success($areas, trans('user.success'), Response::HTTP_OK);
        }


}
