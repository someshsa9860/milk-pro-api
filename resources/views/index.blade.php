<div class="container-scroller">
    <div class="container-fluid page-body-wrapper">
        <div class="main-panel">
            <div class="content-wrapper" style="padding: 20px;">
                <div class="row">
                    <?php
                    function generateCard($title, $value) {
                        $background = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.3)'; // Random background color with reduced opacity
                        echo '<div class="col-md-3 grid-margin stretch-card">
                                <div class="card" style="background: ' . $background . '; background-image: linear-gradient(to bottom right, ' . $background . ', #fff);">
                                    <div class="card-body" style="text-align: center;">
                                        <p class="card-title">' . $title . '</p>
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">' . $value . '</h3>

                                    </div>
                                </div>
                            </div>';
                    }

                    foreach ($data['counts'] as $item) {
                        generateCard($item['name'], $item['count']);
                    }

                    
                    ?>
                </div>
                <div class="card" style="background: rgba(245, 228, 183, 0.3); background-image: linear-gradient(to bottom right, rgba(245, 228, 183, 0.3), #fff);">
                    <div class="card-body">
                        <p class="card-title mb-0">Statistics</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                   

                                    foreach ($data['statistics'] as $item) {
                                        echo '<tr>
                                                <td>' . $item['month'] . '</td>
                                                <td>' . $item['users'] . '</td>
                                              </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="container" style="font-weight:700">
                    @include('reports', ['vehicles' => $data['vehicles']])
                </div>

            </div>

        </div>
    </div>
</div>
