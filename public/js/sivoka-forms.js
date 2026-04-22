/**
 * SiVOKA-SLB Form Validation
 * Menangani validasi form dan input
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Validasi NPSN (harus 8 digit angka)
    const npsnInputs = document.querySelectorAll('input[name="npsn"]');
    npsnInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 8) {
                this.value = this.value.slice(0, 8);
            }
        });
    });
    
    // Validasi latitude (-90 sampai 90)
    const latInputs = document.querySelectorAll('input[name="latitude"]');
    latInputs.forEach(input => {
        input.addEventListener('blur', function() {
            let val = parseFloat(this.value);
            if (isNaN(val)) return;
            if (val < -90) this.value = -90;
            if (val > 90) this.value = 90;
        });
    });
    
    // Validasi longitude (-180 sampai 180)
    const lngInputs = document.querySelectorAll('input[name="longitude"]');
    lngInputs.forEach(input => {
        input.addEventListener('blur', function() {
            let val = parseFloat(this.value);
            if (isNaN(val)) return;
            if (val < -180) this.value = -180;
            if (val > 180) this.value = 180;
        });
    });
    
    // Confirm delete dengan modal Bootstrap
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm(this.dataset.confirm || 'Yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });
});