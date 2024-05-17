const charts = [];

document.querySelectorAll('.stock-chart').forEach(canvas => {
    const ctx = canvas.getContext('2d');
    const ticker = canvas.getAttribute('data-ticker');
    const name = canvas.getAttribute('data-name');
    const chartType = canvas.getAttribute('data-chart-type'); // Retrieve the chart type
    const dataJson = canvas.getAttribute('data-dataJson');
    const labelsJson = canvas.getAttribute('data-labelsJson');
    const chartLabel = `${ticker} - ${name}`;

    let parsedData = JSON.parse(dataJson)
    let parsedLabels = JSON.parse(labelsJson)

    console.log(chartType)
    const chart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: parsedLabels,
            datasets: [{
                label: chartLabel,
                data: parsedData,
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1,
                tension: 0.1,
                fill: {
                    target: 'origin',
                    above: 'rgb(152, 237, 237)',
                    below: 'rgb(152, 237, 237)'
                }
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        maxTicksLimit: 8
                    }
                },
                x: {
                    ticks: {
                        min:10,
                        max:30
                    }
                }
            },
            elements: {
                point: {
                radius: 0,
                hoverRadius: 6,
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    charts.push(chart); // Store for potential future reference
});

var updateTimeScale = function(newTimeScale) {
    document.querySelectorAll('.stock-chart').forEach(canvas => {
        const stockId = canvas.getAttribute('data-stock-id'); // Ensure each canvas has a data-stock-id attribute

        // Fetch new data based on the new time scale
        fetch(`/stocks/${stockId}/data?timeScale=${newTimeScale}`)
            .then(response => response.json())
            .then(data => {
                const chart = charts.find(c => c.canvas === canvas);
                if (chart) {
                    // Parse the JSON data
                    const labels = JSON.parse(data.labels);
                    const values = JSON.parse(data.values);

                    // Update the chart data
                    chart.data.labels = labels;
                    chart.data.datasets.forEach((dataset) => {
                        dataset.data = values;
                    });
                    
                    // Optionally update the data-dataJson attribute if needed
                    canvas.setAttribute('data-dataJson', JSON.stringify(values));

                    // Re-render the chart
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    });
};


var updateChartType = function(chartType) {
    charts.forEach(chart => {    

        chart.config.type = chartType;
    
    })
};


//Time Scale Listeners
document.getElementById('1HButton').addEventListener('click', function() {
    updateTimeScale('1H'); // Change all charts to 'line' type
});
document.getElementById('1DButton').addEventListener('click', function() {
    updateTimeScale('1D'); // Change all charts to 'line' type
});
document.getElementById('1WButton').addEventListener('click', function() {
    updateTimeScale('1W'); // Change all charts to 'line' type
});
document.getElementById('1MButton').addEventListener('click', function() {
    updateTimeScale('1M'); // Change all charts to 'line' type
});
document.getElementById('1YButton').addEventListener('click', function() {
    updateTimeScale('1Y'); // Change all charts to 'line' type
});

// Chart Type Listeners
document.getElementById('lineButton').addEventListener('click', function() {
    updateChartType('line'); // Change all charts to 'line' type
});
document.getElementById('barButton').addEventListener('click', function() {
    updateChartType('bar'); // Change all charts to 'bar' type
});
document.getElementById('pieButton').addEventListener('click', function() {
    updateChartType('pie'); // Change all charts to 'pie' type
});
