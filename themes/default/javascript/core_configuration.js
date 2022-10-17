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
}(window.$cms, window.$util, window.$dom));
