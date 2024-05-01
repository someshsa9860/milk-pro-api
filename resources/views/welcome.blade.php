<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parkall</title>
    <link rel="stylesheet" href="{{ asset('css/plan.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->

</head>

<body>
    <section class="hero" id="Plans">
       
        <video autoplay loop muted plays-inline class="herovideo">
            <source src="{{ asset('videos/videoo.mp4') }}" type="video/mp4">
        </video>

        <div class="container">
            <div class="hero-content">
                

                <h1 class="hero-title">Parkall</h1>

               
            </div>
        </div>
    </section>


    
    
    







    

</body>

<script>
    function changeBackgroundColor() {
        let plans = document.querySelectorAll('.plan-box');
        let colors = ['#5833FF', '#043F30', '#2C074E', '#141814', '#5F054D']; // Add more colors if needed

        plans.forEach((plan, index) => {
            plan.style.background =
                `linear-gradient(135deg, ${colors[index % colors.length]} 0%, ${colors[(index + 1) % colors.length]} 100%)`;
        });
    }

    function updatePlanPrice() {
        let planPrices = document.querySelectorAll('.plan-price');

        planPrices.forEach(price => {
            let amount = parseFloat(price.innerText.slice(2)); // Extracting the price after '₹ '
            if (amount === 0) {
                price.innerText = 'Free';
            }
        });
    }

    // Call the functions when the document is loaded
    document.addEventListener('DOMContentLoaded', function() {
        changeBackgroundColor();
        updatePlanPrice();
    });
</script>


<script>
    function changeDurationText() {
        let planDurations = document.querySelectorAll('.plan-duration');

        planDurations.forEach(duration => {
            let days = parseInt(duration.innerText.split(' ')[0]);

            if (days === 7) {
                duration.innerText = '1 week';
            } else if (days === 30) {
                duration.innerText = '1 month';
            } else if (days === 365) {
                duration.innerText = '1 year';
            } else {
                duration.innerText = `${days} days`;
            }
        });
    }

    // Call the function when the document is loaded
    document.addEventListener('DOMContentLoaded', function() {
        changeDurationText();
    });
</script>


<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
</script> -->

</html>
