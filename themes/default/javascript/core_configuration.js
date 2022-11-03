(function ($cms, $util) {
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
    });
}(window.$cms, window.$util));
