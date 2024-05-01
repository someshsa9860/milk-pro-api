<div class="container">
    <h1>Vehicles</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Location</th>
                <th>Entry</th>
                <th>Departure</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->location }}</td>
                    <td>{{ $vehicle->entry }}</td>
                    <td>{{ $vehicle->departure }}</td>
                    <td>{{ $vehicle->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>