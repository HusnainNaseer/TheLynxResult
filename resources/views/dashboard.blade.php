@extends('layouts.main')
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Dashboard</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Top Cards -->
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Results</p>
                                <h4 class="mb-2">{{ $totalStudents }}</h4>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-primary rounded-3">
                                    <i class="ri-file-list-3-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Current Session</p>
                                <h4 class="mb-2">{{ $currentSession ? $currentSession->title : 'No session yet' }}</h4>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-success rounded-3">
                                    <i class="ri-calendar-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Subjects</p>
                                <h4 class="mb-2">{{ $totalsubjects }}</h4>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-primary rounded-3">
                                    <i class="ri-book-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end top cards -->

            <!-- Latest Results & Branch Progress -->
            <div class="row">
                <!-- Latest Results -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Top Results</h4>
                            <div class="table-responsive">
                                <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            {{-- <th>Serial no</th> --}}
                                            <th>Roll No</th>
                                            <th>Student Name</th>
                                            <th>Class Name</th>
                                            {{-- <th>Section Name</th> --}}
                                            <th>Total Grades</th>
                                            <th>Total Percentage</th>
                                            {{-- <th>Working Session</th>
                                            <th>Total Attendance</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestResults as $result)
                                            <tr>
                                                {{-- <td>{{ $loop->iteration }}</td> --}}
                                                <td>{{ $result->rollno }}</td>
                                                <td>{{ $result->name }}</td>
                                                <td>{{ $result->class }}</td>
                                                {{-- <td>{{ $result->section }}</td> --}}
                                                <td>{{ $result->overall_grade ?? 'N/A' }}</td>
                                                <td>{{ $result->overall_percentage ?? 0 }}%</td>
                                                {{-- <td>{{ $result->session->title ?? 'N/A' }}</td>
                                                <td>{{ $result->attendance ?? 0 }}</td> --}}
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No results found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branch Progress -->
                <!-- Branch Progress -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <!-- Dropdown for session selection -->
                            <form method="GET" action="{{ route('dashboard') }}">
                                <div class="float-end mb-3">
                                    <select class="form-select shadow-none form-select-sm"
                                        onchange="window.location.href='?session_id='+this.value">
                                        @foreach (\App\Models\Session::orderBy('id', 'desc')->get() as $session)
                                            <option value="{{ $session->id }}"
                                                {{ $session->id == $sessionId ? 'selected' : '' }}>
                                                {{ $session->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>

                            <h4 class="card-title mb-4">Branch Progress</h4>

                            @if (isset($sessionResults) && $sessionResults->count() > 0)
                                <p>Total Students in session: {{ $sessionResults->count() }}</p>

                                <div class="mt-2">
                                    @foreach ($gradesPercentage as $grade => $percent)
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2" style="width:30px;">{{ $grade }}</span>
                                            <div class="progress flex-grow-1" style="height: 20px;">
                                                <div class="progress-bar 
                                    @if ($grade == 'A+' || $grade == 'A') bg-success
                                    @elseif($grade == 'B') bg-info
                                    @elseif($grade == 'C') bg-warning
                                    @else bg-danger @endif"
                                                    role="progressbar" style="width: {{ $percent }}%;"
                                                    aria-valuenow="{{ $percent }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $percent }}%
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    <div id="donut-chart" class="apex-charts"></div>
                                </div>
                            @else
                                <p class="text-center text-muted mt-4">No students registered in this session.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <!-- end row -->

        </div>
    </div>

    <!-- ApexCharts for Branch Progress -->
    <script src="{{ asset('assets/auth/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [
                    @foreach ($gradesPercentage as $grade => $percent)
                        {{ $percent }},
                    @endforeach
                ],
                chart: {
                    height: 280,
                    type: 'donut',
                },
                labels: [
                    @foreach ($gradesPercentage as $grade => $percent)
                        '{{ $grade }}',
                    @endforeach
                ],
                colors: ['#0acf97', '#34c38f', '#50a5f1', '#f1b44c', '#f46a6a', '#556ee6', '#74788d'],
                legend: {
                    position: 'bottom'
                },
            };

            var chart = new ApexCharts(document.querySelector("#donut-chart"), options);
            chart.render();
        });
    </script>
@endsection
