(function ($cms) {
    'use strict';

    $cms.templates.facebookFooter = function facebookFooter() {
        var facebookAppid = $cms.configOption('facebook_appid');
        if (facebookAppid !== '') {
            // Load the SDK Asynchronously
            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/all.js#xfbml=1&appId=' + facebookAppid;
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        }
    };
}(window.$cms));
