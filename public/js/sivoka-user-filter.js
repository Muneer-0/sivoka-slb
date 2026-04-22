/**
 * SiVOKA-SLB - User Filter JavaScript
 * Menangani filter di halaman Manajemen User
 */

document.addEventListener('DOMContentLoaded', function() {
    // Reset filter button
    const resetBtn = document.getElementById('resetFilters');
    
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = window.location.pathname;
        });
    }
    
    console.log('User filter initialized');
});