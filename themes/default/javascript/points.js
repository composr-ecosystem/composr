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

        $dom.on(container, 'click', '.js-click-check-gift-options', function (e, el) {
            var anonymous = document.getElementById('points-anon-span');
            var payee = document.getElementById('points-payee-span');
            if (el.value === "gift") {
                anonymous.style.display = "";
                if (payee) payee.style.display = "";
            } else {
                anonymous.style.display = "none";
                if (payee) payee.style.display = "none";
            }
        });

        $dom.on(container, 'change', '.js-change-check-gift-options', function (e, el) {
            var anonymous = document.getElementById('points-anon-span');
            var payee = document.getElementById('points-payee-span');
            if (el.value === "gift") {
                anonymous.style.display = "";
                if (payee) payee.style.display = "";
            } else {
                anonymous.style.display = "none";
                if (payee) payee.style.display = "none";
            }
        });
    };
}(window.$cms, window.$util, window.$dom));
