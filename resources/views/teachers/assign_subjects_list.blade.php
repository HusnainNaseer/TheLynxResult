@extends('layouts.main')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Assign Subjects — Teachers</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Assign Subjects</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Card --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-user-star-line me-1"></i> Teacher List
                            {{-- <small class="text-muted ms-2">(showing users with Teacher role)</small> --}}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0" id="teachersTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        {{-- <th>Branch Name</th>
                                        <th>Branch Email</th>
                                        <th>Branch Phone</th> --}}
                                        {{-- <th>Branch Address</th> --}}
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teachers as $teacher)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-xs">
                                                        <span class="avatar-title rounded-circle bg-primary text-white fs-12">
                                                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <span class="fw-medium">{{ $teacher->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $teacher->email }}</td>
                                            {{-- <td>{{ $teacher->branch_name ?? '—' }}</td>
                                            <td>{{ $teacher->branch_email ?? '—' }}</td>
                                            <td>{{ $teacher->branch_phone ?? '—' }}</td>
                                            <td>{{ $teacher->branch_address ?? '—' }}</td> --}}
                                            <td class="text-center">
                                                <a href="{{ route('assign-subjects.create', $teacher->id) }}"
                                                   class="btn btn-sm btn-primary waves-effect waves-light"
                                                   title="Assign Subjects">
                                                    <i class="ri-book-mark-line me-1"></i> Assign Subject
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="ri-user-search-line fs-24 d-block mb-2"></i>
                                                No teachers found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($teachers->hasPages())
                            <div class="d-flex justify-content-end mt-3">
                                {{ $teachers->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection