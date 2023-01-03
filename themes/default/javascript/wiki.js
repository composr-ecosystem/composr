(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.wikiManageTreeScreen = function wikiManageTreeScreen(params, container) {
        if ($dom.$('#tree-list--root-mtp-tree')) {
            $cms.requireJavascript('tree_list').then(function () {
                $cms.ui.createTreeList('mtp-tree', '{$FIND_SCRIPT_NOHTTP;,ajax_tree}?hook=choose_wiki_page' + $cms.keep(), '', '');
            });
        }

        $dom.on(container, 'change', '.js-change-input-tree-update-children-value', function (e, input) {
            if (input.value !== '') {
                $dom.$('#children').value += input.value + '=' + input.selectedTitle + '\n';
            }
        });
    };

    $cms.templates.wikiPost = function wikiPost(params, container) {
        var id = strVal(params.id);

        $dom.on(container, 'click', '.js-click-checkbox-set-cell-mark-class', function (e, checkbox) {
            var cell = $dom.$('#cell-mark-' + id);
            cell.classList.toggle('cns-on', checkbox.checked);
            cell.classList.toggle('cns-off', !checkbox.checked);
        });

        $dom.on(container, 'click', '.js-click-show-wiki-merge-button', function () {
            var massSelects = $dom.$$($cms.getMainCmsWindow().document, '.wiki-mass-select-marker');
            var hasTickedBox = massSelects.find(function (selectMarker) {
                return selectMarker.classList.contains('cns-on');
            });
            var wikiMergeButton = $dom.$('#wiki-merge-button');

            if (hasTickedBox) {
                wikiMergeButton.classList.remove('button-faded');
                $dom.show(wikiMergeButton);
            } else {
                wikiMergeButton.classList.add('button-faded');
                $dom.hide(wikiMergeButton);
            }
        });
    };

    $cms.templates.wikiPageScreen = function wikiPageScreen(params, container) {
        $dom.on(container, 'click', '.js-click-btn-add-form-marked-posts', function (e, btn) {
            if ($cms.form.addFormMarkedPosts(btn.form, 'mark_')) {
                $cms.ui.disableButton(btn);
            } else {
                e.preventDefault();
                $cms.ui.alert('{!NOTHING_SELECTED=;}');
            }
        });
    };
}(window.$cms, window.$util, window.$dom));
