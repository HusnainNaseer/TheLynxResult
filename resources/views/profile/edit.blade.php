@extends('layouts.main')
@section('content')
    <style>
        .alert {
            display: block;
        }

        .alert-success {
            color: green !important;
        }

        .alert-danger {
            color: red !important;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Profile Settings</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Profile</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            @if (session('error'))
                <div class="row">
                    <span class="alert alert-danger text-white">
                        {{ session('error') }}
                    </span>
                </div>
            @endif

            @if (session('success'))
                <div class="row">
                    <span class="alert alert-success text-white">
                        {{ session('success') }}
                    </span>
                </div>
            @endif
            <div class="row">
                <!-- Profile Picture Card -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Profile Picture</h5>

                            <div class="text-center">
                                <div class="mb-4">
                                    <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('assets/auth/images/users/avatar.png') }}"
                                        alt="Profile Picture" class="rounded-circle img-thumbnail" id="profileImagePreview"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>

                                <form id="profilePictureForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="file" class="form-control" id="profile_picture"
                                            name="profile_picture" accept="image/*">
                                        <small class="text-muted">Allowed: JPG, PNG, GIF (Max: 2MB)</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-upload-2-line"></i> Upload Picture
                                    </button>
                                </form>

                                <div id="pictureMessage" class="mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Branch Information Display -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Branch Information</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium">Branch Name:</td>
                                            <td>{{ Auth::user()->branch_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Email:</td>
                                            <td>{{ Auth::user()->branch_email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Phone:</td>
                                            <td>{{ Auth::user()->branch_phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Address:</td>
                                            <td>{{ Auth::user()->branch_address ?? 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile & Branch Information Form -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Update Profile Information</h5>

                            <form id="profileInfoForm">
                                @csrf
                                @method('PATCH')

                                <!-- Personal Information -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="ri-user-line"></i> Personal Information</h6>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Name *</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name', Auth::user()->name) }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', Auth::user()->email) }}" required>
                                    </div>
                                </div>

                                <hr>

                                <!-- Branch Information -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="ri-building-line"></i> Branch Information
                                        </h6>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="branch_name" class="form-label">Branch Name *</label>
                                        <input type="text" class="form-control" id="branch_name" name="branch_name"
                                            value="{{ old('branch_name', Auth::user()->branch_name) }}"
                                            placeholder="Enter branch name" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="branch_email" class="form-label">Branch Email *</label>
                                        <input type="email" class="form-control" id="branch_email" name="branch_email"
                                            value="{{ old('branch_email', Auth::user()->branch_email) }}"
                                            placeholder="branch@example.com" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="branch_phone" class="form-label">Branch Phone *</label>
                                        <input type="text" class="form-control" id="branch_phone" name="branch_phone"
                                            value="{{ old('branch_phone', Auth::user()->branch_phone) }}"
                                            placeholder="+1 234 567 8900" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="branch_address" class="form-label">Branch Address *</label>
                                        <textarea class="form-control" id="branch_address" required name="branch_address" rows="1"
                                            placeholder="Enter branch address">{{ old('branch_address', Auth::user()->branch_address) }}</textarea>
                                    </div>
                                </div>

                                <div id="profileMessage" class="mb-3"></div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Update Password Card -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Update Password</h5>

                            <form id="passwordForm">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="current_password" class="form-label">Current Password *</label>
                                        <input type="password" class="form-control" id="current_password"
                                            name="current_password" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">New Password *</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                    </div>
                                </div>

                                <div id="passwordMessage" class="mb-3"></div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-lock-password-line"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete Account Card -->
                    <div class="card mt-4 border-danger">
                        <div class="card-body">
                            <h5 class="card-title text-danger mb-4">Logout</h5>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-danger"><i class="ri-shut-down-line align-middle me-1"></i>
                                    Logout</button>
                            </form>
                        </div>
                    </div>
                    {{-- <div class="card mt-4 border-danger">
                        <div class="card-body">
                            <h5 class="card-title text-danger mb-4">Delete Account</h5>
                            
                            <div class="alert alert-warning" role="alert">
                                <i class="ri-alert-line"></i> <strong>Warning!</strong> Once your account is deleted, all of its resources and data will be permanently deleted.
                            </div>

                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="ri-delete-bin-line"></i> Delete Account
                            </button>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">Are you sure you want to delete your account? This action cannot be undone.</p>

                        <div class="mb-3">
                            <label for="delete_password" class="form-label">Enter your password to confirm *</label>
                            <input type="password" class="form-control" id="delete_password" name="password" required>
                        </div>

                        <div id="deleteMessage"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Profile Picture Preview
            $('#profile_picture').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profileImagePreview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Upload Profile Picture
            $('#profilePictureForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('profile.picture.update',Auth::user()->id) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#pictureMessage').html(`
                        <div class="alert alert-success alert-dismissible fade show">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    },
                    error: function(xhr) {
                        $('#pictureMessage').html(`
                        <div class="alert alert-danger alert-dismissible fade show">
                            ${xhr.responseJSON?.message || 'Error uploading picture'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    }
                });
            });

            // Update Profile Information
            $('#profileInfoForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('profile.update',Auth::user()->id) }}",
                    type: 'PATCH',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#profileMessage').html(`
                        <div class="alert alert-success alert-dismissible fade show">
                            ${response.message || 'Profile updated successfully'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    },
                    error: function(xhr) {
                        $('#profileMessage').html(`
                        <div class="alert alert-danger alert-dismissible fade show">
                            ${xhr.responseJSON?.message || 'Error updating profile'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    }
                });
            });

            // Update Password
            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('password.update' ,Auth::user()->id) }}",
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#passwordForm')[0].reset();
                        $('#passwordMessage').html(`
                        <div class="alert alert-success alert-dismissible fade show">
                            ${response.message || 'Password updated successfully'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    },
                    error: function(xhr) {
                        $('#passwordMessage').html(`
                        <div class="alert alert-danger alert-dismissible fade show">
                            ${xhr.responseJSON?.message || 'Error updating password'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    }
                });
            });

            // Delete Account
            $('#deleteAccountForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('profile.destroy') }}",
                    type: 'DELETE',
                    data: $(this).serialize(),
                    success: function(response) {
                        window.location.href = '/';
                    },
                    error: function(xhr) {
                        $('#deleteMessage').html(`
                        <div class="alert alert-danger alert-dismissible fade show">
                            ${xhr.responseJSON?.message || 'Error deleting account'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    }
                });
            });
        });
    </script>
@endsection
