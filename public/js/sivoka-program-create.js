/**
 * SiVOKA-SLB - Program Create JavaScript
 * Menangani modal tambah kategori lokal
 */

document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.getElementById('saveCategoryBtn');
    const categorySelect = document.getElementById('category_id');
    const nameInput = document.getElementById('new_category_name');
    const descInput = document.getElementById('new_category_description');
    
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const name = nameInput ? nameInput.value.trim() : '';
            const description = descInput ? descInput.value.trim() : '';
            
            if (!name) {
                alert('Nama kategori wajib diisi!');
                if (nameInput) nameInput.focus();
                return;
            }
            
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menyimpan...';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token tidak ditemukan. Refresh halaman!');
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Kategori';
                return;
            }
            
            // Gunakan URL absolute
            const url = window.location.origin + '/categories/store-local';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    name: name,
                    description: description
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
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
                    if (nameInput) nameInput.value = '';
                    if (descInput) descInput.value = '';
                    
                    // Tutup modal
                    const modalElement = document.getElementById('newCategoryModal');
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    }
                    
                    alert('✅ ' + data.message);
                } else {
                    let errorMsg = data.error;
                    if (typeof errorMsg === 'object') {
                        errorMsg = Object.values(errorMsg).flat().join(', ');
                    }
                    alert('❌ Gagal: ' + errorMsg);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Terjadi kesalahan: ' + (error.error || error.message || 'Unknown error'));
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Kategori';
            });
        });
    }
    
    // Reset modal saat ditutup
    const newCategoryModal = document.getElementById('newCategoryModal');
    if (newCategoryModal) {
        newCategoryModal.addEventListener('hidden.bs.modal', function() {
            if (nameInput) nameInput.value = '';
            if (descInput) descInput.value = '';
        });
    }
});