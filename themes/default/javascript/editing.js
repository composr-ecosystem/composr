/* Form editing code (general, may be used on many different kinds of form) */
(function ($cms, $util, $dom) {
    'use strict';

    var $editing = window.$editing = {};

    $editing.wysiwygOn = wysiwygOn;
    $editing.toggleWysiwyg = toggleWysiwyg;
    $editing.wysiwygSetReadonly = wysiwygSetReadonly;
    $editing.loadHtmlEdit = loadHtmlEdit;
    $editing.doEmoticon = doEmoticon;
    $editing.doAttachment = doAttachment;
    $editing.getTextbox = getTextbox;
    $editing.setTextbox = setTextbox;
    $editing.insertTextbox = insertTextbox;
    $editing.insertTextboxWrapping = insertTextboxWrapping;
    $editing.getSelectedText = getSelectedText;
    $editing.showUploadSyndicationOptions = showUploadSyndicationOptions;

    $cms.functions.comcodeAddTryForSpecialComcodeTagSpecificContentsUi = function () {
        // If we select an image we want to have good defaults for an image, but only if the defaults weren't already changed
        document.getElementById('tag_contents__b').onchange = function () {
            var ext = this.value.substring(this.value.indexOf('.') + 1);
            var isImage = (',' + $cms.configOption('valid_images')).indexOf(',' + ext + ',') !== -1;
            if (isImage) {
                var framed = document.getElementById('framed');
                var wysiwygEditable = document.getElementById('wysiwyg_editable');
                var thumb = document.getElementById('thumb');

                if (framed.defaultChecked === framed.checked && wysiwygEditable.defaultChecked === wysiwygEditable.checked && thumb.defaultChecked === thumb.checked) {
                    framed.checked = false;
                    wysiwygEditable.checked = true;
                    thumb.checked = false;
                }
            }
        };
    };

    // ===========
    // HTML EDITOR
    // ===========
    function wysiwygOn() {
        if ($cms.isMobile()) {
            return false;
        }

        var cookie = $cms.readCookie('use_wysiwyg');
        switch ($cms.configOption('wysiwyg')) {
            case -1: // Off by default
                return (cookie === '1');
            case 0: // Off
                return false;
            case 1: // On by default
                return (cookie !== '0');
        }
        return false; // Should never get here
    }

    function toggleWysiwyg(name) {
        if (!$cms.configOption('wysiwyg')) {
            $cms.ui.alert('{!comcode:TOGGLE_WYSIWYG_ERROR;^}');
            return;
        }

        var isWysiwygOn = $editing.wysiwygOn();
        if (isWysiwygOn) {
            if ($cms.readCookie('use_wysiwyg') === '-1') {
                _toggleWysiwyg(name);
            } else {
                $cms.ui.generateQuestionUi(
                    '{!comcode:WHETHER_SAVE_WYSIWYG_SELECTION;^}',
                    {
                        'buttons/cancel': '{!INPUTSYSTEM_CANCEL;^}',
                        'buttons/clear': '{!javascript:WYSIWYG_DISABLE_ONCE;^}',
                        //'buttons/no': '{!javascript:WYSIWYG_DISABLE_ONCE_AND_DONT_ASK;^}', Too confusing, re-enable if you want it
                        'buttons/yes': '{!javascript:WYSIWYG_DISABLE_ALWAYS;^}'
                    },
                    '{!comcode:DISABLE_WYSIWYG;^}',
                    '{!javascript:DISCARD_WYSIWYG_CHANGES;^}',
                    null, 600, 140
                ).then(function (savingCookies) {
                    if (!savingCookies) {
                        return;
                    }

                    if (savingCookies.toLowerCase() === '{!javascript:WYSIWYG_DISABLE_ONCE;^}'.toLowerCase()) {
                        _toggleWysiwyg(name);
                    }

                    if (savingCookies.toLowerCase() === '{!javascript:WYSIWYG_DISABLE_ONCE_AND_DONT_ASK;^}'.toLowerCase()) {
                        _toggleWysiwyg(name);
                        $cms.setCookie('use_wysiwyg', '-1', 3000);
                    }

                    if (savingCookies.toLowerCase() === '{!javascript:WYSIWYG_DISABLE_ALWAYS;^}'.toLowerCase()) {
                        _toggleWysiwyg(name);
                        $cms.setCookie('use_wysiwyg', '0', 3000);
                    }
                });
            }
            return;
        }

        _toggleWysiwyg(name);

        if ($cms.readCookie('use_wysiwyg') !== '-1') {
            $cms.setCookie('use_wysiwyg', '1', 3000);
        }

        function _toggleWysiwyg() {
            var isWysiwygOn = $editing.wysiwygOn(),
                forms = document.getElementsByTagName('form'),
                so = document.getElementById('post-special-options--' + name),
                so2 = document.getElementById('post-special-options2--' + name);

            if (isWysiwygOn) {
                // Find if the WYSIWYG has anything in it - if not, discard
                var allEmpty = true,
                    myregexp = new RegExp(/((\s)|(<p\d*\/>)|(<\/p>)|(<p>)|(&nbsp;)|(<br[^>]*>))*/),
                    id;

                for (var fid = 0; fid < forms.length; fid++) {
                    for (var counter = 0; counter < forms[fid].elements.length; counter++) {
                        id = forms[fid].elements[counter].id;
                        if (window.wysiwygEditors[id] !== undefined) {
                            if (window.wysiwygEditors[id].getData().replace(myregexp, '') !== '') {
                                allEmpty = false;
                            }
                        }
                    }
                }

                if (allEmpty) {
                    disableWysiwyg(forms, so, so2, true);
                } else if ((window.wysiwygOriginalComcode[id] === undefined) || window.wysiwygOriginalComcode[id].includes('&#8203;') || window.wysiwygOriginalComcode[id].includes('cms-keep')) {
                    disableWysiwyg(forms, so, so2, false);
                } else {
                    $cms.ui.generateQuestionUi(
                        '{!javascript:DISCARD_WYSIWYG_CHANGES_NICE;^}',
                        {
                            'buttons/cancel': '{!INPUTSYSTEM_CANCEL;^}',
                            'buttons/convert': '{!javascript:DISCARD_WYSIWYG_CHANGES_LINE_CONVERT;^}',
                            'buttons/no': '{!javascript:DISCARD_WYSIWYG_CHANGES_LINE;^}'
                        },
                        '{!comcode:DISABLE_WYSIWYG;^}',
                        '{!javascript:DISCARD_WYSIWYG_CHANGES;^}'
                    ).then(function (prompt) {
                        if (!prompt || (prompt.toLowerCase() === '{!INPUTSYSTEM_CANCEL;^}'.toLowerCase())) {
                            if ($cms.readCookie('use_wysiwyg') === '0') {
                                $cms.setCookie('use_wysiwyg', '1', 3000);
                            }
                            return;
                        }

                        var discard = (prompt.toLowerCase() === '{!javascript:DISCARD_WYSIWYG_CHANGES_LINE;^}'.toLowerCase());
                        disableWysiwyg(forms, so, so2, discard);
                    });
                }
            } else {
                enableWysiwyg(forms, so, so2);
            }
        }

        function enableWysiwyg(forms) {
            forms = window.arrVal(forms);

            $editing.wysiwygOn = function () {
                return true;
            };

            var promiseCalls = [];
            forms.forEach(function (form) {
                promiseCalls.push(function () {
                    return $editing.loadHtmlEdit(form, true);
                });
            });

            return $util.promiseSequence(promiseCalls);
        }

        function disableWysiwyg(forms, so, so2, discard) {
            var promiseCalls = [];

            for (var fid = 0; fid < forms.length; fid++) {
                var form = forms[fid];

                for (var counter = 0; counter < form.elements.length; counter++) {
                    var textarea = form.elements[counter];

                    if (window.wysiwygEditors[textarea.id] === undefined) {
                        continue;
                    }

                    promiseCalls.push((function (textarea) {
                        return function () {
                            return disableWysiwygTextarea(textarea, discard);
                        };
                    }(textarea)));
                }
            }

            return $util.promiseSequence(promiseCalls).then(function () {
                if (so) {
                    $dom.show(so);
                }
                if (so2) {
                    $dom.hide(so2);
                }

                $editing.wysiwygOn = function () {
                    return false;
                };
            });
        }

        function disableWysiwygTextarea(textarea, discard) {
            return new Promise(function (resolvePromise) {
                var id = textarea.id;

                // Mark as non-WYSIWYG
                document.getElementById(id + '__is_wysiwyg').value = '0';
                textarea.style.display = 'block';
                textarea.style.visibility = 'visible';
                textarea.disabled = false;
                textarea.readOnly = false;

                if (typeof window.rebuildAttachmentButtonForNext === 'function') { // NB: The window.rebuildAttachmentButtonForNext type check is important, don't remove.
                    window.rebuildAttachmentButtonForNext();
                }

                // Unload editor
                var wysiwygData = window.wysiwygEditors[id].getData();
                try {
                    window.wysiwygEditors[id].destroy();
                } catch (ignore) {}

                delete window.wysiwygEditors[id];

                // Comcode conversion
                if (discard && window.wysiwygOriginalComcode[id]) {
                    textarea.value = window.wysiwygOriginalComcode[id];
                    postWysiwygDisable(textarea);
                    return resolvePromise();
                }

                var url = $util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,comcode_convert}?from_html=1&is_semihtml=1' + $cms.keep()));
                if ($cms.getPageName() === 'topics') {
                    url += '&forum_db=1';
                }
                var post = 'data=' + encodeURIComponent(wysiwygData.replace(new RegExp(String.fromCharCode(8203), 'g'), ''));
                if ($cms.form.isModSecurityWorkaroundEnabled()) {
                    post = $cms.form.modSecurityWorkaroundAjax(post);
                }
                $cms.doAjaxRequest(url, null, post).then(function (xhr) {
                    if (!xhr.responseXML || !xhr.responseXML.querySelector('result')) {
                        textarea.value = '[semihtml]' + wysiwygData + '[/semihtml]';
                    } else {
                        var result = xhr.responseXML.querySelector('result');
                        textarea.value = result.textContent.replace(/\s*$/, '');
                    }
                    if (textarea.value.includes('{\$,page hint: no_wysiwyg}') && (textarea.value !== '')) { // eslint-disable-line no-useless-escape
                        textarea.value += '{\$,page hint: no_wysiwyg}'; // eslint-disable-line no-useless-escape
                    }

                    postWysiwygDisable(textarea);
                    resolvePromise();
                });
            });

            function postWysiwygDisable(textarea) {
                if (document.getElementById('toggle-wysiwyg-' + textarea.id)) {
                    $dom.html('#toggle-wysiwyg-' + textarea.id, '<img width="16" height="16" src="' + $util.srl('{$IMG*;^,icons/editor/wysiwyg_on}') + '" alt="{!comcode:ENABLE_WYSIWYG;^}" title="{!comcode:ENABLE_WYSIWYG;^}" class="vertical-alignment" />');
                }

                try { // Unload editor
                    window.wysiwygEditors[textarea.id].destroy();
                } catch (ignore) {}
            }
        }
    }

    window.wysiwygReadonlyTimer || (window.wysiwygReadonlyTimer = {});
    function wysiwygSetReadonly(name, readonly) {
        if (window.wysiwygEditors[name] === undefined) {
            return;
        }

        var editor = window.wysiwygEditors[name];
        if (editor.document && editor.document.$ && editor.document.$.body) {
            editor.document.$.body.readOnly = readonly;
            editor.document.$.body.contentEditable = !readonly;
            editor.document.$.body.designMode = readonly ? 'off' : 'on';
        }

        // In case it sticks as read only we need a timer to put it back. But only if not already back.
        if (window.wysiwygReadonlyTimer[name]) {
            clearTimeout(window.wysiwygReadonlyTimer[name]);
            window.wysiwygReadonlyTimer[name] = null;
        }
        if (readonly) {
            window.wysiwygReadonlyTimer[name] = setTimeout(function () {
                $editing.wysiwygSetReadonly(name, false);
            }, 5000);
        }
    }

    // Initialising the HTML editor if requested later (i.e. toggling it to on)
    window.wysiwygEditors || (window.wysiwygEditors = {});
    window.wysiwygOriginalComcode || (window.wysiwygOriginalComcode = {});

    /**
     * @param { HTMLFormElement } postingForm
     * @param {boolean} ajaxCopy
     * @return { Promise }
     */
    function loadHtmlEdit(postingForm, ajaxCopy) {
        if (!postingForm.method || (postingForm.method.toLowerCase() !== 'post')) {
            return Promise.resolve();
        }

        if (!postingForm.elements['http_referer']) {
            var httpReferer = document.createElement('input');
            httpReferer.name = 'http_referer';
            httpReferer.value = window.location.href;
            httpReferer.type = 'hidden';
            postingForm.appendChild(httpReferer);
        }

        if (!window.CKEDITOR || !$cms.configOption('wysiwyg') || !$editing.wysiwygOn()) {
            return Promise.resolve();
        }

        var promiseCalls = [];
        arrVal(postingForm.elements).forEach(function (el) {
            if ((el.type === 'textarea') && el.classList.contains('wysiwyg')) {
                promiseCalls.push(function () {
                    return loadHtmlForTextarea(postingForm, el, ajaxCopy);
                });
            }
        });

        return $util.promiseSequence(promiseCalls);

        function loadHtmlForTextarea(postingForm, textarea, ajaxCopy) {
            return new Promise(function (resolvePromise) {
                var id = textarea.id, indicator;

                var so = document.getElementById('post-special-options--' + id),
                    so2 = document.getElementById('post-special-options2--' + id);

                if (!postingForm.elements['post'] || postingForm.elements['post'].className.includes('wysiwyg')) {
                    if (so) {
                        $dom.hide(so);
                    }
                    if (so2) {
                        $dom.show(so2);
                    }
                }

                if (document.getElementById(id + '__is_wysiwyg')) {
                    indicator = document.getElementById(id + '__is_wysiwyg');
                } else {
                    indicator = document.createElement('input');
                    indicator.type = 'hidden';
                    indicator.id = textarea.id + '__is_wysiwyg';
                    indicator.name = textarea.name + '__is_wysiwyg';
                    postingForm.appendChild(indicator);
                }
                indicator.value = '1';

                if (document.getElementById('toggle-wysiwyg-' + id)) {
                    $dom.html(document.getElementById('toggle-wysiwyg-' + id), '<img width="16" height="16" src="' + $util.srl('{$IMG*;^,icons/editor/wysiwyg_off}') + '" alt="{!comcode:DISABLE_WYSIWYG;^}" title="{!comcode:DISABLE_WYSIWYG;^}" class="vertical-alignment" />');
                }

                window.wysiwygOriginalComcode[id] = textarea.value;
                if (!ajaxCopy) {
                    if ((postingForm.elements[id + '_parsed'] !== undefined) && (postingForm.elements[id + '_parsed'].value !== '') && ((textarea.defaultValue === ''/*LEGACY IE bug*/) || (textarea.defaultValue === textarea.value))) {// The extra conditionals are for if back button used
                        textarea.value = postingForm.elements[id + '_parsed'].value;
                    }

                    setTimeout(function () {
                        wysiwygEditorInitFor(textarea);
                    }, 1000);
                    return resolvePromise();
                }

                var url = $util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,comcode_convert}?semihtml=1&from_html=0' + $cms.keep()));
                if ($cms.getPageName() === 'topics') {
                    url += '&forum_db=1';
                }

                var post = 'data=' + encodeURIComponent(textarea.value.replace(new RegExp(String.fromCharCode(8203), 'g'), '').replace('{' + '$,page hint: no_wysiwyg}', ''));
                if ($cms.form.isModSecurityWorkaroundEnabled()) {
                    post = $cms.form.modSecurityWorkaroundAjax(post);
                }
                $cms.doAjaxRequest(url, null, post).then(function (xhr) {
                    if (!xhr.responseXML) {
                        textarea.value = '';
                    } else {
                        var result = xhr.responseXML.querySelector('result');
                        textarea.value = result ? result.textContent : '';
                    }

                    setTimeout(function () {
                        wysiwygEditorInitFor(textarea);
                    }, 1000);

                    resolvePromise();
                });
            });
        }

        function wysiwygEditorInitFor(element) {
            // Fiddly procedure to find our colour
            var testDiv = document.createElement('div');
            document.body.appendChild(testDiv);
            testDiv.className = 'wysiwyg-toolbar-color-finder';
            var matches,
                wysiwygColor = window.getComputedStyle(testDiv).getPropertyValue('color'); // NB: Used by _wysiwyg_settings.js
            testDiv.parentNode.removeChild(testDiv);
            matches = wysiwygColor.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/, matches);
            if (matches) {
                wysiwygColor = '#';
                var hex;
                hex = String(parseInt(matches[1]).toString(16));
                if (hex.length === 1) {
                    hex = '0' + hex;
                }
                wysiwygColor += hex;
                hex = String(parseInt(matches[2]).toString(16));
                if (hex.length === 1) {
                    hex = '0' + hex;
                }
                wysiwygColor += hex;
                hex = String(parseInt(matches[3]).toString(16));
                if (hex.length === 1) {
                    hex = '0' + hex;
                }
                wysiwygColor += hex;
            }
            // CKEditor doesn't allow low saturation, so raise up if we need to
            matches = wysiwygColor.match(/^#([0-4])(.)([0-4])(.)([0-4])(.)$/);
            if (matches) {
                wysiwygColor = '#';
                wysiwygColor += (parseInt(matches[1]) + 4) + matches[2];
                wysiwygColor += (parseInt(matches[3]) + 4) + matches[4];
                wysiwygColor += (parseInt(matches[5]) + 4) + matches[6];
            }

            // Composr CSS needs passing to CKEditor
            var pageStylesheets = [];
            var linkedSheets = document.getElementsByTagName('link');
            for (var counter = 0; counter < linkedSheets.length; counter++) {
                if (linkedSheets[counter].rel === 'stylesheet') {
                    pageStylesheets.push(linkedSheets[counter].href);
                }
            }

            var editorSettings = {};
            /*{+START,INCLUDE,_wysiwyg_settings,.js,javascript}{+END}*/

            if (window.CKEDITOR.instances[element.id]) {
                // Workaround "The instance "xxx" already exists" error in Google Chrome
                delete window.CKEDITOR.instances[element.id];
            }
            var editor = window.CKEDITOR.replace(element.id, editorSettings);
            if (!editor) { // Not supported on this platform
                return;
            }
            window.wysiwygEditors[element.id] = editor;

            element.parentNode.className += ' ' + editorSettings.skin; // Used for us to target per-skin CSS

            // CSS to run inside the CKEditor frame
            linkedSheets = document.getElementsByTagName('style');
            var css = '';
            for (var counter2 = 0; counter2 < linkedSheets.length; counter2++) {
                css += $dom.html(linkedSheets[counter2]);
            }
            window.CKEDITOR.addCss(css);

            // Change some CKEditor defaults
            window.CKEDITOR.on('dialogDefinition', function (ev) {
                var dialogName = ev.data.name;
                var dialogDefinition = ev.data.definition;

                if (dialogName === 'table') {
                    var info = dialogDefinition.getContents('info');

                    info.get('txtWidth')['default'] = '100%';
                    info.get('txtBorder')['default'] = '0';
                    info.get('txtBorder')['default'] = '0';
                    info.get('txtCellSpace')['default'] = '0';
                    info.get('txtCellPad')['default'] = '0';
                }
            });
            if (document.getElementById('js-attachment-store--' + element.id)) {
                window.lang_PREFER_CMS_ATTACHMENTS = '{!javascript:PREFER_CMS_ATTACHMENTS;^}'; // eslint-disable-line camelcase
                window.lang_INPUTSYSTEM_RAW_IMAGE = '{!javascript:INPUTSYSTEM_RAW_IMAGE;^}'; // eslint-disable-line camelcase
                window.lang_INPUTSYSTEM_ATTACHMENT = '{!javascript:INPUTSYSTEM_ATTACHMENT;^}'; // eslint-disable-line camelcase
                window.lang_INPUTSYSTEM_MEDIA = '{!javascript:INPUTSYSTEM_MEDIA;^}'; // eslint-disable-line camelcase
                window.lang_IMAGE_EDITING_TYPE = '{!javascript:IMAGE_EDITING_TYPE;^}'; // eslint-disable-line camelcase
                window.lang_IMAGE_EDITING_QUESTION = '{!javascript:IMAGE_EDITING_QUESTION;^}'; // eslint-disable-line camelcase
            }
            window.lang_SPELLCHECKER_ENABLED = '{!javascript:SPELLCHECKER_ENABLED;^}'; // eslint-disable-line camelcase
            window.lang_SPELLCHECKER_DISABLED = '{!javascript:SPELLCHECKER_DISABLED;^}'; // eslint-disable-line camelcase
            window.lang_SPELLCHECKER_TOGGLE = '{!javascript:SPELLCHECKER_TOGGLE;^}'; // eslint-disable-line camelcase
            window.lang_SPELLCHECKER_LABEL = '{!javascript:SPELLCHECKER_LABEL;^}'; // eslint-disable-line camelcase
            window.lang_NO_IMAGE_PASTE_SAFARI = '{!javascript:NO_IMAGE_PASTE_SAFARI;^}'; // eslint-disable-line camelcase

            // Mainly used by autosaving, but also sometimes CKEditor seems to not refresh the textarea (e.g. for one user's site when pressing delete key on an image)
            var sync = function (event) {
                element.value = editor.getData();
                if (element.externalOnKeyPress !== undefined) {
                    element.externalOnKeyPress(event, element);
                }
            };
            editor.on('change', sync);
            editor.on('blur', sync); // 'change' can be buggy, e.g. when pasting, or deleting full editor contents
            editor.on('mode', function () {
                var ta = editor.container.$.querySelector('textarea');
                if (ta != null) {
                    ta.onchange = sync; // The source view doesn't fire the 'change' event and we don't want to use the 'key' event
                }
            });

            editor.on('instanceReady', function () {
                editor.setReadOnly(false); // Workaround for CKEditor bug found in 4.5.6, where it started sometimes without contentEditable=true

                if (window.$jqueryAutocomplete !== undefined) {
                    window.$jqueryAutocomplete.setUpComcodeAutocomplete(element.id);
                }

                // Instant preview of Comcode
                findTagsInEditor(editor, element);
            });
            setInterval(function () {
                if ($cms.form.isWysiwygField(element)) {
                    findTagsInEditor(editor, element);
                }
            }, 1000);

            // Weird issues in Chrome cutting+pasting blocks etc
            editor.on('paste', function (event) {
                if (event.data.html) {
                    event.data.html = event.data.html.replace(/<meta charset="utf-8">/g, '');
                    event.data.html = event.data.html.replace(/<br class="Apple-interchange-newline">/g, '<br>');
                    event.data.html = event.data.html.replace(/<div style="text-align: center;"><font class="Apple-style-span" face="'Lucida Grande'"><span class="Apple-style-span" style="font-size: 11px; white-space: pre;"><br><\/span><\/font><\/div>$/, '<br><br>');
                }
            });

            // Monitor pasting, for anti-spam reasons
            editor.on('paste', function (event) {
                if (event.data.dataValue && event.data.dataValue.length > $cms.configOption('spam_heuristic_pasting')) {
                    $cms.setPostDataFlag('paste');
                }
            });

            // Allow drag and drop uploading
            editor.on('contentDom', function () {
                editor.document.on('dragover', function (e) {
                    window.$plupload.html5UploadEventDragOver(e.data.$);
                });

                editor.document.on('drop', function (e) {
                    window.$plupload.html5UploadEventDrop(e.data.$, element, element.id);
                });
            });

            return editor;
        }
    }

    function findTagsInEditor(editor) {
        if (!editor.document || !editor.document.$ || !editor.document.$.body) {
            return;
        }

        var comcodes = editor.document.$.body.querySelectorAll('.cms-keep-ui-controlled');

        arrVal(comcodes).forEach(function (comcode) {
            if (comcode.onmouseout) {
                return;
            }
            comcode.onmouseout = function () {
                $cms.ui.deactivateTooltip(this);
            };
            comcode.onmousemove = function (event) {
                var eventCopy = {};
                if (event) {
                    if (event.pageX) {
                        eventCopy.pageX = 3000;
                    }
                    if (event.clientX) {
                        eventCopy.clientX = 3000;
                    }
                    if (event.pageY) {
                        eventCopy.pageY = 3000;
                    }
                    if (event.clientY) {
                        eventCopy.clientY = 3000;
                    }
                    $cms.ui.repositionTooltip(this, eventCopy);
                }
            };
            comcode.onmousedown = function (event) {
                if (event.altKey) {
                    // Mouse cursor to start
                    var range = document.selection.getRanges()[0];
                    range.startOffset = 0;
                    range.endOffset = 0;
                    range.select();
                    document.selection.selectRanges([range]);
                }
            };

            if (comcode.localName === 'input' || comcode.localName === 'button') {
                comcode.readOnly = true;
                comcode.contentEditable = true; // Undoes what ckeditor sets. Fixes weirdness with copy and paste in Chrome (adding extra block on end)
                comcode.ondblclick = function (e) {
                    e.preventDefault();

                    if (this.onmouseout) {
                        this.onmouseout();
                    }
                    var fieldName = editor.name;
                    if (this.id === '') {
                        this.id = 'comcode_tag_' + Math.round(Math.random() * 10000000);
                    }
                    var tagType = this.title.replace(/^\[/, '').replace(/[= \]](.|\n)*$/, ''),
                        url;

                    if (tagType === 'block') {
                        var blockName = this.title.replace(/\[\/block\]$/, '').replace(/^(.|\s)*\]/, '');
                        url = '{$FIND_SCRIPT_NOHTTP;,block_helper}?type=step2&block=' + encodeURIComponent(blockName) + '&field_name=' + fieldName + '&parse_defaults=' + encodeURIComponent(this.title) + '&save_to_id=' + encodeURIComponent(this.id) + $cms.keep();
                        url = url + '&block_type=' + (((fieldName.indexOf('edit_panel_') === -1) && (window.location.href.indexOf(':panel_') === -1)) ? 'main' : 'side');
                        $cms.ui.open($util.rel($cms.maintainThemeInLink(url)), '', 'width=750,height=auto,status=no,resizable=yes,scrollbars=yes', null, '{!INPUTSYSTEM_CANCEL;^}');
                    } else {
                        url = '{$FIND_SCRIPT_NOHTTP;,comcode_helper}?type=step2&tag=' + encodeURIComponent(tagType) + '&field_name=' + fieldName + '&parse_defaults=' + encodeURIComponent(this.title) + '&save_to_id=' + encodeURIComponent(this.id) + $cms.keep();
                        $cms.ui.open($util.rel($cms.maintainThemeInLink(url)), '', 'width=750,height=auto,status=no,resizable=yes,scrollbars=yes', null, '{!INPUTSYSTEM_CANCEL;^}');
                    }
                };
            }

            comcode.onmouseover = function (event) { // Shows preview
                var tagText = '';
                if (this.localName === 'input') {
                    tagText = this.title;
                } else {
                    tagText = $dom.html(this);
                }

                this.style.cursor = 'pointer';

                var eventCopy = {};
                if (event) {
                    if (event.pageX) {
                        eventCopy.pageX = 3000;
                    }
                    if (event.clientX) {
                        eventCopy.clientX = 3000;
                    }
                    if (event.pageY) {
                        eventCopy.pageY = 3000;
                    }
                    if (event.clientY) {
                        eventCopy.clientY = 3000;
                    }

                    if ((this.renderedTooltip === undefined && !this.isOver) || (this.tagText !== tagText)) {
                        this.tagText = tagText;
                        this.isOver = true;

                        var url = $util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,comcode_convert}?css=1&javascript=1&box_title={!PREVIEW&;^}' + $cms.keep()));
                        if ($cms.getPageName() === 'topics') {
                            url += '&forum_db=1';
                        }

                        var self = this;
                        $cms.doAjaxRequest(url, [function (responseXml) {
                            var ajaxResult = responseXml && responseXml.querySelector('result');

                            if (ajaxResult) {
                                var tmpRendered = ajaxResult.textContent;
                                if (tmpRendered.indexOf('{!CCP_ERROR_STUB;^}') === -1) {
                                    self.renderedTooltip = tmpRendered;
                                }
                            }
                            if (self.renderedTooltip !== undefined) {
                                if (self.isOver) {
                                    $cms.ui.activateTooltip(self, eventCopy, self.renderedTooltip, 'auto', null, null, false, 0);
                                }
                            }
                        }], 'data=' + encodeURIComponent('[semihtml]' + tagText.replace(/<\/?span[^>]*>/gi, '')).substr(0, 1000).replace(new RegExp(String.fromCharCode(8203), 'g'), '') + '[/semihtml]');
                    } else if (this.renderedTooltip !== undefined) {
                        $cms.ui.activateTooltip(this, eventCopy, this.renderedTooltip, '400px', null, null, false, 0);
                    }
                }
            };
        });
    }

    // ============
    // BOTH EDITORS
    // ============

    function doEmoticon(fieldName, callerEl, isOpener) {
        var element, title, text;

        isOpener = Boolean(isOpener);

        if (isOpener) {
            element = $cms.getMainCmsWindow().document.getElementById(fieldName);
            if (!element) { // If it is really actually cascading pop-ups
                element = window.opener.document.getElementById(fieldName);
            }
        } else {
            element = document.getElementById(fieldName);
        }

        if (!element) {
            $util.fatal('$editing.doEmoticon(): Element not found "#' + fieldName + '"');
            return;
        }

        title = callerEl.title;
        if (title === '') {
            // Might be on image inside link instead
            title = callerEl.querySelector('img').alt;
        }
        title = title.replace(/^.*: /, '');

        text = ' ' + title + ' ';

        if (isOpener) {
            return insertTextboxOpener(element, text, true, $dom.html(callerEl));
        } else {
            return $editing.insertTextbox(element, text, true, $dom.html(callerEl));
        }
    }

    /**
     * Insert attachment Comcode
     * @param fieldName
     * @param id
     * @param description
     * @return {Promise}
     */
    function doAttachment(fieldName, id, description) {
        if (!$cms.getMainCmsWindow().wysiwygEditors) {
            return Promise.resolve();
        }

        description = strVal(description);

        var element = $cms.getMainCmsWindow().document.getElementById(fieldName);
        var comcode = '\n\n[attachment description="' + $cms.filter.comcode(description) + '"]' + id + '[/attachment]';

        return insertTextboxOpener(element, comcode);
    }

    /**
     * @param { Element } element
     * @return {string}
     */
    function getTextbox(element) {
        if ($cms.form.isWysiwygField(element)) {
            var ret = strVal(window.wysiwygEditors[element.id].getData());
            if ((ret === '\n') || (ret === '<br />')) {
                ret = '';
            }
            return ret;
        }
        return element.value;
    }

    /**
     * @param { Element } element
     * @param {string} text
     * @param {string} [html]
     */
    function setTextbox(element, text, html) {
        if ($cms.form.isWysiwygField(element)) {
            if (html === undefined) {
                html = $cms.filter.html(text).replace(new RegExp('\\\\n', 'gi'), '<br />');
            }

            window.wysiwygEditors[element.id].document.getBody().setHtml(html);
            window.wysiwygEditors[element.id].updateElement();

            setTimeout(function () {
                findTagsInEditor(window.wysiwygEditors[element.id], element);
            }, 100);
        } else {
            element.value = text;
        }
    }

    /**
     * Insert some text, with WYSIWYG support...
     * (Use $editing.insertTextboxWrapping to wrap Comcode tags around a selection)
     *
     * @param { Element } element - non-WYSIWYG element
     * @param {string} text - text to insert (non-HTML)
     * @param {boolean} [isPlainInsert] - Set to true if we are doing a simple insert, not inserting complex Comcode that needs to have editing representation.
     * @param {string} [html] - HTML to insert (if not passed then 'text' will be escaped)
     * @returns { Promise }
     */
    function insertTextbox(element, text, isPlainInsert, html) {
        text = strVal(text);
        isPlainInsert = boolVal(isPlainInsert);
        html = strVal(html);

        if ($cms.form.isWysiwygField(element)) {
            return insertTextboxWysiwyg(element, text, isPlainInsert, html);
        } else {
            return insertTextboxVanilla(element, text);
        }

        function insertTextboxVanilla(element, text) {
            var from = element.value.length,
                to, start, end;

            element.focus();

            if (element.selectionEnd !== undefined) {
                from = element.selectionStart;
                to = element.selectionEnd;
                start = element.value.substring(0, from);
                end = element.value.substring(to, element.value.length);

                element.value = start + element.value.substring(from, to) + text + end;
                setSelectionRange(element, from + text.length, from + text.length);
            } else {
                // :(
                from += 2;
                element.value += text;
                setSelectionRange(element, from + text.length, from + text.length);
            }

            return Promise.resolve();
        }

        function insertTextboxWysiwyg(element, text, isPlainInsert, html) {
            return new Promise(function (resolvePromise) {
                var editor = window.wysiwygEditors[element.id],
                    insert = '';

                if (isPlainInsert) {
                    insert = getWYSISWYGSelectedHtml(editor) + (html ? html : $cms.filter.html(text).replace(new RegExp('\\\\n', 'gi'), '<br />'));

                    _insertTextboxWysiwyg(element, editor, insert);
                    return resolvePromise();
                }

                var url = $util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,comcode_convert}?semihtml=1&is_semihtml=1&lax_comcode=1' + $cms.keep()));
                if ($cms.getPageName() === 'topics') {
                    url += '&forum_db=1';
                }

                var post = 'data=' + encodeURIComponent(text.replace(new RegExp(String.fromCharCode(8203), 'g'), ''));
                if ($cms.form.isModSecurityWorkaroundEnabled()) {
                    post = $cms.form.modSecurityWorkaroundAjax(post);
                }

                $cms.doAjaxRequest(url, null, post).then(function (xhr) {
                    var responseXML = xhr.responseXML;
                    if (responseXML && (responseXML.querySelector('result'))) {
                        var result = responseXML.querySelector('result');
                        insert = result.textContent.replace(/\s*$/, '');
                    }

                    _insertTextboxWysiwyg(element, editor, insert);
                    resolvePromise();
                });
            });
        }

        function _insertTextboxWysiwyg(element, editor, insert) {
            var before = editor.getData(),
                after;

            try {
                editor.focus(); // Needed on some browsers
                getWYSISWYGSelectedHtml(editor);

                if (editor.getSelection() && (editor.getSelection().getStartElement().getName() === 'kbd')) {// Danger Danger - don't want to insert into another Comcode tag. Put it after. They can cut+paste back if they need.
                    editor.document.getBody().appendHtml(insert);
                } else {
                    // Ideally we use insertElement, as insertHtml may break up the parent tag (we want it to nest nicely)
                    var elementForInserting = window.CKEDITOR.dom.element.createFromHtml(insert);
                    if (elementForInserting.getName === undefined) {
                        editor.insertHtml(insert);
                    } else {
                        editor.insertElement(elementForInserting);
                    }
                }

                after = editor.getData();
                if (after === before) {
                    throw new Error('Failed to insert');
                }

                findTagsInEditor(editor, element);
            } catch (e) { // Sometimes happens on Firefox in Windows, appending is a bit tamer (e.g. you cannot insert if you have the start of a h1 at cursor)
                after = editor.getData();
                if (after === before) { // Could have just been a window.scrollBy pop-up-blocker exception, so only do this if the op definitely failed
                    editor.document.getBody().appendHtml(insert);
                }
            }

            editor.updateElement();
        }
    }
    /**
     * @param element
     * @param text
     * @param isPlainInsert
     * @param html
     * @return { Promise }
     */
    function insertTextboxOpener(element, text, isPlainInsert, html) {
        return $cms.getMainCmsWindow().$editing.insertTextbox(element, text, isPlainInsert, html);
    }

    /**
     * @param element
     * @return {string}
     */
    function getSelectedText(element) {
        if ($cms.form.isWysiwygField(element)) {
            var editor = window.wysiwygEditors[element.id];
            return getWYSISWYGSelectedHtml(editor);
        }
        return getTextareaSelectedText(element);
    }

    /**
     * Get selected HTML from CKEditor
     * @param editor
     * @return {string}
     */
    function getWYSISWYGSelectedHtml(editor) {
        var mySelection = editor.getSelection();
        if (!mySelection || mySelection.getType() === window.CKEDITOR.SELECTION_NONE) {
            return '';
        }

        var element = mySelection.getSelectedElement();
        if (element) {
            return element.$.outerHTML;
        }

        var selectedText = '';
        if (mySelection.getNative()) {
            try {
                selectedText = $dom.html(mySelection.getNative().getRangeAt(0).cloneContents());
            } catch (e) {}
        }
        return selectedText;
    }

    /**
     * @param element
     * @return {string}
     */
    function getTextareaSelectedText(element) {
        if (element.selectionEnd !== undefined) {
            var from = element.selectionStart,
                to = element.selectionEnd;

            return element.value.substring(from, to);
        }

        return '';
    }

    /**
     * Insert into the editor such as to *wrap* the current selection with something new (typically a new Comcode tag)
     * @param element
     * @param beforeWrapTag
     * @param afterWrapTag
     * @return { Promise }
     */
    function insertTextboxWrapping(element, beforeWrapTag, afterWrapTag) {
        beforeWrapTag = strVal(beforeWrapTag);
        afterWrapTag = strVal(afterWrapTag);

        if (afterWrapTag === '') {
            afterWrapTag = '[/' + beforeWrapTag + ']';
            beforeWrapTag = '[' + beforeWrapTag + ']';
        }

        if ($cms.form.isWysiwygField(element)) {
            return insertTextboxWrappingWysiwyg(element, beforeWrapTag, afterWrapTag);
        } else {
            return insertTextboxWrappingVanilla(element, beforeWrapTag, afterWrapTag);
        }

        function insertTextboxWrappingVanilla(element, beforeWrapTag, afterWrapTag) {
            var from, to, start, end;

            if (element.selectionEnd !== undefined) {
                from = element.selectionStart;
                to = element.selectionEnd;
                start = element.value.substring(0, from);
                end = element.value.substring(to, element.value.length);

                if (to > from) {
                    element.value = start + beforeWrapTag + element.value.substring(from, to) + afterWrapTag + end;
                } else {
                    element.value = start + beforeWrapTag + afterWrapTag + end;
                }
                setSelectionRange(element, from, to + beforeWrapTag.length + afterWrapTag.length);
            } else {
                // :(
                element.value += beforeWrapTag + afterWrapTag;
                setSelectionRange(element, from, to + beforeWrapTag.length + afterWrapTag.length);
            }

            return Promise.resolve();
        }

        function insertTextboxWrappingWysiwyg(element, beforeWrapTag, afterWrapTag) {
            return new Promise(function (resolvePromise) {

                var editor = window.wysiwygEditors[element.id];

                editor.focus();
                var selectedHtml = getWYSISWYGSelectedHtml(editor);

                if (selectedHtml === '') {
                    selectedHtml = '{!comcode:TEXT_OR_COMCODE_GOES_HERE;^}'.toUpperCase();
                }

                var newHtml = '',
                    url = $util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,comcode_convert}?semihtml=1&is_semihtml=1&lax_comcode=1' + $cms.keep()));

                if ($cms.getPageName() === 'topics') {
                    url += '&forum_db=1';
                }

                var post = 'data=' + encodeURIComponent((beforeWrapTag + selectedHtml + afterWrapTag).replace(new RegExp(String.fromCharCode(8203), 'g'), ''));
                if ($cms.form.isModSecurityWorkaroundEnabled()) {
                    post = $cms.form.modSecurityWorkaroundAjax(post);
                }
                $cms.doAjaxRequest(url, [function (responseXml) {
                    if (responseXml && (responseXml.querySelector('result'))) {
                        var result = responseXml.querySelector('result');
                        newHtml = result.textContent.replace(/\s*$/, '');
                        /* result is an XML-escaped string of HTML, so we get via looking at the node text */
                    } else {
                        newHtml = selectedHtml;
                    }

                    _insertTextboxWrappingWysiwyg(element, editor, newHtml);
                    resolvePromise();
                }], post);
            });
        }

        function _insertTextboxWrappingWysiwyg(element, editor, newHtml) {
            if ((editor.getSelection()) && (editor.getSelection().getStartElement().getName() === 'kbd')) { // Danger Danger - don't want to insert into another Comcode tag. Put it after. They can cut+paste back if they need.
                editor.document.getBody().appendHtml(newHtml);
            } else {
                editor.insertHtml(newHtml);
            }

            findTagsInEditor(editor, element);

            editor.updateElement();
        }
    }

    // From http://www.faqts.com/knowledge_base/view.phtml/aid/13562
    function setSelectionRange(input, selectionStart, selectionEnd) {
        input.focus();
        if (input.setSelectionRange !== undefined) {
            input.setSelectionRange(selectionStart, selectionEnd);
        }
    }

    function showUploadSyndicationOptions(name, syndicationJson, noQuota) {
        name = strVal(name);
        syndicationJson = strVal(syndicationJson);
        noQuota = Boolean(noQuota);

        var htmlSpot = document.getElementById(name + '-syndication-options'),
            html = '',
            numChecked = 0,
            fileOb = document.getElementById(name),
            preDisabled = fileOb.disabled, hook,
            syndication = JSON.parse(syndicationJson),
            num = Object.keys(syndication).length,
            id, authorised, label, checked;

        for (hook in syndication) {
            id = 'upload_syndicate__' + hook + '__' + name;
            authorised = syndication[hook].authorised;
            label = syndication[hook].label;

            if (authorised) {
                checked = true;
                numChecked++;
            } else {
                checked = false;
            }

            setTimeout((function (id, authorised, hook) {
                $dom.on('#' + id, 'click', function () {
                    var el = document.getElementById(id);
                    if (el.checked && !authorised) {
                        //e.checked=false;  Better to assume success, not all oAuth support callback
                        var url = '{$FIND_SCRIPT_NOHTTP;,upload_syndication_auth}?hook=' + encodeURIComponent(hook) + '&name=' + encodeURIComponent(name) + $cms.keep();

                        if ($cms.isMobile()) {
                            window.open(url);
                        } else {
                            $cms.ui.open(url, null, 'width=960;height=500', '_top');
                        }

                        if (!preDisabled) {
                            fileOb.disabled = false;
                        }
                    }
                });
            }).bind(undefined, id, authorised, hook), 0);

            html += '<span><label for="' + id + '"><input type="checkbox" ' + (checked ? 'checked="checked" ' : '') + 'id="' + id + '" name="' + id + '" value="1" />{!upload_syndication:UPLOAD_TO;^} ' + $cms.filter.html(label) + ((noQuota && (num === 1)) ? ' ({!_REQUIRED;^})' : '') + '</label></span>';
        }

        if (noQuota && (numChecked === 0)) {
            fileOb.disabled = true;
        }

        if ((html !== '') && !noQuota) {
            html += '<span><label for="force_remove_locally"><input type="checkbox" id="force_remove_locally" name="force_remove_locally" value="1" />{!upload_syndication:FORCE_REMOVE_LOCALLY;^}</label></span>';
        }

        html = '<div>' + html + '</div>';

        $dom.html(htmlSpot, html);
    }
}(window.$cms, window.$util, window.$dom));
