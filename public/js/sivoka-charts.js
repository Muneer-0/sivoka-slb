/**
 * SiVOKA-SLB Charts
 * Menangani semua grafik di dashboard dan laporan
 */

// Fungsi untuk mendapatkan ukuran font berdasarkan lebar layar
function getChartFontSize() {
    return window.innerWidth < 480 ? 8 : (window.innerWidth < 768 ? 9 : 10);
}

// Fungsi untuk mendapatkan ukuran legend box
function getLegendBoxSize() {
    return window.innerWidth < 480 ? 8 : (window.innerWidth < 768 ? 10 : 12);
}

// Fungsi untuk mendapatkan max ticks berdasarkan lebar layar
function getMaxTicks() {
    return window.innerWidth < 480 ? 4 : (window.innerWidth < 768 ? 5 : 6);
}

// Fungsi untuk mendapatkan bar thickness berdasarkan lebar layar
function getBarThickness() {
    return window.innerWidth < 480 ? 25 : (window.innerWidth < 768 ? 35 : 50);
}

// Fungsi untuk inisialisasi chart program per kategori (bar chart)
function initCategoryChart(canvasId, labels, data, label = 'Jumlah Program') {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    
    // Hancurkan chart lama jika ada
    const chartKey = canvasId + 'Chart';
    if (window[chartKey]) {
        window[chartKey].destroy();
    }
    
    const ctx = canvas.getContext('2d');
    const fontSize = getChartFontSize();
    const maxTicks = getMaxTicks();
    const barThickness = getBarThickness();
    
    window[chartKey] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: 'rgba(102, 126, 234, 0.7)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 1,
                borderRadius: 5,
                barPercentage: 0.7,
                categoryPercentage: 0.8,
                maxBarThickness: barThickness
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#333',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    bodyFont: { size: fontSize },
                    titleFont: { size: fontSize + 2 }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { 
                        stepSize: 1,
                        font: { size: fontSize },
                        maxTicksLimit: maxTicks,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    },
                    grid: { 
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    }
                },
                x: { 
                    ticks: { 
                        font: { size: fontSize },
                        maxRotation: window.innerWidth < 768 ? 45 : 0,
                        minRotation: window.innerWidth < 768 ? 45 : 0,
                        autoSkip: true,
                        maxTicksLimit: 8
                    },
                    grid: { display: false }
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 5,
                    right: 5
                }
            }
        }
    });
    
    return window[chartKey];
}

// Fungsi untuk inisialisasi chart siswa per kategori (bar chart)
function initStudentsChart(canvasId, labels, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    
    const chartKey = canvasId + 'Chart';
    if (window[chartKey]) {
        window[chartKey].destroy();
    }
    
    const ctx = canvas.getContext('2d');
    const fontSize = getChartFontSize();
    const maxTicks = getMaxTicks();
    const barThickness = getBarThickness();
    
    window[chartKey] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Siswa',
                data: data,
                backgroundColor: 'rgba(240, 147, 251, 0.7)',
                borderColor: 'rgba(240, 147, 251, 1)',
                borderWidth: 1,
                borderRadius: 5,
                barPercentage: 0.7,
                categoryPercentage: 0.8,
                maxBarThickness: barThickness
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#333',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { 
                        font: { size: fontSize },
                        maxTicksLimit: maxTicks,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null;
                        }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { 
                    ticks: { 
                        font: { size: fontSize },
                        maxRotation: window.innerWidth < 768 ? 45 : 0,
                        minRotation: window.innerWidth < 768 ? 45 : 0,
                        autoSkip: true
                    },
                    grid: { display: false }
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 5,
                    right: 5
                }
            }
        }
    });
    
    return window[chartKey];
}

// Fungsi untuk inisialisasi top programs chart (doughnut)
function initTopProgramsChart(canvasId, labels, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    
    const chartKey = canvasId + 'Chart';
    if (window[chartKey]) {
        window[chartKey].destroy();
    }
    
    const ctx = canvas.getContext('2d');
    const fontSize = getChartFontSize();
    const boxSize = getLegendBoxSize();
    const isMobile = window.innerWidth < 768;
    
    window[chartKey] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(102, 126, 234, 0.7)',
                    'rgba(240, 147, 251, 0.7)',
                    'rgba(79, 172, 254, 0.7)',
                    'rgba(67, 233, 123, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    align: 'center',
                    labels: { 
                        font: { 
                            size: fontSize,
                            weight: 'normal'
                        },
                        boxWidth: boxSize,
                        boxHeight: boxSize,
                        padding: isMobile ? 6 : 10,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#333',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    bodyFont: { size: fontSize },
                    titleFont: { size: fontSize + 2 }
                }
            },
            cutout: isMobile ? '65%' : '60%',
            layout: {
                padding: {
                    top: 5,
                    bottom: 5,
                    left: 5,
                    right: 5
                }
            }
        }
    });
    
    return window[chartKey];
}

// Fungsi untuk inisialisasi progress chart (doughnut)
function initProgressChart(canvasId, sudah, belum) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    
    const chartKey = canvasId + 'Chart';
    if (window[chartKey]) {
        window[chartKey].destroy();
    }
    
    const ctx = canvas.getContext('2d');
    const isMobile = window.innerWidth < 768;
    
    window[chartKey] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Sudah Input', 'Belum Input'],
            datasets: [{
                data: [sudah, belum],
                backgroundColor: ['#28a745', '#ffc107'],
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: isMobile ? '65%' : '70%',
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { 
                        font: { size: getChartFontSize() },
                        boxWidth: getLegendBoxSize() * 0.8,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#333',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },
            layout: {
                padding: {
                    top: 5,
                    bottom: 5,
                    left: 5,
                    right: 5
                }
            }
        }
    });
    
    return window[chartKey];
}

// Fungsi untuk inisialisasi chart di laporan (program per kota - pie chart)
function initCityChart(canvasId, labels, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !labels || labels.length === 0) return null;
    
    const chartKey = canvasId + 'Chart';
    if (window[chartKey]) {
        window[chartKey].destroy();
    }
    
    const ctx = canvas.getContext('2d');
    const fontSize = getChartFontSize();
    const boxSize = getLegendBoxSize();
    const isMobile = window.innerWidth < 768;
    
    window[chartKey] = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(240, 147, 251, 0.8)',
                    'rgba(79, 172, 254, 0.8)',
                    'rgba(67, 233, 123, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                ],
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    align: 'center',
                    labels: { 
                        font: { size: fontSize },
                        boxWidth: boxSize,
                        boxHeight: boxSize,
                        padding: isMobile ? 6 : 10,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#333',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 5,
                    right: 5
                }
            }
        }
    });
    
    return window[chartKey];
}

// Fungsi untuk setup resize handler
function setupChartResize(charts) {
    let resizeTimeout;
    
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            const fontSize = getChartFontSize();
            const boxSize = getLegendBoxSize();
            const maxTicks = getMaxTicks();
            const barThickness = getBarThickness();
            const isMobile = window.innerWidth < 768;
            
            // Update category chart
            if (charts.programsChart) {
                charts.programsChart.options.scales.y.ticks.font.size = fontSize;
                charts.programsChart.options.scales.y.ticks.maxTicksLimit = maxTicks;
                charts.programsChart.options.scales.x.ticks.font.size = fontSize;
                charts.programsChart.options.scales.x.ticks.maxRotation = isMobile ? 45 : 0;
                charts.programsChart.options.scales.x.ticks.minRotation = isMobile ? 45 : 0;
                charts.programsChart.data.datasets[0].maxBarThickness = barThickness;
                charts.programsChart.update();
            }
            
            // Update students chart
            if (charts.studentsChart) {
                charts.studentsChart.options.scales.y.ticks.font.size = fontSize;
                charts.studentsChart.options.scales.y.ticks.maxTicksLimit = maxTicks;
                charts.studentsChart.options.scales.x.ticks.font.size = fontSize;
                charts.studentsChart.options.scales.x.ticks.maxRotation = isMobile ? 45 : 0;
                charts.studentsChart.options.scales.x.ticks.minRotation = isMobile ? 45 : 0;
                charts.studentsChart.data.datasets[0].maxBarThickness = barThickness;
                charts.studentsChart.update();
            }
            
            // Update top programs chart
            if (charts.topProgramsChart) {
                charts.topProgramsChart.options.plugins.legend.labels.font.size = fontSize;
                charts.topProgramsChart.options.plugins.legend.labels.boxWidth = boxSize;
                charts.topProgramsChart.options.plugins.legend.labels.padding = isMobile ? 6 : 10;
                charts.topProgramsChart.options.cutout = isMobile ? '65%' : '60%';
                charts.topProgramsChart.update();
            }
            
            // Update progress chart
            if (charts.progressChart) {
                charts.progressChart.options.plugins.legend.labels.font.size = fontSize;
                charts.progressChart.options.cutout = isMobile ? '65%' : '70%';
                charts.progressChart.update();
            }
            
            // Update city chart
            if (charts.cityChart) {
                charts.cityChart.options.plugins.legend.labels.font.size = fontSize;
                charts.cityChart.options.plugins.legend.labels.boxWidth = boxSize;
                charts.cityChart.options.plugins.legend.labels.padding = isMobile ? 6 : 10;
                charts.cityChart.update();
            }
        }, 150);
    });
}

// Fungsi utama untuk inisialisasi chart di dashboard admin
function initDashboardCharts(data) {
    if (!data) return {};
    
    const charts = {};
    
    // Program per Kategori
    if (document.getElementById('programsChart')) {
        charts.programsChart = initCategoryChart('programsChart', data.programLabels, data.programData);
    }
    
    // Siswa per Kategori
    if (document.getElementById('studentsChart')) {
        charts.studentsChart = initStudentsChart('studentsChart', data.studentLabels, data.studentData);
    }
    
    // Top 5 Program
    if (document.getElementById('topProgramsChart')) {
        charts.topProgramsChart = initTopProgramsChart('topProgramsChart', data.topLabels, data.topData);
    }
    
    // Progress Chart
    if (document.getElementById('progressChart')) {
        charts.progressChart = initProgressChart('progressChart', data.schoolsWithData, data.pendingSchools);
    }
    
    // Setup resize handler
    setupChartResize(charts);
    
    return charts;
}

// Fungsi untuk inisialisasi chart di laporan
function initReportCharts(categoryData, cityData) {
    const charts = {};
    
    if (document.getElementById('categoryChart')) {
        charts.categoryChart = initCategoryChart('categoryChart', categoryData.labels, categoryData.data);
    }
    
    if (document.getElementById('cityChart')) {
        charts.cityChart = initCityChart('cityChart', cityData.labels, cityData.data);
    }
    
    return charts;
}