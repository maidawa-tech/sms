@extends('layouts.dashboard')

@section('title', 'Manage Students')

@section('content')
<div class="container-fluid mt-3">

    <!-- Students Table Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <!-- Card Header -->
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #fff; border-bottom: 1px solid #dee2e6;">
            <h5 class="fw-bold mb-0" style="color: #679767;">
                <i class="fas fa-user-graduate me-2" style="color: #679767;"></i> Manage Students
            </h5>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#studentModal">
                <i class="fas fa-plus-circle me-2"></i> Add Student
            </button>
        </div>

        <!-- Alert Area (Removed visible Bootstrap alerts) -->
        <div id="alertMessage" class="px-3 mt-3 d-none"></div>

        <!-- Card Body with Table -->
        <div class="card-body">
            <div class="table-responsive">
                <table id="studentsTable" class="table table-bordered table-hover align-middle">
                    <thead class="table-success text-dark">
                        <tr>
                            <th>#</th>
                            <th>Reg Number</th>
                            <th>Name</th>
                            <th>Parent Phone</th>
                            <th>Address</th>
                            <th>Passport</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 shadow-sm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="studentModalLabel">Add New Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="studentForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="student_id" id="student_id">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="reg_number" class="form-label">Reg Number</label>
                            <input type="text" name="reg_number" id="reg_number" class="form-control" placeholder="e.g., RI/2023/001" required>
                        </div>
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="surname" class="form-label">Surname</label>
                            <input type="text" name="surname" id="surname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="other_name" class="form-label">Other Name (Optional)</label>
                            <input type="text" name="other_name" id="other_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="parent_phone" class="form-label">Parent Phone</label>
                            <input type="text" name="parent_phone" id="parent_phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="passport" class="form-label">Passport</label>
                            <input type="file" name="passport" id="passport" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img id="previewPassport" src="" alt="Passport Preview" class="img-thumbnail d-none" width="100">
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="saveBtn">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatables/dataTables.bootstrap5.min.css') }}">
<script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/dataTables.bootstrap5.min.js') }}"></script>

<script>
$(document).ready(function () {
    // Initialize DataTable
    let table = $('#studentsTable').DataTable({
        responsive: true,
        autoWidth: false,
        ajax: "{{ route('students.index') }}",
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: 'reg_number' },
            { data: null, render: data => `${data.surname} ${data.first_name} ${data.other_name ?? ''}` },
            { data: 'parent_phone' },
            { data: 'address' },
            { data: 'passport', render: data => data ? `<img src="/uploads/students/${data}" width="50" class="rounded">` : '—' },
            {
                data: null,
                render: data => `
                    <button class="btn btn-warning btn-sm editBtn" data-id="${data.student_id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm deleteBtn" data-id="${data.student_id}">
                        <i class="fas fa-trash"></i>
                    </button>
                `
            }
        ],
        pageLength: 10,
        language: { search: "Search:", paginate: { previous: "&laquo;", next: "&raquo;" } }
    });

    // Passport preview
    $('#passport').on('change', function () {
        let reader = new FileReader();
        reader.onload = e => $('#previewPassport').attr('src', e.target.result).removeClass('d-none');
        reader.readAsDataURL(this.files[0]);
    });

    // Add/Edit Student
    $('#studentForm').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#saveBtn').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('students.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: res => {
                $('#studentModal').modal('hide');
                table.ajax.reload();
                showToast('success', res.message);
                $('#studentForm')[0].reset();
                $('#previewPassport').addClass('d-none');
            },
            error: err => {
                $('#studentModal').modal('hide');
                if (err.status === 422) {
                    let errors = err.responseJSON.errors;
                    let messages = Object.values(errors).flat().join('<br>');
                    showToast('error', messages);
                } else {
                    showToast('error', 'Something went wrong!');
                }
            },
            complete: () => $('#saveBtn').prop('disabled', false).text('Save Student')
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        let id = $(this).data('id');
        $.get(`/students/${id}`, function (student) {
            $('#studentModalLabel').text('Edit Student');
            $('#student_id').val(student.student_id);
            $('#reg_number').val(student.reg_number);
            $('#first_name').val(student.first_name);
            $('#surname').val(student.surname);
            $('#other_name').val(student.other_name);
            $('#parent_phone').val(student.parent_phone);
            $('#address').val(student.address);
            if (student.passport) {
                $('#previewPassport').attr('src', '/uploads/students/' + student.passport).removeClass('d-none');
            } else {
                $('#previewPassport').addClass('d-none');
            }
            $('#studentModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        if (!confirm('Are you sure you want to delete this student?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: `/students/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: res => {
                table.ajax.reload();
                showToast('success', res.message);
            },
            error: () => showToast('error', 'Failed to delete student!')
        });
    });

    // Reset modal
    $('#studentModal').on('hidden.bs.modal', function () {
        $('#studentModalLabel').text('Add New Student');
        $('#studentForm')[0].reset();
        $('#student_id').val('');
        $('#previewPassport').addClass('d-none');
        $('#saveBtn').text('Save Student');
    });

    // ✅ Global Toast Integration
    function showToast(type, message) {
        let bgClass = {
            success: 'text-bg-success',
            error: 'text-bg-danger',
            warning: 'text-bg-warning',
            info: 'text-bg-info'
        }[type] || 'text-bg-secondary';

        let toastHTML = `
            <div class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        let $toast = $(toastHTML).appendTo('.toast-container');
        let toast = new bootstrap.Toast($toast[0], { delay: 5000 });
        toast.show();
        $toast.on('hidden.bs.toast', function () { $(this).remove(); });
    }
});
</script>

<style>
input:focus, select:focus {
    border-color: #679767 !important;
    box-shadow: 0 0 0 0.2rem rgba(103,151,103,0.25) !important;
}
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length {
    margin-bottom: 12px !important;
}
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #6c757d !important;
    border-radius: 4px !important;
    padding: 5px 10px !important;
    outline: none;
}
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #679767 !important;
    box-shadow: 0 0 0 0.2rem rgba(103, 151, 103, 0.25);
}
.dataTables_wrapper .dataTables_length select {
    border: 1px solid #6c757d !important;
    border-radius: 4px !important;
}
.dataTables_wrapper .dataTables_length select:focus {
    border-color: #679767 !important;
    box-shadow: 0 0 0 0.2rem rgba(103, 151, 103, 0.25);
}
.page-item.active .page-link {
    background-color: #679767 !important;
    border-color: #679767 !important;
    color: #fff !important;
}
.page-link {
    color: #333 !important;
}
.page-link:hover {
    background-color: #d0f0c0 !important;
    border-color: #679767 !important;
    color: #333 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    margin: 0 2px !important;
}
</style>
@endsection
