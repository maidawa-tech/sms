@extends('layouts.dashboard')

@section('title', 'Manage Arms')

@section('content')
<div class="container-fluid mt-3">

    <!-- Arms Card -->
    <div class="card shadow-sm border-0">
        <!-- Card Header -->
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fa fa-list me-2" aria-hidden="true" style="color: #679767; font-size: 1.4rem;"></i>
                <h5 class="mb-0 fw-bold" style="color: #679767;">Arms</h5>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#armModal">
                <i class="fas fa-plus"></i> Add Arm
            </button>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            @if ($arms->isEmpty())
                <div class="alert alert-info">No arm added yet.</div>
            @else
                <div class="table-responsive">
                    <table id="armTable" class="table table-bordered align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Arm Name</th>
                                <th>Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($arms as $index => $arm)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $arm->arm_name }}</td>
                                <td>{{ $arm->class->class_name ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editArmModal{{ $arm->arm_id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('arms.destroy', $arm->arm_id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this arm?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editArmModal{{ $arm->arm_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">Edit Arm</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('arms.update', $arm->arm_id) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-3">
                                                    <label class="form-label">Class</label>
                                                    <select name="class_id" class="form-select" required>
                                                        <option value="">Select Class</option>
                                                        @foreach ($classes as $class)
                                                            <option value="{{ $class->class_id }}"
                                                                {{ $class->class_id == $arm->class_id ? 'selected' : '' }}>
                                                                {{ $class->class_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Arm Name</label>
                                                    <input type="text" name="arm_name" value="{{ $arm->arm_name }}" class="form-control" required>
                                                    @error('arm_name')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
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

<!-- Add Arm Modal -->
<div class="modal fade @if ($errors->any()) show @endif" id="armModal" tabindex="-1" @if ($errors->any()) style="display:block;" @endif>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add New Arm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('arms.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->class_id }}" {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Arm Name</label>
                        <input type="text" name="arm_name" class="form-control" value="{{ old('arm_name') }}" required>
                        @error('arm_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Inline Styles -->
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

<!-- jQuery -->
<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>

<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('datatables/dataTables.bootstrap5.min.css') }}">
<script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/dataTables.bootstrap5.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('#armTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        lengthChange: true,
        responsive: true,
        language: {
            search: "Filter:",
            lengthMenu: "Show _MENU_ records per page",
            zeroRecords: "No matching records found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No records available"
        }
    });

    // Auto-open Add Arm modal on validation error
    @if ($errors->any())
        var armModal = new bootstrap.Modal(document.getElementById('armModal'));
        armModal.show();
    @endif
});
</script>
@endsection
