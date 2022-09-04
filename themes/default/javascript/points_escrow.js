(function ($cms) {
    'use strict';

    $cms.functions.modulePointsEscrowModerate = function modulePointsEscrowModerate() {
        var rEdit = document.getElementById('j-action-amend'),
        rComplete = document.getElementById('j-action-complete'),
        rCancel = document.getElementById('j-action-cancel'),
        iPoints = document.getElementById('points');

        // Points field should only be enabled when the "complete" radio is chosen
        rEdit.onclick = function() { iPoints.setAttribute('readonly', 'readonly'); };
        rComplete.onclick = function() { iPoints.removeAttribute('readonly'); };
        rCancel.onclick = function() { iPoints.setAttribute('readonly', 'readonly'); };
    };
}(window.$cms));
