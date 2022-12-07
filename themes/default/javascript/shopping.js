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
                        if (billing.localName === 'select') {
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

    $cms.templates.ecomShoppingCartScreen = function (params) {
        var container = this,
            typeCodes = strVal(params.typeCodes),
            emptyCartUrl = strVal(params.emptyCartUrl);

        $dom.on(container, 'click', '.js-click-btn-cart-update', function (e) {
            if (!updateCart(typeCodes)) {
                e.preventDefault();
            }
        });

        $dom.on(container, 'click', '.js-click-btn-cart-empty', function (e, btn) {
            e.preventDefault();
            confirmEmpty('{!shopping:EMPTY_CONFIRM;}', emptyCartUrl, btn.form);
        });

        function updateCart(proIds) {
            var proIdsArray = proIds.split(',');

            var tot = proIdsArray.length;

            for (var i = 0; i < tot; i++) {
                var quantityData = 'quantity_' + proIdsArray[i];

                var qval = document.getElementById(quantityData).value;

                if (isNaN(qval)) {
                    $cms.ui.alert('{!shopping:CART_VALIDATION_REQUIRE_NUMBER;^}');
                    return false;
                }
            }

            return true;
        }

        function confirmEmpty(message, actionUrl, form) {
            $cms.ui.confirm(
                message,
                function (result) {
                    if (result) {
                        form.action = actionUrl;
                        form.submit();
                    }
                }
            );
        }
    };

    $cms.templates.ecomAdminOrderActions = function ecomAdminOrderActions(params, container) {
        $dom.on(container, 'change', '.js-select-change-action-submit-form', function (e, select) {
            if (select.selectedIndex > 0) {
                var actionName = select.value,
                    form = select.form;

                if (actionName === 'dispatch') {
                    $cms.ui.confirm(
                        '{!shopping:DISPATCH_CONFIRMATION_MESSAGE;^}',
                        function (result) {
                            if (result) {
                                form.submit();
                            }
                        }
                    );
                } else if (actionName === 'del_order') {
                    $cms.ui.confirm(
                        '{!shopping:CANCEL_ORDER_CONFIRMATION_MESSAGE;^}',
                        function (result) {
                            if (result) {
                                form.submit();
                            }
                        }
                    );
                } else {
                    form.submit();
                }
            }
        });
    };

    $cms.templates.ecomShoppingItemQuantityField = function ecomShoppingItemQuantityField(params, container) {
        $dom.on(container, 'keypress', '.js-keypress-unfade-cart-update-button', function () {
            document.getElementById('cart-update-button').classList.remove('button-faded');
        });
    };

    $cms.templates.ecomShoppingItemRemoveField = function ecomShoppingItemRemoveField(params, container) {
        $dom.on(container, 'click', '.js-click-unfade-cart-update-button', function () {
            document.getElementById('cart-update-button').classList.remove('button-faded');
        });
    };
}(window.$cms, window.$util, window.$dom));
