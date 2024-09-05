(function ($cms) {
    'use strict';

    /* eslint-disable camelcase */
    $cms.templates.blockMainMultiContentSlider = function blockMainMultiContentSlider(params) {
        if (!window.jQuery || !window.jQuery.fn.skitter) {
            $util.fatal('$cms.templates.blockMainMultiContentSlider(): jQuery.fn.skitter plugin is not loaded');
            return;
        }

        window.jQuery('#skitter-' + params.rand).skitter({
            auto_play: true,
            controls: true,
            dots: true,
            enable_navigation_keys: true,
            interval: params.mill,
            numbers_align: 'center',
            preview: true,
            progressbar: false,
            theme: 'clean',
            thumbs: false
        });
    };
}(window.$cms));
