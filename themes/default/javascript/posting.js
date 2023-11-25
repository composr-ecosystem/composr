/* globals $editing */
/* Form editing code (mostly stuff only used on posting forms) */
(function ($cms, $util, $dom) {
    'use strict';

    var $posting = window.$posting = {};

    window.doInputHtml = doInputHtml;
    window.doInputCode = doInputCode;
    window.doInputQuote = doInputQuote;
    window.doInputBox = doInputBox;
    window.doInputMenu = doInputMenu;
    window.doInputBlock = doInputBlock;
    window.doInputComcode = doInputComcode;
    window.doInputList = doInputList;
    window.doInputHide = doInputHide;
    window.doInputThumb = doInputThumb;
    window.doInputAttachment = doInputAttachment;
    window.doInputUrl = doInputUrl;
    window.doInputPage = doInputPage;
    window.doInputEmail = doInputEmail;
    window.doInputB = doInputB;
    window.doInputI = doInputI;
    window.doInputFont = doInputFont;


    // ===========
    // ATTACHMENTS
    // ===========

    /**
     * @param startNum
     * @param postingFieldName
     */
    function addAttachment(startNum, postingFieldName) {
        var addTo = document.getElementById('js-attachment-store--' + postingFieldName);

        window.numAttachments++;

        // Add new file input, if we are using naked file inputs
        if (window.attachmentTemplate.trim() !== '') { // ATTACHMENT.tpl
            var newDiv = document.createElement('div');
            $dom.html(newDiv, window.attachmentTemplate.replace(/__num_attachments__/g, window.numAttachments));
            addTo.appendChild(newDiv);
        }

        // Rebuild uploader button, if we have a singular button. NB: The window.rebuildAttachmentButtonForNext type check is important, don't remove.
        if (typeof window.rebuildAttachmentButtonForNext === 'function') {
            window.rebuildAttachmentButtonForNext();
        }

        $dom.triggerResize();
    }

    /**
     *
     * @param postValue
     * @param number
     * @return {*|boolean}
     */
    function attachmentPresent(postValue, number) {
        return postValue.includes('[attachment]new_' + number + '[/attachment]') || postValue.includes('[attachment_safe]new_' + number + '[/attachment_safe]') || postValue.includes('[attachment thumb="1"]new_' + number + '[/attachment]') || postValue.includes('[attachment_safe thumb="1"]new_' + number + '[/attachment_safe]') || postValue.includes('[attachment thumb="0"]new_' + number + '[/attachment]') || postValue.includes('[attachment_safe thumb="0"]new_' + number + '[/attachment_safe]');
    }

    /**
     * Adds attachment Comcode to the provided input (e.g., [attachment]new_1[/attachment])
     * @param fieldName
     * @param number
     * @param filename
     * @param multi
     * @param uploaderSettings
     * @return { Promise }
     */
    $posting.setAttachment = function setAttachment(fieldName, number, filename, multi, uploaderSettings) {
        fieldName = strVal(fieldName);
        number = Number(number);
        filename = strVal(filename);
        multi = Boolean(multi);

        return new Promise(function (resolvePromise) {
            var post = document.getElementById(fieldName),
                tmpForm = post.form;

            if (tmpForm && tmpForm.preview) {
                tmpForm.preview.checked = false;
                tmpForm.preview.disabled = true;
            }

            var postValue = $editing.getTextbox(post),
                done = attachmentPresent(post.value, number) || attachmentPresent(postValue, number),
                addAnotherField;

            if (done) {
                // Add field for next one
                addAnotherField = (number === window.numAttachments) && (window.numAttachments < window.maxAttachments);
                if (addAnotherField) {
                    addAttachment(window.numAttachments + 1, fieldName);
                }
                return resolvePromise();
            }

            var filepath = filename;

            if (!filename && document.getElementById('file' + number)) {
                filepath = document.getElementById('file' + number).value;
            }

            if (!filepath) { // Upload error
                return resolvePromise();
            }

            var ext = filepath.replace(/^.*\./, '').toLowerCase(),
                isImage = (',' + $cms.configOption('valid_images') + ',').includes(',' + ext + ','),
                // isVideo = (',' + $cms.configOption('valid_videos') + ',').includes(',' + ext + ','),
                // isAudio = (',' + $cms.configOption('valid_audios') + ',').includes(',' + ext + ','),
                isArchive = (ext === 'tar') || (ext === 'zip'),
                prefix = '', suffix = '';

            if (multi && isImage) {
                prefix = '[media_set]\n';
                suffix = '[/media_set]';
            }

            var tag = 'attachment', // [attachment]
                showOverlay = false,
                defaults = {};

            if (!filepath.includes('fakepath')) { // iPhone gives c:\fakepath\image.jpg, so don't use that
                defaults.description = filepath; // Default caption to local file path
            }

            /*{+START,INCLUDE,_attachment_ui_defaults,.js,javascript}{+END}*/

            if (!showOverlay) {
                var comcode = '[' + tag;
                for (var key in defaults) {
                    comcode += ' ' + key + '="' + (defaults[key].replace(/"/g, '\\"')) + '"';
                }
                comcode += ']new_' + number + '[/' + tag + ']';

                var promiseCalls = [];
                if (prefix !== '') {
                    promiseCalls.push(function () {
                        return $editing.insertTextbox(post, prefix);
                    });
                }

                if (multi) {
                    var splitFileNames = document.getElementById('txt_filename_file' + window.numAttachments).value.split(/:/);
                    splitFileNames.forEach(function (fileName, i) {
                        promiseCalls.push(function () {
                            if (i > 0) {
                                window.numAttachments++;
                            }
                            var newComcode = comcode.replace(']new_' + number + '[', ']new_' + window.numAttachments + '[');
                            if (!fileName.includes('fakepath')) {
                                newComcode = newComcode.replace(' description="' + defaults.description.replace(/"/g, '\\"') + '"', ' description="' + fileName.replace(/"/g, '\\"') + '"');
                            }
                            return $editing.insertTextbox(post, newComcode);
                        });
                    });
                    promiseCalls.push(function () {
                        number = String(parseInt(number) + (splitFileNames.length - 1));
                        return Promise.resolve();
                    });
                } else {
                    promiseCalls.push(function () {
                        return $editing.insertTextbox(post, comcode);
                    });
                }

                if (suffix !== '') {
                    promiseCalls.push(function () {
                        return $editing.insertTextbox(post, suffix);
                    });
                }

                return $util.promiseSequence(promiseCalls).then(function () {
                    // Add field for next one
                    var addAnotherField = (number === window.numAttachments) && (window.numAttachments < window.maxAttachments); // Needs running late, in case something happened in-between

                    if (addAnotherField) {
                        addAttachment(window.numAttachments + 1, fieldName);
                    }

                    if ($cms.form.isWysiwygField(post) && (uploaderSettings !== undefined)) {
                        // Previously named: uploader_settings.callbacks
                        uploaderSettings.onAllUploadsDoneCallbacks.push(function () {
                            // Do insta-preview for image attachments
                            $posting.showPreviewImagesForAttachmentComcodes(post);
                        });
                    }

                    resolvePromise();
                });
            }

            var url = '{$FIND_SCRIPT_NOHTTP;,comcode_helper}';
            url += '?field_name=' + fieldName;
            url += '&type=step2';
            url += '&tag=' + tag;
            url += '&default=new_' + number;
            url += '&is_image=' + (isImage ? '1' : '0');
            url += '&is_archive=' + (isArchive ? '1' : '0');
            url += '&multi=' + (multi ? '1' : '0');
            url += '&prefix=' + prefix;
            if ($cms.form.isWysiwygField(post)) {
                url += '&in_wysiwyg=1';
            }
            for (var def in defaults) {
                url += '&default_' + def + '=' + encodeURIComponent(defaults[def]);
            }
            url += $cms.keep();

            setTimeout(function () {
                $cms.ui.showModalDialog($util.rel($cms.maintainThemeInLink(url)), '', 'width=800,height=auto,status=no,resizable=yes,scrollbars=yes,unadorned=yes').then(function (comcodeAdded) {
                    if (!comcodeAdded) { // Cancelled
                        var clearButton = document.getElementById('fsClear_file' + number);
                        if (clearButton) {
                            $dom.trigger(clearButton, 'click');
                        }
                        return;
                    }

                    var promise = Promise.resolve();

                    if (multi) { // Add in additional Comcode buttons for the other files selected at the same time
                        var comcodeSemihtml = '', comcode = '',
                            splitFilename = document.getElementById('txt_filename_file' + window.numAttachments).value.split(/:/);

                        for (var i = 1; i < splitFilename.length; i++) {
                            window.numAttachments++;
                            var tmp = window.insertComcodeTag(']new_' + number + '[', ']new_' + window.numAttachments + '[', true);
                            comcodeSemihtml += tmp[0];
                            comcode += tmp[1];
                        }

                        number = String(parseInt(number) + splitFilename.length - 1);

                        if (suffix !== '') {
                            comcode += suffix;
                            comcodeSemihtml += suffix;
                        }

                        promise = $editing.insertTextbox(post, comcode, true, comcodeSemihtml);
                    }

                    promise.then(function () {
                        // Add field for next one
                        var addAnotherField = (number === window.numAttachments) && (window.numAttachments < window.maxAttachments); // Needs running late, in case something happened in-between

                        if (addAnotherField) {
                            addAttachment(window.numAttachments + 1, fieldName);
                        }

                        // Do insta-preview
                        if (comcodeAdded.includes('[attachment_safe') && $cms.form.isWysiwygField(post)) {
                            $posting.showPreviewImagesForAttachmentComcodes(post);
                        }
                    });
                });
            }, 800); // In a timeout to disassociate possible 'enter' keypress which could have led to this function being called [enter on the file selection dialogue] and could propagate through (on Google Chrome anyways, maybe a browser bug)

            return resolvePromise();
        });
    };

    /**
     * WYSIWYG preview for image attachments
     * @param { HTMLTextAreaElement } postTextArea
     */
    $posting.showPreviewImagesForAttachmentComcodes = function showPreviewImagesForAttachmentComcodes(postTextArea) {
        var post = '';
        var form = postTextArea.form;

        for (var i = 0; i < form.elements.length; i++) {
            if (!form.elements[i].disabled && (form.elements[i].name !== undefined) && (form.elements[i].name !== '') && (((form.elements[i].type !== 'radio') && (form.elements[i].type !== 'checkbox')) || (form.elements[i].checked)) && (form.elements[i].name !== 'captcha')) {
                var name = form.elements[i].name,
                    value = $cms.form.cleverFindValue(form, form.elements[i]);

                if ((name === 'title') && (value === '')) { // Fudge, title must be filled in on many forms
                    value = 'x';
                }

                if (post !== '') {
                    post += '&';
                }

                post += name + '=' + encodeURIComponent(value);
            }
        }

        if ($cms.form.isModSecurityWorkaroundEnabled()) {
            post = $cms.form.modSecurityWorkaroundAjax(post);
        }

        $cms.doAjaxRequest(window.formPreviewUrl + '&js_only=1&known_utf8=1', null, post).then(function (xhr) {
            $dom.append(document.body, xhr.responseText);
        });
    };

    // ====================
    // COMCODE UI FUNCTIONS
    // ====================

    function doInputHtml(fieldName) {
        var post = document.getElementById(fieldName);
        return $editing.insertTextboxWrapping(post, 'semihtml', '');
    }

    function doInputCode(fieldName) {
        var post = document.getElementById(fieldName);
        return $editing.insertTextboxWrapping(post, (post.name === 'message') ? 'tt' : 'codebox', '');
    }

    function doInputQuote(fieldName) {
        var post = document.getElementById(fieldName);
        $cms.ui.prompt(
            '{!javascript:ENTER_QUOTE_BY;^}',
            '',
            function (va) {
                if (va != null) {
                    $editing.insertTextboxWrapping(post, '[quote="' + va + '"]', '[/quote]');
                }
            },
            '{!comcode:INPUT_COMCODE_quote;^}'
        );
    }

    function doInputBox(fieldName) {
        var post = document.getElementById(fieldName);
        $cms.ui.prompt(
            '{!javascript:ENTER_BOX_TITLE;^}',
            '',
            function (va) {
                if (va != null) {
                    $editing.insertTextboxWrapping(post, '[box="' + va + '"]', '[/box]');
                }
            },
            '{!comcode:INPUT_COMCODE_box;^}'
        );
    }

    function doInputMenu(fieldName) {
        $cms.ui.prompt(
            '{!javascript:ENTER_MENU_NAME;^,' + (document.getElementById(fieldName).form.menu_items.value) + '}',
            '',
            function (va) {
                if (va) {
                    $cms.ui.prompt(
                        '{!javascript:ENTER_MENU_CAPTION;^}',
                        '',
                        function (vb) {
                            if (!vb) {
                                vb = '';
                            }

                            var add;
                            var element = document.getElementById(fieldName);
                            add = '[block="' + $cms.filter.comcode(va) + '" caption="' + $cms.filter.comcode(vb) + '" type="tree"]menu[/block]';
                            $editing.insertTextbox(element, add);
                        },
                        '{!comcode:INPUT_COMCODE_menu;^}'
                    );
                }
            },
            '{!comcode:INPUT_COMCODE_menu;^}'
        );
    }

    function doInputBlock(fieldName) {
        var url = '{$FIND_SCRIPT_NOHTTP;,block_helper}?field_name=' + fieldName + $cms.keep();
        url += '&block_type=' + ((!fieldName.includes('edit_panel_') && !window.location.href.includes(':panel_')) ? 'main' : 'side');

        return $cms.ui.open($util.rel($cms.maintainThemeInLink(url)), '', 'width=800,height=auto,status=no,resizable=yes,scrollbars=yes', null, '{!INPUTSYSTEM_CANCEL;^}');
    }

    function doInputComcode(fieldName, tag, extraQueryString) {
        var attributes = {},
            defaultEmbed = null,
            saveToId = null,
            element = document.getElementById(fieldName);

        if ($cms.form.isWysiwygField(element)) {
            var selection = window.wysiwygEditors[fieldName].getSelection(),
                ranges = selection.getRanges();

            if (ranges[0] !== undefined) {
                var comcodeElement = ranges[0].startContainer.$;
                do {
                    // eslint-disable-next-line no-restricted-properties
                    var matches = comcodeElement.nodeName.toLowerCase().match(/^comcode-(\w+)/);
                    if (matches !== null) {
                        if (tag == null) {
                            tag = matches[1];
                        }

                        for (var i = 0; i < comcodeElement.attributes.length; i++) {
                            if (comcodeElement.attributes[i].name !== 'id') {
                                attributes[comcodeElement.attributes[i].name] = comcodeElement.attributes[i].value;
                            }
                        }

                        defaultEmbed = $dom.html(comcodeElement);

                        if (comcodeElement.id === '') {
                            comcodeElement.id = 'comcode_' + Date.now();
                        }
                        saveToId = comcodeElement.id;

                        break;
                    }

                    comcodeElement = comcodeElement.parentNode;
                } while (comcodeElement != null);
            }
        }

        var url = '{$FIND_SCRIPT_NOHTTP;,comcode_helper}?field_name=' + encodeURIComponent(fieldName);
        if (tag) {
            url += '&tag=' + encodeURIComponent(tag);
        }
        if (defaultEmbed !== null) {
            url += '&type=replace';
        } else {
            if (tag == null) {
                url += '&type=step1';
            } else {
                url += '&type=step2';
            }
        }
        if ($cms.form.isWysiwygField(document.getElementById(fieldName))) {
            url += '&in_wysiwyg=1';
        }

        for (var key in attributes) {
            url += '&default_' + key + '=' + encodeURIComponent(attributes[key]);
        }
        if (defaultEmbed !== null) {
            url += '&default=' + encodeURIComponent(defaultEmbed);
        }
        if (saveToId !== null) {
            url += '&save_to_id=' + encodeURIComponent(saveToId);
        }
        url += $cms.keep();

        if (extraQueryString != null) {
            url += extraQueryString;
        }

        $cms.ui.open($util.rel($cms.maintainThemeInLink(url)), '', 'width=800,height=auto,status=no,resizable=yes,scrollbars=yes', null, '{!INPUTSYSTEM_CANCEL;^}');
    }

    function doInputList(fieldName, add) {
        add = arrVal(add);

        var post = document.getElementById(fieldName);

        return $editing.insertTextbox(post, '\n').then(function () {
            return $cms.ui.prompt('{!javascript:ENTER_LIST_ENTRY;^}', '', null, '{!comcode:INPUT_COMCODE_list;^}');
        }).then(function (va) {
            if (va) {
                add.push(va);
                return doInputList(fieldName, add);
            }

            if (add.length === 0) {
                return;
            }

            var promiseCalls = [];

            promiseCalls.push(function () {
                if (post.value.includes('[semihtml')) {
                    return $editing.insertTextbox(post, '[list]\n');
                }
            });

            add.forEach(function (entryName) {
                promiseCalls.push(function () {
                    if (post.value.includes('[semihtml')) {
                        return $editing.insertTextbox(post, '[*]' + entryName + '\n');
                    } else {
                        return $editing.insertTextbox(post, ' - ' + entryName + '\n');
                    }
                });
            });

            promiseCalls.push(function () {
                if (post.value.includes('[semihtml')) {
                    return $editing.insertTextbox(post, '[/list]\n');
                }
            });

            return $util.promiseSequence(promiseCalls);
        });
    }

    function doInputHide(fieldName) {
        $cms.ui.prompt('{!javascript:ENTER_WARNING;^}', '', null, '{!comcode:INPUT_COMCODE_hide;^}').then(function (va) {
            if (va) {
                var element = document.getElementById(fieldName);

                if ($editing.getSelectedText(element) !== '') {
                    $editing.insertTextbox(element, '[hide="' + $cms.filter.comcode(va) + '"]', '[/hide]');
                    return;
                }

                $cms.ui.prompt(
                    '{!javascript:ENTER_HIDDEN_TEXT;^}',
                    '',
                    function (vb) {
                        if (vb) {
                            $editing.insertTextbox(element, '[hide="' + $cms.filter.comcode(va) + '"]' + $cms.filter.comcode(vb) + '[/hide]');
                        }
                    },
                    '{!comcode:INPUT_COMCODE_hide;^}'
                );
            }
        });
    }

    function doInputThumb(fieldName, url) {
        fieldName = strVal(fieldName);
        url = strVal(url);

        if ((window.startSimplifiedUpload !== undefined) && (document.getElementById(fieldName).name !== 'message')) {
            var test = window.startSimplifiedUpload(fieldName);
            if (test) {
                return;
            }
        }

        var answer;
        $cms.ui.prompt('{!javascript:ENTER_URL;^}', url, null, '{!comcode:INPUT_COMCODE_img;^}').then(function (_url) {
            url = strVal(_url);

            if (!url) {
                return $util.promiseHalt();
            }

            if (!url.includes('://')) {
                $cms.ui.alert('{!javascript:NOT_A_URL;^}').then(function () {
                    doInputUrl(fieldName, url);
                });
                return $util.promiseHalt();
            }

            return $cms.ui.generateQuestionUi(
                '{!javascript:THUMB_OR_IMG_2;^}',
                { 'buttons/thumbnail': '{!THUMBNAIL;^}', 'buttons/full-size': '{!IMAGE;^}' },
                '{!comcode:INPUT_COMCODE_img;^}'
            );
        }).then(function (_answer) {
            answer = strVal(_answer);
            return $cms.ui.prompt('{!javascript:ENTER_IMAGE_CAPTION;^}', '', null, '{!comcode:INPUT_COMCODE_img;^}');
        }).then(function (caption) {
            caption = strVal(caption);

            var element = document.getElementById(fieldName);
            if (answer.toLowerCase() === '{!IMAGE;^}'.toLowerCase()) {
                $editing.insertTextbox(element, '[img="' + $cms.filter.comcode(caption) + '"]' + $cms.filter.comcode(url) + '[/img]');
            } else {
                $editing.insertTextbox(element, '[thumb caption="' + $cms.filter.comcode(caption) + '"]' + $cms.filter.comcode(url) + '[/thumb]');
            }
        });
    }

    function doInputAttachment(fieldName) {
        $cms.ui.prompt(
            '{!javascript:ENTER_ATTACHMENT;^}',
            '',
            function (val) {
                val = Number(val);

                if (!Number.isInteger(val)) {
                    $cms.ui.alert('{!javascript:NOT_VALID_ATTACHMENT;^}');
                } else {
                    var element = document.getElementById(fieldName);
                    $editing.insertTextbox(element, '[attachment]new_' + val + '[/attachment]');
                }
            },
            '{!comcode:INPUT_COMCODE_attachment;^}'
        );
    }

    function doInputUrl(fieldName, va) {
        $cms.ui.prompt('{!javascript:ENTER_URL;^}', va, null, '{!comcode:INPUT_COMCODE_url;^}').then(function (url) {
            if (url == null) {
                return; // Cancel clicked
            }

            url = strVal(url);

            if (!url.includes('://')) {
                $cms.ui.alert('{!javascript:NOT_A_URL;^}').then(function () {
                    doInputUrl(fieldName, url);
                });
                return;
            }

            $cms.ui.prompt('{!javascript:ENTER_LINK_NAME;^}', '', null, '{!comcode:INPUT_COMCODE_url;^}').then(function (linkName) {
                var element = document.getElementById(fieldName);
                if (linkName != null) {
                    $editing.insertTextbox(element, '[url="' + $cms.filter.comcode(linkName) + '"]' + $cms.filter.comcode(url) + '[/url]');
                }
            });
        });
    }

    function doInputPage(fieldName) {
        var result;

        if ($cms.configOption('js_overlays')) {
            $cms.ui.showModalDialog($util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,page_link_chooser}' + $cms.keep(true))), null, 'dialogWidth=600;dialogHeight=400;status=no;unadorned=yes').then(function (result) {
                if (result == null) {
                    return;
                }

                $cms.ui.prompt('{!javascript:ENTER_CAPTION;^}', result.replace(/^[^ ]* /, ''), null, '{!comcode:INPUT_COMCODE_page;^}').then(function (vc) {
                    var element = document.getElementById(fieldName);

                    if ($editing.getSelectedText(element) !== '') {
                        _doInputPage(fieldName, result.replace(/ .*/, ''), '');
                        return;
                    }

                    _doInputPage(fieldName, result, (vc == null) ? '' : vc);
                });
            });

            return;
        }

        $cms.ui.prompt('{!javascript:ENTER_ZONE;^}', '', null, '{!comcode:INPUT_COMCODE_page;^}').then(function (va) {
            if (va !== null) {
                $cms.ui.prompt('{!javascript:ENTER_PAGE;^}', '').then(function (vb) {
                    if (vb !== null) {
                        var element = document.getElementById(fieldName);
                        result = va + ':' + vb;

                        if ($editing.getSelectedText(element) !== '') {
                            _doInputPage(fieldName, result, '');
                            return;
                        }

                        $cms.ui.prompt(
                            '{!javascript:ENTER_CAPTION;^}',
                            '',
                            function (vc) {
                                _doInputPage(fieldName, result, (vc == null) ? '' : vc);
                            },
                            '{!comcode:INPUT_COMCODE_page;^}'
                        );
                    }
                });
            }
        });

        function _doInputPage(fieldName, result, vc) {
            var element = document.getElementById(fieldName);
            if (vc === '') {
                $editing.insertTextboxWrapping(element, '[page="' + $cms.filter.comcode(result) + '"]', '[/page]');
            } else {
                $editing.insertTextbox(element, '[page="' + $cms.filter.comcode(result) + '"]' + $cms.filter.comcode(vc) + '[/page]');
            }
        }
    }

    function doInputEmail(fieldName, va) {
        $cms.ui.prompt(
            '{!javascript:ENTER_ADDRESS;^}',
            va,
            function (va) {
                if ((va != null) && (va.indexOf('@') === -1)) {
                    $cms.ui.alert('{!javascript:NOT_A_EMAIL;^}').then(function () {
                        doInputUrl(fieldName, va);
                    });
                    return;
                }

                if (va !== null) {
                    var element = document.getElementById(fieldName);

                    if ($editing.getSelectedText(element) !== '') {
                        $editing.insertTextbox(element, '[email="' + $cms.filter.comcode(va) + '"]', '[/email]');
                        return;
                    }

                    $cms.ui.prompt(
                        '{!javascript:ENTER_CAPTION;^}',
                        '',
                        function (vb) {
                            if (vb !== null) {
                                $editing.insertTextbox(element, '[email="' + $cms.filter.comcode(vb) + '"]' + $cms.filter.comcode(va) + '[/email]');
                            }
                        },
                        '{!comcode:INPUT_COMCODE_email;^}'
                    );
                }
            },
            '{!comcode:INPUT_COMCODE_email;^}'
        );
    }

    function doInputB(fieldName) {
        var element = document.getElementById(fieldName);
        return $editing.insertTextboxWrapping(element, 'b', '');
    }

    function doInputI(fieldName) {
        var element = document.getElementById(fieldName);
        return $editing.insertTextboxWrapping(element, 'i', '');
    }

    function doInputFont(fieldName) {
        var element = document.getElementById(fieldName);
        var form = element.form;
        var face = form.elements['f_face'];
        var size = form.elements['f_size'];
        var colour = form.elements['f_colour'];
        if ((face.value === '') && (size.value === '') && (colour.value === '')) {
            $cms.ui.alert('{!javascript:NO_FONT_SELECTED;^}');
            return;
        }
        return $editing.insertTextboxWrapping(document.getElementById(fieldName), '[font="' + $cms.filter.comcode(face.value) + '" color="' + $cms.filter.comcode(colour.value) + '" size="' + $cms.filter.comcode(size.value) + '"]', '[/font]', true);
    }

    // ==================
    // Auto-saving/drafts
    // ==================

    /*
     We support both remote and local saving.

     The advantage of local saving is that it works when you're offline or the server is offline.
     Also, it is faster because no load/save network requests are required.

     The advantage of remove saving is you can switch machines.
     */

    $posting.initFormSaving = function initFormSaving(formId) {
        window.lastAutosave = new Date();

        //$util.inform('Initialising auto-save subsystem');

        // Go through all forms/elements
        var form = document.getElementById(formId);
        for (var i = 0; i < form.elements.length; i++) {
            if (fieldSupportsAutosave(form.elements[i])) {
                // Register events for auto-save
                form.elements[i].addEventListener('keypress', handleFormSaving);
                form.elements[i].addEventListener('blur', handleFormSaving);
                form.elements[i].externalOnKeyPress = handleFormSaving;
            }
        }

        // Register event for explicit draft save
        document.body.addEventListener('keydown', (function (form) {
            return function (event) {
                handleFormSavingExplicit(event, form);
            };
        }(form)));

        // Load via local storage
        var autosaveValue = $cms.readCookie(encodeURIComponent(getAutosaveUrlStem()));
        if ((autosaveValue !== '') && (autosaveValue !== '0')) {
            if (window.localStorage !== undefined) {
                var fieldsToDo = {}, fieldsToDoCounter = 0, biggestLengthData = '';
                var value;
                var elementName, autosaveName;
                for (var j = 0; j < form.elements.length; j++) {
                    elementName = (form.elements[j].name === undefined) ? form.elements[0][j].name : form.elements[j].name;
                    autosaveName = getAutosaveName(elementName);
                    if (localStorage[autosaveName] !== undefined) {
                        value = localStorage[autosaveName];

                        if (form.elements[j].value != null && form.elements[j].value.replace(/\s/g, '') === value.replace(/\s/g, '')) {
                            continue;
                        }

                        fieldsToDo[elementName] = value;

                        fieldsToDoCounter++;

                        if (value.length > biggestLengthData.length) {// The longest is what we quote to the user as being restored
                            biggestLengthData = value;
                        }

                        //$util.inform('+ Has autosave for ' + elementName + ' (' + autosaveName + ')');
                    } else {
                        //$util.inform('- Has no autosave for ' + elementName);
                    }
                }

                if ((fieldsToDoCounter !== 0) && (biggestLengthData.length > 25)) {
                    _restoreFormAutosave(form, fieldsToDo, biggestLengthData);
                    return; // If we had it locally, we won't let it continue on to try via AJAX
                } else {
                    //$util.inform('No auto-save, fields found was ' + fieldsToDoCounter + ', largest length was ' + biggestLengthData.length);
                }
            }
        } else {
            //$util.inform('Nothing in local storage');
        }

        // Load via AJAX (if issue happened on another machine, or if we do not support local storage)
        if (navigator.onLine) {
            //$util.inform('Searching AJAX for auto-save');

            var url = '{$FIND_SCRIPT_NOHTTP;,autosave}?type=retrieve';
            url += '&stem=' + encodeURIComponent(getAutosaveUrlStem());
            url += $cms.keep();
            var callback = (function (form) {
                return function (responseXML) {
                    var result = responseXML && responseXML.querySelector('result');
                    //$util.inform('Auto-save AJAX says', result);
                    _retrieveFormAutosave(result, form);
                };
            }(form));
            $cms.doAjaxRequest(url, [callback]);
        }

        function handleFormSavingExplicit(event, form) {
            if (event.keyCode === 83/*s*/ && (navigator.platform.match('Mac') ? event.metaKey : event.ctrlKey) && (!navigator.platform.match('Mac') ? event.ctrlKey : event.metaKey) && (!event.altKey)) {
                //$util.inform('Doing explicit auto-save');

                event.preventDefault(); // Prevent browser save dialog

                // Go through al fields to save
                var post = '', foundValidatedField = false;
                for (var i = 0; i < form.elements.length; i++) {
                    if (form.elements[i].name === 'validated') {
                        foundValidatedField = true;
                    }

                    if (fieldSupportsAutosave(form.elements[i])) {
                        // As this is forced, callback is not async, so despite it being in a for loop, this will not cause problems.
                        _handleFormSaving(event, form.elements[i], true, postFormSaving);
                    }
                }

                if (post !== '') {
                    document.body.style.cursor = 'wait';

                    // Save remotely
                    if (navigator.onLine) {
                        if ($cms.form.isModSecurityWorkaroundEnabled()) {
                            post = $cms.form.modSecurityWorkaroundAjax(post);
                        }
                        $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,autosave}?type=store' + $cms.keep(), null, post).then(function () {
                            if (document.body.style.cursor === 'wait') {
                                document.body.style.cursor = '';
                            }

                            var message = foundValidatedField ? '{!javascript:DRAFT_SAVED_WITH_VALIDATION;^}' : '{!javascript:DRAFT_SAVED_WITHOUT_VALIDATION;^}';
                            $cms.ui.alert(message, '{!javascript:DRAFT_SAVE;^}');
                        });
                    }
                }
            }

            function postFormSaving (temp) {
                if (temp) {
                    if (post !== '') {
                        post += '&';
                    }
                    post += encodeURIComponent(temp[0]) + '=' + encodeURIComponent(temp[1]);
                }
            }
        }

        function _retrieveFormAutosave(result, form) {
            var fieldsToDo = {}, fieldsToDoCounter = 0, biggestLengthData = '';
            var key, value;
            var fields = result.getElementsByTagName('field');
            var element, elementName, autosaveName;
            for (var i = 0; i < fields.length; i++) {
                key = fields[i].getAttribute('key');
                value = fields[i].getAttribute('value');

                element = null;
                for (var j = 0; j < form.elements.length; j++) {
                    elementName = (form.elements[j].name === undefined) ? form.elements[0][j].name : form.elements[j].name;
                    autosaveName = getAutosaveName(elementName);
                    if (autosaveName === key) {
                        element = form.elements[j];
                        break;
                    }
                }

                if (element) {
                    if (element.value != null && element.value.replace(/\s/g, '') === value.replace(/\s/g, '')) {
                        continue;
                    }

                    fieldsToDo[elementName] = value;

                    fieldsToDoCounter++;

                    if (value.length > biggestLengthData.length) // The longest is what we quote to the user as being restored
                    {
                        biggestLengthData = value;
                    }
                }
            }

            if ((fieldsToDoCounter !== 0) && (biggestLengthData.length > 25)) {
                _restoreFormAutosave(form, fieldsToDo, biggestLengthData);
            } else {
                //$util.inform('No auto-save, fields found was ' + fieldsToDoCounter + ', largest length was ' + biggestLengthData.length);
            }
        }

        function _restoreFormAutosave(form, fieldsToDo, biggestLengthData) {
            var autosaveName;

            // If we've found something to restore then invite user to restore it
            biggestLengthData = biggestLengthData.replace(/<[^>]*>/g, '').replace(/\n/g, ' ').replace(/&nbsp;/g, ' '); // Strip HTML and new lines
            if (biggestLengthData.length > 100) { // Trim down if needed
                biggestLengthData = biggestLengthData.substr(0, 100) + '...';
            }

            $cms.ui.confirm('{!javascript:RESTORE_SAVED_FORM_DATA;^}\n\n' + biggestLengthData, null, '{!javascript:AUTO_SAVING;^}').then(function (result) {
                if (result) {
                    for (var key in fieldsToDo) {
                        if (typeof fieldsToDo[key] !== 'string') {
                            continue;
                        }

                        if (form.elements[key] !== undefined) {
                            //$util.inform('Restoring ' + key);
                            cleverSetValue(form, form.elements[key], fieldsToDo[key]);
                        }
                    }
                } else {
                    // Was asked to throw the autosave away...

                    $cms.setCookie(encodeURIComponent(getAutosaveUrlStem()), '0', 0.167/*4 hours*/); // Mark as not wanting to restore from local storage

                    if (window.localStorage !== undefined) {
                        for (var key2 in fieldsToDo) {
                            if (typeof fieldsToDo[key2] !== 'string') {
                                continue;
                            }

                            autosaveName = getAutosaveName(key2);
                            if (localStorage[autosaveName] !== undefined) {
                                delete localStorage[autosaveName];
                            }
                        }
                    }
                }
            });
        }

        function cleverSetValue(form, element, value) {
            // eslint-disable-next-line no-restricted-properties
            if ((element.length !== undefined) && (element.nodeName === undefined)) {
                // Radio button
                element = element[0];
            }

            switch (element.localName) {
                case 'textarea':
                    $editing.setTextbox(element, value, value);
                    break;
                case 'select':
                    for (var i = 0; i < element.options.length; i++) {
                        if (element.options[i].value === value) {
                            element.selectedIndex = i;
                            if (window.jQuery && window.jQuery.fn.select2 !== undefined) {
                                window.jQuery(element).trigger('change');
                            }
                        }
                    }
                    break;
                case 'input':
                    switch (element.type) {
                        case 'checkbox':
                            element.checked = (value !== '');
                            break;

                        case 'radio':
                            value = '';
                            for (var j = 0; j < form.elements.length; j++) {
                                if ((form.elements[j].name === element.name) && (form.elements[j].value === value)) {
                                    form.elements[j].checked = true;
                                }
                            }
                            break;

                        case 'text':
                        case 'color':
                        case 'date':
                        case 'datetime':
                        case 'datetime-local':
                        case 'email':
                        case 'month':
                        case 'number':
                        case 'range':
                        case 'search':
                        case 'tel':
                        case 'time':
                        case 'url':
                        case 'week':
                            element.value = value;
                            break;
                    }
            }

            $dom.trigger(element, 'change');
        }

        function fieldSupportsAutosave(element) {
            // eslint-disable-next-line no-restricted-properties
            if ((element.length !== undefined) && (element.nodeName === undefined)) {
                // Radio button
                element = element[0];
            }

            if (element.name === undefined) {
                return false;
            }

            var name = element.name;
            if (name === '') {
                return false;
            }
            if (name.substr(-2) === '[]') {
                return false;
            }

            if ($cms.form.isWysiwygField(element)) {
                return true;
            }

            if (element.disabled) {
                return false;
            }

            switch (element.localName) {
                case 'textarea':
                case 'select':
                    return true;
                case 'input':
                    switch (element.type) {
                        case 'checkbox':
                        case 'radio':
                        case 'text':
                        case 'color':
                        case 'date':
                        case 'datetime':
                        case 'datetime-local':
                        case 'email':
                        case 'month':
                        case 'number':
                        case 'range':
                        case 'tel':
                        case 'time':
                        case 'url':
                        case 'week':
                            return true;
                    }
            }

            return false;
        }
    };

    function handleFormSaving(event, element, force) {
        _handleFormSaving(event, element, force, postFormSaving);

        function postFormSaving (temp) {
            if (temp) {
                var post = encodeURIComponent(temp[0]) + '=' + encodeURIComponent(temp[1]);
    
                // Save remotely
                if (navigator.onLine) {
                    $util.inform('Doing AJAX auto-save');
    
                    if ($cms.form.isModSecurityWorkaroundEnabled()) {
                        post = $cms.form.modSecurityWorkaroundAjax(post);
                    }
                    $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,autosave}?type=store' + $cms.keep(), null, post);
                }
            }
        }
    }

    function _handleFormSaving(event, element, force, cb) {
        if (force === undefined) {
            force = (event.type === 'blur');
        }

        var thisDate = new Date();
        //var saveFrequency = parseInt($cms.configOption('autosave_time')); TODO: Tracker #5268 uncomment when you figure out how to make this work
        var saveFrequency = 20;

        if (element === undefined) {
            element = event.target;
        }
        if ((element === undefined) || (element === null)) {
            return cb(null); // Some weird error, perhaps an extension fired this event
        }

        var value = $cms.form.cleverFindValue(element.form, element);
        if ((event.type === 'keypress') && (isTypedInput(element))) {
            value += String.fromCharCode(event.keyCode ? event.keyCode : event.charCode);
        }

        var elementName = (element.name === undefined) ? element[0].name : element.name;
        var autosaveName = getAutosaveName(elementName);

        if (!force) {
            clearTimeout($posting.formSaveTimer);
            $posting.formSaveTimer = setTimeout(checkSaveTime, 1000);

            return null;
        }

        actuallyAutosave();
        return cb([autosaveName, value]);

        function isTypedInput(element) {
            // eslint-disable-next-line no-restricted-properties
            if ((element.length !== undefined) && (element.nodeName === undefined)) {
                // Radio button
                element = element[0];
            }

            switch (element.localName) {
                case 'textarea':
                    return true;
                case 'input':
                    switch (element.type) {
                        case 'hidden':
                        case 'text':
                        case 'color':
                        case 'date':
                        case 'datetime':
                        case 'datetime-local':
                        case 'email':
                        case 'month':
                        case 'number':
                        case 'range':
                        case 'tel':
                        case 'time':
                        case 'url':
                        case 'week':
                            return true;
                    }
            }

            return false;
        }

        function actuallyAutosave() {
            // Mark it as saved, so the server can clear it out when we submit, signally local storage should get deleted too
            $cms.setCookie(encodeURIComponent(getAutosaveUrlStem()), '1', 0.167/*4 hours*/);

            window.lastAutosave = thisDate;

            // Save locally
            if (window.localStorage !== undefined) {
                if ($cms.isDevMode()) {
                    //$util.inform('Doing local storage auto-save for ' + elementName + ' (' + autosaveName + ')');
                }

                try {
                    window.localStorage.setItem(autosaveName, value);
                } catch (e) {} // Could have NS_ERROR_DOM_QUOTA_REACHED
            }
        }

        function checkSaveTime () {
            if ((thisDate.getTime() - window.lastAutosave.getTime()) < saveFrequency * 1000) {
                return cb(null); // Only save every configured number of seconds
            }
            actuallyAutosave();
            cb([autosaveName, value]);
        }
    }

    function getAutosaveUrlStem() {
        var name = 'cms_autosave_' + window.location.pathname;
        if ((window.location.search.indexOf('type=') !== -1) || (window.location.search.indexOf('page_link') !== -1)/*editing Comcode page*/) {
            name += window.location.search.replace(/[?&]redirect=.*/, '').replace(/[?&]keep_\w+=.*/, '').replace(/[?&]cat=.*/, '');
        }

        // PHP can't use dots in field names, plus web application firewalls may not like special symbols
        name = name.replace(/[\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f]/g, '_'); // eslint-disable-line no-control-regex

        return name;
    }

    function getAutosaveName(fieldName) {
        return getAutosaveUrlStem() + ':' + fieldName;
    }
}(window.$cms, window.$util, window.$dom));
