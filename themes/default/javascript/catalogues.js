(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.CatalogueAddingScreen = CatalogueAddingScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function CatalogueAddingScreen() {
        CatalogueAddingScreen.base(this, 'constructor', arguments);

        catalogueFieldChangeWatching();
    }

    $util.inherits(CatalogueAddingScreen, $cms.View, /**@lends CatalogueAddingScreen#*/{
    });

    $cms.views.CatalogueEditingScreen = CatalogueEditingScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function CatalogueEditingScreen() {
        CatalogueEditingScreen.base(this, 'constructor', arguments);

        catalogueFieldChangeWatching();
    }

    $util.inherits(CatalogueEditingScreen, $cms.View, /**@lends CatalogueAddingScreen#*/{
    });

    $cms.functions.cmsCataloguesImportCatalogue = function cmsCataloguesImportCatalogue() {
        var keyField = document.getElementById('key_field'),
            form = keyField.form;

        keyField.onchange = updateKeySettings;
        updateKeySettings();

        function updateKeySettings() {
            var hasKey = (keyField.value !== '');

            form.elements['new_handling'][0].disabled = !hasKey;
            form.elements['new_handling'][1].disabled = !hasKey;

            form.elements['delete_handling'][0].disabled = !hasKey;
            form.elements['delete_handling'][1].disabled = !hasKey;

            form.elements['update_handling'][0].disabled = !hasKey;
            form.elements['update_handling'][1].disabled = !hasKey;
            form.elements['update_handling'][2].disabled = !hasKey;
            form.elements['update_handling'][3].disabled = !hasKey;
        }
    };

    $cms.functions.moduleCmsCataloguesRunStartAddCatalogue = function moduleCmsCataloguesRunStartAddCatalogue() {
        var extraChecks = [],
            validValue;
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) { // eslint-disable-line no-unused-vars
            var value = form.elements['catalogue_name'].value;

            if ((value === validValue) || (value === '')) {
                return true;
            }

            return function () {
                var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_catalogue&name=' + encodeURIComponent(value) + $cms.keep();
                return $cms.form.doAjaxFieldTest(url).then(function (valid) {
                    if (valid) {
                        validValue = value;
                    }

                    if (!valid) {
                        erroneous.valueOf = function () { return true; };
                        alerted.valueOf = function () { return true; };
                        firstFieldWithError = form.elements['catalogue_name'];
                    }
                });
            };
        });
        return extraChecks;
    };

    $cms.functions.moduleCmsCataloguesCat = function moduleCmsCataloguesCat() {
        if (document.getElementById('move_days_lower')) {
            var mt = document.getElementById('move_target'),
                form = mt.form,
                crf = function () {
                    var s = (mt.selectedIndex === 0);
                    form.elements['move_days_lower'].disabled = s;
                    form.elements['move_days_higher'].disabled = s;
                };
            crf();
            $dom.on(mt, 'click', crf);
        }
    };

    $cms.functions.moduleCmsCataloguesAlt = function moduleCmsCataloguesAlt() {
        var fn = document.getElementById('title');
        if (fn) {
            var form = fn.form;
            fn.onchange = function () {
                if ((form.elements['catalogue_name']) && (form.elements['catalogue_name'].value === '')) {
                    form.elements['catalogue_name'].value = fn.value.toLowerCase().replace(/[^{$URL_CONTENT_REGEXP_JS}]/g, '_').replace(/_+$/, '').substr(0, 80);
                }
            };
        }
    };

    function catalogueFieldChangeWatching() {
        // Find all our ordering fields
        var s = document.getElementsByTagName('select');
        var allOrderers = [];
        for (var i = 0; i < s.length; i++) {
            if ((s[i].name.startsWith('new_field_') || s[i].name.startsWith('existing_field_')) && s[i].name.endsWith('_order')) {
                allOrderers.push(s[i]);
            }
        }
        // Assign generated change function to all ordering fields (generated so as to avoid JS late binding problem)
        for (var j = 0; j < allOrderers.length; j++) {
            allOrderers[j].onchange = catalogueFieldReindexAround(allOrderers, allOrderers[j]);
        }
    }

    function catalogueFieldReindexAround(allOrderers, ob) {
        return function () {
            // Sort allOrderers by selectedIndex
            allOrderers = allOrderers.sort(function (a, b) {
                if ((a.selectedIndex === b.selectedIndex)) {
                    // The one we just changed takes priority when orders are the same
                    if (a.name === ob.name) {
                        return -1;
                    } else if (b.name === ob.name) {
                        return 1;
                    }
                }
                return a.selectedIndex - b.selectedIndex;
            });

            // Reset selection according to new order
            for (var i = 0; i < allOrderers.length; i++) {
                allOrderers[i].selectedIndex = i;
            }
        };
    }
}(window.$cms, window.$util, window.$dom));
