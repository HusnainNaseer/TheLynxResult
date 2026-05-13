@extends('layouts.main')
@section('content')

    <style>
        .sections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
        }

        .section-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: .75rem;
            padding: 1.25rem;
            display: flex;
            gap: .4rem;
            transition: box-shadow .2s, transform .2s;
            align-items: center;
        }

        .section-card:hover {
            box-shadow: 0 4px 16px rgba(64, 81, 137, .12);
            transform: translateY(-2px);
        }

        .section-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0ab39c 0%, #405189 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }

        .section-name {
            font-weight: 600;
            font-size: .95rem;
            color: #212529;
        }

        .section-meta {
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

        .info-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 20px;
            padding: .15rem .6rem;
            font-size: .72rem;
            font-weight: 500;
            margin-top: .1rem;
        }

        .branch-badge {
            background: #eef2ff;
            color: #405189;
            border: 1px solid #c7d2fe;
        }

        .class-badge {
            background: #f0fdf4;
            color: #0ab39c;
            border: 1px solid #bbf7d0;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">

            {{-- Page Title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Sections</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Sections</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alerts --}}
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

            {{-- Toolbar --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="ri-layout-grid-line me-2 text-primary"></i>
                            Synced Sections
                            <span class="badge bg-primary ms-2">{{ $totalInDb }}</span>
                        </h6>
                        <small class="text-muted">
                            {{ $totalInDb > 0 ? 'Data loaded from local database.' : 'No sections synced yet. Click the button to import.' }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($totalInDb === 0)
                            <form action="{{ route('sections.sync') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-download-cloud-line me-1"></i>Fetch & Save Sections
                                </button>
                            </form>
                        @else
                            <form action="{{ route('sections.resync') }}" method="POST"
                                onsubmit="return confirm('This will update existing sections and add any new ones from ERP. Your local IDs will not change. Continue?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm">
                                    <i class="ri-refresh-line me-1"></i>Re-sync from ERP
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            @if ($totalInDb > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-end g-3">

                            {{-- Branch filter --}}
                            {{-- <div class="col-md-3">
                                <label class="form-label fw-medium mb-1">
                                    <i class="ri-building-2-line me-1 text-primary"></i>Branch
                                </label>
                                <select class="form-select" id="branchFilter">
                                    <option value="">-- All Branches --</option>
                                    @foreach ($branches as $b)
                                        <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                                    @endforeach
                                </select>
                            </div> --}}

                            {{-- Class filter — populated by JS on branch change --}}
                            {{-- <div class="col-md-3">
                                <label class="form-label fw-medium mb-1">
                                    <i class="ri-book-open-line me-1 text-primary"></i>Class
                                </label>
                                <select class="form-select" id="classFilter" disabled>
                                    <option value="">-- Select Branch First --</option>
                                </select>
                            </div> --}}

                            {{-- Search --}}
                            <div class="col-md-3">
                                <label class="form-label fw-medium mb-1">
                                    <i class="ri-search-line me-1 text-primary"></i>Search
                                </label>
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search section name…">
                            </div>

                            {{-- Clear + count --}}
                            {{-- <div class="col-md-3 d-flex align-items-end gap-3">
                                <button class="btn btn-outline-secondary btn-sm" id="clearFilters">
                                    <i class="ri-close-line me-1"></i>Clear
                                </button>
                                <span class="text-muted small" id="filteredCount"></span>
                            </div> --}}

                        </div>
                    </div>
                </div>
            @endif

            {{-- Build lookup maps --}}
            @php
                $branchMap = collect($branches)->pluck('name', 'id')->toArray();
                $classMap = \App\Models\Classes::pluck('name', 'erp_class_id')->toArray();
                $allSections = \App\Models\Section::orderBy('name')->get();
            @endphp

            {{-- Sections Grid --}}
            <div class="sections-grid" id="sectionsGrid">

                @forelse($allSections as $section)
                    <div class="section-card" data-name="{{ strtolower($section->name) }}"
                        data-branch="{{ (string) $section->erp_branch_id }}"
                        data-class="{{ (string) $section->class_id }}">

                        <div class="section-icon">
                            <i class="ri-layout-grid-line"></i>
                        </div>

                        <div class="section-name">{{ $section->name }}</div>

                        {{-- <div class="section-meta">
                            <i class="ri-fingerprint-line me-1"></i>
                            ERP ID: {{ $section->erp_section_id }}
                        </div> --}}

                        {{-- Class badge --}}
                        @if ($section->class_id)
                            <div>
                                <span class="info-badge class-badge">
                                    <i class="ri-book-open-line me-1"></i>
                                    {{ $classMap[$section->class_id] ?? 'Class #' . $section->class_id }}
                                </span>
                            </div>
                        @endif

                        {{-- Branch badge --}}
                        {{-- @if ($section->erp_branch_id)
                            <div>
                                <span class="info-badge branch-badge">
                                    <i class="ri-building-2-line me-1"></i>
                                    {{ $branchMap[$section->erp_branch_id] ?? 'Branch #' . $section->name }}
                                </span>
                            </div>
                        @endif --}}

                    </div>
                @empty
                    <div class="empty-state">
                        <i class="ri-inbox-2-line text-muted"></i>
                        <p class="mb-2 fw-semibold">No sections yet</p>
                        <p class="mb-0 small">
                            Click <strong>"Fetch & Save Sections"</strong> above to import from ERP.
                        </p>
                    </div>
                @endforelse

            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const grid = document.getElementById('sectionsGrid');
            const branchSel = document.getElementById('branchFilter');
            const classSel = document.getElementById('classFilter');
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearFilters');
            const countEl = document.getElementById('filteredCount');

            // if (!branchSel) return;

            // Classes grouped by branch — injected from PHP
            const classesByBranch = @json($classesByBranch);

            const cards = Array.from(grid.querySelectorAll('.section-card'));

            function filterCards() {
                console.log('error');
                
                const search = searchInput.value.toLowerCase().trim();

                let visible = 0;

                cards.forEach(card => {
                    const cardName = String(card.dataset.name).trim();
                    const matchSearch = !search || cardName.includes(search);

                    if (matchSearch) {
                        card.style.display = '';
                        visible++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                countEl.textContent = `Showing ${visible} of ${cards.length}`;
            }

            searchInput.addEventListener('input', filterCards);

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterCards();
            });

            // init count
            filterCards();
        });
    </script>

@endsection
