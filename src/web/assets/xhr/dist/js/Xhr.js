(function () {
    var queue = [];

    var idle = function () {
        // Nothing to do? Check back in a moment:
        if (queue.length === 0) {
            window.setTimeout(idle, 500);

            return;
        }

        var token = queue.shift();
        var request = new XMLHttpRequest;

        request.addEventListener('loadend', idle);

        request.open('GET', '/index.php?token=' + token);
        request.send();
    };

    document.addEventListener('activity:track', function (e) {
        queue.push(e.detail.token);
    });

    // Kickstart the loop:
    idle();
})();
