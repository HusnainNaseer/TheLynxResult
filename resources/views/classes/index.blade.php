@extends('layouts.main')
@section('content')

    <style>
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
        }

        .class-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: .75rem;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: .4rem;
            transition: box-shadow .2s, transform .2s;
        }

        .class-card:hover {
            box-shadow: 0 4px 16px rgba(64, 81, 137, .12);
            transform: translateY(-2px);
        }

        .class-card-1 {
            display: flex !important;
            align-items: center !important;
            gap: 20px !important;
        }

        .class-card-1:hover {
            box-shadow: 0 4px 16px rgba(64, 81, 137, .12);
            transform: translateY(-2px);
        }

        .class-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: .25rem;
        }

        .class-name {
            font-weight: 600;
            font-size: .95rem;
            color: #212529;
        }

        .class-meta {
            font-size: .78rem;
            color: #6c757d;
        }

        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: .75rem;
        }

        .branch-badge {
            display: inline-flex;
            align-items: center;
            background: #eef2ff;
            color: #405189;
            border: 1px solid #c7d2fe;
            border-radius: 20px;
            padding: .15rem .6rem;
            font-size: .58rem;
            font-weight: 500;
            margin-top: .25rem;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid" style="margin-bottom: 20px;">

            {{-- ── Page Title ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Classes</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Classes</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Alerts ── --}}
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

            {{-- ── Toolbar ── --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">

                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="ri-book-open-line me-2 text-primary"></i>
                            Synced Classes
                            <span class="badge bg-primary ms-2">{{ $totalInDb }}</span>
                        </h6>
                        <small class="text-muted">
                            {{ $totalInDb > 0 ? 'Data loaded from local database.' : 'No classes synced yet. Click the button to import.' }}
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        @if ($totalInDb === 0)
                            <form action="{{ route('classes.sync') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-download-cloud-line me-1"></i>Fetch & Save Classes
                                </button>
                            </form>
                        @else
                            <form action="{{ route('classes.resync') }}" method="POST"
                                onsubmit="return confirm('This will update existing classes and add any new ones from ERP. Your local IDs will not change. Continue?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm">
                                    <i class="ri-refresh-line me-1"></i>Re-sync from ERP
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ── Filters ── --}}
            @if ($totalInDb > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-end g-3">

                            {{-- Branch filter --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium mb-1">
                                    <i class="ri-building-2-line me-1 text-primary"></i>Filter by Branch
                                </label>
                                <select class="form-select" id="branchFilter">
                                    <option value=""> All Branches </option>
                                    @foreach ($branches as $b)
                                        <option value="{{ $b['id'] }}">
                                            {{ $b['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search --}}
                            <div class="col-md-4">
                                <label class="form-label fw-medium mb-1">
                                    <i class="ri-search-line me-1 text-primary"></i>Search
                                </label>
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search class name…">
                            </div>

                            {{-- Clear + count --}}
                            <div class="col-md-4 d-flex align-items-end gap-3">
                                <button class="btn btn-outline-secondary btn-sm" id="clearFilters">
                                    <i class="ri-close-line me-1"></i>Clear
                                </button>
                                <span class="text-muted small" id="filteredCount"></span>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Classes Grid ── --}}
            @php
                $branchMap = collect($branches)->pluck('name', 'id')->toArray();
                $allClasses = \App\Models\Classes::orderBy('name')->get();
            @endphp

            <div class="classes-grid" id="classesGrid">

                @forelse($allClasses as $class)
                    <div class="class-card" data-name="{{ strtolower($class->name) }}"
                        data-branch="{{ $class->erp_branch_id }}">
                        <div class="class-card-1">
                            <div class="class-icon">
                                <i class="ri-book-open-line"></i>
                            </div>

                            <div class="class-name">{{ $class->name }}</div>
                        </div>

                        {{-- <div class="class-meta">
                    <i class="ri-fingerprint-line me-1"></i>
                    ERP ID: {{ $class->erp_class_id }}
                </div> --}}

                        @if ($class->erp_branch_id)
                            <div>
                                <span class="branch-badge">
                                    {{-- <i class="ri-building-2-line me-1"></i> --}}
                                    {{ $branchMap[$class->erp_branch_id] ?? 'Branch #' . $class->erp_branch_id }}
                                </span>
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="empty-state">
                        <i class="ri-inbox-2-line text-muted"></i>
                        <p class="mb-2 fw-semibold">No classes yet</p>
                        <p class="mb-0 small">
                            Click <strong>"Fetch & Save Classes"</strong> above to import from ERP.
                        </p>
                    </div>
                @endforelse

            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const grid = document.getElementById('classesGrid');
            const branchSel = document.getElementById('branchFilter');
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearFilters');
            const countEl = document.getElementById('filteredCount');

            // nothing to filter if no data
            if (!branchSel) return;

            const cards = Array.from(grid.querySelectorAll('.class-card'));

            function filterCards() {
                const branch = branchSel.value.trim();
                const search = searchInput.value.toLowerCase().trim();

                let visible = 0;

                cards.forEach(card => {
                    const matchBranch = !branch || card.dataset.branch === branch;
                    const matchSearch = !search || card.dataset.name.includes(search);

                    if (matchBranch && matchSearch) {
                        card.style.display = '';
                        visible++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (countEl) {
                    countEl.textContent = `Showing ${visible} of ${cards.length}`;
                }
            }

            branchSel.addEventListener('change', filterCards);
            searchInput.addEventListener('input', filterCards);

            clearBtn.addEventListener('click', function() {
                branchSel.value = '';
                searchInput.value = '';
                filterCards();
            });

            // run on load
            filterCards();
        });
    </script>

@endsection
