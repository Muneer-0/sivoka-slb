/**
 * SiVOKA-SLB - Password Modal JavaScript
 * Menangani modal ganti password
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('changePasswordForm');
    const submitBtn = document.getElementById('submitPasswordBtn');
    const modal = document.getElementById('changePasswordModal');
    
    if (!form) {
        console.log('Form tidak ditemukan');
        return;
    }
    
    console.log('Password modal initialized');
    
    /**
     * Submit form dengan AJAX
     */
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        // Cari atau buat alert container
        let alertDiv = document.getElementById('passwordAlert');
        if (!alertDiv) {
            // Buat alert div jika belum ada
            alertDiv = document.createElement('div');
            alertDiv.id = 'passwordAlert';
            alertDiv.className = 'alert d-none';
            const modalBody = document.querySelector('#changePasswordModal .modal-body');
            if (modalBody) {
                modalBody.insertBefore(alertDiv, modalBody.firstChild);
            }
        }
        
        // Reset alert
        alertDiv.classList.add('d-none');
        alertDiv.classList.remove('alert-success', 'alert-danger');
        alertDiv.innerHTML = '';
        
        // Disable button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Memproses...';
        
        // Ambil data form
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const newPasswordConfirmation = document.getElementById('new_password_confirmation').value;
        
        console.log('Sending data...');
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: newPasswordConfirmation
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response data:', data);
            
            // Tampilkan alert
            alertDiv.classList.remove('d-none');
            
            if (data.success) {
                alertDiv.classList.add('alert-success');
                alertDiv.innerHTML = '<i class="bi bi-check-circle me-2"></i> ' + data.message;
                form.reset();
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan';
                
                // Tutup modal setelah 2 detik
                setTimeout(() => {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                }, 2000);
            } else {
                alertDiv.classList.add('alert-danger');
                let errorMsg = data.message || 'Terjadi kesalahan';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                alertDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i> ' + errorMsg;
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertDiv.classList.remove('d-none');
            alertDiv.classList.add('alert-danger');
            alertDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i> Terjadi kesalahan pada server';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan';
        });
    });
    
    // Reset form saat modal ditutup
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            console.log('Modal closed');
            form.reset();
            const alertDiv = document.getElementById('passwordAlert');
            if (alertDiv) {
                alertDiv.classList.add('d-none');
                alertDiv.classList.remove('alert-success', 'alert-danger');
                alertDiv.innerHTML = '';
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan';
        });
    }
});