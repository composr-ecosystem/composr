(function ($cms, $util, $dom) {
    'use strict';

    var $coreCns = window.$coreCns = {};

    $cms.templates.cnsGuestBar = function cnsGuestBar(params, container) {
        $dom.on(container, 'click', '.js-check-field-login-username', function (e, btn) {
            var form = btn.form;

            if ($cms.form.checkFieldForBlankness(form.elements['username'])) {
                $cms.ui.disableFormButtons(form);
            } else {
                e.preventDefault();
            }
        });

        $dom.on(container, 'click', '.js-click-checkbox-remember-me-confirm', function (e, checkbox) {
            if (checkbox.checked) {
                $cms.ui.confirm('{!REMEMBER_ME_COOKIE;,{$SITE_NAME}}', function (answer) {
                    if (!answer) {
                        checkbox.checked = false;
                    }
                });
            }
        });
    };

    $cms.templates.cnsJoinStep1Screen = function cnsJoinStep1Screen(params, container) {
        var agreeCheckbox = container.querySelector('.js-chb-click-toggle-proceed-btn');

        if (agreeCheckbox) {
            document.getElementById('proceed-button').disabled = !agreeCheckbox.checked;
        }

        $dom.on(container, 'click', '.js-chb-click-toggle-proceed-btn', function (e, checkbox) {
            var checkBoxes = $dom.$$(checkbox.form, '.js-chb-click-toggle-proceed-btn');
            var allChecked = true;
            for (var i = 0; i < checkBoxes.length; i++) {
                if (!checkBoxes[i].checked) {
                    allChecked = false;
                }
            }
            document.getElementById('proceed-button').disabled = !allChecked;
        });

        $dom.on(container, 'click', '.js-click-set-top-location', function (e, target) {
            window.top.location = strVal(target.dataset.tpTopLocation);
        });
    };

    $cms.templates.cnsJoinReviewRulesScreen = function cnsJoinReviewRulesScreen(params, container) {
        var agreeCheckbox = container.querySelector('.js-chb-click-toggle-proceed-btn');

        if (agreeCheckbox) {
            document.getElementById('proceed-button').disabled = !agreeCheckbox.checked;
        }

        $dom.on(container, 'click', '.js-chb-click-toggle-proceed-btn', function (e, checkbox) {
            var checkBoxes = $dom.$$(checkbox.form, '.js-chb-click-toggle-proceed-btn');
            var allChecked = true;
            for (var i = 0; i < checkBoxes.length; i++) {
                if (!checkBoxes[i].checked) {
                    allChecked = false;
                }
            }
            document.getElementById('proceed-button').disabled = !allChecked;
        });

        $dom.on(container, 'click', '.js-click-set-top-location', function (e, target) {
            window.top.location = strVal(target.dataset.tpTopLocation);
        });
    };

    $cms.templates.joinForm = function (params) {
        var form = document.getElementById('username').form;

        form.elements['username'].onchange = function () {
            if (form.elements['intro_title']) {
                form.elements['intro_title'].value = $util.format('{!cns:INTRO_POST_DEFAULT;^}', [form.elements['username'].value]);
            }
        };

        var validValues = null;
        if (form.extraChecks === undefined) {
            form.extraChecks = [];
        }
        form.extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            if ((form.elements['confirm'] !== undefined) && (form.elements['confirm'].type === 'checkbox') && (!form.elements['confirm'].checked)) {
                $cms.ui.alert('{!cns:DESCRIPTION_I_AGREE_RULES;^}');
                alerted.valueOf = function () { return true; };
                firstFieldWithError = form.elements['confirm'];
                return false;
            }

            if ((form.elements['email_address_confirm'] !== undefined) && (form.elements['email'].value !== '') && (form.elements['email_address_confirm'].value !== form.elements['email'].value)) {
                $cms.ui.alert('{!EMAIL_ADDRESS_MISMATCH;^}');
                alerted.valueOf = function () { return true; };
                firstFieldWithError = form.elements['email_address_confirm'];
                return false;
            }

            if ((form.elements['password_confirm'] !== undefined) && (form.elements['password'].value !== '') && (form.elements['password_confirm'].value !== form.elements['password'].value)) {
                $cms.ui.alert('{!PASSWORD_MISMATCH;^}');
                alerted.valueOf = function () { return true; };
                firstFieldWithError = form.elements['password_confirm'];
                return false;
            }

            var values = [];

            values.push(form.elements['username'].value);
            values.push(form.elements['password'].value);

            if (params.invitesEnabled) {
                values.push(form.elements['email'].value);
            }

            if (params.onePerEmailAddress) {
                values.push(form.elements['email'].value);
            }

            var captchaValues = [],
                captchaElements = [],
                catchaValuesExpected = 0,
                questionCaptcha = false;
            if (params.useCaptcha && ($cms.configOption('recaptcha_site_key') === '')) {
                for (var i = 0; i < form.elements.length; i++) {
                    if ((form.elements[i].name !== undefined) && (form.elements[i].name.match(/^captcha(_|$)/))) {
                        if (form.elements[i].name.indexOf('_') !== -1) {
                            questionCaptcha = true;
                        }

                        captchaElements.push(form.elements[i]);
                        if (form.elements[i].value !== '') {
                            captchaValues.push(form.elements[i].value);
                        }
                        values.push(form.elements[i].value);
                        catchaValuesExpected++;
                    }
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
                var checkPromises = [],
                    url;

                validValues = [];

                if (form.elements['username'].value !== '') {
                    url = params.usernameCheckScript + '?username=' + encodeURIComponent(form.elements['username'].value) + $cms.keep();
                    var usernameCheckPromise = $cms.form.doAjaxFieldTest(url, 'password=' + encodeURIComponent(form.elements['password'].value)).then(function (valid) {
                        if (valid) {
                            validValues.push(form.elements['password'].value);
                        } else {
                            erroneous.valueOf = function () { return true; };
                            alerted.valueOf = function () { return true; };
                            firstFieldWithError = form.elements['username'];
                            validValues.push(null);
                        }
                    });
                    checkPromises.push(usernameCheckPromise);
                }

                if (form.elements['email'].value !== '') {
                    if (params.invitesEnabled) {
                        url = params.snippetScript + '?snippet=invite_missing&name=' + encodeURIComponent(form.elements['email'].value) + $cms.keep();
                        var invitePromise = $cms.form.doAjaxFieldTest(url).then(function (valid) {
                            if (valid) {
                                validValues.push(form.elements['email'].value);
                            } else {
                                erroneous.valueOf = function () { return true; };
                                alerted.valueOf = function () { return true; };
                                firstFieldWithError = form.elements['email'];
                                validValues.push(null);
                            }
                        });
                        checkPromises.push(invitePromise);
                    }

                    if (params.onePerEmailAddress) {
                        url = params.snippetScript + '?snippet=exists_email&name=' + encodeURIComponent(form.elements['email'].value) + $cms.keep();
                        var emailPromise = $cms.form.doAjaxFieldTest(url).then(function (valid) {
                            if (valid) {
                                validValues.push(form.elements['email'].value);
                            } else {
                                erroneous.valueOf = function () { return true; };
                                alerted.valueOf = function () { return true; };
                                firstFieldWithError = form.elements['email'];
                                validValues.push(null);
                            }
                        });
                        checkPromises.push(emailPromise);
                    }
                }

                if (params.useCaptcha && ($cms.configOption('recaptcha_site_key') === '') && (captchaValues.length === catchaValuesExpected)) {
                    url = params.snippetScript + '?snippet=captcha_wrong&name=' + encodeURIComponent(captchaValues.join('||'));
                    if (questionCaptcha) {
                        url += '&question_captcha=1';
                    }
                    url += $cms.keep();
                    var captchaPromise = $cms.form.doAjaxFieldTest(url).then(function (valid) {
                        if (valid) {
                            validValues = validValues.concat(captchaValues);
                        } else {
                            erroneous.valueOf = function () { return true; };
                            alerted.valueOf = function () { return true; };
                            firstFieldWithError = captchaElements[0];
                            for (var i = 0; i < captchaValues.length; i++) {
                                validValues.push(null);
                            }

                            $cms.functions.refreshCaptcha(document.getElementById('captcha-readable'), document.getElementById('captcha-audio'));
                        }

                        return valid;
                    });
                    checkPromises.push(captchaPromise);
                }

                return Promise.all(checkPromises);
            };
        });
    };

    $cms.templates.blockMainJoinDone = function blockMainJoinDone(params, container) {
        $dom.on(container, 'click', '.js-stats-event-track-dl-whitepaper', function (e, btn) {
            if ($dom.isCancelledSubmit(e)) {
                return;
            }

            e.preventDefault();
            $cms.statsEventTrack(null, '{!cns_components:DOWNLOAD_WHITEPAPER;}').then(function () {
                btn.form.submit();
            });
        });
    };

    $cms.views.CnsMemberProfileScreen = CnsMemberProfileScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function CnsMemberProfileScreen(params) {
        CnsMemberProfileScreen.base(this, 'constructor', arguments);

        this.memberId = strVal(params.memberId);
        this.tabs = arrVal(params.tabs);

        var self = this;
        this.tabs.forEach(function (tab) {
            var tabCode = strVal(tab.tabCode);

            if (tab.tabContent == null) {
                window['load_tab__' + tabCode] = function (automated) {
                    // Self destruct loader after this first run
                    window['load_tab__' + tabCode] = function () {};

                    if (automated) {
                        scrollTo(0, 0);
                    }

                    $cms.loadSnippet('profile_tab&tab=' + tabCode + '&member_id=' + self.memberId + window.location.search.replace('?', '&')).then(function (result) {
                        $dom.html('#g-' + tabCode, result);

                        // Give DOM some time to load, and protect against errors
                        window.setTimeout(function () {
                            // Then let subtab specified in URL get called up
                            $cms.ui.findUrlTab();
                        }, 25);
                    });
                };
            }
        });
    }

    $util.inherits(CnsMemberProfileScreen, $cms.View, /**@lends CnsMemberProfileScreen#*/ {
        events: function () {
            return {
                'click .js-click-select-tab-g': 'onClickSelectTab'
            };
        },

        onClickSelectTab: function (e, clicked) {
            var tab = clicked.dataset.vwTab;
            if (tab) {
                $cms.ui.selectTab('g', tab);
            }
        }
    });

    $cms.templates.cnsMemberProfileEdit = function cnsMemberProfileEdit(params, container) {
        $dom.on(container, 'click', '.js-click-select-edit-tab', function (e, clicked) {
            var tabSet = 'edit--',
                tabCode = $cms.filter.id(clicked.dataset.tpTabCode).toLowerCase();
            $util.inform('Select tab', tabSet + tabCode);
            if (tabCode) {
                $cms.ui.selectTab('g', tabSet + tabCode);
            }
        });
    };

    $cms.functions.hookProfilesTabsEditDeleteRenderTab = function hookProfilesTabsEditDeleteRenderTab() {
        if ($dom.$('.js-delete-photo')) {
            $dom.on('.js-delete-photo', 'click', function (event, btn) {
                btn.form.elements['delete_photo'].value = '1';
                btn.form.submit();
            });
        }
    };

    $cms.functions.hookProfilesTabsEditPhotoRenderTab = function hookProfilesTabsEditPhotoRenderTab() {
        var suffix = $cms.filter.id('{!DELETE;^}').toLowerCase();

        window['load_tab__edit__' + suffix] = function () {
            var submitButton = document.getElementById('account-submit-button'),
                deleteCheckbox = document.getElementById('delete'),
                tab = document.getElementById('t-edit--' + suffix);

            submitButton.disabled = !deleteCheckbox.checked;

            setInterval(function () {
                submitButton.disabled = !deleteCheckbox.checked && tab.classList.contains('tab-active');
            }, 100);
        };
    };

    $cms.functions.hookProfilesTabsEditSettingsRenderTab = function hookProfilesTabsEditSettingsRenderTab() {
        var extraChecks = [],
            validValue;
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            if (form.elements['edit_password'] == null) {
                return true;
            }

            if ((form.elements['password_confirm']) && (form.elements['password_confirm'].value !== form.elements['edit_password'].value)) {
                $cms.ui.alert('{!PASSWORD_MISMATCH;^}');
                alerted.valueOf = function () { return true; };
                firstFieldWithError = form.elements['password_confirm'];
                return false;
            }

            var value = form.elements['edit_password'].value;
            if ((value === validValue) || (value === '')) {
                return true;
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;^,username_check}' + $cms.keep(true);
                return $cms.form.doAjaxFieldTest(url, 'password=' + encodeURIComponent(value)).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    if (!valid) {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form.elements['edit_password'];
                    }
                });
            };
        });
        return extraChecks;
    };

    $cms.templates.cnsMemberProfileAbout = function cnsMemberProfileAbout(params, container) {
        $dom.on(container, 'click', '.js-click-member-profile-about-decrypt-data', function () {
            $coreCns.decryptData();
        });
    };

    $coreCns.decryptData = function decryptData() {
        if (document.getElementById('decryption_overlay')) {
            return;
        }

        var container = document.createElement('div');
        container.className = 'decryption-overlay box';
        container.id = 'decryption_overlay';
        container.style.position = 'absolute';
        container.style.width = '26em';
        container.style.padding = '0.5em';
        container.style.left = ($dom.getWindowWidth() / 2 - 200).toString() + 'px';
        container.style.top = ($dom.getWindowHeight() / 2 - 100).toString() + 'px';

        window.setTimeout(function() {
            try {
                scrollTo(0, 0);
            } catch (e) {}
        }, 25);

        var title = document.createElement('h2');
        title.appendChild(document.createTextNode('{!encryption:DECRYPT_TITLE;^}'));
        container.appendChild(title);

        var description = document.createElement('p');
        description.appendChild(document.createTextNode('{!encryption:DECRYPT_DESCRIPTION;^}'));
        container.appendChild(description);

        var form = document.createElement('form');
        form.action = window.location.href;
        form.method = 'post';
        container.appendChild(form);

        var label = document.createElement('label');
        label.for = 'decrypt';
        label.appendChild(document.createTextNode('{!encryption:DECRYPT_LABEL;^}'));
        form.appendChild(label);

        var space = document.createTextNode(' ');
        form.appendChild(space);

        var input = document.createElement('input');
        input.type = 'password';
        input.name = 'decrypt';
        input.id = 'decrypt';
        form.appendChild(input);

        var proceedDiv = document.createElement('div');
        proceedDiv.className = 'proceed-button';
        proceedDiv.style.marginTop = '1em';

        // Cancel button
        /*{+START,SET,icon_cancel}{+START,INCLUDE,ICON}NAME=buttons/cancel{+END}{+END}*/
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-primary btn-scri buttons--cancel';
        $dom.html(button, '{$GET;^,icon_cancel} {!INPUTSYSTEM_CANCEL;^}');
        // Remove the form when it's cancelled
        button.addEventListener('click', function (e) {
            document.body.removeChild(container);
            e.preventDefault();
        });
        proceedDiv.appendChild(button);

        // Submit button
        /*{+START,SET,proceed_icon}{+START,INCLUDE,ICON}NAME=buttons/proceed{+END}{+END}*/
        button = document.createElement('button');
        button.type = 'submit';
        button.disabled = true;
        button.className = 'btn btn-primary btn-scri buttons--proceed';
        $dom.html(button, '{$GET;^,proceed_icon} {!encryption:DECRYPT;^}');
        // Hide the form upon submission
        button.addEventListener('click', function () {
            container.style.display = 'none';
        });
        proceedDiv.appendChild(button);

        var token = document.createElement('input');
        token.type = 'hidden';
        token.name = 'csrf_token';
        token.id = 'csrf_token';
        $cms.getCsrfToken().then(function (text) {
            $util.log('Regenerated CSRF token');

            token.value = text;
            button.disabled = false;
        });
        form.appendChild(token);

        form.appendChild(proceedDiv);

        document.body.appendChild(container);

        setTimeout(function () {
            try {
                input.focus();
            } catch (e) {}
        }, 0);
    };

    $cms.templates.cnsMemberDirectoryScreenFilter = function cnsMemberDirectoryScreenFilter(params, container) {
        $dom.on(container, 'keyup', '.js-keyup-input-filter-update-ajax-member-list', function (e, input) {
            $cms.form.updateAjaxMemberList(input, null, false, e);
        });
    };

    $cms.functions.moduleAdminCnsGroups = function moduleAdminCnsGroups() {
        var form;

        if (document.getElementById('delete')) {
            form = document.getElementById('delete').form;
            var crf = function () {
                if (form.elements['new_usergroup']) {
                    form.elements['new_usergroup'].disabled = (form.elements['delete'] && !form.elements['delete'].checked);
                }
            };
            crf();
            form.elements['delete'].onclick = crf;
        }

        if (document.getElementById('is_presented_at_install')) {
            form = document.getElementById('is_presented_at_install').form;
            var crf2 = function () {
                if (form.elements['is_default']) {
                    form.elements['is_default'].disabled = (form.elements['is_presented_at_install'].checked);
                }
                if (form.elements['is_presented_at_install'].checked) {
                    form.elements['is_default'].checked = false;
                }
            };
            crf2();
            form.elements['is_presented_at_install'].onchange = crf2;
            var crf3 = function () {
                if (form.elements['absorb']) {
                    form.elements['absorb'].disabled = (form.elements['is_private_club'] && form.elements['is_private_club'].checked);
                }
            };
            crf3();
            if (form.elements['is_private_club']) {
                form.elements['is_private_club'].onchange = crf3;
            }
        }
    };

    $cms.functions.moduleAdminCnsCustomProfileFields = function moduleAdminCnsCustomProfileFields() {
        var form;

        if (document.getElementById('encrypted')) {
            form = document.getElementById('type').form;
            var crf = function () {
                var type = form.elements['type'].value;
                var encryptable = (type.indexOf('_text') !== -1) || (type.indexOf('_trans') !== -1) || (type.indexOf('posting') !== -1); // See also cpf_decrypt.php and cpf_encrypt.php
                form.elements['encrypted'].disabled = !encryptable;
                if (!encryptable) {
                    form.elements['encrypted'].checked = false;
                }
            };
            form.elements['type'].onchange = crf;
            crf();
        }
    };

    $cms.templates.cnsViewGroupScreen = function cnsViewGroupScreen(params, container) {
        $dom.on(container, 'click', '.js-add-member-to-group', function (e, btn) {
            var form = btn.form;

            if ($cms.form.checkFieldForBlankness(form.elements.username)) {
                $cms.ui.disableFormButtons(form);
            } else {
                e.preventDefault();
            }
        });

        $dom.on(container, 'keyup', '.js-input-add-member-username', function (e, input) {
            $cms.form.updateAjaxMemberList(input, null, false, e);
        });
    };

    $cms.functions.moduleAdminCnsGroupsRunStart = function moduleAdminCnsGroupsRunStart() {
        var extraChecks = [],
            validValue;
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var value = form.elements['usergroup_name'].value;

            if ((value === validValue) || (value === '')) {
                return true;
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_usergroup&name=' + encodeURIComponent(value) + $cms.keep();
                return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    if (!valid) {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form.elements['usergroup_name'];
                    }
                });
            };
        });
        return extraChecks;
    };

    $cms.functions.moduleAdminCnsEmoticons = function moduleAdminCnsEmoticons() {
        var extraChecks = [],
            validValue;
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var value = form.elements['code'].value;

            if ((value === validValue) || (value === '')) {
                return true;
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_emoticon&name=' + encodeURIComponent(value) + $cms.keep();
                return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    if (!valid) {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form.elements['code'];
                    }
                });
            };
        });
        return extraChecks;
    };

    $cms.functions.adminCnsMembersDownloadCsv = function adminCnsMembersDownloadCsv() {
        var form = $dom.$('#filename').form;
        crf();
        for (var i = 0; i < form.elements['preset'].length; i++) {
            $dom.on(form.elements['preset'][i], 'click', crf);
        }

        function crf() {
            var preset = $cms.form.radioValue(form.elements['preset']);
            if (preset === '') {
                form.elements['fields_to_use'].disabled = false;
                form.elements['order_by'].disabled = false;
                form.elements['usergroups'].disabled = false;
                form.elements['filename'].value = form.elements['filename'].defaultValue;
            } else {
                form.elements['fields_to_use'].disabled = true;
                form.elements['order_by'].disabled = true;
                form.elements['usergroups'].disabled = true;
                form.elements['filename'].value = form.elements['filename'].defaultValue.replace(/^{$LCASE,{!MEMBERS;}}-/, preset + '-');
            }
        }
    };

    $cms.templates.cnsEmoticonTable = function cnsEmoticonTable(params, container) {
        $dom.on(container, 'click', '.js-click-do-emoticon', function (e, target) {
            var fieldName = target.dataset.tpFieldName;
            if (fieldName) {
                window.$editing.doEmoticon(fieldName, target, true);
            }
        });
    };
}(window.$cms, window.$util, window.$dom));
