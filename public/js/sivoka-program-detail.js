/**
 * SiVOKA-SLB - Program Detail JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto close alert
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    }
    
    console.log('✅ Halaman detail program siap');
});