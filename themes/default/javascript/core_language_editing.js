(function ($cms, $util, $dom) {
    'use strict';

    var $translate = window.$translate = {};
    $translate.translate = function translate(id, old, langFrom, langTo) {
        id = strVal(id);
        old = strVal(old);
        langFrom = strVal(langFrom);
        langTo = strVal(langTo);

        var apiKey = '{$CONFIG_OPTION;,google_apis_api_key}';

        if (langFrom === langTo) {
            langFrom = 'EN';
        }

        var callbackName = 'googleTranslateCallback' + $util.random();

        window[callbackName] = function (response) {
            if (response.error) {
                $cms.ui.alert(response.error.message);
                return;
            }

            document.getElementById(id).value = response.data.translations[0].translatedText;
            delete window[callbackName];
        };

        var newScript = document.createElement('script');
        newScript.async = true;
        newScript.src = 'https://www.googleapis.com/language/translate/v2?key=' + encodeURIComponent(apiKey) + '&source=' + encodeURIComponent(langFrom) + '&target=' + encodeURIComponent(langTo) + '&callback=' + callbackName + '&q=' + encodeURIComponent(old);
        document.body.appendChild(newScript);
    };

    $cms.templates.translateScreen = function (params, container) {
        $dom.on(container, 'submit', '.js-form-submit-modsecurity-workaround', function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled() && !e.defaultPrevented) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
        });
    };

    $cms.templates.translateLine = function (params, container) {
        $dom.on(container, 'mouseover', '.js-mouseover-enable-textarea-translate-field', function () {
            var textarea = $dom.$(container, '.js-textarea-translate-field');
            textarea.disabled = false;
        });
    };

    $cms.templates.translateAction = function translateAction(params, container) {
        var name = strVal(params.name),
            old = strVal(params.old),
            langFrom = strVal(params.langFrom),
            langTo = strVal(params.langTo);

        $dom.on(container, 'click', function () {
            window.$translate.translate(name, old, langFrom, langTo);
        });

    };
}(window.$cms, window.$util, window.$dom));
