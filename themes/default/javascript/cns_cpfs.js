(function ($cms) {
    'use strict';

    $cms.functions.moduleAdminCnsCustomProfileFields_createSelectionListChooseTable = function (formId) { // eslint-disable-line camelcase
        formId = strVal(formId);

        var selectElements = document.getElementById(formId).getElementsByTagName('select');
        var selectSubmit = document.getElementById('selection-submit');
        var selectElementLength = selectElements.length;

        for (var counter = 0; counter < selectElementLength; counter++) {
            selectElements[counter].addEventListener('change', function () {
                selectElements[0].form.submit();
            });
        }
        selectSubmit.style.display = 'none';
    };
}(window.$cms));
