(function ($cms, $util, $dom) {
    'use strict';

    var onLoadCallbackName = 'recaptchaLoaded' + $util.random();

    var recaptchaLoadedPromise = new Promise(function (resolve) {
        /* Called from reCAPTCHA's recaptcha/api.js, when it loads. */
        window[onLoadCallbackName] = function () {
            resolve();
            delete window[onLoadCallbackName];
        };
    });

    // Implementation for [data-recaptcha-captcha]
    $cms.behaviors.initializeRecaptchaCaptch = {
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
        var form = document.getElementById('main-form'),
            submitBtn = document.getElementById('submit-button');

        if (!form) {
            form = document.getElementById('posting-form');
        }

        if ($cms.configOption('recaptcha_site_key') !== '') { // reCAPTCHA Enabled
            return;
        }

        // Need to set a timeout because CAPTCHA might appear via JS
        setTimeout(function() {
            var captchaEl = form.elements['captcha'],
                validValue;
            form.addEventListener('submit', function submitCheck(e) {
                var value = captchaEl.value;

                if (value === validValue) {
                    return;
                }

                var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=captcha_wrong&name=' + encodeURIComponent(value) + $cms.keep();
                e.preventDefault();
                submitBtn.disabled = true;
                $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                        $dom.submit(form);
                    } else {
                        document.getElementById('captcha').src += '&'; // Force it to reload latest captcha
                        submitBtn.disabled = false;
                    }
                });
            });
        });
    };
}(window.$cms, window.$util, window.$dom));
