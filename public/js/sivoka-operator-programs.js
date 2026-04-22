/**
 * SiVOKA-SLB - Operator Programs JavaScript
 * Menangani modal tambah kategori dan export
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== EKSPOR EXCEL & PDF =====
    window.exportToExcel = function() {
        window.location.href = '/operator/programs/export-excel';
    }
    
    window.exportToPDF = function() {
        window.location.href = '/operator/programs/export-pdf';
    }
    
    // ===== MODAL TAMBAH KATEGORI LOKAL =====
    const saveCategoryBtn = document.getElementById('saveCategoryBtn');
    const categorySelect = document.getElementById('category_id');
    const newCategoryName = document.getElementById('new_category_name');
    const newCategoryDesc = document.getElementById('new_category_description');
    
    if (saveCategoryBtn) {
        saveCategoryBtn.addEventListener('click', function() {
            const name = newCategoryName ? newCategoryName.value.trim() : '';
            const description = newCategoryDesc ? newCategoryDesc.value.trim() : '';
            
            if (!name) {
                showAlert('Nama kategori wajib diisi!', 'warning');
                if (newCategoryName) newCategoryName.focus();
                return;
            }
            
            saveCategoryBtn.disabled = true;
            saveCategoryBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menyimpan...';
            
            fetch('/categories/store-local', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name: name,
                    description: description
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tambahkan opsi ke dropdown
                    const option = document.createElement('option');
                    option.value = data.id;
                    option.textContent = data.name + ' (Lokal)';
                    option.setAttribute('data-is-global', '0');
                    if (categorySelect) {
                        categorySelect.appendChild(option);
                        option.selected = true;
                    }
                    
                    // Reset form
                    if (newCategoryName) newCategoryName.value = '';
                    if (newCategoryDesc) newCategoryDesc.value = '';
                    
                    // Tutup modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newCategoryModal'));
                    if (modal) modal.hide();
                    
                    // Tampilkan notifikasi sukses
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.error || 'Terjadi kesalahan', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan pada server', 'danger');
            })
            .finally(() => {
                saveCategoryBtn.disabled = false;
                saveCategoryBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Kategori';
            });
        });
    }
    
    // Reset modal saat ditutup
    const newCategoryModal = document.getElementById('newCategoryModal');
    if (newCategoryModal) {
        newCategoryModal.addEventListener('hidden.bs.modal', function() {
            if (newCategoryName) newCategoryName.value = '';
            if (newCategoryDesc) newCategoryDesc.value = '';
        });
    }
    
    // ===== FUNGSI ALERT =====
    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.style.transition = 'opacity 0.5s';
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 500);
        }, 3000);
    }
});