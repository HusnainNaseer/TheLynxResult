@extends('layouts.main')
@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Edit Branch</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('branches.index') }}">Branches</a>
                                </li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="ri-error-warning-line me-2"></i>Please fix the highlighted fields.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="avatar-sm">
                            <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-4">
                                <i class="ri-building-2-line"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $branch->name }}</h5>
                            <div class="text-muted small">ERP ID: {{ $branch->erp_branch_id }}</div>
                        </div>
                    </div>

                    <form action="{{ route('branches.update', $branch) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $branch->email) }}" placeholder="branch@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Contact</label>
                                <input type="text" id="phone" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $branch->phone) }}" placeholder="Enter contact number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="principal_headmistress" class="form-label">Principal/Headmistress</label>
                                <input type="text" id="principal_headmistress" name="principal_headmistress"
                                    class="form-control @error('principal_headmistress') is-invalid @enderror"
                                    value="{{ old('principal_headmistress', $branch->principal_headmistress) }}"
                                    placeholder="Enter Principal/Headmistress">
                                @error('principal_headmistress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="executive_director_islamabad" class="form-label">
                                    Executive Director Islamabad
                                </label>
                                <input type="text" id="executive_director_islamabad"
                                    name="executive_director_islamabad"
                                    class="form-control @error('executive_director_islamabad') is-invalid @enderror"
                                    value="{{ old('executive_director_islamabad', $branch->executive_director_islamabad) }}"
                                    placeholder="Enter Executive Director Islamabad">
                                @error('executive_director_islamabad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                    placeholder="Enter branch address">{{ old('address', $branch->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('branches.index') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line me-1"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-3-line me-1"></i>Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
