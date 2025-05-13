const ctxService = document.getElementById('serviceChart').getContext('2d');

const dataService = {
    datasets: [{
        data: [79, 100 - 79], // 79% xanh, 21% xám
        backgroundColor: ['#27A468', '#F2F5FA'],
        borderWidth: 0,
        cutout: '80%'
    }]
};

const configService = {
    type: 'doughnut',
    data: dataService,
    options: {
        responsive: false,
        plugins: {
            legend: { display: false },
            tooltip: { enabled: false }
        }
    }
};

new Chart(ctxService, configService);
