@extends('layouts.main')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h4 class="mb-0">{{ $title }}</h4>
            </div>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label" for="branchFilter">Branch</label>
                                <select id="branchFilter" name="branch_id" class="form-select">
                                    <option value="">All Branches</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ data_get($branch, 'id') }}"
                                            {{ (string) $selectedBranchId === (string) data_get($branch, 'id') ? 'selected' : '' }}>
                                            {{ data_get($branch, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="classFilter">Class</label>
                                <select id="classFilter" name="class_id" class="form-select" {{ $classes->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">All Classes</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ data_get($class, 'id') }}"
                                            {{ (string) $selectedClassId === (string) data_get($class, 'id') ? 'selected' : '' }}>
                                            {{ data_get($class, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="sectionFilter">Section</label>
                                <select id="sectionFilter" name="section_id" class="form-select" {{ $sections->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">All Sections</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ data_get($section, 'id') }}"
                                            {{ (string) $selectedSectionId === (string) data_get($section, 'id') ? 'selected' : '' }}>
                                            {{ data_get($section, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="search">Search</label>
                                <div class="input-group">
                                    <input type="search" id="search" name="search" class="form-control"
                                        placeholder="Student, roll no, father, phone" value="{{ $search }}">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <a href="{{ url()->current() }}" class="btn btn-light btn-sm">Reset Filters</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Roll No</th>
                                    <th>Branch</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                    <th>Percentage</th>
                                    <th>Approved Date</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('results.partials.workflow_rows', ['results' => $results])
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-3 pagination">
                        {{ $results->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('results.partials.term_result_modal')
@endsection
