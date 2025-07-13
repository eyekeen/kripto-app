document.addEventListener('DOMContentLoaded', function() {
    const chartContainer = document.getElementById('chart-container');
    if (!chartContainer) return;
    
    try {
        const chartData = JSON.parse(chartContainer.dataset.chart);
        const ctx = document.createElement('canvas');
        chartContainer.appendChild(ctx);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Price (7 days)',
                    data: chartData.data,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    } catch (e) {
        console.error('Error initializing chart:', e);
        chartContainer.innerHTML = '<p>Chart data not available</p>';
    }
});