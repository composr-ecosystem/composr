(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.XmlConfigScreen = XmlConfigScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function XmlConfigScreen() {
        XmlConfigScreen.base(this, 'constructor', arguments);

        window.aceComposrLoader('xml', 'xml');
    }

    $util.inherits(XmlConfigScreen, $cms.View, /**@lends XmlConfigScreen#*/{
        events: function () {
            return {
                'submit .js-form-xml-config': 'submit'
            };
        },

        submit: function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled() && !e.defaultPrevented) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
        }
    });

    $cms.templates.configCategoryScreen = function configCategoryScreen(params, container) {
        $dom.on(container, 'submit', '.js-form-primary-page', function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled() && !e.defaultPrevented) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
        });
    };
}(window.$cms, window.$util, window.$dom));
