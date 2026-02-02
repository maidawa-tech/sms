@extends('layouts.dashboard')

@section('title', 'Trait Management')

@section('content')
<div class="container-fluid mt-3">

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color:#679767;">
                <i class="fa-solid fa-list-check me-2"></i>
                Affective & Psychomotor Traits
            </h5>
            <button class="btn btn-success btn-sm" id="addTraitBtn">
                <i class="fa fa-plus"></i> Add Trait
            </button>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Trait Name</th>
                        <th>Trait Type</th>
                        <th>Status</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody id="traitsTable">
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Loading traits...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Trait Modal -->
<div class="modal fade" id="traitModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="traitForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="traitModalTitle">Add Trait</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="traitId">

                <div class="mb-3">
                    <label class="form-label">Trait Name</label>
                    <input type="text" class="form-control" id="traitName" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trait Type</label>
                    <select class="form-select" id="traitType" required>
                        <option value="">-- Select Type --</option>
                        <option value="affective">Affective</option>
                        <option value="psychomotor">Psychomotor</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" id="saveTraitBtn" type="submit">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const table = document.getElementById('traitsTable');
const modal = new bootstrap.Modal(document.getElementById('traitModal'));
const form = document.getElementById('traitForm');
const csrf = document.querySelector('meta[name="csrf-token"]').content;

let editId = null;

/* ---------------- TOAST FUNCTION ---------------- */
function showToast(message, type = 'success') {
    const container = document.querySelector('.toast-container');

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    toastEl.setAttribute('role', 'alert');

    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"></button>
        </div>
    `;

    container.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

/* ---------------- LOAD TRAITS ---------------- */
function loadTraits() {
    fetch("{{ route('admin.traits.list') }}")
        .then(res => res.json())
        .then(data => {
            table.innerHTML = '';

            if (data.length === 0) {
                table.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No traits added yet
                        </td>
                    </tr>`;
                return;
            }

            data.forEach((t, i) => {
                table.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${t.trait_name}</td>
                        <td class="text-capitalize">${t.trait_type}</td>
                        <td>
                            <span class="badge ${t.is_active ? 'bg-success' : 'bg-danger'}">
                                ${t.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary"
                                onclick="editTrait(${t.id}, '${t.trait_name}', '${t.trait_type}')">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-warning"
                                onclick="toggleTrait(${t.id})">
                                Toggle
                            </button>
                            <button class="btn btn-sm btn-danger"
                                onclick="deleteTrait(${t.id})">
                                Delete
                            </button>
                        </td>
                    </tr>`;
            });
        });
}

loadTraits();

/* ---------------- ADD TRAIT ---------------- */
document.getElementById('addTraitBtn').onclick = () => {
    editId = null;
    form.reset();
    document.getElementById('traitModalTitle').innerText = 'Add Trait';
    document.getElementById('saveTraitBtn').innerText = 'Save';
    modal.show();
};

/* ---------------- EDIT TRAIT ---------------- */
function editTrait(id, name, type) {
    editId = id;
    document.getElementById('traitName').value = name;
    document.getElementById('traitType').value = type;
    document.getElementById('traitModalTitle').innerText = 'Edit Trait';
    document.getElementById('saveTraitBtn').innerText = 'Update';
    modal.show();
}

/* ---------------- SAVE / UPDATE ---------------- */
form.onsubmit = e => {
    e.preventDefault();

    const url = editId
        ? `/admin/traits/${editId}`
        : "{{ route('admin.traits.store') }}";

    fetch(url, {
        method: editId ? 'PUT' : 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            trait_name: traitName.value,
            trait_type: traitType.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.errors) {
            Object.values(data.errors).forEach(err =>
                showToast(err[0], 'danger')
            );
            return;
        }

        showToast(data.message, 'success');
        modal.hide();
        loadTraits();
    })
    .catch(() => showToast('Something went wrong', 'danger'));
};

/* ---------------- TOGGLE STATUS ---------------- */
function toggleTrait(id) {
    fetch(`/admin/traits/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': csrf }
    })
    .then(res => res.json())
    .then(data => {
        showToast(data.message, 'info');
        loadTraits();
    });
}

/* ---------------- DELETE ---------------- */
function deleteTrait(id) {
    if (!confirm('Delete this trait?')) return;

    fetch(`/admin/traits/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf }
    })
    .then(res => res.json())
    .then(data => {
        showToast(data.message, 'danger');
        loadTraits();
    });
}
</script>
@endsection
