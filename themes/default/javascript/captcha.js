(function ($cms, $util, $dom) {
    'use strict';

    var onLoadCallbackName = 'recaptchaLoaded' + $util.random();

    var soundObject;

    var recaptchaLoadedPromise = new Promise(function (resolve) {
        /* Called from reCAPTCHA's recaptcha/api.js, when it loads. */
        window[onLoadCallbackName] = function () {
            resolve();
            delete window[onLoadCallbackName];
        };
    });

    $cms.templates.inputCaptcha = function inputCaptcha(params, container) {
        if ($cms.configOption('js_captcha')) {
            $dom.html($dom.$('#captcha-spot'), params.captcha);
        } else {
            window.addEventListener('pageshow', function () {
                $cms.functions.refreshCaptcha(document.getElementById('captcha-readable'), document.getElementById('captcha-audio'));
            });
        }

        $cms.functions.initialiseAudioLink(container, document.getElementById('captcha-audio'));
    };

    $cms.functions.initialiseAudioLink = function initialiseAudioLink(container, audioCaptchaElement) {
        if (audioCaptchaElement) {
            soundObject = (typeof window.Audio !== 'undefined') ? new Audio(audioCaptchaElement.href) : null;

            $dom.on(container, 'click', '.js-click-play-self-audio-link', function (e, link) {
                e.preventDefault();
                $cms.playSelfAudioLink(link, soundObject);
            });
        }
    };

    // Implementation for [data-recaptcha-captcha]
    $cms.behaviors.initializeRecaptchaCaptcha = {
        attach: function attach(context) {
            var captchaEls = $util.once($dom.$$$(context, '[data-recaptcha-captcha]'), 'behavior.initializeRecaptchaCaptcha');

            if (captchaEls.length < 1) {
                return;
            }

            $cms.requireJavascript('https://www.google.com/recaptcha/api.js?render=explicit&onload=' + onLoadCallbackName + '&hl=' + $cms.userLang().toLowerCase());

            recaptchaLoadedPromise.then(function () {
                captchaEls.forEach(function (captchaEl) {
                    var form = $dom.parent(captchaEl, 'form'),
                        grecaptchaParameters;

                    captchaEl.dataset.recaptchaSuccessful = '0';

                    grecaptchaParameters = {
                        sitekey: $cms.configOption('recaptcha_site_key'),
                        callback: function () {
                            captchaEl.dataset.recaptchaSuccessful = '1';
                            $dom.submit(form);
                        },
                        theme: '{$?,{$THEME_DARK},dark,light}',
                        size: 'invisible'
                    };

                    if (captchaEl.dataset.tabindex != null) {
                        grecaptchaParameters.tabindex = captchaEl.dataset.tabindex;
                    }

                    // Decrease perceived page load time - the delay stops the browser 'spinning' while loading 13 URLs right away - people won't submit form within 5 seconds
                    setTimeout(function () {
                        window.grecaptcha.render(captchaEl, grecaptchaParameters, false);
                    }, 5000);

                    $dom.on(form, 'submit', function (e) {
                        if (!captchaEl.dataset.recaptchaSuccessful || (captchaEl.dataset.recaptchaSuccessful === '0')) {
                            e.preventDefault();
                            window.grecaptcha.execute();
                        }
                    });
                });
            });
        }
    };

    $cms.functions.captchaCaptchaAjaxCheck = function captchaCaptchaAjaxCheck() {
        var form = document.getElementById('main-form');

        if (!form) {
            form = document.getElementById('posting-form');
        }

        if ($cms.configOption('recaptcha_site_key') !== '') { // reCAPTCHA Enabled
            return;
        }

        // Need to set a timeout because CAPTCHA might appear via JS
        setTimeout(function () {
            var captchaEl = form.elements['captcha'],
                validValue;
            form.addEventListener('submit', function submitCheck(submitEvent) {
                var value = captchaEl.value;

                if ($dom.isCancelledSubmit(submitEvent) || (value === validValue)) {
                    return;
                }

                var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=captcha_wrong&name=' + encodeURIComponent(value) + $cms.keep();
                submitEvent.preventDefault();
                var submitBtn = form.querySelector('#submit-button');
                var promise = $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    } else {
                        $cms.functions.refreshCaptcha(document.getElementById('captcha-readable'), document.getElementById('captcha-audio'));
                    }

                    return valid;
                });

                $dom.awaitValidationPromiseAndResubmit(submitEvent, promise, submitBtn);
            });
        });
    };

    $cms.functions.refreshCaptcha = function refreshCaptcha(captchaReadable, audioCaptchaElement) {
        // Force it to reload latest captcha
        if (captchaReadable) {
            captchaReadable.src = $cms.addKeepStub('{$FIND_SCRIPT;,captcha}?mode=text&cache_break=' + $util.random());
        }
        if (audioCaptchaElement) {
            audioCaptchaElement.href = $cms.addKeepStub('{$FIND_SCRIPT;,captcha}?mode=audio&cache_break=' + $util.random()); // Directly .wav link (needed for Safari) won't be good anymore
            soundObject.src = audioCaptchaElement.href;
        }
    };
}(window.$cms, window.$util, window.$dom));
