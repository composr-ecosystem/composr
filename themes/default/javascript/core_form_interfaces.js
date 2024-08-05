/* See also checking.js, which is not always loaded at the same time as this file */

(function ($cms, $util, $dom) {
    'use strict';

    var $coreFormInterfaces = window.$coreFormInterfaces = {};

    // Templates:
    // POSTING_FORM.tpl
    // - POSTING_FIELD.tpl
    $cms.views.PostingForm = PostingForm;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function PostingForm() {
        PostingForm.base(this, 'constructor', arguments);
    }

    $util.inherits(PostingForm, $cms.View, {
        events: function () {
            return {
                'click .js-click-pf-toggle-subord-fields': 'toggleSubordFields',
                'keypress .js-click-pf-toggle-subord-fields': 'toggleSubordFields'
            };
        },

        toggleSubordFields: function (e, target) {
            toggleSubordinateFields(target, 'fes-attachments-help--' + target.id.replace(/^fes-attachments--/, 'fes-attachments-help--'));
        }
    });

    $cms.views.SubmissionFlow = SubmissionFlow;
    /**
     * @memberof $cms.views
     * @class SubmissionFlow
     * @extends $cms.View
     */
    function SubmissionFlow(params) {
        SubmissionFlow.base(this, 'constructor', arguments);

        this.backUrl = strVal(params.backUrl);
        this.cancelUrl = strVal(params.cancelUrl);
        this.analyticEventCategory = params.analyticEventCategory;
        this.form = this.el;
        this.btnSubmit = this.$('.js-btn-main-submit-form');

        if (this.form.extraChecks === undefined) {
            this.form.extraChecks = [];
        }

        window.formPreviewUrl = strVal(params.previewUrl);
        window.separatePreview = Boolean(params.separatePreview);
        window.analyticEventCategory = params.analyticEventCategory;

        var self = this;

        if (params.forcePreviews) {
            $dom.hide(this.btnSubmit);
        }

        if (params.jsFunctionCalls != null) {
            var result = $cms.executeJsFunctionCalls(params.jsFunctionCalls);
            if (Array.isArray(result)) {
                this.form.extraChecks = this.form.extraChecks.concat(result);
            }
        }

        if (!params.secondaryForm) {
            $dom.on(this.form, 'keyup', 'input', function (e) {
                self.fixFormEnterKey(e);
            });
        }

        if (params.supportAutosave && params.formName) {
            setTimeout(function () {
                // eslint-disable-next-line no-constant-condition
                if ('{$VALUE_OPTION;,disable_form_auto_saving}' !== '1') {
                    window.$posting.initFormSaving(params.formName);
                }
            }, 3000/*Let CKEditor load*/);
        }
    }

    $util.inherits(SubmissionFlow, $cms.View, /**@lends SubmissionFlow#*/{
        events: function () {
            return {
                'click .js-click-do-form-cancel': 'doFormCancel',
                'click .js-click-btn-go-back': 'goBack',

                // These are connected
                'submit': 'cancelNativeFormSubmit',
                'click .js-click-do-form-preview': 'doStandardFormPreview',
                'click .js-btn-main-submit-form': 'doCMSFormSubmitChain',
            };
        },

        doFormCancel: function () {
            var that = this;
            $cms.ui.confirm(
                '{!Q_SURE;*}',
                function (result) {
                    if (result) {
                        window.location = that.cancelUrl;
                    }
                }
            );
        },

        cancelNativeFormSubmit: function (e) {
            e.preventDefault(); // Stops native form submit, so that form submit event handlers can be called while doCMSFormSubmitChain remains in control of actual submission after all promises are met
        },

        doStandardFormPreview: function (e) {
            e.preventDefault();

            if (this.form.extraChecks === undefined) {
                this.form.extraChecks = [];
            }
            $cms.form.doFormPreview(e, this.form, window.formPreviewUrl, window.separatePreview, this.form.extraChecks);
        },

        doCMSFormSubmitChain: function (e) {
            e.preventDefault();

            if (this.form.extraChecks === undefined) {
                this.form.extraChecks = [];
            }
            $cms.form.doCheckingCMSFormSubmitChain(e, this.form, this.analyticEventCategory, this.form.extraChecks);
        },

        goBack: function (e, btn) {
            if (btn.form.method.toLowerCase() === 'get') {
                window.location = this.backUrl;
            } else {
                btn.form.action = this.backUrl;
                btn.form.submit();
            }
        },

        fixFormEnterKey: function (e) {
            if (!this.btnSubmit) {
                return;
            }

            var types = ['text', 'password', 'color', 'email', 'number', 'range', 'search', 'tel', 'url'];

            if (!types.includes(e.target.type)) {
                return;
            }

            if (e.key === 'Enter') {
                $dom.trigger(this.btnSubmit, 'click');
            }
        }
    });

    $cms.views.FromScreenInputUpload = FromScreenInputUpload;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function FromScreenInputUpload(params) {
        FromScreenInputUpload.base(this, 'constructor', arguments);

        if (params.plupload && !$cms.isHttpauthLogin() && $cms.configOption('complex_uploader')) {
            $cms.requireJavascript('plupload').then(function () {
                window.$plupload.preinitFileInput('upload', params.name, null, params.filter);
            });
        }

        if (params.syndicationJson != null) {
            $cms.requireJavascript('editing').then(function () {
                window.$editing.showUploadSyndicationOptions(params.name, params.syndicationJson);
            });
        }
    }

    $util.inherits(FromScreenInputUpload, $cms.View);

    function setAccessPresetsSelectedOption(prefix) {
        var list = document.getElementById(prefix + '_presets');
        // Test to see what we wouldn't have to make a change to get - and that is what we're set at
        if (!window.$corePermissionManagement.copyPermissionPresets(prefix, '0', true)) {
            list.selectedIndex = list.options.length - 4;
        } else if (!window.$corePermissionManagement.copyPermissionPresets(prefix, '1', true)) {
            list.selectedIndex = list.options.length - 3;
        } else if (!window.$corePermissionManagement.copyPermissionPresets(prefix, '2', true)) {
            list.selectedIndex = list.options.length - 2;
        } else if (!window.$corePermissionManagement.copyPermissionPresets(prefix, '3', true)) {
            list.selectedIndex = list.options.length - 1;
        }
    }

    $cms.views.FormScreenInputPermission = FormScreenInputPermission;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function FormScreenInputPermission(params) {
        FormScreenInputPermission.base(this, 'constructor', arguments);

        this.groupId = params.groupId;
        this.prefix = 'access_' + this.groupId;
        var prefix = this.prefix;

        if (!params.allGlobal) {
            setAccessPresetsSelectedOption(prefix);
        }
    }

    $util.inherits(FormScreenInputPermission, $cms.View, {
        events: function () {
            return {
                'click .js-click-copy-perm-presets': 'copyPresets',
                'change .js-change-copy-perm-presets': 'copyPresets',
                'click .js-click-perm-repeating': 'permissionRepeating'
            };
        },

        copyPresets: function (e, select) {
            window.$corePermissionManagement.copyPermissionPresets(this.prefix, select.value);
            window.$corePermissionManagement.cleanupPermissionList(this.prefix);
        },

        permissionRepeating: function (e, button) {
            var name = this.prefix,
                oldPermissionCopying = window.permissionCopying,
                tr = button.parentNode.parentNode,
                trs = tr.parentNode.getElementsByTagName('tr');

            if (window.permissionCopying) { // Undo current copying
                var priorButton = document.getElementById('copy_button_' + window.permissionCopying);
                priorButton.classList.remove('active_repeating');
                priorButton.style.textDecoration = 'none';
                window.permissionCopying = null;
            }

            if (oldPermissionCopying !== name) { // Starting a new copying session
                button.classList.add('active_repeating');
                window.permissionCopying = name;
                $cms.ui.alert('{!permissions:REPEAT_PERMISSION_NOTICE;^}');
                for (var j = 0; j < trs.length; j++) {
                    if (trs[j] !== tr) {
                        $dom.on(trs[j], 'click', copyPermissionsFunction(trs[j], tr));
                    }
                }
            }

            function copyPermissionsFunction(toRow, fromRow) {
                return function () {
                    var inputsTo = toRow.getElementsByTagName('input');
                    var inputsFrom = fromRow.getElementsByTagName('input');
                    for (var i = 0; i < inputsTo.length; i++) {
                        inputsTo[i].checked = inputsFrom[i].checked;
                    }
                    var selectsTo = toRow.getElementsByTagName('select');
                    var selectsFrom = fromRow.getElementsByTagName('select');
                    for (var x = 0; x < selectsTo.length; x++) {
                        while (selectsTo[x].options.length > 0) {
                            selectsTo[x].remove(0);
                        }
                        for (var j = 0; j < selectsFrom[x].options.length; j++) {
                            selectsTo[x].add(selectsFrom[x].options[j].cloneNode(true), null);
                        }
                        selectsTo[x].selectedIndex = selectsFrom[x].selectedIndex;
                        selectsTo[x].disabled = selectsFrom[x].disabled;
                    }
                };
            }
        }
    });

    $cms.views.FormScreenInputPermissionOverride = FormScreenInputPermissionOverride;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function FormScreenInputPermissionOverride(params) {
        FormScreenInputPermissionOverride.base(this, 'constructor', arguments);

        var prefix = 'access_' + params.groupId,
            defaultAccess = intVal(params.defaultAccess);

        this.groupId = params.groupId;
        this.prefix = prefix;

        window.$corePermissionManagement.setupPrivilegeOverrideSelector(prefix, defaultAccess, params.privilege, params.title, Boolean(params.allGlobal));

        if (!params.allGlobal) {
            setAccessPresetsSelectedOption(prefix);
        }
    }

    $util.inherits(FormScreenInputPermissionOverride, $cms.View, /**@lends FormScreenInputPermissionOverride#*/{
        events: function () {
            return {
                'click .js-click-perms-overridden': 'permissionsOverridden',
                'change .js-change-perms-overridden': 'permissionsOverridden',
                'mouseover .js-mouseover-show-perm-setting': 'showPermSetting'
            };
        },

        permissionsOverridden: function () {
            permissionsOverridden(this.prefix);
        },

        showPermSetting: function (e, select) {
            if (select.value === '-1') {
                window.$corePermissionManagement.showPermissionSetting(select);
            }
        }
    });

    $cms.templates.formScreenFieldInput = function formScreenFieldInput(params) {
        var el = $dom.$('#form-table-field-input--' + strVal(params.randomisedId));
        if (el) {
            $cms.form.setUpChangeMonitor(el.parentElement);
        }
    };

    $cms.templates.formScreenInputPassword = function (params, container) {
        var value = strVal(params.value),
            name = strVal(params.name);

        if ((value === '') && (name === 'edit_password')) {
            // LEGACY Work around annoying Firefox bug. It ignores autocomplete="off" if a password was already saved somehow
            setTimeout(function () {
                $dom.$('#' + name).value = '';
            }, 300);
        }

        $dom.on(container, 'mouseover', '.js-mouseover-activate-password-strength-tooltip', function (e, el) {
            if (el.parentNode.title !== undefined) {
                el.parentNode.title = '';
            }
            $cms.ui.activateTooltip(el, e, '{!PASSWORD_STRENGTH;^}', 'auto');
        });

        $dom.on(container, 'change', '.js-input-change-check-password-strength', function (e, input) {
            if (input.name.includes('2') || input.name.includes('confirm')) {
                return;
            }

            var _ind = $dom.$('#password-strength-' + input.id);
            if (!_ind) {
                return;
            }
            var ind = _ind.querySelector('span');
            var post = 'password=' + encodeURIComponent(input.value);
            if (input.form) {
                if (input.form.elements.username !== undefined) {
                    post += '&username=' + input.form.elements['username'].value;
                } else {
                    if (input.form.elements.edit_username !== undefined) {
                        post += '&username=' + input.form.elements['edit_username'].value;
                    }
                }
                if (input.form.elements.email_address !== undefined) {
                    post += '&email_address=' + input.form.elements['email_address'].value;
                }
                if (input.form.elements.birthday_year !== undefined) {
                    post += '&birthday_year=' + input.form.elements['birthday_year'].value;
                    post += '&birthday_month=' + input.form.elements['birthday_month'].value;
                    post += '&birthday_day=' + input.form.elements['birthday_day'].value;
                }
            }

            $cms.loadSnippet('password_strength', post, true).then(function (resp) {
                var data = JSON.parse(resp);

                var strength = Number(data.strength);
                var minStrength = Number(data.minStrength);

                ind.style.display = 'block';
                ind.style.width = (strength * 10) + 'px';
                if (strength >= minStrength) {
                    ind.style.backgroundColor = 'green';
                } else {
                    ind.style.backgroundColor = 'red';
                }

                ind.parentNode.style.display = (input.value.length === 0) ? 'none' : 'block';
            });
        });
    };

    $cms.templates.formScreenInputLine = function formScreenInputLine(params) {
        $cms.requireJavascript(['jquery', 'jquery_autocomplete']).then(function () {
            window.$jqueryAutocomplete.setUpComcodeAutocomplete(params.name, Boolean(params.wysiwyg));
        });
    };

    $cms.templates.formScreenInputCombo = function formScreenInputCombo(params, container) {
        var name = strVal(params.name),
            comboInput = $dom.$('#' + name),
            fallbackList = $dom.$('#' + name + '-fallback-list');

        if (window.HTMLDataListElement === undefined) {
            comboInput.classList.remove('input-line-required');
            comboInput.classList.add('input-line');
        }

        if (fallbackList) {
            fallbackList.disabled = (comboInput.value !== '');

            $dom.on(container, 'keyup', '.js-keyup-toggle-fallback-list', function () {
                fallbackList.disabled = (comboInput.value !== '');
            });
        }
    };

    $cms.templates.formScreenInputLineMulti = function (params, container) {
        $dom.on(container, 'keypress', '.js-keypress-ensure-next-field', function (e, input) {
            $coreFormInterfaces.ensureNextField2(e, input);
        });
    };

    $cms.templates.formScreenInputTextMulti = function formScreenInputTextMulti(params, container) {
        $dom.on(container, 'keypress', '.js-keypress-textarea-ensure-next-field', function (e, textarea) {
            if (!$dom.keyPressed(e, 'Tab')) {
                ensureNextField(textarea);
            }
        });
    };

    $cms.templates.formScreenInputUploadMulti = function formScreenInputUploadMulti(params, container) {
        var nameStub = strVal(params.nameStub),
            index = strVal(params.i);

        if (params.syndicationJson != null) {
            $cms.requireJavascript('editing').then(function () {
                window.$editing.showUploadSyndicationOptions(nameStub, params.syndicationJson);
            });
        }

        if (params.plupload && !$cms.isHttpauthLogin() && $cms.configOption('complex_uploader')) {
            window.$plupload.preinitFileInput('upload_multi', nameStub + '_' + index, null, params.filter);
        }

        $dom.on(container, 'change', '.js-input-change-ensure-next-field-upload', function (e, input) {
            if (!$dom.keyPressed(e, 'Tab')) {
                ensureNextFieldUpload(input);
            }
        });

        $dom.on(container, 'click', '.js-click-clear-name-stub-input', function () {
            var input = $dom.$('#' + nameStub + '_' + index);
            $dom.changeValue(input, '');
        });


        function ensureNextFieldUpload(thisField) {
            var mid = thisField.name.lastIndexOf('_'),
                nameStub = thisField.name.substring(0, mid + 1),
                thisNum = thisField.name.substring(mid + 1, thisField.name.length) - 0,
                nextNum = thisNum + 1,
                nextField = document.getElementById('multi_' + nextNum),
                thisId = thisField.id;

            if (!nextField) {
                nextNum = thisNum + 1;
                thisField = document.getElementById(thisId);
                nextField = document.createElement('input');
                nextField.className = 'input-upload';
                nextField.id = 'multi_' + nextNum;
                nextField.addEventListener('change', function (event) {
                    if (!$dom.keyPressed(event, 'Tab')) {
                        ensureNextFieldUpload(this);
                    }
                });
                nextField.type = 'file';
                nextField.name = nameStub + nextNum;
                thisField.parentNode.appendChild(nextField);
            }
        }
    };

    $cms.templates.formScreenInputUsernameMulti = function formScreenInputUsernameMulti(params, container) {
        $dom.on(container, 'focus', '.js-focus-update-ajax-member-list', function (e, input) {
            if (input.value === '') {
                $cms.form.updateAjaxMemberList(input, null, true, e);
            }
        });

        $dom.on(container, 'keyup', '.js-keyup-update-ajax-member-list', function (e, input) {
            $cms.form.updateAjaxMemberList(input, null, false, e);
        });

        $dom.on(container, 'change', '.js-change-ensure-next-field', function (e, input) {
            ensureNextField(input);
        });

        $dom.on(container, 'keypress', '.js-keypress-ensure-next-field', function (e, input) {
            ensureNextField(input);
        });
    };

    $cms.templates.formScreenInputUsername = function formScreenInputUsername(params, container) {
        $dom.on(container, 'focus', '.js-focus-update-ajax-member-list', function (e, input) {
            if (input.value === '') {
                $cms.form.updateAjaxMemberList(input, null, true, e);
            }
        });

        $dom.on(container, 'keyup', '.js-keyup-update-ajax-member-list', function (e, input) {
            $cms.form.updateAjaxMemberList(input, null, false, e);
        });
    };

    $cms.templates.formScreenInputHugeComcode = function formScreenInputHugeComcode(params) {
        var required = strVal(params.required),
            textarea = document.getElementById(params.name),
            input = document.getElementById('form-table-field-input--' + params.randomisedId);

        if (required.includes('wysiwyg') && window.$editing.wysiwygOn()) {
            textarea.readOnly = true;
        }

        if (input) {
            $cms.form.setUpChangeMonitor(input.parentElement);
        }

        if (!$cms.isMobile()) {
            $cms.ui.manageScrollHeight(textarea);
        }

        $cms.requireJavascript(['jquery', 'jquery_autocomplete']).then(function () {
            window.$jqueryAutocomplete.setUpComcodeAutocomplete(params.name, required.includes('wysiwyg'));
        });
    };

    $cms.templates.formScreenInputAuthor = function formScreenInputAuthor(params, container) {
        $dom.on(container, 'keyup', '.js-keyup-update-ajax-author-list', function (e, target) {
            $cms.form.updateAjaxMemberList(target, 'author', false, e);
        });
    };

    $cms.templates.formScreenInputColour = function (params) {
        var label = params.rawField ? ' ' : params.prettyName;

        window.$themeColours.makeColourChooser(params.name, params.default, '', params.tabindex, label, 'input-colour' + params._required, params._required);
        window.$themeColours.doColorChooser();
    };

    $cms.templates.formScreenInputTreeList = function formScreenInputTreeList(params, container) {
        var name = strVal(params.name),
            hook = $cms.filter.url(params.hook),
            rootId = $cms.filter.url(params.rootId),
            opts = $cms.filter.url(params.options),
            multiSelect = Boolean(params.multiSelect) && (params.multiSelect !== '0');

        $cms.requireJavascript('tree_list').then(function () {
            $cms.ui.createTreeList(params.name, '{$FIND_SCRIPT_NOHTTP;,ajax_tree}?hook=' + hook + $cms.keep(), rootId, opts, multiSelect, params.tabIndex, false, Boolean(params.useServerId));
        });

        $dom.on(container, 'change', '.js-input-change-update-mirror', function (e, input) {
            var mirror = document.getElementById(name + '-mirror');
            if (mirror) {
                $dom.toggle(mirror.parentElement, Boolean(input.selectedTitle));
                $dom.html(mirror, input.selectedTitle ? $cms.filter.html(input.selectedTitle) : '{!NA_EM;}');
            }
        });
    };

    $cms.templates.formScreenInputPermissionMatrix = function (params, container) {
        window.permServerid = params.serverId;

        $dom.on(container, 'click', '.js-click-permissions-toggle', function (e, clicked) {
            var cell = $dom.closest(clicked, 'th, td');
            permissionsToggle(cell);
        });

        function permissionsToggle(cell) {
            var index = cell.cellIndex;
            var table = $dom.parent(cell, 'table');
            var stateList = null;
            var stateCheckbox = null;

            for (var i = 1; i < table.rows.length; i++) {
                // ^ Note: Start from `one` to skip the titles row in <thead>
                var cell2 = table.rows[i].cells[index];
                var input = cell2.querySelector('input[type=checkbox], select');

                if (input == null) {
                    continue;
                }

                if (input.localName === 'input') {
                    // <input type=checkbox> field
                    if (!input.disabled) {
                        if (stateCheckbox == null) {
                            stateCheckbox = input.checked;
                        }
                        input.checked = !stateCheckbox;
                    }
                } else {
                    // <select> field
                    if (stateList == null) {
                        stateList = input.selectedIndex;
                    }
                    input.selectedIndex = ((stateList !== (input.options.length - 1)) ? (input.options.length - 1) : (input.options.length - 2));
                    input.disabled = false;

                    permissionsOverridden(table.rows[i].id.replace(/-privilege-container$/, ''));
                }
            }
        }
    };

    $cms.templates.formScreenInputDate = function formScreenInputDate(params) {
        if (!$dom.support.inputTypes.date) {
            window.jQuery('#' + params.name).inputDate({});
        }
        if (!$dom.support.inputTypes.time) {
            window.jQuery('#' + params.name).inputTime({});
        }
    };

    $cms.templates.formScreenInputTime = function formScreenInputTime(params) {
        if (!$dom.support.inputTypes.time) {
            window.jQuery('#' + params.name).inputTime({});
        }
    };

    $cms.templates.formScreenInputText = function formScreenInputText(params) {
        if (params.required.includes('wysiwyg')) {
            if (window.$editing && window.$editing.wysiwygOn()) {
                document.getElementById(params.name).readOnly = true;
            }
        }

        if (!$cms.isMobile()) {
            $cms.ui.manageScrollHeight(document.getElementById(params.name));
        }
    };

    $cms.templates.formScreenInputHugeInput = function (params) {
        var textArea = document.getElementById(params.name),
            el = $dom.$('#form-table-field-input--' + params.randomisedId);

        if (el) {
            $cms.form.setUpChangeMonitor(el.parentElement);
        }

        if (!$cms.isMobile()) {
            $cms.ui.manageScrollHeight(textArea);

            $dom.on(textArea, 'change keyup', function () {
                $cms.ui.manageScrollHeight(textArea);
            });
        }
    };

    $cms.templates.postingField = function postingField(params/* NB: multiple containers */) {
        var id = strVal(params.id),
            name = strVal(params.name),
            initDragDrop = Boolean(params.initDragDrop),
            postEl = $dom.$('#' + name),
            // Container elements:
            labelRow = $dom.$('#field-' + id + '-label'),
            inputRow = $dom.$('#field-' + id + '-input');

        if (params.class.includes('wysiwyg')) {
            if (window.$editing && window.$editing.wysiwygOn()) {
                postEl.readOnly = true; // Stop typing while it loads

                setTimeout(function () {
                    if (postEl.value === postEl.defaultValue) {
                        postEl.readOnly = false; // Too slow, maybe WYSIWYG failed due to some network issue
                    }
                }, 3000);
            }

            if (params.wordCounter !== undefined) {
                setupWordCounter($dom.$('#post'), $dom.$('#word-count-' + params.wordCountId));
            }
        }

        if (!$cms.isMobile()) {
            $cms.ui.manageScrollHeight(postEl);
        }

        $cms.requireJavascript(['jquery', 'jquery_autocomplete']).then(function () {
            window.$jqueryAutocomplete.setUpComcodeAutocomplete(name, true);
        });

        if (initDragDrop) {
            $cms.requireJavascript('plupload').then(function () {
                window.$plupload.initialiseHtml5DragdropUpload('container-for-' + name, name);
            });
        }

        $dom.on(labelRow, 'click', '.js-click-toggle-wysiwyg', function () {
            window.$editing.toggleWysiwyg(name);
        });

        $dom.on(labelRow, 'click', '.js-link-click-open-field-emoticon-chooser-window', function (e, link) {
            var url = $util.rel($cms.maintainThemeInLink(link.href));
            $cms.ui.open(url, 'field_emoticon_chooser', 'width=300,height=320,status=no,resizable=yes,scrollbars=no');
        });

        $dom.on(inputRow, 'click', '.js-link-click-open-site-emoticon-chooser-window', function (e, link) {
            var url = $util.rel($cms.maintainThemeInLink(link.href));
            $cms.ui.open(url, 'site_emoticon_chooser', 'width=300,height=320,status=no,resizable=yes,scrollbars=no');
        });
    };

    $cms.templates.comcodeEditor = function (params, container) {
        var postingField = strVal(params.postingField);

        $dom.on(container, 'click', '.js-click-do-input-font-posting-field', function () {
            window.doInputFont(postingField);
        });
    };

    $cms.templates.formScreenInputTick = function (params, el) {
        if (params.name === 'validated') {
            if (el.previousElementSibling.classList.contains('validated-checkbox')) {
                el.previousElementSibling.classList.toggle('checked', el.checked);
            }

            $dom.on(el, 'click', function () {
                if (el.previousElementSibling.classList.contains('validated-checkbox')) {
                    el.previousElementSibling.classList.toggle('checked', el.checked);
                }
            });
        }

        if (params.name === 'delete') {
            assignTickDeletionConfirm(params.name);
        }

        function assignTickDeletionConfirm(name) {
            var el = document.getElementById(name);

            el.onchange = function () {
                if (this.checked) {
                    $cms.ui.confirm(
                        '{!ARE_YOU_SURE_DELETE;^}',
                        function (result) {
                            if (result) {
                                var form = el.form;
                                if (!form.action.includes('_post')) { // Remove redirect if redirecting back, IF it's not just deleting an on-page post (Wiki+)
                                    form.action = form.action.replace(/([&?])redirect=[^&]*/, '$1');
                                }
                            } else {
                                el.checked = false;
                            }
                        }
                    );
                }
            };
        }
    };

    $cms.templates.formScreenInputList = function formScreenInputList(params, selectEl) {
        var imageSources;

        if (params.inlineList) {
            return;
        }

        if (params.images !== undefined) {
            imageSources = JSON.parse(strVal(params.imageSources) || '{}');
        }

        var select2Options = {
            dropdownAutoWidth: window.parent === window, /*Otherwise can overflow*/
            templateResult: (params.images === undefined) ? formatSelectSimple : formatSelectImage
        };
        if (window.jQuery && (window.jQuery.fn.select2 != null) && (selectEl.options.length > 20)/*only for long lists*/ && (!$dom.html(selectEl.options[1]).match(/^\d+$/)/*not for lists of numbers*/)) {
            selectEl.classList.remove('form-control');
            window.jQuery(selectEl).select2(select2Options);
        }

        function formatSelectSimple(state) {
            if (!state.id) { // optgroup
                return state.text;
            }
            return $('<span title="' + $cms.filter.html(state.element[0].title) + '">' + $cms.filter.html(state.text) + '</span>');
        }

        function formatSelectImage(state) {
            if (!state.id) {
                return state.text; // optgroup
            }

            for (var imageName in imageSources) {
                if (state.id === imageName) {
                    return $('<span class="vertical-alignment inline-lined-up"><img style="width: 24px;" src="' + imageSources[imageName] + '" /> ' + $cms.filter.html(state.text) + '</span>');
                }
            }

            return $cms.filter.html(state.text);
        }
    };

    $cms.templates.formScreenInputMultiList = function formScreenInputMultiList(params, parentEl) {
        var select2Options = {
            dropdownAutoWidth: window.parent === window, /*Otherwise can overflow*/
            containerCssClass: 'form-control-wide'
        };

        var selectEl = parentEl.querySelector('select');

        if (window.jQuery && (window.jQuery.fn.select2 !== null) && (selectEl.size === 5)/*only for short UIs*/) {
            selectEl.classList.remove('form-control');
            window.jQuery(selectEl).select2(select2Options);
        }

        $dom.on(selectEl, 'keypress', '.js-keypress-input-ensure-next-field', function (e, input) {
            $coreFormInterfaces.ensureNextField2(e, input);
        });
    };

    $cms.templates.formScreenInputHugeListInput = function (params, parentEl) {
        var select2Options = {
            dropdownAutoWidth: window.parent === window, /*Otherwise can overflow*/
            containerCssClass: 'form-control-wide'
        };

        var selectEl = parentEl.querySelector('select');

        if (window.jQuery && (window.jQuery.fn.select2 != null) && (selectEl.size <= 1)) {
            selectEl.classList.remove('form-control');
            window.jQuery(selectEl).select2(select2Options);
        }

        var el = $dom.$('#form-table-field-input--' + params.randomisedId);

        if (!params.inlineList && el) {
            $cms.form.setUpChangeMonitor(el.parentElement);
        }
    };

    $cms.templates.formScreenInputThemeImageEntry = function (params) {
        var name = $cms.filter.id(params.name),
            code = $cms.filter.id(params.code),
            stem = name + '-' + code,
            el = document.getElementById('w-' + stem),
            img = el.querySelector('img'),
            input = document.getElementById('j-' + stem),
            label = el.querySelector('label'),
            form = input.form;

        el.onkeypress = function (event) {
            if ($dom.keyPressed(event, 'Enter')) {
                return clickFunc(event);
            }
        };

        function clickFunc(event) {
            choosePicture('j-' + stem, img, name, event);

            if (window.mainFormVerySimple !== undefined) {
                form.submit();
            }
        }

        $dom.on(img, 'keypress', clickFunc);
        $dom.on(img, 'click', clickFunc);
        $dom.on(el, 'click', clickFunc);

        label.className = 'js-widget';

        $dom.on(input, 'click', function () {
            if (input.disabled) {
                return;
            }

            deselectAltUrl(input.form);

            if (window.mainFormVerySimple !== undefined) {
                input.form.submit();
            }
        });

        function deselectAltUrl(form) {
            if (form.elements['alt_url'] != null) {
                form.elements['alt_url'].value = '';
            }
        }

    };

    $cms.templates.formScreenInputRadioList = function (params) {
        if (params.name === undefined) {
            return;
        }

        if (params.code !== undefined) {
            choosePicture('j_' + $cms.filter.id(params.name) + '_' + $cms.filter.id(params.code), null, params.name, null);
        }

        if (params.name === 'delete') {
            assignRadioDeletionConfirm(params.name);
        }

        function assignRadioDeletionConfirm(name) {
            for (var i = 1; i < 3; i++) {
                var el = document.getElementById('j_' + name + '_' + i);
                if (el) {
                    el.onchange = function () {
                        if (this.checked) {
                            $cms.ui.confirm('{!ARE_YOU_SURE_DELETE;^}').then(function (result) {
                                var el2 = document.getElementById('j_' + name + '_0');
                                if (el2) {
                                    if (result) {
                                        var form = el2.form;
                                        form.action = form.action.replace(/([&?])redirect=[^&]*/, '$1');
                                    } else {
                                        el2.checked = true; // Check first radio
                                    }
                                }
                            });
                        }
                    };
                }
            }
        }
    };

    $cms.templates.formScreenInputRadioListComboEntry = function formScreenInputRadioListComboEntry(params, container) {
        var nameId = $cms.filter.id(params.name);

        toggleOtherCustomInput();
        $dom.on(container, 'change', '.js-change-toggle-other-custom-input', function () {
            toggleOtherCustomInput();
        });

        function toggleOtherCustomInput() {
            $dom.$('#j-' + nameId + '-other-custom').disabled = !$dom.$('#j-' + nameId + '-other').checked;
        }
    };

    $cms.templates.formScreenInputVariousTicks = function formScreenInputVariousTicks(params, container) {
        var customName = strVal(params.customName);

        if (customName && !params.customAcceptMultiple) {
            var el = document.getElementById(params.customName + '_value');
            $dom.trigger(el, 'change');
        }

        $dom.on(container, 'click', '.js-click-checkbox-toggle-value-field', function (e, checkbox) {
            document.getElementById(customName + '_value').disabled = !checkbox.checked;
        });

        $dom.on(container, 'change', '.js-change-input-toggle-value-checkbox', function (e, input) {
            document.getElementById(customName).checked = (input.value !== '');
            input.disabled = (input.value === '');
        });

        $dom.on(container, 'keypress', '.js-keypress-input-ensure-next-field', function (e, input) {
            $coreFormInterfaces.ensureNextField2(e, input);
        });
    };

    $cms.templates.formScreen = function (params, container) {
        tryToSimplifyIframeForm();

        if (params.iframeUrl) {
            setInterval(function () {
                $dom.resizeFrame('iframe-under');
            }, 1500);

            $dom.on(container, 'click', '.js-checkbox-will-open-new', function (e, checkbox) {
                var form = $dom.$(container, '#main-form');

                form.action = checkbox.checked ? params.url : params.iframeUrl;
                form.elements['opens_below'].value = checkbox.checked ? '0' : '1';
                form.target = checkbox.checked ? '_blank' : 'iframe-under';
            });
        }

        $dom.on(container, 'click', '.js-btn-skip-step', function (e, el) {
            $dom.$('#' + params.skippable).value = '1';
            el.form.submit();
        });
    };

    $cms.templates.form = function (params, container) {
        var skippable = strVal(params.skippable);

        $dom.on(container, 'click', '.js-btn-skip-step', function (e, el) {
            $dom.$('#' + skippable).value = '1';
            el.form.submit();
        });
    };

    $cms.templates.formScreenFieldSpacer = function (params, container) {
        var titleId = $cms.filter.id(params.title),
            sectionHidden = Boolean(params.sectionHidden);

        if (titleId !== '') {
            $dom.on(container, 'click', '.js-click-toggle-subord-fields', function (e, clicked) {
                toggleSubordinateFields(clicked, 'fes-' + titleId + '-help');
            });

            $dom.on(container, 'keypress', '.js-keypress-toggle-subord-fields', function (e, pressed) {
                toggleSubordinateFields(pressed, 'fes-' + titleId + '-help');
            });

            $dom.on(container, 'click', '.js-click-geolocate-address-fields', function () {
                geolocateAddressFields();
            });

            if (sectionHidden) {
                $dom.trigger('#fes-' + titleId, 'click');
            }
        }
    };

    $cms.templates.formScreenFieldsSet = function (params) {
        standardAlternateFieldsWithin(params.setName, Boolean(params.required), params.defaultSet);
    };

    $cms.templates.formScreenFieldsSetItem = function formScreenFieldsSetItem(params) {
        var el = document.getElementById('form-table-field-input--' + params.name);

        if (el) {
            $cms.form.setUpChangeMonitor(el.parentElement);
        }

        var block = document.getElementById('field_set_' + params.name),
            radioBtn = document.getElementById('choose-' + params.name);
        block.addEventListener('click', function (e) {
            if ((e.target === radioBtn) || $dom.closest(e.target, 'a')) {
                return; // Prevent infinite loop
            }

            window.setTimeout(function () {
                $dom.trigger(radioBtn, 'click');
            }, 0);
        });
    };

    $cms.templates.previewScript = function (params, container) {
        var inner = $dom.$(container, '.js-preview-box-scroll');

        $dom.on(container, 'click', function () {
            $dom.triggerResize();
        });

        if (inner) {
            $dom.on(inner, $cms.browserMatches('gecko')/*LEGACY*/ ? 'DOMMouseScroll' : 'mousewheel', function (event) {
                inner.scrollTop -= event.wheelDelta ? event.wheelDelta : event.detail;
                event.preventDefault();
            });
        }

        $dom.on(container, 'click', '.js-click-preview-mobile-button', function (event, el) {
            el.form.action = el.form.action.replace(/keep_mobile=\d/g, 'keep_mobile=' + (el.checked ? '1' : '0'));
            if (window.parent) {
                try {
                    window.parent.scrollTo(0, $dom.findPosY(window.parent.document.getElementById('preview-iframe')));
                } catch (e) {}
                window.parent.mobileVersionForPreview = Boolean(el.checked);
                $dom.trigger(window.parent.document.getElementById('preview-button'), 'click');
                return;
            }

            el.form.submit();
        });
    };

    $cms.templates.previewScriptCode = function (params) {
        var newPostValue = strVal(params.newPostValue),
            newPostValueHtml = strVal(params.newPostValueHtml),
            mainWindow = $cms.getMainCmsWindow();

        var postField = strVal(params.attachmentField);

        var post = mainWindow.document.getElementById(postField);

        // Replace Comcode
        var oldComcode = mainWindow.$editing.getTextbox(post);
        mainWindow.$editing.setTextbox(post, newPostValue.replace(/&#111;/g, 'o').replace(/&#79;/g, 'O'), newPostValueHtml);

        // Turn main post editing back on
        if (window.$editing !== undefined) {
            window.$editing.wysiwygSetReadonly(postField, false);
        }

        // Remove attachment uploads
        var inputs = post.form.elements, uploadButton,
            i, doneOne = false;

        for (i = 0; i < inputs.length; i++) {
            if (((inputs[i].type === 'file') || ((inputs[i].type === 'text') && (inputs[i].disabled))) && (inputs[i].value !== '') && (inputs[i].name.match(/file\d+/))) {
                if ($dom.data(inputs[i]).pluploadObject != null) {
                    if ((inputs[i].value !== '-1') && (inputs[i].value !== '')) {
                        if (!doneOne) {
                            if (!oldComcode.includes('attachment_safe')) {
                                $cms.ui.alert('{!javascript:ATTACHMENT_SAVED;^}');
                            } else {
                                if (!mainWindow.$cms.form.isWysiwygField(post)) {// Only for non-WYSIWYG, as WYSIWYG has preview automated at same point of adding
                                    $cms.ui.alert('{!javascript:ATTACHMENT_SAVED;^}');
                                }
                            }
                        }
                        doneOne = true;
                    }

                    uploadButton = mainWindow.document.getElementById('upload-button-' + inputs[i].name);
                    if (uploadButton) {
                        uploadButton.disabled = true;
                    }
                    inputs[i].value = '-1';
                } else {
                    try {
                        inputs[i].value = '';
                    } catch (e) {}
                }
                if (inputs[i].form.elements['hid_file_id_' + inputs[i].name] !== undefined) {
                    inputs[i].form.elements['hid_file_id_' + inputs[i].name].value = '';
                }
            }
        }
    };

    $cms.templates.blockHelperDone = function (params) {
        var targetWin = window.opener ? window.opener : window.parent,
            element = targetWin.document.getElementById(params.fieldName);

        if (!element) {
            var overlay = target_window.document.getElementById('overlay-iframe');
            if (overlay) {
                targetWin = overlay.contentWindow;
                element = targetWin.document.getElementById(params.fieldName);
            }
        }
        if (!element) {
            targetWin = targetWin.frames['iframe_page'];
            element = targetWin.document.getElementById(params.fieldName);
        }

        var block = strVal(params.block),
            tagContents = strVal(params.tagContents),
            comcode = strVal(params.comcode),
            comcodeSemihtml = strVal(params.comcodeSemihtml),
            isWysiwyg = targetWin.$cms.form.isWysiwygField(element),
            loadingSpace = document.getElementById('loading-space'),
            attachedEventAction = false;

        window.returnValue = comcode;

        if ((block === 'attachment_safe') && /^new_\d+$/.test(tagContents)) {
            // WYSIWYG-editable attachments must be synched
            var field = 'file' + tagContents.substr(4),
                uploadEl = targetWin.document.getElementById(field);

            if (!uploadEl) {
                uploadEl = targetWin.document.getElementById('hid_file_id_' + field);
            }

            if (($dom.data(uploadEl).pluploadObject != null) && isWysiwyg) {
                var ob = $dom.data(uploadEl).pluploadObject;
                if (Number(ob.state) === Number(targetWin.plupload.STARTED)) {
                    ob.bind('UploadComplete', function () {
                        setTimeout(dispatchBlockHelper, 100); // Give enough time for everything else to update
                    });
                    ob.bind('Error', shutdownOverlay);

                    // Keep copying the upload indicator
                    var imageProgressElement = targetWin.document.getElementById('fsUploadProgress_' + field);
                    if (imageProgressElement) {
                        var progress = $dom.html(imageProgressElement);
                        setInterval(function () {
                            if (progress !== '') {
                                $dom.html(loadingSpace, progress);
                                loadingSpace.className = 'spaced';
                            }
                        }, 100);
                    }

                    attachedEventAction = true;
                }
            }
        }

        if (!attachedEventAction) {
            setTimeout(dispatchBlockHelper, 1000); // Delay it, so if we have in a faux pop-up it can set up fauxClose
        }

        function shutdownOverlay() {
            setTimeout(function () { // Close top-level window in timeout, so that this will close first (issue on Firefox) / give chance for messages
                if (window.fauxClose !== undefined) {
                    window.fauxClose();
                } else {
                    window.close();
                }
            }, 200);
        }

        function dispatchBlockHelper() {
            var saveToId = strVal(params.saveToId),
                toDelete = Boolean(params.delete);

            if (saveToId !== '') {
                var ob = targetWin.wysiwygEditors[element.id].document.$.getElementById(saveToId);

                if (toDelete) {
                    ob.parentNode.removeChild(ob);
                } else {
                    var inputContainer = document.createElement('div');
                    $dom.html(inputContainer, comcodeSemihtml.replace(/^\s*/, ''));
                    ob.parentNode.replaceChild(inputContainer.firstElementChild, ob);
                }

                targetWin.wysiwygEditors[element.id].updateElement();

                shutdownOverlay();
                return;
            }

            var message = '';
            if (comcode.includes('[attachment') && comcode.includes('[attachment_safe') && !isWysiwyg) {
                message = '{!comcode:ADDED_COMCODE_ONLY_SAFE_ATTACHMENT;^}';
            }

            // We define as a temporary global method so we can clone out the tag if needed (e.g. for multiple attachment selections)
            targetWin.insertComcodeTag = function insertComcodeTag(repFrom, repTo, ret, callback) {
                ret = Boolean(ret);

                var newComcodeSemihtml = comcodeSemihtml,
                    newComcode = comcode;

                if (repFrom != null) {
                    for (var i = 0; i < repFrom.length; i++) {
                        newComcodeSemihtml = newComcodeSemihtml.replace(repFrom[i], repTo[i]);
                        newComcode = newComcode.replace(repFrom[i], repTo[i]);
                    }
                }

                if (ret) {
                    if (callback != null) {
                        callback();
                    }
                    return [newComcodeSemihtml, newComcode];
                }

                var promise = Promise.resolve();
                if (!element.value.includes(comcodeSemihtml) || !comcode.includes('[attachment')) { // Don't allow attachments to add twice
                    promise = targetWin.$editing.insertTextbox(element, newComcode, true, newComcodeSemihtml);
                }

                promise.then(function () {
                    if (callback != null) {
                        callback();
                    }
                });
            };

            var promise = Promise.resolve();
            if (params.prefix !== undefined) {
                promise = targetWin.$editing.insertTextbox(element, params.prefix, true, '');
            }
            promise.then(function () {
                targetWin.insertComcodeTag(null, null, false, function () {
                    if (message !== '') {
                        $cms.ui.alert(message).then(function () {
                            shutdownOverlay();
                        });
                    } else {
                        shutdownOverlay();
                    }
                });
            });
        }
    };

    /**
     * Marking things (to avoid illegally nested forms)
     * @memberof $cms.form
     * @param form
     * @param prefix
     * @returns {boolean}
     */
    $cms.form.addFormMarkedPosts = function addFormMarkedPosts(form, prefix) {
        prefix = strVal(prefix);

        var get = form.method.toLowerCase() === 'get',
            i;

        if (get) {
            for (i = 0; i < form.elements.length; i++) {
                if ((new RegExp('&' + prefix + '\\d+=1$', 'g')).test(form.elements[i].name)) {
                    form.elements[i].parentNode.removeChild(form.elements[i]);
                }
            }
        } else {
            // Strip old marks out of the URL
            form.action = form.action.replace('?', '&')
                .replace(new RegExp('&' + prefix + '\\d+=1$', 'g'), '')
                .replace('&', '?'); // will just do first due to how JS works
        }

        var checkboxes = $dom.$$('input[type="checkbox"][name^="' + prefix + '"]:checked'),
            append = '';

        for (i = 0; i < checkboxes.length; i++) {
            append += (((append === '') && !form.action.includes('?') && !form.action.includes('/pg/') && !get) ? '?' : '&') + checkboxes[i].name + '=1';
        }

        if (get) {
            var bits = append.split('&');
            for (i = 0; i < bits.length; i++) {
                if (bits[i] !== '') {
                    $dom.append(form, $dom.create('input', {
                        name: bits[i].substr(0, bits[i].indexOf('=1')),
                        type: 'hidden',
                        value: '1'
                    }));
                }
            }
        } else {
            form.action += append;
        }

        return append !== '';
    };

    /**
     * @memberof $cms.form
     * @return {boolean}
     */
    $cms.form.isModSecurityWorkaroundEnabled = function isModSecurityWorkaroundEnabled() {
        return '{$VALUE_OPTION;,disable_modsecurity_workaround}' !== '1';
    };

    /**
     * @memberof $cms.form
     * @param form
     * @returns {boolean}
     */
    $cms.form.modSecurityWorkaround = function modSecurityWorkaround(form) {
        $util.log('Using modSecurityWorkaround');

        var tempForm = document.createElement('form');
        tempForm.method = 'post';

        if (form.target) {
            tempForm.target = form.target;
        }
        tempForm.action = form.action;

        var data = $dom.serialize(form);
        data = _modSecurityWorkaround(data);

        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_data';
        input.value = data;
        tempForm.appendChild(input);

        if (form.elements['csrf_token']) {
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = form.elements['csrf_token'].value;
            tempForm.appendChild(csrfInput);
        }

        tempForm.style.display = 'none';
        document.body.appendChild(tempForm);

        setTimeout(function () {
            tempForm.submit();
            tempForm.parentNode.removeChild(tempForm);
        });

        form.submittedFormAlready = true;
    };

    /**
     * @memberof $cms.form
     * @param data
     * @returns {string}
     */
    $cms.form.modSecurityWorkaroundAjax = function modSecurityWorkaroundAjax(data) {
        return '_data=' + encodeURIComponent(_modSecurityWorkaround(data));
    };

    function _modSecurityWorkaround(data) {
        data = strVal(data);

        var remapper = {
                '\\': '<',
                '/': '>',
                '<': '\'',
                '>': '"',
                '\'': '/',
                '"': '\\',
                '%': '&',
                '&': '%',
                '@': ':',
                ':': '@'
            },
            out = '',
            character;

        for (var i = 0; i < data.length; i++) {
            character = data[i];
            if (remapper[character] !== undefined) {
                out += remapper[character];
            } else {
                out += character;
            }
        }

        return out;
    }

    /* Set up a word count for a form field */
    function setupWordCounter(post, countElement) {
        setInterval(function () {
            if ($cms.form.isWysiwygField(post)) {
                try {
                    var textValue = window.CKEDITOR.instances[post.name].getData();
                    var matches = textValue.replace(/<[^<|>]+?>|&nbsp;/gi, ' ').match(/\b/g);
                    var count = 0;
                    if (matches) {
                        count = matches.length / 2;
                    }
                    var wordsText = $util.format('{!WORDS;^}', [count]);

                    if ($dom.html(countElement) !== wordsText) {
                        $dom.html(countElement, $util.format('{!WORDS;^}', [count]));
                    }
                } catch (e) {}
            }
        }, 1000);
    }

    function permissionsOverridden(select) {
        select = strVal(select);

        var element = document.getElementById(select + '-presets');

        if (!element) {
            element = document.getElementById(select.replaceAll('-', '_') + '_presets');
        }

        if (element.options[0].id !== select + '-custom-option') {
            var newOption = document.createElement('option');
            $dom.html(newOption, '{!permissions:PINTERFACE_LEVEL_CUSTOM;^}');
            newOption.id = select + '-custom-option';
            newOption.value = '';
            element.insertBefore(newOption, element.options[0]);
        }
        element.selectedIndex = 0;
    }

    function tryToSimplifyIframeForm() {
        var iframe = document.getElementById('iframe-under'),
            formCatSelector = document.getElementById('main-form'),
            elements, i, element,
            count = 0, found, foundButton;

        if (!formCatSelector) {
            return;
        }

        elements = $dom.$$(formCatSelector, 'input, button, select, textarea');
        for (i = 0; i < elements.length; i++) {
            element = elements[i];
            if (((element.localName === 'input') && (element.type !== 'hidden') && (element.type !== 'button') && (element.type !== 'image') && (element.type !== 'submit')) || (element.localName === 'select') || (element.localName === 'textarea')) {
                found = element;
                count++;
            }
            if (((element.localName === 'input') && ((element.type === 'button') || (element.type === 'image') || (element.type === 'submit'))) || (element.localName === 'button')) {
                foundButton = element;
            }
        }

        if ((count === 1) && (found.localName === 'select')) {
            if (iframe) {
                $dom.on(found, 'change', foundChangeHandler);

                if ((found.size > 1) || (found.multiple)) {
                    $dom.on(found, 'click', foundChangeHandler);
                }

                foundButton.style.display = 'none';
            }
        }

        function foundChangeHandler(e) {
            if (iframe) {
                if (iframe.contentDocument && (iframe.contentDocument.getElementsByTagName('form').length !== 0)) {
                    $cms.ui.confirm('{!Q_SURE_LOSE;^}').then(function (result) {
                        if (result) {
                            _simplifiedFormContinueSubmit(e, iframe, formCatSelector);
                        }
                    });

                    return null;
                }
            }

            _simplifiedFormContinueSubmit(e, iframe, formCatSelector);

            return null;
        }
    }

    function _simplifiedFormContinueSubmit(e, iframe, formCatSelector) {
        $cms.form.checkForm(e, formCatSelector, false, []).then(function (valid) {
            if (valid) {
                if (iframe) {
                    $dom.animateFrameLoad(iframe, 'iframe-under');
                }
                formCatSelector.submit();
            }
        });
    }

    /* Geolocation for address fields */
    function geolocateAddressFields() {
        if (!navigator.geolocation) {
            return;
        }
        try {
            navigator.geolocation.getCurrentPosition(function (position) {
                var fields = [
                    '{!cns_special_cpf:SPECIAL_CPF__cms_street_address;^}',
                    '{!cns_special_cpf:SPECIAL_CPF__cms_city;^}',
                    '{!cns_special_cpf:SPECIAL_CPF__cms_county;^}',
                    '{!cns_special_cpf:SPECIAL_CPF__cms_state;^}',
                    '{!cns_special_cpf:SPECIAL_CPF__cms_post_code;^}',
                    '{!cns_special_cpf:SPECIAL_CPF__cms_country;^}'
                ];

                var geocodeUrl = '{$FIND_SCRIPT_NOHTTP;,geocode}';
                geocodeUrl += '?latitude=' + encodeURIComponent(position.coords.latitude) + '&longitude=' + encodeURIComponent(position.coords.longitude);
                geocodeUrl += $cms.keep();

                $cms.doAjaxRequest(geocodeUrl).then(function (xhr) {
                    var parsed = JSON.parse(xhr.responseText);
                    if (parsed === null) {
                        return;
                    }
                    var labels = document.getElementsByTagName('label'), label, fieldName, field;
                    for (var i = 0; i < labels.length; i++) {
                        label = $dom.html(labels[i]);
                        for (var j = 0; j < fields.length; j++) {
                            if (fields[j].replace(/^.*: /, '') === label) {
                                if (parsed[j + 1] === null) {
                                    parsed[j + 1] = '';
                                }

                                fieldName = labels[i].for;
                                field = document.getElementById(fieldName);
                                if (field.localName === 'select') {
                                    field.value = parsed[j + 1];
                                    if (window.jQuery && window.jQuery.fn.select2 !== undefined) {
                                        window.jQuery(field).trigger('change');
                                    }
                                } else {
                                    field.value = parsed[j + 1];
                                }
                            }
                        }
                    }
                });
            });
        } catch (ignore) {}
    }

    // Hide a 'tray' of trs in a form
    function toggleSubordinateFields(anchor, helpId) {
        var fieldInput = $dom.parent(anchor, '.form-table-field-spacer'),
            icon = fieldInput.querySelector('.toggleable-tray-button .icon'),
            iconAnchor = $dom.parent(icon, 'a'),
            next = fieldInput.nextElementSibling,
            newDisplayState, newDisplayState2;

        if (!next) {
            return;
        }

        while (next.classList.contains('field-input')) { // Sometimes divs or whatever may have erroneously been put in a table by a programmer, skip past them
            next = next.nextElementSibling;
            if (!next || next.classList.contains('form-table-field-spacer')) { // End of section, so no need to keep going
                next = null;
                break;
            }
        }

        if ((!next && $cms.isIcon(icon, 'trays/expand')) || (next && (next.style.display === 'none'))) {/* Expanding now */
            iconAnchor.title = '{!CONTRACT;^}';
            if (iconAnchor.cmsTooltipTitle != null) {
                iconAnchor.cmsTooltipTitle = '{!CONTRACT;^}';
            }
            $cms.ui.setIcon(icon, 'trays/contract', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/trays/contract}');
            newDisplayState = ''; // default state from CSS
            newDisplayState2 = ''; // default state from CSS
        } else { /* Contracting now */
            iconAnchor.title = '{!EXPAND;^}';
            if (iconAnchor.cmsTooltipTitle != null) {
                iconAnchor.cmsTooltipTitle = '{!EXPAND;^}';
            }
            $cms.ui.setIcon(icon, 'trays/expand', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/trays/expand}');
            newDisplayState = 'none';
            newDisplayState2 = 'none';
        }

        // Hide everything until we hit end of section
        var count = 0;
        while (fieldInput.nextElementSibling) {
            fieldInput = fieldInput.nextElementSibling;

            /* Start of next section? */
            if (fieldInput.classList.contains('form-table-field-spacer')) {
                break; // End of section
            }

            /* Ok to proceed */

            if ((newDisplayState2 !== 'none') && (count < 50/*Performance*/)) {
                $dom.fadeIn(fieldInput);
                count++;
            } else {
                fieldInput.style.display = newDisplayState;
            }
        }

        var help = document.getElementById(helpId);

        while (help !== null) {
            help.style.display = newDisplayState2;
            help = help.nextElementSibling;
            if (help && (help.localName !== 'p')) {
                break;
            }
        }

        $dom.triggerResize();
    }

    function choosePicture(jId, imgOb, name) {
        var jEl = document.getElementById(jId);
        if (!jEl) {
            return;
        }

        if (!imgOb) {
            imgOb = document.getElementById('w-' + jId.substring(2, jId.length)).querySelector('img');
            if (!imgOb) {
                return;
            }
        }

        var e = jEl.form.elements[name];
        for (var i = 0; i < e.length; i++) {
            if (e[i].disabled) {
                continue;
            }
            var img = e[i].parentNode.parentNode.querySelector('img');
            if (img && (img !== imgOb)) {
                if (img.parentNode.classList.contains('selected')) {
                    img.parentNode.classList.remove('selected');
                    img.style.outline = '0';
                    img.style.background = 'none';
                }
            }
        }

        if (jEl.disabled) {
            return;
        }
        $dom.changeChecked(jEl, true);

        imgOb.parentNode.classList.add('selected');
    }

    /**
     * @param setName
     * @param somethingRequired
     * @param defaultSet
     */
    function standardAlternateFieldsWithin(setName, somethingRequired, defaultSet) {
        setName = strVal(setName);
        somethingRequired = Boolean(somethingRequired);


        var form = $dom.closest('#set-wrapper-' + setName, 'form');

        var fields = form.elements[setName],
            fieldNames = [];

        for (var i = 0; i < fields.length; i++) {
            if (fields[i][0] === undefined) {
                if (fields[i].id.startsWith('choose-')) {
                    fieldNames.push(fields[i].id.replace(/^choose-/, ''));
                }
            } else { // RadioNodeList
                if (fields[i][0].id.startsWith('choose-')) {
                    fieldNames.push(fields[i][0].id.replace(/^choose-/, ''));
                }
            }
        }

        standardAlternateFields(fieldNames, somethingRequired, false, defaultSet);

        // Do dynamic $cms.form.setLocked/$cms.form.setRequired such that one of these must be set, but only one may be
        function standardAlternateFields(fieldNames, somethingRequired, secondRun, defaultSet) {
            secondRun = Boolean(secondRun);
            defaultSet = strVal(defaultSet);

            // Look up field objects
            var fields = [], i, field;

            for (i = 0; i < fieldNames.length; i++) {
                field = _standardAlternateFieldsGetObject(fieldNames[i]);
                fields.push(field);
            }

            // Set up listeners...
            for (i = 0; i < fieldNames.length; i++) {
                field = fields[i];
                if ((!field) || (field.alternating === undefined)) { // ... but only if not already set
                    // We'll re-call ourself on change
                    _standardAlternateFieldCreateListeners(field, function () {
                        standardAlternateFields(fieldNames, somethingRequired, true, '');
                    });
                }
            }

            // Update things
            for (i = 0; i < fieldNames.length; i++) {
                field = fields[i];
                if ((defaultSet === '') && (_standardAlternateFieldIsFilledIn(field, secondRun, false)) || (defaultSet !== '') && (fieldNames[i].indexOf('_' + defaultSet) !== -1)) {
                    _standardAlternateFieldUpdateEditability(field, fields, somethingRequired);
                    return;
                }
            }

            // Hmm, force first one chosen then
            for (i = 0; i < fieldNames.length; i++) {
                if (fieldNames[i] === '') {
                    var radioButton = document.getElementById('choose-'); // Radio button handles field alternation
                    radioButton.checked = true;
                    _standardAlternateFieldUpdateEditability(null, fields, somethingRequired);
                    return;
                }

                field = fields[i];
                if ((field) && (_standardAlternateFieldIsFilledIn(field, secondRun, true))) {
                    _standardAlternateFieldUpdateEditability(field, fields, somethingRequired);
                    return;
                }
            }

            function _standardAlternateFieldUpdateEditability(chosen, choices, somethingRequired) {
                for (var i = 0; i < choices.length; i++) {
                    __standardAlternateFieldUpdateEditability(choices[i], chosen, (choices[i] !== chosen), (choices[i] === chosen), somethingRequired);
                }
            }

            // NB: is_chosen may only be null if is_locked is false
            function __standardAlternateFieldUpdateEditability(field, chosenField, isLocked, isChosen, somethingRequired) {
                // eslint-disable-next-line no-restricted-properties
                if ((!field) || (field.nodeName !== undefined)) {
                    ___standardAlternateFieldUpdateEditability(field, chosenField, isLocked, isChosen, somethingRequired);
                } else { // List of fields (e.g. radio list, or just because standardAlternateFieldsWithin was used)
                    for (var i = 0; i < field.length; i++) {
                        if (field[i].name !== undefined) { // If it is an object, as opposed to some string in the collection
                            ___standardAlternateFieldUpdateEditability(field[i], chosenField, isLocked, isChosen, somethingRequired);
                            somethingRequired = false; // Only the first will be required
                        }
                    }
                }
            }

            function ___standardAlternateFieldUpdateEditability(field, chosenField, isLocked, isChosen, somethingRequired) {
                if (!field) {
                    return;
                }

                var radioButton = document.getElementById('choose-' + field.name.replace(/\[\]$/, ''));
                if (!radioButton) {
                    radioButton = document.getElementById('choose-' + field.name.replace(/_\d+$/, '_'));
                }

                $cms.form.setLocked(field, isLocked, chosenField);
                if (somethingRequired) {
                    $cms.form.setRequired(field.name.replace(/\[\]$/, ''), isChosen);
                }

                radioButton = $dom.$('#choose-' + field.name);
                if (radioButton) {
                    radioButton.checked = isChosen;
                }
            }

            function _standardAlternateFieldsGetObject(fieldName) {
                fieldName = strVal(fieldName);

                // Maybe it's an N/A so no actual field
                if (fieldName === '') {
                    return null;
                }

                // Try and get direct field
                var field = document.getElementById(fieldName);
                if (field) {
                    return field;
                }

                // A radio field, so we need to create a virtual field object to return that will hold our value
                var radioButtons = [], i, j, el;
                /*JSLINT: Ignore errors*/
                radioButtons['name'] = fieldName;
                radioButtons['value'] = '';
                for (i = 0; i < document.forms.length; i++) {
                    for (j = 0; j < document.forms[i].elements.length; j++) {
                        el = document.forms[i].elements[j];
                        if (!el.name) {
                            continue;
                        }

                        if ((el.name.replace(/\[\]$/, '') === fieldName) || (el.name.replace(/_\d+$/, '_') === fieldName)) {
                            radioButtons.push(el);
                            if (el.checked) {// This is the checked radio equivalent to our text field, copy the value through to the text field
                                radioButtons['value'] = el.value;
                            }
                            if (el.alternating) {
                                radioButtons.alternating = true;
                            }
                        }
                    }
                }

                if (radioButtons.length === 0) {
                    return null;
                }

                return radioButtons;
            }

            function _standardAlternateFieldIsFilledIn(field, secondRun, force) {
                if (!field) { // N/A input is considered unset
                    return false;
                }

                var isSet = force || ((field.value !== '') && (field.value !== '-1'));

                var radioButton = document.getElementById('choose-' + (field ? field.name : '').replace(/\[\]$/, '')); // Radio button handles field alternation
                if (!radioButton) {
                    radioButton = document.getElementById('choose-' + field.name.replace(/_\d+$/, '_'));
                }
                if (secondRun) {
                    if (radioButton) {
                        return radioButton.checked;
                    }
                } else {
                    if (radioButton) {
                        radioButton.checked = isSet;
                    }
                }
                return isSet;
            }

            function _standardAlternateFieldCreateListeners(field, refreshFunction) {
                // eslint-disable-next-line no-restricted-properties
                if ((!field) || (field.nodeName !== undefined)) {
                    __standardAlternateFieldCreateListeners(field, refreshFunction);
                } else {
                    var i;
                    for (i = 0; i < field.length; i++) {
                        if (field[i].name !== undefined) {
                            __standardAlternateFieldCreateListeners(field[i], refreshFunction);
                        }
                    }
                    field.alternating = true;
                }

                return null;
            }

            function __standardAlternateFieldCreateListeners(field, refreshFunction) {
                var radioButton = document.getElementById('choose-' + (field ? field.name : '').replace(/\[\]$/, ''));
                if (!radioButton) {
                    radioButton = document.getElementById('choose-' + field.name.replace(/_\d+$/, '_'));
                }
                if (radioButton) { // Radio button handles field alternation
                    radioButton.addEventListener('change', refreshFunction);
                } else { // Filling/blanking out handles field alternation
                    if (field) {
                        field.addEventListener('keyup', refreshFunction);
                        field.addEventListener('change', refreshFunction);
                    }
                }
                if (field) {
                    field.alternating = true;
                }
            }
        }
    }

    // ===========
    // Multi-field
    // ===========
    $coreFormInterfaces.ensureNextField2 = function ensureNextField2(event, el) {
        if ($dom.keyPressed(event, 'Enter')) {
            gotoNextField(el);
        } else if (!$dom.keyPressed(event, 'Tab')) {
            ensureNextField(el);
        }

        function gotoNextField(thisField) {
            var mid = thisField.id.lastIndexOf('_'),
                nameStub = thisField.id.substring(0, mid + 1),
                thisNum = thisField.id.substring(mid + 1, thisField.id.length) - 0,
                nextNum = thisNum + 1,
                nextField = document.getElementById(nameStub + nextNum);

            if (nextField) {
                try {
                    nextField.focus();
                } catch (e) {}
            }
        }
    };

    function ensureNextField(thisField) {
        var mid = thisField.id.lastIndexOf('_'),
            nameStub = thisField.id.substring(0, mid + 1),
            thisNum = thisField.id.substring(mid + 1, thisField.id.length) - 0,
            nextNum = thisNum + 1,
            nextField = document.getElementById(nameStub + nextNum),
            thisId = thisField.id;

        if (!nextField) {
            $util.log('Creating next field: ' + nameStub + nextNum);

            thisField = document.getElementById(thisId);
            var nextFieldWrap = document.createElement('div');
            nextFieldWrap.className = thisField.parentNode.className;
            if (thisField.localName === 'textarea') {
                nextField = document.createElement('textarea');
            } else {
                nextField = document.createElement('input');
                nextField.size = thisField.size;
            }
            nextField.className = thisField.className.replace(/-required/g, '');
            if (thisField.form.elements['label_for__' + nameStub + '0']) {
                // Hidden label input
                var nextLabel = document.createElement('input');
                nextLabel.type = 'hidden';
                nextLabel.value = thisField.form.elements['label_for__' + nameStub + '0'].value + ' (' + (nextNum + 1) + ')';
                nextLabel.name = 'label_for__' + nameStub + nextNum;
                nextFieldWrap.appendChild(nextLabel);

                // Hidden label for accessibility
                var nextLabelAccessibility = document.createElement('div');
                nextLabelAccessibility.classList.add('accessibility-hidden');
                var nextLabelAccessibilityLabel = document.createElement('label');
                nextLabelAccessibilityLabel.htmlFor = nameStub + nextNum;
                nextLabelAccessibilityLabel.innerText = thisField.form.elements['label_for__' + nameStub + '0'].value + ' (' + (nextNum + 1) + ')';
                nextLabelAccessibility.appendChild(nextLabelAccessibilityLabel);
                nextFieldWrap.appendChild(nextLabelAccessibility);
            }
            nextField.tabIndex = thisField.tabIndex;
            nextField.id = nameStub + nextNum;
            if (thisField.onfocus) {
                nextField.onfocus = thisField.onfocus;
            }
            if (thisField.onblur) {
                nextField.onblur = thisField.onblur;
            }
            if (thisField.onkeyup) {
                nextField.onkeyup = thisField.onkeyup;
            }
            nextField.onkeypress = function (event) {
                $coreFormInterfaces.ensureNextField2(event, nextField);
            };
            if (thisField.onchange) {
                nextField.onchange = thisField.onchange;
            }
            if (thisField.onrealchange != null) {
                nextField.onchange = thisField.onrealchange;
            }
            if (thisField.localName !== 'textarea') {
                nextField.type = thisField.type;
            }
            nextField.value = '';
            nextField.name = (thisField.name.includes('[]') ? thisField.name : (nameStub + nextNum));
            nextFieldWrap.appendChild(nextField);
            thisField.parentNode.parentNode.insertBefore(nextFieldWrap, thisField.parentNode.nextSibling);
        }
    }
}(window.$cms, window.$util, window.$dom));
