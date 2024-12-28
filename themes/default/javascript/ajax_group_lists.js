(function ($cms, $util, $dom) {
    'use strict';

    /**
     * @memberof $cms.form
     * @param target
     * @param e
     * @param searchType
     */
    $cms.form.updateAjaxGroupSearchList = function updateAjaxGroupSearchList(target, e, searchType) {
        var special = 'search';
        searchType = strVal(searchType);
        if (searchType) {
            special += '&search_type=' + encodeURIComponent(searchType);
        }
        $cms.form.updateAjaxGroupList(target, special, false, e);
    };

    var currentlyDoingListTimer = 0,
        currentListForEl = null;
    /**
     * @memberof $cms.form
     * @param target
     * @param special
     * @param delayed
     * @param event
     */
    $cms.form.updateAjaxGroupList = function updateAjaxGroupList(target, special, delayed, event) {
        if ((event && $dom.keyPressed(event, 'Enter')) || target.disabled) {
            return;
        }

        if (!$cms.browserMatches('ios') && !target.onblur) {
            target.onblur = function () {
                setTimeout(function () {
                    closeDownAjaxList();
                }, 300);
            };
        }

        if (!delayed) { // A delay, so as not to throw out too many requests
            if (currentlyDoingListTimer) {
                clearTimeout(currentlyDoingListTimer);
            }
            var eCopy = { 'keyCode': event.keyCode, 'which': event.which };

            currentlyDoingListTimer = setTimeout(function () {
                $cms.form.updateAjaxGroupList(target, special, true, eCopy);
            }, 400);
            return;
        } else {
            currentlyDoingListTimer = 0;
        }

        target.special = special;

        var v = target.value;

        currentListForEl = target;
        var script = '{$FIND_SCRIPT_NOHTTP;,grouplike}?id=' + encodeURIComponent('*' + v + '*');
        if (special) {
            script = script + '&special=' + special;
        }

        $cms.doAjaxRequest(script + $cms.keep()).then(function (xhr) {
            if (xhr.responseXML) {
                updateAjaxGroupListResponse(xhr.responseXML);
            }
        });

        function closeDownAjaxList() {
            var current = $dom.$('#ajax_list');
            if (current) {
                current.parentNode.removeChild(current);
            }
        }

        function updateAjaxGroupListResponse(responseXml) {
            var listContents = responseXml && responseXml.querySelector('result');

            if (!listContents || !currentListForEl) {
                return;
            }

            closeDownAjaxList();

            var isDataList = false;//(document.createElement('datalist').options!==undefined); Still too buggy in browsers

            //if (list_contents.childNodes.length==0) return;
            var list = document.createElement(isDataList ? 'datalist' : 'select');
            list.className = 'group-list';
            list.id = 'ajax_list';
            if (isDataList) {
                currentListForEl.setAttribute('list', 'ajax_list');
            } else {
                if (listContents.childNodes.length === 1) { // We need to make sure it is not a dropdown. Normally we'd use size (multiple isn't correct, but we'll try this for 1 as it may be more stable on some browsers with no side effects)
                    list.multiple = true;
                } else {
                    list.size = listContents.childNodes.length + 1;
                }
                list.style.position = 'absolute';
                list.style.left = ($dom.findPosX(currentListForEl)) + 'px';
                list.style.top = ($dom.findPosY(currentListForEl) + currentListForEl.offsetHeight) + 'px';
            }

            if (listContents.children.length === 0) {
                return;
            }

            var i, item, displaytext;

            for (i = 0; i < listContents.children.length; i++) {
                item = document.createElement('option');
                item.value = listContents.children[i].getAttribute('value');
                item.title = listContents.children[i].getAttribute('title');
                displaytext = item.value;
                if (listContents.children[i].getAttribute('groupname')) {
                    displaytext = listContents.children[i].getAttribute('groupname');
                }
                item.text = displaytext;
                item.textContent = displaytext;
                list.appendChild(item);
            }
            item = document.createElement('option');
            item.disabled = true;
            item.text = '{!javascript:SUGGESTIONS_ONLY;^}'.toUpperCase();
            item.textContent = '{!javascript:SUGGESTIONS_ONLY;^}'.toUpperCase();
            list.appendChild(item);
            currentListForEl.parentNode.appendChild(list);

            if (isDataList) {
                return;
            }

            $dom.fadeIn(list);

            var currentListForCopy = currentListForEl;

            if (currentListForEl.oldOnkeyup === undefined) {
                currentListForEl.oldOnkeyup = currentListForEl.onkeyup;
            }

            if (currentListForEl.oldOnchange === undefined) {
                currentListForEl.oldOnchange = currentListForEl.onchange;
            }

            currentListForEl.downOnce = false;

            currentListForEl.onkeyup = function (event) {
                var ret = handleArrowUsage(event);
                if (ret != null) {
                    return ret;
                }
                return $cms.form.updateAjaxGroupList(currentListForCopy, currentListForCopy.special, false, event);
            };
            currentListForEl.onchange = function (event) {
                currentListForCopy.onkeyup = currentListForCopy.oldOnkeyup;
                currentListForCopy.onchange = currentListForCopy.oldOnchange;
                if (currentListForCopy.onchange) {
                    currentListForCopy.onchange(event);
                }
            };
            list.onkeyup = function (event) {
                var ret = handleArrowUsage(event);
                if (ret != null) {
                    return ret;
                }

                if ($dom.keyPressed(event, 'Enter')) { // ENTER
                    makeSelection(event);
                    currentListForCopy.disabled = true;
                    setTimeout(function () {
                        currentListForCopy.disabled = false;
                    }, 200);

                    return true;
                }
                if (!event.shiftKey && $dom.keyPressed(event, ['ArrowUp', 'ArrowDown'])) {
                    if (event.cancelable) {
                        event.preventDefault();
                    }
                    return true;
                }
                return null;
            };

            currentListForEl.onkeypress = function (event) {
                if (!event.shiftKey && $dom.keyPressed(event, ['ArrowUp', 'ArrowDown'])) {
                    if (event.cancelable) {
                        event.preventDefault();
                    }
                    return true;
                }
                return null;
            };
            list.onkeypress = function (event) {
                if (!event.shiftKey && $dom.keyPressed(event, ['Enter', 'ArrowUp', 'ArrowDown'])) {
                    if (event.cancelable) {
                        event.preventDefault();
                    }
                    return true;
                }
                return null;
            };

            list.addEventListener($cms.browserMatches('ios') ? 'change' : 'click', makeSelection, false);

            currentListForEl = null;

            function handleArrowUsage(event) {
                if (event.shiftKey) {
                    return;
                }

                if (event.key === 'ArrowDown') { // DOWN
                    _handleArrowUsage('down');
                    return true;
                }

                if (event.key === 'ArrowUp') { // UP
                    _handleArrowUsage('up');
                    return true;
                }
            }

            function _handleArrowUsage(direction) {
                currentListForCopy.disabled = true;
                setTimeout(function () {
                    currentListForCopy.disabled = false;
                }, 1000);

                var temp = currentListForCopy.onblur;
                currentListForCopy.onblur = function () {};
                list.focus();
                currentListForCopy.onblur = temp;

                if (!currentListForCopy.downOnce) {
                    currentListForCopy.downOnce = true;
                    list.selectedIndex = 0;
                } else {
                    if (direction === 'down') {
                        if (list.selectedIndex < (list.options.length - 1)) {
                            list.selectedIndex++;
                        }
                    } else if (direction === 'up') {
                        if (list.selectedIndex > 0) {
                            list.selectedIndex--;
                        }
                    }
                }

                list.options[list.selectedIndex].selected = true;
            }

            function makeSelection(e) {
                var el = e.target;

                currentListForCopy.value = el.value;
                currentListForCopy.onkeyup = currentListForCopy.oldOnkeyup;
                currentListForCopy.onchange = currentListForCopy.oldOnchange;
                currentListForCopy.onkeypress = function () {};
                if (currentListForCopy.onrealchange) {
                    currentListForCopy.onrealchange(e);
                }
                if (currentListForCopy.onchange) {
                    currentListForCopy.onchange(e);
                }
                var al = $dom.$id('ajax_list');
                al.parentNode.removeChild(al);
                setTimeout(function () {
                    currentListForCopy.focus();
                }, 300);
            }
        }
    };
}(window.$cms, window.$util, window.$dom));
