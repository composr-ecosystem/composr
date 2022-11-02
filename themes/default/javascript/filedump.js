(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.filedumpEmbedScreen = function filedumpEmbedScreen(params, container) {
        if (params && (params.generated !== undefined)) {
            var el = $dom.$('#generated_comcode');
            try {
                el.focus();

                el.select();
            } catch (e) {}
        }

        $dom.on(container, 'click', '.js-click-input-img-size-select', function (e, input) {
            input.select();
        });

        $dom.on(container, 'click', '.js-click-generated-html-select', function (e, input) {
            input.select();
        });
    };

    $cms.templates.filedumpScreen = function filedumpScreen(params, container) {
        if (params.fileLink) {
            $cms.ui.open(params.fileLink, null, 'width=950;height=700', '_top');
        }

        $dom.on(container, 'click', '.js-check-filedump-selections', function (e, btn) {
            if (checkFiledumpSelections(btn.form) === false) {
                e.preventDefault();
            }
        });

        $dom.on(container, 'click', '.js-click-select-tab-g', function (e, clicked) {
            var tab = clicked.dataset.tpTab;

            if (tab) {
                $cms.ui.selectTab('g', tab);
            }
        });

        function checkFiledumpSelections(form) {
            var action = form.elements['filedump_action'].value;

            if (!action) {
                $cms.ui.alert('{!filedump:SELECT_AN_ACTION;^}');
                return false;
            }

            if (action === 'edit') {
                return true;
            }

            for (var i = 0; i < form.elements.length; i++) {
                if ((form.elements[i].name.match(/^select_\d+$/)) && (form.elements[i].checked)) {
                    return true;
                }
            }

            $cms.ui.alert('{!NOTHING_SELECTED_YET;^}');
            return false;
        }
    };
}(window.$cms, window.$util, window.$dom));
