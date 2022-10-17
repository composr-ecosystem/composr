(function ($cms, $util, $dom) {
    'use strict';

    $cms.functions.newsletterNewsletterForm = function newsletterNewsletterForm() {
        var extraChecks = [];
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) {
            if ((form.elements['password_confirm']) && (form.elements['password_confirm'].value !== form.elements['password'].value)) {
                $cms.ui.alert('{!PASSWORD_MISMATCH;^}');
                alerted.valueOf = function () { return true; };
                firstFieldWithError = form.elements['password_confirm'];
                return false;
            }
            return true;
        });
        return extraChecks;
    };

    $cms.templates.newsletterPreview = function (params) {
        var frameId = 'preview-frame',
            html = strVal(params.htmlPreview);

        var show_html_preview = function() {
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

                body.className = adjustedPreview.replace(/^(.|\n)*<body[^>]*\sclass="([^"]*)"(.|\n)*$/i, '$2');

                $dom.html(body, adjustedPreview.replace(/^(.|\n)*<body[^>]*>((.|\n)*)<\/body>(.|\n)*$/i, '$2'));
            }

            $dom.resizeFrame(frameId, 300);
        };

        var iframe = document.getElementById(frameId);
        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        if (iframeDoc.readyState  == 'complete') {
            show_html_preview();
        } else {
            iframe.onload = show_html_preview;
        }

        setInterval(function () {
            $dom.resizeFrame(frameId, 300);
        }, 1000);
    };

    $cms.templates.blockMainNewsletterSignup = function (params, container) {
        var nid = strVal(params.nid);

        $dom.on(container, 'click', '.js-newsletter-email-subscribe', function (e, btn) {
            if ($dom.isCancelledSubmit(e)) {
                return;
            }

            var form = btn.form;

            var emailInput = form.elements['address' + nid];

            if (!$cms.form.checkFieldForBlankness(emailInput)) {
                $dom.cancelSubmit(e);
                return;
            }

            if (!emailInput.value.match(/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9._-]+$/)) {
                $cms.ui.alert('{!javascript:NOT_A_EMAIL;}');
                $dom.cancelSubmit(e);
                return;
            }

            e.preventDefault();
            $cms.ui.disableFormButtons(form);

            // Tracking
            var promise = $cms.statsEventTrack(null, '{!newsletter:__NEWSLETTER_JOIN;}').then(function () {
                return true;
            });

            $dom.awaitValidationPromiseAndSubmitForm(e, promise);
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
