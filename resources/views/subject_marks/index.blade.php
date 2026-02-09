@extends('layouts.main')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Subject Marks</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                {{-- <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                            <li class="breadcrumb-item active">Basic Tables</li> --}}
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center p-2">
                        <h4 class="card-title"></h4>
                        <div class="d-flex gap-2">
                            <form  action="">
                                <input class="form-control mb-0" type="search" id="search" name="search"
                                    placeholder="Search Subject">
                            </form>
                            <a href="{{ route('subject-marks.create') }}" type="button"
                                class="btn btn-primary waves-effect waves-light d-flex align-items-center"><i
                                    class="ri-add-line"></i>&nbsp; Create</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table id="sub-table" class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Subject Name</th>
                                            <th>Term One Mark</th>
                                            <th>Term Two Mark</th>
                                            <th class="text-center">Actions</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($subject_marks as $mark)
                                            <tr>
                                                <td>{{ $mark->subject_name }}</td>
                                                <td>{{ $mark->term_one_marks }}</td>
                                                <td>{{ $mark->term_two_marks }}</td>

                                                <!-- Actions side by side -->
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="{{ route('subject-marks.edit', $mark->id) }}"
                                                            class="btn btn-info btn-sm">
                                                            Edit
                                                        </a>

                                                        {{-- <form method="POST"
                                                            action="{{ route('subject-marks.destroy', $mark->id) }}"
                                                            onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-danger btn-sm">Delete</button>
                                                        </form> --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>



                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

        </div> <!-- container-fluid -->
    </div>
        <script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const table = document.getElementById('sub-table');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();

        Array.from(rows).forEach(row => {
            const cells = row.getElementsByTagName('td');
            let match = false;

            Array.from(cells).forEach(cell => {
                if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                }
            });

            if (match) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
