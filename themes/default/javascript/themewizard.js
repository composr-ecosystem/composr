(function ($cms) {
    'use strict';

    $cms.functions.adminThemeWizard = function () {
        var form = document.getElementById('source_theme').form;

        form.elements['source_theme'].addEventListener('change', function () {
            var defaultTheme = (form.elements['source_theme'].value === 'default');
            form.elements['algorithm'][0].checked = defaultTheme;
            form.elements['algorithm'][1].checked = !defaultTheme;
        });

        var setUseOnAllState = function () {
            if (form.elements['name'].value === '') {
                form.elements['use_on_all'].disabled = true;
                form.elements['use_on_all'].checked = false;
                form.elements['use_on_all'].indeterminate = true;
            } else {
                form.elements['use_on_all'].disabled = false;
                form.elements['use_on_all'].indeterminate = false;
            }
        };
        setUseOnAllState();
        form.elements['name'].addEventListener('keyup', setUseOnAllState);

        var extraChecks = [],
            validValue;
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var value = form.elements['name'].value;

            if ((value === validValue) || (value === '')) {
                return true;
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=exists_theme&name=' + encodeURIComponent(value) + $cms.keep();
                return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    if (!valid) {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form.elements['name'];
                    }
                });
            };
        });
        return extraChecks;
    };
}(window.$cms));
