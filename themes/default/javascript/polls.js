(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.PollBox = PollBox;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function PollBox() {
        PollBox.base(this, 'constructor', arguments);
    }

    $util.inherits(PollBox, $cms.View, /**@lends PollBox#*/{
        events: function () {
            return {
                'click .js-click-confirm-forfeit': 'confirmForfeit'
            };
        },
        confirmForfeit: function (e, target) {
            var form = target.form;

            $cms.ui.confirm('{!cns_polls:VOTE_FORFEIGHT;^}', function (answer) {
                if (answer) {
                    $dom.submit(form);
                }
            });

            return false;
        }
    });

    $cms.templates.blockMainPoll = function blockMainPoll() {};

    $cms.templates.pollAnswer = function pollAnswer(params, container) {
        var pollId = strVal(params.pid);

        $dom.on(container, 'click', '.js-click-enable-poll-input', function () {
            $dom.$('#poll' + pollId).disabled = false;
        });
    };
}(window.$cms, window.$util, window.$dom));
