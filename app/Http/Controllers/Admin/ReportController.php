<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportAddEditRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Order;
use App\Traits\RestfulTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
// use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @category	Controller
 * @package		Report
 * @author		Harish Mogilipuri
 * @license
 * @link
 *
 */
class ReportController extends Controller
{
    use RestfulTrait;

    public function __construct()
    {
        $this->view = 'Admin.';
    }

    /**
     * Display a listing of the Report.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function index(Request $request)
    {

    }

    public function manageReports()
    {
        return view($this->view . 'reports');
    }

    public function manageOrderReports()
    {
        return view($this->view . 'reportsOrders');
    }


    public function getMonthlyOrderReport(Request $request)
    {
        $this->reportValidator($request->startDate,$request->endDate);
        
        $ordersQuery = Order::with('user');
        // ->select('*', DB::raw('@rownum  := @rownum  + 1 AS rownum'));
        if (isset($request->trash) && $request->trash == 1) {
            $ordersQuery = $ordersQuery->trashed();
        } else {
            $ordersQuery = $ordersQuery->notTrashed();
        }
        if ($request->startDate) {
            $ordersQuery->whereDate('created_at', '>=', $request->startDate);
        }
    
        if ($request->endDate) {
            $ordersQuery->whereDate('created_at', '<=', $request->endDate);
        }

        $orders = $ordersQuery->orderBy('id', 'DESC')->get();
        return DataTables::of($orders)
            // ->addColumn('srno', function ($orders) {
            //     return '<input type="checkbox" class="inline-checkbox" name="multiple[]" value="' . $orders->id .  '"/> ' . $orders->rownum;
            // })
            ->editColumn('user', function ($orders) {
                return $orders->user->name ?? '';
            })
            ->editColumn('date', function ($orders) {
                return $orders->created_at;
            })
            ->editColumn('order_ref', function ($orders) {
                return $orders->order_ref;
            })
            ->editColumn('amount', function ($orders) {
                return $orders->amount;
            })
            ->editColumn('razorpay_order_id', function ($orders) {
                return $orders->razorpay_order_id;
            })
            ->editColumn('payment_status', function ($orders) {
                if ($orders->payment_status == 'paid') {
                    return   '<span class="fw-bold text-success">' . $orders->payment_status. '</span>';
                } else {
                    return '<span class="fw-bold text-danger">' . $orders->payment_status . '</span>';
                }
            })
            ->rawColumns(['srno', 'payment_status'])
            ->make(true);
}


public function reportValidator($startDate,$endDate){
         // Input data
         $data = [
            'startDate' => $startDate, 
            'endDate' => $endDate,     
        ];
    
        // Validation rules
        $rules = [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ];
    
        // Validate
        $validator = Validator::make($data, $rules);
    
        // Check validation
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all(); // Get all errors as an array
            $errorString = implode(', ', $errorMessages); // Join errors with a comma
            $response = [
                'code' => 422,
                'status' => 'errors',
                'data' => [],
                'message' => $errorString,
            ];
            return response()->json($response, $response['code']);
        }
}
public function getMonthlyOrderCountReport($startDate,$endDate)
{
   $this->reportValidator($startDate,$endDate);
    // Initialize query builder for Order model
    $ordersQuery = Order::query();

    // Apply filters if date range is provided
    if ($startDate) {
        $ordersQuery->whereDate('created_at', '>=', $startDate);
    }

    if ($endDate) {
        $ordersQuery->whereDate('created_at', '<=', $endDate);
    }

    // Clone the query to avoid modifying the original instance
    $paidOrdersQuery = (clone $ordersQuery)->where('payment_status', 'paid');
    $pendingOrdersQuery = (clone $ordersQuery)->where('payment_status', 'pending');
    $failedOrdersQuery = (clone $ordersQuery)->where('payment_status', 'failed');

    // Step 1: Count different types of orders
    $paidOrdersCount = $paidOrdersQuery->count();
    $pendingOrdersCount = $pendingOrdersQuery->count();
    $failedOrdersCount = $failedOrdersQuery->count();

    // Step 2: Calculate total revenue
    $totalRevenue = $paidOrdersQuery->sum('amount');

    // Output the results
    $countReports = [
        'paid_orders' => $paidOrdersCount,
        'pending_orders' => $pendingOrdersCount,
        'failed_orders' => $failedOrdersCount,
        'total_revenue' => $totalRevenue,
    ];
    return $this->showSuccessResponse($countReports);
}


    /**
     * Store a newly created Report data.
     *
     * @param  App\Http\Requests\Admin\Request;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
    }

    /**
     * get selected  Report.
     *
     * @param 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        
    }

    public function show($id)
    {
    }

    /**
     * Update a Report
     *
     * @param App\Http\Requests\Admin\Request $request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
    }

   
    /**
     * Enable or Disable the Report
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableOrDisableReport(
        Request $request
    ) {
      
    }


    /**
     * Enable or Disable the Report
     *
     * @param  \Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyReport(
        Request $request
    ) {
      
    }

}
