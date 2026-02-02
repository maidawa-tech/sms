@extends('layouts.dashboard')

@section('title', 'Marks Setting')

@section('content')
<style>
    /* Custom style for the select dropdown */
    .form-control-custom-border {
        border-color: #679767 !important; /* Apply the border color */
    }

    /* Style for when the select dropdown is focused (clicked/tabbed into) */
    .form-control-custom-border:focus {
        border-color: #679767 !important; /* Keep the color */
        box-shadow: 0 0 0 0.25rem rgba(103, 151, 103, 0.25) !important; /* Custom focus ring (optional, but good practice) */
        border-width: 2px !important; /* Make the border bolder (e.g., 2px) */
    }
</style>

<div class="container-fluid mt-3">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center"
            style="background-color: #fff; border-bottom: 1px solid #dee2e6;">
            
            <h5 class="fw-bold mb-0" style="color:#679767;">
                <i class="fa fa-gear me-1"></i> Marks Setting
            </h5>

            <button class="btn btn-success" onclick="openAddModal()">
                <i class="fa fa-plus"></i> Add
            </button>
        </div>

        <div class="card-body">

            {{-- Section Dropdown --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="fw-bold">Select Section</label>
                    {{-- ADDED: form-control-custom-border class to apply the style --}}
                    <select id="section_id" class="form-control form-control-custom-border" onchange="loadSettings()">
                        <option value="">-- Select Section --</option>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->section_id }}">{{ $sec->section_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table class="table table-bordered" id="marksTable">
                <thead class="table-light">
                    <tr>
                        <th>MAX CA1</th>
                        <th>MAX CA2</th>
                        <th>MAX Exam</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>

</div>

{{-- Modal --}}
<div class="modal fade" id="marksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTitle">Add Marks Setting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="marksForm">
                    @csrf
                    <input type="hidden" id="edit_id">
                    <input type="hidden" id="section_id_hidden">

                    <label class="mt-2">CA1 Max</label>
                    <input type="number" min="0" max="100" id="max_ca1" class="form-control" required>

                    <label class="mt-2">CA2 Max</label>
                    <input type="number" min="0" max="100" id="max_ca2" class="form-control" required>

                    <label class="mt-2">Exam Max</label>
                    <input type="number" min="0" max="100" id="max_exam" class="form-control" required>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success" onclick="saveMarksSetting()">Save</button>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
/* ===========================================
    TOAST NOTIFICATION
=========================================== */
function showToast(message, type = 'success') {
    let bg = {
        success: "text-bg-success",
        error: "text-bg-danger",
        warning: "text-bg-warning",
        info: "text-bg-info"
    }[type] || "text-bg-primary";

    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = "toast-container position-fixed bottom-0 end-0 p-3";
        document.body.appendChild(container);
    }

    let toastHtml = `
        <div class="toast align-items-center ${bg} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', toastHtml);

    let newToast = container.lastElementChild;
    new bootstrap.Toast(newToast, { delay: 5000 }).show();
}

/* ===========================================
    LOAD SETTINGS BY SECTION
=========================================== */
function loadSettings() {
    let sectionId = document.getElementById("section_id").value;

    if (!sectionId) {
        document.querySelector("#marksTable tbody").innerHTML =
            "<tr><td colspan='4' class='text-center text-muted'>Select a section</td></tr>";
        return;
    }

    fetch(`/marks-setting/fetch/${sectionId}`)
        .then(res => res.json())
        .then(data => {
            let tbody = document.querySelector("#marksTable tbody");

            if (!data.data.length) {
                tbody.innerHTML = "<tr><td colspan='4' class='text-center text-muted'>No record found</td></tr>";
                return;
            }

            tbody.innerHTML = data.data.map(row => `
                <tr>
                    <td>${row.max_ca1}</td>
                    <td>${row.max_ca2}</td>
                    <td>${row.max_exam}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-success"
                                onclick="openEditModal(${row.id}, ${row.max_ca1}, ${row.max_ca2}, ${row.max_exam})">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteSetting(${row.id})">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>`).join('');
        });
}

/* ===========================================
    MODAL HANDLING
=========================================== */
function openAddModal() {
    let sectionId = document.getElementById("section_id").value;
    if (!sectionId) { showToast("Please select a section first", "warning"); return; }

    document.getElementById("modalTitle").innerText = "Add Marks Setting";
    document.getElementById("marksForm").reset();
    document.getElementById("edit_id").value = "";
    document.getElementById("section_id_hidden").value = sectionId;

    new bootstrap.Modal(document.getElementById("marksModal")).show();
}

function openEditModal(id, ca1, ca2, exam) {
    document.getElementById("modalTitle").innerText = "Edit Marks Setting";
    document.getElementById("edit_id").value = id;
    document.getElementById("max_ca1").value = ca1;
    document.getElementById("max_ca2").value = ca2;
    document.getElementById("max_exam").value = exam;
    document.getElementById("section_id_hidden").value = document.getElementById("section_id").value;

    new bootstrap.Modal(document.getElementById("marksModal")).show();
}

/* ===========================================
    SAVE OR UPDATE
    -- Handles validation errors gracefully
=========================================== */
function saveMarksSetting() {
    let id = document.getElementById("edit_id").value;
    let url = id ? `/marks-setting/update/${id}` : `/marks-setting/store`;

    let formData = new FormData();
    formData.append("section_id", document.getElementById("section_id_hidden").value);
    formData.append("max_ca1", document.getElementById("max_ca1").value);
    formData.append("max_ca2", document.getElementById("max_ca2").value);
    formData.append("max_exam", document.getElementById("max_exam").value);
    formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

    if (id) formData.append("_method", "PUT"); // Method spoofing

    fetch(url, { method: "POST", body: formData })
        .then(async res => {
            let data;
            try { data = await res.json(); } 
            catch { throw new Error("Unexpected server response. Please check your input."); }

            if (!res.ok) {
                if (data.errors) {
                    let messages = Object.values(data.errors).flat().join(" ");
                    throw new Error(messages);
                }
                throw new Error(data.message || "Something went wrong.");
            }

            return data;
        })
        .then(res => {
            showToast(res.message, res.status);
            loadSettings();
            bootstrap.Modal.getInstance(document.getElementById("marksModal")).hide();
        })
        .catch(err => showToast(err.message, "error"));
}

/* ===========================================
    DELETE
=========================================== */
function deleteSetting(id) {
    if (!confirm("Delete this record?")) return;

    fetch(`/marks-setting/delete/${id}`, {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(res => res.json())
    .then(res => {
        showToast(res.message, res.status);
        loadSettings();
    })
    .catch(() => showToast("Failed to delete record", "error"));
}
</script>
@endsection