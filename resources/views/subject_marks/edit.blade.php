@extends('layouts.main')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div style="display:grid; grid-template-columns:25% 50% 25%; align-items:center;" class="mt-4">
                <div></div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit</h4>
                        <hr>
                        <form action="{{ route('subject-marks.update', $submark->id) }}" method="POST"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('PUT') <!-- this is essential for update -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Subject Name</label>
                                        <input type="text" name="subject_name" value="{{ $submark->subject_name }}"
                                            class="form-control" placeholder="Subject Name" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Term One Mark</label>
                                        <input type="number" name="term_one_marks" value="{{ $submark->term_one_marks }}"
                                            class="form-control" placeholder="Term 1 Marks" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Term Two Mark</label>
                                        <input type="number" name="term_two_marks" value="{{ $submark->term_two_marks }}"
                                            class="form-control" placeholder="Term 2 Marks" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <div>

                </div>
            </div>
        </div>
    </div>
@endsection
