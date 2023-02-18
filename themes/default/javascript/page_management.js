(function ($cms, $util, $dom) {
    'use strict';

    var $ADDON_INSTALLED_code_editor = boolVal('{$ADDON_INSTALLED,code_editor}'); // eslint-disable-line camelcase

    $cms.templates.sitemapEditorScreen = function sitemapEditorScreen(params, container) {
        var editZoneUrl = params.editZoneUrl,
            addZoneUrl = params.addZoneUrl,
            zoneEditorUrl = params.zoneEditorUrl,
            permissionTreeEditorUrl = params.permissionTreeEditorUrl,
            editPageUrl = params.editPageUrl,
            addPageUrl = params.addPageUrl,
            deleteUrl = params.deleteUrl;

        $cms.requireJavascript('tree_list').then(function () {
            window.sitemap = $cms.ui.createTreeList('tree-list', '{$FIND_SCRIPT_NOHTTP;,sitemap}?start_links=1&get_perms=0&label_content_types=1&keep_full_structure=1' + $cms.keep(), null, '', false, null, true);
        });

        $dom.on(container, 'change', '.js-change-update-details-box', function (e, changed) {
            updateDetailsBox(changed);
        });

        function updateDetailsBox(element) {
            /*{+START,SET,icon_proceed2}{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}{+END}*/
            var actionsTpl = '{!ACTIONS;^}:<nav><ul class="actions-list">[1]</ul></nav><br />',
                actionsTplItem = '<li>{$GET;^,icon_proceed2} <a href="[2]">[1]</a></li>',
                infoTpl = '<table class="map-table results-table wide-table autosized-table"><tbody>[1]</tbody></table>',
                infoTplItem = '<tr><th>[1]</th><td>[2]</td></tr>';


            if (!window.sitemap) {
                return;
            }

            var target = document.getElementById('details-target');
            if (!element.value) {
                $dom.html(target, '{!zones:NO_ENTRY_POINT_SELECTED;^}');
                return;
            }

            var node = window.sitemap.getElementByIdHack(element.value);
            var type = node.getAttribute('type');
            var pageLink = node.getAttribute('serverid');
            var pageLinkBits = pageLink.split(/:/);
            var zoneDir = node.getAttribute('zone_dir');

            if (zoneDir) {
                zoneDir = zoneDir.toLowerCase();
                if (zoneDir.includes('/')) {
                    zoneDir = zoneDir.substr(0, zoneDir.indexOf('/'));
                }
            }

            var actionBuildup = '';
            var infoBuildup = '';
            var path;
            switch (zoneDir) {
                case 'root':
                    actionBuildup += actionsTplItem.replace(/\[1\]/, '{!zones:ADD_ZONE;^}').replace(/\[2\]/, addZoneUrl);
                    break;

                case 'zone':
                    actionBuildup += actionsTplItem.replace(/\[1\]/, '{!zones:ZONE_EDITOR;^}').replace(/\[2\]/, zoneEditorUrl.replace(/%21/, pageLink.replace(/:/, '', pageLink)));
                    actionBuildup += actionsTplItem.replace(/\[1\]/, '{!permissions:PERMISSIONS_TREE;^}').replace(/\[2\]/, permissionTreeEditorUrl.replace(/%21/, pageLink.replace(/:/, '%3A', pageLink)));
                    actionBuildup += actionsTplItem.replace(/\[1\]/, '{!zones:EDIT_ZONE;^}').replace(/\[2\]/, editZoneUrl.replace(/%21/, pageLink.replace(/:/, '', pageLink)));
                    actionBuildup += actionsTplItem.replace(/\[1\]/, '{!zones:COMCODE_PAGE_ADD;^}').replace(/\[2\]/, addPageUrl.replace(/%21/, pageLink.replace(/:/, '', pageLink) + '%3Aexample'));
                    break;

                case 'modules':
                case 'modules_custom':
                case 'minimodules':
                case 'minimodules_custom':
                    path = pageLinkBits[0] + ((pageLinkBits[0] === '') ? '' : '/') + 'pages/' + zoneDir + '/' + pageLinkBits[1] + '.php';
                    if ($ADDON_INSTALLED_code_editor && !$cms.configOption('single_public_zone')) { // eslint-disable-line camelcase
                        actionBuildup += actionsTplItem.replace(/\[1\]/, '{!EDIT;^}').replace(/\[2\]/, '{$BASE_URL;,0}/code_editor.php?path=' + encodeURIComponent(path));
                    }
                    switch (zoneDir) {
                        case 'modules':
                        case 'modules_custom':
                            actionBuildup += actionsTplItem.replace(/\[1\]/, '{!permissions:PERMISSIONS_TREE;^}').replace(/\[2\]/, permissionTreeEditorUrl.replace(/%21/, pageLink.replace(/:/, '%3A', pageLink)));
                            if (node.getAttribute('author')) {
                                infoBuildup += infoTplItem.replace(/\[1\]/, '{!AUTHOR;^}').replace(/\[2\]/, $cms.filter.html(node.getAttribute('author')));
                            }
                            if (node.getAttribute('organisation')) {
                                infoBuildup += infoTplItem.replace(/\[1\]/, '{!ORGANISATION;^}').replace(/\[2\]/, $cms.filter.html(node.getAttribute('organisation')));
                            }
                            if (node.getAttribute('version')) {
                                infoBuildup += infoTplItem.replace(/\[1\]/, '{!VERSION;^}').replace(/\[2\]/, $cms.filter.html(node.getAttribute('version')));
                            }
                            break;
                        case 'minimodules':
                        case 'minimodules_custom':
                            break;
                    }
                    break;

                case 'comcode':
                case 'comcode_custom':
                    path = pageLinkBits[0] + '/pages/' + zoneDir + '/' + pageLinkBits[1] + '.txt';
                    actionBuildup += actionsTplItem.replace(/\[1\]/, '{!permissions:PERMISSIONS_TREE;^}').replace(/\[2\]/, permissionTreeEditorUrl.replace(/%21/, pageLink.replace(/:/, '%3A', pageLink)));
                    actionBuildup += actionsTplItem.replace(/\[1\]/, '{!EDIT;^}').replace(/\[2\]/, editPageUrl.replace(/%21/, pageLink));
                    break;

                case 'html':
                case 'html_custom':
                    path = pageLinkBits[0] + '/pages/' + zoneDir + '/' + pageLinkBits[1] + '.htm';
                    break;

                case 'entry_point':
                    break;
            }

            // Pages
            if ((type === 'page') || (type === 'comcode_page')) {
                actionBuildup += actionsTplItem.replace(/\[1\]/, '{!DELETE;^}').replace(/\[2\]/, deleteUrl.replace(/%5B1%5D/, pageLinkBits[0]).replace(/\[2\]/, pageLinkBits[1]));
            }

            // All except root
            if (zoneDir !== 'root') {
                actionBuildup += actionsTplItem.replace(/\[1\]/, '{!VIEW;^}').replace(/\[2\]/, $cms.filter.html('{$FIND_SCRIPT_NOHTTP;,page_link_redirect}?id=' + encodeURIComponent(pageLink) + $cms.keep()));
                infoBuildup += infoTplItem.replace(/\[1\]/, '{!zones:PAGE_LINK;^}').replace(/\[2\]/, '<kbd>' + $cms.filter.html(pageLink) + '</kbd>');
                if (element.selectedEditlink) {
                    actionBuildup += actionsTplItem.replace(/\[1\]/, '{!EDIT;^}').replace(/\[2\]/, $cms.filter.html('{$FIND_SCRIPT_NOHTTP;,page_link_redirect}?id=' + element.selectedEditlink + $cms.keep()));
                }
            }

            // Output
            $dom.empty(target);
            if (actionBuildup) {
                var actions = document.createElement('div');
                $dom.html(actions, actionsTpl.replace(/\[1\]/, actionBuildup));
                target.appendChild(actions);
            }

            if (infoBuildup) {
                var info = document.createElement('div');
                $dom.html(info, infoTpl.replace(/\[1\]/, infoBuildup));
                target.appendChild(info);
            }
        }

    };
}(window.$cms, window.$util, window.$dom));
