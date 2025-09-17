<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserTreeRelation;
use App\Traits\RestfulTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

/**
 * @category	Controller
 * @package		Booking
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */

class OrderController extends Controller
{
    use RestfulTrait;
    public function __construct()
    {
        $this->view = 'Admin.';
    }

    public function index(Request $request)
    {
        // Fetch the logged-in admin's role
        $admin = auth()->user(); // Assuming the admin is authenticated
        $roleId = $admin->role; // Role of the logged-in admin
// dd($admin->id);
        // Step 1: Define the base query
        $orders = Order::select('*', DB::raw('@rownum := @rownum + 1 AS rownum'))->with(['orderLogs']);

        // Step 2: Apply role-based filtering
        if (in_array($roleId, [2, 5])) { // For roles 2 and 5, show only assigned orders
            $orders->leftJoin('order_assignments', 'orders.id', '=', 'order_assignments.order_id')
                ->where(function ($query) use ($admin, $roleId) {
                    $query->where('order_assignments.admin_id', $admin->id); // Orders assigned to this admin
                        //->orWhere('order_assignments.role_id', $roleId);  // Orders assigned to their role
                });
        }

        // Step 3: Order by latest and fetch
        $orders = $orders->treeOrders()->orderBy('orders.id', 'DESC')->get();
        // Step 4: Return data to DataTables
        return DataTables::of($orders)
            ->addColumn('srno', function ($orders) {
                return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $orders->id .  '"/> ' . $orders->rownum;
            })
            ->editColumn('order_ref', function ($orders) {
                return '<a style="color:green;" href="' . route('order.show', $orders->id ??'NA') . '" target="_blank">
                    <u>' . $orders->order_ref . '</u></a>';
            })
            ->editColumn('type', function ($orders) {
                $types = [1 => 'Sponsor', 2 => 'Adopt', 3 => 'Adopt Renewal', 4 => 'Ecommerce Product'];
                return $types[$orders->type] ?? 'Unknown';
            })
            ->editColumn('amount', function ($orders) {
                return $orders->amount;
            })
            ->editColumn('orderDate', function ($orders) {
                return date('d-m-Y', strtotime($orders->created_at));
            })
            ->editColumn('order_status', function ($orders) {
                return '<div class="ms-sm-3"><span class="badge super-badge bg-success">' . $orders->order_status . '</span></div>';
            })
            ->editColumn('payment_status', function ($orders) {
                return '<div class="ms-sm-3"><span class="badge super-badge bg-success">' . $orders->payment_status . '</span></div>';
            })
            ->addColumn('actions', function ($orders) {
                return '<div class="btn-group">
                    <button class="btn btn-link text-dark dropdown-toggle dropdown-toggle-split m-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                    <span class="visually-hidden">Toggle Dropdown</span></button>
                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1" style="">
                        <a target="_blank" class="dropdown-item d-flex align-items-center" href="' . route('order.show', ['order' => $orders->id ?? 'NA']) . '">
                        <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                        View Details </a>
                        <a target="_blank" class="dropdown-item d-flex align-items-center" href="' . route('order.invoice', ['orderId' => $orders->id]) . '">
                        <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path>
                            <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path>
                        </svg>
                        Invoice</a>
                    </div></div>';
            })
            ->rawColumns(['srno', 'type', 'actions', 'payment', 'order_ref', 'order_status', 'payment_status'])
            ->make(true);
    }




    public function manageOrder()
    {
        $All = Order::count();
        return view(
            $this->view . 'orders',
            compact(['All'])
        );
    }



    public function show($id)
    {
        // dd(auth('admin')->user()->hasRole('supervisors'));
        $order = Order::with(['orderLogs', 'user', 'orderLogs.tree','paymentDetails'])->find($id);
        $adoptedTrees = UserTreeRelation::with(['user', 'originalTree', 'adoptedTree','order'])
        ->where(['status'=>'active','order_id'=>$id])->get();
        return view(
            $this->view . 'orderList',
            compact(['order','adoptedTrees'])
        );
    }

    public function invoice($id)
    {
        $order = Order::with(['orderLogs', 'user', 'orderLogs.tree'])->find($id);
        return view(
            $this->view . 'invoice',
            compact(['order'])
        );
    }


    public function distributeOrdersByRole(int $roleId, array $statuses = ['failed', 'pending'])
    {
        // Step 1: Fetch admins with the specified role and their workload
        $admins = DB::table('admins')
            ->leftJoin('order_assignments', function ($join) use ($roleId) {
                $join->on('admins.id', '=', 'order_assignments.admin_id')
                    ->where('order_assignments.role_id', '=', $roleId);
            })
            ->where('admins.role', '=', $roleId)
            ->select('admins.id', DB::raw('COUNT(order_assignments.id) as assigned_orders'))
            ->groupBy('admins.id')
            ->orderBy('assigned_orders', 'ASC') // Admins with fewer orders get priority
            ->get();

        if ($admins->isEmpty()) {
            return "No admins with role ID $roleId available to assign orders.";
        }

        // Step 2: Fetch unassigned orders with the given statuses
        $orders = DB::table('orders')->where('product_type', Order::TREE_ORDERS)
            ->whereIn('order_status', $statuses)
            ->whereNotExists(function ($query) use ($roleId) {
                $query->select(DB::raw(1))
                    ->from('order_assignments')
                    ->whereRaw('orders.id = order_assignments.order_id')
                    ->where('order_assignments.role_id', '=', $roleId);
            })
            ->pluck('id');

        if ($orders->isEmpty()) {
            return "No orders with statuses " . implode(', ', $statuses) . " available for assignment.";
        }

        // Step 3: Distribute orders among admins
        $assignments = [];
        $adminCount = $admins->count();
        $index = 0;

        foreach ($orders as $orderId) {
            $admin = $admins[$index % $adminCount];
            $assignments[] = [
                'order_id' => $orderId,
                'admin_id' => $admin->id,
                'role_id' => $roleId,
                'assigned_at' => now(),
            ];
            $index++;
        }

        // Step 4: Insert assignments into the database
        DB::table('order_assignments')->insert($assignments);

        return "Orders have been successfully assigned to role ID $roleId.";
    }

    public function assignOrders()
    {
        $telecallerResult = $this->distributeOrdersByRole(5, ['failed', 'pending']); // Telecallers
        echo $telecallerResult;

        $supervisorResult = $this->distributeOrdersByRole(2, ['paid']); // Supervisors
        echo $supervisorResult;
    }

}
