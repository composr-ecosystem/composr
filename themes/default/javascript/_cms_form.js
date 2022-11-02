/* This file contains Composr-specific form functionality which is CMS-wide (as opposed to core_form_interfaces.js and checking.js) */

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
}());
