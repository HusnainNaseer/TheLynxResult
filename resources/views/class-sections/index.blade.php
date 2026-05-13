@extends('layouts.main')
@section('content')

    <style>
        /* ── Grid ───────────────────────────────────────────────────── */
        .cs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.1rem;
        }

        /* ── Card ───────────────────────────────────────────────────── */
        .cs-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: .875rem;
            padding: 1.25rem 1.25rem 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: box-shadow .22s, transform .22s;
        }

        .cs-card:hover {
            box-shadow: 0 6px 24px rgba(64, 81, 137, .13);
            transform: translateY(-3px);
        }

        /* ── Top row: icon + class name ─────────────────────────────── */
        .cs-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: .85rem;
        }

        .cs-icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }

        .cs-class-name {
            font-weight: 700;
            font-size: .9rem;
            color: #212529;
            line-height: 1.3;
            letter-spacing: .01em;
        }

        /* ── Section pills ──────────────────────────────────────────── */
        .cs-sections {
            display: flex;
            flex-wrap: wrap;
            gap: .3rem;
            padding-left: 56px;
            margin-bottom: 1rem;
        }

        .cs-section-pill {
            display: inline-flex;
            align-items: center;
            background: #f0fdf4;
            color: #0ab39c;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: .18rem .6rem;
            font-size: .71rem;
            font-weight: 600;
            white-space: nowrap;
            letter-spacing: .02em;
        }

        .cs-section-pill i {
            font-size: .63rem;
            margin-right: .25rem;
            opacity: .8;
        }

        /* ── Branch footer pinned at bottom ─────────────────────────── */
        .cs-branch-footer {
            margin-top: auto;
            border-top: 1px solid #f0f2f5;
            padding: .65rem 1.25rem;
            margin-left: -1.25rem;
            margin-right: -1.25rem;
            background: #f8f9fc;
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .cs-branch-footer i {
            font-size: .78rem;
            color: #405189;
            flex-shrink: 0;
        }

        .cs-branch-footer span {
            font-size: .7rem;
            font-weight: 600;
            color: #405189;
            letter-spacing: .03em;
            text-transform: uppercase;
            line-height: 1.3;
        }

        /* ── No branch spacer ───────────────────────────────────────── */
        .cs-no-branch {
            padding-bottom: 1.25rem;
        }

        /* ── Empty state ────────────────────────────────────────────── */
        .empty-state {
            grid-column: 1 / -1;
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
        <div class="container-fluid" style="margin-bottom: 20px;">

            {{-- ── Page Title ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Class Sections</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Class Sections</li>
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
                            <i class="ri-layout-column-line me-2 text-primary"></i>
                            Synced Class Sections
                            <span class="badge bg-primary ms-2">{{ $totalInDb }}</span>
                        </h6>
                        <small class="text-muted">
                            {{ $totalInDb > 0 ? 'Data loaded from local database.' : 'No class-sections synced yet. Click the button to import.' }}
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        @if ($totalInDb === 0)
                            <form action="{{ route('class-sections.sync') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-download-cloud-line me-1"></i>Fetch & Save Class Sections
                                </button>
                            </form>
                        @else
                            <form action="{{ route('class-sections.resync') }}" method="POST"
                                onsubmit="return confirm('This will update existing class-section links and add any new ones from ERP. Your local IDs will not change. Continue?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm">
                                    <i class="ri-refresh-line me-1"></i>Re-sync from ERP
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ── Filters (same structure as Classes page) ── --}}
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
                                        <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
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

            {{-- ── Build lookup maps ── --}}
            @php
                $branchMap = collect($branches)->pluck('name', 'id')->toArray();
            @endphp

            {{-- ── Class-Sections Grid ── --}}
            <div class="cs-grid" id="csGrid">

                @forelse($grouped as $classId => $rows)
                    @php
                        $cls        = $rows->first()->class;
                        $branchId   = $cls?->erp_branch_id;
                        $branchName = $branchId ? ($branchMap[$branchId] ?? 'Branch #' . $branchId) : null;
                    @endphp

                    <div class="cs-card"
                         data-name="{{ strtolower($cls?->name ?? '') }}"
                         data-branch="{{ $branchId }}">

                        {{-- Header: icon + class name --}}
                        <div class="cs-card-header">
                            <div class="cs-icon">
                                <i class="ri-book-open-line"></i>
                            </div>
                            <div class="cs-class-name">{{ $cls?->name ?? 'Class #' . $classId }}</div>
                        </div>

                        {{-- Section pills --}}
                        @php $validSections = $rows->filter(fn($r) => $r->section !== null); @endphp
                        @if ($validSections->isNotEmpty())
                            <div class="cs-sections">
                                @foreach ($validSections as $row)
                                    <span class="cs-section-pill">
                                        <i class="ri-layout-grid-line"></i>
                                        {{ $row->section->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Branch footer pinned at bottom --}}
                        @if ($branchName)
                            <div class="cs-branch-footer">
                                <i class="ri-building-2-line"></i>
                                <span>{{ $branchName }}</span>
                            </div>
                        @else
                            <div class="cs-no-branch"></div>
                        @endif

                    </div>

                @empty
                    <div class="empty-state">
                        <i class="ri-inbox-2-line text-muted"></i>
                        <p class="mb-2 fw-semibold">No class sections yet</p>
                        <p class="mb-0 small">
                            Click <strong>"Fetch & Save Class Sections"</strong> above to import from ERP.
                        </p>
                    </div>
                @endforelse

            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const grid      = document.getElementById('csGrid');
            const branchSel = document.getElementById('branchFilter');
            const searchIn  = document.getElementById('searchInput');
            const clearBtn  = document.getElementById('clearFilters');
            const countEl   = document.getElementById('filteredCount');

            // Nothing to filter when DB is empty
            if (!branchSel) return;

            const cards = Array.from(grid.querySelectorAll('.cs-card'));

            function filterCards() {
                const branch = branchSel.value.trim();
                const search = searchIn.value.toLowerCase().trim();
                let visible  = 0;

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

                if (countEl) countEl.textContent = `Showing ${visible} of ${cards.length}`;
            }

            branchSel.addEventListener('change', filterCards);
            searchIn.addEventListener('input',   filterCards);

            clearBtn.addEventListener('click', function () {
                branchSel.value = '';
                searchIn.value  = '';
                filterCards();
            });

            // Run on load to populate count
            filterCards();
        });
    </script>

@endsection