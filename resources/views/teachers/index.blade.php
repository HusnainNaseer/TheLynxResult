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

            <!-- start page title -->
            <div class="row" style="display:flex; justify-content:center;align-items:center;">
                <div class="col-10">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Employees</h4>
                    </div>
                </div>
                <div class="col-2" style="display:flex; justify-content:end;">
                    <a class="btn btn-primary mb-4" href="{{ route('teachers.create') }}">
                        <i class="ri-add-line"></i> Add Employee
                    </a>
                </div>
            </div>
            <!-- end page title -->

            <div class="row student-results">
                <div class="col-lg-12">
                    @if (session('error'))
                        <span class="alert alert-danger text-white">
                            {{ session('error') }}
                        </span>
                    @endif

                    @if (session('success'))
                        <span class="alert alert-success text-white">
                            {{ session('success') }}
                        </span>
                    @endif

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title">All Employees</h4>

                                <div class="d-flex gap-2">
                                    <select class="form-select" id="roleFilter" style="width: auto;">
                                        <option value="all">All Roles</option>
                                        {{-- <option value="Admin">Admin</option> --}}
                                        <option value="Coordinator">Coordinator</option>
                                        <option value="Teacher">Teacher</option>
                                        {{-- <option value="Student">Student</option> --}}
                                        <option value="User">User</option>
                                    </select>

                                    <input class="form-control" type="search" id="teacherSearch" name="search"
                                        placeholder="Search employee" style="width: 250px;">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="teacherTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($users as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @php
                                                        $img_src = asset('assets/auth/images/users/avatar.png');
                                                        if (
                                                            ($user->profile_picture)){
                                                            $img_src = asset('storage/' . $user->profile_picture);
                                                        }
                                                        elseif(isset($user->erp_picture)){
                                                            $baseUrl = env('ERP_URL');
                                                            $img_src = $baseUrl.'/storage/emp_profile_images/'.$user->erp_picture;
                                                        }
                                                        else{
                                                            $img_src = $img_src;
                                                        }
                                                    @endphp
                                                    <img src="{{ $img_src}}"

                                                        alt="Profile Picture" class="rounded-circle" width="50"
                                                        height="50"
                                                        onerror="this.src='{{ asset('assets/auth/images/users/avatar.png') }}'">
                                                </td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>

                                                <!-- ROLE COLUMN -->
                                                <td>
                                                    @if ($user->hasRole('Admin'))
                                                        <span class="badge bg-dark" data-role="Admin">Admin</span>
                                                    @elseif($user->hasRole('Coordinator'))
                                                        <span class="badge bg-info"
                                                            data-role="Coordinator">Coordinator</span>
                                                    @elseif($user->hasRole('Teacher'))
                                                        <span class="badge bg-success" data-role="Teacher">Teacher</span>
                                                    @elseif($user->hasRole('Student'))
                                                        <span class="badge bg-primary" data-role="Student">Student</span>
                                                    @else
                                                        <span class="badge bg-secondary" data-role="User">User</span>
                                                    @endif
                                                </td>

                                                <!-- ACTION COLUMN -->
                                                <td>
                                                    @if ($user->hasRole('User'))
                                                        <form action="{{ route('users.grant', $user->id) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            <button class="btn btn-sm btn-success">
                                                                Grant Access
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if ($user->hasRole('Teacher') || $user->hasRole('Coordinator'))
                                                        <form action="{{ route('users.revoke', $user->id) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            <button class="btn btn-sm btn-danger">
                                                                Revoke Access
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if ($user->hasRole('Teacher') || $user->hasRole('Coordinator'))
                                                        <a href="{{ route('teachers.edit', $user->id) }}"
                                                            class="btn btn-warning btn-sm">
                                                            <i class="ri-edit-line"></i> Edit
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    No employees found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('teacherSearch');
            const roleFilter = document.getElementById('roleFilter');
            const table = document.getElementById('teacherTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            function filterTable() {
                const searchFilter = searchInput.value.toLowerCase();
                const selectedRole = roleFilter.value;

                Array.from(rows).forEach(row => {
                    const cells = row.getElementsByTagName('td');

                    // Skip empty row
                    if (cells.length === 0 || cells[0].getAttribute('colspan')) {
                        row.style.display = 'none';
                        return;
                    }

                    // Search filter
                    let searchMatch = false;

                    Array.from(cells).forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(searchFilter)) {
                            searchMatch = true;
                        }
                    });

                    // Role filter
                    let roleMatch = true;

                    if (selectedRole !== 'all') {
                        const roleBadge = cells[4].querySelector('[data-role]');
                        roleMatch = roleBadge && roleBadge.getAttribute('data-role') === selectedRole;
                    }

                    // Show row only if both filters match
                    row.style.display = (searchMatch && roleMatch) ? '' : 'none';
                });
            }

            searchInput.addEventListener('keyup', filterTable);
            roleFilter.addEventListener('change', filterTable);

            // Run once on load
            filterTable();
        });
    </script>
@endsection
