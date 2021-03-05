(function ($cms, $util, $dom) {
    'use strict';

    $cms.functions.adminThemeWizard = function () {
        var form = document.getElementById('main-form');
        form.elements['source_theme'].addEventListener('change', function () {
            var defaultTheme = (form.elements['source_theme'].value === 'default');
            form.elements['algorithm'][0].checked = defaultTheme;
            form.elements['algorithm'][1].checked = !defaultTheme;
        });

        var validValue;
        form.addEventListener('submit', function submitCheck(submitEvent) {
            var value = form.elements['name'].value;

            if (value === validValue) {
                return;
            }

            submitEvent.preventDefault();

            var submitBtn = form.querySelector('#submit-button');

            if (value != '') {
                var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=exists_theme&name=' + encodeURIComponent(value) + $cms.keep();
                var promise = $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    return valid;
                });
                $dom.awaitValidationPromiseAndResubmit(submitEvent, promise, submitBtn);
            } else {
                $dom.submit(form);
            }

        });
    };
}(window.$cms, window.$util, window.$dom));
