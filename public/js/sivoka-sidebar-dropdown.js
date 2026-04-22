/**
 * SiVOKA-SLB - Sidebar Dropdown JavaScript
 * Menangani dropdown menu di sidebar
 */

document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * Setup dropdown sidebar untuk semua role
     */
    function setupSidebarDropdowns() {
        // Cari semua elemen yang memiliki dropdown
        const dropdownContainers = document.querySelectorAll('.has-dropdown');
        
        dropdownContainers.forEach(container => {
            // Cari tombol toggle (menu parent)
            const toggleButton = container.querySelector(':scope > .menu-item');
            const submenu = container.querySelector(':scope > .dropdown-submenu');
            const icon = toggleButton ? toggleButton.querySelector('.dropdown-icon') : null;
            
            if (!toggleButton || !submenu) return;
            
            // Hapus event listener lama dengan clone & replace
            const newToggle = toggleButton.cloneNode(true);
            toggleButton.parentNode.replaceChild(newToggle, toggleButton);
            
            // Update referensi
            const newButton = container.querySelector(':scope > .menu-item');
            const newIcon = newButton ? newButton.querySelector('.dropdown-icon') : null;
            
            // Pastikan href="#" tidak menyebabkan page scroll
            newButton.setAttribute('href', 'javascript:void(0)');
            
            // Tambah event listener
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Tutup semua dropdown lain yang terbuka
                document.querySelectorAll('.has-dropdown').forEach(other => {
                    if (other !== container) {
                        const otherSubmenu = other.querySelector(':scope > .dropdown-submenu');
                        const otherIcon = other.querySelector(':scope > .menu-item .dropdown-icon');
                        
                        if (otherSubmenu && otherSubmenu.classList.contains('show')) {
                            otherSubmenu.classList.remove('show');
                            other.classList.remove('active');
                            if (otherIcon) {
                                otherIcon.style.transform = 'rotate(0deg)';
                                otherIcon.style.transition = 'transform 0.3s ease';
                            }
                        }
                    }
                });
                
                // Toggle dropdown ini
                if (submenu.classList.contains('show')) {
                    submenu.classList.remove('show');
                    container.classList.remove('active');
                    if (newIcon) {
                        newIcon.style.transform = 'rotate(0deg)';
                    }
                } else {
                    submenu.classList.add('show');
                    container.classList.add('active');
                    if (newIcon) {
                        newIcon.style.transform = 'rotate(180deg)';
                    }
                }
            });
        });
        
        // Buka dropdown yang sesuai dengan halaman aktif
        openActiveDropdowns();
    }
    
    /**
     * Buka dropdown otomatis jika ada menu aktif di dalamnya
     */
    function openActiveDropdowns() {
        document.querySelectorAll('.dropdown-submenu').forEach(submenu => {
            // Cek apakah ada menu aktif di dalam submenu
            const activeItem = submenu.querySelector('.menu-item.active');
            if (activeItem) {
                const parentContainer = submenu.closest('.has-dropdown');
                if (parentContainer) {
                    const submenuElement = parentContainer.querySelector(':scope > .dropdown-submenu');
                    const icon = parentContainer.querySelector(':scope > .menu-item .dropdown-icon');
                    
                    parentContainer.classList.add('active');
                    if (submenuElement) {
                        submenuElement.classList.add('show');
                    }
                    if (icon) {
                        icon.style.transform = 'rotate(180deg)';
                        icon.style.transition = 'transform 0.3s ease';
                    }
                }
            }
        });
    }
    
    /**
     * Reset semua dropdown (tutup semua)
     */
    function resetAllDropdowns() {
        document.querySelectorAll('.has-dropdown').forEach(container => {
            const submenu = container.querySelector(':scope > .dropdown-submenu');
            const icon = container.querySelector(':scope > .menu-item .dropdown-icon');
            
            container.classList.remove('active');
            if (submenu) {
                submenu.classList.remove('show');
            }
            if (icon) {
                icon.style.transform = 'rotate(0deg)';
            }
        });
    }
    
    /**
     * Inisialisasi ulang sidebar (dipanggil setelah AJAX atau perubahan DOM)
     */
    function reinitSidebar() {
        resetAllDropdowns();
        setupSidebarDropdowns();
    }
    
    // Jalankan inisialisasi
    setupSidebarDropdowns();
    
    // Event listener untuk toggle sidebar (mobile)
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('show');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
        });
    }
    
    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.remove('show');
            if (sidebarOverlay) sidebarOverlay.classList.remove('show');
        });
    }
    
    if (sidebarOverlay && sidebar) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }
    
    // Ekspor fungsi untuk digunakan di tempat lain
    window.sidebarDropdown = {
        reset: resetAllDropdowns,
        refresh: setupSidebarDropdowns,
        reinit: reinitSidebar
    };
});