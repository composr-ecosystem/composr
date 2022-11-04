(function ($cms) {
    'use strict';

    $cms.functions.gfxRolloverButton = function gfxRolloverButton(combId, url) {
        $cms.ui.createRollover(combId, url);
    };

    $cms.functions.moduleAdminCustomComcode = function moduleAdminCustomComcode() {
        var tag = document.getElementById('tag');

        var updateFunc = function () {
            var e = document.getElementById('example');
            e.value = '[' + tag.value;
            var i = 0, param;
            do {
                param = document.getElementById('parameters_' + i);
                if ((param) && (param.value !== '')) {
                    e.value += ' ' + param.value.replace('=', '="') + '"';
                }
                i++;
            } while (param != null);
            e.value += '][/' + tag.value + ']';
        };

        var i = 0, param;
        do {
            param = document.getElementById('parameters_' + i);
            if (param) {
                param.addEventListener('blur', updateFunc);
            }
            i++;
        } while (param != null);
        tag.addEventListener('blur', function () {
            updateFunc();
            var title = document.getElementById('title');
            if (title.value === '') {
                title.value = tag.value.substr(0, 1).toUpperCase() + tag.value.substring(1, tag.value.length).replace(/_/g, ' ');
            }
        });
    };

    $cms.functions.moduleAdminCustomComcodeRunStart = function moduleAdminCustomComcodeRunStart() {
        var extraChecks = [],
            validValue;
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var value = form.elements['tag'].value;

            if ((value === validValue) || (value === '')) {
                return true;
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_tag&name=' + encodeURIComponent(form.elements['tag'].value) + $cms.keep();
                return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    if (!valid) {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form.elements['tag'];
                    }
                });
            };
        });
        return extraChecks;
    };
}(window.$cms));
