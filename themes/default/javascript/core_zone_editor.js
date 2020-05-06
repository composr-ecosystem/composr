(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.ZoneEditorScreen = ZoneEditorScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function ZoneEditorScreen() {
        ZoneEditorScreen.base(this, 'constructor', arguments);
    }

    $util.inherits(ZoneEditorScreen, $cms.View, /**@lends ZoneEditorScreen#*/{
        events: function () {
            return {
                'submit .js-form-ze-save': 'submit',
                'click .js-btn-fetch-and-submit': 'fetchAndSubmit'
            };
        },

        submit: function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled() && !e.defaultPrevented) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
        },

        fetchAndSubmit: function (e, btn) {
            var params = this.params;

            setEditedPanel('panel_left');
            setEditedPanel('panel_right');
            setEditedPanel('panel_top');
            setEditedPanel('panel_bottom');
            setEditedPanel(params.defaultZonePageName);

            var form = $dom.$id('middle-fields');
            var editFieldStore = $dom.$id('edit-field-store');
            var i, store;
            for (i = 0; i < form.elements.length; i++) {
                store = document.createElement('input');
                store.type = 'hidden';
                store.name = form.elements[i].name;
                if (form.elements[i].type === 'checkbox') {
                    store.value = form.elements[i].checked ? '1' : '0';
                } else {
                    store.value = form.elements[i].value;
                }
                editFieldStore.appendChild(store);
            }

            $dom.submit(btn.form);
        }
    });

    $cms.views.ZoneEditorPanel = ZoneEditorPanel;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function ZoneEditorPanel(params) {
        ZoneEditorPanel.base(this, 'constructor', arguments);

        this.id = params.id;

        if (params.preview !== undefined) {
            $cms.form.disablePreviewScripts($dom.$('#view-' + params.id));
        }

        if (params.comcode && params.class.includes('wysiwyg')) {
            if (window.$editing && window.$editing.wysiwygOn()) {
                $dom.$('#edit_' + params.id + '_textarea').readOnly = true;
            }
        }
    }

    $util.inherits(ZoneEditorPanel, $cms.View, /**@lends ZoneEditorPanel#*/{
        events: function () {
            return {
                'click .js-click-select-tab': 'selectTab',
                'submit .js-form-zone-editor-comcode': 'submitComcode',
                'click .js-a-toggle-wysiwyg': 'toggleWysiwyg',
                'change .js-ta-ze-comcode, .js-sel-zones-draw, .js-inp-zones-draw': 'setEditedPanel'
            };
        },

        selectTab: function (e, target) {
            var id = this.params.id,
                tab = target.dataset.jsTab;

            selectZeTab(id, tab);

            if (tab === 'view') {
                reloadPreview(id);
            }

            function selectZeTab(id, tab) {
                var tabs = ['view', 'edit', 'info', 'settings'];
                var i, element, elementh;

                for (i = 0; i < tabs.length; i++) {
                    element = $dom.$id(tabs[i] + '-' + id);
                    elementh = $dom.$id(tabs[i] + '-tab-' + id);
                    if (element) {
                        if ((tabs[i] === tab) && (tab === 'edit')) {
                            if ($cms.form.isWysiwygField($dom.$id('edit_' + id + '_textarea'))) {
                                // LEGACY Fix for Firefox
                                if (window.wysiwygEditors['edit_' + id + '_textarea'].document !== undefined) {
                                    if (window.wysiwygEditors['edit_' + id + '_textarea'].document.getBody().$) {
                                        window.wysiwygEditors['edit_' + id + '_textarea'].document.getBody().$.contentEditable = 'false';
                                        window.wysiwygEditors['edit_' + id + '_textarea'].document.getBody().$.contentEditable = 'true';
                                    }
                                }
                            }
                        }

                        if (tabs[i] === tab) {
                            $dom.fadeIn(element);
                            elementh.classList.add('tab-active');
                        } else {
                            $dom.hide(element);
                            elementh.classList.remove('tab-active');
                        }
                    }
                }
            }

            function reloadPreview(id) {
                var element = $dom.$('#view-' + id);

                var editElement = $dom.$id('edit_' + id + '_textarea');
                if (!editElement) {
                    return; // Nothing interactively edited
                }

                $dom.html(element, '<div aria-busy="true" class="ajax-loading vertical-alignment"><img width="20" height="20" src="' + $util.srl('{$IMG;,loading}') + '" /> <span>{!LOADING;^}</span></div>');

                var loadingPreviewOf = id;

                var data = '';
                data += window.$editing.getTextbox(editElement);
                var url = '{$FIND_SCRIPT_NOHTTP;,comcode_convert}?fix_bad_html=1&css=1&javascript=1&from_html=0&is_semihtml=' + ($cms.form.isWysiwygField(editElement) ? '1' : '0') + '&panel=' + (((id === 'panel_left') || (id === 'panel_right')) ? '1' : '0') + $cms.keep();
                var post = ($cms.form.isWysiwygField(editElement) ? 'data__is_wysiwyg=1&' : '') + 'data=' + encodeURIComponent(data);
                if ($cms.form.isModSecurityWorkaroundEnabled()) {
                    post = $cms.form.modSecurityWorkaroundAjax(post);
                }
                $cms.doAjaxRequest(url, reloadedPreview, post);

                function reloadedPreview(responseXml) {
                    var ajaxResult = responseXml && responseXml.querySelector('result');

                    if (!ajaxResult || !loadingPreviewOf) {
                        return;
                    }

                    var element = $dom.$('#view-' + loadingPreviewOf);
                    $dom.html(element, ajaxResult.textContent.replace(/^((\s)|(<br\s*>)|(&nbsp;))*/, '').replace(/((\s)|(<br\s*>)|(&nbsp;))*$/, ''));
                    $cms.form.disablePreviewScripts(element);
                }
            }
        },

        submitComcode: function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled() && !e.defaultPrevented) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
        },

        toggleWysiwyg: function () {
            window.$editing.toggleWysiwyg('edit_' + this.params.id + '_textarea');
        },

        setEditedPanel: function (e, field) {
            var params = this.params,
                editor = $dom.$id('edit-tab-' + params.id);

            setEditedPanel(params.id);

            if (editor) {
                if (field.localName === 'select') {
                    $dom.toggle(editor, (params.currentZone === field.value));
                } else if (field.localName === 'input') {
                    $dom.toggle(editor, (params.currentZone === field.value));
                }
            }
        }
    });

    $cms.functions.moduleAdminZonesGetFormFields = function moduleAdminZonesGetFormFields() {
        var zone = document.getElementById('new_zone');
        if (!zone) {
            zone = document.getElementById('zone');
        }
        if (zone) {
            zone.addEventListener('blur', function () {
                var title = document.getElementById('title');
                if (title.value === '') {
                    title.value = zone.value.substr(0, 1).toUpperCase() + zone.value.substring(1, zone.value.length).replace(/_/g, ' ');
                }
            });
        }
    };

    $cms.functions.moduleAdminZonesAddZone = function moduleAdminZonesAddZone() {
        var form = document.getElementById('main-form'),
            submitBtn = form.querySelector('#submit-button'),
            validValue;

        form.addEventListener('submit', function submitCheck(submitEvent) {
            var value = form.elements['zone'].value;

            if ($dom.isCancelledSubmit(submitEvent) || (value === validValue)) {
                return;
            }

            var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_zone&name=' + encodeURIComponent(value) + $cms.keep();
            submitEvent.preventDefault();

            var promise = $cms.form.doAjaxFieldTest(url).then(function (valid) {
                if (valid) {
                    validValue = value;
                }

                return valid;
            });

            $dom.awaitValidationPromiseAndResubmit(submitEvent, promise, submitBtn);
        });
    };

    function setEditedPanel(id) {
        var el, store;

        /* The editing box */

        el = $dom.$('teaxtarea#edit_' + id + '_textarea');
        if (el) {
            store = $dom.$('#store_' + id);
            if (!store) {
                store = document.createElement('textarea');
                store.name = el.name;
                store.id = 'store_' + id;
                $dom.$('#edit-field-store').appendChild(store);
            }
            store.value = window.$editing.getTextbox(el);
        }

        /* The WYSIWYG setting (not the actual HTML text value of the editor, the setting of whether WYSIWYG was used or not) */

        el = $dom.$id('edit_' + id + '_textarea__is_wysiwyg');
        if (el) {
            store = $dom.$id('wysiwyg_store_' + id);
            if (!store) {
                store = document.createElement('textarea');
                store.id = 'wysiwyg_store_' + id;
                store.name = el.name;
                $dom.$id('edit-field-store').appendChild(store);
            }
            store.value = el.value;
        }

        /* The redirect setting */

        el = $dom.$('select#redirect_' + id);
        if (el) {
            store = $dom.$id('redirects_store_' + id);
            if (!store) {
                store = document.createElement('textarea');
                store.name = el.name;
                store.id = 'redirects_store_' + id;
                $dom.$id('edit-field-store').appendChild(store);
            }
            store.value = el.value;
        }
    }
}(window.$cms, window.$util, window.$dom));
