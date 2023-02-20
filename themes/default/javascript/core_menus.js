/* The menu editor */

(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.menuSitemap = function (params, container) {
        var menuId = strVal(params.menuSitemapId),
            content = arrVal($dom.data(container, 'tpMenuContent'));

        generateMenuSitemap($dom.$('#' + menuId), content, 0);

        // ==============================
        // DYNAMIC TREE CREATION FUNCTION
        // ==============================
        function generateMenuSitemap(targetEl, structure, theLevel) {
            structure = arrVal(structure);
            theLevel = Number(theLevel) || 0;

            var allExpanded = ($cms.pageUrl().searchParams.get('keep_expand_sitemap') === '1');

            if (theLevel === 0) {
                $dom.empty(targetEl);
                var ul = document.createElement('ul');
                $dom.append(targetEl, ul);
                targetEl = ul;
            }

            var node;
            for (var i = 0; i < structure.length; i++) {
                node = structure[i];
                _generateMenuSitemap(targetEl, node, theLevel);
            }

            function _generateMenuSitemap(target, node, theLevel) {
                theLevel = Number(theLevel) || 0;

                var branchId = 'sitemap_menu_branch_' + $util.random(),
                    li = $dom.create('li', {
                        id: branchId,
                        className: (node.current ? 'current' : 'non-current') + ' ' + (node.img ? 'has-img' : 'has-no-img'),
                        dataset: {
                            toggleableTray: '{}'
                        }
                    });

                var span = $dom.create('span');
                $dom.append(li, span);

                if (node.img) {
                    $dom.append(span, $dom.create('img', { src: node.img }));
                    $dom.append(span, document.createTextNode(' '));
                }

                var a = $dom.create(node.url ? 'a' : 'span');
                if (node.url) {
                    if (node.tooltip) {
                        a.title = node.caption + ': ' + node.tooltip;
                    }
                    a.href = node.url;
                }

                $dom.append(span, a);
                $dom.html(a, node.caption);
                $dom.append(target, li);

                if (node.children && node.children.length) {
                    var ul = $dom.create('ul', {
                        id: 'sitemap_menu_children_' + $util.random(),
                        className: 'toggleable-tray js-tray-content'
                    });
                    // Show expand icon...
                    $dom.append(span, document.createTextNode(' '));

                    var expand = $dom.create('a', {
                        className: 'menu-sitemap-item-a toggleable-tray-button',
                        href: '#!',
                        dataset: {
                            clickTrayToggle: '#' + branchId
                        }
                    });

                    if (!allExpanded) {
                        /*{$SET,contract_icon,{+START,INCLUDE,ICON}NAME=trays/contract{+END}}*/
                        /*{$SET,expand_icon,{+START,INCLUDE,ICON}NAME=trays/expand{+END}}*/
                        if (theLevel < 2) { // High-levels start expanded
                            $dom.append(expand, '{$GET;^,contract_icon}');
                        } else {
                            $dom.hide(ul);
                            $dom.append(expand, '{$GET;^,expand_icon}');
                        }

                        $dom.append(span, expand);
                    }

                    // Show children...
                    $dom.append(li, ul);
                    generateMenuSitemap(ul, node.children, theLevel + 1);
                }
            }
        }
    };

    $cms.templates.menuEditorScreen = function (params, container) {
        var menuEditorWrapEl = $dom.$(container, '.js-el-menu-editor-wrap');

        window.allMenus = params.allMenus;

        $dom.$('#url').addEventListener('dblclick', doubleClick);
        $dom.$('#caption_long').addEventListener('dblclick', doubleClick);
        $dom.$('#page_only').addEventListener('dblclick', doubleClick);

        if (window.jQuery && (window.jQuery.fn.select2 != null)) {
            window.jQuery('#branch_type, #include_sitemap').select2(); // NB: #theme_img_code will also be select2, but that is handled via formScreenInputList
        }

        window.currentSelection = '';
        $cms.requireJavascript('tree_list').then(function () {
            window.sitemap = $cms.ui.createTreeList('tree-list', '{$FIND_SCRIPT_NOHTTP;,sitemap}?get_perms=0' + $cms.keep() + '&start_links=1', null, '', false, null, false, true);
        });

        function doubleClick() {
            if (!menuEditorWrapEl.classList.contains('docked')) {
                $dom.smoothScroll($dom.findPosY(document.getElementById('caption_' + window.currentSelection)));
            }
        }

        $dom.on(container, 'click', '.js-click-menu-editor-add-new-page', function () {
            var form = $dom.$('#edit-form');

            $cms.ui.prompt(
                $cms.configOption('single_public_zone') ? '{!javascript:ENTER_ZONE_SPZ;^}' : '{!javascript:ENTER_ZONE;^}', '', null, '{!menus:SPECIFYING_NEW_PAGE;^}'
            ).then(function (zone) {
                if (zone != null) {
                    $cms.ui.prompt(
                        '{!javascript:ENTER_PAGE;^}', '', null, '{!menus:SPECIFYING_NEW_PAGE;^}'
                    ).then(function (page) {
                        if (page != null) {
                            form.elements['url'].value = zone + ':' + page;
                        }
                    });
                }
            });
        });

        $dom.on(container, 'change', '.js-tree-input-change-update-selection', function (e, input) {
            var el = $dom.$('#url_' + window.currentSelection),
                urlEl,
                captionEl;

            if (!el) {
                return;
            }

            el.value = input.value;

            urlEl = $dom.$('#edit-form').elements['url'];
            urlEl.value = input.value;

            captionEl = $dom.$('#edit-form').elements['caption_' + window.currentSelection];
            if ((captionEl.value === '') && input.selectedTitle) {
                captionEl.value = input.selectedTitle.replace(/^.*:\s*/, '');
            }
        });

        $dom.on(container, 'click', '.js-click-preview-menu', function (e, button) {
            doMenuPreview(e, button, params.menuType);
        });

        $dom.on(container, 'click', '.js-click-save-menu', function (e, button) {
            doMenuSave(e, button);
        });

        $dom.on(container, 'click', '.js-click-toggle-docked-field-editing', toggleDockedFieldEditing);

        function toggleDockedFieldEditing(e, clicked) {
            if (!menuEditorWrapEl.classList.contains('docked')) {
                menuEditorWrapEl.classList.add('docked');
                menuEditorWrapEl.classList.remove('non-docked');
                $cms.ui.setIcon(clicked.querySelector('.icon'), 'arrow_box/arrow_box_hover', '{$IMG;^,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/arrow_box/arrow_box_hover}');
                adjustPaneHeights(true);
            } else {
                menuEditorWrapEl.classList.add('non-docked');
                menuEditorWrapEl.classList.remove('docked');
                $cms.ui.setIcon(clicked.querySelector('.icon'), 'arrow_box/arrow_box', '{$IMG;^,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/arrow_box/arrow_box}');
                adjustPaneHeights(false);
            }
        }

        var footers = document.getElementsByTagName('footer');
        for (var i = 0; i < footers.length; i++) {
            footers[i].parentNode.removeChild(footers[i]);
        }

        adjustPaneHeights(false);
        $dom.on(window, 'resize', function () {
            adjustPaneHeights();
        });
    };

    $cms.templates.menuEditorBranchWrap = function menuEditorBranchWrap(params, container) {
        var id = strVal(params.i),
            sIndex = Number(params.branchType) || 0;

        if (params.clickableSections) {
            sIndex = (sIndex === 0) ? 0 : (sIndex - 1);
        }

        document.getElementById('branch_type_' + id).selectedIndex = sIndex;

        $dom.on(container, 'focus', '.js-focus-make-caption-field-selected', function (e, focused) {
            makeFieldSelected(focused);
        });

        $dom.on(container, 'dblclick', '.js-dblclick-scroll-to-heading', function () {
            if (!document.getElementById('menu-editor-wrap').classList.contains('docked')) {
                $dom.smoothScroll($dom.findPosY(document.getElementsByTagName('h2')[2]));
            }
        });

        $dom.on(container, 'click', '.js-click-delete-menu-branch', function (e, clicked) {
            deleteMenuBranch(clicked);
        });

        $dom.on(container, 'click', '.js-click-menu-editor-branch-type-change', function () {
            menuEditorBranchTypeChange(id);
        });

        $dom.on(container, 'change', '.js-change-menu-editor-branch-type-change', function () {
            menuEditorBranchTypeChange(id);
        });

        $dom.on(container, 'click', '.js-click-btn-move-down-handle-ordering', function (e, btn) {
            handleOrdering(btn, /*up*/false, /*down*/true);
        });

        $dom.on(container, 'click', '.js-click-btn-move-up-handle-ordering', function (e, btn) {
            handleOrdering(btn, /*up*/true, /*down*/false);
        });

        function makeFieldSelected(el) {
            if (el.classList.contains('menu-editor-selected-field')) {
                return;
            }

            el.classList.add('menu-editor-selected-field');

            var changed = false;
            for (var i = 0; i < el.form.elements.length; i++) {
                if ((el.form.elements[i].classList.contains('menu-editor-selected-field')) && (el.form.elements[i] !== el)) {
                    el.form.elements[i].classList.remove('menu-editor-selected-field');
                    changed = true;
                }
            }

            copyFieldsIntoBottom(el.id.substr(8), changed);
        }

        /**
         * @param element
         * @param upwards
         */
        function handleOrdering(element, upwards) {
            var form = $dom.$('#edit-form');

            // Find the num
            var index = element.id.substring(element.id.indexOf('_') + 1, element.id.length),
                num = parseInt(form.elements['order_' + index].value) || 0;

            // Find the parent
            var parentNum = $dom.$('#parent_' + index).value,
                i, b, bindex,
                best = -1;

            if (upwards) { // Up
                // Find previous branch with same parent (if exists)
                for (i = 0; i < form.elements.length; i++) {
                    if ((form.elements[i].name.startsWith('parent_')) && (form.elements[i].value === parentNum)) {
                        bindex = form.elements[i].name.substr('parent_'.length, form.elements[i].name.length);
                        b = parseInt(form.elements['order_' + bindex].value) || 0;
                        if ((b < num) && (b > best)) {
                            best = b;
                        }
                    }
                }
            } else { // Down
                // Find next branch with same parent (if exists)
                for (i = 0; i < form.elements.length; i++) {
                    if ((form.elements[i].name.startsWith('parent_')) && (form.elements[i].value === parentNum)) {
                        bindex = form.elements[i].name.substr('parent_'.length, form.elements[i].name.length);
                        b = parseInt(form.elements['order_' + bindex].value);
                        if ((b > num) && ((b < best) || (best === -1))) {
                            best = b;
                        }
                    }
                }
            }

            var elements = form.querySelectorAll('input');
            for (i = 0; i < elements.length; i++) {
                if (elements[i].name === 'parent_' + index) { // Found our spot
                    var el = elements[i];
                    for (b = upwards ? (i - 1) : (i + 1); upwards ? (b > 0) : (b < elements.length); upwards ? b-- : b++) {
                        if ((!isChild(elements, index, elements[b].name.substr('parent_'.length))) && (elements[b].name.startsWith('parent_') && ((upwards) || (document.getElementById('branch_type_' + elements[b].name.substr('parent_'.length)).selectedIndex === 0) || (!existsChild(elements, elements[b].name.substr('parent_'.length)))))) {
                            var target = elements[b];
                            var main = el.parentNode.parentNode;
                            var place = target.parentNode.parentNode;
                            if ((upwards && (branchDepth(target) <= branchDepth(el))) || ((branchDepth(target) !== branchDepth(el)))) {
                                main.parentNode.removeChild(main);
                                place.parentNode.insertBefore(main, place);
                            } else {
                                main.parentNode.removeChild(main);
                                if (!place.nextSibling) {
                                    place.parentNode.appendChild(main);
                                } else {
                                    place.parentNode.insertBefore(main, place.nextSibling);
                                }
                            }
                            el.value = target.value;
                            return;
                        }
                    }
                }
            }

            function isChild(elements, possibleParent, possibleChild) {
                for (var i = 0; i < elements.length; i++) {
                    if ((elements[i].name.substr('parent_'.length) === possibleChild) && (elements[i].name.substr(0, 'parent_'.length) === 'parent_')) {
                        if (elements[i].value === possibleParent) {
                            return true;
                        }

                        return isChild(elements, possibleParent, elements[i].value);
                    }
                }

                return false;
            }

            function branchDepth(branch) {
                if (branch.parentNode) {
                    return branchDepth(branch.parentNode) + 1;
                }

                return 0;
            }
        }

        function deleteMenuBranch(ob) {
            var id = ob.id.substring(4, ob.id.length);

            if (((window.showModalDialog !== undefined) || $cms.configOption('js_overlays')) || (ob.form.elements['branch_type_' + id] !== 'page')) {
                var choices = { 'buttons/cancel': '{!INPUTSYSTEM_CANCEL;^}', 'admin/delete3': '{!DELETE;^}', 'admin/move': '{!menus:MOVETO_MENU;^}' };
                $cms.ui.generateQuestionUi(
                    '{!menus:CONFIRM_DELETE_LINK_NICE;^,xxxx}'.replace('xxxx', document.getElementById('caption_' + id).value),
                    choices,
                    '{!menus:DELETE_MENU_ITEM;^}',
                    null
                ).then(function (result) {
                    if (result.toLowerCase() === '{!DELETE;^}'.toLowerCase()) {
                        deleteBranch('branch-wrap-' + ob.name.substr(4, ob.name.length));
                    } else if (result.toLowerCase() === '{!menus:MOVETO_MENU;^}'.toLowerCase()) {
                        var choices = { 'buttons/cancel': '{!INPUTSYSTEM_CANCEL;^}' };
                        for (var i = 0; i < window.allMenus.length; i++) {
                            choices['no_icon_' + i] = window.allMenus[i];
                        }
                        return $cms.ui.generateQuestionUi(
                            '{!menus:CONFIRM_MOVE_LINK_NICE;^,xxxx}'.replace('xxxx', document.getElementById('caption_' + id).value),
                            choices,
                            '{!menus:MOVE_MENU_ITEM;^}',
                            null
                        );
                    }

                    // Halt here unless we're moving an item
                    return $util.promiseHalt();
                }).then(function (answer) {
                    if (answer.toLowerCase() === '{!INPUTSYSTEM_CANCEL;^}'.toLowerCase()) {
                        return;
                    }

                    var post = '', name, value;
                    for (var i = 0; i < ob.form.elements.length; i++) {
                        name = ob.form.elements[i].name;
                        if (name.substr(name.length - (('_' + id).length)) === '_' + id) {
                            if (ob.localName === 'select') {
                                value = ob.form.elements[i].value;
                                window.myValue = ob.value;
                            } else {
                                if ((ob.type.toLowerCase() === 'checkbox') && !ob.checked) {
                                    continue;
                                }

                                value = ob.form.elements[i].value;
                            }
                            if (post !== '') {
                                post += '&';
                            }
                            post += name + '=' + encodeURIComponent(value);
                        }
                    }

                    $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,menu_management}' + '?id=' + encodeURIComponent(id) + '&menu=' + encodeURIComponent(answer) + $cms.keep(), null, post);
                    deleteBranch('branch-wrap-' + ob.name.substr(4, ob.name.length));
                });
            } else {
                $cms.ui.confirm('{!menus:CONFIRM_DELETE_LINK;^,xxxx}'.replace('xxxx', document.getElementById('caption_' + id).value)).then(function (result) {
                    if (result) {
                        deleteBranch('branch-wrap-' + ob.name.substr(4, ob.name.length));
                    }
                });
            }
        }
    };

    $cms.templates.menuEditorBranch = function menuEditorBranch(params, container) {
        var parentId = strVal(params.i),
            clickableSections = Boolean(params.clickableSections);

        $dom.on(container, 'click', '.js-click-add-new-menu-item', function (e) {
            e.stopPropagation();

            var insertBeforeId = 'branches-go-before-' + parentId,
                template = $dom.$id('template').value,
                before = $dom.$id(insertBeforeId),
                newId = Math.floor(Math.random() * 10000),
                template2 = template.replace(/replace_me_with_random/gi, newId),
                highestOrderElement = $dom.$id('highest_order'),
                newOrder = highestOrderElement.value + 1;

            highestOrderElement.value++;
            template2 = template2.replace(/replace_me_with_order/gi, newOrder);
            template2 = template2.replace(/replace_me_with_parent/gi, parentId);

            // Backup form branches
            var form = $dom.$id('edit-form'),
                _elementsBak = form.elements,
                elementsBak = [], i;

            for (i = 0; i < _elementsBak.length; i++) {
                elementsBak.push([_elementsBak[i].name, _elementsBak[i].value]);
            }

            $dom.append(before, template2); // Technically we are actually putting after "branches-go-before-XXX", but it makes no difference. It only needs to act as a divider.

            // Restore form branches
            for (i = 0; i < elementsBak.length; i++) {
                if (elementsBak[i][0]) {
                    form.elements[elementsBak[i][0]].value = elementsBak[i][1];
                }
            }

            if (!clickableSections) {
                menuEditorBranchTypeChange(newId);
            }

            $dom.slideUp('#mini-form-hider');
        });
    };

    function menuEditorBranchTypeChange(id) {
        var disabled = (document.getElementById('branch_type_' + id).value !== 'page'),
            sub = $dom.$id('branch-' + id + '-follow-1'),
            sub2 = $dom.$id('branch-' + id + '-follow-2');

        if (sub) {
            $dom.toggle(sub, disabled);
        }

        if (sub2) {
            $dom.toggle(sub2, disabled);
        }
    }

    $cms.templates.pageLinkChooser = function pageLinkChooser(params, container) {
        var ajaxUrl = '{$FIND_SCRIPT_NOHTTP;,sitemap}?get_perms=0' + $cms.keep() + '&start_links=1';

        if (params.pageType != null) {
            ajaxUrl += '&page_type=' + params.pageType;
        }

        $cms.requireJavascript('tree_list').then(function () {
            $cms.ui.createTreeList(params.name, ajaxUrl, '', '', false, null, false, true);
        });

        $dom.on(container, 'change', '.js-input-page-link-chooser', function (e, input) {
            if (params.getTitleToo) {
                if (input.selectedTitle === undefined) {
                    input.value = '';/*was autocomplete, unwanted*/
                } else {
                    input.value += ' ' + input.selectedTitle;
                }
            }

            if (!params.asField) {
                window.returnValue = input.value;
                if (window.fauxClose !== undefined) {
                    window.fauxClose();
                } else {
                    window.close();
                }
            }
        });
    };

    function doMenuPreview(e, button, menuType) {
        if (!checkMenu()) {
            e.preventDefault();
            return;
        }

        var form = button.form;

        $cms.ui.disableButton(button);

        if (form.originalURL === undefined) {
            form.originalURL = form.action;
        }

        form.target = '_blank';
        form.action = '{$FIND_SCRIPT;,preview}?page=admin_menus&menu_type=' + encodeURIComponent(menuType ? menuType : '') + $cms.keep();
    }

    function doMenuSave(e, button) {
        if (!checkMenu()) {
            e.preventDefault();
            return;
        }

        var form = button.form;

        $cms.ui.disableButton(button);

        if (form.originalURL !== undefined) {
            form.action = form.originalURL;
        }

        form.target = '_self';
    }

    function adjustPaneHeights(docked) {
        var menuEditorWrapEl = $dom.$('.js-el-menu-editor-wrap');
        if (typeof docked === undefined) {
            docked = menuEditorWrapEl.classList.contains('docked');
        }
        if (docked) {
            var miniFormHider = document.getElementById('mini-form-hider');
            var newHeight = $dom.getWindowHeight() - $dom.findPosY(menuEditorWrapEl, true) - $dom.height(miniFormHider) - 10;
            if (newHeight < 0) {
                newHeight = 0;
            }
            menuEditorWrapEl.style.height = newHeight + 'px';
        } else {
            menuEditorWrapEl.style.height = '';
        }
    }

    function copyFieldsIntoBottom(i, changed) {
        window.currentSelection = i;
        var form = $dom.$id('edit-form');

        // Note that we use old-school event handlers because we need to overwrite the previous event handler for the previous selected field

        form.elements['caption_long'].value = $dom.$id('caption_long_' + i).value;
        form.elements['caption_long'].onchange = function () {
            $dom.$('#caption_long_' + i).value = this.value;
            $dom.$('#caption_long_' + i).disabled = (this.value === '');
        };

        form.elements['url'].value = $dom.$id('url_' + i).value;
        form.elements['url'].onchange = function () {
            $dom.$('#url_' + i).value = this.value;
        };

        form.elements['page_only'].value = $dom.$id('page_only_' + i).value;
        form.elements['page_only'].onchange = function () {
            $dom.$('#page_only_' + i).value = this.value;
            $dom.$('#page_only_' + i).disabled = (this.value === '');
        };

        var s;
        for (s = 0; s < form.elements['theme_img_code'].options.length; s++) {
            if (document.getElementById('theme_img_code_' + i).value === form.elements['theme_img_code'].options[s].value) {
                break;
            }
        }
        if (s === form.elements['theme_img_code'].options.length) {
            s = 0;
            $cms.ui.alert('{!menus:MISSING_THEME_IMAGE_FOR_MENU;^}'.replace(/\\{1\\}/, $cms.filter.html($dom.$id('theme_img_code_' + i).value)));
        }
        form.elements['theme_img_code'].selectedIndex = s;
        form.elements['theme_img_code'].onchange = function () {
            $dom.$('#theme_img_code_' + i).value = this.value;
            $dom.$('#theme_img_code_' + i).disabled = (this.selectedIndex === 0);
        };
        if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery(form.elements['theme_img_code']).trigger('change');
        }

        form.elements['new_window'].checked = $dom.$id('new_window_' + i).value === '1';
        form.elements['new_window'].onclick = function () {
            $dom.$('#new_window_' + i).value = this.checked ? '1' : '0';
            $dom.$('#new_window_' + i).disabled = !this.checked;
        };

        form.elements['check_perms'].checked = $dom.$id('check_perms_' + i).value === '1';
        form.elements['check_perms'].onclick = function () {
            $dom.$('#check_perms_' + i).value = this.checked ? '1' : '0';
            $dom.$('#check_perms_' + i).disabled = !this.checked;
        };

        form.elements['branch_type'].selectedIndex = $dom.$id('branch_type_' + i).selectedIndex;
        form.elements['branch_type'].onchange = function (event) {
            $dom.$('#branch_type_' + i).selectedIndex = this.selectedIndex;
            if ($dom.$('#branch_type_' + i).onchange) {
                $dom.$('#branch_type_' + i).onchange(event);
            }
        };
        if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery(form.elements['branch_type']).trigger('change');
        }

        form.elements['include_sitemap'].selectedIndex = $dom.$id('include_sitemap_' + i).value;
        form.elements['include_sitemap'].onchange = function () {
            $dom.$('#include_sitemap_' + i).value = this.selectedIndex;
            $dom.$('#include_sitemap_' + i).disabled = (this.selectedIndex === 0);
        };
        if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery(form.elements['include_sitemap']).trigger('change');
        }

        var mfh = $dom.$('#mini-form-hider');
        $dom.slideDown(mfh);
        if (changed) {
            $dom.fadeIn(form.elements['url']);
        }
    }

    function existsChild(elements, parent) {
        for (var i = 0; i < elements.length; i++) {
            if ((elements[i].name.substr(0, 'parent_'.length) === 'parent_') && (elements[i].value === parent)) {
                return true;
            }
        }

        return false;
    }

    function deleteBranch(id) {
        var branch = $dom.$id(id);
        branch.parentNode.removeChild(branch);
    }

    function checkMenu() {
        var form = $dom.$('#edit-form');
        var i, id, name, theParent, ignore, caption, url, branchType;
        for (i = 0; i < form.elements.length; i++) {
            name = form.elements[i].name.substr(0, 'parent_'.length);
            if (name === 'parent_') { // We don't care about this, but it does tell us we have found a menu branch ID

                id = form.elements[i].name.substring('parent_'.length, form.elements[i].name.length);

                // Is this visible? (if it is we need to check the IDs
                theParent = form.elements[i];
                do {
                    if (theParent.style.display === 'none') {
                        ignore = true;
                        break;
                    }
                    theParent = theParent.parentNode;
                } while (theParent.parentNode);

                if (!ignore) { // It's the real deal
                    // Check we have a caption
                    caption = $dom.$id('caption_' + id);
                    url = $dom.$id('url_' + id);
                    if ((caption.value === '') && (url.value !== '')) {
                        $cms.ui.alert('{!menus:MISSING_CAPTION_ERROR;^}');
                        return false;
                    }

                    // If we are a page, check we have a URL
                    branchType = $dom.$id('branch_type_' + id);
                    if (branchType.value === 'page') {
                        if ((caption.value !== '') && (url.value === '')) {
                            $cms.ui.alert('{!menus:MISSING_URL_ERROR;^}');
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
}(window.$cms, window.$util, window.$dom));
