/**
 * SiVOKA-SLB Core JavaScript
 * Menangani fungsi-fungsi dasar aplikasi
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // SIDEBAR TOGGLE (MOBILE)
    // ========================================
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    const body = document.body;
    
    // Fungsi untuk membuka sidebar
    function openSidebar() {
        if (sidebar) {
            sidebar.classList.add('active');
            if (overlay) overlay.classList.add('active');
            body.classList.add('sidebar-open');
            body.style.overflow = 'hidden'; // Prevent scroll
        }
    }
    
    // Fungsi untuk menutup sidebar
    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            body.classList.remove('sidebar-open');
            body.style.overflow = ''; // Restore scroll
        }
    }
    
    // Fungsi untuk toggle sidebar
    function toggleSidebar() {
        if (sidebar && sidebar.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }
    
    // Event listener untuk tombol toggle
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // Event listener untuk tombol close
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeSidebar();
        });
    }
    
    // Event listener untuk overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            closeSidebar();
        });
    }
    
    // Tutup sidebar saat klik menu item di mobile (HANYA UNTUK LINK BIASA, BUKAN DROPDOWN)
    const menuItems = document.querySelectorAll('.menu-item:not(.has-dropdown > .menu-item)');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                // Jangan tutup jika ini adalah dropdown toggle
                const isDropdownToggle = this.closest('.has-dropdown') && this === this.closest('.has-dropdown').querySelector(':scope > .menu-item');
                if (!isDropdownToggle) {
                    closeSidebar();
                }
            }
        });
    });
    
    // Handle resize window - tutup sidebar saat resize ke desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
    
    // ========================================
    // AUTO CLOSE ALERT
    // ========================================
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);
    
    // ========================================
    // CONFIRM DELETE
    // ========================================
    document.querySelectorAll('.delete-confirm').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Yakin ingin menghapus data ini?')) {
                this.closest('form').submit();
            }
        });
    });
});