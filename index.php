<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('location:login.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Banking</title>
    <?php require 'assets/autoloader.php'; ?>
    <?php require 'assets/db.php'; ?>
    <?php require 'assets/function.php'; ?>
</head>
<body style="background:#96D678;background-size: 100%">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">
            <img src="images/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
            <?php echo bankName; // Use the correct case ?>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item ">
                    <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item ">  <a class="nav-link" href="accounts.php">Accounts</a></li>
                <li class="nav-item ">  <a class="nav-link" href="statements.php">Account Statements</a></li>
                <li class="nav-item ">  <a class="nav-link" href="transfer.php">Funds Transfer</a></li>
            </ul>
            <?php include 'sideButton.php'; ?>
        </div>
    </nav>
    <br><br><br>
    <div class="row w-100">
        <div class="col" style="padding: 22px;padding-top: 0">
            <div class="jumbotron shadowBlack" style="padding: 25px;min-height: 241px;max-height: 241px">
                <h4 class="display-5">Welcome to SBI Bank</h4>
                <p class="lead alert alert-warning"><b>Latest Notification:</b>
                    <?php
                    $array = $con->query("select * from notice where userId = '$_SESSION[userId]' order by date desc");
                    if ($array->num_rows > 0) {
                        $row = $array->fetch_assoc();
                        echo $row['notice'];
                    } else {
                        echo "<div class='alert alert-info'>Notice box empty</div>";
                    }
                    ?>
                </p>
            </div>
            <div id="carouselExampleIndicators" class="carousel slide my-2 rounded-1 shadowBlack" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block w-100" src="images/1.jpg" alt="First slide" style="max-height: 250px">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="images/2.jpg" alt="Second slide" style="max-height: 250px">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="images/3.jpg" alt="Third slide" style="max-height: 250px">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="row" style="padding: 22px;padding-top: 0">
                <div class="col">
                    <div class="card shadowBlack ">
                        <img class="card-img-top" src="images/acount.jpg" style="max-height: 155px;min-height: 155px" alt="Card image cap">
                        <div class="card-body">
                            <a href="accounts.php" class="btn btn-outline-success btn-block">Account Summary</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadowBlack ">
                        <img class="card-img-top" src="images/transfer.jpg" alt="Card image cap" style="max-height: 155px;min-height: 155px">
                        <div class="card-body">
                            <a href="transfer.php" class="btn btn-outline-success btn-block">Transfer Money</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="padding: 22px">
                <div class="col">
                    <div class="card shadowBlack ">
                        <img class="card-img-top" src="images/bell.gif" style="max-height: 155px;min-height: 155px" alt="Card image cap">
                        <div class="card-body">
                            <a href="notice.php" class="btn btn-outline-primary btn-block">Check Notification</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadowBlack ">
                        <img class="card-img-top" src="images/contacts.gif" alt="Card image cap" style="max-height: 155px;min-height: 155px">
                        <div class="card-body">
                            <a href="feedback.php" class="btn btn-outline-primary btn-block">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'chatbot.php'; ?>
<!-- 
ATM locations -->

<div class="row" style="padding: 22px">
    <div class="col">
        <div class="card shadowBlack ">
            <img class="card-img-top" src="images/atms1.jpg" style="max-height: 155px;min-height: 155px" alt="Card image cap">
            <div class="card-body">
                <button id="find-atm" class="btn btn-outline-primary btn-block">Find Nearby ATMs</button>
            </div>
        </div>
    </div>
</div>

<div id="map-container" style="display: none; padding: 22px;">
    <div id="map" style="height: 400px; width: 100%;"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const findAtmButton = document.getElementById('find-atm');
        const mapContainer = document.getElementById('map-container');
        let map;
        let infoWindow;

        findAtmButton.addEventListener('click', function() {
            mapContainer.style.display = 'block';
            initMap();
        });

        function initMap() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    map = new google.maps.Map(document.getElementById('map'), {
                        center: userLocation,
                        zoom: 15
                    });

                    infoWindow = new google.maps.InfoWindow();

                    const service = new google.maps.places.PlacesService(map);
                    service.nearbySearch({
                        location: userLocation,
                        radius: 5000, // Search within a 5000-meter radius
                        type: ['atm'] // Search for ATMs
                    }, callback);
                }, function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
            } else {
                handleLocationError(false, infoWindow, map.getCenter());
            }
        }

        function callback(results, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                for (let i = 0; i < results.length; i++) {
                    createMarker(results[i]);
                }
            }
        }

        function createMarker(place) {
            const marker = new google.maps.Marker({
                map: map,
                position: place.geometry.location
            });

            google.maps.event.addListener(marker, 'click', function() {
                infoWindow.setContent(place.name);
                infoWindow.open(map, this);
            });
        }

        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                'Error: The Geolocation service failed.' :
                'Error: Your browser doesn\'t support geolocation.');
            infoWindow.open(map);
        }
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCeXfqNFkuDYWzPf4D6zeXDPkSlPY2qISo&libraries=places"></script>
</body>

</html>