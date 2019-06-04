(function ($cms, $util, $dom) {
    'use strict';

    $cms.functions.getBannerFormFields = function getBannerFormFields() {
        if (document.getElementById('campaign_remaining')) {
            var form = document.getElementById('campaign_remaining').form;
            var crf = function () {
                form.elements['campaign_remaining'].disabled = (!form.elements['deployment_agreement'][1].checked);
            };
            crf();
            form.elements['deployment_agreement'][0].addEventListener('click', crf);
            form.elements['deployment_agreement'][1].addEventListener('click', crf);
            form.elements['deployment_agreement'][2].addEventListener('click', crf);
        }
    };

    $cms.functions.moduleCmsBannersRunStart = function moduleCmsBannersRunStart() {
        document.getElementById('display_likelihood').onkeyup = function () {
            var _imHere = document.getElementById('im-here');
            if (_imHere) {
                var _imTotal = document.getElementById('im-total'),
                    imHere = parseInt(document.getElementById('display_likelihood').value),
                    imTotal = parseInt(_imTotal.className.replace('im-', '')) + imHere;

                $dom.html(_imHere, imHere);
                $dom.html(document.getElementById('im-here-2'), imHere);
                $dom.html(_imTotal, imTotal);
                $dom.html(document.getElementById('im-total-2'), imTotal);
            }
        };
    };

    $cms.functions.moduleCmsBannersRunStartAdd = function moduleCmsBannersRunStartAdd() {
        var form = document.getElementById('main-form'),
            submitBtn = document.getElementById('submit-button');

        var validValue;
        form.addEventListener('submit', function submitCheck(e) {
            var value = form.elements['banner_codename'].value;

            if (value === validValue) {
                return;
            }

            submitBtn.disabled = true;
            var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=exists_banner&name=' + encodeURIComponent(value) + $cms.keep();
            e.preventDefault();
            $cms.form.doAjaxFieldTest(url).then(function (valid) {
                if (valid) {
                    validValue = value;
                    $dom.submit(form);
                } else {
                    submitBtn.disabled = false;
                }
            });
        });
    };

    $cms.functions.moduleCmsBannersRunStartAddCategory = function moduleCmsBannersRunStartAddCategory() {
        var form = document.getElementById('main-form'),
            submitBtn = document.getElementById('submit-button'),
            validValue;

        form.addEventListener('submit', function submitCheck(e) {
            var value = form.elements['new_id'].value;

            if (value === validValue) {
                return;
            }

            submitBtn.disabled = true;
            var url = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=exists_banner_type&name=' + encodeURIComponent(form.elements['new_id'].value) + $cms.keep();
            e.preventDefault();
            $cms.form.doAjaxFieldTest(url).then(function (valid) {
                if (valid) {
                    validValue = value;
                    $dom.submit(form);
                } else {
                    submitBtn.disabled = false;
                }
            });
        });
    };
}(window.$cms, window.$util, window.$dom));
