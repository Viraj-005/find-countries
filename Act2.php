<?php
// API URL to fetch country data
$api_url = 'https://restcountries.com/v3.1/all';

// Fetch country data from the API
$response = file_get_contents($api_url);

// Decode the JSON response
$countries = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Countries Information</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        
        body {
            font-family: 'Monsterrat', sans-serif;
            background-color: #f4f4f4;
            background-image: url('act2bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            margin-bottom: 20px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(10px); 
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2); 
        }

        .container.mt-5 {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.5); 
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .heading {
            color: #fff;
            text-align: center;
            letter-spacing: 5px;
        }

        h2 {
            color: #333;
        }
        
        .form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            font-size: 15px;
            margin-bottom: 20px;
        }
        
        .form-control::after {
            content: '\25BC'; /* Unicode character for down arrow */
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none; 
        }
        
        .form-control:active,
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        #countryInfo {
            /* background-color: #EEEDED; */
            padding: 20px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            /* border-radius: 10px; */
        }

        #countryInfo img {
            display: block;
            margin: 0 auto;
            padding: 20px;
        }

        #countryInfo h2 {
            text-align: center;
        }

        #map {
            margin-top: 20px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        #footer {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            padding-top: 30px;
            padding-bottom: 30px;
            text-align: center;
            margin-top: 2rem;
            max-width: 100%;
        }

        .ttf {
            font-size: 18px;
            font-weight: bold;
        }

    </style>
</head>
<body>

    <div class="container mt-5">
        <h2 class="heading">All Country Details</h2>
        <select id="countrySelect" class="form-control">
            <option value="" selected disabled>Select a Country</option>
            <?php foreach ($countries as $country) : ?>
                <option value="<?php echo $country['cca2']; ?>"><?php echo $country['name']['common']; ?></option>
            <?php endforeach; ?>
        </select>

        <div id="countryInfo" class="mt-4">
            <!-- Information will be displayed here -->
        </div>

        <div id="map" class="mt-4" style="height: 500px; width:100%;"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>

    <script>
        var map;
        var marker; // Added marker variable

        $(document).ready(function () {
            // Handle country selection change
            $('#countrySelect').change(function () {
                var selectedCountryCode = $(this).val();

                // Fetch detailed information for the selected country
                $.ajax({
                    url: 'https://restcountries.com/v3.1/alpha/' + selectedCountryCode,
                    type: 'GET',
                    success: function (data) {
                        var countryInfo = data[0];
                        displayCountryInfo(countryInfo);

                        // Display Google Map
                        initMap(countryInfo.latlng[0], countryInfo.latlng[1]);

                        // Add marker to the map
                        addMarker(countryInfo.latlng[0], countryInfo.latlng[1]);
                    }
                });
            });

            // Function to display country information
            function displayCountryInfo(countryInfo) {
                var html = '<h2>' + countryInfo['name']['common'] + '</h2>';
                html += '<img src="' + countryInfo['flags']['svg'] + '" alt="Flag" style="width: 300px; height: auto;">';
                html += '<p><strong>Official Name:</strong> ' + countryInfo['name']['official'] + '</p>';
                html += '<p><strong>Capital City:</strong> ' + countryInfo['capital'] + '</p>';
                html += '<p><strong>Region:</strong> ' + countryInfo['region'] + '</p>';
                html += '<p><strong>Subregion:</strong> ' + (countryInfo['subregion'] ? countryInfo['subregion'] : 'None') + '</p>';
                html += '<p><strong>Currencies:</strong> ' + formatCurrencies(countryInfo['currencies']) + '</p>';
                html += '<p><strong>Country Code:</strong> ' + countryInfo['cca2'] + '</p>';
                html += '<p><strong>Population:</strong> ' + countryInfo['population'] + '</p>';
                html += '<p><strong>Area:</strong> ' + countryInfo['area'] + ' km<sup>2</sup></p>';
                html += '<p><strong>Borders:</strong> ' + (countryInfo['borders'] ? countryInfo['borders'].join(', ') : 'None') + '</p>';

                $('#countryInfo').html(html);
            }

            // Function to initialize Google Map
            function initMap(lat, lng) {
                if (map) {
                    map.setCenter({lat: lat, lng: lng});
                } else {
                    map = new google.maps.Map(document.getElementById('map'), {
                        center: {lat: lat, lng: lng},
                        zoom: 6
                    });
                }
            }

            // Function to add marker to the map
            function addMarker(lat, lng) {
                if (marker) {
                    marker.setPosition({lat: lat, lng: lng});
                } else {
                    marker = new google.maps.Marker({
                        position: {lat: lat, lng: lng},
                        map: map,
                        title: 'Selected Country'
                    });
                }
            }

            // Function to format currencies with symbols
            function formatCurrencies(currencies) {
                var formattedCurrencies = Object.keys(currencies).map(function (code) {
                    var name = currencies[code].name;
                    var symbol = currencies[code].symbol ? ' (' + currencies[code].symbol + ')' : '';
                    return name + symbol;
                });
                return formattedCurrencies.join(', ');
            }
        });
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap">
    </script>
    <div id="footer">
        <div class="ttf" align="center">All Rights Received © ITBIN-2110-0041 Intake10_IT_2023</div>
    </div>                   
</body>
</html>
