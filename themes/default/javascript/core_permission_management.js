(function ($cms, $util, $dom) {
    'use strict';

    var $corePermissionManagement = window.$corePermissionManagement = {};

    $corePermissionManagement.showPermissionSetting = showPermissionSetting;
    $corePermissionManagement.cleanupPermissionList = cleanupPermissionList;
    $corePermissionManagement.copyPermissionPresets = copyPermissionPresets;
    $corePermissionManagement.setupPrivilegeOverrideSelector = setupPrivilegeOverrideSelector;
    $corePermissionManagement.permissionsImgFunc1 = permissionsImgFunc1;
    $corePermissionManagement.permissionsImgFunc2 = permissionsImgFunc2;

    $cms.views.PermissionsTreeEditorScreen = PermissionsTreeEditorScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function PermissionsTreeEditorScreen(params) {
        PermissionsTreeEditorScreen.base(this, 'constructor', arguments);

        window.columnColor = params.color;
        window.usergroupTitles = params.usergroups;

        $cms.requireJavascript('tree_list').then(function () {
            window.sitemap = $cms.ui.createTreeList('tree-list', '{$FIND_SCRIPT_NOHTTP;,sitemap}?start_links=1&get_perms=1&label_content_types=1&keep_full_structure=1' + $cms.keep(), null, '', true);
        });
    }

    $util.inherits(PermissionsTreeEditorScreen, $cms.View, /**@lends PermissionsTreeEditorScreen#*/{
        events: function () {
            return {
                'click .js-click-update-group': 'updateGroupDisplayer',
                'change .js-change-update-group': 'updateGroupDisplayer',
                'click .js-click-set-permissions': 'setPermissions',
                'change .js-change-update-perm-box': 'updatePermissionBox'
            };
        },

        updateGroupDisplayer: function (e, select) {
            if (!window.sitemap.treeListData) {
                $cms.ui.alert('{!PLEASE_WAIT;}', null, true);
                return;
            }

            $dom.html('#group-name', $cms.filter.html(window.usergroupTitles[select.value]));
            var tree = document.getElementById('tree-list--root-tree-list');
            $dom.empty(tree);
            window.sitemap.renderTree(window.sitemap.treeListData, tree);
        },

        setPermissions: function () {
            setPermissions(document.getElementById('tree-list'));
        },

        updatePermissionBox: function (e, target) {
            updatePermissionBox(target);
        }
    });

    $cms.templates.permissionRow = function permissionRow(params, container) {
        $dom.on(container, 'click', '.js-click-btn-toggle-value', function (e, btn) {
            var add = (btn.innerHTML === '+');
            btn.innerHTML = add ? '-' : '+'; // Toggle

            for (var i = 0; i < params.rowGroupCellIds.length; i++) {
                var groupCellId = params.rowGroupCellIds[i];
                btn.form.elements[groupCellId].checked = add || params.rowModeratorGroupCellIds.includes(groupCellId);
            }
        });
    };

    $cms.templates.permissionKeysMessageRow = function permissionKeysMessageRow(params, container) {
        $dom.on(container, 'focus', '.js-focus-textarea-expand', function (e, textarea) {
            textarea.rows = '10';
        });

        $dom.on(container, 'blur', '.js-blur-textarea-contract', function (e, textarea) {
            if (!textarea.form.disableSizeChange) {
                textarea.rows = '2';
            }
        });
    };

    $cms.templates.permissionKeysPermissionsScreen = function permissionKeysPermissionsScreen(params, container) {
        $dom.on(container, 'mouseover mouseout', '.js-btn-hover-toggle-disable-size-change', function (e, btn) {
            btn.form.disableSizeChange = (e.type === 'mouseover');
        });
    };

    // Selection changed, so update box
    function updatePermissionBox(setting) {
        if (!window.sitemap) {
            return;
        }

        if (!setting.value) {
            document.getElementById('selection-form-fields').style.display = 'none';
            document.getElementById('selection-button').disabled = true;
            $dom.html('#selection-message', '{!permissions:PERMISSIONS_TREE_EDITOR_NONE_SELECTED;^}');
            return;
        }

        // Go through and set maximum permissions/override from those selected
        var values = setting.value.split(',');
        var id, name, value, i, node, j, group, element, privilege, privilegeTitle,
            knownGroups = [], knownPrivileges = [], k, newOption,
            numPrivilegeDefault, numPrivilege, ths, tds, cells, newCell, row;
        var matrix = document.getElementById('enter-the-matrix').querySelector('table');
        var numPrivilegeTotal = 0;
        var isCms = null;
        var rows = matrix.getElementsByTagName('tr');
        var doneHeader = false;
        for (i = 0; i < values.length; i++) { // For all items that we are loading permissions for (we usually just do it for one, but sometimes we load whole sets if we are batch setting permissions)
            node = window.sitemap.getElementByIdHack(values[i]);

            if (i === 0) { // On first iteration we do a cleanup

                // Find usergroups
                for (j = 0; j < node.attributes.length; j++) {
                    if (node.attributes[j].name.substr(0, 7) === 'g_view_') {
                        group = node.attributes[j].name.substr(7);
                        knownGroups.push(group);
                    }
                }

                // Blank out everything in the permissions box, remove all privileges
                for (j = 0; j < knownGroups.length; j++) {
                    group = knownGroups[j];
                    element = document.getElementById('access_' + group);
                    element.checked = false;
                    element = document.getElementById('access_' + group + '_presets');
                    if (element.options[0].id !== 'access-' + group + '-custom-option') {
                        newOption = document.createElement('option');
                        $dom.html(newOption, '{!permissions:PINTERFACE_LEVEL_CUSTOM;^}');
                        newOption.id = 'access-' + group + '-custom-option';
                        newOption.value = '';
                        element.insertBefore(newOption, element.options[0]);
                    }
                    element.selectedIndex = 0;

                    // Delete existing privilege nodes
                    ths = matrix.getElementsByTagName('th');
                    tds = matrix.getElementsByTagName('td');
                    cells = [];
                    for (k = 0; k < ths.length; k++) {
                        cells.push(ths[k]);
                    }
                    for (k = 0; k < tds.length; k++) {
                        cells.push(tds[k]);
                    }
                    for (k = 0; k < cells.length; k++) {
                        if (cells[k].classList.contains('privilege-header') || cells[k].classList.contains('privilege-footer') || cells[k].classList.contains('privilege-cell')) {
                            cells[k].remove();
                        }
                    }
                }
            }

            isCms = Boolean(node.getAttribute('serverid').includes(':cms_') && (isCms !== false));

            // Set view access
            for (j = 0; j < node.attributes.length; j++) {
                if (node.attributes[j].name.substr(0, 7) === 'g_view_') {
                    group = node.attributes[j].name.substr(7);
                    element = document.getElementById('access_' + group);
                    if (!element.checked) {
                        element.checked = (node.attributes[j].value === 'true');
                    }
                    element = document.getElementById('access_' + group);
                }
            }
            var form = document.getElementById('permissions-form');
            var noViewSettings = (node.getAttribute('serverid') === '_root') || (node.getAttribute('serverid').substr(0, 22) === 'cms:cms_comcode_pages:');
            for (j = 0; j < form.elements.length; j++) {
                element = form.elements[j];
                if (element.id.substr(0, 7) === 'access_') {
                    element.style.display = ((values.length === 1) && (noViewSettings)) ? 'none' : 'inline';
                    element.disabled = (element.name === '_ignore') || ((values.length === 1) && (noViewSettings));
                }
            }

            // Create privilege nodes
            numPrivilege = 0;
            knownPrivileges = [];
            id = node.getAttribute('id');
            if (window.attributesFull === undefined) {
                window.attributesFull = [];
            }
            if (window.attributesFull[id] === undefined) {
                window.attributesFull[id] = node.attributes;
            }
            for (name in window.attributesFull[id]) {
                value = window.attributesFull[id][name];
                if (name.substr(0, 'privilege_'.length) === 'privilege_') {
                    privilege = name.substr('privilege_'.length);
                    privilegeTitle = value;
                    doneHeader = false;
                    for (k = 0; k < rows.length; k++) {
                        if (!rows[k].id.startsWith('access-')) {
                            continue;
                        }

                        group = rows[k].id.substring(7, rows[k].id.indexOf('-privilege-container'));

                        element = document.getElementById('access_' + group + '_privilege_' + privilege);
                        if ((!element) && (!document.getElementById('privilege_cell_' + group + '_' + privilege))) { // We haven't added it yet for one of the resources we're doing permissions for
                            if (!doneHeader) {
                                row = rows[0];
                                newCell = row.insertBefore(document.createElement('th'), row.cells[row.cells.length]);
                                newCell.className = 'privilege-header';
                                newCell.id = 'privilege_header_' + privilege;
                                $dom.html(newCell, '<img class="gd-text" data-gd-text="1" src="' + $cms.filter.html('{$FIND_SCRIPT_NOHTTP;,gd_text}?color=' + window.columnColor + '&text=' + encodeURIComponent(privilegeTitle) + $cms.keep()) + '" title="' + $cms.filter.html(privilegeTitle) + '" alt="' + $cms.filter.html(privilegeTitle) + '" />');

                                rows[rows.length - 1].appendChild(document.createElement('td')).className = 'form-table-field-input privilege-footer'; // Footer cell

                                numPrivilegeTotal++;

                                doneHeader = true;
                            }

                            // Manually build up cell
                            row = document.getElementById('access-' + group + '-privilege-container');
                            newCell = row.insertBefore(document.createElement('td'), row.cells[row.cells.length - 1]);
                            newCell.className = 'form-table-field-input privilege-cell';
                            newCell.id = 'privilege_cell_' + group + '_' + privilege;

                            var accessInput = document.getElementById('access-' + group);

                            if (accessInput == null) {
                                accessInput = $dom.$requireByName('access_' + group);
                            }

                            if (accessInput.name !== '_ignore') {
                                $dom.html(newCell, '<div class="accessibility-hidden"><label for="access_' + group + '_privilege_' + privilege + '">{!permissions:OVERRIDE;^}</label></div><select class="js-mouseover-show-permission-setting" title="' + $cms.filter.html(privilegeTitle) + '" id="access_' + group + '_privilege_' + privilege + '" name="access_' + group + '_privilege_' + privilege + '"><option selected="selected" value="-1">/</option><option value="0">{!permissions:NO_COMPACT;^}</option><option value="1">{!permissions:YES_COMPACT;^}</option></select>');
                                $dom.on(newCell, 'mouseover', '.js-mouseover-show-permission-setting', function (e, select) {
                                    if (select.value === '-1') {
                                        $corePermissionManagement.showPermissionSetting(select, e);
                                    }
                                });

                                element = document.getElementById('access_' + group + '_privilege_' + privilege);

                                $corePermissionManagement.setupPrivilegeOverrideSelector('access_' + group, '-1', privilege, privilegeTitle, false);
                            }
                        }
                        if (element) {
                            element.options[0].disabled = ((values.length === 1) && (node.getAttribute('serverid') === '_root'));
                        }
                    }
                    knownPrivileges.push(privilege);
                    numPrivilege++;
                }
            }

            // Set privileges for all usergroups (to highest permissions from all usergroups selected)
            for (name in window.attributesFull[id]) {
                value = window.attributesFull[id][name];
                if (name.substr(0, 'group_privileges_'.length) === 'group_privileges_') {
                    group = name.substr(name.lastIndexOf('_') + 1);
                    privilege = name.substr('group_privileges_'.length, name.length - group.length - 1 - ('group_privileges_'.length));
                    element = document.getElementById('access_' + group + '_privilege_' + privilege);
                    if (element.selectedIndex < parseInt(value) + 1) { // -1 corresponds to 0.
                        element.selectedIndex = parseInt(value) + 1;
                    }
                }
            }

            // Blank out any rows of defaults
            for (k = 0; k < knownGroups.length; k++) {
                group = knownGroups[k];
                numPrivilegeDefault = 0;
                for (j = 0; j < knownPrivileges.length; j++) {
                    element = document.getElementById('access_' + group + '_privilege_' + knownPrivileges[j]);
                    if (element.selectedIndex === 0) {
                        numPrivilegeDefault++;
                    }
                }
                if (numPrivilegeDefault === numPrivilege) {
                    element = document.getElementById('access_' + group + '_presets');
                    element.selectedIndex = 1;
                    $corePermissionManagement.cleanupPermissionList('access_' + group);
                    for (j = 0; j < knownPrivileges.length; j++) {
                        element = document.getElementById('access_' + group + '_privilege_' + knownPrivileges[j]);
                        if (window.sitemap == null) {
                            element.disabled = true;
                        }
                    }
                }
            }

            var button;
            // Hide certain things if we only have view settings here, else show them
            if (numPrivilegeTotal === 0) {
                $dom.html(matrix.querySelector('tr').cells[0], '{!USERGROUP;^}');
                for (k = 0; k < knownGroups.length; k++) {
                    document.getElementById('access_' + knownGroups[k] + '_presets').style.display = 'none';
                    button = document.getElementById('access-' + knownGroups[k] + '-privilege-container').querySelector('button');
                    if (button) {
                        button.disabled = true;
                    }
                }
            } else {
                $dom.html(matrix.querySelector('tr').cells[0], '<span class="heading-group">{!USERGROUP;^}</span> <span class="heading-presets">{!permissions:PINTERFACE_PRESETS;^}</span>');
                for (k = 0; k < knownGroups.length; k++) {
                    document.getElementById('access_' + knownGroups[k] + '_presets').style.display = 'block';
                    button = document.getElementById('access-' + knownGroups[k] + '-privilege-container').querySelector('button');
                    if (button) {
                        button.disabled = false;
                    }
                }
            }

            // Test to see what we wouldn't have to make a change to get - and that is what we're set at
            for (k = 0; k < knownGroups.length; k++) {
                group = knownGroups[k];
                var list = document.getElementById('access_' + group + '_presets');
                if (!$corePermissionManagement.copyPermissionPresets('access_' + group, '0', true)) {
                    list.selectedIndex = list.options.length - 4;
                } else if (!$corePermissionManagement.copyPermissionPresets('access_' + group, '1', true)) {
                    list.selectedIndex = list.options.length - 3;
                } else if (!$corePermissionManagement.copyPermissionPresets('access_' + group, '2', true)) {
                    list.selectedIndex = list.options.length - 2;
                } else if (!$corePermissionManagement.copyPermissionPresets('access_' + group, '3', true)) {
                    list.selectedIndex = list.options.length - 1;
                }
            }
        }

        // Set correct admin colspan
        for (i = 0; i < matrix.rows.length; i++) {
            if (matrix.rows[i].cells.length === 3) {
                matrix.rows[i].cells[2].colSpan = numPrivilegeTotal + 1;
            }
        }

        document.getElementById('selection-form-fields').style.display = 'block';
        document.getElementById('selection-button').disabled = false;
        $dom.html('#selection-message', (values.length <= 1) ? '{!permissions:PERMISSIONS_TREE_EDITOR_ONE_SELECTED;^}' : '{!permissions:PERMISSIONS_TREE_EDITOR_MULTI_SELECTED;^}');
    }

    // Saving
    function setPermissions(setting) {
        if (!setting.value) {
            return;
        }

        // Go through and set maximum permissions/override from those selected
        var values = setting.value.split(',');
        var id, i, node, j, group, element, privilege,
            knownGroups = [], serverid,
            setRequest = '',
            setRequestB, newValue;

        for (i = 0; i < values.length; i++) {
            node = window.sitemap.getElementByIdHack(values[i]);
            serverid = node.getAttribute('serverid');

            // Find usergroups
            for (j = 0; j < node.attributes.length; j++) {
                if (node.attributes[j].name.substr(0, 7) === 'g_view_') {
                    group = node.attributes[j].name.substr(7);
                    knownGroups.push(group);
                }
            }

            setRequestB = '';

            for (j = 0; j < knownGroups.length; j++) {
                group = knownGroups[j];

                // Set view access
                element = document.getElementById('access_' + group);
                newValue = element.checked ? 'true' : 'false';
                if (newValue !== node.getAttribute('g_view_' + group)) {
                    node.setAttribute('g_view_' + group, newValue);
                    setRequestB = setRequestB + '&' + i + 'g_view_' + group + '=' + ((newValue === 'true') ? 1 : 0);
                }
            }

            // Set privileges for all usergroups
            id = node.getAttribute('id');
            if (window.attributesFull === undefined) {
                window.attributesFull = [];
            }
            if (window.attributesFull[id] === undefined) {
                window.attributesFull[id] = node.attributes;
            }
            for (var name in window.attributesFull[id]) {
                var value = window.attributesFull[id][name];
                if (name.substr(0, 'privilege_'.length) === 'privilege_') {
                    for (j = 0; j < knownGroups.length; j++) {
                        group = knownGroups[j];
                        privilege = name.substr('privilege_'.length);
                        value = window.attributesFull[id]['group_privileges_' + privilege + '_' + group];
                        if (!value) {
                            value = -1;
                        }
                        element = document.getElementById('access_' + group + '_privilege_' + privilege);
                        if (element) {
                            newValue = element.selectedIndex - 1;
                            if (newValue !== Number(value)) {
                                window.attributesFull[id]['group_privileges_' + privilege + '_' + group] = newValue;
                                setRequestB = setRequestB + '&' + i + 'group_privileges_' + privilege + '_' + group + '=' + newValue;
                            }
                        }
                    }
                }

                // Update UI indicators
                $dom.html($dom.$requireById('tree-listextra_' + id), $corePermissionManagement.permissionsImgFunc1(node, id) + $corePermissionManagement.permissionsImgFunc2(node, id));
            }

            if (setRequestB !== '') {
                setRequest = setRequest + '&map_' + i + '=' + encodeURIComponent(serverid) + setRequestB;
            }
        }

        // Send AJAX request
        if (setRequest !== '') {
            if ($cms.form.isModSecurityWorkaroundEnabled()) {
                setRequest = $cms.form.modSecurityWorkaroundAjax(setRequest);
            }
            $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,sitemap}?set_perms=1' + $cms.keep(), null, setRequest).then(function (xhr) {
                if (xhr.status !== 500) {
                    $cms.ui.alert('{!permissions:PERMISSIONS_TREE_EDITOR_SAVED;^}');
                } else {
                    $cms.ui.alert('{!INTERNAL_ERROR;^,9b2eed24ff534c9b8df3eaca81c30efa}');
                }
            });
            return;
        }

        $cms.ui.alert('{!permissions:PERMISSIONS_TREE_EDITOR_SAVED;^}');
    }

    function showPermissionSetting(ob) {
        if (ob.disabled) { // already showing default in disabled dropdown
            return;
        }
        if (ob.done) {
            return;
        }
        ob.done = true;

        if (!ob.fullSetting) {
            var serverid;

            if (window.sitemap != null) {
                var value = document.getElementById('tree-list').value;

                if (value.indexOf(',') !== -1) { // Can't find any single value, as multiple resources are selected
                    return;
                }

                var node = window.sitemap.getElementByIdHack(value);
                serverid = node.getAttribute('serverid');
            } else {
                serverid = window.permServerid + ':_new_';
            }

            var url = '{$FIND_SCRIPT_NOHTTP;,find_permissions}?serverid=' + encodeURIComponent(serverid) + '&x=' + encodeURIComponent(ob.name);
            $cms.doAjaxRequest(url + $cms.keep()).then(function (xhr) {
                if (!xhr) {
                    return;
                }
                ob.fullSetting = xhr.responseText;
                ob.title += ' [{!permissions:DEFAULT_PERMISSION;^} ' + ob.fullSetting + ']';
            });
            return;
        }

        ob.title += ' [{!permissions:DEFAULT_PERMISSION;^} ' + ob.fullSetting + ']';
    }

    function cleanupPermissionList(name) {
        // We always try and cleanup the 'custom' option if we're choosing something else (because it's confusing for it to stay there)
        var customOption = document.getElementById(name + '-custom-option');
        if (customOption) {
            customOption.parentNode.removeChild(customOption);
        }
    }

    function copyPermissionPresets(name, value, justTrack) {
        name = strVal(name);
        value = strVal(value);
        justTrack = Boolean(justTrack);

        if (value === '') {
            return false;
        }

        var madeChange = false;
        var usualSuspects = ['bypass_validation_xrange_content', 'edit_xrange_content', 'edit_own_xrange_content', 'delete_xrange_content', 'delete_own_xrange_content', 'submit_xrange_content', 'edit_cat_xrange_content'];
        var access = [2, 3, 2, 3, 2, 1, 3]; // The minimum access level that turns on each of the above permissions   NB: Also defined in resource_fs.php, so keep that in-sync

        var holder = document.getElementById(name.replaceAll('_', '-') + '-privilege-container');
        var elements = holder.getElementsByTagName('select');
        var i, j, test, stub = name + '_privilege_', name2, x;

        var node = null;
        if (window.sitemap != null) {
            node = window.sitemap.getElementByIdHack(document.getElementById('tree-list').value.split(',')[0]);
        }

        if (value !== '-1') {
            for (i = 0; i < elements.length; i++) {
                if (elements[i].name.indexOf('presets') !== -1) {
                    continue;
                }

                if (window.sitemap == null) {
                    elements[i].disabled = false;
                }
                test = -1;
                name2 = elements[i].name.substr(stub.length);
                x = name2.replace(/(high|mid|low)/, 'x');

                for (j = 0; j < usualSuspects.length; j++) {
                    if (usualSuspects[j] === x) {
                        test = (access[j] <= parseInt(value)) ? 1 : 0;
                        break;
                    }
                }
                if ((test !== -1) || ((node) && (node.getAttribute('serverid') !== '_root'))) {
                    if (elements[i].selectedIndex !== test + 1) {
                        madeChange = true;
                        if (elements[i].selectedIndex !== test + 1) {
                            madeChange = true;
                            if (!justTrack) {
                                elements[i].selectedIndex = test + 1; // -1 is at index 0
                            }
                        }
                    }
                }
            }
        } else {
            for (i = 0; i < elements.length; i++) {
                if (elements[i].name.indexOf('presets') !== -1) {
                    continue;
                }

                if (window.sitemap == null) {
                    elements[i].disabled = true;
                }
                // Any disabled ones will be set to show the default permission rather than the "use-default" one, WHILE all-global is on
                elements[i].selectedIndex = window[elements[i].name] + 1; // -1 is at index 0
            }
        }

        if (!justTrack && (elements.length === 2) && (madeChange)) {
            $cms.ui.alert('{!permissions:JUST_PRESETS;^}');
        }

        return madeChange;
    }

    function setupPrivilegeOverrideSelector(name, defaultAccess, privilege, title, allGlobal) {
        window[name + '_privilege_' + privilege] = defaultAccess;

        var selectElement = document.getElementById(name + '_privilege_' + privilege);
        if (allGlobal) {
            // Any disabled ones will be set to show the default permission rather than the "use-default" one, WHILE all-global is on
            selectElement.selectedIndex = window[name + '_privilege_' + privilege] + 1; // -1 is at index 0
            if (window.sitemap == null) {
                selectElement.disabled = true;
            }
        }
    }

    // =========================================
    // These are for the Permissions Tree Editor
    // =========================================
    function permissionsImgFunc1(node, id) {
        var temp = permissionsImgFunc1b(node, id);
        var url = temp[0];
        var title = temp[1];
        return '<img class="vertical-alignment perm-icon" width="36" height="21" style="width: 36px; height: 21px;" src="' + url + '" alt="' + title + '" title="' + title + '" />&nbsp;';


        function permissionsImgFunc1b(node, id) {
            var group = document.getElementById('group').value;

            if (id === undefined) {
                id = node.getAttribute('id');
            }

            if (window.attributesFull === undefined) {
                window.attributesFull = [];
            }

            if (window.attributesFull[id] === undefined) {
                window.attributesFull[id] = node.attributes;
            }

            if (((window.attributesFull[id]['group_privileges_delete_highrange_content_' + group]) && (window.attributesFull[id]['group_privileges_delete_highrange_content_' + group] === '1')) ||
                ((window.attributesFull[id]['group_privileges_delete_midrange_content_' + group]) && (window.attributesFull[id]['group_privileges_delete_midrange_content_' + group] === '1')) ||
                ((window.attributesFull[id]['group_privileges_delete_lowrange_content_' + group]) && (window.attributesFull[id]['group_privileges_delete_lowrange_content_' + group] === '1'))) {
                return [$util.srl('{$IMG;,perm_levels/3}'), '{!permissions:PINTERFACE_LEVEL_3;^}'];
            } else if (((window.attributesFull[id]['group_privileges_bypass_validation_highrange_content_' + group]) && (window.attributesFull[id]['group_privileges_bypass_validation_highrange_content_' + group] === '1')) ||
                ((window.attributesFull[id]['group_privileges_bypass_validation_midrange_content_' + group]) && (window.attributesFull[id]['group_privileges_bypass_validation_midrange_content_' + group] === '1')) ||
                ((window.attributesFull[id]['group_privileges_bypass_validation_lowrange_content_' + group]) && (window.attributesFull[id]['group_privileges_bypass_validation_lowrange_content_' + group] === '1'))) {
                return [$util.srl('{$IMG;,perm_levels/2}'), '{!permissions:PINTERFACE_LEVEL_2;^}'];
            } else if (((window.attributesFull[id]['group_privileges_submit_highrange_content_' + group]) && (window.attributesFull[id]['group_privileges_submit_highrange_content_' + group] === '1')) ||
                ((window.attributesFull[id]['group_privileges_submit_midrange_content_' + group]) && (window.attributesFull[id]['group_privileges_submit_midrange_content_' + group] === '1')) ||
                ((window.attributesFull[id]['group_privileges_submit_lowrange_content_' + group]) && (window.attributesFull[id]['group_privileges_submit_lowrange_content_' + group] === '1'))) {
                return [$util.srl('{$IMG;,perm_levels/1}'), '{!permissions:PINTERFACE_LEVEL_1;^}'];
            } else if (window.attributesFull[id]['inherits_something']) {
                return [$util.srl('{$IMG;,perm_levels/inherit}'), '{!permissions:PINTERFACE_LEVEL_GLOBAL;^}'];
            } else if (window.attributesFull[id]['no_privileges']) {
                return [$util.srl('{$IMG;,blank}'), ''];
            }

            return [$util.srl('{$IMG;,perm_levels/0}'), '{!permissions:PINTERFACE_LEVEL_0;^}'];
        }
    }

    function permissionsImgFunc2(node, id) {
        var temp = permissionsImgFunc2b(node, id);
        var url = temp[0];
        var title = temp[1];
        return '<img class="vertical-alignment" width="10" height="10" style="width: 10px; height: 10px;" src="' + url + '" alt="' + title + '" title="' + title + '" />';

        function permissionsImgFunc2b(node, id) {
            if (id === undefined) {
                id = node.getAttribute('id');
            }

            var group = document.getElementById('group').value;

            if (node.getAttribute('g_view_' + group) === 'true') {
                return [$util.srl('{$IMG;,led/on}'), '{!permissions:PINTERFACE_VIEW;^}'];
            }

            return [$util.srl('{$IMG;,led/off}'), '{!permissions:PINTERFACE_VIEW_NO;^}'];
        }
    }
}(window.$cms, window.$util, window.$dom));
