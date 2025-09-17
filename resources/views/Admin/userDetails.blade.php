@extends('Admin.layouts.admin_layout')
@section('title', 'Users')

@push('styles')
<style>
    .user-details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .section {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #333;
        font-weight: bold;
        border-bottom: 2px solid #007bff;
        padding-bottom: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="user-details-container">
    <!-- User Details -->
    <div class="section">
        <h2 class="section-title">User Details</h2>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone:</strong> {{ $user->phone }}</p>
        <p><strong>Created At:</strong> {{ $user->created_at }}</p>
    </div>

    <!-- Referrals -->
    <div class="section">
        <h2 class="section-title">Referrals</h2>
        <ul>
            @forelse($user->referrals as $referral)
                <li>{{ $referral->name }} ({{ $referral->email }})</li>
            @empty
                <p>No referrals found.</p>
            @endforelse
        </ul>
    </div>

    <!-- Subscriptions -->
    <div class="section">
        <h2 class="section-title">Subscriptions</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Original Tree ID</th>
                    <th>Adopted Tree ID</th>
                    <th>Subscription Start</th>
                    <th>Subscription End</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->id }}</td>
                    <td>
                    <a target="_blank" href="{{ route('admin.tree') }}?type=Sponsor&typeId=1&treeId={{$subscription->original_tree_id}}" ><div class="action-drp-dwn action-btns"><button id="reg-user_ 25" class="btn btn-success mb-3">
                                         <i class="la la-ellipsis-v"></i>View
                                        </button>
                                            </div></a>
                    </td>
                    <td>
                    <a target="_blank" href="{{ route('admin.tree') }}?type=Adopted&treeId={{$subscription->adopted_tree_id}}&adoptedStatus=1" ><div class="action-drp-dwn action-btns"><button id="reg-user_ 25" class="btn btn-success mb-3">
                                         <i class="la la-ellipsis-v"></i>View
                                        </button>
                                            </div></a>
                    </td>
                    <td>{{ $subscription->subscription_start }}</td>
                    <td>{{ $subscription->subscription_end }}</td>
                    <td>{{ $subscription->status }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">No subscriptions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Orders -->
    <div class="section">
        <h2 class="section-title">Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order Ref</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->order_ref }}</td>
                    <td>${{ $order->amount }}</td>
                    <td>{{ $order->order_status }}</td>
                    <td>{{ $order->created_at }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">No orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Donations -->
    <div class="section">
        <h2 class="section-title">Donations</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->donations as $donation)
                <tr>
                    <td>{{ $donation->id }}</td>
                    <td>₹{{ $donation->amount }}</td>
                    <td>{{ $donation->created_at }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">No donations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Cart Items -->
    <div class="section">
        <h2 class="section-title">Cart Items</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->carts as $cart)
                <tr>
                    <td>{{ $cart->id }}</td>
                    <td>{{ $cart->product->name ?? '' }}</td>
                    <td>{{ $cart->quantity }}</td>
                    <td>${{ $cart->price }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">No cart items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
