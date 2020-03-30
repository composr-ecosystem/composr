(function ($cms) {
    'use strict';

    $cms.templates.comcodeFlip = function comcodeFlip(params, container) {
        var $container = window.jQuery(container);
        $container.flip({
            speed: params.speed
        });
    };
}(window.$cms));
