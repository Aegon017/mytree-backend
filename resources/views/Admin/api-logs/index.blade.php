@extends('Admin.layouts.admin_layout')
@section('title', 'ApiLogs')
@section('content')
<div class="container">
    <h1 class="mt-4">Activity Logs</h1>
    <div class="card">
        <div class="card-header">
            <h4>API Activity Logs</h4>
        </div>
        <div class="card-body">
            <!-- Add .table-responsive to make the table responsive -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>User Email</th>
                            <th>Endpoint</th>
                            <th>Method</th>
                            <th>Request Payload</th>
                            <th>Response Payload</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->user_id ?? 'Guest' }}</td>
                                <td>{{ optional($log->user)->name ?? 'N/A' }}</td>
                                <td>{{ optional($log->user)->email ?? 'N/A' }}</td>
                                <td>{{ $log->endpoint }}</td>
                                <td>{{ $log->method }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#requestPayloadModal{{ $log->id }}">View</button>
                                    
                                    <!-- Modal for Request Payload -->
                                    <div class="modal fade" id="requestPayloadModal{{ $log->id }}" tabindex="-1" aria-labelledby="requestPayloadModalLabel{{ $log->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="requestPayloadModalLabel{{ $log->id }}">Request Payload</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <pre>{{ json_encode(json_decode($log->request_payload), JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#responsePayloadModal{{ $log->id }}">View</button>
                                    
                                    <!-- Modal for Response Payload -->
                                    <div class="modal fade" id="responsePayloadModal{{ $log->id }}" tabindex="-1" aria-labelledby="responsePayloadModalLabel{{ $log->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="responsePayloadModalLabel{{ $log->id }}">Response Payload</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <pre>{{ json_encode(json_decode($log->response_payload), JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ $log->user_agent }}</td>
                                <td>{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">No logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
        {{ $logs->links('pagination::bootstrap-5') }} <!-- Laravel's pagination links -->
        </div>
    </div>
</div>
@endsection
