@extends('layouts.dashboard')

@section('title', 'Manage Academic Sessions')

@section('content')
<div class="container-fluid mt-3">

    <!-- Table Card -->
    <div class="card shadow-sm border-0">

        <!-- Card Header with Title & Button -->
        <div class="card-header d-flex justify-content-between align-items-center py-2 px-3" style="background-color: #ffffff;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-calendar-alt" style="color: #679767; font-size: 1.4rem;"></i>
                <h5 class="mb-0 fw-bold" style="color: #679767;">Manage Academic Sessions</h5>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sessionModal">
                <i class="fas fa-plus"></i> Add Session
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
                <table id="sessionTable" class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th class="text-start">Session Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $key => $session)
                            <tr @if($session->is_active) class="table-success" @endif>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-start fw-semibold">{{ $session->session_name }}</td>
                                <td class="text-center">{{ $session->start_date ?? '—' }}</td>
                                <td class="text-center">{{ $session->end_date ?? '—' }}</td>
                                <td class="text-center">
                                    @if($session->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('academic_sessions.activate', $session->session_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $session->is_active ? 'btn-outline-success' : 'btn-outline-primary' }}"
                                                @if($session->is_active) disabled @endif
                                                onclick="return confirm('Activating this session will deactivate all others. Continue?')">
                                            {{ $session->is_active ? 'Active' : 'Activate' }}
                                        </button>
                                    </form>

                                    <button class="btn btn-sm btn-outline-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editSessionModal{{ $session->session_id }}">
                                        Edit
                                    </button>

                                    <form action="{{ route('academic_sessions.destroy', $session->session_id) }}" method="POST" class="d-inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger"
                                                @if($session->is_active) disabled @endif
                                                onclick="return confirm('Are you sure you want to delete this session?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editSessionModal{{ $session->session_id }}" tabindex="-1" aria-labelledby="editSessionModalLabel{{ $session->session_id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content rounded-3 shadow">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title" id="editSessionModalLabel{{ $session->session_id }}">Edit Session</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('academic_sessions.update', $session->session_id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="session_name_{{ $session->session_id }}" class="form-label">Session Name</label>
                                                    <input type="text" name="session_name" id="session_name_{{ $session->session_id }}" class="form-control" value="{{ $session->session_name }}" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="start_date_{{ $session->session_id }}" class="form-label">Start Date</label>
                                                        <input type="date" name="start_date" id="start_date_{{ $session->session_id }}" class="form-control" value="{{ $session->start_date }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="end_date_{{ $session->session_id }}" class="form-label">End Date</label>
                                                        <input type="date" name="end_date" id="end_date_{{ $session->session_id }}" class="form-control" value="{{ $session->end_date }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-warning">Update Session</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No academic sessions have been added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Add Session Modal -->
<div class="modal fade" id="sessionModal" tabindex="-1" aria-labelledby="sessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="sessionModalLabel">Add Academic Session</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('academic_sessions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="session_name" class="form-label">Session Name</label>
                        <input type="text" name="session_name" id="session_name" class="form-control" placeholder="e.g. 2024/2025" required>
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
                    <button class="btn btn-success">Save Session</button>
                </div>
            </form>
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

<!-- jQuery -->
<script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>

<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('datatables/dataTables.bootstrap5.min.css') }}">
<script src="{{ asset('datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/dataTables.bootstrap5.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('#sessionTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        lengthChange: true,
        responsive: true,
        language: {
            search: "Filter:",
            lengthMenu: "Show _MENU_ records per page",
            zeroRecords: "No matching sessions found",
            info: "Showing _START_ to _END_ of _TOTAL_ sessions",
            infoEmpty: "No sessions available"
        }
    });

    setTimeout(() => {
        $('.alert').fadeOut('slow');
    }, 4000);
});
</script>
@endsection
