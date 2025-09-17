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
            <li class="breadcrumb-item active" aria-current="page">Invoice</li>
        </ol>
    </nav>
</div>

<div class="container" >
    <div id="flash-message">
    </div>
    <!-- <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6 ml-auto">
                    <button class="btn btn-danger mb-3" onclick="printInvoice()"><i class="fa fa-trash" aria-hidden="true"
                            onclick=""></i>&nbsp;Print Invoice</button>
                </div>
    </div> -->
    <div id="printContent">
    <div class="row justify-content-center mt-4">
        <div class="col-12 col-xl-9">
            <div class="card shadow border-0 p-4 p-md-5 position-relative">
                <div class="d-flex justify-content-between pb-4 pb-md-5 mb-4 mb-md-5 border-bottom border-light">
                    <img class="image-md7" src="{{ asset('frontEnd/images/fav-icon.png') }}" height="200" width="200" alt="Rocket Logo">
                    
                    <div>
                        <h4>MyTree PVT.</h4>
                        <ul class="list-group simple-list">
                            <li class="list-group-item fw-normal">MyTree</li>
                            <li class="list-group-item fw-normal">Hyderabad, Telangana</li>
                            <li class="list-group-item fw-normal"><a class="fw-bold text-primary" href="#">info@mytree.com</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-6 d-flex align-items-center justify-content-center">
                    <h2 class="h1 mb-0">OrderId #{{ $order->order_ref }}</h2><span class="badge badge-lg bg-success ms-4">Paid</span>
                </div>
                <div class="row justify-content-between mb-4 mb-md-5">
                    <div class="col-sm">
                        <h5>User Information:</h5>
                        <div>
                            <ul class="list-group simple-list">
                                <li class="list-group-item fw-normal">{{ $order->user->name }}</li>
                                <li class="list-group-item fw-normal">{{ $order->user->mobile}}</li>
                                <li class="list-group-item fw-normal"><a class="fw-bold text-primary" href="#">{{ $order->user->email }}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm col-lg-4">
                        <dl class="row text-sm-right">
                            <dt class="col-6"><strong>Order ID.</strong></dt>
                            <dd class="col-6">{{ $order->order_ref }}</dd>
                            <dt class="col-6"><strong>Date:</strong></dt>
                            <dd class="col-6">{{date('d-m-Y', strtotime($order->created_at))}}</dd>
                        </dl>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="bg-light border-top">
                                    <tr>
                                        <th scope="row" class="border-0 text-left">#</th>
                                        <th scope="row" class="border-0">Item</th>
                                        <th scope="row" class="border-0">Qty</th>
                                        <th scope="row" class="border-0">Price</th>
                                        <th scope="row" class="border-0">Duration</th>
                                        <th scope="row" class="border-0">Age</th>
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
                                        <td scope="row" class="text-left fw-bold h6">₹ {{ $log->price }} INR</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->duration }} years</td>
                                        <td scope="row" class="text-left fw-bold h6">{{ $log->age }} years</td>
                                    <tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end text-right mb-4 py-4">
                            <div class="mt-4">
                                <table class="table table-clear">
                                    <tbody>
                                        <tr>
                                            <td class="left"><strong>Subtotal</strong></td>
                                            <td class="right">₹ {{ $order->amount }}</td>
                                        </tr>
                                        <tr>
                                            <td class="left"><strong>Discount (0%)</strong></td>
                                            <td class="right">₹ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="left"><strong>VAT (0%)</strong></td>
                                            <td class="right">₹ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="left"><strong>Total</strong></td>
                                            <td class="right"><strong>₹ {{ $order->amount }}</strong></td>
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
    </div>



</div>
@endsection
@section('script')
@parent
<script>
    function printInvoice() {
            // var contentToPrint = document.getElementById('invoicePdf').innerHTML; // Get the content of the div

            // // Create a new window or iframe for printing
            // var printWindow = window.open('', '', 'height=600,width=800');
            // printWindow.document.write('<html><head><title>Invoice</title></head><body>');
            // printWindow.document.write(contentToPrint); // Add the content to print
            // printWindow.document.write('</body></html>');
            // printWindow.document.close(); // Close the document to trigger loading
            // printWindow.print(); // Open the print dialog
            // // window.print()

            // Get the content to be printed (You can also clone if needed)
            var contentToPrint = document.getElementById('printContent').innerHTML;

            // Open a new window for printing
            var printWindow = window.open('', '', 'height=600,width=800');
            
            // Add the necessary HTML structure and CSS for printing
            printWindow.document.write('<html><head><title>Print</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; background-color: #fff; }');
            printWindow.document.write('.content { padding: 15px; border: 1px solid #000; background-color: #fff; }');
            printWindow.document.write('@media print { body * { visibility: hidden; } #printContent, #printContent * { visibility: visible; } }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(contentToPrint);  // Insert the content to be printed
            printWindow.document.write('</body></html>');
            printWindow.document.close();  // Close the document to trigger loading
            printWindow.print();
        }
</script>
@endsection