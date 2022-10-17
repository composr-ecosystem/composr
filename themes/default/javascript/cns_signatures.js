(function ($cms) {
    'use strict';

    $cms.functions.hookProfilesTabsEditSignatureRenderTab = function hookProfilesTabsEditSignatureRenderTab(size) {
        size = strVal(size);

        var extraChecks = [];
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) {
            var post = form.elements['signature'];

            if ((!post.value) && (post[1])) {
                post = post[1];
            }
            if (post.value.length > size) {
                $cms.ui.alert('{!cns:SIGNATURE_TOO_BIG;^}');
                alerted.valueOf = function () { return true; };
                firstFieldWithError = post;
                return false;
            }
            return true;
        });
        return extraChecks;
    };
}(window.$cms));
