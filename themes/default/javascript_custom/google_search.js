(function ($cms) {
    'use strict';

    $cms.templates.blockSideGoogleSearch = function blockSideGoogleSearch(params) {
        var cx = strVal(params.id);

        $cms.requireJavascript([
            'https://cse.google.com/cse.js?cx=' + cx
        ]).then(function () {
            if (document.getElementById('cse')) { // On results page
                var noSearchEntered = document.getElementById('no-search-entered');
                if (noSearchEntered) {
                    noSearchEntered.parentNode.removeChild(noSearchEntered);
                }
            }
        });
    };
}(window.$cms));
