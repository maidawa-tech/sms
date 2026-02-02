@extends('layouts.dashboard')

@section('title', 'Manage Sections')

@section('content')
<div class="container-fluid mt-3">

    <!-- Header 
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark">Manage Sections</h4>
    </div> -->

    <!-- Section Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fa fa-list me-2" style="color: #679767; font-size: 1.4rem;"></i>
                <h5 class="mb-0 fw-bold" style="color: #679767;"> Sections</h5>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sectionModal">
                <i class="fas fa-plus"></i> Add Section
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-success text-start">
                    <tr>
                        <th>#</th>
                        <th>Section Name</th>
                        <th>Short Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="text-start">
                    @forelse ($sections as $index => $section)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $section->section_name }}</td>
                            <td>{{ $section->section_short_name }}</td>
                            <td>
                                <button class="btn btn-secondary btn-sm edit-btn"
                                        data-id="{{ $section->section_id }}"
                                        data-name="{{ $section->section_name }}"
                                        data-short="{{ $section->section_short_name }}"
                                        data-bs-toggle="modal" data-bs-target="#sectionModal">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                <form action="{{ route('sections.destroy', $section->section_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this section?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">No sections added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Section Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1" aria-labelledby="sectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="sectionModalLabel">Add Section</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                 <form id="sectionForm" method="POST" action="{{ route('sections.store') }}">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" id="section_id" name="section_id">

                    <div class="mb-3">
                        <label for="section_name" class="form-label">Section Name</label>
                        <input type="text" id="section_name" name="section_name" class="form-control @error('section_name') is-invalid @enderror" value="{{ old('section_name') }}" required>
                        @error('section_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="section_short_name" class="form-label">Short Name</label>
                        <input type="text" id="section_short_name" name="section_short_name" class="form-control @error('section_short_name') is-invalid @enderror" value="{{ old('section_short_name') }}" required>
                        @error('section_short_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    input:focus, textarea:focus {
        border-color: #679767 !important;
        box-shadow: 0 0 0 0.2rem rgba(103, 151, 103, 0.25) !important;
    }
</style>

<!-- Custom Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('sectionForm');
        const methodInput = form.querySelector('input[name="_method"]');

        // Edit button
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const shortName = button.getAttribute('data-short');

                document.getElementById('sectionModalLabel').textContent = 'Edit Section';
                document.getElementById('section_name').value = name;
                document.getElementById('section_short_name').value = shortName;
                form.action = `/sections/${id}`;
                methodInput.value = 'PUT';
            });
        });

        // Reset on modal close
        const sectionModal = document.getElementById('sectionModal');
        sectionModal.addEventListener('hidden.bs.modal', () => {
            form.reset();
            document.getElementById('sectionModalLabel').textContent = 'Add Section';
            form.action = '{{ route('sections.store') }}';
            methodInput.value = 'POST';
        });

        // Auto-open modal if validation error exists
        @if($errors->any())
            const myModal = new bootstrap.Modal(document.getElementById('sectionModal'));
            myModal.show();
        @endif
    });
</script>
@endsection
