@extends('layouts.main')
@section('content')

    <style>
        .branch-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .branch-summary {
            display: flex;
            align-items: center;
            gap: .85rem;
        }

        .branch-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            flex: 0 0 auto;
        }

        .branch-table th {
            font-size: .75rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0;
        }

        .branch-table td {
            vertical-align: middle;
        }

        .branch-name {
            font-weight: 600;
            color: #212529;
        }

        .branch-meta {
            font-size: .78rem;
            color: #6c757d;
        }

        .branch-address {
            max-width: 360px;
            white-space: normal;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: .75rem;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Branches</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Branches</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-body branch-toolbar">
                    <div class="branch-summary">
                        <div class="branch-icon">
                            <i class="ri-building-2-line"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">
                                Synced Branches
                                <span class="badge bg-primary ms-2">{{ $totalInDb }}</span>
                            </h6>
                            <small class="text-muted">
                                {{ $totalInDb > 0 ? $activeCount . ' active branch(es) in local database.' : 'No branches synced yet. Click the button to import.' }}
                            </small>
                        </div>
                    </div>

                    @can('sync branches')
                        <form action="{{ $totalInDb === 0 ? route('branches.sync') : route('branches.resync') }}" method="POST"
                            @if ($totalInDb > 0) onsubmit="return confirm('This will update existing branches and add any new ones from ERP. Continue?')" @endif>
                            @csrf
                            <button type="submit" class="{{ $totalInDb === 0 ? 'btn btn-primary' : 'btn btn-outline-warning btn-sm' }}">
                                <i class="{{ $totalInDb === 0 ? 'ri-download-cloud-line' : 'ri-refresh-line' }} me-1"></i>
                                {{ $totalInDb === 0 ? 'Fetch & Save Branches' : 'Re-sync from ERP' }}
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            @if ($totalInDb > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-end g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium mb-1">
                                    <i class="ri-search-line me-1 text-primary"></i>Search
                                </label>
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search branch name, email, phone, or address">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium mb-1">
                                    <i class="ri-toggle-line me-1 text-primary"></i>Status
                                </label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Branches</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-5 d-flex align-items-end gap-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFilters">
                                    <i class="ri-close-line me-1"></i>Clear
                                </button>
                                <span class="text-muted small" id="filteredCount"></span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    @if ($branches->isEmpty())
                        <div class="empty-state">
                            <i class="ri-inbox-2-line text-muted"></i>
                            <p class="mb-2 fw-semibold">No branches yet</p>
                            <p class="mb-0 small">
                                Click <strong>"Fetch & Save Branches"</strong> above to import from ERP.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 branch-table">
                                <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Updated</th>
                                    </tr>
                                </thead>
                                <tbody id="branchesTable">
                                    @foreach ($branches as $branch)
                                        <tr data-search="{{ strtolower($branch->name . ' ' . $branch->email . ' ' . $branch->phone . ' ' . $branch->address) }}"
                                            data-status="{{ $branch->is_active ? 'active' : 'inactive' }}">
                                            <td>
                                                <div class="branch-name">{{ $branch->name }}</div>
                                                <div class="branch-meta">ERP ID: {{ $branch->erp_branch_id }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $branch->email ?: '-' }}</div>
                                                <div class="branch-meta">{{ $branch->phone ?: '-' }}</div>
                                            </td>
                                            <td class="branch-address">{{ $branch->address ?: '-' }}</td>
                                            <td>
                                                @if ($branch->is_active)
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="branch-meta">
                                                {{ $branch->updated_at?->format('d M Y, h:i A') ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('branchesTable');
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const clearBtn = document.getElementById('clearFilters');
            const countEl = document.getElementById('filteredCount');

            if (!table || !searchInput || !statusFilter) return;

            const rows = Array.from(table.querySelectorAll('tr'));

            function filterRows() {
                const search = searchInput.value.toLowerCase().trim();
                const status = statusFilter.value;
                let visible = 0;

                rows.forEach(row => {
                    const matchSearch = !search || row.dataset.search.includes(search);
                    const matchStatus = !status || row.dataset.status === status;
                    const shouldShow = matchSearch && matchStatus;

                    row.style.display = shouldShow ? '' : 'none';
                    if (shouldShow) visible++;
                });

                countEl.textContent = `Showing ${visible} of ${rows.length}`;
            }

            searchInput.addEventListener('input', filterRows);
            statusFilter.addEventListener('change', filterRows);
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                statusFilter.value = '';
                filterRows();
            });

            filterRows();
        });
    </script>

@endsection
