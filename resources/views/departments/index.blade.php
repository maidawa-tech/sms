@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <h4 class="mt-4 mb-3">Departments Management</h4>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Add Department Button --}}
    <div class="mb-3 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
            <i class="fas fa-plus-circle"></i> Add Department
        </button>
    </div>

    {{-- Departments Table --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle" id="departmentsTable">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Department Name</th>
                        <th>Short Name</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $key => $department)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $department->department_name }}</td>
                            <td>{{ $department->department_short_name }}</td>
                            <td>
                                @if ($department->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary editBtn" 
                                    data-id="{{ $department->department_id }}"
                                    data-name="{{ $department->department_name }}"
                                    data-short="{{ $department->department_short_name }}"
                                    data-active="{{ $department->is_active }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editDepartmentModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('departments.destroy', $department->department_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this department?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Department Modal --}}
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addDepartmentModalLabel">Add Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="department_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Name</label>
                        <input type="text" name="department_short_name" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Department</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Department Modal --}}
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDepartmentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="department_name" id="edit_department_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Name</label>
                        <input type="text" name="department_short_name" id="edit_department_short_name" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Department</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.editBtn');
        const form = document.getElementById('editDepartmentForm');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const short = this.dataset.short;
                const active = this.dataset.active == 1 ? true : false;

                form.action = `/departments/${id}`;
                document.getElementById('edit_department_name').value = name;
                document.getElementById('edit_department_short_name').value = short;
                document.getElementById('edit_is_active').checked = active;
            });
        });
    });
</script>
@endsection
