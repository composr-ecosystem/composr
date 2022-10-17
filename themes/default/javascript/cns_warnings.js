(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.cnsSavedWarning = function cnsSavedWarning(params) {
        var id = $cms.filter.id(params.title);

        $dom.$(document.getElementById('saved-use--' + id), 'button').addEventListener('click', function (e) {
            var win = $cms.getMainCmsWindow();

            var explanation = win.document.getElementById('explanation');
            explanation.value = params.explanation;

            var message = win.document.getElementById('message');
            win.$editing.insertTextbox(message, params.message, false, params.messageHtml, true).then(function () {
                if (window.fauxClose !== undefined) {
                    window.fauxClose();
                } else {
                    window.close();
                }

            });

            e.preventDefault();
        });

        document.getElementById('saved-delete--' + id).getElementsByTagName('input')[1].addEventListener('click', function (e) {
            var form = this.form;

            $cms.ui.confirm(params.question, function (answer) {
                if (answer) {
                    $dom.trigger(form, 'submit');
                }
            });

            e.preventDefault();
        });
    };
}(window.$cms, window.$util, window.$dom));
