const ctx = document.getElementById('revenueChart').getContext('2d');

const data = {
    datasets: [{
        data: [52, 100 - 52],
        backgroundColor: ['#F2A735', '#F2F5FA'],
        borderWidth: 0,
        cutout: '80%'
    }]
};

const config = {
    type: 'doughnut',
    data: data,
    options: {
        responsive: false,
        plugins: {
            legend: { display: false },
            tooltip: { enabled: false }
        }
    }
};

new Chart(ctx, config);
