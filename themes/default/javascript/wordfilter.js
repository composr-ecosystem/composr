(function () {
    'use strict';

    var WORDFILTER_REPLACEMENT_GRAWLIXES = '%GRAWLIXES%';

    // Show/Hide the "replacement" input box when the "replace_with_grawlixes" checkbox is toggled
    $cms.functions.adminWordfilterWordForm = function () {
        var replacementFieldWrapper = document.getElementById('form-table-field--replacement'),
            replacementField = document.getElementsByName('replacement')[0],
            grawlixesTickWrapper = document.getElementById('form-table-field--replace_with_grawlixes'),
            grawlixesTick = document.getElementsByName('replace_with_grawlixes')[0];

        if (!replacementFieldWrapper || !replacementField || !grawlixesTickWrapper || !grawlixesTick) {
            return;
        }

        if (grawlixesTick.checked) {
            $dom.hide(replacementFieldWrapper);
            replacementField.value = WORDFILTER_REPLACEMENT_GRAWLIXES;
        }

        var originalValue = '';
        grawlixesTick.addEventListener('change', function () {
            if (grawlixesTick.checked) {
                $dom.hide(replacementFieldWrapper);

                if (replacementField.value !== WORDFILTER_REPLACEMENT_GRAWLIXES) {
                    originalValue = replacementField.value;
                }
                replacementField.value = WORDFILTER_REPLACEMENT_GRAWLIXES;
            } else {
                replacementField.value = originalValue;
                $dom.show(replacementFieldWrapper);
            }
        });
    };
}());
