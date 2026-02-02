@extends('layouts.dashboard')

@section('title', 'Final Average Comments')

@section('content')
<div class="container-fluid mt-4">

    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1" style="color: #2c5282;">
                        <i class="fas fa-comment-alt me-2"></i>Final Average Comments
                    </h4>
                    <p class="text-muted mb-0">Manage score-based comments for final evaluations</p>
                </div>
                <button class="btn btn-success d-flex align-items-center gap-2 px-4 py-2"
                        data-bs-toggle="modal"
                        data-bs-target="#commentModal"
                        onclick="resetForm()">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Comment</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="60">#</th>
                            <th>Score Range</th>
                            <th>Comment</th>
                            <th width="100">Status</th>
                            <th width="120" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="commentsTable">
                        <!-- Loading State -->
                        <tr id="loadingRow">
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Empty State (Initially Hidden) -->
            <div id="emptyState" class="text-center py-5" style="display: none;">
                <div class="mb-3">
                    <i class="fas fa-comment-alt fa-3x text-light" style="color: #e2e8f0;"></i>
                </div>
                <h5 class="text-muted mb-2">No comments yet</h5>
                <p class="text-muted mb-4">Add your first comment to get started</p>
                <button class="btn btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#commentModal"
                        onclick="resetForm()">
                    <i class="fas fa-plus me-2"></i>Add Comment
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="commentForm" class="modal-content border-0 shadow-lg">
            @csrf
            <input type="hidden" id="comment_id">
            
            <div class="modal-header bg-primary text-white border-0 rounded-top">
                <h5 class="modal-title" id="commentModalLabel">
                    <i class="fas fa-comment-alt me-2"></i>
                    <span id="modalTitle">Add Final Average Comment</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="row g-3">
                    <!-- Score Range -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calculator me-1 text-primary"></i>
                            Min Score
                        </label>
                        <input type="number" step="0.01" min="0" max="100" 
                               class="form-control border-1 border-primary-subtle"
                               id="min_score" required
                               placeholder="e.g., 0.00">
                        <small class="form-text text-muted">Minimum score for this comment</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calculator me-1 text-primary"></i>
                            Max Score
                        </label>
                        <input type="number" step="0.01" min="0" max="100"
                               class="form-control border-1 border-primary-subtle"
                               id="max_score" required
                               placeholder="e.g., 100.00">
                        <small class="form-text text-muted">Maximum score for this comment</small>
                    </div>
                    
                    <!-- Comment -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-comment me-1 text-primary"></i>
                            Comment
                        </label>
                        <input type="text" 
                               class="form-control border-1 border-primary-subtle"
                               id="comment" required
                               placeholder="Enter comment text">
                        <small class="form-text text-muted">Evaluation comment to display</small>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-top-0 bg-light rounded-bottom">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i>
                    <span id="submitBtnText">Save Comment</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('commentsTable');
    const emptyState = document.getElementById('emptyState');
    const loadingRow = document.getElementById('loadingRow');
    const modalEl = document.getElementById('commentModal');
    const modal = new bootstrap.Modal(modalEl);
    const modalTitle = document.getElementById('modalTitle');
    const submitBtnText = document.getElementById('submitBtnText');
    const minScoreInput = document.getElementById('min_score');
    const maxScoreInput = document.getElementById('max_score');

    /* ================= TOAST ================= */
    function showToast(type, message) {
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0 shadow-sm`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>`;
        toastContainer.appendChild(toast);
        new bootstrap.Toast(toast, { delay: 3000 }).show();
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }

    /* ================= VALIDATION ================= */
    function validateScoreRange() {
        const minScore = parseFloat(minScoreInput.value);
        const maxScore = parseFloat(maxScoreInput.value);

        if (minScore > maxScore) {
            minScoreInput.classList.add('is-invalid');
            maxScoreInput.classList.add('is-invalid');
            return false;
        }

        minScoreInput.classList.remove('is-invalid');
        maxScoreInput.classList.remove('is-invalid');
        return true;
    }

    [minScoreInput, maxScoreInput].forEach(input => input.addEventListener('blur', validateScoreRange));

    /* ================= FETCH COMMENTS ================= */
    function fetchComments() {
        fetch('/final-average-comments/fetch')
            .then(res => res.json())
            .then(data => {
                table.innerHTML = '';

                if (data.length === 0) {
                    emptyState.style.display = 'block';
                    return;
                }

                emptyState.style.display = 'none';

                data.forEach((row, i) => {
                    const scoreRange = `${row.min_score} - ${row.max_score}`;
                    const rowHTML = `
                    <tr class="hover-shadow-sm">
                        <td class="ps-4 fw-semibold text-muted">${i + 1}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary-subtle text-primary me-2">
                                    ${scoreRange}
                                </span>
                                <small class="text-muted">
                                    ${row.min_score === 0 ? 'Starting' : ''}${row.max_score === 100 ? 'Maximum' : ''}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-comment text-primary me-2"></i>
                                <span class="text-break">${row.comment}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill d-flex align-items-center gap-1 w-100 justify-content-center ${row.is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'}">
                                <i class="fas fa-circle ${row.is_active ? 'text-success' : 'text-secondary'}" style="font-size: 8px;"></i>
                                ${row.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary border-end-0 rounded-start"
                                        onclick='editComment(${JSON.stringify(row)})'
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger rounded-end"
                                        onclick="deleteComment(${row.id})"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                    table.insertAdjacentHTML('beforeend', rowHTML);
                });
            })
            .catch(error => {
                console.error('Error fetching comments:', error);
                showToast('danger', 'Failed to load comments');
            })
            .finally(() => {
                if (loadingRow) loadingRow.style.display = 'none';
            });
    }

    fetchComments();

    /* ================= CREATE / UPDATE ================= */
    document.getElementById('commentForm').addEventListener('submit', e => {
        e.preventDefault();

        if (!validateScoreRange()) {
            showToast('warning', 'Min score cannot be greater than max score');
            return;
        }

        const id = document.getElementById('comment_id').value;
        const url = id ? `/final-average-comments/update/${id}` : `/final-average-comments/store`;
        const method = id ? 'PUT' : 'POST';

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const btnIcon = submitBtn.querySelector('i');
        const btnText = submitBtn.querySelector('#submitBtnText');

        // Show spinner and saving text
        btnIcon.className = 'fas fa-spinner fa-spin me-2';
        btnText.textContent = 'Saving...';
        submitBtn.disabled = true;

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                min_score: minScoreInput.value,
                max_score: maxScoreInput.value,
                comment: document.getElementById('comment').value
            })
        })
        .then(res => res.json())
        .then(res => {
            showToast(res.type, res.message);
            modal.hide();
            resetForm();
            fetchComments();
        })
        .catch(error => {
            console.error('Error saving comment:', error);
            showToast('danger', 'Failed to save comment');
        })
        .finally(() => {
            btnIcon.className = 'fas fa-save me-2';
            btnText.textContent = document.getElementById('comment_id').value ? 'Update Comment' : 'Save Comment';
            submitBtn.disabled = false;
        });
    });

    /* ================= EDIT ================= */
    window.editComment = (row) => {
        document.getElementById('comment_id').value = row.id;
        document.getElementById('min_score').value = row.min_score;
        document.getElementById('max_score').value = row.max_score;
        document.getElementById('comment').value = row.comment;

        modalTitle.textContent = 'Edit Comment';
        submitBtnText.textContent = 'Update Comment';
        modal.show();
    };

    /* ================= DELETE ================= */
    window.deleteComment = (id) => {
        if (!confirm('Are you sure you want to delete this comment?\nThis action cannot be undone.')) return;

        fetch(`/final-average-comments/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(res => res.json())
        .then(res => {
            showToast(res.type, res.message);
            fetchComments();
        })
        .catch(error => {
            console.error('Error deleting comment:', error);
            showToast('danger', 'Failed to delete comment');
        });
    };

    /* ================= RESET FORM ================= */
    window.resetForm = () => {
        document.getElementById('comment_id').value = '';
        document.getElementById('commentForm').reset();
        modalTitle.textContent = 'Add Final Average Comment';
        submitBtnText.textContent = 'Save Comment';
        [minScoreInput, maxScoreInput].forEach(input => input.classList.remove('is-invalid'));
    };

    /* ================= OPEN MODAL FOR ADD ================= */
    window.openAddCommentModal = () => {
        resetForm();
        modal.show();
    };

    // Reset form when modal closes
    modalEl.addEventListener('hidden.bs.modal', resetForm);
});
</script>

<style>
.hover-shadow-sm:hover {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    transition: box-shadow 0.2s ease-in-out;
}

.border-primary-subtle {
    border-color: #e2e8f0 !important;
}

.bg-primary-subtle {
    background-color: #f7fafc !important;
}

.table-hover tbody tr:hover {
    background-color: #f8fafc;
}

.btn-outline-primary:hover, .btn-outline-danger:hover {
    transform: translateY(-1px);
    transition: transform 0.2s;
}

.modal-content {
    border: none;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #4a5568;
}

.badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
}
</style>
@endsection