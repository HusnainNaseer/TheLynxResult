<div class="vertical-menu">
    <div data-simplebar class="h-100">
        @php
            $setupActive = request()->routeIs(
                'sessions.*',
                'branches.*',
                'classes.*',
                'sections.*',
                'class-sections.*',
                'class-subjects.*',
                'subject-marks.*',
                'assign-subjects.*'
            );

            $resultsActive = request()->routeIs(
                'students.result',
                'results.*',
                'student_result.*'
            );
        @endphp

        <!-- User details -->
        <div class="user-profile text-center mt-3">
            <div class="">
                <img src="{{ Auth::user()->display_picture_url }}"
                    alt="{{ Auth::user()->name }}" class="avatar-md rounded-circle"
                    onerror="this.src='{{ asset('assets/auth/images/users/avatar.png') }}'">
            </div>
            <div class="mt-3">
                <h4 class="font-size-16 mb-1">{{ Auth::user()->name }}</h4>
                <span class="text-muted">
                    <i class="ri-record-circle-line align-middle font-size-14 text-success"></i>
                    {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                </span>
            </div>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                {{-- <li class="menu-title">Menu</li> --}}

                <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="waves-effect {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ri-dashboard-line"></i>
                        {{-- <span class="badge rounded-pill bg-success float-end">3</span> --}}
                        <span>Dashboard</span>
                    </a>
                </li>
                @hasanyrole('Admin|Coordinator')
                    <li class="{{ request()->routeIs('teachers.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('teachers.index') }}" class="waves-effect {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                            <i class="ri-user-line"></i>
                            {{-- <span class="badge rounded-pill bg-success float-end">3</span> --}}
                            <span>Employees List</span>
                        </a>
                    </li>
                @endhasanyrole

                @hasanyrole('Admin|Coordinator')
                    <li class="{{ $setupActive ? 'mm-active' : '' }}">
                        <a href="javascript:void(0);" class="has-arrow waves-effect {{ $setupActive ? 'active' : '' }}">
                            <i class="ri-briefcase-line"></i>
                            <span>Setup</span>
                        </a>
                        <ul class="sub-menu {{ $setupActive ? 'mm-show' : '' }}" aria-expanded="{{ $setupActive ? 'true' : 'false' }}">
                            @role('Admin')
                            <li class="{{ request()->routeIs('sessions.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('sessions.index') }}" class="{{ request()->routeIs('sessions.*') ? 'active' : '' }}">Sessions</a>
                            </li>

                            @can('view branches')
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('branches.*') ? 'active' : '' }}"
                                    href="{{ route('branches.index') }}">
                                    <span data-key="t-branches">All Branches</span>
                                </a>
                            </li>
                            @endcan
                            @endrole

                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"
                                    href="{{ route('classes.index') }}">
                                  
                                    <span data-key="t-assign-subjects">All Classes</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('sections.*') ? 'active' : '' }}"
                                    href="{{ route('sections.index') }}">
                                  
                                    <span data-key="t-assign-subjects">All Sections</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('class-sections.*') ? 'active' : '' }}"
                                    href="{{ route('class-sections.index') }}">
                                  
                                    <span data-key="t-assign-subjects">Class and Sections</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('class-subjects.*') ? 'active' : '' }}"
                                    href="{{ route('class-subjects.index') }}">
                                  
                                    <span data-key="t-assign-subjects">Class and Subjects</span>
                                </a>
                            </li>

                            <li class="{{ request()->routeIs('subject-marks.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('subject-marks.index') }}" class="{{ request()->routeIs('subject-marks.*') ? 'active' : '' }}">Subject Marks</a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('assign-subjects.*') ? 'active' : '' }}"
                                    href="{{ route('assign-subjects.index') }}">
                                  
                                    <span data-key="t-assign-subjects">Assign Subjects</span>
                                </a>
                            </li>
                            
                            
                            {{-- Add more setup links here --}}
                        </ul>
                    </li>
                @endhasanyrole

                <li class="{{ $resultsActive ? 'mm-active' : '' }}">
                    <a href="javascript:void(0);" class="has-arrow waves-effect {{ $resultsActive ? 'active' : '' }}">
                        <i class="ri-file-list-3-line"></i>
                        <span>Student Result</span>
                    </a>
                    <ul class="sub-menu {{ $resultsActive ? 'mm-show' : '' }}" aria-expanded="{{ $resultsActive ? 'true' : 'false' }}">
                        <li class="{{ request()->routeIs('students.result') ? 'mm-active' : '' }}">
                            <a href="{{ route('students.result') }}" class="{{ request()->routeIs('students.result') ? 'active' : '' }}">Result List</a>
                        </li>
                        @hasanyrole('Admin|Coordinator')
                        <li class="{{ request()->routeIs('results.coordinator-approvals') ? 'mm-active' : '' }}">
                            <a href="{{ route('results.coordinator-approvals') }}" class="{{ request()->routeIs('results.coordinator-approvals') ? 'active' : '' }}">Coordinator Approval List</a>
                        </li>
                        <li class="{{ request()->routeIs('results.approved') ? 'mm-active' : '' }}">
                            <a href="{{ route('results.approved') }}" class="{{ request()->routeIs('results.approved') ? 'active' : '' }}">Approved Results</a>
                        </li>
                        @endhasanyrole
                        <li class="{{ request()->routeIs('results.create') ? 'mm-active' : '' }}">
                            <a href="{{ route('results.create') }}" class="{{ request()->routeIs('results.create') ? 'active' : '' }}">Create Result</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
