<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use Session;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Http\Requests;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Models\Admin\Admin;
use App\Models\Admin\Booking;
use App\Models\User;
use App\Traits\RestfulTrait;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\Admin\LoginRequest;
// use App\Models\Admin\AddOns;
use App\Models\Admin\Area;
use App\Models\Admin\City;
use App\Models\Admin\FollowUp;
use App\Models\Admin\Leads;
use App\Models\Admin\State;
use App\Models\Admin\Tree;
use App\Models\User as ModelsUser;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\DB;

/**
 * @category	Controller
 * @package		Admin
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on	17-08-2022
 */
class AdminAuthController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, RestfulTrait;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'Admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest:admin')->except('logout');
        $this->view = 'Admin.';
    }

    public function index()
    {
        if (auth()->guard('admin')->user()) {
            return redirect()->route('dashboard');
        }
        return view('Admin.auth.login');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect()->route('login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * Login
     *
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    public function login(LoginRequest $request)
    {
        if (auth()->guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            // dd(55);
            //Auth::guard('admin')->user();
            if(Auth::guard('admin')->user()->role ==1){
                return redirect()->route('dashboard');
            }else{
                return redirect()->route('location');
            }
        } else {
            return redirect()->back()->with('error', 'your username and password are wrong.');
        }
    }
    /**
     * Change Password
     */
    public function changePassword()
    {
        return view('Admin.change_password');
    }
    /**
     * Change Password
     */
    public function updatePassword(ChangePasswordRequest $request)
    {
        $admin = Admin::find(auth()->user()->id)->update(['password' => bcrypt($request->new_password)]);
        if ($admin) {
            return $this->createdResponse(
                $admin,
                'Password updated'
            );
        } else {
            return response()->json('Password updated failed', 204);
        }
    }
    /**
     * Show the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        
        $total_user = User::count();
        $total_package = 0;
        //Package::notTrashed()->count();
        $total_employee = Admin::active()->notTrashed()->count();
        $total_cities = City::active()->notTrashed()->count();
        $total_states = State::active()->notTrashed()->count();
        $total_trees =  Tree::active()->notTrashed()->count();
        $latest_logins = Admin::active()->notTrashed()->orderBy('login_at','DESC')->take(5)->get();
        $dayWiseData= [];
        //$this->getDayWiseStatusCounts();
       
        return view('Admin.dashboard', compact(['total_employee','total_cities','total_states','total_trees','dayWiseData','total_user','latest_logins']));
    }


    public function getDayWiseStatusCounts()
    {
            $daysAgo = Carbon::now()->subDays(6);
            $statusCounts = DB::table('trees')
                ->select(DB::raw('DATE(created_at) as date'), 'status', DB::raw('count(*) as count'))
                ->where('created_at', '>=', $daysAgo)
                ->groupBy('date', 'status')
                ->orderBy('date', 'asc')
                ->get();
            // Formatting the result into a more usable structure
            $result = [];
            foreach ($statusCounts as $count) {
                $result[$count->date]['status_' . $count->status] = $count->count;
            }
            // Ensure all 5 days are present in the result, even if counts are 0
            for ($i = 0; $i < 6; $i++) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $result[$date]['status_1'] = $result[$date]['status_1'] ?? 0;
                $result[$date]['status_0'] = $result[$date]['status_0'] ?? 0;
            }

            // Sort the result by date to maintain consistency
            ksort($result);
            return $result;
    }
    
    /**
     * Show the location.
     *
     * @return \Illuminate\Http\Response
     */
    public function location()
    {
       return view('Admin.location');
    }

    

    /**
     * storeLocation
     *
     * @param  Request;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeLocation(Request $request)
    {
        //$data       =   $request->all();
        $leads    =   $this->checkResource(Auth::user()->id);
        if (!$leads instanceof Admin) {
            return $leads;
        }
        $data['longitude'] = $request->longitude;
        $data['latitude'] = $request->latitude;
        $data['updated_by'] = Auth::guard('admin')->user()->id;
        $data['login_at'] = date('Y-m-d h:i:s');
        $leads->fill($data);
        $leads->save();
        return redirect()->route('dashboard');
    }

    /**
     * Check for the Leads exists
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\app\Models\Leads
     */
    public function checkResource($id)
    {
        $package = Admin::find($id);
        if (!$package) {
            return $this->notFoundResponse(
                trans('leads.not_found')
            );
        }
        return $package;
    }
}
