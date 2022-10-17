(function ($cms) {
    'use strict';

    $cms.templates.pointsSend = function pointsSend(params, container) {
        var sendPointsFormLastValid;

        $dom.on(container, 'click', '.js-points-check-form', function (e, btn) {
            var form = btn.form;

            if ($dom.isCancelledSubmit(e) || (sendPointsFormLastValid && (sendPointsFormLastValid.getTime() === $cms.form.lastChangeTime(form).getTime()))) {
                return;
            }

            e.preventDefault();

            var promise = $cms.form.checkForm(e, form, false, []).then(function (valid) {
                if (valid) {
                    sendPointsFormLastValid = $cms.form.lastChangeTime(form);
                }

                return valid;
            });

            $dom.awaitValidationPromiseAndSubmitForm(e, promise);
        });

        $dom.on(container, 'click', '.js-click-check-send-options', function (e, el) {
            var anonymous = document.getElementById('points-anon-span');
            var role = document.getElementById('points-role-span');
            if (el.value === "send") {
                if (anonymous !== null) {
                    anonymous.style.display = "";
                }
                role.style.display = "";
            } else {
                if (anonymous !== null) {
                    anonymous.style.display = "none";
                }
                role.style.display = "none";
            }
        });

        $dom.on(container, 'change', '.js-change-check-send-options', function (e, el) {
            var anonymous = document.getElementById('points-anon-span');
            var role = document.getElementById('points-role-span');
            if (el.value === "send") {
                if (anonymous !== null) {
                    anonymous.style.display = "";
                }
                role.style.display = "";
            } else {
                if (anonymous !== null) {
                    anonymous.style.display = "none";
                }
                role.style.display = "none";
            }
        });
    };
}(window.$cms));
