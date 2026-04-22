/**
 * SiVOKA-SLB Modal
 * Menangani modal untuk detail sekolah
 */

document.addEventListener('DOMContentLoaded', function() {
    
    const schoolModal = document.getElementById('schoolModal');
    if (!schoolModal) return;
    
    const modalTitle = document.getElementById('schoolModalTitle');
    const modalBody = document.getElementById('schoolModalBody');
    const modalDetailLink = document.getElementById('modalDetailLink');
    
    // Fungsi untuk menampilkan modal dengan data sekolah
    window.showSchoolDetail = function(schoolId) {
        // Fetch data sekolah via AJAX
        fetch(`/schools/${schoolId}/json`)
            .then(response => response.json())
            .then(data => {
                modalTitle.textContent = data.name;
                modalBody.innerHTML = `
                    <p><strong>NPSN:</strong> ${data.npsn}</p>
                    <p><strong>Alamat:</strong> ${data.address}</p>
                    <p><strong>Kota/Kab:</strong> ${data.city}</p>
                    <p><strong>Kecamatan:</strong> ${data.district}</p>
                    <p><strong>Kepala Sekolah:</strong> ${data.headmaster || '-'}</p>
                    <p><strong>Status:</strong> ${data.status === 'negeri' ? 'Negeri' : 'Swasta'}</p>
                    <p><strong>Akreditasi:</strong> ${data.accreditation || '-'}</p>
                    <p><strong>Jumlah Program:</strong> ${data.programs_count || 0}</p>
                `;
                modalDetailLink.href = `/schools/${schoolId}`;
                
                // Tampilkan modal
                new bootstrap.Modal(schoolModal).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data sekolah');
            });
    };
});