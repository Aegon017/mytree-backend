@extends('Admin.layouts.admin_layout')
@section('style')
@parent
@endsection
@section('title', 'Ecommerce')
@section('content')

<div class="py-4">
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
                <a href="#">
                    <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order Details</li>
        </ol>
    </nav>
</div>
<div class="container">
    <div id="flash-message">
    </div>
    <div class="row">
        <div class="col-md-12">
            {{-- <h1 class="h4">order Details</h1> --}}
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card card-body border-0 shadow mb-4">
                <h2 class="h5 mb-4">Order Details</h2>
                @if ($order->payment_status == 'paid')
                <img src=" {{ asset('frontEnd/images/paid.png') }}"
                    class="avatar-xl rounded-circle mx-auto mt-n7 mb-4" alt="Neil Portrait">
                @elseif ($order->payment_status == 'failed')
                <img src=" {{ asset('frontEnd/images/cancel.png') }}"
                    class="avatar-xl rounded-circle mx-auto mt-n7 mb-4" alt="Neil Portrait">
                @else
                <img src="{{ asset('frontEnd/images/fail.png') }}"
                    class="avatar-xl rounded-circle mx-auto mt-n7 mb-4" alt="Neil Portrait">
                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table mb-0">

                                <tbody>
                                    <tr>
                                        <th scope="row" class="text-left fw-bold h6"><b>Order Id</b></th>
                                        <td>{{ $order->order_ref }}</td>
                                    <tr>
                                        <th scope="row" class="text-left fw-bold h6">Paid Amount</th>
                                        <td>{{ $order->amount }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left fw-bold h6">Order Status</th>
                                        <td>{{ $order->order_status }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left fw-bold h6">Razorpay Order Id</th>
                                        <td>{{ $order->razorpay_order_id }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left fw-bold h6">Razorpay Payment Id</th>
                                        <td>{{ $order->razorpay_payment_id ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left fw-bold h6">Payment Status</th>
                                        <td>
                                            @if ($order->payment_status == 'paid')
                                            <!-- <span class="badge bg-success">Paid</span> -->
                                            <div class="ms-sm-3"><span class="badge super-badge bg-success">Paid</span></div>
                                            @elseif ($order->payment_status == 'failed')
                                            <div class="ms-sm-3"><span class="badge super-badge bg-danger">Cancelled</span></div>
                                            @else
                                            <div class="ms-sm-3"><span class="badge super-badge bg-danger">Failed</span></div>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow border-0 text-center p-0">
                        <div class="profile-cover rounded-top"
                            data-background="{{ $order->user->profile_image_url }}"
                            style="background: url({{ $order->user->profile_image_url }});"></div>
                        <div class="card-body pb-5"><img src="{{ $order->user->profile_image_url }}"
                                class="avatar-xl rounded-circle mx-auto mt-n7 mb-4" alt="Neil Portrait">
                            <h4 class="h3">{{ $order->user->name }}</h4>
                            <h5 class="fw-normal">
                               
                            {{ $order->user->email }}
                            </h5>
                            <h5 class="fw-normal">
                                {{ $order->user->mobile }}
                            </h5>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-12 col-xl-12">
            <div class="card card-body border-0 shadow mb-4">
                <h2 class="h5 mb-4">Order Item List</h2>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0 rounded-start">#</th>
                                        <th class="border-0">Tree Name</th>
                                        <th class="border-0">Quantity</th>
                                        <th class="border-0">Price</th>
                                        <th class="border-0">Duration</th>
                                        <th class="border-0 rounded-end">Age</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $k=0;?>
                                    @foreach($order->orderLogs as $log)
                                    <?php $k++;?>
                                    <tr>
                                        <td scope="row" class="text-left fw-bold h6">{{ $k }}</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->tree_name }}</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->quantity }}</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->price }} INR</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->duration }} years</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->age }} years</td>
                                    <tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br/><br/><br/>
                <h2 class="h5 mb-4">Sponsored/Adopted Tree List</h2>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0 rounded-start">#</th>
                                        <th class="border-0">Tree Name</th>
                                        <th class="border-0">SKU</th>
                                        <th class="border-0">Subscription Start</th>
                                        <th class="border-0">Subscription End</th>
                                        <th class="border-0 rounded-end">Status</th>
                                        <th class="border-0 rounded-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $l=0;?>
                                    @foreach($adoptedTrees as $log)
                                    <?php $l++;?>
                                    <tr>
                                        <td scope="row" class="text-left fw-bold h6">{{ $l }}</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->adoptedTree->name }}</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->adoptedTree->sku }}</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->subscription_start }} </td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->subscription_end }} </td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->status }} </td>
                                        <td scope="row" class="text-left fw-bold h6">
                                        <a target="_blank" href="{{ route('admin.tree') }}?type=Sponsor&treeId={{$log->adopted_tree_id}}&adoptedStatus=1" ><div class="action-drp-dwn action-btns"><button id="reg-user_ 25" class="btn btn-success mb-3">
                                         <i class="la la-ellipsis-v"></i>View
                                        </button>
                                            </div></a>
                                        </td>
                                    <tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-body border-0 shadow mb-4">
    <h2 class="h5 mb-4">Payment History</h2>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table mb-0">
                    <tbody>
                        
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Razorpay Payment ID</th>
                            <td>{{ $order->paymentDetails->razorpay_payment_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Entity</th>
                            <td>{{ $order->paymentDetails->entity ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Amount</th>
                            <td>{{ $order->paymentDetails->amount ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Currency</th>
                            <td>{{ $order->paymentDetails->currency ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Status</th>
                            <td>{{ $order->paymentDetails->status ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Razorpay Order ID</th>
                            <td>{{ $order->paymentDetails->razorpay_order_id ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Invoice ID</th>
                            <td>{{ $order->paymentDetails->invoice_id ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">International</th>
                            <td>{{ $order->paymentDetails->international ?? null ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Method</th>
                            <td>{{ $order->paymentDetails->method ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Amount Refunded</th>
                            <td>{{ $order->paymentDetails->amount_refunded ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Refund Status</th>
                            <td>{{ $order->paymentDetails->refund_status ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Captured</th>
                            <td>{{ $order->paymentDetails->captured ?? null ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Description</th>
                            <td>{{ $order->paymentDetails->description ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Card ID</th>
                            <td>{{ $order->paymentDetails->card_id ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Bank</th>
                            <td>{{ $order->paymentDetails->bank ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Wallet</th>
                            <td>{{ $order->paymentDetails->wallet ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">VPA</th>
                            <td>{{ $order->paymentDetails->vpa ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Email</th>
                            <td>{{ $order->paymentDetails->email ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Phone</th>
                            <td>{{ $order->paymentDetails->contact ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Notes</th>
                            <td>{{ $order->paymentDetails->notes ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Fee</th>
                            <td>{{ $order->paymentDetails->fee ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Tax</th>
                            <td>{{ $order->paymentDetails->tax ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Error Code</th>
                            <td>{{ $order->paymentDetails->error_code ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Error Description</th>
                            <td>{{ $order->paymentDetails->error_description ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Payment Created At</th>
                            <td>{{ $order->paymentDetails->pay_created_at ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Created At</th>
                            <td>{{ $order->paymentDetails->created_at ?? 'N/A'}}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left fw-bold h6">Updated At</th>
                            <td>{{ $order->paymentDetails->updated_at ?? 'N/A'}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

        </div>
        
    </div>



</div>
@endsection
@section('script')
@parent
<script></script>
@endsection