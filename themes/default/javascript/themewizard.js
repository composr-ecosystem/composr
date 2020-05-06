(function ($cms, $util, $dom) {
    'use strict';

    $cms.functions.adminThemeWizardStep1 = function () {
        var form = document.getElementById('main-form');
        form.elements['source_theme'].addEventListener('change', function () {
            var defaultTheme = (form.elements['source_theme'].value === 'default');
            form.elements['algorithm'][0].checked = defaultTheme;
            form.elements['algorithm'][1].checked = !defaultTheme;
        });

        var validValue;
        form.addEventListener('submit', function submitCheck(submitEvent) {
            var value = form.elements['themename'].value;

            if (value === validValue) {
                return;
            }

            var submitBtn = form.querySelector('#submit-button');
            var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=exists_theme&name=' + encodeURIComponent(value) + $cms.keep();
            submitEvent.preventDefault();

            var promise = $cms.form.doAjaxFieldTest(url).then(function (valid) {
                if (valid) {
                    validValue = value;
                }

                return valid;
            });

            $dom.awaitValidationPromiseAndResubmit(submitEvent, promise, submitBtn);
        });
    };
}(window.$cms, window.$util, window.$dom));
