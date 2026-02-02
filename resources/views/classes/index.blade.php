@extends('layouts.dashboard')

@section('title', 'Manage Classes')

@section('content')
<div class="container-fluid mt-3">

    <!-- Header 
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark">Manage Classes</h4>
    </div>-->

    <!-- Class Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fa fa-list me-2" aria-hidden="true" style="color: #679767; font-size: 1.4rem;"></i>
                <h5 class="mb-0 fw-bold" style="color: #679767;">Classes</h5>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#classModal">
                <i class="fas fa-plus"></i> Add Class
            </button>
        </div>

        <div class="card-body">
            @if ($classes->isEmpty())
                <div class="alert alert-info">No class added yet.</div>
            @else
                <div class="table-responsive">
                    <table id="classTable" class="table table-bordered align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Class Name</th>
                                <th>Short Name</th>
                                <th>Section</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $index => $class)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $class->class_name }}</td>
                                <td>{{ $class->class_short_name }}</td>
                                <td>{{ $class->section->section_name ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editClassModal{{ $class->class_id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('classes.destroy', $class->class_id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this class?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editClassModal{{ $class->class_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">Edit Class</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('classes.update', $class->class_id) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-3">
                                                    <label class="form-label">Section</label>
                                                    <select name="section_id" class="form-select" required>
                                                        <option value="">Select Section</option>
                                                        @foreach ($sections as $section)
                                                            <option value="{{ $section->section_id }}"
                                                                {{ $section->section_id == $class->section_id ? 'selected' : '' }}>
                                                                {{ $section->section_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Class Name</label>
                                                    <input type="text" name="class_name" value="{{ $class->class_name }}" class="form-control" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Short Name</label>
                                                    <input type="text" name="class_short_name" value="{{ $class->class_short_name }}" class="form-control" required>
                                                </div>

                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add New Class</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('classes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Section</label>
                        <select name="section_id" class="form-select" required>
                            <option value="">Select Section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->section_id }}">{{ $section->section_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Name</label>
                        <input type="text" name="class_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Name</label>
                        <input type="text" name="class_short_name" class="form-control" required>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
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

<style>
input:focus, select:focus {
    border-color: #679767 !important;
    box-shadow: 0 0 0 0.2rem rgba(103,151,103,0.25) !important;
}

/* Table enhancements */
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length {
    margin-bottom: 12px !important;
}
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #6c757d !important;
    border-radius: 4px !important;
    padding: 5px 10px !important;
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
.page-link:hover {
    background-color: #d0f0c0 !important;
    border-color: #679767 !important;
    color: #333 !important;
}
</style>

<script>
$(document).ready(function() {
    $('#classTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        responsive: true,
        language: {
            search: "Filter:",
            lengthMenu: "Show _MENU_ per page",
            zeroRecords: "No matching records found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No records available"
        }
    });
});
</script>
@endsection
