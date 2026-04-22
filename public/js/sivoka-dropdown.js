/**
 * SiVOKA-SLB User Dropdown
 * Menangani dropdown user di navbar
 */

document.addEventListener('DOMContentLoaded', function() {
    
    const userDropdown = document.getElementById('userDropdown');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    if (!userDropdown || !userDropdownMenu) return;
    
    // Toggle dropdown
    userDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        if (userDropdownMenu.style.display === 'none' || userDropdownMenu.style.display === '') {
            userDropdownMenu.style.display = 'block';
        } else {
            userDropdownMenu.style.display = 'none';
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!userDropdown.contains(event.target) && !userDropdownMenu.contains(event.target)) {
            userDropdownMenu.style.display = 'none';
        }
    });
    
    // Close dropdown when pressing ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            userDropdownMenu.style.display = 'none';
        }
    });
});