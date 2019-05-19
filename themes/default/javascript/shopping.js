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

    $cms.templates.ecomShoppingCartScreen = function (params) {
        var container = this,
            typeCodes = strVal(params.typeCodes),
            emptyCaryUrl = strVal(params.emptyCartUrl);

        $dom.on(container, 'click', '.js-click-btn-cart-update', function (e) {
            if (updateCart(typeCodes) === false) {
                e.preventDefault();
            }
        });

        $dom.on(container, 'click', '.js-click-btn-cart-empty', function (e, btn) {
            if (confirmEmpty('{!shopping:EMPTY_CONFIRM;}', emptyCaryUrl, btn.form) === false) {
                e.preventDefault();
            }
        });

        $dom.on(container, 'click', '.js-click-do-cart-form-submit', function (e, btn) {
            $cms.form.doFormSubmit(btn.form);
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
        }

        function confirmEmpty(message, actionUrl, form) {
            $cms.ui.confirm(
                message,
                function (result) {
                    if (result) {
                        form.action = actionUrl;
                        $dom.submit(form);
                    }
                }
            );
            return false;
        }
    };

    $cms.templates.ecomAdminOrdersScreen = function ecomAdminOrdersScreen(params, container) {
        $dom.on(container, 'submit', '.js-submit-scroll-to-top', function () {
            try {
                scrollTo(0, 0);
            } catch (ignore) {}
        });
    };

    $cms.templates.ecomAdminOrderActions = function ecomAdminOrderActions(params, container) {
        $dom.on(container, 'change', '.js-select-change-action-submit-form', function (e, select) {
            if (select.selectedIndex > 0) {
                $dom.submit(select.form);
            }
        });

        $dom.on(container, 'submit', '.js-submit-confirm-admin-order-actions', function (e, form) {
            var actionName = form.elements.action.value;

            if (actionName === 'dispatch') {
                $cms.ui.confirm(
                    '{!shopping:DISPATCH_CONFIRMATION_MESSAGE;^}',
                    function (result) {
                        if (result) {
                            $cms.ui.disableFormButtons(form);
                            $dom.submit(form);
                        }
                    }
                );
            }

            if (actionName === 'del_order') {
                $cms.ui.confirm(
                    '{!shopping:CANCEL_ORDER_CONFIRMATION_MESSAGE;^}',
                    function (result) {
                        if (result) {
                            $cms.ui.disableFormButtons(form);
                            $dom.submit(form);
                        }
                    }
                );
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

    window.checkout = checkout;
    function checkout(formName, checkoutUrl) {
        var form = document.getElementById(formName);
        form.action = checkoutUrl;
        $dom.submit(form);
        return true;
    }
}(window.$cms, window.$util, window.$dom));
