(function ($cms) {
    'use strict';

    $cms.templates.mantisTracker = function mantisTracker(params, container) {
        $dom.on(container, 'click', '.js-click-add-voted-class', function (e, el) {
            el.classList.remove('tracker-issue-not-voted');
            el.classList.add('tracker-issue-voted');
        });
    };
}(window.$cms));
