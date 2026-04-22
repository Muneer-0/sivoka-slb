/**
 * SiVOKA-SLB - Monitoring JavaScript
 * Menangani filter dan interaksi di halaman monitoring
 */

document.addEventListener('DOMContentLoaded', function() {
    // Reset filter
    const resetBtn = document.getElementById('resetFilters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = window.location.pathname;
        });
    }
    
    // Auto submit ketika filter berubah (opsional)
    const autoSubmit = document.getElementById('autoSubmit');
    if (autoSubmit && autoSubmit.value === '1') {
        const selects = document.querySelectorAll('.filter-group select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                const form = this.closest('form');
                if (form) form.submit();
            });
        });
    }
    
    console.log('Monitoring page loaded');
});