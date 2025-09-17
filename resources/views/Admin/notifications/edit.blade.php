@extends('Admin.layouts.admin_layout')

@section('title', 'Notifications')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Push Notification</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('notifications.update', $notification->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="title">Notification Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $notification->title) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Notification Message</label>
                        <textarea name="message" id="message" class="form-control" rows="5" required>{{ old('message', $notification->message) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="send_to">Send To</label>
                        <select name="send_to" id="send_to" class="form-control" required>
                            <option value="all" {{ old('send_to', $notification->send_to) == 'all' ? 'selected' : '' }}>All Users</option>
                            <option value="specific" {{ old('send_to', $notification->send_to) == 'specific' ? 'selected' : '' }}>Specific Users</option>
                        </select>
                    </div>

                    <div class="form-group" id="user_ids_div" style="{{ old('send_to', $notification->send_to) == 'specific' ? 'display:block;' : 'display:none;' }}">
                        <label for="user_ids">Select Users</label>
                        <select name="user_ids[]" id="user_ids" class="form-control" multiple>
                            <!-- Dynamically load user options here -->
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, old('user_ids', $notification->user_ids ?? [])) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Update Notification</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle user selection based on "send_to" value
        document.getElementById('send_to').addEventListener('change', function() {
            const userIdsDiv = document.getElementById('user_ids_div');
            if (this.value === 'specific') {
                userIdsDiv.style.display = 'block';
            } else {
                userIdsDiv.style.display = 'none';
            }
        });
    </script>
@endsection
