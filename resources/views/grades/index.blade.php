@extends('layouts.dashboard')

@section('title', 'Manage Grades')

@section('content')
<style>
    /* Custom style for the select dropdown */
    .form-control-custom-border {
        border-color: #679767 !important; /* Apply the border color */
    }

    /* Style for when the select dropdown is focused (clicked/tabbed into) */
    .form-control-custom-border:focus {
        border-color: #679767 !important; /* Keep the color */
        box-shadow: 0 0 0 0.25rem rgba(103, 151, 103, 0.25) !important; /* Custom focus ring */
        border-width: 2px !important; /* Make the border bolder */
    }
</style>

<div class="container-fluid">
    {{-- Section and Grading Selection Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header" style="background-color:#fff;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color:#679767;">
                    <i class="fa-solid fa-list-check"></i> Selection Filters
                </h5>
                {{-- Grading Type Toggle (Only for SSS) --}}
                <div class="d-none" id="gradingToggleContainer">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold me-2" style="color:#679767;">Grading Type:</span>
                        <div class="btn-group" role="group" id="gradingToggleGroup">
                            <input type="radio" class="btn-check" name="grading_type" id="customGrading" value="custom" autocomplete="off">
                            <label class="btn btn-outline-success" for="customGrading">
                                <i class="fa-solid fa-gear"></i> Custom
                            </label>
                            
                            <input type="radio" class="btn-check" name="grading_type" id="waecGrading" value="waec" autocomplete="off">
                            <label class="btn btn-outline-primary" for="waecGrading">
                                <i class="fa-solid fa-file-certificate"></i> WAEC
                            </label>
                        </div>
                        <small class="text-muted ms-2" id="gradingTypeHint">Toggle to switch between grading systems</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Column for Section Selection --}}
                <div class="col-md-12 mb-3">
                    <label for="section_id" class="form-label fw-bold">Select Section</label>
                    <select id="section_id" class="form-select form-control-custom-border">
                        <option value="">-- Select Section --</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->section_id }}" data-short="{{ $sec->section_short_name }}">
                                {{ $sec->section_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Grades Table with Grading Type Indicator --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#fff;">
            <div>
                <h5 class="mb-0" style="color:#679767;">
                    <i class="fa-solid fa-graduation-cap"></i> Manage Grades
                </h5>
                <div class="d-none mt-2" id="currentGradingTypeIndicator">
                    <span class="badge bg-light text-dark border">
                        <i class="fa-solid fa-filter"></i> Currently viewing: 
                        <span id="gradingTypeLabel" class="fw-bold"></span> grades
                    </span>
                </div>
            </div>
            <div>
                {{-- REMOVED data-bs-toggle and data-bs-target to prevent modal pop-up before validation --}}
                <button class="btn" id="addGradeBtn" style="background-color:#679767; color:white;">
                    <i class="fa fa-plus"></i> Add Grade
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="gradesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Min Score</th>
                        <th>Max Score</th>
                        <th>Grade</th>
                        <th>Remark</th>
                        <th>Type</th>
                        <th width="140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7" class="text-center text-muted">Select a section to load grades</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="gradeForm" method="POST" action="{{ route('grades.store') }}">
                @csrf
                <input type="hidden" name="selected_section" id="selected_section">
                <input type="hidden" id="grade_id">
                <input type="hidden" name="is_waec" id="is_waec" value="0">

                <div class="modal-header" style="background-color:#679767; color:white;">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info d-none" id="gradingTypeAlert">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <div>
                                <small>
                                    This grade will be added to <span id="modalGradingTypeText" class="fw-bold"></span> 
                                    grading system for SSS section.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Min Score</label>
                        <input type="number" step="0.01" class="form-control" id="min_score" name="min_score" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Score</label>
                        <input type="number" step="0.01" class="form-control" id="max_score" name="max_score" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade Letter</label>
                        <input type="text" maxlength="5" class="form-control" id="grade_letter" name="grade_letter" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remark</label>
                        <input type="text" class="form-control" id="remark" name="remark" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="saveBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
/**
 * =================================================================
 * *** AJAX TOAST NOTIFICATION FUNCTION ***
 * =================================================================
 */
window.showAjaxToast = function(message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        console.error('Toast container not found!');
        return;
    }

    let bgColorClass;
    let btnClass = 'btn-close';
    
    switch(type) {
        case 'success':
            bgColorClass = 'text-bg-success border-0';
            btnClass += ' btn-close-white';
            break;
        case 'danger':
        case 'error':
            bgColorClass = 'text-bg-danger border-0';
            btnClass += ' btn-close-white';
            break;
        case 'warning':
            bgColorClass = 'text-bg-warning border-0';
            break; 
        case 'info':
            bgColorClass = 'text-bg-info border-0';
            break; 
        default:
            bgColorClass = 'text-bg-secondary border-0';
            btnClass += ' btn-close-white';
    }

    const toastHtml = `
        <div class="toast align-items-center ${bgColorClass}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="${btnClass} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('afterbegin', toastHtml);
    const newToastEl = toastContainer.querySelector('.toast:first-child');
    const toast = new bootstrap.Toast(newToastEl, { delay: 5000 });
    toast.show();

    newToastEl.addEventListener('hidden.bs.toast', () => {
        newToastEl.remove();
    });
};


document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]') ? 
                document.querySelector('meta[name="csrf-token"]').content : '';
    const sectionDropdown = document.getElementById("section_id");
    const selectedSectionInput = document.getElementById("selected_section");
    const gradingToggleContainer = document.getElementById("gradingToggleContainer");
    const gradingTypeAlert = document.getElementById("gradingTypeAlert");
    const modalGradingTypeText = document.getElementById("modalGradingTypeText");
    const isWaecInput = document.getElementById("is_waec");
    const currentGradingTypeIndicator = document.getElementById("currentGradingTypeIndicator");
    const gradingTypeLabel = document.getElementById("gradingTypeLabel");
    
    const gradeModalEl = document.getElementById("gradeModal");
    const gradeModal = new bootstrap.Modal(gradeModalEl);
    const gradeForm = document.getElementById("gradeForm");
    
    let currentGradingType = 'custom';

    function toggleGradingTypeSelector() {
        const selectedOption = sectionDropdown.options[sectionDropdown.selectedIndex];
        const short = selectedOption ? selectedOption.dataset.short : null;
        
        if (short === "SSS") {
            gradingToggleContainer.classList.remove("d-none");
            currentGradingTypeIndicator.classList.remove("d-none");
            
            const savedType = localStorage.getItem(`grading_type_${sectionDropdown.value}`);
            if (savedType) {
                currentGradingType = savedType;
                document.getElementById(`${savedType}Grading`).checked = true;
            } else {
                document.getElementById("customGrading").checked = true;
            }
            
            updateGradingTypeDisplay();
        } else {
            gradingToggleContainer.classList.add("d-none");
            currentGradingTypeIndicator.classList.add("d-none");
            currentGradingType = 'custom';
            isWaecInput.value = '0';
        }
    }

    function updateGradingTypeDisplay() {
        const isWaec = currentGradingType === 'waec';
        isWaecInput.value = isWaec ? '1' : '0';
        
        gradingTypeLabel.textContent = isWaec ? 'WAEC' : 'Custom';
        gradingTypeLabel.className = isWaec ? 'text-primary' : 'text-success';
        
        if (sectionDropdown.value) {
            localStorage.setItem(`grading_type_${sectionDropdown.value}`, currentGradingType);
        }
        
        fetchGrades();
    }

    function fetchGrades() {
        const section_id = sectionDropdown.value;
        selectedSectionInput.value = section_id;
        
        if (!section_id) {
            document.querySelector("#gradesTable tbody").innerHTML =
                `<tr><td colspan="7" class="text-center text-muted">Select a section to load grades</td></tr>`;
            return;
        }
        
        const url = `{{ route('grades.fetch') }}?section_id=${section_id}&type=${currentGradingType}`;
        
        fetch(url)
            .then(res => res.json())
            .then(data => {
                let tbody = "";
                if (!data.success) {
                    window.showAjaxToast(data.error || 'Failed to fetch grades.', 'danger');
                    tbody = `<tr><td colspan="7" class="text-center text-danger">${data.error || 'Failed to fetch grades.'}</td></tr>`;
                } else {
                    const grades = data.grades || [];
                    if (grades.length === 0) {
                        tbody = `<tr><td colspan="7" class="text-center text-muted">
                            No ${currentGradingType} grades found for this section.
                            <br><small>Click "Add Grade" to create new ${currentGradingType === 'waec' ? 'WAEC' : 'custom'} grades.</small>
                        </td></tr>`;
                    } else {
                        grades.forEach((g, i) => {
                            let typeBadge = g.is_waec 
                                ? "<span class='badge bg-primary'><i class='fa-solid fa-file-certificate'></i> WAEC</span>" 
                                : "<span class='badge bg-success'><i class='fa-solid fa-gear'></i> Custom</span>";
                            tbody += `
                                <tr>
                                    <td>${i+1}</td>
                                    <td>${g.min_score}</td>
                                    <td>${g.max_score}</td>
                                    <td>${g.grade_letter}</td>
                                    <td>${g.remark}</td>
                                    <td>${typeBadge}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success editBtn" data-id="${g.id}" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger deleteBtn" data-id="${g.id}" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                        });
                    }
                }
                document.querySelector("#gradesTable tbody").innerHTML = tbody;
            })
            .catch(error => {
                console.error('Error fetching grades:', error);
                window.showAjaxToast('A network error occurred while loading grades.', 'danger');
                document.querySelector("#gradesTable tbody").innerHTML =
                    `<tr><td colspan="7" class="text-center text-danger">Error fetching data.</td></tr>`;
            });
    }

    // --- Event Listeners ---

    sectionDropdown.addEventListener("change", () => {
        toggleGradingTypeSelector();
        fetchGrades();
    });

    document.querySelectorAll('input[name="grading_type"]').forEach(radio => {
        radio.addEventListener("change", (e) => {
            if (e.target.checked) {
                currentGradingType = e.target.value;
                updateGradingTypeDisplay();
            }
        });
    });

    /**
     * Updated Add Grade Button Listener
     * Validates selection before manually triggering the modal
     */
    document.getElementById("addGradeBtn").addEventListener("click", (e) => {
        // Validation check: If no section is selected, show toast and exit
        if (!sectionDropdown.value) {
            window.showAjaxToast('Please select a section before adding a grade.', 'warning');
            return; 
        }

        // Logic to prepare the form for a new entry
        gradeForm.reset();
        document.getElementById("grade_id").value = "";
        document.getElementById("modalTitle").innerText = "Add New Grade";
        gradeForm.action = "{{ route('grades.store') }}"; 
        gradeForm.method = "POST";
        
        const selectedOption = sectionDropdown.options[sectionDropdown.selectedIndex];
        const isSss = selectedOption && selectedOption.dataset.short === "SSS";
        
        isWaecInput.value = currentGradingType === 'waec' ? '1' : '0';
        
        if (isSss) {
            gradingTypeAlert.classList.remove("d-none");
            modalGradingTypeText.textContent = currentGradingType === 'waec' ? 'WAEC' : 'custom';
            modalGradingTypeText.className = currentGradingType === 'waec' ? 'text-primary' : 'text-success';
        } else {
            gradingTypeAlert.classList.add("d-none");
        }

        // Only show the modal if the validation above passed
        gradeModal.show();
    });

    gradeForm.addEventListener("submit", function(e) {
        let id = document.getElementById("grade_id").value;
        
        if (id) {
            e.preventDefault();
            
            let url = `/grades/update/${id}`;
            let payload = {
                section_id: selectedSectionInput.value, 
                min_score: document.getElementById("min_score").value,
                max_score: document.getElementById("max_score").value,
                grade_letter: document.getElementById("grade_letter").value,
                remark: document.getElementById("remark").value,
                is_waec: isWaecInput.value
            };

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { 
                        throw new Error(err.error || 'Failed to update grade.'); 
                    });
                }
                return res.json();
            })
            .then(data => {
                if (data.error) { 
                    window.showAjaxToast(data.error, 'danger'); 
                    return; 
                } 
                fetchGrades();
                gradeModal.hide();
                if(data.message) window.showAjaxToast(data.message, 'success'); 
            })
            .catch(error => {
                window.showAjaxToast(error.message || 'An unknown error occurred during update.', 'danger'); 
            });
        }
    });

    document.addEventListener("click", e => {
        if (!e.target.closest(".editBtn")) return;
        const editBtn = e.target.closest(".editBtn");
        let id = editBtn.dataset.id;
        const section_id = sectionDropdown.value;
        
        fetch(`{{ route('grades.fetch') }}?section_id=${section_id}&type=${currentGradingType}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) throw new Error(data.error);
                
                let grade = (data.grades || []).find(g => g.id == id);
                if (!grade) {
                    window.showAjaxToast('Grade details not found in current filtered set.', 'warning'); 
                    return;
                }
                
                document.getElementById("grade_id").value = grade.id;
                document.getElementById("min_score").value = grade.min_score;
                document.getElementById("max_score").value = grade.max_score;
                document.getElementById("grade_letter").value = grade.grade_letter;
                document.getElementById("remark").value = grade.remark;
                isWaecInput.value = grade.is_waec ? '1' : '0';
                
                gradingTypeAlert.classList.add("d-none");
                
                document.getElementById("modalTitle").innerText = "Edit Grade";
                gradeForm.action = `/grades/update/${grade.id}`; 
                gradeForm.method = "POST"; 
                gradeModal.show();
            })
            .catch(error => window.showAjaxToast(error.message || 'Failed to load grade for editing.', 'danger')); 
    });

    document.addEventListener("click", e => {
        if (!e.target.closest(".deleteBtn")) return;
        const deleteBtn = e.target.closest(".deleteBtn");
        
        if (!confirm("Delete this grade? This action cannot be undone.")) return;

        let id = deleteBtn.dataset.id;
        
        fetch(`/grades/delete/${id}`, { 
            method: "DELETE",
            headers: { "X-CSRF-TOKEN": token }
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { 
                    throw new Error(err.error || 'Failed to delete grade.'); 
                });
            }
            return res.json();
        })
        .then(data => {
            if(data.success) {
                fetchGrades();
                window.showAjaxToast(data.message || 'Grade deleted successfully', 'success'); 
            } else {
                window.showAjaxToast(data.error || 'Could not delete grade.', 'danger'); 
            }
        })
        .catch(error => {
            window.showAjaxToast(error.message || 'An unknown error occurred during delete.', 'danger'); 
        });
    });

    if (sectionDropdown.value) {
        toggleGradingTypeSelector();
        fetchGrades();
    }
});
</script>
@endsection