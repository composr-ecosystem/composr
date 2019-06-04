(function ($cms, $util, $dom) {
    'use strict';

    $cms.views.CatalogueAddingScreen = CatalogueAddingScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function CatalogueAddingScreen(params) {
        CatalogueAddingScreen.base(this, 'constructor', arguments);

        if (params.jsFunctionCalls != null) {
            $cms.executeJsFunctionCalls(params.jsFunctionCalls);
        }

        catalogueFieldChangeWatching();
    }

    $util.inherits(CatalogueAddingScreen, $cms.View, /**@lends CatalogueAddingScreen#*/{
        events: function () {
            return {
                'submit .js-form-catalogue-add': 'submit'
            };
        },

        submit: function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled()) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
        }
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

    $util.inherits(CatalogueEditingScreen, $cms.View, /**@lends CatalogueEditingScreen#*/{
        events: function () {
            return {
                'submit .js-form-catalogue-edit': 'submit'
            };
        },

        submit: function (e, form) {
            if ($cms.form.isModSecurityWorkaroundEnabled()) {
                e.preventDefault();
                $cms.form.modSecurityWorkaround(form);
            }
        }
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
        var form = document.getElementById('new_field_0_name').form,
            submitBtn = document.getElementById('submit-button'),
            validValue;

        form.addEventListener('submit', function submitCheck(e) {
            var value = form.elements['catalogue_name'].value;

            if (value === validValue) {
                return;
            }

            submitBtn.disabled = true;
            var url = '{$FIND_SCRIPT_NOHTTP;^,snippet}?snippet=exists_catalogue&name=' + encodeURIComponent(value) + $cms.keep();
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
            if (s[i].name.indexOf('order') !== -1) {
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
            var nextIndex = 0;

            // Sort our all_orderers array by selectedIndex
            for (var i = 0; i < allOrderers.length; i++) {
                for (var j = i + 1; j < allOrderers.length; j++) {
                    if (allOrderers[j].selectedIndex < allOrderers[i].selectedIndex) {
                        var temp = allOrderers[i];
                        allOrderers[i] = allOrderers[j];
                        allOrderers[j] = temp;
                    }
                }
            }

            // Go through all fields, assigning them the order (into selectedIndex). We are reordering *around* the field that has just had it's order set.
            for (var k = 0; k < allOrderers.length; k++) {
                if (nextIndex === ob.selectedIndex) {
                    nextIndex++;
                }

                if (allOrderers[k] !== ob) {
                    allOrderers[k].selectedIndex = nextIndex;
                    nextIndex++;
                }
            }
        };
    }
}(window.$cms, window.$util, window.$dom));
