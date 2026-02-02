@extends('layouts.dashboard')

@section('title', 'Manage Subjects')

@section('content')
<div class="container-fluid mt-3">

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;">
            <h5 class="fw-bold mb-0" style="color:#679767;">
                <i class="fas fa-book me-2"></i> Manage Subjects
            </h5>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#subjectModal">
                <i class="fas fa-plus-circle"></i> Add Subject
            </button>
        </div>

        <div id="alertMessage" class="px-3 mt-3 d-none"></div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="subjectsTable" class="table table-bordered table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Subject Name</th>
                            <th>Short Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow-sm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Subject</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="subjectForm">
                @csrf
                <input type="hidden" id="subject_id" name="subject_id">

                <div class="modal-body">
                    <label class="form-label">Subject Name</label>
                    <input type="text" name="subject_name" id="subject_name" class="form-control" required>

                    <label class="form-label mt-3">Short Name</label>
                    <input type="text" name="short_name" id="short_name" class="form-control">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" id="saveBtn">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatables/dataTables.bootstrap5.min.css') }}">
<script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/dataTables.bootstrap5.min.js') }}"></script>

<script>
$(document).ready(() => {

    let table = $('#subjectsTable').DataTable({
        responsive: true,
        autoWidth: false,
        ajax: "{{ route('subjects.index') }}",
        columns: [
            { data: null, render:(d,t,r,m)=>m.row+1 },
            { data: 'subject_name' },
            { data: 'short_name', defaultContent: '—' },
            {
                data: null,
                render: d => `
                    <button class="btn btn-warning btn-sm editBtn" data-id="${d.subject_id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm deleteBtn" data-id="${d.subject_id}">
                        <i class="fas fa-trash"></i>
                    </button>
                `
            }
        ]
    });

    // Save
    $('#subjectForm').on('submit', e => {
        e.preventDefault();
        $('#saveBtn').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('subjects.store') }}",
            method: "POST",
            data: new FormData($('#subjectForm')[0]),
            processData: false,
            contentType: false,
            responsive: true,
            success: res => {
                $('#subjectModal').modal('hide');
                table.ajax.reload();
                showToast('success', res.message);
            },
            error: err => {
                let msg = 'Something went wrong';
                if (err.status === 422) {
                    msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                }
                showToast('error', msg);
            },
            complete: () => $('#saveBtn').prop('disabled', false).text('Save Subject')
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(`/subjects/${id}`, sub => {
            $('#subject_id').val(sub.subject_id);
            $('#subject_name').val(sub.subject_name);
            $('#short_name').val(sub.short_name);
            $('#subjectModal .modal-title').text("Edit Subject");
            $('#subjectModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        if (!confirm("Delete this subject?")) return;
        let id = $(this).data('id');
        $.ajax({
            url: `/subjects/${id}`,
            method: "DELETE",
            data: { _token: "{{ csrf_token() }}" },
            success: res => {
                table.ajax.reload();
                showToast('success', res.message);
            },
            error: () => showToast('error', "Delete failed")
        });
    });

    // Reset modal
    $('#subjectModal').on('hidden.bs.modal', () => {
        $('#subjectForm')[0].reset();
        $('#subject_id').val('');
        $('#subjectModal .modal-title').text("Add Subject");
    });

    // Toast function
    function showToast(type, message) {
        let bg = {
            success: 'text-bg-success',
            error: 'text-bg-danger'
        }[type];

        let toastHTML = `
            <div class="toast align-items-center ${bg} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        let $toast = $(toastHTML).appendTo('.toast-container');
        new bootstrap.Toast($toast[0], { delay: 5000 }).show();
        $toast.on('hidden.bs.toast', ()=> $toast.remove());
    }
});
</script>

@endsection
