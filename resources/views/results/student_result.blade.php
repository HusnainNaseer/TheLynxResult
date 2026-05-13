@extends('layouts.main')
@section('content')
    <style>
        .pagination nav {
            width: 100% !important;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">

            <h4 class="mb-3">All Results</h4>

            @if ($isAdmin)
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select id="userFilter" class="form-control">
                            <option value="">All Branches Result</option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <input type="search" id="search" class="form-control w-25" placeholder="Search student">
                        {{-- @role('Teacher') --}}
                            <a href="{{ route('results.create') }}" class="btn btn-primary">Create</a>
                        {{-- @endrole --}}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="resultTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Roll No</th>
                                    <th>Grade</th>
                                    <th>Percentage</th>
                                    <th>Session</th>
                                    <th>Attendance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sr = $subjects->firstItem();
                                @endphp
                                @foreach ($subjects as $result)
                                    <tr>
                                        <td>{{ $sr++ }}</td>
                                        <td>{{ $result->name }}</td>
                                        <td>{{ $result->class }}</td>
                                        <td>{{ $result->section }}</td>
                                        <td>{{ $result->rollno }}</td>
                                        <td>{{ $result->overall_grade }}</td>
                                        <td>{{ $result->overall_percentage }}%</td>
                                        <td>{{ $result->session->title ?? 'N/A' }}</td>
                                        <td>{{ $result->attendance }}</td>
                                        <td>
                                            <a href="{{ route('results.show', $result->id) }}" class="btn btn-info btn-sm">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            {{-- @role('Teacher') --}}
                                                <a href="{{ route('results.edit', $result->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <form action="{{ route('results.destroy', $result->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this result?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                {{-- @endrole --}}
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ✅ Pagination Links --}}
                    <div class="d-flex justify-content-between mt-3 pagination">
                        {{ $subjects->links() }}
                    </div>


                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const searchInput = document.getElementById('search');
            const userFilter = document.getElementById('userFilter');
            const tbody = document.querySelector('#resultTable tbody');

            function fetchResults() {
                const search = searchInput.value;
                const userId = userFilter ? userFilter.value : '';

                fetch(`{{ route('results.search') }}?search=${search}&user_id=${userId}`)
                    .then(res => res.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        if (data.length === 0) {
                            tbody.innerHTML =
                                `<tr><td colspan="10" class="text-center">No results found</td></tr>`;
                            return;
                        }

                        data.forEach((r, i) => {
                            tbody.innerHTML += `
                    <tr>
                        <td>${i+1}</td>
                        <td>${r.name}</td>
                        <td>${r.class}</td>
                        <td>${r.section}</td>
                        <td>${r.rollno}</td>
                        <td>${r.overall_grade ?? 'N/A'}</td>
                        <td>${r.overall_percentage ?? 0}%</td>
                        <td>${r.session ? r.session.title : 'N/A'}</td>
                        <td>${r.attendance ?? 0}</td>
                        <td>
    <a href="{{ url('results') }}/${r.id}" class="btn btn-info btn-sm">
        <i class="ri-eye-line"></i>
    </a>
    
    <a href="{{ url('results') }}/${r.id}/edit" class="btn btn-warning btn-sm">
        <i class="ri-edit-line"></i>
    </a>

    <form action="{{ url('results') }}/${r.id}" method="POST" class="d-inline"
          onsubmit="return confirm('Are you sure you want to delete this result?')">

        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">

        <button class="btn btn-danger btn-sm">
            <i class="ri-delete-bin-line"></i>
        </button>
    </form>
</td>
                    </tr>`;
                        });
                    });
            }

            searchInput.addEventListener('keyup', fetchResults);
            if (userFilter) userFilter.addEventListener('change', fetchResults);
        });
    </script>
@endsection
