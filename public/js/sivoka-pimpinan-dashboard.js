/**
 * SiVOKA-SLB - Pimpinan Dashboard
 * Menangani chart dan interaktivitas dashboard pimpinan
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

// Fungsi untuk inisialisasi top programs chart (bar chart)
function initTopProgramsChart(canvasId, labels, data) {
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
                label: 'Jumlah Program',
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

// Fungsi untuk inisialisasi students per category chart (pie/doughnut)
function initStudentsCategoryChart(canvasId, labels, data) {
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

// Fungsi untuk setup resize handler
function setupResizeHandler(charts) {
    let resizeTimeout;
    
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            const fontSize = getChartFontSize();
            const boxSize = getLegendBoxSize();
            const maxTicks = getMaxTicks();
            const barThickness = getBarThickness();
            const isMobile = window.innerWidth < 768;
            
            if (charts.topProgramsChart) {
                charts.topProgramsChart.options.scales.y.ticks.font.size = fontSize;
                charts.topProgramsChart.options.scales.y.ticks.maxTicksLimit = maxTicks;
                charts.topProgramsChart.options.scales.x.ticks.font.size = fontSize;
                charts.topProgramsChart.options.scales.x.ticks.maxRotation = isMobile ? 45 : 0;
                charts.topProgramsChart.options.scales.x.ticks.minRotation = isMobile ? 45 : 0;
                charts.topProgramsChart.data.datasets[0].maxBarThickness = barThickness;
                charts.topProgramsChart.update();
            }
            
            if (charts.studentsChart) {
                charts.studentsChart.options.plugins.legend.labels.font.size = fontSize;
                charts.studentsChart.options.plugins.legend.labels.boxWidth = boxSize;
                charts.studentsChart.options.plugins.legend.labels.padding = isMobile ? 6 : 10;
                charts.studentsChart.options.cutout = isMobile ? '65%' : '60%';
                charts.studentsChart.update();
            }
        }, 150);
    });
}

// Fungsi utama untuk inisialisasi dashboard pimpinan
function initPimpinanDashboard(data) {
    if (!data) return {};
    
    const charts = {};
    
    // Top Programs Chart
    if (document.getElementById('topProgramsChart')) {
        charts.topProgramsChart = initTopProgramsChart('topProgramsChart', data.topLabels, data.topData);
    }
    
    // Students per Category Chart
    if (document.getElementById('studentsChart')) {
        charts.studentsChart = initStudentsCategoryChart('studentsChart', data.studentLabels, data.studentData);
    }
    
    // Setup resize handler
    setupResizeHandler(charts);
    
    return charts;
}