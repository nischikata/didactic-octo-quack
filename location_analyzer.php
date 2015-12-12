<?php

require __DIR__ . "/vendor/autoload.php";

define('APPLICATION_ID', "71fd369b");
define('APPLICATION_KEY', "cc8bba3692e4445f39e48838b12cdf6a");

function call_api($endpoint, $parameters)
{
    $ch = curl_init('https://api.aylien.com/api/v1/' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'X-AYLIEN-TextAPI-Application-Key: ' . APPLICATION_KEY,
        'X-AYLIEN-TextAPI-Application-ID: ' . APPLICATION_ID
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    return json_decode($response);
}

if (isset($_POST['analyse_location']) && (!empty($_POST['textInput']))) {
    $input = htmlspecialchars($_POST['textInput']);
    $params = array('text' => $input);
    $entities = call_api('entities', $params);

    $originalText = $entities->text;
} else {
    $entities = null;
    $originalText = "";
}

/*
if (isset($entities->entities->location)) {
    echo json_encode($entities->entities->location);
}

if (isset($entities->entities->person)) {
    echo json_encode($entities->entities->person);
}

if (isset($entities->entities->organization)) {
    echo json_encode($entities->entities->organization);
}
*/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Semantic Text Analysis</title>

    <link href="css/location_analyzer.css" rel="stylesheet" type="text/css">

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-2.1.3.min.js" type="text/javascript"></script>

    <!-- bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw=="
          crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
            integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A=="
            crossorigin="anonymous"></script>

    <!-- google maps api -->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>
    <script type="text/javascript" src="js/maps_script.js"></script>
    <script type="text/javascript" src="js/image_search.js"></script>

</head>

<body>
    <div class="container-fluid" id="main">
        <div class="row" id="header">
            <h1>Semantic Text Analysis (Entity Extraction)</h1>
        </div>

        <div class="row">
            <div id="input_box" class="col-md-6 col-md-offset-3">
                <form id="form" method="post" action="">
                    <div class="form-group">
                        <label for="textInput">Input</label>
                        <textarea class="form-control" id="textInput" name="textInput"></textarea>
                    </div>
                    <input class="btn btn-default" type="submit" value="analyse" name="analyse_location">
                </form>
            </div>
        </div>

        <div class="row" id="maps">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div id="maps_box" class="glyphicon glyphicon-map-marker">
                    <h3>Locations</h3>
                </div>
            </div>
        </div>

        <div class="row" id="persons">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div id="persons_box" class="glyphicon glyphicon-user">
                    <h3>Persons</h3>
                </div>
            </div>
        </div>

        <div class="row" id="input">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div id="originalText_box" class="glyphicon glyphicon-pencil">
                    <h3>Your input</h3>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <div id="originalText">
                    <?php echo $originalText ?>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function getLocations() {
            var locationsJSON = '<?php
                                        if(isset($entities->entities->location)) {
                                            echo json_encode($entities->entities->location);
                                        }
                                    ?>';

            if (locationsJSON.length > 0) {
                return $.parseJSON(locationsJSON);
            } else {
                return '';
            }
        }

        function getPersons() {
            var personsJSON = '<?php
                                        if(isset($entities->entities->person)) {
                                            echo json_encode($entities->entities->person);
                                        }
                                    ?>';

            if (personsJSON.length > 0) {
                return $.parseJSON(personsJSON);
            } else {
                return '';
            }
        }

    </script>
</body>
</html>