(function ($cms, $util, $dom) {
    'use strict';

    /*
     Naming conventions...

     t- Tab header
     g- Tab body
     b_ Toolbar
     e_ Editor textbox
     */

    // INIT CODE
    window.templateEditorOpenFiles || (window.templateEditorOpenFiles = {});
    window.doneCleanupTemplateMarkers = Boolean(window.doneCleanupTemplateMarkers);

    if ($cms.pageUrl().searchParams.get('keep_template_magic_markers') === '1') {
        $dom.ready.then(function () {
            cleanupTemplateMarkers(window);
        });
    }

    $cms.views.ThemeManageScreen = ThemeManageScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function ThemeManageScreen() {
        ThemeManageScreen.base(this, 'constructor', arguments);
    }

    $util.inherits(ThemeManageScreen, $cms.View);

    $cms.views.ThemeTemplateEditorTab = ThemeTemplateEditorTab;
    /**
     * @memberof $cms.views
     * @class ThemeTemplateEditorTab
     * @extends $cms.View
     */
    function ThemeTemplateEditorTab(params) {
        ThemeTemplateEditorTab.base(this, 'constructor', arguments);

        // Allow searching via URL hash
        if (window.location.hash) {
            setTimeout(function () {
                var hash = window.location.hash.substr(1, window.location.hash.length - 1);
                window.editareaDoSearch('e_' + params.fileId, hash);
            }, 2000);
        }

        if ($cms.configOption('editarea')) {
            window.aceComposrLoader('e_' + params.fileId, params.highlighterType, false);
        }

        if (params.includeCssEditing && window.opener && window.opener.document) {
            this.loadContextualCssEditor(params.file, params.fileId);
        }
    }

    $util.inherits(ThemeTemplateEditorTab, $cms.View, /**@lends ThemeTemplateEditorTab#*/{
        events: function () {
            return {
                'keydown .js-ta-tpl-editor': 'editorKeyPress',
                'click .js-btn-save-content': 'saveContent',
                'click .js-btn-live-preview': 'livePreview',
                'click .js-btn-screen-preview': 'screenPreview',
                'click .js-a-insert-guid': 'insertGuid',
                'click .js-a-tpl-editor-add-tab': 'addEditorTab',
                'click .js-btn-equation-helper': 'cssEquationHelper'
            };
        },

        editorKeyPress: function (e, textarea) {
            if ($dom.keyPressed(e, 'Tab')) {
                e.preventDefault();
                window.$editing.insertTextbox(textarea, "\t");
            }
        },

        saveContent: function (e) {
            e.preventDefault();
            templateEditorTabSaveContent(this.params.file);

            function templateEditorTabSaveContent(file) {
                var url = 'template_editor_save';
                url += '&file=' + encodeURIComponent(file);
                url += '&theme=' + encodeURIComponent(window.templateEditorTheme);

                window.editareaReverseRefresh('e_' + fileToFileId(file));

                var post = 'contents=' + encodeURIComponent(getFileTextbox(file).value);
                $cms.loadSnippet(url, post).then(function (ajaxResult) {
                    $cms.ui.alert(ajaxResult);
                    templateEditorTabMarkNonchangedContent(file);

                    e.target.disabled = false;
                });
            }

            function templateEditorTabMarkNonchangedContent(file) {
                window.templateEditorOpenFiles[file].unsavedChanges = false;

                var fileId = fileToFileId(file);
                var ob = document.getElementById('t-' + fileId);
                ob.classList.remove('file-changed');
                ob.classList.add('file-nonchanged');
            }
        },

        livePreview: function (e, target) {
            var params = this.params;
            if (!templateEditorPreview(e, params.fileId, params.livePreviewUrl, target, true)) {
                e.preventDefault();
            }
        },

        screenPreview: function (e, target) {
            var opts = this.params;
            if (!templateEditorPreview(e, opts.fileId, opts.screenPreviewUrl, target)) {
                e.preventDefault();
            }
        },

        insertGuid: function (e, target) {
            var guid = target.dataset.insertGuid;

            insertGuid(this.params.file, guid);

            function insertGuid(file, guid) {
                var textbox = getFileTextbox(file);

                var hasEditarea = window.editareaIsLoaded(textbox.name);

                window.editareaReverseRefresh('e_' + fileToFileId(file));

                window.$editing.insertTextbox(textbox, '{' + '+START,IF,{' + '$EQ,{' + '_GUID},' + guid + '}}\n{' + '+END}').then(function () {
                    if (hasEditarea) {
                        window.editareaRefresh(textbox.id);
                    }
                });
            }
        },

        addEditorTab: function (e, target) {
            var file = target.dataset.templateFile;

            templateEditorAddTab(file);
        },

        cssEquationHelper: function (e) {
            e.preventDefault();

            var params = this.params,
                url = 'themewizard_equation';

            url += '&theme=' + encodeURIComponent(params.theme);
            url += '&css_equation=' + encodeURIComponent(document.getElementById('css-equation-' + params.fileId).value);

            $cms.loadSnippet(url).then(function (result) {
                if (!result || result.includes('<html')) {
                    $cms.ui.alert('{!ERROR_OCCURRED;^}');
                } else {
                    document.getElementById('css-result-' + params.fileId).value = result;
                }
            });
        },

        loadContextualCssEditor: function loadContextualCssEditor(file, fileId) {
            var ui = document.getElementById('selectors-' + fileId);
            ui.style.display = 'block'; // Un-hide it
            var list = document.createElement('ul');
            list.id = 'selector_list_' + fileId;
            document.getElementById('selectors-inner-' + fileId).appendChild(list);

            setUpParentPageHighlighting(file, fileId);

            // Set up background compiles
            var textareaId = 'e_' + fileId;
            if (window.editareaIsLoaded(textareaId)) {
                var editor = window.aceEditors[textareaId];

                var lastCss = window.editareaGetValue(textareaId);

                editor.cssRecompilerTimer = setInterval(function () {
                    if ((window.opener) && (window.opener.document)) {
                        if (editor.lastChange === undefined) { // No change made at all
                            return;
                        }

                        var millisecondsAgo = (new Date()).getTime() - editor.lastChange;
                        if (millisecondsAgo > 3 * 1000) { // Not changed recently enough (within last 3 seconds)
                            return;
                        }

                        if (window.opener.haveSetUpParentPageHighlighting === undefined) {
                            setUpParentPageHighlighting(file, fileId);
                            lastCss = '';
                            /*force new CSS to apply*/
                        }

                        var newCss = window.editareaGetValue(textareaId);
                        if (newCss === lastCss) {// Not changed
                            return;
                        }

                        var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=css_compile__text' + $cms.keep(),
                            post = 'css=' + encodeURIComponent(newCss);
                        if ($cms.form.isModSecurityWorkaroundEnabled()) {
                            post = $cms.form.modSecurityWorkaroundAjax(post);
                        }
                        $cms.doAjaxRequest(url, null, post).then(function (xhr) {
                            receiveCompiledCss(xhr, file);
                        });

                        lastCss = newCss;
                    }
                }, 2000);
            }

            function receiveCompiledCss(ajaxResultFrame, file, win) {
                var doingCssFor = file.replace(/^css\//, '').replace('.css', '');

                win || (win = window.opener);

                if (win) {
                    try {
                        var css = ajaxResultFrame.responseText;

                        // Remove old link tag
                        var e;
                        if (doingCssFor === 'no_cache') {
                            e = win.document.getElementById('inline_css');
                            if (e) {
                                e.parentNode.removeChild(e);
                            }
                        } else {
                            var links = win.document.getElementsByTagName('link');
                            for (var i = 0; i < links.length; i++) {
                                e = links[i];
                                if ((e.type === 'text/css') && e.href.includes('/templates_cached/' + window.opener.$cms.userLang() + '/' + doingCssFor)) {
                                    e.parentNode.removeChild(e);
                                }
                            }
                        }

                        // Create style tag for this
                        var style = win.document.getElementById('style_for_' + doingCssFor);
                        if (!style) {
                            style = win.document.createElement('style');
                        }
                        style.type = 'text/css';
                        style.id = 'style_for_' + doingCssFor;
                        if (style.styleSheet) {
                            style.styleSheet.cssText = css;
                        } else {
                            if (style.childNodes[0] !== undefined) {
                                style.removeChild(style.childNodes[0]);
                            }
                            var tn = win.document.createTextNode(css);
                            style.appendChild(tn);
                        }
                        win.document.querySelector('head').appendChild(style);

                        for (var j = 0; j < win.frames.length; j++) {
                            if (win.frames[j]) {// If test needed for some browsers, as window.frames can get out-of-date
                                receiveCompiledCss(ajaxResultFrame, file, win.frames[j]);
                            }
                        }
                    } catch (ex) {}
                }
            }

            function setUpParentPageHighlighting(file, fileId) {
                window.opener.haveSetUpParentPageHighlighting = true;

                var doingCssFor = file.replace(/^css\//, '').replace('.css', '');

                var li, a, selector, elements, element, j, cssText;

                var selectors = findActiveSelectors(doingCssFor, window.opener);

                var list = document.getElementById('selector_list_' + fileId);
                $dom.html(list, '');

                for (var i = 0; i < selectors.length; i++) {
                    selector = selectors[i].selectorText;

                    // Add to list of selectors
                    li = document.createElement('li');
                    a = document.createElement('a');
                    li.appendChild(a);
                    a.href = '#!';
                    a.id = 'selector_' + i;
                    $dom.html(a, $cms.filter.html(selector));
                    list.appendChild(li);

                    // Add tooltip so we can see what the CSS text is in when hovering the selector
                    cssText = (selectors[i].cssText === undefined) ? selectors[i].style.cssText : selectors[i].cssText;
                    if (cssText.indexOf('{') !== -1) {
                        cssText = cssText.replace(/ \{ /g, ' {<br />\n&nbsp;&nbsp;&nbsp;').replace(/; \}/g, '<br />\n}').replace(/; /g, ';<br />\n&nbsp;&nbsp;&nbsp;');
                    } else { // IE
                        cssText = cssText.toLowerCase().replace(/; /, ';<br />\n');
                    }
                    li.addEventListener('mouseout', function () {
                        $cms.ui.deactivateTooltip(this);
                    });
                    li.addEventListener('mousemove', function (event) {
                        $cms.ui.repositionTooltip(this, event);
                    });
                    li.addEventListener('mouseover', (function (cssText) {
                        return function (event) {
                            $cms.ui.activateTooltip(this, event, cssText, 'auto');
                        };
                    }(cssText)));

                    // Jump-to
                    a.addEventListener('click', (function (selector) {
                        return function (e) {
                            window.editareaDoSearch(
                                'e_' + fileId,
                                '^[ \t]*' + selector.replace(/\./g, '\\.').replace(/\[/g, '\\[').replace(/\]/g, '\\]').replace(/\{/g, '\\{').replace(/\}/g, '\\}').replace(/\+/g, '\\+').replace(/\*/g, '\\*').replace(/\s/g, '[ \t]+') + '\\s*\\{'
                            );
                            e.preventDefault();
                        };
                    }(selector)));

                    // Highlighting on parent page
                    a.addEventListener('onmouseover', (function (selector) {
                        return function (event) {
                            if ((window.opener) && (!event.ctrlKey) && (!event.metaKey)) {
                                var elements = findSelectorsFor(window.opener, selector);
                                for (var i = 0; i < elements.length; i++) {
                                    elements[i].style.outline = '3px dotted green';
                                    elements[i].style.backgroundColor = 'green';
                                }
                            }
                        };
                    }(selector)));
                    a.addEventListener('mouseout', (function (selector) {
                        return function (event) {
                            if ((window.opener) && (!event.ctrlKey) && (!event.metaKey)) {
                                var elements = findSelectorsFor(window.opener, selector);
                                for (var i = 0; i < elements.length; i++) {
                                    elements[i].style.outline = '';
                                    elements[i].style.backgroundColor = '';
                                }
                            }
                        };
                    }(selector)));

                    // Highlighting from parent page
                    elements = findSelectorsFor(window.opener, selector);
                    for (j = 0; j < elements.length; j++) {
                        element = elements[j];

                        element.addEventListener('mouseover', (function (a, element) {
                            return function (event) {
                                if (window && !event.ctrlKey && !event.metaKey) {
                                    var target = event.target;
                                    var targetDistance = 0;
                                    var elementRecurse = element;
                                    do {
                                        if (elementRecurse === target) {
                                            break;
                                        }
                                        elementRecurse = elementRecurse.parentNode;
                                        targetDistance++;
                                    } while (elementRecurse);
                                    if (targetDistance > 10) { // Max range
                                        targetDistance = 10;
                                    }

                                    a.style.outline = '1px dotted green';
                                    a.style.background = '#00' + ($util.decToHex(255 - targetDistance * 25)) + '00';
                                    if (targetDistance > 4) {
                                        a.style.color = 'white';
                                    } else {
                                        a.style.color = 'black';
                                    }
                                }
                            };
                        }(a, element)));
                        element.addEventListener('mouseout', (function (a) {
                            return function (event) {
                                if ((window) && (!event.ctrlKey) && (!event.metaKey)) {
                                    a.style.outline = '';
                                    a.style.background = '';
                                    a.style.color = '';
                                }
                            };
                        }(a)));
                    }
                }

                function findSelectorsFor(opener, selector) {
                    var result = [], result2;
                    try {
                        result2 = opener.document.querySelectorAll(selector);
                        for (var j = 0; j < result2.length; j++) {
                            result.push(result2[j]);
                        }
                    } catch (e) {}

                    for (var i = 0; i < opener.frames.length; i++) {
                        if (opener.frames[i]) {// If test needed for some browsers, as window.frames can get out-of-date
                            result2 = findSelectorsFor(opener.frames[i], selector);
                            for (var k = 0; k < result2.length; k++) {
                                result.push(result2[k]);
                            }
                        }
                    }
                    return result;
                }

                function findActiveSelectors(match, win) {
                    var test, selector, selectors = [], classes, i, j, result2;
                    try {
                        for (i = 0; i < win.document.styleSheets.length; i++) {
                            try {
                                if (
                                    (!match) ||
                                    (!win.document.styleSheets[i].href && ((win.document.styleSheets[i].ownerNode && win.document.styleSheets[i].ownerNode.id === 'style_for_' + match) ||
                                        (!win.document.styleSheets[i].ownerNode && win.document.styleSheets[i].id === 'style_for_' + match))) ||
                                    (win.document.styleSheets[i].href && win.document.styleSheets[i].href.indexOf('/' + match) !== -1) ||
                                    (win.document.styleSheets[i].href && win.document.styleSheets[i].href.indexOf('sheet=' + match) !== -1)
                                ) {
                                    classes = win.document.styleSheets[i].rules || win.document.styleSheets[i].cssRules;
                                    for (j = 0; j < classes.length; j++) {
                                        selector = classes[j].selectorText;
                                        test = win.document.querySelectorAll(selector);
                                        if (test.length !== 0) {
                                            selectors.push(classes[j]);
                                        }
                                    }
                                }
                            } catch (e) {}
                        }
                    } catch (e) {}

                    for (i = 0; i < win.frames.length; i++) {
                        if (win.frames[i]) {// If test needed for some browsers, as window.frames can get out-of-date
                            result2 = findActiveSelectors(match, win.frames[i]);
                            for (j = 0; j < result2.length; j++) {
                                selectors.push(result2[j]);
                            }
                        }
                    }

                    return selectors;
                }
            }
        }
    });

    function templateEditorPreview(e, fileId, url, button, askForUrl) {
        e.preventDefault();

        if (askForUrl === undefined) {
            askForUrl = false;
        }

        var hasEditarea = window.editareaIsLoaded('e_' + fileId);
        if (hasEditarea) {
            window.editareaReverseRefresh('e_' + fileId);
        }

        if (document.getElementById('mobile_preview_' + fileId).checked) {
            url += (url.indexOf('?') === -1) ? '?' : '&';
            url += 'keep_mobile=1';
        }

        var submitPreview = function () {
            var tokenField = button.form.elements['csrf_token'];
            if (tokenField) {
                return $cms.getCsrfToken().then(function (text) {
                    $util.log('Regenerated CSRF token');

                    tokenField.value = text;

                    button.form.action = url;
                    button.form.target = '_blank';

                    if ($cms.form.isModSecurityWorkaroundEnabled()) {
                        $cms.form.modSecurityWorkaround(button.form);
                        return;
                    }

                    button.form.submit();
                });
            }
        };

        if (askForUrl) {
            $cms.ui.prompt(
                '{!themes:URL_TO_PREVIEW_WITH;^}',
                url,
                function (url) {
                    if (url !== null) {
                        submitPreview();
                    }
                },
                '{!PREVIEW;^}'
            );

            return false;
        }

        submitPreview();

        return false;
    }

    $cms.functions.adminThemesEditTheme = function () {
        var themee = document.getElementById('theme'),
            themet = document.getElementById('title'),
            copy = document.getElementById('copy');

        if (copy) {
            copy.addEventListener('change', function () {
                if (copy.checked && !themee.value.includes('-copy')) {
                    themee.value += '-copy';
                    themet.value += ' copy';
                }
            });
        }
    };

    $cms.functions.adminThemesAddTheme = function () {
        var title = document.getElementById('title');
        title.addEventListener('change', function () {
            var codename = document.getElementById('theme');
            if (codename.value === '') {
                codename.value = title.value.replace(/[^{$URL_CONTENT_REGEXP_JS}]/g, '');
            }
        });
        var extraChecks = [],
            validValue;
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var value = form.elements['theme'].value;

            if ((value === validValue) || (value === '')) {
                return true;
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=exists_theme&name=' + encodeURIComponent(value) + $cms.keep();
                return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    if (!valid) {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form.elements['theme'];
                    }
                });
            };
        });
        return extraChecks;
    };

    $cms.templates.tempcodeTesterScreen = function tempcodeTesterScreen(params, container) {
        $dom.on(container, 'click', '.js-click-btn-tempcode-tester-do-preview', function (e, btn) {
            var request = '';

            for (var i = 0; i < btn.form.elements.length; i++) {
                request += encodeURIComponent(btn.form.elements[i].name) + '=' + encodeURIComponent(btn.form.elements[i].value) + '&';
            }

            $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,tempcode_tester}' + $cms.keep(true), null, request).then(function (xhr) {
                $dom.html('#preview-raw', $cms.filter.html(xhr.responseText));
                $dom.html('#preview-html', xhr.responseText);
            });

            $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,tempcode_tester}?comcode=1' + $cms.keep(), null, request).then(function (xhr) {
                $dom.html('#preview-comcode', xhr.responseText);
            });
        });
    };

    $cms.templates.themeTemplateEditorTempcodeDropdown = function themeTemplateEditorTempcodeDropdown(params, container) {
        var fileId = strVal(params.fileId),
            stub = strVal(params.stub);

        $dom.on(container, 'click', '.js-click-template-insert-parameter', function () {
            templateInsertParameter('b_' + fileId + '_' + stub, fileId);
        });

        function templateInsertParameter(dropdownName, fileId) {
            var textarea = document.getElementById('e_' + fileId);

            window.editareaReverseRefresh('e_' + fileId);

            var dropdown = document.getElementById(dropdownName),
                valueParts = dropdown.value.split('__'),
                value = valueParts[0],
                arity = valueParts[1];

            if (value === '---') {
                return;
            }

            var hasEditarea = window.editareaIsLoaded(textarea.name);

            if ((value === 'BLOCK') && ($cms.configOption('js_overlays') || (window.showModalDialog !== undefined))) {
                var url = '{$FIND_SCRIPT_NOHTTP;,block_helper}?field_name=' + textarea.name + '&block_type=template' + $cms.keep();
                $cms.ui.showModalDialog($util.rel($cms.maintainThemeInLink(url)), null, 'dialogWidth=750;dialogHeight=600;status=no;resizable=yes;scrollbars=yes;unadorned=yes').then(function () {
                    if (hasEditarea) {
                        window.editareaRefresh(textarea.name);
                    }
                });
                return;
            }

            // Number of required parameters to be entered
            var definiteGets = 0;
            if (arity === '1') {
                definiteGets = 1;
            } else if (arity === '2') {
                definiteGets = 2;
            } else if (arity === '3') {
                definiteGets = 3;
            } else if (arity === '4') {
                definiteGets = 4;
            } else if (arity === '5') {
                definiteGets = 5;
            } else if (arity === '0-1') {
                definiteGets = 0;
            } else if (arity === '3-4') {
                definiteGets = 3;
            } else if (arity === '0+') {
                definiteGets = 0;
            } else if (arity === '1+') {
                definiteGets = 1;
            }

            _getParameterParameters(definiteGets, arity, 0, '', function (params) {
                var text;

                if (dropdownName.endsWith('_DIRECTIVE')) {
                    text = '{' + '+START,' + value + params + '}{' + '+END}';
                } else {
                    text = '{' + (dropdownName.endsWith('_PARAMETER') ? '' : '$') + value + '*' + params + '}';
                }


                if (hasEditarea) {
                    window.aceEditors[textarea.name].insert(text); // Insert at cursor, emulating user input:
                    window.editareaReverseRefresh(textarea.name);
                } else {
                    textarea.value += text;
                }
            });

            function _getParameterParameters(definiteGets, arity, numDone, params, callback) {
                var parameter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

                if (numDone < definiteGets) {
                    $cms.ui.prompt('{!themes:INPUT_NECESSARY_PARAMETER;^}' + ', ' + parameter[numDone], '', null, '{!themes:INSERT_PARAMETER;^}').then(function (v) {
                        if (v !== null) {
                            params = params + ',' + v;
                            _getParameterParameters(definiteGets, arity, numDone + 1, params, callback);
                        }
                    });
                } else {
                    if ((arity === '0+') || (arity === '1+')) {
                        $cms.ui.prompt('{!themes:INPUT_OPTIONAL_PARAMETER;^}', '', null, '{!themes:INSERT_PARAMETER;^}').then(function (v) {
                            if (v !== null) {
                                params = params + ',' + v;
                                _getParameterParameters(definiteGets, arity, numDone + 1, params, callback);
                            } else {
                                callback(params);
                            }
                        });
                    } else if ((arity === '0-1') || (arity === '3-4')) {
                        $cms.ui.prompt('{!themes:INPUT_OPTIONAL_PARAMETER;^}', '', null, '{!themes:INSERT_PARAMETER;^}').then(function (v) {
                            if (v != null) {
                                params = params + ',' + v;
                            }

                            callback(params);
                        });
                    } else {
                        callback(params);
                    }
                }
            }
        }
    };

    $cms.templates.templateEditLink = function templateEditLink(params, container) {
        var editUrl = strVal(params.editUrl);

        $dom.on(container, 'click', '.js-click-open-edit-url', function () {
            window.open(editUrl);
        });

        $dom.on(container, 'click', '.js-keypress-open-edit-url', function () {
            window.open(editUrl);
        });
    };

    $cms.templates.themeTemplateEditorScreen = function themeTemplateEditorScreen(params, container) {
        window.templateEditorTheme = params.theme;

        if (params.activeGuid !== undefined) {
            window.templateEditorActiveGuid = params.activeGuid;
        }

        if (params.livePreviewUrl !== undefined) {
            window.templateEditorLivePreviewUrl = params.livePreviewUrl;
        }

        templateEditorCleanTabs();

        $cms.requireJavascript('tree_list').then(function () {
            window.sitemap = $cms.ui.createTreeList('theme_files', '{$FIND_SCRIPT_NOHTTP;,ajax_tree}?hook=choose_theme_files' + $cms.keep(), null, $cms.filter.url(JSON.stringify({'theme': params.theme})), false, null, false, true);
        });

        setTimeout(function () {
            for (var i = 0, len = params.filesToLoad.length; i < len; i++) {
                templateEditorAddTab(params.filesToLoad[i]);
            }
        }, 1000);

        if (window.jQuery && window.jQuery.fn.resizable) {
            window.jQuery('.template-editor-file-selector').resizable();
        }

        templateEditorAssignUnloadEvent();

        $dom.on(container, 'change', '.js-change-template-editor-add-tab-wrap', function () {
            templateEditorAddTab(document.getElementById('theme_files').value);
        });

        $dom.on(container, 'click', '.js-click-btn-add-template', function () {
            addTemplate();
        });

        function addTemplate() {
            $cms.ui.prompt('{!themes:INPUT_TEMPLATE_TYPE;^}', 'templates', null, '{!themes:ADD_TEMPLATE;^}').then(function (subdir) {
                if (subdir !== null) {
                    if (subdir !== 'templates' && subdir !== 'css' && subdir !== 'javascript' && subdir !== 'text' && subdir !== 'xml') {
                        $cms.ui.alert('{!themes:BAD_TEMPLATE_TYPE;^}');
                        return;
                    }

                    $cms.ui.prompt('{!themes:INPUT_TEMPLATE_NAME;^}', 'example', null, '{!themes:ADD_TEMPLATE;^}').then(function (file) {
                        if (file !== null) {
                            file = file.replace(/\..*$/, '');
                            switch (subdir) {
                                case 'templates':
                                    file = 'templates/' + file + '.tpl';
                                    break;

                                case 'css':
                                    file = 'css/' + file + '.css';
                                    break;

                                case 'javascript':
                                    file = 'javascript/' + file + '.js';
                                    break;

                                case 'text':
                                    file = 'text/' + file + '.txt';
                                    break;

                                case 'xml':
                                    file = 'xml/' + file + '.xml';
                                    break;
                            }

                            templateEditorAddTab(file);
                        }
                    });
                }
            });
        }

        function templateEditorAssignUnloadEvent() {
            window.addEventListener('beforeunload', function (event) {
                if (document.querySelector('.file-changed')) {
                    var ret = '{!themes:UNSAVED_TEMPLATE_CHANGES;^}';
                    event.returnValue = ret; // Workaround Chrome issue (explained on https://developer.mozilla.org/en-US/docs/Web/Events/beforeunload)
                    return ret;
                }
                return null;
            });
        }
    };

    $cms.templates.themeImageManageScreen = function () {
        window.mainFormVerySimple = true;
    };

    $cms.templates.themeImageEditScreen = function themeImageEditScreen(params, container) {
        $dom.on(container, 'click', '.js-click-select-tab-g', function (e, clicked) {
            var tab = clicked.dataset.tpTab;

            if (tab) {
                $cms.ui.selectTab('g', tab);
            }
        });
    };

    $cms.templates.themeTemplateEditorRestoreRevision = function (params, container) {
        var file = strVal(params.file),
            revisionId = strVal(params.revisionId);

        $dom.on(container, 'click', function (e) {
            e.preventDefault();
            templateEditorRestoreRevision(file, revisionId);
        });

        function templateEditorRestoreRevision(file, revisionId) {
            var fileId = fileToFileId(file);

            // Set content from revision
            var url = templateEditorLoadingUrl(file, revisionId);
            $cms.loadSnippet(url).then(function (html) {
                document.getElementById('t-' + fileId).className = 'tab tab-active';

                templateEditorTabLoadedContent(html, file);
            });
        }
    };

    $cms.templates.templateTreeItem = function templateTreeItem(params, container) {
        $dom.on(container, 'click', '.js-click-checkbox-toggle-guid-input', function (e, checkbox) {
            var el = $dom.$('#f' + checkbox.id.replace(/file/, 'guid'));
            if (el) {
                el.disabled = !checkbox.checked;
            }
        });
    };

    $cms.templates.themeScreenPreview = function (params, container) {
        var template = strVal(params.template);

        $dom.on(container, 'click', '.js-link-click-open-template-preview-window', function (e, link) {
            window.open(link.href, 'template_preview_' + template, 'width=1280,height=720,status=no,resizable=yes,scrollbars=yes');
        });

        $dom.on(container, 'click', '.js-link-click-open-mobile-template-preview-window', function (e, link) {
            var actualWidth = 320 + window.innerWidth - document.documentElement.clientWidth; // We want exactly 320 for the width to accurately test mobile, but scroll bars may shrink the actual width of the page.
            window.open(link.href, 'template_preview_' + template, 'width=' + strVal(actualWidth) + ',height=568,status=no,resizable=yes,scrollbars=yes');
        });
    };

    function cleanupTemplateMarkers(win) {
        if (window.doneCleanupTemplateMarkers) {
            return;
        }

        _cleanupTemplateMarkers(win.document.body, 0);

        window.doneCleanupTemplateMarkers = true;

        function _cleanupTemplateMarkers(node, depth) {
            var inside = [];

            node = node.firstChild;
            while (node) {
                if (node.nodeType === 3) { // Text node
                    var matches = node.data.match(/[\u200B\uFEFF]+/g);
                    if (matches) {
                        var allDecoded = [];
                        for (var i = 0; i < matches.length; i++) {
                            var decoded = invisibleOutputDecode(matches[i]);
                            var _decoded = decoded.match(/<\/?templates\/[^<>]*>/g);
                            for (var j = 0; j < _decoded.length; j++) {
                                allDecoded.push(_decoded[j]);
                            }
                        }
                        for (var k = 0; k < allDecoded.length; k++) {
                            var decoded2 = allDecoded[k];
                            var openerMatch = decoded2.match('<(templates/.*)>');
                            if (openerMatch != null) {
                                inside.push(openerMatch[1]);
                            }
                            var closerMatch = decoded2.match('</(templates/.*)>');
                            if (closerMatch != null) {
                                var at = inside.indexOf(closerMatch[1]);
                                if (at !== -1) {
                                    inside.splice(at, 1);
                                }
                            }

                            node.data = node.data.replace(matches[k], ''); // Strip it, to clean document
                        }
                    }
                } else if (node.nodeType === 1) { // Element node
                    var before = node.getAttribute('data-template');
                    if (!before) {
                        before = '';
                    }
                    node.setAttribute('data-template', before + ' ' + inside.join(' ') + ' ');
                }

                // Continue...

                _cleanupTemplateMarkers(node, depth + 1);

                node = node.nextSibling;
            }

            function invisibleOutputDecode(string) {
                var ret = '';
                var character, _bitsRep, bitsRep, _bit, bit;
                var len = string.length;
                for (var i = 0; i < len / 8; i++) {
                    _bitsRep = '';
                    for (_bit = 0; _bit < 8; _bit++) {
                        character = string.substr(i * 8 + _bit, 1);
                        bit = (character === "\u200B") ? "1" : "0";
                        _bitsRep += bit;
                    }
                    bitsRep = parseInt(_bitsRep, 2);
                    ret += String.fromCharCode(bitsRep);
                }

                return ret;
            }
        }
    }

    function templateEditorAddTab(file) {
        var tabTitle = file.replace(/^.*\//, ''),
            fileId = fileToFileId(file);

        // Switch to tab if exists
        if (document.getElementById('t-' + fileId)) {
            $cms.ui.selectTab('g', fileId);

            templateEditorShowTab(fileId);

            return;
        }

        // Create new tab header
        var headers = document.getElementById('template-editor-tab-headers');

        var header = document.createElement('a');
        header.setAttribute('aria-controls', 'g-' + fileId);
        header.setAttribute('role', 'tab');
        header.href = '#!';
        header.id = 't-' + fileId;
        header.className = 'tab file-nonchanged';
        header.addEventListener('click', function () {
            $cms.ui.selectTab('g', fileId);
            templateEditorShowTab(fileId);
        });

        var ext = (tabTitle.indexOf('.') !== -1) ? tabTitle.substring(tabTitle.indexOf('.') + 1, tabTitle.length) : '';
        if (ext !== '') {
            tabTitle = tabTitle.substr(0, tabTitle.length - ext.length - 1);
        }
        var iconImg = document.createElement('img');
        if (ext === 'tpl') {
            iconImg.src = $util.srl('{$IMG;,icons/file_types/page_tpl}');
        }
        if (ext === 'css') {
            iconImg.src = $util.srl('{$IMG;,icons/file_types/page_css}');
        }
        if (ext === 'js') {
            iconImg.src = $util.srl('{$IMG;,icons/file_types/page_js}');
        }
        if (ext === 'xml') {
            iconImg.src = $util.srl('{$IMG;,icons/file_types/page_xml}');
        }
        if (ext === 'txt' || ext === '') {
            iconImg.src = $util.srl('{$IMG;,icons/file_types/page_txt}');
        }
        iconImg.width = '16';
        iconImg.height = '16';
        header.appendChild(iconImg);
        header.appendChild(document.createTextNode(' '));
        var span = document.createElement('span');
        span.textContent = tabTitle;
        header.appendChild(span);
        var closeButton = document.createElement('img');
        closeButton.src = $util.srl('{$IMG;,icons/buttons/close}');
        closeButton.width = '32';
        closeButton.height = '32';
        closeButton.alt = '{!CLOSE;^}';
        closeButton.style.paddingLeft = '5px';
        closeButton.width = '16';
        closeButton.height = '16';
        closeButton.style.verticalAlign = 'middle';
        closeButton.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation(); // Required to prevent tab click listener from being fired too

            if (window.templateEditorOpenFiles[file].unsavedChanges) {
                $cms.ui.confirm('{!themes:UNSAVED_CHANGES;^}'.replace('{1}', file), null, '{!Q_SURE;^}', true).then(function (result) {
                    if (result) {
                        templateEditorTabUnloadContent(file);
                    }
                });
            } else {
                templateEditorTabUnloadContent(file);
            }
        });
        header.appendChild(closeButton);
        headers.appendChild(header);

        // Create new tab body
        var bodies = document.getElementById('template-editor-tab-bodies');
        var body = document.createElement('div');
        body.setAttribute('aria-labelledby', 't-' + fileId);
        body.setAttribute('role', 'tabpanel');
        body.id = 'g-' + fileId;
        body.style.display = 'none';
        var loadingImage = document.createElement('img');
        loadingImage.className = 'ajax-loading';
        loadingImage.src = $util.srl('{$IMG;,loading}');
        loadingImage.width = '12';
        loadingImage.height = '12';
        body.appendChild(loadingImage);
        bodies.appendChild(body);

        // Set content
        var url = templateEditorLoadingUrl(file);
        $cms.loadSnippet(url).then(function (html) {
            templateEditorTabLoadedContent(html, file);
        });

        // Cleanup
        templateEditorCleanTabs();

        // Select tab
        $cms.ui.selectTab('g', fileId);

        templateEditorShowTab(fileId);

        function templateEditorTabUnloadContent(file) {
            var fileId = fileToFileId(file),
                wasActive = templateEditorRemoveTab(fileId);

            delete window.templateEditorOpenFiles[file];

            if (wasActive) {
                // Select tab
                var c = document.getElementById('template-editor-tab-headers').firstElementChild;
                if (c != null) {
                    var nextFileId = c.id.substr(2);

                    $cms.ui.selectTab('g', nextFileId);

                    templateEditorShowTab(nextFileId);
                }
            }

            function templateEditorRemoveTab(fileId) {
                var header = document.getElementById('t-' + fileId);
                if (header) {
                    var isActive = (header.classList.contains('tab-active'));

                    header.remove();
                    var body = document.getElementById('g-' + fileId);
                    if (body) {
                        body.remove();
                    }

                    templateEditorCleanTabs();

                    return isActive;
                }

                return false;
            }
        }
    }

    function templateEditorLoadingUrl(file, revisionId) {
        var url = 'template_editor_load';
        url += '&file=' + encodeURIComponent(file);
        url += '&theme=' + encodeURIComponent(window.templateEditorTheme);
        if (window.templateEditorActiveGuid != null) {
            url += '&active_guid=' + encodeURIComponent(window.templateEditorActiveGuid);
        }
        if (window.templateEditorLivePreviewUrl != null) {
            url += '&live_preview_url=' + encodeURIComponent($cms.protectURLParameter(window.templateEditorLivePreviewUrl));
        }
        if (revisionId !== undefined) {
            url += '&undo_revision=' + encodeURIComponent(revisionId);
        }
        return url;
    }

    function templateEditorCleanTabs() {
        var headers = document.getElementById('template-editor-tab-headers');
        var bodies = document.getElementById('template-editor-tab-bodies');
        var numTabs = headers.childNodes.length;

        var header = document.getElementById('t-default');
        var body = document.getElementById('g-default');

        if (header && (numTabs > 1)) {
            header.parentNode.removeChild(header);
            body.parentNode.removeChild(body);
        }

        if (numTabs === 0) {
            $dom.html(headers, '<a href="#!" id="t-default" class="tab"><span>&mdash;</span></a>');
            $dom.html(bodies, '<div id="g-default"><p class="nothing-here">{!NA}</p></div>');
        }
    }

    function templateEditorTabLoadedContent(html, file) {
        var fileId = fileToFileId(file);

        $dom.html('#g-' + fileId, html);

        setTimeout(function () {
            var textareaId = 'e_' + fileId;
            if (window.editareaIsLoaded(textareaId)) {
                var editor = window.aceEditors[textareaId];
                var editorSession = editor.getSession();
                editorSession.on('change', function () {
                    templateEditorTabMarkChangedContent(file);
                    editor.lastChange = (new Date()).getTime();
                });
            } else {
                getFileTextbox(file).addEventListener('change', function () {
                    templateEditorTabMarkChangedContent(file);
                });
            }
        }, 1000);

        window.templateEditorOpenFiles[file] = {
            unsavedChanges: false
        };

        function templateEditorTabMarkChangedContent(file) {
            window.templateEditorOpenFiles[file].unsavedChanges = true;

            var fileId = fileToFileId(file);
            var ob = document.getElementById('t-' + fileId);
            ob.classList.remove('file-nonchanged');
            ob.classList.add('file-changed');
        }
    }

    function templateEditorShowTab(fileId) {
        setTimeout(function () {
            if (!document.getElementById('t-' + fileId) || !document.getElementById('t-' + fileId).classList.contains('tab-active')) {
                // No longer visible
                return;
            }

            if (window.opener) {// If anchored
                highlightTemplate(window.opener, fileIdToFile(fileId));
            }

            if (window.jQuery && window.jQuery.fn.resizable) {
                window.jQuery('#e-' + fileId.replace(/\./g, '\\.') + '-wrap').resizable({
                    resize: function () {
                        var editor = window.aceEditors['e_' + fileId];
                        if (editor !== undefined) {
                            $dom.$('#e_' + fileId.replace(/\./g, '\\.') + '__ace').style.height = '100%';
                            $dom.$('#e_' + fileId.replace(/\./g, '\\.') + '__ace').parentNode.style.height = '100%';
                            editor.resize();
                        }
                    },
                    handles: 's'
                });
            }
        }, 1000);

        function fileIdToFile(fileId) {
            for (var file in window.templateEditorOpenFiles) {
                if (fileToFileId(file) === fileId) {
                    return file;
                }
            }
            return null;
        }

        function highlightTemplate(win, templatePath) {
            _highlightTemplate(win.document.body, templatePath, 0);


            function _highlightTemplate(node, templatePath, depth) {
                node = node.firstChild;
                while (node) {
                    if (node.nodeType === 1) { // Element node
                        var template = node.getAttribute('data-template');
                        var dataMatch = (template && template.includes(' ' + templatePath + ' '));
                        if (dataMatch) {
                            node.classList.add('glowing-node');
                        } else {
                            node.classList.remove('glowing-node');
                        }
                    }

                    // Continue...

                    _highlightTemplate(node, templatePath, depth + 1);

                    node = node.nextSibling;
                }
            }
        }
    }

    function getFileTextbox(file) {
        return document.getElementById('e_' + fileToFileId(file));
    }

    function fileToFileId(file) {
        return file.replace(/\//, '__').replace(/:/, '__').replace(/\./, '__');
    }
}(window.$cms, window.$util, window.$dom));
