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

    $cms.templates.translateLine = function (params, container) {
        $dom.on(container, 'change', '.js-textarea-translate-field', function (textarea) {
            var button = $dom.$('#translate-button'),
                hasEdits = false;

            if (textarea.value !== textarea.defaultValue) {
                hasEdits = true;
            } else {
                var fields = $dom.$$('.js-textarea-translate-field');
                for (var i = 0; i < fields.length; i++) {
                    if (fields[i].value !== fields[i].defaultValue) {
                        hasEdits = true;
                        break;
                    }
                }
            }

            button.disabled = !hasEdits;
            $dom.$('.js-translate-pagination').style.display = hasEdits ? 'none' : 'block';
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
