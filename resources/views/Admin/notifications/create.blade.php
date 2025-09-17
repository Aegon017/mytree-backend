@extends('Admin.layouts.admin_layout')

@section('title', 'Notification')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Create Push Notification</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('notifications.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="title">Notification Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Notification Message</label>
                        <textarea name="message" id="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="send_to">Send To</label>
                        <select name="send_to" id="send_to" class="form-control" required>
                            <option value="all" {{ old('send_to') == 'all' ? 'selected' : '' }}>All Users</option>
                            <option value="specific" {{ old('send_to') == 'specific' ? 'selected' : '' }}>Specific Users</option>
                        </select>
                    </div>

                    <div class="form-group" id="user_ids_div" style="{{ old('send_to') == 'specific' ? 'display:block;' : 'display:none;' }}">
                        <label for="user_ids">Select Users</label>
                        <select name="user_ids[]" id="user_ids" class="form-control" multiple>
                            <!-- Dynamically load user options via AJAX -->
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Send Notification</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        var getUsersUrl = "{{ route('notifications.loadUsers') }}";
        // Toggle user selection based on "send_to" value
        document.getElementById('send_to').addEventListener('change', function() {
            const userIdsDiv = document.getElementById('user_ids_div');
            if (this.value === 'specific') {
                userIdsDiv.style.display = 'block';
                loadUsers(); // Load users when specific is selected
            } else {
                userIdsDiv.style.display = 'none';
            }
        });

        // Function to load users dynamically
        function loadUsers() {
            // Clear existing options
            const userSelect = document.getElementById('user_ids');
            userSelect.innerHTML = '';

            // Make an AJAX request to fetch users
            fetch(getUsersUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const users = data.users;
                        users.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = `${user.name} (${user.email})`;
                            userSelect.appendChild(option);
                        });
                    } else {
                        alert('Failed to load users.');
                    }
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                });
        }
    </script>
@endsection
