didactic = {
    config: {
        appId: "ffdf30be",
        appKey: "a8c46b72dee7f4565e03a6f1e8ab1b68",
        elForm: "#form",
        elOutput: "#output"
    },
    init: function() {
        didactic.assignEventHandlers();
    },
    assignEventHandlers: function () {
        $(didactic.config.elForm).submit(function (e) {
            e.preventDefault();
            $(didactic.config.elOutput).html("here comes the output");
        });
    }
};

$(window).load(function () {
    didactic.init();
});