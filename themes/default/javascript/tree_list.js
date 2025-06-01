(function ($cms, $util, $dom) {
    'use strict';

    /**
     * @memberof $cms.ui
     * @param name
     * @param ajaxUrl
     * @param rootId
     * @param opts
     * @param multiSelection
     * @param tabindex
     * @param allNodesSelectable
     * @param useServerId
     * @returns { $cms.views.TreeList }
     */
    $cms.ui.createTreeList = function createTreeList(name, ajaxUrl, rootId, opts, multiSelection, tabindex, allNodesSelectable, useServerId) {
        var options = {
                name: name,
                ajaxUrl: ajaxUrl,
                rootId: rootId,
                options: opts,
                multiSelection: multiSelection,
                tabindex: tabindex,
                allNodesSelectable: allNodesSelectable,
                useServerId: useServerId
            },
            el = $dom.$id('tree-list--root-' + name);

        return new $cms.views.TreeList(options, {el: el});
    };

    $cms.views.TreeList = TreeList;
    /**
     * @memberof $cms.views
     * @class TreeList
     * @extends $cms.View
     */
    function TreeList(params) {
        TreeList.base(this, 'constructor', arguments);

        this.name = strVal(params.name);
        this.ajaxUrl = strVal(params.ajaxUrl);
        this.options = strVal(params.options);
        this.multiSelection = Boolean(params.multiSelection);
        this.tabindex = strVal(params.tabindex, null);
        this.allNodesSelectable = Boolean(params.allNodesSelectable);
        this.useServerId = Boolean(params.useServerId);

        $dom.html(this.el, '<div class="ajax-loading vertical-alignment"><img width="20" height="20" src="' + $util.srl('{$IMG*;^,loading}') + '" alt="" /> <span>{!LOADING;^}</span></div>');

        // Initial rendering
        var url = this.ajaxUrl,
            that = this;
        if (params.rootId) {
            url += '&id=' + encodeURIComponent(params.rootId);
        }
        url += '&options=' + this.options;
        url += '&default=' + encodeURIComponent($dom.$id(this.name).value);

        $cms.doAjaxRequest(url).then(function (xhr) {
            that.response(xhr);
        });

        $dom.on(document.documentElement, 'mousemove', function (event) {
            that.specialKeyPressed = Boolean(event.ctrlKey || event.altKey || event.metaKey || event.shiftKey);
        });
    }

    $util.inherits(TreeList, $cms.View, /**@lends TreeList#*/{
        specialKeyPressed: false,
        /**@type { Node }*/
        treeListData: null,
        busy: false,
        lastClicked: null, // The hyperlink object that was last clicked (usage during multi selection when holding down shift)

        /* Go through our tree list looking for a particular XML node */
        getElementByIdHack: function getElementByIdHack(id, type, ob, serverid) {
            id = strVal(id);
            type = strVal(type) || 'c';
            ob = ob || this.treeListData;
            serverid = Boolean(serverid);

            var results = ob.getElementsByTagName((type === 'c') ? 'category' : 'entry');
            for (var i = 0; i < results.length; i++) {
                if ((results[i].getAttribute !== undefined) && (results[i].getAttribute(serverid ? 'serverid' : 'id') === id)) {
                    return results[i];
                }
            }

            return null;
        },

        response: function response(xhr, expandingId) {
            var ajaxResult = xhr.responseXML && xhr.responseXML.querySelector('result');

            expandingId = strVal(expandingId);

            if (!ajaxResult) {
                return;
            }

            try {
                ajaxResult = document.importNode(ajaxResult, true);
            } catch (e) {}

            var i, xml, tempNode, html;
            if (expandingId === '') { // Root
                html = $dom.$('#tree-list--root-' + this.name);
                $dom.empty(html);

                this.treeListData = ajaxResult.cloneNode(true);
                xml = this.treeListData;

                if (!xml.firstElementChild) {
                    var isCategoryPage = (window.location.pathname.split('/').pop() || '').includes('category');
                    var error = document.createTextNode((!this.name.includes('category') && !isCategoryPage) ? '{!NO_ENTRIES;^}' : '{!NO_CATEGORIES;^}');
                    html.className = 'red-alert';
                    html.appendChild(error);
                    return;
                }
            } else { // Appending
                xml = this.getElementByIdHack(expandingId, 'c');
                if (xml.children.length !== 0) {
                    // User was likely clicking like mad, or had connection issues - as we already rendered this
                } else {
                    for (i = 0; i < ajaxResult.childNodes.length; i++) {
                        tempNode = ajaxResult.childNodes[i];
                        xml.appendChild(tempNode.cloneNode(true));
                    }
                }
                html = $dom.$id(this.name + 'tree-list-c-' + expandingId);
            }

            attributesFullFixup(xml);
            this.renderTree(xml, html);
            fixupNodePositions(this.name);
        },

        renderTree: function renderTree(xml, html, element) {
            var that = this, colour, newHtml, escapedTitle, initiallyExpanded,
                selectable, extra, func;

            element || (element = $dom.$id(this.name));

            if (xml.firstElementChild) {
                $dom.fadeIn(html);
            } else {
                $dom.hide(html);
            }

            $util.toArray(xml.children).forEach(function (node) {
                var nodeSelfWrap, nodeSelf, el, label, htmlNode, expanding;

                // Special handling of 'options' nodes, inject new options
                if (node.localName === 'options') {
                    that.options = encodeURIComponent($dom.html(node));
                    return;
                }

                // Special handling of 'expand' nodes, which say to pre-expand some categories as soon as the page loads
                if (node.localName === 'expand') {
                    el = $dom.$id(that.name + 'texp-c-' + $dom.html(node));
                    if (el) {
                        htmlNode = $dom.$id(that.name + 'tree-list-c-' + $dom.html(node));
                        expanding = (htmlNode.style.display !== 'block');
                        if (expanding) {
                            if ($dom.$('#choose-' + that.name)) {
                                $dom.$('#choose-' + that.name).click();
                            }

                            that.handleTreeClick(true, el);
                        }
                    } else {
                        // Now try against serverid
                        var xmlNode = that.getElementByIdHack($dom.html(node), 'c', null, true);
                        if (xmlNode) {
                            el = $dom.$id(that.name + 'texp-c-' + xmlNode.getAttribute('id'));
                            if (el) {
                                htmlNode = $dom.$id(that.name + 'tree-list-c-' + xmlNode.getAttribute('id'));
                                expanding = (htmlNode.style.display !== 'block');
                                if (expanding) {
                                    if ($dom.$('#choose-' + that.name)) {
                                        $dom.$('#choose-' + that.name).click();
                                    }

                                    that.handleTreeClick(true, el);
                                }
                            }
                        }
                    }
                    return;
                }

                /* Category or entry nodes */
                extra = ' ';
                func = node.getAttribute('img_func_1');
                if (func) {
                    if (func === 'permissionsImgFunc1') {
                        extra = extra + window.$corePermissionManagement.permissionsImgFunc1(node);
                    } else if (func === 'permissionsImgFunc2') {
                        extra = extra + window.$corePermissionManagement.permissionsImgFunc2(node);
                    }
                }
                func = node.getAttribute('img_func_2');
                if (func) {
                    if (func === 'permissionsImgFunc1') {
                        extra = extra + window.$corePermissionManagement.permissionsImgFunc1(node);
                    } else if (func === 'permissionsImgFunc2') {
                        extra = extra + window.$corePermissionManagement.permissionsImgFunc2(node);
                    }
                }
                nodeSelfWrap = document.createElement('div');
                nodeSelf = document.createElement('div');
                nodeSelf.className = 'tree-list-node';
                nodeSelfWrap.appendChild(nodeSelf);
                colour = (node.getAttribute('selectable') === 'true' || that.allNodesSelectable) ? 'native-ui-foreground' : 'locked-input-field';
                selectable = (node.getAttribute('selectable') === 'true' || that.allNodesSelectable);

                if (node.localName === 'category') {
                    // Render self
                    nodeSelf.className = (node.getAttribute('highlighted') === 'true') ? 'tree-list-highlighted' : 'tree-list-nonhighlighted';
                    initiallyExpanded = (node.getAttribute('has_children') !== 'true') || (node.getAttribute('expanded') === 'true');
                    escapedTitle = $cms.filter.html((node.getAttribute('title') !== undefined) ? node.getAttribute('title') : '');
                    if (escapedTitle === '') {
                        escapedTitle = '{!NA_EM;^}';
                    }
                    var description = '',
                        descriptionInUse = '';
                    if (node.getAttribute('description_html')) {
                        description = node.getAttribute('description_html');
                        descriptionInUse = description;
                    } else {
                        if (node.getAttribute('description')) {
                            description = $cms.filter.html('. ' + node.getAttribute('description'));
                        }
                        descriptionInUse = escapedTitle + ': {!TREE_LIST_SELECT*;^}' + description;
                        if (node.getAttribute('serverid')) {
                            descriptionInUse += ' ({!IDENTIFIER;}: <kbd>' + $cms.filter.html(node.getAttribute('serverid')) + '</kbd>)';
                        } else if (node.getAttribute('id')) {
                            descriptionInUse += ' ({!IDENTIFIER;}: <kbd>' + $cms.filter.html(node.getAttribute('id')) + '</kbd>)';
                        }
                    }
                    var imgUrl = $util.srl('{$IMG;,icons/tree_field/category}');
                    if (node.getAttribute('img_url')) {
                        imgUrl = node.getAttribute('img_url');
                    }

                    /*{+START,SET,tree_field_expand_icon}{+START,INCLUDE,ICON}
                        NAME=tree_field/expand
                        ICON_SIZE=11
                    {+END}{+END}*/

                    /*{+START,SET,tree_field_collapse_icon}{+START,INCLUDE,ICON}
                        NAME=tree_field/collapse
                        ICON_SIZE=11
                    {+END}{+END}*/

                    $dom.html(nodeSelf, /** @lang HTML */'' +
                        '<div>' +
                        '    <a class="ajax-tree-expand-icon"' + (that.tabindex ? (' tabindex="' + that.tabindex + '"') : '') + ' title="' + ((!initiallyExpanded) ? '{!EXPAND;^}' : '{!CONTRACT;^}') + ': ' + escapedTitle + '" id="' + that.name + 'texp-c-' + node.getAttribute('id') + '">' + (initiallyExpanded ? '{$GET;^,tree_field_collapse_icon}' : '{$GET;^,tree_field_expand_icon}') + '</a>' +
                        '    <img class="ajax-tree-cat-icon" alt="{!CATEGORY;^}" width="14" height="14" src="' + $cms.filter.html(imgUrl) + '" />' +
                        '    <label id="' + that.name + 'tsel_c_' + node.getAttribute('id') + '" for="' + that.name + 'tsel_r_' + node.getAttribute('id') + '" data-cms-tooltip="' + $cms.filter.html(descriptionInUse.replace(/\n/g, '')) + '" class="ajax-tree-magic-button ' + colour + '"> <input ' + (that.tabindex ? ('tabindex="' + that.tabindex + '" ') : '') + 'id="' + that.name + 'tsel_r_' + node.getAttribute('id') + '" style="position: absolute; left: -10000px" type="radio" name="_' + that.name + '" value="1" title="' + $cms.filter.html(descriptionInUse) + '" />' + escapedTitle + '</label>' +
                        '    <span id="' + that.name + 'extra_' + node.getAttribute('id') + '">' + extra + '</span>' +
                        '</div>');
                    var expandButton = nodeSelf.querySelector('.ajax-tree-expand-icon');
                    expandButton.oncontextmenu = function (e) { e.preventDefault(); };

                    $dom.on(expandButton, 'click', function (e) {
                        e.preventDefault();

                        if ($dom.$('#choose-' + that.name)) {
                            $dom.$('#choose-' + that.name).click();
                        }

                        that.handleTreeClick(false, expandButton);
                    });

                    label = nodeSelf.querySelector('label');
                    expandButton.onkeypress = label.onkeypress = label.firstElementChild.onkeypress = function (event) {
                        if (((event.keyCode ? event.keyCode : event.charCode) === 13) || ['+', '-', '='].includes(String.fromCharCode(event.keyCode ? event.keyCode : event.charCode))) {
                            event.preventDefault();

                            if ($dom.$('#choose-' + that.name)) {
                                $dom.$('#choose-' + that.name).click();
                            }

                            that.handleTreeClick(false, expandButton);
                        }
                    };
                    label.oncontextmenu = function (e) { e.preventDefault(); };
                    label.firstElementChild.addEventListener('focus', function () {
                        label.style.outline = '1px dotted';
                    });
                    label.firstElementChild.addEventListener('blur', function () {
                        label.style.outline = '';
                    });
                    label.firstElementChild.addEventListener('click', function (e) {
                        that.handleSelection(e, false, label.firstElementChild);
                    });
                    label.addEventListener('click', function (e) { // Needed by Firefox, the radio button's onclick will not be called if shift/ctrl held
                        that.handleSelection(e, false, label);
                    });
                    label.addEventListener('mousedown', function (event) { // To disable selection of text when holding shift or control
                        if (event.ctrlKey || event.metaKey || event.shiftKey) {
                            event.preventDefault();
                        }
                    });
                    html.appendChild(nodeSelfWrap);

                    // Do any children
                    newHtml = document.createElement('div');
                    newHtml.setAttribute('role', 'treeitem');
                    newHtml.id = that.name + 'tree-list-c-' + node.getAttribute('id');
                    newHtml.style.display = ((!initiallyExpanded) || (node.getAttribute('has_children') !== 'true')) ? 'none' : 'block';
                    newHtml.style.paddingLeft = '15px';
                    var selected = ((that.useServerId ? node.getAttribute('serverid') : node.getAttribute('id')) === element.value && element.value !== '') || node.getAttribute('selected') === 'yes';
                    if (selectable) {
                        that.makeElementLookSelected($dom.$id(that.name + 'tsel_c_' + node.getAttribute('id')), selected);
                        if (selected) {
                            // Copy in proper ID for what is selected, not relying on what we currently have as accurate
                            var newVal = strVal(that.useServerId ? node.getAttribute('serverid') : node.getAttribute('id'));

                            if (newVal !== '') {
                                if (element.selectedTitle == null) {
                                    element.selectedTitle = '';
                                }
                                if (element.selectedTitle !== '') {
                                    element.selectedTitle += ',';
                                }
                                element.selectedTitle += node.getAttribute('title');
                            }

                            $dom.value(element, newVal);
                        }
                    }
                    nodeSelf.appendChild(newHtml);

                    // Auto-expand
                    if (that.specialKeyPressed && !initiallyExpanded) {
                        if ($dom.$('#choose-' + that.name)) {
                            $dom.$('#choose-' + that.name).click();
                        }

                        that.handleTreeClick(false, expandButton);
                    }
                } else { // Assume <entry>
                    newHtml = null;

                    escapedTitle = $cms.filter.html((node.getAttribute('title') !== undefined) ? node.getAttribute('title') : '');
                    if (escapedTitle === '') {
                        escapedTitle = '{!NA_EM;^}';
                    }

                    var description2 = '',
                        descriptionInUse2 = '';
                    if (node.getAttribute('description_html')) {
                        description2 = node.getAttribute('description_html');
                        descriptionInUse2 = $cms.filter.html(description2);
                    } else {
                        if (node.getAttribute('description')) {
                            description2 = $cms.filter.html('. ' + node.getAttribute('description'));
                        }
                        descriptionInUse2 = escapedTitle + ': {!TREE_LIST_SELECT*;^}' + description2 + ((node.getAttribute('serverid') === '') ? (' (' + $cms.filter.html(node.getAttribute('serverid')) + ')') : '');
                    }

                    // Render self
                    initiallyExpanded = false;
                    var imgUrl2 = $util.srl('{$IMG;,icons/tree_field/entry}');
                    if (node.getAttribute('img_url')) {
                        imgUrl2 = node.getAttribute('img_url');
                    }
                    $dom.html(nodeSelf, '<div><img alt="{!ENTRY;^}" width="14" height="14" src="' + $cms.filter.html(imgUrl2) + '" /> ' +
                        '<label id="' + that.name + 'tsel_e_' + node.getAttribute('id') + '" class="ajax-tree-magic-button ' + colour + '" for="' + that.name + 'tsel_s_' + node.getAttribute('id') + '" data-cms-tooltip="{ contents: \'' + descriptionInUse2.replace(/\n/g, '').replace(/'/g, '\\\'') + '\', width: \'800px\'}">' +
                        '<input' + (that.tabindex ? (' tabindex="' + that.tabindex + '"') : '') + ' id="' + that.name + 'tsel_s_' + node.getAttribute('id') + '" style="position: absolute; left: -10000px" type="radio" name="_' + that.name + '" value="1" />' + escapedTitle + '</label>' + extra + '</div>');

                    label = nodeSelf.querySelector('label');
                    label.firstElementChild.addEventListener('focus', function () {
                        label.style.outline = '1px dotted';
                    });
                    label.firstElementChild.addEventListener('blur', function () {
                        label.style.outline = '';
                    });
                    label.firstElementChild.addEventListener('click', function (e) {
                        that.handleSelection(e, false, label.firstElementChild);
                    });
                    label.addEventListener('click', function (e) { // Needed by Firefox, the radio button's onclick will not be called if shift/ctrl held
                        that.handleSelection(e, false, label);
                    });
                    label.addEventListener('mousedown', function (event) { // To disable selection of text when holding shift or control
                        if (event.ctrlKey || event.metaKey || event.shiftKey) {
                            event.preventDefault();
                        }
                    });
                    html.appendChild(nodeSelfWrap);
                    var selected2 = ((that.useServerId ? node.getAttribute('serverid') : node.getAttribute('id')) === element.value) || node.getAttribute('selected') === 'yes';
                    if ((that.multiSelection) && !selected2) {
                        selected2 = (',' + element.value + ',').indexOf(',' + node.getAttribute('id') + ',') !== -1;
                    }
                    that.makeElementLookSelected($dom.$id(that.name + 'tsel_e_' + node.getAttribute('id')), selected2);
                }

                if (node.getAttribute('draggable') && (node.getAttribute('draggable') !== 'false')) {
                    fixUpNodePosition(nodeSelf);
                    nodeSelf.cmsDraggable = node.getAttribute('draggable');
                    nodeSelf.draggable = true;
                    nodeSelf.ondragstart = function () {
                        $cms.ui.clearOutTooltips();
                        nodeSelf.classList.add('being-dragged');
                        window.isDoingADrag = true;
                    };
                    nodeSelf.ondrag = function (event) {
                        if (!event.clientY) {
                            return;
                        }
                        var hit = findOverlappingSelectable(event.clientY + window.pageYOffset, nodeSelf, that.treeListData, that.name);
                        if (nodeSelf.lastHit != null) {
                            nodeSelf.lastHit.parentNode.parentNode.style.border = '0px';
                        }
                        if (hit != null) {
                            hit.parentNode.parentNode.style.border = '1px dotted green';
                            nodeSelf.lastHit = hit;
                        }
                    };
                    nodeSelf.ondragend = function () {
                        window.isDoingADrag = false;

                        nodeSelf.classList.remove('being-dragged');

                        if (nodeSelf.lastHit != null) {
                            nodeSelf.lastHit.parentNode.parentNode.style.border = '0px';

                            if (nodeSelf.parentNode.parentNode !== nodeSelf.lastHit) {
                                var xmlNode = that.getElementByIdHack(nodeSelf.querySelector('input').id.substr(7 + that.name.length));
                                var targetXmlNode = that.getElementByIdHack(nodeSelf.lastHit.id.substr(12 + that.name.length));

                                // eslint-disable-next-line no-restricted-properties
                                if ((nodeSelf.lastHit.childNodes.length === 1) && (nodeSelf.lastHit.childNodes[0].nodeName === '#text')) {
                                    $dom.empty(nodeSelf.lastHit);
                                    that.renderTree(targetXmlNode, nodeSelf.lastHit);
                                }

                                // Change HTML
                                nodeSelf.parentNode.parentNode.removeChild(nodeSelf.parentNode);
                                nodeSelf.lastHit.appendChild(nodeSelf.parentNode);

                                // Change node structure
                                xmlNode.parentNode.removeChild(xmlNode);
                                targetXmlNode.appendChild(xmlNode);

                                // Ajax request
                                if (xmlNode.getAttribute('draggable') === 'page') {
                                    dragPage(xmlNode.getAttribute('serverid'), targetXmlNode.getAttribute('serverid'));
                                }

                                fixupNodePositions(that.name);
                            }
                        }
                    };
                }

                if ((node.getAttribute('droppable')) && (node.getAttribute('droppable') !== 'false')) {
                    nodeSelf.ondragover = function (event) {
                        event.preventDefault();
                    };
                    nodeSelf.ondrop = function (event) {
                        event.preventDefault();
                        // ondragend will call with lastHit set, we don't track the drop spots using this event handler, we track it in real time using mouse coordinate analysis
                    };
                }

                if (initiallyExpanded) {
                    that.renderTree(node, newHtml, element);
                } else if (newHtml) {
                    $dom.append(newHtml, '{!PLEASE_WAIT;^}');
                }
            });

            $dom.triggerResize();

            function dragPage(from, to) {
                var newZone = to.replace(/:/, ''),
                    bits = from.split(/:/),
                    moveUrl = '{$PAGE_LINK;,_SEARCH:admin_sitemap:_move:zone=[1]:destination_zone=[3]:page__[2]=1,0,1}' + $cms.keep();

                if (bits.length === 1) {// Workaround IE bug
                    bits.push(bits[0]);
                    bits[0] = '';
                }

                var myUrl = moveUrl.replace(/%5B1%5D/, bits[0]).replace(/\[2\]/, bits[1]).replace(/%5B3%5D/, newZone);

                window.open(myUrl, 'move_page');
            }
        },

        handleTreeClick: function handleTreeClick(automated, target) {
            var element = $dom.$id(this.name),
                xmlNode;
            if (element.disabled || this.busy) {
                return false;
            }

            this.busy = true;

            var clickedId = target.getAttribute('id').substr(7 + this.name.length);
            var htmlNode = $dom.$id(this.name + 'tree-list-c-' + clickedId);
            var expandBtn = $dom.$id(this.name + 'texp-c-' + clickedId);
            var expandBtnIcon = expandBtn.querySelector('.icon');

            var expanding = !$dom.isDisplayed(htmlNode);

            if (expanding) {
                xmlNode = this.getElementByIdHack(clickedId, 'c');
                xmlNode.setAttribute('expanded', 'true');
                var realClickedId = xmlNode.getAttribute('serverid');
                if (typeof realClickedId !== 'string') {
                    realClickedId = clickedId;
                }

                if ((xmlNode.getAttribute('has_children') === 'true') && !xmlNode.firstElementChild) {
                    var url = this.ajaxUrl + '&id=' + encodeURIComponent(realClickedId) + '&options=' + this.options + '&default=' + encodeURIComponent(element.value);
                    var that = this;
                    $cms.doAjaxRequest(url).then(function (xhr) {
                        $dom.empty(htmlNode);
                        that.response(xhr, clickedId);
                    });
                    $dom.html(htmlNode, '<div aria-busy="true" class="vertical-alignment"><img width="20" height="20" src="' + $util.srl('{$IMG*;,loading}') + '" alt="" /> <span>{!LOADING;^}</span></div>');
                    var container = $dom.$id('tree-list--root-' + that.name);
                    if (automated && container && (container.style.overflowY === 'auto')) {
                        setTimeout(function () {
                            container.scrollTop = $dom.findPosY(htmlNode) - 20;
                        }, 0);
                    }
                }

                $dom.fadeIn(htmlNode);

                expandBtn.title = expandBtn.title.replace('{!EXPAND;^}', '{!CONTRACT;^}');

                if (expandBtn.cmsTooltipTitle != null) {
                    expandBtn.cmsTooltipTitle = expandBtn.cmsTooltipTitle.replace('{!EXPAND;^}', '{!CONTRACT;^}');
                }

                if (expandBtnIcon) {
                    $cms.ui.setIcon(expandBtnIcon, 'tree_field/collapse', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/tree_field/collapse}');
                }
            } else {
                xmlNode = this.getElementByIdHack(clickedId, 'c');
                xmlNode.setAttribute('expanded', 'false');
                htmlNode.style.display = 'none';

                expandBtn.title = expandBtn.title.replace('{!CONTRACT;^}', '{!EXPAND;^}');

                if (expandBtn.cmsTooltipTitle != null) {
                    expandBtn.cmsTooltipTitle = expandBtn.cmsTooltipTitle.replace('{!CONTRACT;^}', '{!EXPAND;^}');
                }

                if (expandBtnIcon) {
                    $cms.ui.setIcon(expandBtnIcon, 'tree_field/expand', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/tree_field/expand}');
                }
            }

            fixupNodePositions(this.name);

            $dom.triggerResize();

            this.busy = false;
        },

        handleSelection: function handleSelection(event, assumeCtrl, target) {
            var that = this;

            assumeCtrl = Boolean(assumeCtrl);

            var element = $dom.$id(this.name);

            if (element.disabled) {
                return;
            }

            var i, selectedBefore = (element.value === '') ? [] : (this.multiSelection ? element.value.split(',') : [element.value]);

            event.preventDefault();

            if (!assumeCtrl && event.shiftKey && this.multiSelection) {
                // We're holding down shift so we need to force selection of everything bounded between our last click spot and here
                var allLabels = $dom.$id('tree-list--root-' + this.name).getElementsByTagName('label'),
                    posLast = -1,
                    posUs = -1;

                if (this.lastClicked == null) {
                    this.lastClicked = allLabels[0];
                }
                for (i = 0; i < allLabels.length; i++) {
                    if (allLabels[i] === target || allLabels[i] === target.parentNode) {
                        posUs = i;
                    }
                    if (allLabels[i] === this.lastClicked || allLabels[i] === this.lastClicked.parentNode) {
                        posLast = i;
                    }
                }
                if (posUs < posLast) {// ReOrder them
                    var temp = posUs;
                    posUs = posLast;
                    posLast = temp;
                }
                var thatSelectedId, thatXmlNode, thatType;
                for (i = 0; i < allLabels.length; i++) {
                    thatType = target.getAttribute('id').charAt(5 + this.name.length);
                    if (thatType === 'r') {
                        thatType = 'c';
                    }
                    if (thatType === 's') {
                        thatType = 'e';
                    }

                    // TODO: Does not work yet at all for server IDs and also selects all children instead of the requested range
                    if (allLabels[i].getAttribute('id').substr(5 + this.name.length, thatType.length) === thatType) {
                        thatSelectedId = allLabels[i].getAttribute('id').substr(7 + this.name.length);
                        thatXmlNode = this.getElementByIdHack(thatSelectedId, thatType);
                        if ((thatXmlNode.getAttribute('selectable') === 'true') || (this.allNodesSelectable)) {
                            if ((i >= posLast) && (i <= posUs)) {
                                if (selectedBefore.indexOf(thatSelectedId) === -1) {
                                    that.handleSelection(event, true, allLabels[i]);
                                }
                            } else {
                                if (selectedBefore.indexOf(thatSelectedId) !== -1) {
                                    that.handleSelection(event, true, allLabels[i]);
                                }
                            }
                        }
                    }
                }

                return;
            }

            var type = target.getAttribute('id').charAt(5 + this.name.length);
            if (type === 'r') {
                type = 'c';
            } else if (type === 's') {
                type = 'e';
            }
            var realSelectedId = target.getAttribute('id').substr(7 + this.name.length),
                xmlNode = this.getElementByIdHack(realSelectedId, type),
                selectedId = this.useServerId ? xmlNode.getAttribute('serverid') : realSelectedId;

            if ((xmlNode.getAttribute('selectable') === 'true') || this.allNodesSelectable) {
                var selectedAfter = selectedBefore;
                var realSelected;
                for (i = 0; i < selectedBefore.length; i++) {
                    realSelected = this.getElementByIdHack(selectedBefore[i], type, null, this.useServerId);
                    this.makeElementLookSelected($dom.$id(this.name + 'tsel_' + type + '_' + realSelected.getAttribute('id')), false);
                }
                if (!this.multiSelection || ((!event.ctrlKey && !event.metaKey && !event.altKey) && !assumeCtrl)) {
                    selectedAfter = [];
                }
                if ((selectedBefore.indexOf(selectedId) !== -1) && (((selectedBefore.length === 1) && (selectedBefore[0] !== selectedId)) || ((event.ctrlKey) || (event.metaKey) || (event.altKey)) || (assumeCtrl))) {
                    for (var key in selectedAfter) {
                        if (selectedAfter[key] === selectedId) {
                            selectedAfter.splice(key, 1);
                        }
                    }
                } else if (selectedAfter.indexOf(selectedId) === -1) {
                    selectedAfter.push(selectedId);
                    if (!this.multiSelection) { // This is a bit of a hack to make selection look nice, even though we aren't storing natural IDs of what is selected
                        var anchors = $dom.$id('tree-list--root-' + this.name).getElementsByTagName('label');
                        for (i = 0; i < anchors.length; i++) {
                            this.makeElementLookSelected(anchors[i], false);
                        }
                        console.log(this.name + 'tsel_' + type + '_' + realSelectedId);
                        this.makeElementLookSelected($dom.$id(this.name + 'tsel_' + type + '_' + realSelectedId), true);
                    }
                }

                for (i = 0; i < selectedAfter.length; i++) {
                    realSelected = this.getElementByIdHack(selectedAfter[i], type, null, this.useServerId);
                    this.makeElementLookSelected($dom.$id(this.name + 'tsel_' + type + '_' + realSelected.getAttribute('id')), true);
                }

                var newVal = selectedAfter.join(',');
                element.selectedTitle = (selectedAfter.length === 1) ? xmlNode.getAttribute('title') : newVal;
                element.selectedEditlink = xmlNode.getAttribute('edit');
                if (newVal === '') {
                    element.selectedTitle = '';
                }

                $dom.changeValue(element, newVal);
            }

            if (!assumeCtrl) {
                this.lastClicked = target;
            }
        },

        makeElementLookSelected: function makeElementLookSelected(target, selected) {
            if (!target) {
                return;
            }
            target.classList.toggle('native-ui-selected', Boolean(selected));
            target.style.cursor = 'pointer';
        }
    });


    function attributesFullFixup(xml) {
        var node, i, id = xml.getAttribute('id');

        window.attributesFull || (window.attributesFull = {});
        window.attributesFull[id] || (window.attributesFull[id] = {});

        for (i = 0; i < xml.attributes.length; i++) {
            window.attributesFull[id][xml.attributes[i].name] = xml.attributes[i].value;
        }
        for (i = 0; i < xml.children.length; i++) {
            node = xml.children[i];

            if ((node.localName === 'category') || (node.localName === 'entry')) {
                attributesFullFixup(node);
            }
        }
    }

    function fixupNodePositions(name) {
        var html = $dom.$id('tree-list--root-' + name),
            toFix = html.getElementsByTagName('div'), i;

        for (i = 0; i < toFix.length; i++) {
            if (toFix[i].style.position === 'absolute') {
                fixUpNodePosition(toFix[i]);
            }
        }
    }

    function fixUpNodePosition(nodeSelf) {
        nodeSelf.style.left = $dom.findPosX(nodeSelf.parentNode, true) + 'px';
        nodeSelf.style.top = $dom.findPosY(nodeSelf.parentNode, true) + 'px';
    }

    function findOverlappingSelectable(mouseY, element, node, name) { // Find drop targets
        var i, childNode, temp, childNodeElement, y, height;

        // Recursion
        if (node.getAttribute('expanded') !== 'false') {
            for (i = 0; i < node.children.length; i++) {
                childNode = node.children[i];
                temp = findOverlappingSelectable(mouseY, element, childNode, name);
                if (temp) {
                    return temp;
                }
            }
        }

        if (node.getAttribute('droppable') === element.cmsDraggable) {
            childNodeElement = $dom.$id(name + 'tree_list_' + ((node.localName === 'category') ? 'c' : 'e') + '_' + node.getAttribute('id'));
            y = $dom.findPosY(childNodeElement.parentNode.parentNode, true);
            height = childNodeElement.parentNode.parentNode.offsetHeight;
            if ((y < mouseY) && (y + height > mouseY)) {
                return childNodeElement;
            }
        }

        return null;
    }
}(window.$cms, window.$util, window.$dom));
