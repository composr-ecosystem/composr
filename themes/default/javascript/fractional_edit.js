(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.fractionalEdit = function fractionalEdit(params, el) {
        var explicitEditingLinks = Boolean(params.explicitEditingLinks),
            url = strVal(params.url),
            editText = strVal(params.editText),
            editParamName = strVal(params.editParamName),
            editType = strVal(params.editType);

        if (!explicitEditingLinks) {
            $dom.on(el, 'click', function (e) {
                _fractionalEdit(e, el, url, editText, editParamName, null, null, editType);
            });

            $dom.on(el, 'mouseover mouseout', function (e, target) {
                if (e.relatedTarget && target.contains(e.relatedTarget)) {
                    return;
                }

                if (e.type === 'mouseover') {
                    window.oldStatus = window.status;
                    window.status = '{!SPECIAL_CLICK_TO_EDIT;}';
                    el.classList.add('fractional-edit');
                    el.classList.remove('fractional-edit-nonover');
                } else {
                    window.status = window.oldStatus;
                    el.classList.remove('fractional-edit');
                    el.classList.add('fractional-edit-nonover');
                }
            });
        } else {
            $dom.on(el, 'click', function (e) {
                _fractionalEdit(e, el.previousElementSibling.previousElementSibling, url, editText, editParamName, null, null, editType);
            });
        }
    };


    function _fractionalEdit(event, object, url, rawText, editParamName, wasDoubleClick, controlButton, type) {
        wasDoubleClick = Boolean(wasDoubleClick);
        type = strVal(type) || 'line';

        if (rawText.length > 255) {
            // Cannot process this
            return;
        }

        if (!$cms.magicKeypress(event) && !wasDoubleClick && (object === event.target)) {
            return;
        }

        event.preventDefault();

        // Position form
        var width = object.offsetWidth;
        if (width < 160) {
            width = 160;
        }
        var x = $dom.findPosX(object, true);
        var y = $dom.findPosY(object, true) - 8;

        // Record old JS events
        object.oldOnclick = object.onclick;
        object.oldOndblclick = object.ondblclick;
        object.oldOnkeypress = object.onkeypress;

        // Create form
        var form = document.createElement('form'); // The form is never submitted actually: we use XMLHttpRequest
        form.method = 'post';
        form.action = url;
        form.style.display = 'inline';
        var populatedValue;
        if (object.rawText !== undefined) {
            populatedValue = object.rawText; // Our previous text edited in this JS session
        } else {
            object.rawText = rawText;
            populatedValue = rawText; // What was in the DB when the screen loaded
        }
        var input;
        switch (type) {
            case 'line':
                input = document.createElement('input');
                if (rawText.length > 255) {
                    input.size = '30';
                } else {
                    input.maxlength = '255';
                }
                input.value = populatedValue;
                break;
            case 'textarea':
                input = document.createElement('textarea');
                input.value = populatedValue;
                input.rows = '6';
                break;
            default:
                input = document.createElement('select');
                var listOptions = type.split('|');
                var listOption;
                for (var i = 0; i < listOptions.length; i++) {
                    listOption = document.createElement('option');
                    $dom.html(listOption, $cms.filter.html(listOptions[i]));
                    listOption.selected = (populatedValue === listOptions[i]);
                    input.appendChild(listOption);
                }
                break;
        }
        input.style.position = 'absolute';
        input.style.left = $cms.isMobile() ? '0px' : (x + 'px');
        if (rawText.length <= 255) {
            input.style.width = $cms.isMobile() ? ($dom.getWindowWidth() + 'px') : (width + 'px');
        }
        input.style.top = (y + 8) + 'px';
        input.style.margin = 0;

        var toCopy = ['font-size', 'font-weight', 'font-style'];
        if (type === 'line') {
            toCopy.push('border');
            toCopy.push('border-top');
            toCopy.push('border-right');
            toCopy.push('border-bottom');
            toCopy.push('border-left');
        }

        for (var j = 0; j < toCopy.length; j++) {
            var style = window.getComputedStyle(object.parentNode).getPropertyValue(toCopy[j]);
            if (style !== undefined) {
                input.style[toCopy[j]] = style;
            }
        }
        input.name = editParamName;
        $dom.on(form, 'submit', function (e) {
            e.preventDefault();
        });
        if (controlButton) {
            $dom.html(controlButton, '{!SAVE;^}');
        }

        function cleanupFunction() {
            object.onclick = object.oldOnclick;
            object.ondblclick = object.oldOndblclick;
            object.onkeypress = object.oldOnkeypress;

            if (input.form.parentNode) {
                input.onblur = null; // So don't get recursion
                input.form.parentNode.removeChild(input.form);
            }

            if (controlButton) {
                $dom.html(controlButton, '{!EDIT;^}');

                // To stop it instantly re-clicking
                var backup = controlButton.onclick;
                controlButton.onclick = function (e) {
                    e.preventDefault();
                };
                setTimeout(function () {
                    controlButton.onclick = backup;
                }, 10);
            }
        }

        function cancelFunction() {
            cleanupFunction();

            $cms.ui.alert('{!FRACTIONAL_EDIT_CANCELLED;^}', '{!FRACTIONAL_EDIT;^}');
        }

        function saveFunction() {
            // Call AJAX request
            $cms.doAjaxRequest(input.form.action, null, input.name + '=' + encodeURIComponent(input.value)).then(function (xhr) {
                // Some kind of error?
                if (((xhr.responseText === '') && (input.value !== '')) || (xhr.status !== 200)) {
                    var sessionTestUrl = '{$FIND_SCRIPT_NOHTTP;,confirm_session}';

                    $cms.doAjaxRequest(sessionTestUrl + $cms.keep(true)).then(function (sessionXhr) {
                        if (sessionXhr.responseText) { // If it failed, see if it is due to a non-confirmed session
                            $cms.ui.confirmSession().then(function (sessionConfirmed) {
                                if (sessionConfirmed) {
                                    saveFunction();
                                } else {
                                    cleanupFunction();
                                }
                            });
                        } else {
                            cleanupFunction(); // Has to happen before, as that would cause defocus then refocus, causing a second save attempt
                            $cms.ui.alert((xhr.status === 500) ? xhr.responseText : '{!ERROR_FRACTIONAL_EDIT;^}', '{!FRACTIONAL_EDIT;^}');
                        }
                    });
                } else { // Success
                    object.rawText = input.value;
                    $dom.html(object, xhr.responseText);

                    cleanupFunction();
                }
            });
        }

        // If we activate it again, we actually treat this as a cancellation
        object.onclick = object.ondblclick = function (event) {
            event.preventDefault();

            if ($cms.magicKeypress(event)) {
                cleanupFunction();
            }
        };

        // Cancel or save actions
        if (type === 'line') {
            input.onkeyup = function (event) { // Not using onkeypress because that only works for actual represented characters in the input box
                if ($dom.keyPressed(event, 'Escape')) { // Cancel (escape key)
                    var tmp = input.onblur;
                    input.onblur = null;
                    $cms.ui.confirm('{!javascript:FRACTIONAL_EDIT_CANCEL_CONFIRM;^}', function (result) {
                        if (result) {
                            cancelFunction();
                        } else {
                            input.focus();
                            input.onblur = tmp;
                        }
                    }, '{!CONFIRM_TEXT;^}');
                }

                if ($dom.keyPressed(event, 'Enter') && (this.value !== '')) { // Save
                    saveFunction();
                }
            };
        }
        input.onblur = function () {
            if (this.value !== '' || rawText === '') {
                saveFunction();
            } else {
                cancelFunction();
            }
        };

        // Add in form
        form.appendChild(input);
        var websiteInner = document.body;
        websiteInner.appendChild(form);
        input.focus();
        if (input.select !== undefined) {
            input.select();
        }
    }
}(window.$cms, window.$util, window.$dom));
