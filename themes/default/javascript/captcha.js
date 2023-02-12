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
            soundObject = (window.Audio !== undefined) ? new Audio(audioCaptchaElement.href) : null;

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

                    form.lastSubmitEvent = null;

                    $util.inform('Google reCAPTCHA: Initialised');

                    grecaptchaParameters = {
                        sitekey: $cms.configOption('recaptcha_site_key'),
                        callback: function () {
                            captchaEl.dataset.recaptchaSuccessful = '1';

                            $util.inform('Google reCAPTCHA: Passed');

                            if (form.lastSubmitEvent && $dom.isCancelledSubmit(form.lastSubmitEvent)) {
                                $util.inform('Google reCAPTCHA: Submission cancelled due to failing validation');

                                return;
                            }

                            var submitButton = $dom.$(form, 'input[type="submit"], button[type="submit"]');
                            if (submitButton) {
                                $util.inform('Google reCAPTCHA: Re-clicking button');

                                $dom.trigger(submitButton, 'click');
                            } else {
                                $util.inform('Google reCAPTCHA: Re-submitting button');

                                form.submit();
                            }
                        },
                        theme: '{$?,{$THEME_DARK},dark,light}',
                        size: 'invisible'
                    };

                    if (captchaEl.dataset.tabindex != null) {
                        grecaptchaParameters.tabindex = captchaEl.dataset.tabindex;
                    }

                    window.grecaptcha.render(captchaEl, grecaptchaParameters, false);

                    if (form.extraChecks === undefined) {
                        form.extraChecks = [];
                    }

                    form.extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
                        if (!captchaEl.dataset.recaptchaSuccessful || (captchaEl.dataset.recaptchaSuccessful === '0')) {
                            // CAPTCHA either not run yet, or failed, so execute it (grecaptchaParameters.callback will submit the form if it passes)
                            window.grecaptcha.execute();

                            $util.inform('Validation flow terminated due to Google reCAPTCHA not running yet; running now');
                            alerted.valueOf = function () { return true; }; // Don't show a form-not-filled in error, as it's not that

                            return false;
                        }
                        return true;
                    });
                });
            });
        }
    };

    $cms.functions.captchaCaptchaAjaxCheck = function captchaCaptchaAjaxCheck() {
        if ($cms.configOption('recaptcha_site_key') !== '') { // reCAPTCHA Enabled
            return;
        }

        var extraChecks = [],
            validValues = null;
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var values = [],
                captchaValues = [],
                captchaElements = [],
                catchaValuesExpected = 0,
                questionCaptcha = false;
            for (var i = 0; i < form.elements.length; i++) {
                if ((form.elements[i].name !== undefined) && (form.elements[i].name.match(/^captcha(_|$)/))) {
                    if (form.elements[i].name.indexOf('_') != -1) {
                        questionCaptcha = true;
                    }

                    captchaElements.push(form.elements[i]);
                    if (form.elements[i].value != '') {
                        captchaValues.push(form.elements[i].value);
                    }
                    values.push(form.elements[i].value);
                    catchaValuesExpected++;
                }
            }

            if ((validValues !== null) && (validValues.length === values.length)) {
                var areSame = validValues.every(function (element, index) {
                    return element === values[index];
                });

                if (areSame) {
                    // All valid
                    return true;
                }
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=captcha_wrong&name=' + encodeURIComponent(captchaValues.join('||'));
                if (questionCaptcha) {
                    url += '&question_captcha=1';
                }
                url += $cms.keep();
                return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValues = captchaValues;
                    } else {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = captchaElements[0];
                        validValues = [];
                        for (var i = 0; i < captchaValues.length; i++) {
                            validValues.push(null);
                        }

                        $cms.functions.refreshCaptcha(document.getElementById('captcha-readable'), document.getElementById('captcha-audio'));
                    }
                });
            };
        });
        return extraChecks;
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
