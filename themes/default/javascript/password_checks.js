/*{+START,IF_PASSED,PASSWORD_PROMPT}*/
var passwordPrompt = '{PASSWORD_PROMPT;/}';
/*{+END}*/
/*{+START,IF_NON_PASSED,PASSWORD_PROMPT}*/
var passwordPrompt = '{!installer:CONFIRM_MAINTENANCE_PASSWORD}';
/*{+END}*/

/**
 * NOTE: This function also has a similar copy in themes/default/javascript/installer.js so update that as well when modifying here.
 * @param form
 * @return {boolean}
 */
function checkPasswords(form) {
    if (form.confirm) {
        return true;
    }

    if ((form.elements['cns_admin_password_confirm'] != null) && (form.elements['cns_admin_password_confirm'].value != '')) {
        if (!checkPassword(form, 'cns_admin_password', '{!ADMIN_USERS_PASSWORD;^/}')) {
            return false;
        }
    }

    if ((form.elements['maintenance_password_confirm'] != null) && (form.elements['maintenance_password_confirm'].value != '')) {
        if (!checkPassword(form, 'maintenance_password', '{!MAINTENANCE_PASSWORD;^/}')) {
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
            window.alert('{!PASSWORDS_DO_NOT_MATCH;^/}'.replace('\{1\}', fieldLabel));
            return false;
        }

        // Check does not match database password
        if (form.elements['db_site_password'] != null) {
            if ((form.elements[fieldName].value !== '') && (form.elements[fieldName].value === form.elements['db_site_password'].value)) {
                window.alert('{!PASSWORDS_DO_NOT_REUSE;^/}'.replace('\{1\}', fieldLabel));
                return false;
            }
        }

        // Check password is secure
        var isSecurePassword = true;
        if (form.elements[fieldName].value.length < 12) {
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
            return window.confirm('{!PASSWORD_INSECURE;^/}'.replace('\{1\}', fieldLabel)) && window.confirm('{!CONFIRM_REALLY;^/} {!PASSWORD_INSECURE;^/}'.replace('\{1\}', fieldLabel));
        }

        return true;
    }
}
