(function ($cms) {
    'use strict';

    $cms.templates.wMainScreen = function wMainScreen(params, container) {
        $dom.on(container, 'click', '.js-click-set-hidemod-cookie', function (e, el) {
            $cms.setCookie('buildr_hide_mods', $cms.isIcon(el.querySelector('.icon'), 'trays/contract') ? '0' : '1', 'PERSONALIZATION');
        });

        $dom.on(container, 'click', '.js-click-set-type-edititem', function (e, el) {
            el.form.elements['type'].value = 'edititem';
        });
        $dom.on(container, 'click', '.js-click-set-type-confirm', function (e, el) {
            el.form.elements['type'].value = 'confirm';
        });
    };
}(window.$cms));
