(function ($cms, $util, $dom) {
    'use strict';

    $cms.functions.moduleCmsQuiz = function moduleCmsQuiz() {
        document.getElementById('type').addEventListener('change', hideFunc);
        hideFunc();

        function hideFunc() {
            var ob = document.getElementById('type');
            if (ob.value === 'TEST') {
                document.getElementById('percentage').disabled = false;
                document.getElementById('num_winners').disabled = true;
            }
            if (ob.value === 'COMPETITION') {
                document.getElementById('num_winners').disabled = false;
                document.getElementById('percentage').disabled = true;
            }
            if (ob.value === 'SURVEY') {
                document.getElementById('text').value = document.getElementById('text').value.replace(/ \[\*\]/g, '');
                document.getElementById('num_winners').disabled = true;
                document.getElementById('percentage').disabled = true;
            }
        }
    };

    $cms.templates.quizScreen = function quizScreen(params, container) {
        var form = $dom.$(container, '.js-quiz-form'),
            timeout = Number(params.timeout) || 0,
            quizFormLastValid;

        $dom.on(container, 'submit', '.js-submit-check-form', function (submitEvent, form) {
            if ($dom.isCancelledSubmit(submitEvent) || (quizFormLastValid && (quizFormLastValid.getTime() === $cms.form.lastChangeTime(form).getTime()))) {
                return;
            }

            submitEvent.preventDefault();

            var promise = $cms.form.checkForm(form, false).then(function (valid) {
                if (valid) {
                    quizFormLastValid = $cms.form.lastChangeTime(form);
                }

                return valid;
            });

            $dom.awaitValidationPromiseAndResubmit(submitEvent, promise);
        });

        if (timeout > 0) {
            setTimeout(function () {
                $dom.submit(form);
            }, timeout * 1000);

            setInterval(function () {
                iterateCountdown(-1);
            }, 1000);
            iterateCountdown(0); // Because quiz_timer_minutes_and_seconds needs setting correctly
        }

        var e = $dom.$$(form, '.field-input');
        for (var i = 0; i < e.length; i++) {
            $cms.form.setUpChangeMonitor(e[i]);
        }
    };

    function iterateCountdown(dif) {
        var st = document.getElementById('quiz-timer');
        var newValue = parseInt($dom.html(st)) + dif;
        if (newValue >= 0) {
            $dom.html(st, newValue);
        }

        var st2 = document.getElementById('quiz-timer-minutes-and-seconds');
        if (st2) {
            if (newValue >= 0) {
                var v = '';
                v += Math.floor(newValue / 60);
                v += ':';
                if (newValue % 60 < 10) {
                    v += '0';
                }
                v += newValue % 60;
                $dom.html(st2, v);
            }
        }
    }
}(window.$cms, window.$util, window.$dom));
