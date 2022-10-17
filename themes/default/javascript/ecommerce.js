(function ($cms, $util, $dom) {
    'use strict';

    $dom.ready.then(function () {
        var addressFields = ['address1', 'city', 'county', 'state', 'postalcode', 'country'];
        for (var i = 0; i < addressFields.length; i++) {
            var billing = document.getElementById('billing_' + addressFields[i]);
            var shipping = document.getElementById('shipping_' + addressFields[i]);
            if (billing && shipping) {
                billing.onchange = (function (billing, shipping) {
                    return function () {
                        if (billing.nodeName.toLowerCase() === 'select') {
                            if ((shipping.selectedIndex === 0) && (billing.selectedIndex !== 0)) {
                                shipping.selectedIndex = billing.selectedIndex;
                                if (window.jQuery && (window.jQuery.fn.select2 !== undefined)) {
                                    window.jQuery(shipping).trigger('change');
                                }
                            }
                        } else {
                            if (shipping.value === '') {
                                shipping.value = billing.value;
                            }
                        }
                    };
                }(billing, shipping));
            }
        }
    });

    $cms.functions.moduleAdminEcommerce = function moduleAdminEcommerce() {
        var _lengthUnits = document.getElementById('length_units'), _length = document.getElementById('length');
        _lengthUnits.addEventListener('change', adjustLengths);
        _length.addEventListener('change', adjustLengths);

        function adjustLengths() {
            var lengthUnits = _lengthUnits.value, length = _length.value;
            if (document.getElementById('auto_recur').checked) {
                // Limits based on https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/
                if ((lengthUnits === 'd') && ((length < 1) || (length > 90))) {
                    _length.value = (length < 1) ? 1 : 90;
                }

                if ((lengthUnits === 'w') && ((length < 1) || (length > 52))) {
                    _length.value = (length < 1) ? 1 : 52;
                }

                if ((lengthUnits === 'm') && ((length < 1) || (length > 24))) {
                    _length.value = (length < 1) ? 1 : 24;
                }

                if ((lengthUnits === 'y') && ((length < 1) || (length > 5))) {
                    _length.value = (length < 1) ? 1 : 5;
                }
            } else {
                if (length < 1) {
                    _length.value = 1;
                }
            }
        }
    };

    $cms.functions.ecommerceEmailGetNeededFieldsPop3 = function () {
        var extraChecks = [];
        extraChecks.push(function (e, form, erroneous, alerted, firstFieldWithError) {
            if (form.elements['pass1'].value !== form.elements['pass2'].value) {
                $cms.ui.alert('{!PASSWORD_MISMATCH;}');
                alerted.valueOf = function () { return true; };
                firstFieldWithError = form.elements['pass2'];
                return false;
            }
            return true;
        });
        return extraChecks;
    };

    $cms.templates.ecomPurchaseStageDetails = function ecomPurchaseStageDetails(params) {
        if (params.jsFunctionCalls != null) {
            $cms.executeJsFunctionCalls(params.jsFunctionCalls);
        }
    };

    $cms.templates.purchaseWizardStageTerms = function purchaseWizardStageTerms(params, container) {
        $dom.on(container, 'click', '.js-checkbox-click-toggle-proceed-btn', function (e, checkbox) {
            $dom.$('#proceed-button').disabled = !checkbox.checked;
        });

        $dom.on(container, 'click', '.js-click-btn-i-disagree', function (e, btn) {
            var newLocation = strVal(btn.dataset.tpLocation);

            if (newLocation) {
                window.location = newLocation;
            }
        });
    };

    $cms.templates.ecomLogosAuthorize = function ecomLogosAuthorize(params) {
        window.ANS_customer_id = strVal(params.customerId);
        $cms.requireJavascript('https://verify.authorize.net/anetseal/seal.js');
    };

    $cms.templates.ecomPurchaseStagePay = function ecomPurchaseStagePay(params) {
        var typeCode = strVal(params.typeCode);

        if (typeCode.toUpperCase().startsWith('CART_ORDER_')) {
            // Automatic link clicking of purchase button for cart orders (because button was already pressed on cart screen)
            $dom.trigger('#purchase-button', 'click');
        }
    };
}(window.$cms, window.$util, window.$dom));
