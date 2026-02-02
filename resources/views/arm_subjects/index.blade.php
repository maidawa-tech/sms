@extends('layouts.dashboard')

@section('title', 'Assign Subjects to Arms')

@section('content')
<div class="container-fluid mt-3">

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center" style="background:#fff;">
            <h5 class="fw-bold mb-0" style="color:#679767;">
                <i class="fas fa-list me-2"></i> Assign Subjects to Arms
            </h5>

            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                <i class="fas fa-plus-circle"></i> Assign Subject
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="assignTable" class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Arm</th>
                            <th>Subject</th>
                            <th>Teacher (Optional)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow-sm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Assign Subject to Arm</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="assignForm">
                @csrf
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <label class="form-label">Select Arm</label>
                    <select class="form-select" name="arm_id" id="arm_id" required>
                        <option value="">-- Select Arm --</option>
                        @foreach($arms as $a)
                            <option value="{{ $a->arm_id }}">{{ $a->arm_name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label mt-3">Select Subject</label>
                    <select class="form-select" name="subject_id" id="subject_id" required>
                        <option value="">-- Select Subject --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->subject_id }}">{{ $s->subject_name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label mt-3">Assign Teacher (optional)</label>
                    <input type="number" class="form-control" name="teacher_id" id="teacher_id" placeholder="Enter Teacher User ID">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" id="saveBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('datatables/dataTables.bootstrap5.min.css') }}">
<script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/dataTables.bootstrap5.min.js') }}"></script>

<script>
$(document).ready(() => {

    let table = $('#assignTable').DataTable({
        responsive: true,
        autoWidth: false,
        ajax: "{{ route('arm_subjects.index') }}",
        columns: [
            { data: null, render:(d,t,r,m)=>m.row+1 },
            { data: 'arm.arm_name' },
            { data: 'subject.subject_name' },
            { data: 'teacher_id', render: d => d ?? '—' },
            {
                data: null,
                render: d => `
                    <button class="btn btn-warning btn-sm editBtn" data-id="${d.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm deleteBtn" data-id="${d.id}">
                        <i class="fas fa-trash"></i>
                    </button>`
            }
        ]
    });

    // SAVE
    $('#assignForm').on('submit', e => {
        e.preventDefault();

        $('#saveBtn').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('arm_subjects.store') }}",
            method: "POST",
            data: new FormData($('#assignForm')[0]),
            processData: false,
            contentType: false,
            success: res => {
                $('#assignModal').modal('hide');
                table.ajax.reload();
                showToast('success', res.message);
            },
            error: err => {
                let msg = "Something went wrong";
                if (err.status === 422) {
                    msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                }
                showToast('error', msg);
            },
            complete: () => $('#saveBtn').prop('disabled', false).text('Save')
        });
    });

    // EDIT
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');

        $.get(`/arm_subjects/${id}`, res => {
            $('#id').val(res.id);
            $('#arm_id').val(res.arm_id);
            $('#subject_id').val(res.subject_id);
            $('#teacher_id').val(res.teacher_id);

            $('#assignModal .modal-title').text("Edit Assignment");
            $('#assignModal').modal('show');
        });
    });

    // DELETE
    $(document).on('click', '.deleteBtn', function () {
        if (!confirm("Delete this assignment?")) return;

        let id = $(this).data('id');

        $.ajax({
            url: `/arm_subjects/${id}`,
            method: "DELETE",
            data: { _token: "{{ csrf_token() }}" },
            success: res => {
                table.ajax.reload();
                showToast('success', res.message);
            },
            error: () => showToast('error', 'Failed!')
        });
    });

    $('#assignModal').on('hidden.bs.modal', () => {
        $('#assignForm')[0].reset();
        $('#id').val('');
        $('#assignModal .modal-title').text("Assign Subject to Arm");
    });

    // toast
    function showToast(type, message) {
        let bg = { success:'text-bg-success', error:'text-bg-danger' }[type];

        let toastHTML = `
        <div class="toast ${bg} border-0">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;

        let $toast = $(toastHTML).appendTo('.toast-container');
        new bootstrap.Toast($toast[0], { delay: 5000 }).show();
        $toast.on('hidden.bs.toast', () => $toast.remove());
    }
});
</script>

@endsection
