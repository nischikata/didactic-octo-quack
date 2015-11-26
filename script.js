didactic = {
    config: {
        appId: "ffdf30be",
        appKey: "a8c46b72dee7f4565e03a6f1e8ab1b68",
        elForm: "#form",
        elOutput: "#output"
    },
    init: function () {
        didactic.assignEventHandlers();
    },
    assignEventHandlers: function () {
        $(didactic.config.elForm).submit(function (e) {
            e.preventDefault();

            var input = $(e.target).children("textarea").val();

            didactic.callAPI("entities",
                {text: input, language: "en"},
                function (result) { // done
                    console.dir(result);
                }
            );

            $(didactic.config.elOutput).html("here comes the output");
        });
    },
    callAPI: function (_url, _data, _callback) {
        $.ajax({
                url: "https://api.aylien.com/api/v1/"+_url,
                type: "POST",
                //beforeSend: function (xhr) {
                //    xhr.setRequestHeader("X-AYLIEN-TextAPI-Application-ID", didactic.config.appId);
                //    xhr.setRequestHeader("X-AYLIEN-TextAPI-Application-Key", didactic.config.appKey);
                //},
                data: _data,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-AYLIEN-TextAPI-Application-ID': didactic.config.appId,
                    'X-AYLIEN-TextAPI-Application-Key': didactic.config.appKey,
                },
                dataType: 'json',
            }, function (response) {
                if (_callback) {
                    var retVal = $.parseJSON(response);
                    _callback(retVal);
                }
            }
        )
        ;
    }
}
;

$(window).load(function () {
    didactic.init();
});

function byteCount(s) {
    return encodeURI(s).split(/%..|./).length - 1;
}