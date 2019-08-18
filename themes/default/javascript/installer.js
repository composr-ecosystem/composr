(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.installerStepLog = function installerStepLog() {
        /* Code to auto-submit the form after 5 seconds, but only if there were no errors */
        if (document.querySelector('.installer-warning')) {
            return;
        }

        var button = document.getElementById('proceed-button'),
            timer;

        button.countdown = 6;

        continueFunc();
        timer = window.setInterval(continueFunc, 1000);
        button.addEventListener('mouseover', function () {
            if (timer) {
                window.clearInterval(timer);
                timer = null;
            }
        });
        window.addEventListener('unload', function () {
            if (timer) {
                window.clearInterval(timer);
                timer = null;
            }
        });
        button.addEventListener('mouseout', function () {
            timer = window.setInterval(continueFunc, 1000);
        });

        function continueFunc() {
            var labelEl = button.querySelector('.js-button-label');
            labelEl.textContent = '{!PROCEED;^} ({!AUTO_IN;^} ' + button.countdown + ')';
            if (button.countdown === 0) {
                if (timer) {
                    window.clearInterval(timer);
                    timer = null;
                }
                $dom.submit(button.form);
            } else {
                button.countdown--;
            }
        }
    };

    $cms.templates.installerHtmlWrap = function installerHtmlWrap(params) {
        var defaultForum = strVal(params.defaultForum);

        var none = document.getElementById(defaultForum);
        if (none) {
            none.checked = true;
        }

        if ((defaultForum !== 'none') && (defaultForum !== 'cns')) {
            var d = document.getElementById('forum-path');
            if (d) {
                d.style.display = 'block';
            }
        }

        var form = document.querySelector('form');
        if (form != null) {
            form.title = '';
        }

        var cns = document.getElementById('cns');
        if (cns) {
            useMultiDbLocker();

            for (var i=0; i < form.elements['forum'].length; i++) {
                form.elements['forum'][i].onclick = useMultiDbLocker;
            }
        }

        function useMultiDbLocker() {
            form.elements['use_multi_db'][0].disabled = cns.checked;
            form.elements['use_multi_db'][1].disabled = cns.checked;
            if (cns.checked) {
                form.elements['use_multi_db'][1].checked = true;
            }
        }
    };

    function toggleInstallerSection(id) {
        // Try and grab our item
        var itm = document.getElementById(id),
            img = document.getElementById('img-' + id);

        if (itm.style.display === 'none') {
            itm.style.display = 'block';
            if (img) {
                img.src = $util.rel('install.php?type=contract');
                img.alt = img.alt.replace('{!EXPAND;}', '{!CONTRACT;}');
                img.title = img.title.replace('{!EXPAND;}', '{!CONTRACT;}');
            }
        } else {
            itm.style.display = 'none';
            if (img) {
                img.src = $util.rel('install.php?type=expand');
                img.alt = img.alt.replace('{!CONTRACT;}', '{!EXPAND;}');
                img.title = img.title.replace('{!CONTRACT;}', '{!EXPAND;}');
            }
        }
    }

    $cms.templates.installerStep3 = function installerStep3(params, container) {
        $dom.on(container, 'click', '.js-click-toggle-advanced-db-setup-section', function (e, clicked) {
            var id = strVal(clicked.dataset.tpSection);
            toggleInstallerSection(id);
        });
    };

    $cms.templates.installerForumChoice = function installerForumChoice(params, container) {
        var versions = strVal(params.versions);

        $dom.on(container, 'click', '.js-click-do-forum-choose', function (e, clicked) {
            doForumChoose(clicked, versions);
        });

        function doForumChoose(el, versions) {
            $dom.html('#versions', versions);

            var show = false;
            if ((el.id !== 'none') && (el.id !== 'cns')) {
                show = true;
                var label = $dom.$('#sep-forum');
                if (label) {
                    $dom.html(label, el.nextElementSibling.textContent);
                }
            }

            $dom.toggle('#forum-database-info', show);
            if ($dom.$('#forum-path')) {
                $dom.toggle('#forum-path', show);
            }
        }
    };

    $cms.templates.installerInputLine = function installerInputLine(params, input) {
        $dom.on(input, 'change', function () {
            input.changed = true;
        });
    };

    $cms.templates.installerStep4 = function installerStep4(params) {
        var passwordPrompt = strVal(params.passwordPrompt),
            domain = document.getElementById('domain');

        if (domain) {
            domain.addEventListener('change', function () {
                var cs = document.getElementById('Cookie_space_settings');
                if (cs && (cs.style.display === 'none')) {
                    toggleInstallerSection('Cookie_space_settings');
                }
                var cd = document.getElementById('cookie_domain');
                if (cd && (cd.value !== '')) {
                    cd.value = '.' + domain.value;
                }
            });
        }

        var gaeApp = document.getElementById('gae_application');

        if (gaeApp) {
            gaeOnChange();
            gaeApp.addEventListener('change', gaeOnChange);
        }

        function gaeOnChange() {
            var gaeLiveDbSite = document.getElementById('gae_live_db_site'),
                gaeLiveDbSiteHost = document.getElementById('gae_live_db_site_host'),
                gaeBucketName = document.getElementById('gae_bucket_name');

            gaeLiveDbSite.value = gaeLiveDbSite.value.replace(/(<application>|composr)/g, gaeApp.value);
            gaeLiveDbSiteHost.value = gaeLiveDbSiteHost.value.replace(/(<application>|composr)/g, gaeApp.value);
            gaeBucketName.value = gaeBucketName.value.replace(/(<application>|composr)/g, gaeApp.value);
        }

        var step4Form = document.getElementById('form-installer-step-4');

        if (step4Form) {
            step4Form.addEventListener('submit', validateSettings);
        }

        function validateSettings(e) {
            e.preventDefault();

            if ((step4Form.elements['forum_base_url']) && (step4Form.elements['forum_base_url'].type !== 'hidden') && (step4Form.elements['forum_base_url'].value === step4Form.elements['base_url'].value)) {
                window.alert('{!FORUM_BASE_URL_INVALID;/}');
                return;
            }

            if ((step4Form.elements['forum_base_url']) && (step4Form.elements['forum_base_url'].type !== 'hidden') && (step4Form.elements['forum_base_url'].value.substr(-7) === '/forums') && (!step4Form.elements['forum_base_url'].changed)) {
                if (!window.confirm('{!FORUM_BASE_URL_UNCHANGED;/}')) {
                    return;
                }
            }

            for (var i = 0; i < step4Form.elements.length; i++) {
                if ((step4Form.elements[i].className.indexOf('-required') !== -1) && (step4Form.elements[i].value === '')) {
                    window.alert('{!IMPROPERLY_FILLED_IN;^}');
                    return;
                }
            }

            if (!checkPasswords(step4Form)) {
                return;
            }

            var checkPromises = [], post;

            if ((step4Form.elements['db_site_password'])) {
                var sitePwdCheckUrl = 'install.php?type=ajax_db_details';
                post = 'db_type=' + encodeURIComponent(step4Form.elements['db_type'].value) + '&db_site_host=' + encodeURIComponent(step4Form.elements['db_site_host'].value) + '&db_site=' + encodeURIComponent(step4Form.elements['db_site'].value) + '&db_site_user=' + encodeURIComponent(step4Form.elements['db_site_user'].value) + '&db_site_password=' + encodeURIComponent(step4Form.elements['db_site_password'].value);
                checkPromises.push($cms.form.doAjaxFieldTest(sitePwdCheckUrl, post));
            }

            if (step4Form.elements['db_forums_password']) {
                var forumsPwdCheckUrl = 'install.php?type=ajax_db_details';
                post = 'db_type=' + encodeURIComponent(step4Form.elements['db_type'].value) + '&db_forums_host=' + encodeURIComponent(step4Form.elements['db_forums_host'].value) + '&db_forums=' + encodeURIComponent(step4Form.elements['db_forums'].value) + '&db_forums_user=' + encodeURIComponent(step4Form.elements['db_forums_user'].value) + '&db_forums_password=' + encodeURIComponent(step4Form.elements['db_forums_password'].value);
                checkPromises.push($cms.form.doAjaxFieldTest(forumsPwdCheckUrl, post));
            }

            if (step4Form.elements['ftp_domain']) {
                var ftpDomainCheckUrl = 'install.php?type=ajax_ftp_details';
                post = 'ftp_domain=' + encodeURIComponent(step4Form.elements['ftp_domain'].value) + '&ftp_folder=' + encodeURIComponent(step4Form.elements['ftp_folder'].value) + '&ftp_username=' + encodeURIComponent(step4Form.elements['ftp_username'].value) + '&ftp_password=' + encodeURIComponent(step4Form.elements['ftp_password'].value);
                checkPromises.push($cms.form.doAjaxFieldTest(ftpDomainCheckUrl, post));
            }

            Promise.all(checkPromises).then(function (validities) {
                if (!validities.includes(false)) {
                    // All valid!
                    step4Form.submit();
                }
            });
        }

        /**
         * NOTE: This function also has a copy in themes/default/templates/PASSWORD_CHECK_JS.tpl so update that as well when modifying here.
         * @param form
         * @return {boolean}
         */
        function checkPasswords(form) {
            if (form.confirm) {
                return true;
            }

            if (form.elements['cns_admin_password_confirm'] != null) {
                if (!checkPassword(form, 'cns_admin_password', '{!ADMIN_USERS_PASSWORD;^}')) {
                    return false;
                }
            }

            if (form.elements['master_password_confirm'] != null) {
                if (!checkPassword(form, 'master_password', '{!MASTER_PASSWORD;^}')) {
                    return false;
                }
            }

            if (passwordPrompt !== '') {
                window.alert(passwordPrompt);
            }

            return true;

            function checkPassword(form, fieldName, fieldLabel) {
                // Check matches with confirm field
                if (form.elements[fieldName + '_confirm'].value !== form.elements[fieldName].value) {
                    window.alert($util.format('{!PASSWORDS_DO_NOT_MATCH;^/}', [fieldLabel]));
                    return false;
                }

                // Check does not match database password
                if (form.elements['db_site_password'] != null) {
                    if ((form.elements[fieldName].value !== '') && (form.elements[fieldName].value === form.elements['db_site_password'].value)) {
                        window.alert($util.format('{!PASSWORDS_DO_NOT_REUSE;^/}', [fieldLabel]));
                        return false;
                    }
                }

                // Check password is secure
                var isSecurePassword = true;
                if (form.elements[fieldName].value.length < 8) {
                    isSecurePassword = false;
                }
                if (!form.elements[fieldName].value.match(/[a-z]/)) {
                    isSecurePassword = false;
                }
                if (!form.elements[fieldName].value.match(/[A-Z]/)) {
                    isSecurePassword = false;
                }
                if (!form.elements[fieldName].value.match(/\d/)) {
                    isSecurePassword = false;
                }
                if (!form.elements[fieldName].value.match(/[^a-zA-Z\d]/)) {
                    isSecurePassword = false;
                }

                if (!isSecurePassword) {
                    return window.confirm($util.format('{!PASSWORD_INSECURE;^}', [fieldLabel])) && window.confirm($util.format('{!CONFIRM_REALLY;^} {!PASSWORD_INSECURE;^}', [fieldLabel]));
                }

                return true;
            }
        }
    };

    $cms.templates.installerStep4SectionHide = function installerStep4SectionHide(params, container) {
        var title = strVal(params.title);

        $dom.on(container, 'click', '.js-click-toggle-title-section', function () {
            toggleInstallerSection($cms.filter.id($cms.filter.nl(title)));
        });
    };
}(window.$cms, window.$util, window.$dom));
