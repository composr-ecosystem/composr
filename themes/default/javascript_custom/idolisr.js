(function ($cms) {
    'use strict';

    $cms.templates.pointsGive = function pointsGive(params, container) {
        var givePointsFormLastValid;

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

        $dom.on(container, 'click', '.js-click-check-reason', function (e, el) {
            var reason = document.getElementById('give-reason');
            if ((reason.value.substr(reason.value.indexOf(': ')).length <= 3) && (el.selectedIndex !== 0)) {
                reason.value = el.value + ': ';
            }
        });

        $dom.on(container, 'change', '.js-change-check-reason', function (e, el) {
            var reason = document.getElementById('give-reason');
            if ((reason.value.substr(reason.value.indexOf(': ')).length <= 3) && (el.selectedIndex !== 0)) {
                reason.value = el.value + ': ';
            }
        });
    };
}(window.$cms));
