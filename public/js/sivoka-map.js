/**
 * SiVOKA-SLB - Map
 * Menangani peta leaflet dengan filter pencarian
 */

let map;
let markerGroup;
let allSchools = [];

// Inisialisasi peta
function initMap(schoolsData, centerLat = 3.5952, centerLng = 98.6722) {
    allSchools = schoolsData;
    
    console.log('🚀 Memuat peta dengan', allSchools.length, 'sekolah');
    
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('❌ Element #map tidak ditemukan');
        return;
    }
    
    // Inisialisasi peta
    map = L.map('map', {
        center: [centerLat, centerLng],
        zoom: 9,
        zoomControl: true
    });
    
    // Tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Group untuk marker
    markerGroup = L.layerGroup().addTo(map);
    
    // Tambah legend
    addLegend();
    
    // Tampilkan semua marker
    displayAllMarkers();
    
    // Setup filter listeners
    setupFilters();
    
    console.log('✅ Peta siap');
}

// Fungsi untuk mendapatkan warna berdasarkan jumlah program
function getMarkerColor(programCount) {
    if (programCount >= 5) return '#dc3545';
    if (programCount >= 3) return '#fd7e14';
    if (programCount >= 1) return '#ffc107';
    return '#6c757d';
}

// Fungsi untuk membuat popup content
function createPopupContent(school) {
    return `
        <div style="max-width: 280px; min-width: 220px;">
            <h6 style="margin:0 0 8px; font-weight:bold; color:#0d6efd; border-bottom:1px solid #eee; padding-bottom:5px;">
                ${escapeHtml(school.name)}
            </h6>
            <div style="margin-bottom: 8px;">
                <div style="display: flex; gap: 8px; margin-bottom: 4px;">
                    <span style="font-weight:500; width: 70px;">NPSN:</span>
                    <span>${escapeHtml(school.npsn) || '-'}</span>
                </div>
                <div style="display: flex; gap: 8px; margin-bottom: 4px;">
                    <span style="font-weight:500; width: 70px;">Kota:</span>
                    <span>${escapeHtml(school.city)}</span>
                </div>
                <div style="display: flex; gap: 8px; margin-bottom: 4px;">
                    <span style="font-weight:500; width: 70px;">Kecamatan:</span>
                    <span>${escapeHtml(school.district)}</span>
                </div>
                <div style="display: flex; gap: 8px; margin-bottom: 4px;">
                    <span style="font-weight:500; width: 70px;">Akreditasi:</span>
                    <span>${escapeHtml(school.accreditation) || '-'}</span>
                </div>
                <div style="display: flex; gap: 8px; margin-bottom: 4px;">
                    <span style="font-weight:500; width: 70px;">Status:</span>
                    <span>${school.status == 'negeri' ? 'Negeri' : 'Swasta'}</span>
                </div>
                <div style="display: flex; gap: 8px; margin-bottom: 4px;">
                    <span style="font-weight:500; width: 70px;">Program:</span>
                    <span>${school.programs_count || 0} program</span>
                </div>
            </div>
            <div style="margin: 8px 0; padding: 4px 0; border-top: 1px solid #eee;">
                <div style="display: inline-flex; align-items: center; gap: 5px;">
                    <span style="background:${getMarkerColor(school.programs_count)}; width:12px; height:12px; border-radius:50%; display:inline-block;"></span>
                    <span style="font-size:10px;">${school.programs_count >= 5 ? '≥5 Program' : (school.programs_count >= 3 ? '3-4 Program' : (school.programs_count >= 1 ? '1-2 Program' : '0 Program'))}</span>
                </div>
            </div>
            <a href="/schools/${school.id}" 
               style="display:block; margin-top:10px; padding:6px 12px; background:#0d6efd; color:white; text-align:center; border-radius:5px; text-decoration:none; font-size:12px; font-weight:500;">
                <i class="bi bi-eye"></i> Lihat Detail Sekolah
            </a>
        </div>
    `;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Fungsi untuk menampilkan semua marker
function displayAllMarkers(filteredSchools = null) {
    const schoolsToShow = filteredSchools || allSchools;
    
    markerGroup.clearLayers();
    
    schoolsToShow.forEach(school => {
        if (school.latitude && school.longitude) {
            const markerColor = getMarkerColor(school.programs_count);
            
            const marker = L.marker([school.latitude, school.longitude], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background-color: ${markerColor};
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    "></div>`,
                    iconSize: [26, 26],
                    popupAnchor: [0, -13]
                })
            }).bindPopup(createPopupContent(school));
            
            markerGroup.addLayer(marker);
        }
    });
    
    updateInfoPanel(schoolsToShow.length);
    updateStatistics(schoolsToShow);
    zoomToSchool(schoolsToShow);
}

// Zoom ke hasil filter
function zoomToSchool(schools) {
    if (schools.length === 0) {
        showNoResultAlert();
        return;
    }
    
    if (schools.length === 1) {
        const school = schools[0];
        if (school.latitude && school.longitude) {
            map.setView([school.latitude, school.longitude], 15);
            // Buka popup otomatis
            setTimeout(() => {
                markerGroup.eachLayer(layer => {
                    const latlng = layer.getLatLng();
                    if (Math.abs(latlng.lat - school.latitude) < 0.0001 && 
                        Math.abs(latlng.lng - school.longitude) < 0.0001) {
                        layer.openPopup();
                    }
                });
            }, 500);
        }
    } else if (schools.length > 1) {
        const bounds = L.latLngBounds();
        schools.forEach(school => {
            if (school.latitude && school.longitude) {
                bounds.extend([school.latitude, school.longitude]);
            }
        });
        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
}

// Fungsi filter dengan NPSN
function filterSchools() {
    const searchInput = document.getElementById('searchSchool');
    const searchNpsn = document.getElementById('searchNpsn');
    const cityFilter = document.getElementById('filterCity');
    const programFilter = document.getElementById('filterProgram');
    
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const npsnTerm = searchNpsn ? searchNpsn.value.trim() : '';
    const city = cityFilter ? cityFilter.value : '';
    const minProgram = programFilter ? parseInt(programFilter.value) || 0 : 0;
    
    console.log('🔍 Filter:', { searchTerm, npsnTerm, city, minProgram });
    
    const filtered = allSchools.filter(school => {
        // Filter NPSN
        if (npsnTerm) {
            const schoolNpsn = school.npsn ? school.npsn.toString() : '';
            if (!schoolNpsn.includes(npsnTerm)) return false;
        }
        
        // Filter nama/alamat
        if (searchTerm) {
            const matchesName = school.name.toLowerCase().includes(searchTerm);
            const matchesAddress = school.address ? school.address.toLowerCase().includes(searchTerm) : false;
            const matchesCity = school.city.toLowerCase().includes(searchTerm);
            if (!matchesName && !matchesAddress && !matchesCity) return false;
        }
        
        // Filter kota
        if (city && school.city !== city) return false;
        
        // Filter program
        if (minProgram > 0 && (school.programs_count || 0) < minProgram) return false;
        
        return true;
    });
    
    console.log('📊 Hasil filter:', filtered.length, 'sekolah');
    
    if (filtered.length === 0) {
        showNoResultAlert();
    }
    
    displayAllMarkers(filtered);
}

// Setup filter
function setupFilters() {
    const applyBtn = document.getElementById('applyFilters');
    const resetBtn = document.getElementById('resetFilters');
    const searchNpsn = document.getElementById('searchNpsn');
    
    if (applyBtn) {
        applyBtn.onclick = function(e) {
            e.preventDefault();
            filterSchools();
        };
    }
    
    if (resetBtn) {
        resetBtn.onclick = function(e) {
            e.preventDefault();
            document.getElementById('searchSchool').value = '';
            document.getElementById('searchNpsn').value = '';
            document.getElementById('filterCity').value = '';
            document.getElementById('filterProgram').value = '0';
            displayAllMarkers(allSchools);
            map.setView([3.5952, 98.6722], 9);
        };
    }
    
    // Validasi NPSN hanya angka
    if (searchNpsn) {
        searchNpsn.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);
        });
        searchNpsn.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') filterSchools();
        });
    }
    
    // Enter key untuk input nama
    const searchSchool = document.getElementById('searchSchool');
    if (searchSchool) {
        searchSchool.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') filterSchools();
        });
    }
}

// Update info panel
function updateInfoPanel(displayed) {
    const displayCount = document.getElementById('displayCount');
    const filterInfo = document.getElementById('filterInfo');
    if (displayCount) displayCount.textContent = displayed;
    if (filterInfo) {
        filterInfo.innerHTML = `<i class="bi bi-info-circle"></i> Menampilkan ${displayed} dari ${allSchools.length} SLB`;
    }
}

// Update statistik
function updateStatistics(schools) {
    const totalPrograms = schools.reduce((sum, s) => sum + (s.programs_count || 0), 0);
    const totalStudents = schools.reduce((sum, s) => sum + (s.total_students || 0), 0);
    
    const statTotal = document.getElementById('statTotal');
    const statPrograms = document.getElementById('statPrograms');
    const statStudents = document.getElementById('statStudents');
    
    if (statTotal) statTotal.textContent = schools.length;
    if (statPrograms) statPrograms.textContent = totalPrograms;
    if (statStudents) statStudents.textContent = totalStudents.toLocaleString();
}

// Alert tidak ada hasil
function showNoResultAlert() {
    const alertDiv = document.createElement('div');
    alertDiv.innerHTML = `
        <i class="bi bi-exclamation-triangle"></i>
        Tidak ada sekolah yang ditemukan dengan filter yang dipilih.
    `;
    alertDiv.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: #ffc107;
        color: #856404;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 9999;
        animation: fadeOut 3s ease forwards;
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 3000);
}

// Legend
function addLegend() {
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function() {
        const div = L.DomUtil.create('div', 'legend-control');
        div.innerHTML = `
            <div class="legend-title">Keterangan</div>
            <div class="legend-item"><span class="legend-color red"></span> ≥ 5 Program</div>
            <div class="legend-item"><span class="legend-color orange"></span> 3-4 Program</div>
            <div class="legend-item"><span class="legend-color yellow"></span> 1-2 Program</div>
            <div class="legend-item"><span class="legend-color gray"></span> 0 Program</div>
        `;
        return div;
    };
    legend.addTo(map);
}

// Dropdown kota
function populateCityFilter(schools) {
    const citySelect = document.getElementById('filterCity');
    if (!citySelect) return;
    
    const cities = [...new Set(schools.map(s => s.city))].sort();
    cities.forEach(city => {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        citySelect.appendChild(option);
    });
}

// CSS animasi
const style = document.createElement('style');
style.textContent = `@keyframes fadeOut { 0% { opacity: 1; } 70% { opacity: 1; } 100% { opacity: 0; display: none; } }`;
document.head.appendChild(style);