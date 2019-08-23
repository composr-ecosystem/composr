(function ($cms, $util, $dom) {
    'use strict';

    $cms.functions.newsletterNewsletterForm = function newsletterNewsletterForm() {
        var form = document.getElementById('password').form;
        form.addEventListener('submit', function () {
            if ((form.elements['password_confirm']) && (form.elements['password_confirm'].value !== form.elements['password'].value)) {
                $cms.ui.alert('{!PASSWORD_MISMATCH;^}');
                return false;
            }
        });
    };

    $cms.templates.newsletterPreview = function (params) {
        var frameId = 'preview-frame',
            html = strVal(params.htmlPreview);

        setTimeout(function () {
            var adjustedPreview = html.replace(/<!DOCTYPE[^>]*>/i, '').replace(/<html[^>]*>/i, '').replace(/<\/html>/i, '');
            var de = window.frames[frameId].document.documentElement;
            var body = de.querySelector('body');
            if (!body) {
                $dom.html(de, adjustedPreview);
            } else {
                var headElement = de.querySelector('head');
                if (!headElement) {
                    headElement = document.createElement('head');
                    de.appendChild(headElement);
                }
                if (!de.querySelector('style') && adjustedPreview.indexOf('<head') !== -1) { /* The conditional is needed for Firefox - for some odd reason it is unable to parse any head tags twice */
                    $dom.html(headElement, adjustedPreview.replace(/^(.|\n)*<head[^>]*>((.|\n)*)<\/head>(.|\n)*$/i, '$2'));
                }

                $dom.html(body, adjustedPreview.replace(/^(.|\n)*<body[^>]*>((.|\n)*)<\/body>(.|\n)*$/i, '$2'));
            }

            $dom.resizeFrame(frameId, 300);
        }, 500);

        setInterval(function () {
            $dom.resizeFrame(frameId, 300);
        }, 1000);
    };

    $cms.templates.blockMainNewsletterSignup = function (params, container) {
        var nid = strVal(params.nid),
            validValue;

        $dom.on(container, 'submit', '.js-form-newsletter-email-subscribe', function (e, form) {
            var emailInput = form.elements['address' + nid];

            if (validValue === emailInput.value) {
                return;
            }

            e.preventDefault();

            if (!$cms.form.checkFieldForBlankness(emailInput)) {
                return;
            }

            if (!emailInput.value.match(/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9._-]+$/)) {
                $cms.ui.alert('{!javascript:NOT_A_EMAIL;}');
                return;
            }

            validValue = emailInput.value;

            $cms.ui.disableFormButtons(form);

            // Tracking
            $cms.gaTrack(null, '{!newsletter:NEWSLETTER_JOIN;}').then(function () {
                $dom.submit(form);
            });
        });
    };

    $cms.templates.periodicNewsletterRemove = function periodicNewsletterRemove(params, container) {
        $dom.on(container, 'click', '.js-click-btn-disable-self', function (e, btn) {
            setTimeout(function () {
                btn.disabled = true;
            }, 100);
        });
    };
}(window.$cms, window.$util, window.$dom));
