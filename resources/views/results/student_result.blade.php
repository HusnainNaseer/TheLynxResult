@extends('layouts.main')

@section('content')
    <style>
        .pagination nav {
            width: 100% !important;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h4 class="mb-0">Result List</h4>
                @hasanyrole('Admin|Coordinator')
                <form method="POST" action="{{ route('results.sync-students', request()->query()) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">Sync Students</button>
                </form>
                @endhasanyrole
            </div>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <form id="resultFilterForm" method="GET" action="{{ route('students.result') }}">
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

                            <div class="col-md-3">
                                <label class="form-label" for="statusFilter">Status</label>
                                <select id="statusFilter" name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="draft" {{ $selectedStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="forwarded" {{ $selectedStatus === 'forwarded' ? 'selected' : '' }}>Forwarded</option>
                                    <option value="forwarded_to_class_teacher" {{ $selectedStatus === 'forwarded_to_class_teacher' ? 'selected' : '' }}>Forwarded to Class Teacher</option>
                                    <option value="forwarded_to_coordinator" {{ $selectedStatus === 'forwarded_to_coordinator' ? 'selected' : '' }}>Forwarded to Coordinator</option>
                                    <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <a href="{{ route('students.result') }}" class="btn btn-light btn-sm">Reset Filters</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if ($canUseForwardControls)
                        <form id="bulkForwardForm" method="POST" action="{{ route('results.bulk-forward') }}"
                            class="d-flex flex-wrap gap-2 align-items-center mb-3 d-none"
                            onsubmit="return confirm('Forward selected results?')">
                            @csrf
                            <input type="hidden" name="action" id="bulkForwardAction" value="">
                            <button type="submit" class="btn btn-primary btn-sm bulk-forward-btn d-none"
                                data-forward-action="forward_class_teacher">
                                FW Selected to Class Teacher
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm bulk-forward-btn d-none"
                                data-forward-action="forward_coordinator">
                                FW Selected to Coordinator
                            </button>
                        </form>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="resultTable">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        @if ($canUseForwardControls)
                                            <input type="checkbox" id="bulkCheckAll">
                                        @endif
                                    </th>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Roll No</th>
                                    <th>Branch</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                    <th>Percentage</th>
                                    <th style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentRows">
                                @include('results.partials.student_rows', ['students' => $students, 'canManageResults' => $canManageResults])
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-3 pagination" id="paginationLinks">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('resultFilterForm');
            const branch = document.getElementById('branchFilter');
            const classSelect = document.getElementById('classFilter');
            const section = document.getElementById('sectionFilter');
            const status = document.getElementById('statusFilter');
            const search = document.getElementById('search');
            const rows = document.getElementById('studentRows');
            const pagination = document.getElementById('paginationLinks');
            const bulkForwardForm = document.getElementById('bulkForwardForm');
            const bulkCheckAll = document.getElementById('bulkCheckAll');
            let timer = null;

            function refreshBulkControls() {
                if (!bulkForwardForm) return;

                const checks = Array.from(document.querySelectorAll('.bulk-result-check'));
                const availableActions = new Set(checks.map(check => check.dataset.forwardAction));

                bulkForwardForm.classList.toggle('d-none', checks.length === 0);
                document.querySelectorAll('.bulk-forward-btn').forEach(button => {
                    button.classList.toggle('d-none', !availableActions.has(button.dataset.forwardAction));
                });

                if (bulkCheckAll) {
                    bulkCheckAll.checked = checks.length > 0 && checks.every(check => check.checked);
                    bulkCheckAll.disabled = checks.length === 0;
                }
            }

            function updateOptions(select, rows, placeholder, selectedValue = '') {
                let html = `<option value="">${placeholder}</option>`;
                rows.forEach(row => {
                    const selected = String(selectedValue) === String(row.id) ? 'selected' : '';
                    html += `<option value="${row.id}" ${selected}>${row.name}</option>`;
                });
                select.innerHTML = html;
                select.disabled = rows.length === 0;
            }

            function fetchResults(url = null) {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                const target = url || `${form.action}?${params.toString()}`;

                fetch(target, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        rows.innerHTML = data.rows;
                        pagination.innerHTML = data.pagination;
                        refreshBulkControls();

                        if (!url) {
                            updateOptions(classSelect, data.classes, 'All Classes', classSelect.value);
                            updateOptions(section, data.sections, 'All Sections', section.value);
                            window.history.replaceState({}, '', target);
                        }
                    });
            }

            branch.addEventListener('change', function() {
                classSelect.value = '';
                section.value = '';
                fetchResults();
            });

            classSelect.addEventListener('change', function() {
                section.value = '';
                fetchResults();
            });

            section.addEventListener('change', function() {
                fetchResults();
            });

            status.addEventListener('change', function() {
                fetchResults();
            });

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                fetchResults();
            });

            search.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(fetchResults, 300);
            });

            document.addEventListener('click', function(event) {
                const pageLink = event.target.closest('#paginationLinks a');
                if (!pageLink) return;

                event.preventDefault();
                fetchResults(pageLink.href);
            });

            document.addEventListener('submit', function(event) {
                const form = event.target.closest('.ajax-delete-result');
                if (!form) return;

                event.preventDefault();
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(form)
                }).then(() => fetchResults());
            });

            document.addEventListener('change', function(event) {
                if (event.target.id === 'bulkCheckAll') {
                    document.querySelectorAll('.bulk-result-check').forEach(check => {
                        check.checked = event.target.checked;
                    });
                    refreshBulkControls();
                }

                if (event.target.classList.contains('bulk-result-check')) {
                    refreshBulkControls();
                }
            });

            document.querySelectorAll('.bulk-forward-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('bulkForwardAction').value = this.dataset.forwardAction;
                });
            });

            if (bulkForwardForm) {
                bulkForwardForm.addEventListener('submit', function(event) {
                    const action = document.getElementById('bulkForwardAction').value;
                    document.querySelectorAll('.bulk-result-check').forEach(check => {
                        check.disabled = check.dataset.forwardAction !== action;
                    });

                    if (!document.querySelector('.bulk-result-check:checked:not(:disabled)')) {
                        event.preventDefault();
                        document.querySelectorAll('.bulk-result-check').forEach(check => check.disabled = false);
                        alert('Please select at least one eligible result.');
                    }
                });
            }

            if (bulkForwardForm) {
                refreshBulkControls();
            }
        });
    </script>
@endsection
