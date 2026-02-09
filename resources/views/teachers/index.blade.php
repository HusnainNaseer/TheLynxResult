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
                        <h4 class="mb-sm-0">Teachers</h4>
                    </div>
                </div>
                <div class="col-2" style="display:flex; justify-content:end;">
                    <a class="btn btn-primary mb-4" href="{{ route('teachers.create') }}">
                       <i class="ri-add-line"></i> Add Teacher
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
                                <h4 class="card-title">All Teachers</h4>

                                <form action="">
                                    <input class="form-control mb-6" type="search" id="teacherSearch" name="search"
                                        placeholder="Search student">
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="teacherTable">
                                    <thead>
                                        <tr>
                                            <th>Teacher Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>

                                                <!-- ROLE COLUMN -->
                                                <td>
                                                    @if ($user->hasRole('admin'))
                                                        <span class="badge bg-dark">Admin</span>
                                                    @elseif($user->hasRole('Teacher'))
                                                        <span class="badge bg-success">Teacher</span>
                                                    @else
                                                        <span class="badge bg-secondary">User</span>
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

                                                    @if ($user->hasRole('Teacher'))
                                                        <form action="{{ route('users.revoke', $user->id) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            <button class="btn btn-sm btn-danger">
                                                                Revoke Access
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if ($user->hasRole('Teacher'))
                                                        <form action="{{ route('teachers.edit', $user->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button class="btn btn-warning btn-sm 
                                                            +ri-edit-line">
                                                            Edit Teacher Data
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    No users found
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
            const table = document.getElementById('teacherTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();

                Array.from(rows).forEach(row => {
                    const cells = row.getElementsByTagName('td');
                    let match = false;

                    Array.from(cells).forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(filter)) {
                            match = true;
                        }
                    });

                    row.style.display = match ? '' : 'none';
                });
            });
        });
    </script>
@endsection
