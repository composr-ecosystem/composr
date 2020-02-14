(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.cnsSavedWarning = function cnsSavedWarning(params) {
        var id = $cms.filter.id(params.title);

        document.getElementById('saved-use--' + id).addEventListener('submit', function () {
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

            return false;
        });

        document.getElementById('saved-delete--' + id).getElementsByTagName('input')[1].addEventListener('click', function () {
            var form = this.form;

            $cms.ui.confirm(params.question, function (answer) {
                if (answer) {
                    $dom.submit(form);
                }
            });

            return false;
        });
    };
}(window.$cms, window.$util, window.$dom));
