(function () {
    'use strict';

    /**
     * Validation code and other general code relating to forms
     * @namespace $cms.form
     */
    $cms.form || ($cms.form = {});

    /**
     * Calls up a URL to check something, giving any 'feedback' as an error (or if just 'false' then returning false with no message)
     * @memberof $cms.form
     * @param url
     * @param post
     * @returns { Promise<Boolean> }
     */
    $cms.form.doAjaxFieldTest = function doAjaxFieldTest(url, post) {
        url = strVal(url);

        return new Promise(function (resolve) {
            $cms.doAjaxRequest(url, null, post).then(function (xhr) {
                if ((xhr.responseText !== '') && (xhr.responseText.replace(/[ \t\n\r]/g, '') !== '0'/*some cache layers may change blank to zero*/)) {
                    if (xhr.responseText !== 'false') {
                        if (xhr.responseText.length > 1000) {
                            //$util.inform('$cms.form.doAjaxFieldTest()', 'xhr.responseText:', xhr.responseText);
                            $cms.ui.alert(xhr.responseText, '{!ERROR_OCCURRED;^}', true);
                        } else {
                            $cms.ui.alert(xhr.responseText);
                        }
                    }
                    resolve(false);
                    return;
                }
                resolve(true);
            });
        });
    };

    var formSubmitValidators = new WeakMap();
    var haveAttachedSubmitListener = new WeakSet();
    /**
     * @memberof $cms.form
     * @param { HTMLFormElement } formElement
     * @param { function } validatorFunction
     */
    $cms.form.addSubmitValidator = function addSubmitValidator(formElement, validatorFunction) {
        formElement = $dom.elArg(formElement);

        var validatorsArray = formSubmitValidators.get(formElement);

        if (validatorsArray == null) {
            validatorsArray = [];
            formSubmitValidators.set(formElement, validatorsArray);
        }

        validatorsArray.push(validatorFunction);

        if (!haveAttachedSubmitListener.has(formElement)) {
            haveAttachedSubmitListener.add(formElement);
            $dom.on(formElement, 'submit', submitListenerForValidation);
        }
    };

    var latestValidatorPromise = new WeakMap();

    function submitListenerForValidation(e) {
        var formElement = e.target;
        var validatorsArray = formSubmitValidators.get(formElement);

        if ((formElement.localName !== 'form') || (validatorsArray == null)) {
            return;
        }

        validatorsArray.every(function (validatorFn) {
            var result = validatorFn(formElement);

            if ((result == null) || (result === true)) {
                return; // continue
            }

            if (result === false) {
                e.preventDefault();
                return false; // break
            }

            if ($util.isPromise(result)) {
                e.preventDefault();

                latestValidatorPromise.set(formElement, result);

                result.then(function (valid) {
                    if (valid && (latestValidatorPromise.get(formElement) === result)) {
                        $dom.submit(formElement);
                    }
                });

                return false; // break
            }

            $util.error('$cms.form.addSubmitValidator(): Validator function returned result of invalid type "' + $util.typeName(result) + '"', { result: result, validatorFunction: validatorFn});
        });
    }

}());
