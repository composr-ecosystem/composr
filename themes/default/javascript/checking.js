/* Validation code and other general code relating to forms */
(function ($cms, $util, $dom) {
    'use strict';

    /**
     * @memberof $cms.form
     * @param radios
     * @returns {string}
     */
    $cms.form.radioValue = function radioValue(radios) {
        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                return radios[i].value;
            }
        }
        return '';
    };

    /**
     * @memberof $cms.form
     * @param theElement
     * @param errorMsg
     */
    $cms.form.setFieldError = function setFieldError(theElement, errorMsg) {
        errorMsg = strVal(errorMsg);

        if (theElement.name !== undefined) {
            var name = theElement.name,
                errorMsgElementWrapper = getErrorMsgElement(name);

            if ((errorMsg === '') && (name.includes('_hour')) || (name.includes('_minute'))) { // Do not blank out as day/month/year (which comes first) would have already done it
                return;
            }

            if (errorMsgElementWrapper) {
                var errorMsgElement = errorMsgElementWrapper.querySelector('.js-error-message');

                // Make error message visible, if there's an error
                $dom.toggle(errorMsgElementWrapper, (errorMsg !== ''));

                // Changed error message
                if ($dom.html(errorMsgElement) !== $cms.filter.html(errorMsg)) {
                    $dom.empty(errorMsgElement);
                    if (errorMsg !== '') {// If there actually an error
                        theElement.setAttribute('aria-invalid', 'true');

                        // Need to switch tab?
                        var p = errorMsgElementWrapper.parentElement;
                        while (p != null) {
                            if ((errorMsg.substr(0, 5) !== '{!DISABLED_FORM_FIELD;^}'.substr(0, 5)) && (p.id.substr(0, 2) === 'g-') && (p.style.display === 'none')) {
                                $cms.ui.selectTab('g', p.id.substr(2, p.id.length - 2), false, true);
                                break;
                            }
                            p = p.parentElement;
                        }

                        // Set error message
                        errorMsgElement.textContent += errorMsg;
                        errorMsgElement.setAttribute('role', 'alert');

                        // Fade in
                        $dom.fadeIn(errorMsgElementWrapper);

                    } else {
                        theElement.setAttribute('aria-invalid', 'false');
                        errorMsgElementWrapper.setAttribute('role', '');
                    }
                }
            }
        }

        if ($cms.form.isWysiwygField(theElement)) {
            theElement = theElement.parentElement;
        }

        theElement.classList.toggle('is-invalid', (errorMsg !== ''));

        function getErrorMsgElement(id) {
            var errorMsgElement = document.getElementById('error-' + id);

            if (!errorMsgElement) {
                errorMsgElement = document.getElementById('error-' + id.replace(/_day$/, '').replace(/_month$/, '').replace(/_year$/, '').replace(/_hour$/, '').replace(/_minute$/, ''));
            }
            return errorMsgElement;
        }
    };

    /**
     * Whether all Plupload file uploads are complete
     * @memberof $cms.form
     * @param form
     * @return {boolean}
     */
    $cms.form.areUploadsComplete = function areUploadsComplete(form) {
        var plObj, uploadsComplete = true;

        for (var i = 0; i < form.elements.length; i++) {
            plObj = $dom.data(form.elements[i]).pluploadObject;
            if ((plObj != null) && (Number(plObj.total.queued)/*Number of files yet to be uploaded*/ !== 0)) {
                uploadsComplete = false;
                break;
            }
        }

        return uploadsComplete;
    };

    /**
     * @memberof $cms.form
     * @param form
     * @return { Promise }
     */
    $cms.form.whenUploadsComplete = function whenUploadsComplete(form) {
        if ($cms.form.areUploadsComplete(form)) {
            return Promise.resolve();
        }

        return new Promise(function (resolvePromise) {
            var resolved = false;

            arrVal(form.elements).forEach(function (el) {
                var plObj = $dom.data(el).pluploadObject;

                if (plObj == null) {
                    return;
                }

                plObj.bind('FileUploaded', fileUploadedListener);
            });

            function fileUploadedListener(plObj) {
                if (resolved) {
                    plObj.unbind('FileUploaded', fileUploadedListener);
                    return;
                }

                if ($cms.form.areUploadsComplete(form)) {
                    resolvePromise();
                    resolved = true;
                }
            }
        });
    };

    /**
     * @memberof $cms.form
     * @param form
     * @return { Promise }
     */
    $cms.form.startUploads = function startUploads(form) {
        var plObj, scrolled = false;

        for (var i = 0; i < form.elements.length; i++) {
            plObj = $dom.data(form.elements[i]).pluploadObject;

            if ((plObj != null) && (plObj.state === window.plupload.STOPPED) && (plObj.total.queued > 0)) { /* plObj.total.queued is number of files yet to be uploaded. */
                plObj.start(); // Starts uploading the queued files.

                if (!scrolled) {
                    $dom.smoothScroll(document.getElementById(plObj.settings.txtFileName));
                    scrolled = true;
                }
            }
        }

        return $cms.form.whenUploadsComplete(form);
    };

    /**
     * @memberof $cms.form
     * @param form
     * @param analyticEventCategory
     * @returns { Promise<boolean> }
     */
    $cms.form.doFormSubmit = function doFormSubmit(form, analyticEventCategory) {
        return new Promise(function (resolveSubmitPromise) {
            var checkFormPromise = $cms.form.checkForm(form, false);

            checkFormPromise.then(function (valid) {
                if (!valid) {
                    resolveSubmitPromise(false);
                    return $util.promiseHalt();
                }

                if (form.oldAction) {
                    form.action = form.oldAction;
                }
                if (form.oldTarget) {
                    form.target = form.oldTarget;
                }
                if (!form.getAttribute('target')) {
                    form.target = '_top';
                }

                $cms.ui.disableSubmitAndPreviewButtons();

                if ($cms.form.areUploadsComplete(form)) {
                    return Promise.resolve();
                }

                // Uploads pending
                $cms.ui.alert({ notice: '{!javascript:PLEASE_WAIT_WHILE_UPLOADING;^}', single: true });

                return $cms.form.startUploads(form);
            }).then(function () {
                // Refresh CSRF tokens if expired
                if (form.method.toLowerCase() === 'post') {
                    var hoursSincePageLoad = (Date.now() - $cms.pageGenerationTimestamp) / 1000 / 60 / 60 + 0.05 /*A little extra give*/,
                        tokenField = form.elements['csrf_token'],
                        expireFresh = $cms.configOption('csrf_token_expire_fresh'),
                        expireNew = $cms.configOption('csrf_token_expire_new');
                    if (tokenField) {
                        if ((hoursSincePageLoad >= expireNew) || ((expireFresh != 0) && (hoursSincePageLoad >= expireFresh))) {
                            return $cms.getCsrfToken().then(function (text) {
                                tokenField.value = text;
                            });
                        }
                    }
                }
            }).then(function () {
                if (form.method.toLowerCase() === 'get') {
                    /* Remove any stuff that is only in the form for previews if doing a GET request */
                    var previewInputs = $dom.$$(form, 'input[name^="label_for__"], input[name^="tick_on_form__"], input[name^="comcode__"], input[name^="require__"]');

                    previewInputs.forEach(function (input) {
                        $dom.remove(input);
                    });
                }

                var ret = $dom.trigger(form, 'submit');

                if (ret === false) {
                    $cms.ui.enableSubmitAndPreviewButtons();
                    resolveSubmitPromise(false);
                    return;
                }

                if (window.ajaxScreenDetectInterval !== undefined) {
                    clearInterval(window.ajaxScreenDetectInterval);
                    delete window.ajaxScreenDetectInterval;
                }

                if (analyticEventCategory) {
                    $cms.statsEventTrack(null, analyticEventCategory, null).then(function () {
                        resolveSubmitPromise(true);
                        form.submit();
                    });
                } else {
                    resolveSubmitPromise(true);
                    form.submit();
                }
            });
        });
    };

    /**
     * @memberof $cms.form
     * @param { HTMLFormElement } form
     * @param {string|URL} previewUrl
     * @param {boolean} [hasSeparatePreview]
     * @returns { Promise }
     */
    $cms.form.doFormPreview = function doFormPreview(form, previewUrl, hasSeparatePreview) {
        form = $dom.elArg(form);
        previewUrl = $util.url(previewUrl);
        hasSeparatePreview = Boolean(hasSeparatePreview);

        return new Promise(function (resolvePreviewPromise) {
            if (!$dom.$('#preview-iframe')) {
                $cms.ui.alert('{!ADBLOCKER;^}');
                return resolvePreviewPromise(false);
            }

            var checkFormPromise = $cms.form.checkForm(form, true);

            checkFormPromise.then(function (valid) {
                if (!valid) {
                    resolvePreviewPromise(false);
                    return $util.promiseHalt();
                }

                if (window.mobileVersionForPreview !== undefined) {
                    previewUrl.searchParams.set('keep_mobile', (window.mobileVersionForPreview ? 1 : 0));
                }

                var oldAction = form.action;
                if (!form.oldAction) {
                    form.oldAction = oldAction;
                }

                if ($util.url(form.oldAction).searchParams.get('uploading') === '1') {
                    previewUrl.searchParams.set('uploading', '1');
                }

                form.action = $util.srl(previewUrl);

                var oldTarget = form.target || '_top'; // not _self due to edit screen being a frame itself

                if (!form.oldTarget) {
                    form.oldTarget = oldTarget;
                }

                form.target = 'preview-iframe';

                $cms.ui.disableSubmitAndPreviewButtons();

                if ($cms.form.areUploadsComplete(form)) {
                    return Promise.resolve();
                }

                // Uploads pending
                $cms.ui.alert({ notice: '{!javascript:PLEASE_WAIT_WHILE_UPLOADING;^}', single: true });
                return $cms.form.startUploads(form);
            }).then(function () {
                if ($dom.trigger(form, 'submit', { detail: { triggeredByDoFormPreview: true } }) === false) {
                    $cms.ui.enableSubmitAndPreviewButtons();
                    return resolvePreviewPromise(false);
                }

                if (hasSeparatePreview) {
                    var action = $util.url(form.oldAction);
                    action.searchParams.set('preview', 1);
                    form.action = $util.srl(action);
                    resolvePreviewPromise(true);
                    form.submit();
                    return;
                }

                /* Do our loading-animation */
                setInterval($dom.triggerResize, 500);
                /* In case its running in an iframe itself */
                $dom.illustrateFrameLoad('preview-iframe');

                // Turn main post editing back off
                window.$editing.wysiwygSetReadonly('post', true);

                resolvePreviewPromise(true);
                form.submit();
            });
        });
    };

    /**
     * @memberof $cms.form
     * @param el
     * @returns {boolean}
     */
    $cms.form.isWysiwygField = function isWysiwygField(el) {
        return (window.wysiwygEditors != null) && (typeof window.wysiwygEditors === 'object') && (window.wysiwygEditors[el.id] != null) && (typeof window.wysiwygEditors[el.id] === 'object');
    };

    /**
     * @memberof $cms.form
     * @param form
     * @param element
     * @returns {string}
     */
    $cms.form.cleverFindValue = function cleverFindValue(form, element) {
        if ($util.isArrayLike(element) && (element.name === undefined) && (typeof element.value === 'string')) {
            // A RadioNodeList (returned by form.elements[<name of a radio input>])
            return element.value;
        }

        var value = '';
        switch (element.localName) {
            case 'textarea':
                value = window.$editing.getTextbox(element);
                break;
            case 'select':
                value = '';
                if (element.selectedIndex >= 0) {
                    if (element.multiple) {
                        for (var i = 0; i < element.options.length; i++) {
                            if (element.options[i].selected) {
                                if (value !== '') {
                                    value += ',';
                                }
                                value += element.options[i].value;
                            }
                        }
                    } else if (element.selectedIndex >= 0) {
                        value = element.value;
                        if ((value === '') && (element.size > 1)) {
                            value = '-1'; // Fudge, as we have selected something explicitly that is blank
                        }
                    }
                }
                break;
            case 'input':
                switch (element.type) {
                    case 'checkbox':
                        value = (element.checked) ? element.value : '';
                        break;

                    case 'radio':
                        value = '';
                        for (var j = 0; j < form.elements.length; j++) {
                            if ((form.elements[j].name === element.name) && (form.elements[j].checked)) {
                                value = form.elements[j].value;
                            }
                        }
                        break;

                    default:
                        value = element.value;
                        break;
                }
        }

        return value;
    };

    var _lastChangeTimes = {};
    /**
     * @memberof $cms.form
     * @param form
     * @returns { Date }
     */
    $cms.form.lastChangeTime = function lastChangeTime(form) {
        var uid = $util.uid(form);

        if (_lastChangeTimes[uid] === undefined) {
            _lastChangeTimes[uid] = new Date();

            $dom.on(form, 'input change reset', function () {
                _lastChangeTimes[uid] = new Date();
            });
        }

        return _lastChangeTimes[uid];
    };

    /**
     * @memberof $cms.form
     * @param { HTMLFormElement } theForm
     * @param {boolean} [forPreview]
     * @returns { Promise<boolean> }
     */
    $cms.form.checkForm = function checkForm(theForm, forPreview) {
        var deleteElement = $dom.$('#delete');

        // Skip checks if 'delete' checkbox is checked
        if (!forPreview && (deleteElement != null) && (((deleteElement.classList[0] === 'input-radio') && (deleteElement.value !== '0')) || (deleteElement.classList[0] === 'input-tick')) && (deleteElement.checked)) {
            return Promise.resolve(true);
        }

        return new Promise(function (resolveCheckFormPromise) {
            var erroneous = false,
                totalFileSize = 0,
                alerted = false,
                firstFieldWithError = null,
                fieldElements = arrVal(theForm.elements),
                fieldCheckPromiseCalls = [];

            fieldElements.forEach(function (fieldElement) {
                fieldCheckPromiseCalls.push(function () {
                    var checkResult = checkField(fieldElement, theForm);

                    return checkResult.then(function (result) {
                        if (result == null) {
                            return;
                        }

                        erroneous = result.erroneous || erroneous;
                        if (!firstFieldWithError && result.erroneous) {
                            firstFieldWithError = fieldElement;
                        }
                        totalFileSize += result.totalFileSize;
                        alerted = result.alerted || alerted;

                        if (result.erroneous) {
                            if (fieldElement.type === 'radio') {
                                for (var i = 0; i < theForm.elements.length; i++) {
                                    theForm.elements[i].onchange = function () { autoResetError(this); };
                                }
                            } else {
                                fieldElement.onblur = function () { autoResetError(fieldElement); };
                            }
                        }
                    });
                });
            });

            $util.promiseSequence(fieldCheckPromiseCalls).then(function () {
                if ((totalFileSize > 0) && (theForm.elements['MAX_FILE_SIZE']) && (totalFileSize > theForm.elements['MAX_FILE_SIZE'].value)) {
                    if (!erroneous) {
                        firstFieldWithError = fieldElements[fieldElements.length - 1];
                        erroneous = true;
                    }
                    if (!alerted) {
                        $cms.ui.alert($util.format('{!javascript:TOO_MUCH_FILE_DATA;^}', [Math.round(totalFileSize / 1024), Math.round(theForm.elements['MAX_FILE_SIZE'].value / 1024)]));
                        alerted = true;
                    }
                }

                if (erroneous) {
                    if (!alerted) {
                        $cms.ui.alert({ notice: '{!IMPROPERLY_FILLED_IN;^}', single: true });
                    }
                    var posy = $dom.findPosY(firstFieldWithError, true);
                    if (posy === 0) {
                        posy = $dom.findPosY(firstFieldWithError.parentNode, true);
                    }
                    if (posy !== 0) {
                        $dom.smoothScroll(posy - 50, function () {
                            try {
                                firstFieldWithError.focus();
                            } catch (e) {} // Can have exception giving focus on IE for invisible fields
                        });
                    }
                }

                // Try and workaround max_input_vars problem if lots of usergroups
                if (!erroneous) {
                    var deleteE = document.getElementById('delete'),
                        isDelete = deleteE && (deleteE.type === 'checkbox') && deleteE.checked,
                        es = document.getElementsByTagName('select'), selectEl;

                    for (var k = 0; k < es.length; k++) {
                        selectEl = es[k];
                        if ((selectEl.name.match(/^access_\d+_privilege_/)) && ((isDelete) || (selectEl.value === '-1'))) {
                            selectEl.disabled = true;
                        }
                    }
                }

                resolveCheckFormPromise(!erroneous);
            });
        });

        function autoResetError(theElement, recursing) {
            var checkResult = checkField(theElement, theForm);

            checkResult.then(function (result) {
                if ((result != null) && !result.erroneous) {
                    $cms.form.setFieldError(theElement, '');
                }

                if (!recursing && (theElement.classList.contains('date')) && (theElement.name.match(/_(day|month|year)$/))) {
                    var preid = theElement.id.replace(/_(day|month|year)$/, ''),
                        el = document.getElementById(preid + '_day');
                    if (el !== theElement) {
                        autoResetError(el, true);
                    }
                    el = document.getElementById(preid + '_month');
                    if (el !== theElement) {
                        autoResetError(el, true);
                    }
                    el = document.getElementById(preid + '_year');
                    if (el !== theElement) {
                        autoResetError(el, true);
                    }
                }
            });
        }
    };

    /**
     * @param fieldElement
     * @param formElement
     * @return { Promise }
     */
    function checkField(fieldElement, formElement) {
        return new Promise(function (resolveCheckFieldPromise) {
            var myValue,
                required = false,
                erroneous = false,
                errorMsg = '',
                totalFileSize = 0,
                alerted = false;

            // No checking for hidden elements
            if (((fieldElement.type === 'hidden') || (((fieldElement.style.display === 'none') || (fieldElement.parentNode.style.display === 'none') || (fieldElement.parentNode.parentNode.style.display === 'none') || (fieldElement.parentNode.parentNode.parentNode.style.display === 'none')) && (!$cms.form.isWysiwygField(fieldElement)))) && !fieldElement.classList.contains('hidden-but-needed')) {
                return resolveCheckFieldPromise(null);
            }
            // No checking for disabled elements either
            if (fieldElement.disabled) {
                return resolveCheckFieldPromise(null);
            }

            if (fieldElement.type === 'file') {
                // Test file sizes
                if ((fieldElement.files) && (fieldElement.files.item) && (fieldElement.files.item(0)) && (fieldElement.files.item(0).fileSize)) {
                    totalFileSize += fieldElement.files.item(0).fileSize;
                }

                // Test file types
                if ((fieldElement.value) && (fieldElement.name !== 'file_anytype')) {
                    var allowedTypes = '{$VALID_FILE_TYPES;^}'.split(/,/),
                        typeOk = false,
                        theFileType = fieldElement.value.includes('.') ? fieldElement.value.substr(fieldElement.value.lastIndexOf('.') + 1) : '{!NONE;^}';

                    for (var k = 0; k < allowedTypes.length; k++) {
                        if (allowedTypes[k].toLowerCase() === theFileType.toLowerCase()) {
                            typeOk = true;
                        }
                    }
                    if (!typeOk) {
                        errorMsg = $util.format('{!INVALID_FILE_TYPE;^}', [theFileType, '{$VALID_FILE_TYPES}']).replace(/<[^>]*>/g, '').replace(/&[lr][sd]quo;/g, '\'').replace(/,/g, ', ');
                        if (!alerted) {
                            $cms.ui.alert(errorMsg);
                            alerted = true;
                        }
                    }
                }
            }

            // Find whether field is required and value of it
            if (fieldElement.type === 'radio') {
                required = (formElement.elements['require__' + fieldElement.name] != null) && (formElement.elements['require__' + fieldElement.name].value === '1');
            } else {
                required = fieldElement.className.includes('-required');
            }

            myValue = $cms.form.cleverFindValue(formElement, fieldElement);

            // Prepare for custom error messages, stored as HTML5 data on the error message display element
            var errorMsgElement = (fieldElement.name === undefined) ? null : getErrorMsgElement(fieldElement.name),
                isBlank = (required && (myValue.replace(/&nbsp;/g, ' ').replace(/<br\s*\/?>/g, ' ').replace(/\s/g, '') === '')),
                validatePromise = Promise.resolve();

            if ($dom.data(fieldElement).pluploadObject != null) { // Plupload placeholder field
                var plObj = $dom.data(fieldElement).pluploadObject,
                    fileNameField = document.getElementById(plObj.settings.txtFileName);

                if (plObj.settings.required && (fileNameField.value === '')) {
                    $cms.ui.alert({ notice: '{!IMPROPERLY_FILLED_IN;^}', single: true });
                    alerted = true;
                    isBlank = true;
                }
            }

            // Blank?
            if (isBlank) {
                errorMsg = '{!REQUIRED_NOT_FILLED_IN;^}';
            } else {
                // Standard field-type checks
                if (fieldElement.classList.contains('date') && (fieldElement.name.match(/_(day|month|year)$/)) && (myValue !== '')) {
                    var prename = fieldElement.name.replace(/_(day|month|year)$/, ''),
                        _day = formElement.elements[prename + '_day'],
                        _month = formElement.elements[prename + '_month'],
                        _year = formElement.elements[prename + '_year'];

                    if (_day && _month && _year) {
                        var day = _day.value,
                            month = _month.value,
                            year = _year.value,
                            sourceDate = new Date(year, month - 1, day);

                        if (Number(year) !== sourceDate.getFullYear()) {
                            errorMsg = '{!javascript:NOT_A_DATE;^}';
                        }
                        if (Number(month) !== (sourceDate.getMonth() + 1)) {
                            errorMsg = '{!javascript:NOT_A_DATE;^}';
                        }
                        if (Number(day) !== sourceDate.getDate()) {
                            errorMsg = '{!javascript:NOT_A_DATE;^}';
                        }
                    }
                }

                // Shim for HTML5 regexp patterns
                var matches;
                if ((myValue !== '') && fieldElement.getAttribute('pattern') && (!(matches = myValue.match(new RegExp('^' + fieldElement.getAttribute('pattern') + '$'))) || (myValue !== matches[0]))) {
                    errorMsg = $util.format('{!javascript:PATTERN_NOT_MATCHED;^}', [myValue]);
                } else if ((fieldElement.classList.contains('input-username') || fieldElement.classList.contains('input-username-required')) && (myValue !== '') && (myValue !== '****')) {
                    validatePromise = $cms.form.doAjaxFieldTest('{$FIND_SCRIPT_NOHTTP;,username_exists}?username=' + encodeURIComponent(myValue)).then(function (exists) {
                        if (!exists) {
                            errorMsg = $util.format('{!javascript:NOT_USERNAME;^}', [myValue]);
                        }
                    });
                } else if ((fieldElement.classList.contains('input-email') || fieldElement.classList.contains('input-email-required')) && (myValue !== '') && (!myValue.match(/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9._-]+$/))) {
                    errorMsg = $util.format('{!javascript:NOT_A_EMAIL;^}', [myValue]);
                } else if ((fieldElement.classList.contains('input-codename') || fieldElement.classList.contains('input-codename-required')) && (myValue !== '') && (!myValue.match(/^[a-zA-Z0-9._-]*$/))) {
                    errorMsg = $util.format('{!javascript:NOT_CODENAME;^}', [myValue]);
                } else if ((fieldElement.classList.contains('input-integer') || fieldElement.classList.contains('input-integer-required')) && (myValue !== '') && (parseInt(myValue, 10) !== Number(myValue))) {
                    errorMsg = $util.format('{!javascript:NOT_INTEGER;^}', [myValue]);
                } else if ((fieldElement.classList.contains('input-float') || fieldElement.classList.contains('input-float-required')) && (myValue !== '') && (parseFloat(myValue) !== Number(myValue))) {
                    errorMsg = $util.format('{!javascript:NOT_FLOAT;^}', [myValue]);
                }
            }

            validatePromise.then(function () {
                if ((errorMsg !== '') && errorMsgElement && errorMsgElement.getAttribute('data-errorRegexp')) { // Custom error message?
                    errorMsg = errorMsgElement.getAttribute('data-errorRegexp');
                }

                erroneous = erroneous || (errorMsg !== '');

                if (!erroneous) {
                    if (!fieldElement.checkValidity()) {
                        erroneous = true;
                        errorMsg = $util.format('{!javascript:PATTERN_NOT_MATCHED;^}', [myValue]);
                    }
                }

                // Show error?
                $cms.form.setFieldError(fieldElement, errorMsg);

                resolveCheckFieldPromise({
                    erroneous: erroneous,
                    totalFileSize: totalFileSize,
                    alerted: alerted
                });
            });
        });

        function getErrorMsgElement(id) {
            var errorMsgElement = document.getElementById('error-' + id);
            if (!errorMsgElement) {
                errorMsgElement = document.getElementById('error-' + id.replace(/_day$/, '').replace(/_month$/, '').replace(/_year$/, '').replace(/_hour$/, '').replace(/_minute$/, ''));
            }
            return errorMsgElement;
        }
    }

    /**
     * @memberof $cms.form
     * @param field
     * @param isLocked
     * @param chosenOb
     */
    $cms.form.setLocked = function setLocked(field, isLocked, chosenOb) {
        var radioButton = document.getElementById('choose-' + field.name.replace(/\[\]$/, ''));
        if (!radioButton) {
            radioButton = document.getElementById('choose-' + field.name.replace(/_\d+$/, '_'));
        }

        // For All-and-not,Line-multi,Compound-Tick,Radio-List,Date/Time: $cms.form.setLocked assumes that the calling code is clever
        // special input types are coded to observe their master input field readonly status)
        var button = document.getElementById('upload-button-' + field.name.replace(/\[\]$/, ''));

        if (isLocked) {
            var labels = document.getElementsByTagName('label'), label = null;
            for (var i = 0; i < labels.length; i++) {
                if (chosenOb && (labels[i].for === chosenOb.id)) {
                    label = labels[i];
                    break;
                }
            }
            if (!radioButton) {
                if (label) {
                    var labelNice = $dom.html(label).replace('&raquo;', '').replace(/^\s*/, '').replace(/\s*$/, '');
                    if (field.type === 'file') {
                        $cms.form.setFieldError(field, $util.format('{!DISABLED_FORM_FIELD_ENCHANCEDMSG_UPLOAD;^}', [labelNice]));
                    } else {
                        $cms.form.setFieldError(field, $util.format('{!DISABLED_FORM_FIELD_ENCHANCEDMSG;^}', [labelNice]));
                    }
                } else {
                    $cms.form.setFieldError(field, '{!DISABLED_FORM_FIELD;^}');
                }
            }
            field.classList.remove('is-invalid');
        } else if (!radioButton) {
            $cms.form.setFieldError(field, '');
        }
        field.disabled = isLocked;

        if (button) {
            button.disabled = isLocked;
            button.style.pointerEvents = 'none'; // Allows clicking even when disabled
        }
    };

    /**
     * @memberof $cms.form
     * @param fieldName
     * @param isRequired
     */
    $cms.form.setRequired = function setRequired(fieldName, isRequired) {
        fieldName = strVal(fieldName);
        isRequired = Boolean(isRequired);

        var radioButton = $dom.$('#choose-' + fieldName);

        if (!radioButton) {
            var requiredA = $dom.$('#form-table-field-name--' + fieldName),
                requiredB = $dom.$('#required-readable-marker--' + fieldName),
                requiredC = $dom.$('#required-posted--' + fieldName),
                requiredD = $dom.$('#form-table-field-input--' + fieldName);

            if (requiredA) {
                requiredA.className = 'form-table-field-name';

                if (isRequired) {
                    requiredA.classList.add('required');
                }
            }

            if (requiredB) {
                $dom.toggle(requiredB, isRequired);
            }

            if (requiredC) {
                requiredC.value = isRequired ? 1 : 0;
            }

            if (requiredD) {
                requiredD.className = 'form-table-field-input';
            }
        }

        var element = $dom.$('#' + fieldName);

        if (element) {
            element.className = element.className.replace(/(input-[a-z-]+)-required/g, '$1');

            if (isRequired) {
                element.className = element.className.replace(/(input-[a-z-]+)/g, '$1-required');
            }

            if ($dom.data(element).pluploadObject != null) {
                $dom.data(element).pluploadObject.settings.required = isRequired;
            }
        }

        if (!isRequired) {
            var error = $dom.$('#error__' + fieldName);
            if (error) {
                error.style.display = 'none';
            }
        }
    };

    /**
     * @memberof $cms.form
     * @param context
     */
    $cms.form.disablePreviewScripts = function disablePreviewScripts(context) {
        if (context === undefined) {
            context = document;
        }

        var elements, i;

        elements = $dom.$$(context, 'button, input[type="button"], input[type="image"]');
        for (i = 0; i < elements.length; i++) {
            elements[i].addEventListener('click', alertNotInPreviewMode);
        }

        // Make sure links in the preview don't break it - put in a new window
        elements = $dom.$$(context, 'a');
        for (i = 0; i < elements.length; i++) {
            if (elements[i].href && elements[i].href.includes('://')) {
                try {
                    if (!elements[i].href.toLowerCase().startsWith('javascript:') && (elements[i].target !== '_self') && (elements[i].target !== '_blank')) { // guard due to JS actions still opening new window in some browsers
                        elements[i].target = 'false_blank'; // Real _blank would trigger annoying CSS. This is better anyway.
                    }
                } catch (ignore) {} // IE can have security exceptions
            }
        }

        function alertNotInPreviewMode() {
            $cms.ui.alert('{!NOT_IN_PREVIEW_MODE;^}');
            return false;
        }
    };

    /**
     * Set it up so a form field is known and can be monitored for changes
     * @memberof $cms.form
     * @param container
     */
    $cms.form.setUpChangeMonitor = function setUpChangeMonitor(container) {
        var firstInp = $dom.$(container, 'input, select, textarea');

        if (!firstInp || firstInp.id.includes('choose-')) {
            return;
        }

        $dom.on(container, 'focusout change', function () {
            container.classList.toggle('filledin', $cms.form.findIfChildrenSet(container));
        });
    };

    /**
     * @memberof $cms.form
     * @param container
     * @returns {boolean}
     */
    $cms.form.findIfChildrenSet = function findIfChildrenSet(container) {
        var value, blank = true, el,
            elements = $dom.$$(container, 'input, select, textarea');

        for (var i = 0; i < elements.length; i++) {
            el = elements[i];
            if (((el.type === 'hidden') || ((el.style.display === 'none') && !$cms.form.isWysiwygField(el))) && !el.classList.contains('hidden-but-needed')) {
                continue;
            }
            value = $cms.form.cleverFindValue(el.form, el);
            blank = blank && (value === '');
        }
        return !blank;
    };


    /**
     * Very simple form control flow.
     * Shows the associated error message element if the field is blank, and an alert dialog - unless `alreadyShownMessage` is true.
     * @memberof $cms.form
     * @param field
     * @param alreadyShownMessage
     * @returns {boolean} - true if the field isn't empty, false otherwise
     */
    $cms.form.checkFieldForBlankness = function checkFieldForBlankness(field, alreadyShownMessage) {
        field = $dom.domArg(field);
        alreadyShownMessage = Boolean(alreadyShownMessage);

        var value = field.value,
            errorEl = $dom.$('#error-' + field.id);

        if ((value.trim() === '') || (value === '{!POST_WARNING;^}') || (value === '{!THREADED_REPLY_NOTICE;^,{!POST_WARNING}}')) {
            if (errorEl != null) {
                $dom.show(errorEl);
                $dom.html(errorEl, '{!REQUIRED_NOT_FILLED_IN;^}');
            }

            if (!alreadyShownMessage) {
                $cms.ui.alert({ notice: '{!IMPROPERLY_FILLED_IN;^}', single: true });
            }

            return false;
        }

        if (errorEl != null) {
            $dom.hide(errorEl);
        }

        return true;
    };

}(window.$cms, window.$util, window.$dom));
