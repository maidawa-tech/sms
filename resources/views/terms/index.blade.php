@extends('layouts.dashboard')

@section('title', 'Manage Terms')

@section('content')
<div class="container-fluid mt-3">

    <!-- Table Card -->
    <div class="card shadow-sm border-0">

        <!-- Card Header with Title & Button -->
        <div class="card-header d-flex justify-content-between align-items-center py-2 px-3" style="background-color: #ffffff;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-book-reader" style="color: #679767; font-size: 1.4rem;"></i>
                <h5 class="mb-0 fw-bold" style="color: #679767;">Manage Terms</h5>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#termModal">
                <i class="fas fa-plus"></i> Add Term
            </button>
        </div>

        <!-- Card Body -->
        <div class="card-body">

            <!-- Alerts 
            @foreach (['success', 'error', 'info'] as $msg)
                @if(session($msg))
                    <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} alert-dismissible fade show" role="alert">
                        {{ session($msg) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach -->

            <!-- Table -->
            <div class="table-responsive">
                <table id="termTable" class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th class="text-start">Term Name</th>
                            <th>Academic Session</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terms as $key => $term)
                            <tr @if($term->is_active) class="table-success" @endif>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-start fw-semibold">{{ $term->term_name }}</td>
                                <td class="text-center">{{ $term->session?->session_name ?? '—' }}</td>
                                <td class="text-center">{{ $term->start_date ?? '—' }}</td>
                                <td class="text-center">{{ $term->end_date ?? '—' }}</td>
                                <td class="text-center">
                                    @if($term->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <!-- Activate -->
                                    <form action="{{ route('terms.activate', $term->term_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm {{ $term->is_active ? 'btn-outline-success' : 'btn-outline-primary' }}"
                                                @if($term->is_active) disabled @endif
                                                onclick="return confirm('Activating this term will deactivate all others. Continue?')">
                                            {{ $term->is_active ? 'Active' : 'Activate' }}
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <button class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editTermModal{{ $term->term_id }}">
                                        Edit
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('terms.destroy', $term->term_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                @if($term->is_active) disabled @endif
                                                onclick="return confirm('Are you sure you want to delete this term?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                                
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     EDIT TERM MODALS
=========================================== -->
@foreach($terms as $term)
<div class="modal fade" id="editTermModal{{ $term->term_id }}" tabindex="-1"
     aria-labelledby="editTermModalLabel{{ $term->term_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editTermModalLabel{{ $term->term_id }}">Edit Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('terms.update', $term->term_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="term_name_{{ $term->term_id }}" class="form-label">Term Name</label>
                        <input type="text" name="term_name" id="term_name_{{ $term->term_id }}" class="form-control"
                               value="{{ $term->term_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="session_id_{{ $term->term_id }}" class="form-label">Academic Session</label>
                        <select name="session_id" id="session_id_{{ $term->term_id }}" class="form-select" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->session_id }}"
                                    {{ $term->session_id == $session->session_id ? 'selected' : '' }}>
                                    {{ $session->session_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date_{{ $term->term_id }}" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date_{{ $term->term_id }}" class="form-control"
                                   value="{{ $term->start_date }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date_{{ $term->term_id }}" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date_{{ $term->term_id }}" class="form-control"
                                   value="{{ $term->end_date }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning">Update Term</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- ==========================================
     ADD TERM MODAL
=========================================== -->
<div class="modal fade" id="termModal" tabindex="-1" aria-labelledby="termModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="termModalLabel">Add Term</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('terms.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="term_name" class="form-label">Term Name</label>
                        <input type="text" name="term_name" id="term_name" class="form-control"
                               placeholder="e.g. First Term" required>
                    </div>
                    <div class="mb-3">
                        <label for="session_id" class="form-label">Academic Session</label>
                        <select name="session_id" id="session_id" class="form-select" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->session_id }}">{{ $session->session_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Save Term</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     CUSTOM STYLES & DATATABLE JS
=========================================== -->
<link rel="stylesheet" href="{{ asset('datatables/dataTables.bootstrap5.min.css') }}">
<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/dataTables.bootstrap5.min.js') }}"></script>

<style>
input:focus, select:focus {
    border-color: #679767 !important;
    box-shadow: 0 0 0 0.2rem rgba(103,151,103,0.25) !important;
}
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length {
    margin-bottom: 10px !important;
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
    box-shadow: 0 0 0 0.2rem rgba(103,151,103,0.25);
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
    $('#termTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        lengthChange: true,
        responsive: true,
        autoWidth: false,
        language: {
            search: "Filter:",
            lengthMenu: "Show _MENU_ records per page",
            zeroRecords: "No matching terms found",
            info: "Showing _START_ to _END_ of _TOTAL_ terms",
            infoEmpty: "No terms available",
            emptyTable: "No terms have been added yet."
        },
        columnDefs: [
            { targets: '_all', defaultContent: '' }
        ]
    });

    setTimeout(() => {
        $('.alert').fadeOut('slow');
    }, 4000);
});
</script>

@endsection
