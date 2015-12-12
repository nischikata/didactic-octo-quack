var API_KEY = "AIzaSyBNGqDyhvWM_4Sjb5tenngmclZxo6GEllw";
var CSE_ID = "009075917247977096438:lezriff3hew";
var _searchType = "image";
var _alt = "json";
var BOX_HEIGHT = 200.0;

$(document).ready(function () {
    var persons = getPersons();

    $.each(persons, function (index) {
        $("#persons").append("<div class='person_container col-xs-12 col-sm-6 col-md-3 col-lg-3'><div id='person" + index + "'></div>");
    });

    $.each(persons, function (index, value) {
        getImage(index, value);
    });

});

function getImage(_index, _person) {

    var params = {key: API_KEY, cx: CSE_ID, q: _person, searchType: _searchType, alt: _alt};

    $.ajax({
        type: "GET",
        url: "https://www.googleapis.com/customsearch/v1",
        contentType: "application/json; charset=utf-8",
        data: params,
        dataType: "json",
        success: function (data) {
            console.dir(data);
            //$("#person"+_index).append("<img src='"+data.items[0].link+"' alt='"+data.items[0].title+"' class='img-responsive'>");

            var image = $("<img>", {src: data.items[0].link, alt: data.items[0].title});
            var currentWidth = $("#maps_box").width();
            var tmpImg = new Image();

            tmpImg.src = data.items[0].link;
            $(tmpImg).on('load', function () {
                var factor = 1;
                var width = 1;

                if (tmpImg.height < BOX_HEIGHT) {
                    factor = BOX_HEIGHT / tmpImg.height;
                    width = tmpImg.width * factor;
                } else {
                    factor = tmpImg.height / BOX_HEIGHT;
                    width = Math.round(tmpImg.width / factor);
                }

                if (width >= currentWidth) {
                    image.addClass("adaptHeight");
                } else {
                    image.addClass("adaptWidth");
                }
            });

            $("#person" + _index).append(image);
        },
        error: function (xhr) {
            var _src = "http://www.aucklandoffroadracing.co.nz/site/wp-content/themes/desire/colors/light/images/default-slide-img.jpg";
            var image = $("<img>", {src: _src, alt: "default-image"});

            var currentWidth = $("#maps_box").width();

            var tmpImg = new Image();
            tmpImg.src = _src;
            $(tmpImg).on('load', function () {
                var factor = 1;
                var width = 1;

                if (tmpImg.height < BOX_HEIGHT) {
                    factor = BOX_HEIGHT / tmpImg.height;
                    width = tmpImg.width * factor;
                } else {
                    factor = tmpImg.height / BOX_HEIGHT;
                    width = Math.round(tmpImg.width / factor);
                }

                if (width >= currentWidth) {
                    image.addClass("adaptHeight");
                } else {
                    image.addClass("adaptWidth");
                }
            });

            $("#person" + _index).append(image);
        }
    });
}
