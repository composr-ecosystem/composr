(function ($cms, $util, $dom) {
    'use strict';

    var givePointsFormLastValid;
    $cms.templates.pointsGive = function pointsGive(params, container) {
        $dom.on(container, 'submit', '.js-submit-check-form', function (submitEvent, form) {
            if ($dom.isCancelledSubmit(submitEvent) || (givePointsFormLastValid && (givePointsFormLastValid.getTime() === $cms.form.lastChangeTime(form).getTime()))) {
                return;
            }

            submitEvent.preventDefault();

            var promise = $cms.form.checkForm(form, false).then(function (valid) {
                if (valid) {
                    givePointsFormLastValid = $cms.form.lastChangeTime(form);
                }

                return valid;
            });

            $dom.awaitValidationPromiseAndResubmit(submitEvent, promise);
        });
    };
}(window.$cms, window.$util, window.$dom));
