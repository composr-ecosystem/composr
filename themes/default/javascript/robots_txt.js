(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.RobotsTxtScreen = RobotsTxtScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function RobotsTxtScreen() {
        RobotsTxtScreen.base(this, 'constructor', arguments);

        window.aceComposrLoader('robots_txt', 'plain_text');
    }

    $util.inherits(RobotsTxtScreen, $cms.View, /**@lends RobotsTxtScreen#*/{
        events: function () {
            return {
                'submit .js-form-robots-txt': 'submit',
                'click #robots_txt_default': 'selectDefault'
            };
        },

        submit: function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled() && !e.defaultPrevented) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
        },

        selectDefault: function (e, selectEl) {
            selectEl.select();
        }
    });
}(window.$cms, window.$util, window.$dom));
