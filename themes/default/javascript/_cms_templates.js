/* This file contains CMS-wide Templates */

(function ($cms, $util, $dom) {
    'use strict';

    /**
     * Addons will add template related methods under this namespace
     * @namespace $cms.templates
     */
    $cms.templates = {};

    $cms.templates.globalHtmlWrap = function () {
        if (document.getElementById('global-messages-2')) {
            var m1 = document.getElementById('global-messages');
            if (!m1) {
                return;
            }
            var m2 = document.getElementById('global-messages-2');
            $dom.append(m1, $dom.html(m2));
            m2.parentNode.removeChild(m2);
        }

        if (boolVal($cms.pageUrl().searchParams.get('wide_print'))) {
            try {
                window.print();
            } catch (ignore) {
                // continue
            }
        }
    };

    $cms.templates.blockMainScreenActions = function blockMainScreenActions(params, container) {
        var urlEncodedCanonicalUrl = strVal(params.urlEncodedCanonicalUrl);
        $dom.on(container, 'click', '.js-click-print-screen', function () {
            $cms.statsEventTrack(null, '{!recommend:PRINT_THIS_SCREEN;}', null, null, null, true);
        });

        $dom.on(container, 'click', '.js-click-add-to-facebook', function () {
            $cms.statsEventTrack(null, 'social__facebook', null, null, null, true);
        });

        $dom.on(container, 'click', '.js-click-add-to-twitter', function (e, link) {
            link.href = 'https://twitter.com/share?count=horizontal&counturl=' + urlEncodedCanonicalUrl + '&original_referer=' + urlEncodedCanonicalUrl + '&text=' + encodeURIComponent(document.title) + '&url=' + urlEncodedCanonicalUrl;

            $cms.statsEventTrack(null, 'social__twitter', null, null, null, true);
        });

        $dom.on(container, 'click', '.js-click-add-to-linkedin', function () {
            $cms.statsEventTrack(null, 'social__linkedin', null, null, null, true);
        });

        $dom.on(container, 'click', '.js-click-add-to-pinterest', function () {
            $cms.statsEventTrack(null, 'social__pinterest', null, null, null, true);
        });

        $dom.on(container, 'click', '.js-click-add-to-tumblr', function () {
            $cms.statsEventTrack(null, 'social__tumblr', null, null, null, true);
        });

        $dom.on(container, 'click', '.js-click-add-to-vkontakte', function () {
            $cms.statsEventTrack(null, 'social__vkontakte', null, null, null, true);
        });

        $dom.on(container, 'click', '.js-click-add-to-sina-weibo', function () {
            $cms.statsEventTrack(null, 'social__sina_weibo', null, null, null, true);
        });

        $dom.on(container, 'click', '.js-click-add-to-qzone', function () {
            $cms.statsEventTrack(null, 'social__qzone', null, null, null, true);
        });
    };

    $cms.functions.abstractFileManagerGetAfmForm = function abstractFileManagerGetAfmForm() {
        var usesFtp = document.getElementById('uses_ftp');
        if (!usesFtp) {
            return;
        }

        ftpTicker();
        usesFtp.onclick = ftpTicker;

        function ftpTicker() {
            var form = usesFtp.form;
            form.elements['ftp_domain'].disabled = !usesFtp.checked;
            form.elements['ftp_directory'].disabled = !usesFtp.checked;
            form.elements['ftp_username'].disabled = !usesFtp.checked;
            form.elements['ftp_password'].disabled = !usesFtp.checked;
            form.elements['remember_password'].disabled = !usesFtp.checked;
        }
    };

    $cms.templates.standaloneHtmlWrap = function (params) {
        if (window.parent) {
            $dom.load.then(function () {
                document.body.classList.add('frame');

                try {
                    $dom.triggerResize();
                } catch (e) {
                    // continue
                }

                setTimeout(function () { // LEGACY: Needed for IE10
                    try {
                        $dom.triggerResize();
                    } catch (e) {
                        // continue
                    }
                }, 1000);
            });
        }

        if (params.isPreview) {
            $cms.form.disablePreviewScripts();
        }
    };

    $cms.templates.jsRefresh = function (params) {
        if (!window.location.hash.includes('redirected_once')) {
            window.location.hash = 'redirected_once';
            document.getElementById(params.formName).submit();
        } else {
            window.history.go(-2); // We've used back button, don't redirect forward again
        }
    };

    $cms.templates.forumsEmbed = function () {
        var frame = this;
        setInterval(function () {
            $dom.resizeFrame(frame.name);
        }, 500);
    };

    $cms.templates.massSelectFormButtons = function (params, delBtn) {
        var form = delBtn.form;

        $dom.on(delBtn, 'click', function () {
            $cms.ui.confirm('{!_ARE_YOU_SURE_DELETE;^}').then(function (result) {
                if (result) {
                    var idEl = $dom.$id('id'),
                        ids = (idEl.value === '') ? [] : idEl.value.split(',');

                    for (var i = 0; i < ids.length; i++) {
                        prepareMassSelectMarker('', params.type, ids[i], true);
                    }

                    form.method = 'post';
                    form.action = params.actionUrl;
                    form.target = '_top';
                    form.submit();
                }
            });
        });

        $dom.on('#id', 'change', initialiseButtonVisibility);
        initialiseButtonVisibility();

        function initialiseButtonVisibility() {
            var id = $dom.$('#id'),
                ids = (id.value === '') ? [] : id.value.split(/,/);

            $dom.$('#submit-button').disabled = (ids.length !== 1);
            $dom.$('#mass-select-button').disabled = (ids.length === 0);
        }
    };

    $cms.templates.massSelectDeleteForm = function (e, form) {
        var confirmedFor;

        $dom.on(form, 'click', 'button', function (e) {
            if ($dom.isCancelledSubmit(e)) {
                return;
            }

            if (confirmedFor && (confirmedFor.getTime() === $cms.form.lastChangeTime(form).getTime())) {
                return;
            }

            e.preventDefault();

            var promise = $cms.ui.confirm('{!_ARE_YOU_SURE_DELETE;^}').then(function (result) {
                if (result) {
                    confirmedFor = $cms.form.lastChangeTime(form);
                }

                return result;
            });

            $dom.awaitValidationPromiseAndSubmitForm(e, promise, null, form);
        });
    };

    $cms.templates.groupMemberTimeoutManageScreen = function groupMemberTimeoutManageScreen(params, container) {
        $dom.on(container, 'focus', '.js-focus-update-ajax-member-list', function (e, input) {
            if (input.value === '') {
                $cms.form.updateAjaxMemberList(input, null, true, e);
            }
        });

        $dom.on(container, 'keyup', '.js-keyup-update-ajax-member-list', function (e, input) {
            $cms.form.updateAjaxMemberList(input, null, false, e);
        });
    };

    $cms.templates.uploadSyndicationSetupScreen = function (params) {
        var winParent = window.parent || window.opener,
            id = 'upload_syndicate__' + params.hook + '__' + params.name,
            el = winParent.document.getElementById(id);

        el.checked = true;

        setTimeout(function () {
            if (window.fauxClose !== undefined) {
                window.fauxClose();
            } else {
                window.close();
            }
        }, 4000);
    };

    $cms.templates.blockMainComcodePageChildren = function blockMainComcodePageChildren() {};

    function onclickConfirmRememberMe(e, checkbox) {
        var checkboxWasFocused = (document.activeElement === checkbox);

        if (checkbox.checked) {
            $cms.ui.confirm('{!REMEMBER_ME_COOKIE;,{$SITE_NAME}}').then(function (answer) {
                if (!answer) {
                    checkbox.checked = false;
                }

                if (checkboxWasFocused) {
                    checkbox.focus();
                }
            });
        }
    }

    $cms.templates.loginScreen = function loginScreen(params, container) {
        if ((document.activeElement != null) || (document.activeElement !== $dom.$('#password'))) {
            try {
                $dom.$('#login_username').focus();
            } catch (ignore) {
                // continue
            }
        }

        $dom.on(container, 'click', '.js-click-confirm-remember-me', onclickConfirmRememberMe);

        $dom.on(container, 'click', '.js-check-login-username-field', function (e, btn) {
            var form = btn.form;

            if ($cms.form.checkFieldForBlankness(form.elements['username'])) {
                $cms.ui.disableFormButtons(form);
            } else {
                e.preventDefault();
            }
        });
    };

    $cms.templates.blockTopLogin = function (blockTopLogin, container) {
        $dom.on(container, 'click', '.js-top-login', function (e, btn) {
            var form = btn.form;

            if ($cms.form.checkFieldForBlankness(form.elements['username'])) {
                $cms.ui.disableFormButtons(form);
            } else {
                e.preventDefault();
            }
        });

        $dom.on(container, 'click', '.js-click-confirm-remember-me', onclickConfirmRememberMe);
    };

    $cms.templates.ipBanScreen = function (params, container) {
        var textarea = container.querySelector('#bans');
        $cms.ui.manageScrollHeight(textarea);

        if (!$cms.isMobile()) {
            $dom.on(container, 'keyup', '#bans', function (e, textarea) {
                $cms.ui.manageScrollHeight(textarea);
            });
        }
    };

    $cms.templates.jsBlock = function jsBlock(params) {
        $cms.callBlock(params.blockCallUrl, '', document.getElementById(params.jsBlockId), false, false, null, false, false);
    };

    $cms.templates.massSelectMarker = function (params, container) {
        $dom.on(container, 'click', '.js-chb-prepare-mass-select', function (e, checkbox) {
            var massSelectablEl = $dom.parent(checkbox, '[data-mass-selectable]');
            if (massSelectablEl != null) {
                massSelectablEl.classList.toggle('is-mass-selected', checkbox.checked);
            }

            prepareMassSelectMarker(params.supportMassSelect, params.type, params.id, checkbox.checked);
        });
    };

    $cms.templates.blockTopPersonalStats = function () {};

    $cms.templates.blockTopLanguage = function () {};

    $cms.templates.blockSidePersonalStatsNo = function blockSidePersonalStatsNo(params, container) {
        $dom.on(container, 'click', '.js-check-login-username-field-block', function (e, btn) {
            var form = btn.form;

            if ($cms.form.checkFieldForBlankness(form.elements['username'])) {
                $cms.ui.disableFormButtons(form);
            } else {
                e.preventDefault();
            }
        });

        $dom.on(container, 'click', '.js-click-checkbox-remember-me-confirm', onclickConfirmRememberMe);
    };

    $cms.templates.memberTooltip = function (params, container) {
        var submitter = strVal(params.submitter),
            loadTooltipPromise;

        $dom.on(container, 'mouseover', '.js-mouseover-activate-member-tooltip', function (e, el) {
            el.cancelled = false;

            if (loadTooltipPromise == null) {
                loadTooltipPromise = $cms.loadSnippet('member_tooltip&member_id=' + submitter);
            }

            loadTooltipPromise.then(function (result) {
                if (!el.cancelled) {
                    $cms.ui.activateTooltip(el, e, result, 'auto', null, null, false, true);
                }
            });
        });

        $dom.on(container, 'mouseout', '.js-mouseout-deactivate-member-tooltip', function (e, el) {
            $cms.ui.deactivateTooltip(el);
            el.cancelled = true;
        });
    };

    $cms.templates.resultsLauncherContinue = function resultsLauncherContinue(params, link) {
        var max = params.max,
            urlStub = params.urlStub,
            numPages = params.numPages,
            message = $util.format('{!javascript:ENTER_PAGE_NUMBER;^}', [numPages]);

        $dom.on(link, 'click', function () {
            $cms.ui.prompt(message, numPages, function (res) {
                if (!res) {
                    return;
                }

                res = parseInt(res);
                if ((res >= 1) && (res <= numPages)) {
                    $util.navigate(urlStub + (urlStub.includes('?') ? '&' : '?') + 'start=' + (max * (res - 1)));
                }
            }, '{!JUMP_TO_PAGE;^}');
        });
    };

    $cms.templates.doNextItem = function doNextItem(params, container) {
        var rand = params.randDoNextItem,
            url = params.url,
            target = params.target,
            warning = params.warning,
            autoAdd = params.autoAdd;

        $dom.on(container, 'focusin focusout', function (e) {
            container.classList.toggle('focus', e.type === 'focusin');
        });

        $dom.on(container, 'click', function (e) {
            var clickedLink = $dom.closest(e.target, 'a', container);

            if (!clickedLink) {
                $util.navigate(url, target);
                return;
            }

            if (autoAdd) {
                e.preventDefault();
                $cms.ui.confirm('{!KEEP_ADDING_QUESTION;^}', function (answer) {
                    var append = '';
                    if (answer) {
                        append += url.includes('?') ? '&' : '?';
                        append += autoAdd + '=1';
                    }
                    $util.navigate(url + append, target);
                });
                return;
            }

            if (warning && clickedLink.classList.contains('js-click-confirm-warning')) {
                e.preventDefault();
                $cms.ui.confirm(warning, function (answer) {
                    if (answer) {
                        $util.navigate(url, target);
                    }
                });
            }
        });

        var docEl = document.getElementById('doc-' + rand),
            docElHtml = docEl && $dom.html(docEl),
            helpEl = document.getElementById('help'),
            origHelpElHtml = helpEl ? $dom.html(helpEl) : null;

        if (docEl && helpEl && docElHtml) {
            /* Do-next document tooltips */
            $dom.on(container, 'mouseover', function (e) {
                if (container.contains(e.relatedTarget)) {
                    return;
                }

                var helpElHtml = $dom.html(helpEl);

                if (helpElHtml !== docElHtml) {
                    $dom.stop(helpEl, true).then(function () {
                        helpEl.style.opacity = 0;
                        $dom.html(helpEl, docElHtml);
                        $dom.fadeTo(helpEl, 'fast', 1);

                        helpEl.classList.remove('global-helper-panel-text');
                        helpEl.classList.add('global-helper-panel-text-over');
                    });

                }
            });

            $dom.on(container, 'mouseout', function (e) {
                if (container.contains(e.relatedTarget)) {
                    return;
                }

                var helpElHtml = $dom.html(helpEl);

                if (helpElHtml !== origHelpElHtml) {
                    $dom.stop(helpEl, true).then(function () {
                        helpEl.style.opacity = 0;
                        $dom.html(helpEl, origHelpElHtml);
                        $dom.fadeTo(helpEl, 'fast', 1);

                        helpEl.classList.remove('global-helper-panel-text-over');
                        helpEl.classList.add('global-helper-panel-text');
                    });
                }
            });
        }

        if (autoAdd) {
            var links = $dom.$$(container, 'a');

            links.forEach(function (link) {
                link.onclick = function (event) {
                    event.preventDefault();
                    $cms.ui.confirm(
                        '{!KEEP_ADDING_QUESTION;^}',
                        function (test) {
                            if (test) {
                                link.href += link.href.includes('?') ? '&' : '?';
                                link.href += autoAdd + '=1';
                            }

                            $util.navigate(link);
                        }
                    );
                };
            });
        }
    };

    $cms.templates.internalisedAjaxScreen = function internalisedAjaxScreen(params, element) {
        var url = strVal(params.url),
            changeDetectionUrl = strVal(params.changeDetectionUrl),
            refreshTime = Number(params.refreshTime) || 0,
            refreshIfChanged = strVal(params.refreshIfChanged);

        if (changeDetectionUrl && (refreshTime > 0)) {
            window.ajaxScreenDetectInterval = setInterval(function () {
                detectChange(changeDetectionUrl, refreshIfChanged, function () {
                    if (document.hidden) {
                        return; /* Don't hurt server performance needlessly when running in a background tab - let an e-mail notification alert them instead */
                    }

                    if (!document.getElementById('post') || (document.getElementById('post').value === '')) {
                        $cms.callBlock(url, '', element, false, true, null, true).then(function () {
                            detectedChange();
                        });
                    }
                });
            }, refreshTime * 1000);
        }
    };

    $cms.templates.ajaxPagination = function ajaxPagination(params) {
        var wrapperEl = $dom.elArg('#' + params.wrapperId),
            infiniteScrollCallUrl = params.infiniteScrollCallUrl;

        if (infiniteScrollCallUrl) {
            $cms.ui.enableInternaliseInfiniteScrolling(infiniteScrollCallUrl, wrapperEl);
        }
    };

    $cms.templates.confirmScreen = function confirmScreen() {};

    $cms.templates.warnScreen = function warnScreen() {
        if (window.top !== window) {
            $dom.triggerResize();
        }
    };

    $cms.templates.fatalScreen = function fatalScreen() {
        if (window.top !== window) {
            $dom.triggerResize();
        }
    };

    $cms.templates.columnedTableScreen = function columnedTableScreen(params) {
        if (params.jsFunctionCalls != null) {
            $cms.executeJsFunctionCalls(params.jsFunctionCalls);
        }
    };

    $cms.templates.questionUiButtons = function questionUiButtons(params, container) {
        $dom.on(container, 'click', '.js-click-close-window-with-val', function (e, clicked) {
            window.returnValue = clicked.dataset.tpReturnValue;

            if (window.fauxClose !== undefined) {
                window.fauxClose();
            } else {
                try {
                    window.$cms.getMainCmsWindow().focus();
                } catch (ignore) {
                    // continue
                }

                window.close();
            }
        });
    };

    $cms.templates.buttonScreenItem = function buttonScreenItem(params, btn) {
        var onclickCallFunctions = params.onclickCallFunctions;
        var onmousedownCallFunctions = params.onmousedownCallFunctions;

        if (onclickCallFunctions != null) {
            $dom.on(btn, 'click', function (e) {
                var funcs = JSON.parse(JSON.stringify(onclickCallFunctions));

                e.preventDefault();

                funcs.forEach(function (func) {
                    func.push(e);
                });

                $cms.executeJsFunctionCalls(funcs, btn);
            });
        }

        if (onmousedownCallFunctions != null) {
            $dom.on(btn, 'mousedown', function (e) {
                var funcs = JSON.parse(JSON.stringify(onmousedownCallFunctions));

                funcs.forEach(function (func) {
                    func.push(e);
                });

                $cms.executeJsFunctionCalls(funcs, btn);
            });
        }
    };

    $cms.functions.spamWarning = function (e) {
        if (e.which === 2/*middle button*/) {
            this.href += '&spam=1';
        } else {
            this.href = this.href.replace(/&spam=1/g, '');
        }
    };

    $cms.templates.tooltip = function (params, el) {
        var tooltipText = params.tooltip;

        $dom.on(el, 'mouseover', function (e) {
            var win = $cms.getMainCmsWindow(true);
            win.$cms.ui.activateTooltip(el, e, tooltipText, '40%', null, null, null, null, false, false, win);
        });
    };

    $cms.templates.handleConflictResolution = function (params) {
        var pingUrl = strVal(params.pingUrl);

        // eslint-disable-next-line no-constant-condition
        if ('{$VALUE_OPTION;,disable_handle_conflict_resolution}' === '1') {
            return;
        }

        if (pingUrl) {
            var previousPingFinished = false;
            $cms.doAjaxRequest(pingUrl, function () {
                previousPingFinished = true;
            });

            setInterval(function () {
                if (previousPingFinished) {
                    previousPingFinished = false;
                    $cms.doAjaxRequest(pingUrl, function () {
                        previousPingFinished = true;
                    }, null, 2000);
                }
            }, 12000);
        }
    };

    $cms.templates.indexScreenFancierScreen = function indexScreenFancierScreen(params) {
        if (document.getElementById('search-content')) {
            document.getElementById('search-content').value = strVal(params.rawSearchString);
        }
    };

    $cms.templates.doNextScreen = function doNextScreen() {};

    function detectChange(changeDetectionUrl, refreshIfChanged, callback) {
        $cms.doAjaxRequest(changeDetectionUrl, null, 'refresh_if_changed=' + encodeURIComponent(refreshIfChanged)).then(function (xhr) {
            var response = strVal(xhr.responseText);
            if (response === '1') {
                clearInterval(window.ajaxScreenDetectInterval);
                $util.inform('detectChange(): Change detected');
                callback();
            }
        });
    }

    function detectedChange() {
        $util.inform('detectedChange(): Change notification running');

        try {
            window.focus();
        } catch (e) {
            // continue
        }

        var soundUrl = 'data/sounds/message_received.mp3',
            baseUrl = $util.rel((!soundUrl.includes('data_custom') && !soundUrl.includes('uploads/')) ? $cms.getBaseUrl() : $cms.getCustomBaseUrl()),
            soundObject = window.soundManager.createSound({ url: baseUrl + '/' + soundUrl });

        if (soundObject && document.hasFocus()/*don't want multiple tabs all pinging*/) {
            soundObject.play();
        }

        var myToast = Toastify({ // eslint-disable-line no-undef
            text: '{!CONTENT_CHANGE_DETECTED;/}',
            duration: 4000
        });
        myToast.showToast();
    }

    $cms.functions.decisionTreeRender = function decisionTreeRender(parameter, value, notice, noticeTitle) {
        value = strVal(value);
        var els = document.getElementById('main-form').elements[parameter];
        if (els.length === undefined) {
            els = [els];
        }
        $util.toArray(els).forEach(function (el) {
            el.addEventListener('click', function () {
                var selected = false;
                if (el.type === 'checkbox') {
                    selected = (el.checked && (el.value === value)) || (!el.checked && ('' === value));
                } else {
                    selected = (el.value === value);
                }
                if (selected) {
                    $cms.ui.alert(notice, noticeTitle, true);
                }
            });
        });
    };

    function prepareMassSelectMarker(set, type, id, checked) {
        var massDeleteForm = document.getElementById('mass-select-form--' + set);
        if (!massDeleteForm) {
            massDeleteForm = document.getElementById('mass-select-button').form;
        }
        var key = type + '_' + id;
        var hidden;
        if (massDeleteForm.elements[key] === undefined) {
            hidden = document.createElement('input');
            hidden.className = 'js-key-to-delete';
            hidden.type = 'hidden';
            hidden.name = key;
            massDeleteForm.appendChild(hidden);
        } else {
            hidden = massDeleteForm.elements[key];
        }
        hidden.value = checked ? '1' : '0';

        var hasKeysToDelete = checked || Boolean(massDeleteForm.querySelector('input[value="1"].js-key-to-delete'));

        if (hasKeysToDelete) {
            $dom.fadeIn(massDeleteForm);
        } else {
            $dom.fadeOut(massDeleteForm);
        }
    }

    // TODO: #5235
    $cms.templates.blockMainMultiContentTiles = function blockMainMultiContentTiles(params, container) {
        $dom.on(container, 'mouseover', '.multi-content-tile', function (e, el) {
            if (document.documentElement.classList.contains('is-mouse-enabled')) {
                var detailsTilesBottom = $dom.$$$(el, '.multi-content-tile-details-bottom');
                detailsTilesBottom.forEach(function detailsTilesBottom(tileBottom) {
                    $dom.fadeIn(tileBottom);
                });
            }
        });
        $dom.on(container, 'mouseout', '.multi-content-tile', function (e, el) {
            if (document.documentElement.classList.contains('is-mouse-enabled')) {
                var detailsTilesBottom = $dom.$$$(el, '.multi-content-tile-details-bottom');
                detailsTilesBottom.forEach(function detailsTilesBottom(tileBottom) {
                    $dom.fadeOut(tileBottom);
                });
            }
        });
    }
}(window.$cms, window.$util, window.$dom));
