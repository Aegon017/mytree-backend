@extends('Admin.layouts.admin_layout')
@section('title', 'Campaign')

@push('styles')
    <style>
        .campaign-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #FFF;
        }

        .campaign-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .campaign-header img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .campaign-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .campaign-details {
            flex: 1;
            margin-right: 20px;
        }

        .campaign-progress {
            text-align: center;
        }

        .progress-bar {
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: #28a745;
            transition: width 0.4s ease;
        }

        .donor-list {
            margin-top: 30px;
        }

        .donor-list h3 {
            margin-bottom: 15px;
        }

        .donor-table {
            width: 100%;
            border-collapse: collapse;
        }

        .donor-table th, .donor-table td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .donor-table th {
            background-color: #f8f9fa;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
            color: #fff;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
@endpush

@section('content')
<div class="campaign-container">
    <div class="campaign-header">
        <img src="{{ $campaign->main_image_url }}" alt="Campaign Image">
        <h1>{{ $campaign->name }}</h1>
    </div>

    <div class="campaign-info">
        <div class="campaign-details">
            <p><strong>Target Amount:</strong> ₹{{ $campaign->goal_amount }}</p>
            <p><strong>Raised Amount:</strong> ₹{{ $raisedAmount }}</p>
            <p><strong>Pending Amount:</strong> ₹{{ $pendingAmount }}</p>
            <p><strong>Donors:</strong> {{ $donors->count() }}</p>
        </div>

        
    </div>
    <div class="campaign-progress">
            <p><strong>{{ $raisedAmount }} raised of ₹{{ $campaign->goal_amount }}</strong></p>
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: {{ ($raisedAmount / $campaign->goal_amount) * 100 }}%;"></div>
            </div>
            <p><strong>Expire Date: {{ $campaign->expiration_date }} </strong></p>
            @php
                $daysLeft = \Carbon\Carbon::now()->diffInDays($campaign->expiration_date, false);
            @endphp
            @if ($daysLeft > 0)
                {{ $daysLeft }} Days Left
            @elseif ($daysLeft == 0)
                Last Day
            @else
            Expired
            @endif
        </div>
    <div class="donor-list">
        <h3>Supporters</h3>
        <table class="donor-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donors as $donor)
                    <tr>
                        <td>{{ $donor->donor_name }}</td>
                        <td>{{ $donor->donor_email }}</td>
                        <td>₹{{ $donor->amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p>{!! $campaign->description !!}</p>

</div>
@endsection
