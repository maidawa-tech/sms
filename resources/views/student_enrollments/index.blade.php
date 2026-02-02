@extends('layouts.dashboard')

@section('title', 'Student Enrollment')

@section('content')

<style>
    .enroll-input {
        background: #67976715 !important; /* lighter green */
        border: 1px solid #67976740 !important;
        color: #333 !important;
    }
    .enroll-input:focus {
        border-color: #679767 !important;
        box-shadow: 0 0 6px #67976760 !important;
    }
</style>

<div class="container-fluid mt-3">

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold" style="color: #679767;">
             <i class="fa-solid fa-user-graduate"></i> Enroll a Student
            </h5>
        </div>

        <div class="card-body">

            <form id="enrollmentForm">
                @csrf

                {{-- REG NUMBER --}}
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Student Reg Number</label>
                    <div class="col-sm-9">
                        <input type="text" name="reg_number" class="form-control enroll-input" placeholder="Enter REG Number">
                    </div>
                </div>

                {{-- SECTION --}}
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Section</label>
                    <div class="col-sm-9">
                        <select name="section_id" id="section_id" class="form-select enroll-input">
                            <option value="">-- Select Section --</option>
                            @foreach($sections as $s)
                                <option value="{{ $s->section_id }}">{{ $s->section_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- CLASS --}}
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Class</label>
                    <div class="col-sm-9">
                        <select name="class_id" id="class_id" class="form-select enroll-input">
                            <option value="">-- Select Class --</option>
                        </select>
                    </div>
                </div>

                {{-- ARM --}}
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Arm</label>
                    <div class="col-sm-9">
                        <select name="arm_id" id="arm_id" class="form-select enroll-input">
                            <option value="">-- Select Arm --</option>
                        </select>
                    </div>
                </div>

                {{-- ACADEMIC SESSION --}}
                <div class="row mb-4">
                    <label class="col-sm-3 col-form-label">Academic Session</label>
                    <div class="col-sm-9">
                        <select name="session_id" class="form-select enroll-input">
                            <option value="">-- Select Academic Session --</option>
                            @foreach($sessions as $sess)
                                <option value="{{ $sess->session_id }}">{{ $sess->session_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4" id="enrollBtn">
                        Enroll Student
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

{{-- Load jQuery --}}
<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>

{{-- AJAX SCRIPT --}}
<script>
$(document).ready(function () {

    // CSRF Token for all AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // URL for AJAX enrollment
    const ENROLL_STUDENT_URL = "{{ route('ajax.enrollStudent') }}";

    // Load classes based on section
    $('#section_id').change(function () {
        let sectionId = $(this).val();
        $('#class_id').html('<option>Loading...</option>');

        if (sectionId) {
            $.get('{{ url("ajax/classes") }}/' + sectionId, function (data) {
                $('#class_id').html('<option value="">-- Select Class --</option>');
                $.each(data, function (i, v) {
                    $('#class_id').append(`<option value="${v.class_id}">${v.class_name}</option>`);
                });
                $('#arm_id').html('<option value="">-- Select Arm --</option>');
            });
        } else {
            $('#class_id').html('<option value="">-- Select Class --</option>');
            $('#arm_id').html('<option value="">-- Select Arm --</option>');
        }
    });

    // Load arms based on class
    $('#class_id').change(function () {
        let classId = $(this).val();
        $('#arm_id').html('<option>Loading...</option>');

        if (classId) {
            $.get('{{ url("ajax/arms") }}/' + classId, function (data) {
                $('#arm_id').html('<option value="">-- Select Arm --</option>');
                $.each(data, function (i, v) {
                    $('#arm_id').append(`<option value="${v.arm_id}">${v.arm_name}</option>`);
                });
            });
        } else {
            $('#arm_id').html('<option value="">-- Select Arm --</option>');
        }
    });

    // Submit form via AJAX
    $('#enrollmentForm').submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: ENROLL_STUDENT_URL,
            type: "POST",
            data: $(this).serialize(),

            success: function(res) {
                showToast(res.message, 'success');
                $('#enrollmentForm')[0].reset();
                $('#class_id').html('<option value="">-- Select Class --</option>');
                $('#arm_id').html('<option value="">-- Select Arm --</option>');
            },

            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    showToast(xhr.responseJSON.message, 'error');
                } else {
                    showToast('Error! Check inputs.', 'error');
                }
            }
        });
    });

    // Function to show Bootstrap 5 toast
    function showToast(message, type = 'info') {
        let bgClass = 'bg-info';
        if (type === 'success') bgClass = 'bg-success';
        if (type === 'error') bgClass = 'bg-danger';
        if (type === 'warning') bgClass = 'bg-warning';

        let toastHTML = `
        <div class="toast align-items-center text-white ${bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;

        let $toastContainer = $('.toast-container');
        if ($toastContainer.length === 0) {
            $toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>');
            $('body').append($toastContainer);
        }

        let $toast = $(toastHTML);
        $toastContainer.append($toast);

        let toastEl = $toast[0];
        let bsToast = new bootstrap.Toast(toastEl, { delay: 5000 });
        bsToast.show();

        // Remove toast from DOM after hidden
        $toast.on('hidden.bs.toast', function () {
            $(this).remove();
        });
    }

});
</script>

@endsection
