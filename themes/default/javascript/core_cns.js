(function ($cms, $util, $dom) {
    'use strict';

    var $coreCns = window.$coreCns = {};

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
                            $cms.ui.findUrlTab();
                        }, 0);
                    });
                };
            }
        });

        if (this.tabs.length > 1) {
            // we do not want it to scroll down
            var oldHash = window.location.hash;
            window.location.hash = '#';
            $cms.ui.findUrlTab(oldHash);
        }
    }

    $cms.templates.cnsGuestBar = function cnsGuestBar(params, container) {
        $dom.on(container, 'submit', '.js-submit-check-field-login-username', function (e, form) {
            if ($cms.form.checkFieldForBlankness(form.elements['username'])) {
                $cms.ui.disableFormButtons(form);
            } else {
                e.preventDefault();
            }
        });

        $dom.on(container, 'click', '.js-click-checkbox-remember-me-confirm', function (e, checkbox) {
            if (checkbox.checked) {
                $cms.ui.confirm('{!REMEMBER_ME_COOKIE;}', function (answer) {
                    if (!answer) {
                        checkbox.checked = false;
                    }
                });
            }
        });
    };

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

    $cms.templates.blockMainJoinDone = function blockMainJoinDone(params, container) {
        $dom.on(container, 'submit', '.js-submit-stats-event-track-dl-whitepaper', function (e, form) {
            e.preventDefault();
            $cms.statsEventTrack(null, '{!cns:DOWNLOAD_WHITEPAPER;}').then(function () {
                $dom.submit(form);
            });
        });
    };

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

    $cms.templates.cnsMemberDirectoryScreenFilter = function cnsMemberDirectoryScreenFilter(params, container) {
        $dom.on(container, 'keyup', '.js-keyup-input-filter-update-ajax-member-list', function (e, input) {
            $cms.form.updateAjaxMemberList(input, null, false, e);
        });
    };

    $cms.templates.cnsMemberProfileAbout = function cnsMemberProfileAbout(params, container) {
        $dom.on(container, 'click', '.js-click-member-profile-about-decrypt-data', function () {
            $coreCns.decryptData();
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
            if (form.elements['delete']) {
                form.elements['delete'].onchange = crf;
            }
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

    $cms.functions.moduleAdminCnsGroupsRunStart = function moduleAdminCnsGroupsRunStart() {
        var form = document.getElementById('main-form'),
            submitBtn = document.getElementById('submit-button'),
            validValue;

        form.addEventListener('submit', function submitCheck(e) {
            var value = form.elements['usergroup_name'].value;

            if (value === validValue) {
                return;
            }

            submitBtn.disabled = true;
            var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_usergroup&name=' + encodeURIComponent(value) + $cms.keep();
            e.preventDefault();
            $cms.form.doAjaxFieldTest(url).then(function (valid) {
                if (valid) {
                    validValue = value;
                    $dom.submit(form);
                } else {
                    submitBtn.disabled = false;
                }
            });
        });
    };

    $cms.functions.moduleAdminCnsEmoticons = function moduleAdminCnsEmoticons() {
        var form = document.getElementById('main-form'),
            submitBtn = document.getElementById('submit-button'),
            validValue;

        form.addEventListener('submit', function submitCheck(e) {
            var value = form.elements['code'].value;

            if (value === validValue) {
                return;
            }

            submitBtn.disabled = true;
            var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_emoticon&name=' + encodeURIComponent(value) + $cms.keep();
            e.preventDefault();
            $cms.form.doAjaxFieldTest(url).then(function (valid) {
                if (valid) {
                    validValue = value;
                    $dom.submit(form);
                } else {
                    submitBtn.disabled = false;
                }
            });
        });
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

    $cms.functions.hookProfilesTabsEditDeleteRenderTab = function hookProfilesTabsEditDeleteRenderTab() {
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
        var form = document.getElementById('main-form'),
            submitBtn = document.getElementById('account-submit-button'),
            validValue;

        form.addEventListener('submit', function submitCheck(e) {
            if (form.elements['edit_password'] == null) {
                return;
            }

            if ((form.elements['password_confirm']) && (form.elements['password_confirm'].value !== form.elements['edit_password'].value)) {
                submitBtn.disabled = false;
                $cms.ui.alert('{!PASSWORD_MISMATCH;^}');
                return false;
            }

            var value = form.elements['edit_password'].value;
            if ((value !== '') && (value !== validValue)) {
                e.preventDefault();
                var url = '{$FIND_SCRIPT_NOHTTP;^,username_check}' + $cms.keep(true);
                $cms.form.doAjaxFieldTest(url, 'password=' + encodeURIComponent(value)).then(function (valid) {
                    if (valid) {
                        validValue = value;
                        $dom.submit(form);
                    } else {
                        submitBtn.disabled = false;
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
            var checkBoxes = $('.js-chb-click-toggle-proceed-btn');
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


    $cms.templates.cnsViewGroupScreen = function cnsViewGroupScreen(params, container) {
        $dom.on(container, 'submit', '.js-form-submit-add-member-to-group', function (e, form) {
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

    $cms.templates.cnsEmoticonTable = function cnsEmoticonTable(params, container) {
        $dom.on(container, 'click', '.js-click-do-emoticon', function (e, target) {
            var fieldName = target.dataset.tpFieldName;
            if (fieldName) {
                window.$editing.doEmoticon(fieldName, target, true);
            }
        });
    };

    $cms.templates.joinForm = function (params, container) {
        var skippable = strVal(params.skippable);

        joinForm(params);

        $dom.on(container, 'click', '.js-click-btn-skip-step', function () {
            $dom.$('#' + skippable).value = '1';
        });

        $dom.on(container, 'submit', '.js-submit-modesecurity-workaround', function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled()) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
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
        try {
            scrollTo(0, 0);
        } catch (e) {}

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

        var token = document.createElement('input');
        token.type = 'hidden';
        token.name = 'csrf_token';
        token.id = 'csrf_token';
        token.value = $cms.getCsrfToken();
        form.appendChild(token);

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
        button.addEventListener('click', function () {
            document.body.removeChild(container);
            return false;
        });
        proceedDiv.appendChild(button);

        // Submit button
        /*{+START,SET,proceed_icon}{+START,INCLUDE,ICON}NAME=buttons/proceed{+END}{+END}*/
        button = document.createElement('button');
        button.type = 'submit';
        button.className = 'btn btn-primary btn-scri buttons--proceed';
        $dom.html(button, '{$GET;^,proceed_icon} {!encryption:DECRYPT;^}');
        // Hide the form upon submission
        button.addEventListener('click', function () {
            container.style.display = 'none';
        });
        proceedDiv.appendChild(button);

        form.appendChild(proceedDiv);

        document.body.appendChild(container);

        setTimeout(function () {
            try {
                input.focus();
            } catch (e) {}
        }, 0);
    };

    function joinForm(params) {
        var form = document.getElementById('username').form,
            submitBtn = document.getElementById('submit-button');

        form.elements['username'].onchange = function () {
            if (form.elements['intro_title']) {
                form.elements['intro_title'].value = $util.format('{!cns:INTRO_POST_DEFAULT;^}', [form.elements['username'].value]);
            }
        };

        var validValues;

        form.addEventListener('submit', function submitCheck(e) {
            if ((form.elements['confirm'] !== undefined) && (form.elements['confirm'].type === 'checkbox') && (!form.elements['confirm'].checked)) {
                $cms.ui.alert('{!cns:DESCRIPTION_I_AGREE_RULES;^}');
                return false;
            }

            if ((form.elements['email_address_confirm'] !== undefined) && (form.elements['email_address_confirm'].value !== form.elements['email'].value)) {
                $cms.ui.alert('{!EMAIL_ADDRESS_MISMATCH;^}');
                return false;
            }

            if ((form.elements['password_confirm'] !== undefined) && (form.elements['password_confirm'].value !== form.elements['password'].value)) {
                $cms.ui.alert('{!PASSWORD_MISMATCH;^}');
                return false;
            }

            var checkPromises = [], values = [];

            values.push(form.elements['username'].value);
            values.push(form.elements['password'].value);

            if (params.invitesEnabled) {
                values.push(form.elements['email'].value);
            }

            if (params.onePerEmailAddress) {
                values.push(form.elements['email'].value);
            }

            if (params.useCaptcha && ($cms.configOption('recaptcha_site_key') === '')) {
                values.push(form.elements['captcha'].value);
            }

            if ((validValues != null) && (validValues.length === values.length)) {
                var areSame = validValues.every(function (element, index) {
                    return element === values[index];
                });

                if (areSame) {
                    // All valid
                    return;
                }
            }

            var url = params.usernameCheckScript + '?username=' + encodeURIComponent(form.elements['username'].value) + $cms.keep();
            checkPromises.push($cms.form.doAjaxFieldTest(url, 'password=' + encodeURIComponent(form.elements['password'].value)));

            if (params.invitesEnabled) {
                url = params.snippetScript + '?snippet=invite_missing&name=' + encodeURIComponent(form.elements['email'].value) + $cms.keep();
                checkPromises.push($cms.form.doAjaxFieldTest(url));
            }

            if (params.onePerEmailAddress) {
                url = params.snippetScript + '?snippet=exists_email&name=' + encodeURIComponent(form.elements['email'].value) + $cms.keep();
                checkPromises.push($cms.form.doAjaxFieldTest(url));
            }

            if (params.useCaptcha && ($cms.configOption('recaptcha_site_key') === '')) {
                url = params.snippetScript + '?snippet=captcha_wrong&name=' + encodeURIComponent(form.elements['captcha'].value) + $cms.keep();
                checkPromises.push($cms.form.doAjaxFieldTest(url));
            }

            e.preventDefault();
            submitBtn.disabled = true;

            Promise.all(checkPromises).then(function (validities) {
                if (!validities.includes(false)) {
                    // All valid!
                    validValues = values;
                    $dom.submit(form);
                } else {
                    submitBtn.disabled = false;
                }
            });
        });
    }
}(window.$cms, window.$util, window.$dom));
