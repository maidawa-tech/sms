@extends('layouts.dashboard')

@section('title', 'Manage School')

@section('content')
<div class="container-fluid mt-3">

    <!-- Header Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper bg-light-success rounded-circle p-2 me-3">
                    <i class="fa fa-school" aria-hidden="true" style="color: #679767; font-size: 1.3rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" style="color: #679767;">School Management</h5>
                    <small class="text-muted">Manage your school information and branding</small>
                </div>
            </div>
            <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#schoolModal">
                <i class="fas fa-plus me-2"></i> {{ $school ? 'Edit School' : 'Add School' }}
            </button>
        </div>

        <!-- School Details Section -->
        <div class="card-body p-4">
            @if ($school)
                <div class="row">
                    <!-- School Logo & Basic Info -->
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start">
                            <div class="position-relative me-4">
                                @if($school->logo)
                                    <img src="{{ asset('uploads/school_logo/' . $school->logo) }}" 
                                        alt="School Logo" class="rounded shadow-sm" width="120" height="120"
                                        style="object-fit: cover; border: 3px solid #f1f1f1;">
                                @else
                                    <div class="bg-gradient-secondary text-white rounded d-flex align-items-center justify-content-center shadow-sm" 
                                        style="width:120px; height:120px; background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                                        <i class="fas fa-school fa-3x"></i>
                                    </div>
                                @endif
                                @if($school->logo)
                                    <div class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle p-1" 
                                        style="transform: translate(25%, 25%);">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <h3 class="mb-2 fw-bold text-dark">{{ $school->school_name }}</h3>
                                
                                @if($school->motto)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-light-warning px-3 py-1 rounded-pill d-inline-flex align-items-center">
                                            <i class="fas fa-quote-left me-2" style="font-size: 0.8rem;"></i>
                                            <span class="fw-medium">{{ $school->motto }}</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="icon-circle bg-light-success me-3">
                                            <i class="fas fa-map-marker-alt text-success"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-muted small">Address</p>
                                            <p class="mb-0 fw-medium">{{ $school->address }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="icon-circle bg-light-info me-3">
                                            <i class="fas fa-calendar-alt text-info"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-muted small">Last Updated</p>
                                            <p class="mb-0 fw-medium">{{ $school->updated_at->format('F d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons Card -->
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="card border h-100">
                            <div class="card-body d-flex flex-column justify-content-center p-4">
                                <h6 class="text-uppercase text-muted mb-3 fw-semibold">School Actions</h6>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#schoolModal">
                                        <i class="fas fa-edit me-2"></i> Edit Details
                                    </button>
                                    <form action="{{ route('school.destroy') }}" method="POST" 
                                        onsubmit="return confirm('Are you sure you want to delete this school? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="fas fa-trash me-2"></i> Delete School
                                        </button>
                                    </form>
                                </div>
                                <div class="mt-4 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> 
                                        School information will be used across the system
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="empty-state-icon rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-4 mb-3">
                            <i class="fas fa-school fa-3x text-muted"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">No School Added Yet</h4>
                        <p class="text-muted mb-4">Add your school details to get started with the system setup.</p>
                        <button class="btn btn-success px-4 py-2" data-bs-toggle="modal" data-bs-target="#schoolModal">
                            <i class="fas fa-plus me-2"></i> Add School Details
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bootstrap Modal for Add/Edit School -->
<div class="modal fade" id="schoolModal" tabindex="-1" aria-labelledby="schoolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-success text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-white bg-opacity-25 rounded-circle p-2 me-3">
                        <i class="fas fa-school"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="schoolModalLabel">
                            {{ $school ? 'Edit School Details' : 'Add School Details' }}
                        </h5>
                        <small class="opacity-75">Fill in your school information below</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form action="{{ route('school.store') }}" method="POST" enctype="multipart/form-data" id="schoolForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="school_name" class="form-label fw-semibold">
                                School Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-university text-muted"></i>
                                </span>
                                <input type="text" id="school_name" name="school_name" 
                                    class="form-control border-start-0 ps-0" 
                                    value="{{ $school->school_name ?? old('school_name') }}" 
                                    placeholder="Enter school name" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="motto" class="form-label fw-semibold">School Motto</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-quote-right text-muted"></i>
                                </span>
                                <input type="text" id="motto" name="motto" 
                                    class="form-control border-start-0 ps-0" 
                                    value="{{ $school->motto ?? old('motto') }}"
                                    placeholder="e.g., Excellence in Education">
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="address" class="form-label fw-semibold">Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 align-items-start pt-3">
                                    <i class="fas fa-map-marker-alt text-muted"></i>
                                </span>
                                <textarea id="address" name="address" rows="3" 
                                    class="form-control border-start-0 ps-0" 
                                    placeholder="Enter complete school address">{{ $school->address ?? old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="logo" class="form-label fw-semibold">School Logo</label>
                            <div class="card border">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="upload-preview rounded-circle bg-light d-flex align-items-center justify-content-center" 
                                                style="width: 100px; height: 100px; border: 2px dashed #dee2e6;">
                                                @if ($school && $school->logo)
                                                    <img id="logoPreview" 
                                                        src="{{ asset('uploads/school_logo/' . $school->logo) }}" 
                                                        class="rounded-circle w-100 h-100" 
                                                        style="object-fit: cover;">
                                                @else
                                                    <i class="fas fa-camera text-muted fa-2x" id="uploadIcon"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col">
                                            <input type="file" id="logo" name="logo" 
                                                class="form-control" accept="image/*"
                                                onchange="previewLogo(event)">
                                            <small class="text-muted d-block mt-2">
                                                Recommended: Square image, 300×300 pixels, max 2MB
                                            </small>
                                            @if ($school && $school->logo)
                                                <small class="text-success d-block mt-1">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Current file: {{ $school->logo }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light px-4 py-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save me-2"></i> Save School Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .icon-wrapper {
        transition: all 0.3s ease;
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .empty-state-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #679767 0%, #4a7c4a 100%);
    }
    
    .bg-light-success {
        background-color: rgba(103, 151, 103, 0.1);
    }
    
    .bg-light-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-light-info {
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    .upload-preview {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .upload-preview:hover {
        border-color: #679767 !important;
        background-color: rgba(103, 151, 103, 0.05);
    }
    
    /* Enhanced focus effects */
    input:focus, textarea:focus, select:focus {
        border-color: #679767 !important;
        box-shadow: 0 0 0 0.25rem rgba(103, 151, 103, 0.25) !important;
    }
    
    .input-group:focus-within .input-group-text {
        border-color: #679767 !important;
    }
    
    .btn-success {
        background-color: #679767;
        border-color: #679767;
    }
    
    .btn-success:hover {
        background-color: #5a875a;
        border-color: #5a875a;
    }
</style>

<script>
function previewLogo(event) {
    const preview = document.getElementById('logoPreview');
    const icon = document.getElementById('uploadIcon');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (!preview) {
                // Create new preview image if it doesn't exist
                const uploadDiv = document.querySelector('.upload-preview');
                const img = document.createElement('img');
                img.id = 'logoPreview';
                img.className = 'rounded-circle w-100 h-100';
                img.style.objectFit = 'cover';
                img.src = e.target.result;
                
                if (icon) icon.style.display = 'none';
                uploadDiv.innerHTML = '';
                uploadDiv.appendChild(img);
            } else {
                preview.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection