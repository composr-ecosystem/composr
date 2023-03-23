(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.cnsSavedWarning = function cnsSavedWarning(params, container) {
        $dom.on(container, 'click', '.js-use-warning', function () {
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
        });

        $dom.on(container, 'click', '.js-delete-warning', function (e) {
            e.preventDefault();

            var form = this.form;
            $cms.ui.confirm(params.question, function (answer) {
                if (answer) {
                    form.submit();
                }
            });
        });
    };
}(window.$cms, window.$util, window.$dom));
