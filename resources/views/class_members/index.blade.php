@extends('layouts.dashboard')

@section('title', 'View Students')

@section('content')

<style>
    .filter-input {
        background: #67976715 !important;
        border: 1px solid #67976740 !important;
    }
    .filter-input:focus {
        border-color: #679767 !important;
        box-shadow: 0 0 6px #67976760 !important;
    }
</style>

<div class="container-fluid mt-3">
    <div class="card shadow-sm border-0 rounded-3">

        <!-- HEADER -->
        <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <h5 class="fw-bold mb-0" style="color:#679767;">
                <i class="fa-solid fa-users"></i> View Registered Students
            </h5>

            <!-- TOTAL STUDENTS -->
            <span id="totalStudentsBox" class="badge bg-secondary p-2 px-3 d-none" style="font-size:14px;">
                Total Students: <b id="totalStudents">0</b>
            </span>
        </div>

        <!-- BODY -->
        <div class="card-body">

            <!-- FILTERS -->
            <div class="row g-3 mb-4">

                <!-- SECTION FILTER -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Section</label>
                    <select id="filter_section_id" class="form-select filter-input">
                        <option value="">-- All Sections --</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->section_id }}">{{ $s->section_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- CLASS FILTER -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Class</label>
                    <select id="filter_class_id" class="form-select filter-input">
                        <option value="">-- Select Class --</option>
                    </select>
                </div>

                <!-- ARM FILTER -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Arm</label>
                    <select id="filter_arm_id" class="form-select filter-input">
                        <option value="">-- Select Arm --</option>
                    </select>
                </div>

                <!-- BUTTON -->
                <div class="col-md-3 d-flex align-items-end">
                    <button id="filterBtn" class="btn btn-success w-100">
                        <i class="fa-solid fa-filter"></i> Filter Results
                    </button>
                </div>

            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table id="studentsTable" class="table table-striped table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Reg Number</th>
                            <th>Full Name</th>
                            <th>Section</th>
                            <th>Class</th>
                            <th>Arm</th>
                            <th>Session</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- jQuery -->
<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>

<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('datatables/dataTables.bootstrap5.min.css') }}">
<script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/dataTables.bootstrap5.min.js') }}"></script>

<script>
$(document).ready(function () {

    // ================================
    // DEPENDENT CLASS DROPDOWN
    // ================================
    $('#filter_section_id').change(function() {
        let id = $(this).val();
        $('#filter_class_id').html('<option>Loading...</option>');
        $('#filter_arm_id').html('<option value="">-- Select Arm --</option>');

        $.get('{{ url("ajax/classes") }}/' + id, function(data) {
            $('#filter_class_id').html('<option value="">-- Select Class --</option>');
            $.each(data, function(i, v){
                $('#filter_class_id').append(`<option value="${v.class_id}">${v.class_name}</option>`);
            });
        });
    });

    // ================================
    // DEPENDENT ARM DROPDOWN
    // ================================
    $('#filter_class_id').change(function() {
        let id = $(this).val();
        $('#filter_arm_id').html('<option>Loading...</option>');

        $.get('{{ url("ajax/arms") }}/' + id, function(data) {
            $('#filter_arm_id').html('<option value="">-- Select Arm --</option>');
            $.each(data, function(i, v){
                $('#filter_arm_id').append(`<option value="${v.arm_id}">${v.arm_name}</option>`);
            });
        });
    });

    // ================================
    // DATATABLE INITIALIZATION
    // ================================
    let table = $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        deferLoading: 0,
        ajax: {
            url: "{{ route('ajax.class_members') }}",
            data: function (d) {
                if (!$('#filterBtn').data('clicked')) return {};

                d.section_id = $('#filter_section_id').val();
                d.class_id   = $('#filter_class_id').val();
                d.arm_id     = $('#filter_arm_id').val();
            },
            dataSrc: function(response) {
                // update total students
                if (response.total_class_students !== undefined) {
                    $("#totalStudents").text(response.total_class_students);
                    $("#totalStudentsBox").removeClass("d-none");
                }
                return response.data;
            }
        },
        columns: [
            { data: 'reg_number' },
            { data: 'full_name' },
            { data: 'section_name' },
            { data: 'class_name' },
            { data: 'arm_name' },
            { data: 'session_name' }
        ]
    });

    // ================================
    // FILTER BUTTON
    // ================================
    $('#filterBtn').click(function () {
        $(this).data('clicked', true);

        let section = $('#filter_section_id').val();
        let classID = $('#filter_class_id').val();
        let arm     = $('#filter_arm_id').val();

        if (!section || !classID || !arm) {
            alert("Please select Section, Class and Arm to filter results.");
            return;
        }

        table.draw();
    });

});
</script>

@endsection
