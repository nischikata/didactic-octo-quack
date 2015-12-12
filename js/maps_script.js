var maps = [];
var geocoder;

$(document).ready(function () {
    var locations = getLocations();
    if (locations.length > 0) {
        $.each(locations, function (index) {
            $("#maps").append("<div class='map_container col-xs-12 col-sm-6 col-md-3 col-lg-3'><div id='map" + index + "'></div></div>");
        });

        $.each(locations, function (index, value) {
            google.maps.event.addDomListener(window, 'load', initialize('map' + index, value));
        });
    }
});


function initialize(_map_id, _location) { // Map id for future manipulations
    geocoder = new google.maps.Geocoder();

    maps[_map_id] = new google.maps.Map($("#" + _map_id)[0], {
        center: {lat: -34.397, lng: 150.644}
    });

    codeAddress(_map_id, _location);
}


function codeAddress(_map_id, _location) {
    geocoder.geocode({'address': _location}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            maps[_map_id].setCenter(results[0].geometry.location);
            maps[_map_id].fitBounds(results[0].geometry.viewport);
            var marker = new google.maps.Marker({
                map: maps[_map_id], // target nedded map by second parametr
                position: results[0].geometry.location,
                title: _location
            });
        } else {
            $("#" + _map_id).remove();
            delete maps[_map_id];
        }
    });
}