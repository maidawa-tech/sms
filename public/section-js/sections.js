document.addEventListener('DOMContentLoaded', function () {
    const sectionForm = document.getElementById('sectionForm');
    const tableBody = document.getElementById('sectionTableBody');
    const modal = new bootstrap.Modal(document.getElementById('sectionModal'));
    const alertContainer = document.getElementById('alert-container');
    const csrf = document.querySelector('input[name="_token"]').value;
    const saveButton = sectionForm.querySelector('button[type="submit"]');

    // Show alert inside the page (Bootstrap)
    function showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show mt-2`;
        alert.innerHTML = `
            <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.innerHTML = ''; // clear previous alerts
        alertContainer.appendChild(alert);

        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    }

    // Add fade animation to table rows
    function fadeInRow(row) {
        row.style.opacity = 0;
        row.style.transition = 'opacity 0.5s ease';
        requestAnimationFrame(() => (row.style.opacity = 1));
    }

    // Add or Update Section
    sectionForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const id = document.getElementById('section_id').value;
        const url = id ? `/sections/${id}` : '/sections';
        const method = id ? 'PUT' : 'POST';

        // Show spinner in Save button
        saveButton.disabled = true;
        const originalText = saveButton.innerHTML;
        saveButton.innerHTML = `<i class="fa fa-spinner fa-spin me-2"></i> Saving...`;

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({
                section_name: document.getElementById('section_name').value,
                section_short_name: document.getElementById('section_short_name').value,
            }),
        })
        .then(res => res.json())
        .then(data => {
            saveButton.disabled = false;
            saveButton.innerHTML = originalText;

            if (data.status === 'success') {
                modal.hide();
                showAlert(data.message, 'success');

                if (id) {
                    // Update existing row
                    const row = document.getElementById(`row-${id}`);
                    row.children[1].textContent = data.section.section_name;
                    row.children[2].textContent = data.section.section_short_name;
                    fadeInRow(row);
                } else {
                    // Add new row
                    const newRow = document.createElement('tr');
                    newRow.id = `row-${data.section.id}`;
                    newRow.innerHTML = `
                        <td>${tableBody.children.length + 1}</td>
                        <td>${data.section.section_name}</td>
                        <td>${data.section.section_short_name}</td>
                        <td>
                            <button class="btn btn-sm btn-primary editBtn" data-id="${data.section.id}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${data.section.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>`;
                    tableBody.appendChild(newRow);
                    fadeInRow(newRow);
                }

                sectionForm.reset();
                document.getElementById('section_id').value = '';
            } else {
                showAlert('Something went wrong. Please try again.', 'danger');
            }
        })
        .catch(() => {
            saveButton.disabled = false;
            saveButton.innerHTML = originalText;
            showAlert('Server error occurred. Try again.', 'danger');
        });
    });

    // Edit Section
    document.addEventListener('click', function (e) {
        if (e.target.closest('.editBtn')) {
            const btn = e.target.closest('.editBtn');
            const row = document.getElementById(`row-${btn.dataset.id}`);

            document.getElementById('section_id').value = btn.dataset.id;
            document.getElementById('section_name').value = row.children[1].textContent.trim();
            document.getElementById('section_short_name').value = row.children[2].textContent.trim();
            document.querySelector('.modal-title').textContent = 'Edit Section';
            modal.show();
        }
    });

    // Delete Section
    document.addEventListener('click', function (e) {
        if (e.target.closest('.deleteBtn')) {
            if (!confirm('Are you sure you want to delete this section?')) return;

            const btn = e.target.closest('.deleteBtn');
            fetch(`/sections/${btn.dataset.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf },
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const row = document.getElementById(`row-${btn.dataset.id}`);
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = 0;
                    setTimeout(() => row.remove(), 300);
                    showAlert(data.message, 'success');
                } else {
                    showAlert('Unable to delete section.', 'danger');
                }
            });
        }
    });

    // Reset modal on close
    document.getElementById('sectionModal').addEventListener('hidden.bs.modal', function () {
        sectionForm.reset();
        document.querySelector('.modal-title').textContent = 'Add Section';
        document.getElementById('section_id').value = '';
        saveButton.disabled = false;
        saveButton.innerHTML = `<i class="fa fa-save me-1"></i> Save`;
    });
});
