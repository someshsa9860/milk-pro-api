
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
    /* Styling for the purple top section */
    .purple-bg {
        background-color: #90EE90;
        padding: 30px;
        color: white;
        border-radius: 0 0 30px 30px;
    }

    /* Card styling with hover effect */
    .stat-card {
        border-radius: 10px;
        background-color: #f4f4f4;
        margin: 8px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Styling for the white chart section */
    .white-bg {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 30px 30px 0 0;
        margin-top: -20px;
    }

    /* Chart container styling */
    .chart-container {
        margin-bottom: 20px;
        width: 100%;
        height: 400px;
    }
</style>
<main>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper">
            <!-- Purple Section with Statistics -->
            <div class="purple-bg">
                <h1 style="text-align: center; margin-bottom: 30px;">Dashboard Metrics</h1>
                <div class="row">
                    @foreach ($data['counts'] as $item)
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="stat-card shadow-sm">
                                <p class="card-header text-center"
                                    style="background-color: #6a0dad; color: white; font-size: 1.2rem;">
                                    {{ $item['name'] }}
                                </p>
                                <div class="card-body" style="text-align: center; padding: 20px;">
                                    <h5 class="card-title mb-0" style="font-size: 2rem; color: #333;">
                                        {{ $item['count'] }}</h5>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
    
            <!-- White Section with ApexChart -->
            <div class="white-bg">
                @foreach ($data['statistics'] as $index => $report)
                    <div class="chart-container" id="chart-{{ $index }}"></div>
                @endforeach
            </div>
        </div>
    </div>
</main>

<script>
    const statistics = {!! json_encode($data['statistics']) !!};

    statistics.forEach((stat, index) => {
        const locations = stat.data.map(item => item.location_id);
        const totals = stat.data.map(item => item.total);

        const options = {
            chart: {
                type: 'bar',
                height: 400,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                }
            },
            series: [{
                name: stat.labelY,
                data: totals
            }],
            xaxis: {
                categories: locations,
                title: { text: stat.labelX }
            },
            yaxis: {
                title: { text: stat.labelY }
            },
            colors: ['#90EE90'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'rounded'
                }
            },
            title: {
                text: stat.title,
                align: 'center',
                style: {
                    fontSize: '18px',
                    color: '#333'
                }
            },
            subtitle: {
                text: stat.description,
                align: 'center',
                style: {
                    fontSize: '14px',
                    color: '#666'
                }
            }
        };

        const chart = new ApexCharts(document.querySelector(`#chart-${index}`), options);
        chart.render();
    });
</script>