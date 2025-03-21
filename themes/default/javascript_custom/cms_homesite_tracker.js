(function ($cms) {
    'use strict';

    $cms.templates.mantisTracker = function mantisTracker(params, container) {
        $dom.on(container, 'click', '.js-click-add-voted-class', function (e, el) {
            el.classList.remove('tracker-issue-not-voted');
            el.classList.add('tracker-issue-voted');
        });
    };

    $cms.templates.blockCreditExpsInner = function blockCreditExpsInner(params, container) {
        updateProductInfoDisplay();

        $dom.on(container, 'change', '.js-change-update-product-info-display', function () {
            updateProductInfoDisplay();
        });

        function updateProductInfoDisplay() {
            var typeCode = document.getElementById('type_code');
            var value = typeCode.value;
            var creditsInfo = document.body.querySelectorAll('.creditsInfo');
            for (var i = 0; i < creditsInfo.length; i++) {
                creditsInfo[i].style.display = (creditsInfo[i].id === 'info_' + value) ? 'block' : 'none';
            }
        }
    };
}(window.$cms));
