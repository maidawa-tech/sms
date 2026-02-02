@extends('layouts.dashboard')

@section('title', 'Move Enrolled Students')

@section('content')
<style>
/* Common style for all dropdowns */
.filter-input {
    background: #67976715 !important;
    border: 1px solid #67976740 !important;
    height: 38px;
}
.filter-input:focus {
    border-color: #679767 !important;
    box-shadow: 0 0 6px #67976760 !important;
}

/* Horizontal filter + move panel */
.horizontal-panel {
    margin-top: 1rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: flex-end;
}
.horizontal-panel select,
.horizontal-panel button {
    margin: 0 !important;
    width: auto;
    min-width: 150px;
}

/* Responsive stacking */
@media (max-width: 767px) {
    .horizontal-panel select,
    .horizontal-panel button {
        width: 100% !important;
    }
}
</style>

<div class="container-fluid mt-3">

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3"></div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color:#679767;">
                <i class="fa-solid fa-arrows-right-left"></i> Move Enrolled Students
            </h5>
        </div>

        <div class="card-body">

            <!-- FILTER PANEL (Top) -->
            <div class="horizontal-panel">

                <select id="filter_section_id" class="form-select filter-input">
                    <option value="">-- All Sections --</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->section_id }}">{{ $s->section_name }}</option>
                    @endforeach
                </select>

                <select id="filter_class_id" class="form-select filter-input">
                    <option value="">-- Select Class --</option>
                </select>

                <select id="filter_arm_id" class="form-select filter-input">
                    <option value="">-- Select Arm --</option>
                </select>

                <select id="filter_session_id" class="form-select filter-input">
                    <option value="">-- Select Session --</option>
                    @foreach($sessions as $sess)
                        <option value="{{ $sess->session_id }}">{{ $sess->session_name }}</option>
                    @endforeach
                </select>

                <button id="loadStudentsBtn" class="btn btn-success">
                    <i class="fa-solid fa-eye"></i> Load Students
                </button>

            </div>

            <!-- STUDENTS TABLE -->
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered" id="moveStudentsTable">
                    <thead class="table-success">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
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

            <!-- MOVE PANEL (Bottom) -->
            <div class="horizontal-panel mt-3">

                <select id="move_section_id" class="form-select filter-input">
                    <option value="">-- Select Section --</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->section_id }}">{{ $s->section_name }}</option>
                    @endforeach
                </select>

                <select id="move_class_id" class="form-select filter-input">
                    <option value="">-- Select Class --</option>
                </select>

                <select id="move_arm_id" class="form-select filter-input">
                    <option value="">-- Select Arm --</option>
                </select>

                <select id="move_session_id" class="form-select filter-input">
                    <option value="">-- Select Session --</option>
                    @foreach($sessions as $sess)
                        <option value="{{ $sess->session_id }}">{{ $sess->session_name }}</option>
                    @endforeach
                </select>

                <button id="moveBtn" class="btn btn-primary" style="white-space:nowrap;">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i> Move Selected Students
                </button>

            </div>

        </div>
    </div>
</div>

<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
@endsection

@section('scripts')
<script>
$(function() {

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    /* --------------------------
       DEPENDENT DROPDOWN (FILTER SIDE)
    ---------------------------*/
    $('#filter_section_id').change(function() {
        let id = $(this).val();
        $('#filter_class_id').html('<option>Loading...</option>');
        $('#filter_arm_id').html('<option value="">-- Select Arm --</option>');

        if(!id){
            $('#filter_class_id').html('<option value="">-- Select Class --</option>');
            return;
        }

        $.get(`/move-students/get-classes/${id}`, function(data) {
            $('#filter_class_id').html('<option value="">-- Select Class --</option>');
            $.each(data, (i,v)=> $('#filter_class_id').append(`<option value="${v.class_id}">${v.class_name}</option>`));
        });
    });

    $('#filter_class_id').change(function() {
        let id = $(this).val();
        $('#filter_arm_id').html('<option>Loading...</option>');

        if(!id){
            $('#filter_arm_id').html('<option value="">-- Select Arm --</option>');
            return;
        }

        $.get(`/move-students/get-arms/${id}`, function(data) {
            $('#filter_arm_id').html('<option value="">-- Select Arm --</option>');
            $.each(data,(i,v)=> $('#filter_arm_id').append(`<option value="${v.arm_id}">${v.arm_name}</option>`));
        });
    });

    /* --------------------------
       DEPENDENT DROPDOWN (MOVE SIDE)
    ---------------------------*/
    $('#move_section_id').change(function() {
        let id = $(this).val();
        $('#move_class_id').html('<option>Loading...</option>');
        $('#move_arm_id').html('<option value="">-- Select Arm --</option>');

        if(!id){
            $('#move_class_id').html('<option value="">-- Select Class --</option>');
            return;
        }

        $.get(`/move-students/get-classes/${id}`, function(data) {
            $('#move_class_id').html('<option value="">-- Select Class --</option>');
            $.each(data,(i,v)=> $('#move_class_id').append(`<option value="${v.class_id}">${v.class_name}</option>`));
        });
    });

    $('#move_class_id').change(function() {
        let id = $(this).val();
        $('#move_arm_id').html('<option>Loading...</option>');

        if(!id){
            $('#move_arm_id').html('<option value="">-- Select Arm --</option>');
            return;
        }

        $.get(`/move-students/get-arms/${id}`, function(data) {
            $('#move_arm_id').html('<option value="">-- Select Arm --</option>');
            $.each(data,(i,v)=> $('#move_arm_id').append(`<option value="${v.arm_id}">${v.arm_name}</option>`));
        });
    });

    /* --------------------------
       LOAD STUDENTS
    ---------------------------*/
    $('#loadStudentsBtn').click(function() {

        let section = $('#filter_section_id').val();
        let classID = $('#filter_class_id').val();
        let arm = $('#filter_arm_id').val();
        let session = $('#filter_session_id').val();

        if(!session){
            showToast('warning','Please select an academic session.');
            return;
        }

        $.get('/move-students/load-students', {
            section_id: section,
            class_id: classID,
            arm_id: arm,
            session_id: session
        }, function(res) {

            let tbody = '';

            if(res.data.length === 0){
                tbody = `<tr><td colspan="7" class="text-center text-danger">No students found.</td></tr>`;
            } else {
                $.each(res.data, function(i,v){
                    tbody += `
                    <tr>
                        <td><input type="checkbox" class="select-student" value="${v.reg_number}"></td>
                        <td>${v.reg_number}</td>
                        <td>${v.full_name}</td>
                        <td>${v.section_name}</td>
                        <td>${v.class_name}</td>
                        <td>${v.arm_name}</td>
                        <td>${v.session_name}</td>
                    </tr>`;
                });
            }

            $('#moveStudentsTable tbody').html(tbody);
            $('#selectAll').prop('checked', false);

        }).fail(()=> showToast('danger','Failed to load students'));
    });

    /* --------------------------
       SELECT ALL CHECKBOX
    ---------------------------*/
    $('#selectAll').on('change', function() {
        $('.select-student').prop('checked', this.checked);
    });

    /* --------------------------
       MOVE STUDENTS
    ---------------------------*/
    $('#moveBtn').click(function() {

        let selected = $('.select-student:checked').map(function(){
            return $(this).val();
        }).get();

        if(selected.length === 0){
            showToast('warning','Select at least one student.');
            return;
        }

        let newSection = $('#move_section_id').val();
        let newClass = $('#move_class_id').val();
        let newArm = $('#move_arm_id').val();
        let session = $('#move_session_id').val();

        if(!newSection || !newClass || !newArm || !session){
            showToast('warning','Select Section, Class, Arm, and Session for destination.');
            return;
        }

        if(!confirm(`Move ${selected.length} student(s) to the selected destination?`)) return;

        $.post("{{ route('move_students.move') }}", {
            reg_numbers: selected,
            new_section_id: newSection,
            new_class_id: newClass,
            new_arm_id: newArm,
            session_id: session
        }, function(resp){

            if(resp.status === 'success'){
                showToast('success', resp.message);
                $('#loadStudentsBtn').click(); // Reload students
            } else {
                showToast('danger', resp.message);
            }

        }).fail(function(xhr){
            showToast('danger','Error moving students.');
        });
    });

    /* --------------------------
       TOAST FUNCTION
    ---------------------------*/
    function showToast(type, message){
        const id = 'toast'+Date.now();
        const bg = {
            success:'text-bg-success',
            danger:'text-bg-danger',
            warning:'text-bg-warning',
            info:'text-bg-info'
        }[type];

        const toast = `
        <div class="toast align-items-center ${bg} border-0" id="${id}">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;

        $('.toast-container').append(toast);

        const bsToast = new bootstrap.Toast(document.getElementById(id), { delay: 4000 });
        bsToast.show();

        document.getElementById(id).addEventListener('hidden.bs.toast', function(){
            $(this).remove();
        });
    }

});
</script>
@endsection
