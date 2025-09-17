<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\RestfulTrait;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

/**
 * @category	Controller
 * @package		Users
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */

class UserController extends Controller
{
    use RestfulTrait;
    public function __construct()
    {
        $this->view = 'Admin.';
    }
    public function index(Request $request)
    {
        // DB::statement(DB::raw('set @rownum=0'));
        $users = User::select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));

        $users = $users->orderBy('id', 'DESC')->get();
        return DataTables::of($users)
            ->addColumn('srno', function ($users) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $users->id .  '"/> ' . $users->rownum;
            })
            ->addColumn('details', function ($users) {
                return '<a style="color:green;" href="' . route('user.show',$users->id) . '" target="_blank">
                View</a>';
            })
            ->editColumn('name', function ($users) {
                return '<img src="' . $users->main_image_url .  '" style="width:30px;height:30px;border-radius: 67%;"/> '.$users->name;
            })

            ->editColumn('email', function ($users) {
                return '<span class="fw-normal d-flex align-items-center"><svg class="icon icon-xxs text-danger me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>'.$users->email.'</span>';
            })
            ->editColumn('mobile', function ($users) {
                return '<span class="fw-normal d-flex align-items-center"><svg class="icon icon-xxs text-success me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'.$users->mobile.' </span>';
            })


            ->rawColumns(['srno', 'name', 'email','mobile','details'])
            ->make(true);
    }


    public function manageUser()
    {
        $All = User::count();
        return view(
            $this->view . 'users',
            compact(['All'])
        );
    }
    public function show($id)
    {
        $user = User::with(['referrals', 'subscriptions', 'orders', 'donations', 'carts','carts.product'])->findOrFail($id);
        return view('Admin.userDetails', compact('user'));
    }
}
