(function ($cms) {
    'use strict';

    $cms.templates.blockMainMultiContentSlider = function blockMainMultiContentSlider(params) {
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
