/**
 * SiVOKA-SLB - Action Icons JavaScript
 * Menangani tooltip dan konfirmasi untuk action icons
 */

document.addEventListener('DOMContentLoaded', function() {
    // Tooltip untuk action icons (jika ada)
    const actionIcons = document.querySelectorAll('.btn-icon');
    actionIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            const title = this.getAttribute('title');
            if (title && typeof bootstrap !== 'undefined') {
                // Bootstrap tooltip bisa ditambahkan di sini
            }
        });
    });
    
    console.log('Action icons initialized');
});