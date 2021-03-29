// NB: This is based on andrewsnowden's Sew fork, plus other people's fixes, our own fixes, and changes brought in direct from Bevis Zhao

/**
 * jQuery plugin for getting position of cursor in textarea

 * @license under Apache license
 * @author Bevis Zhao (i@bevis.me, http://bevis.me)
 */

jQuery(function ($) {
    var calculator = {
        // key styles
        primaryStyles: ['fontFamily', 'fontSize', 'fontWeight', 'fontVariant', 'fontStyle',
            'paddingLeft', 'paddingTop', 'paddingBottom', 'paddingRight',
            'marginLeft', 'marginTop', 'marginBottom', 'marginRight',
            'borderLeftColor', 'borderTopColor', 'borderBottomColor', 'borderRightColor',
            'borderLeftStyle', 'borderTopStyle', 'borderBottomStyle', 'borderRightStyle',
            'borderLeftWidth', 'borderTopWidth', 'borderBottomWidth', 'borderRightWidth',
            'line-height', 'outline', 'text-align'],

        specificStyle: {
            'word-wrap': 'break-word',
            'overflow-x': 'hidden',
            'overflow-y': 'auto'
        },

        simulator: $('<div id="textarea_simulator"/>').css({
            position: 'absolute',
            top: 0,
            left: 0,
            visibility: 'hidden'
        }).appendTo(document.body),

        toHtml: function (text) {
            return text.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>')
                .split(' ').join('<span style="white-space: pre-wrap">&nbsp;</span>');
        },
        // calculate position
        getCaretPosition: function () {
            var cal = calculator, self = this, element = self[0], elementOffset = self.offset();

            // IE has easy way to get caret offset position
            if (document.selection) {
                try {
                    // must get focus first
                    element.focus();
                }
                catch (ex) {
                }
                var range = document.selection.createRange();
                if (typeof range.boundingLeft != 'undefined') { // Only IE has this
                    $(element).val(element.scrollTop);
                    return {
                        left: range.boundingLeft - elementOffset.left,
                        top: parseInt(range.boundingTop) - elementOffset.top + element.scrollTop + document.documentElement.scrollTop
                    };
                }
            }

            cal.simulator.empty();
            // clone primary styles to imitate textarea
            $.each(cal.primaryStyles, function (index, styleName) {
                self.cloneStyle(cal.simulator, styleName);
            });

            // calculate width and height
            cal.simulator.css($.extend({
                'width': self.width(),
                'height': self.height()
            }, cal.specificStyle));

            var value = self.val(), cursorPosition = self.getCursorPosition();
            var beforeText = value.substring(0, cursorPosition),
                afterText = value.substring(cursorPosition);

            var before = $('<span class="before"/>').html(cal.toHtml(beforeText)),
                focus = $('<span class="focus"/>'),
                after = $('<span class="after"/>').html(cal.toHtml(afterText));

            cal.simulator.append(before).append(focus).append(after);
            var focusOffset = focus.offset(), simulatorOffset = cal.simulator.offset();
            // alert(focusOffset.left  + ',' +  simulatorOffset.left + ',' + element.scrollLeft);
            return {
                top: focusOffset.top - simulatorOffset.top - element.scrollTop + parseInt(self.getComputedStyle('fontSize')),
                left: focus[0].offsetLeft - cal.simulator[0].offsetLeft - element.scrollLeft
            };
        }
    };

    $.fn.extend({
        setCursorPosition: function (position) {
            if (this.length == 0) return this;
            return $(this).setSelection(position, position);
        },
        setSelection: function (selectionStart, selectionEnd) {
            if (this.length == 0) return this;
            input = this[0];

            if (input.createTextRange) {
                var range = input.createTextRange();
                range.collapse(true);
                range.moveEnd('character', selectionEnd);
                range.moveStart('character', selectionStart);
                range.select();
            } else if (input.setSelectionRange) {
                try {
                    input.focus();
                }
                catch (ex) {
                }
                input.setSelectionRange(selectionStart, selectionEnd);
            } else {
                var el = this.get(0);

                var range = document.createRange();
                range.collapse(true);
                range.setStart(el.childNodes[0], selectionStart);
                range.setEnd(el.childNodes[0], selectionEnd);

                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }

            return this;
        },
        getComputedStyle: function (styleName) {
            if (this.length == 0) return;
            var thiz = this[0];
            var result = this.css(styleName);
            result = result || document.defaultView.getComputedStyle(thiz, null)[styleName];
            return result;
        },
        // easy clone method
        cloneStyle: function (target, styleName) {
            var styleVal = this.getComputedStyle(styleName);
            if (!!styleVal) {
                $(target).css(styleName, styleVal);
            }
        },
        cloneAllStyle: function (target, style) {
            var thiz = this[0];
            for (var styleName in thiz.style) {
                var val = thiz.style[styleName];
                typeof val == 'string' || typeof val == 'number'
                    ? this.cloneStyle(target, styleName)
                    : NaN;
            }
        },
        getCursorPosition: function () {
            var thiz = this[0], result = 0;
            if ('selectionStart' in thiz) {
                result = thiz.selectionStart;
            } else if ('selection' in document) {
                var range = document.selection.createRange();
                try {
                    thiz.focus();
                }
                catch (ex) {
                }
                var length = document.selection.createRange().text.length;
                range.moveStart('character', -thiz.value.length);
                result = range.text.length - length;
            }
            return result;
        },
        getCaretPosition: calculator.getCaretPosition
    });
});

/**
 * jQuery plugin for autocompleting within a textarea
 * @license under dfyw
 * @author leChantaux (@leChantaux)
 */

(function ($, window, undefined) {
    // Create the defaults once
    var elementFactory = function (element, value, token) {
        element.text(value.val);
    };

    var pluginName = 'sew',
        defaults = {
            token: '@',
            elementFactory: elementFactory,
            values: [],
            repeat: true,
            onFilterChanged: undefined,
            preload: false
        };

    function Plugin(element, options) {
        this.element = element;
        this.$element = $cms.form.isWysiwygField(element) ? null : $(element);
        this.$itemList = $(Plugin.MENU_TEMPLATE);
        this.currentToken = undefined;
        this.startPos = null;

        this.options = $.extend({}, defaults, options);
        if (!$.isArray(this.options.token)) {
            this.options.token = [this.options.token];
        }
        this.reset();

        this._defaults = defaults;
        this._name = pluginName;

        var tokens = this.options.token.join('');
        this.expression = new RegExp('(^|\\s)([' + tokens + '])([\\w.]*)$');
        this.cleanupHandle = null;

        this.init();
    }

    Plugin.MENU_TEMPLATE = '<div class="-sew-list-container" style="display: none; position: absolute;"><ul class="-sew-list"></ul></div>';

    Plugin.ITEM_TEMPLATE = '<li class="-sew-list-item"></li>';

    Plugin.KEYS = [40, 38, 13, 27, 9];

    Plugin.prototype.init = function () {
        if (this.options.values.length < 1 && !this.options.onFilterChanged) {
            return;
        }

        if (this.options.preload && this.options.onFilterChanged) {
            this.options.onFilterChanged(this);
        }

        if (window.CKEDITOR && CKEDITOR.instances[this.element.id]) {
            var _this = this;
            var editor = CKEDITOR.instances[this.element.id];
            if (editor.document) {
                editor.document.on('keyup', function (e) {
                    _this.onKeyUp.call(_this, e);
                });
                editor.document.on('keydown', function (e) {
                    _this.onKeyDown.call(_this, e);
                });
                editor.document.on('focus', function (e) {
                    _this.renderElements.call(_this, _this.options.values);
                });
                editor.document.on('blur', function (e) {
                    _this.remove.call(_this);
                });
                editor.document.on('click', function (e) {
                    _this.remove.call(_this);
                });
            }
        }
        else if (this.$element) {
            this.$element
                .bind('keyup', $.proxy(this.onKeyUp, this))
                .bind('keydown', $.proxy(this.onKeyDown, this))
                .bind('focus', $.proxy(this.renderElements, this, this.options.values))
                .bind('blur', $.proxy(this.remove, this))
                .bind('click', $.proxy(this.remove, this))
            ;
        }
    };

    Plugin.prototype.reset = function () {
        this.index = 0;
        this.matched = false;
        this.dontFilter = false;
        this.lastFilter = undefined;
        this.filtered = this.options.values.slice(0);
    };

    Plugin.prototype.setValues = function (values) {
        this.options.values = values;

        var listVisible = this.$itemList.is(':visible');
        this.reset();

        if (values.length > 0) {
            if (!listVisible) {
                this.displayList();
            }

            var filter = this.lastFilter;
            if (!filter) {
                filter = '';
            }
            this.lastFilter = '\n';
            this.filterList(filter);
        }
        else {
            this.hideList();
        }
    };

    Plugin.prototype.next = function () {
        this.index = (this.index + 1) % this.filtered.length;
        this.highlightItem();
    };

    Plugin.prototype.prev = function () {
        this.index = (this.index + this.filtered.length - 1) % this.filtered.length;
        this.highlightItem();
    };

    Plugin.prototype.select = function () {
        this.replace(this.filtered[this.index].val);
        if (this.$element)
            this.$element.trigger('mention-selected', this.filtered[this.index]);
        this.hideList();
    };

    Plugin.prototype.remove = function () {
        this.$itemList.fadeOut('slow');

        this.cleanupHandle = window.setTimeout($.proxy(function () {
            this.$itemList.remove();
        }, this), 1000);
    };

    Plugin.prototype.replace = function (replacement) {
        if (this.$element) {
            var startPos = this.$element.getCursorPosition();
        } else {
            var startPos = this.startPos; // Has to use this.startPos because focus may have moved away, breaking CKEditor selection
        }

        var fullStuff = this.getText();
        var val = fullStuff.substring(0, startPos);
        val = val.replace(this.expression, '$1' + '$2' + replacement);

        var posfix = fullStuff.substring(startPos, fullStuff.length);
        var separator = posfix.match(/^\s/) ? '' : (this.$element ? ' ' : '&nbsp;');

        var finalFight = val + separator + posfix;
        this.setText(finalFight);
        if (this.$element) {
            this.$element.setCursorPosition(val.length + 1);
        } else {
            // Complex code to move CKEditor caret to end

            CKEDITOR.instances[this.element.id].focus();

            var s = CKEDITOR.instances[this.element.id].getSelection(); // getting selection
            var selected_ranges = s.getRanges(); // getting ranges
            if (selected_ranges[0] !== undefined) {
                var node = selected_ranges[0].startContainer; // selecting the starting node
                var parents = node.getParents(true);

                node = parents[parents.length - 2].getFirst();

                while (true) {
                    var x = node.getNext();
                    if (x == null) {
                        break;
                    }
                    node = x;
                }

                s.selectElement(node);
                selected_ranges = s.getRanges();
                selected_ranges[0].collapse(false);  //  false collapses the range to the end of the selected node, true before the node.
                s.selectRanges(selected_ranges);  // putting the current selection there
            }
        }
    };

    Plugin.prototype.highlightItem = function () {
        if (this.filtered.length === 0) {
            return;
        }

        this.$itemList.find('.-sew-list-item').removeClass('selected');

        var container = this.$itemList.find('.-sew-list-item').parent();
        var element = this.filtered[this.index].element.addClass('selected');

        var scrollPosition = element.position().top;
        container.scrollTop(container.scrollTop() + scrollPosition);
    };

    Plugin.prototype.renderElements = function (values) {
        window.sew = this;

        $('body').append(this.$itemList);

        var container = this.$itemList.find('ul').empty();
        for (var i = 0; i < values.length; i++) {
            var e = values[i];

            var $item = $(Plugin.ITEM_TEMPLATE);

            this.options.elementFactory($item, e, this.currentToken);

            e.element = $item.appendTo(container).bind('click', $.proxy(this.onItemClick, this, e)).bind('mouseover', $.proxy(this.onItemHover, this, i));
        }

        this.index = 0;
        this.highlightItem();
    };

    Plugin.prototype.displayList = function () {
        if (!this.filtered.length) return;

        this.$itemList.show();

        if (this.$element) {
            var element = this.$element;
            var offset = this.$element.offset();
            var pos = element.getCaretPosition();

            this.$itemList.css({
                left: offset.left + pos.left,
                top: offset.top + pos.top
            });
        } else {
            // Complex hack to find cursor position in CKEditor

            var dummyElement = CKEDITOR.instances[this.element.id].document.createElement('img',
                {
                    attributes: {
                        src: $util.srl('{$IMG;,blank}'),
                        width: 0,
                        height: 0
                    }
                });

            CKEDITOR.instances[this.element.id].insertElement(dummyElement);

            var _this = this;
            window.setTimeout(function () {
                var cke = CKEDITOR.instances[_this.element.id];
                var iframe = cke.container.$.getElementsByTagName('iframe')[0];

                var sel = cke.getSelection(); // text selection
                var obj = sel.getStartElement().$; // the element the selected text resides in

                var x = $dom.findPosX(obj, true) - window.pageXOffset + cke.window.$.pageXOffset + $dom.findPosX(iframe, true);
                var y = $dom.findPosY(obj, true) - window.pageYOffset + cke.window.$.pageYOffset + $dom.findPosY(iframe, true) + 20;
                // NB: The get_window_scroll_x/get_window_scroll_y is because calculation happened on wrong window object

                var text = _this.getText().substring(0, this.startPos);
                console.log(text);
                var lines = (text.match(/<br( \/)?>/g) || []).length;
                window.top.console.log(lines);
                y += 17 * lines;

                dummyElement.remove();

                _this.$itemList.css({
                    left: x,
                    top: y
                });
            }, 0);
        }
    };

    Plugin.prototype.hideList = function () {
        this.$itemList.hide();
        this.reset();
    };

    Plugin.prototype.filterList = function (val) {
        if (val == this.lastFilter) return;

        this.lastFilter = val;
        this.$itemList.find('.-sew-list-item').remove();
        var values = this.options.values;

        var vals = this.filtered = values.filter($.proxy(function (e) {
            var exp = new RegExp('\\W*' + this.options.token + e.val + '(\\W|$)');
            if (!this.options.repeat && this.getText().match(exp)) {
                return false;
            }

            return val === '' ||
                e.val.toLowerCase().indexOf(val.toLowerCase()) >= 0 ||
                (e.meta || '').toLowerCase().indexOf(val.toLowerCase()) >= 0;
        }, this));

        if (vals.length) {
            this.renderElements(vals);
            this.$itemList.show();
        } else {
            this.hideList();
        }
    };

    Plugin.prototype.getText = function () {
        if (!this.$element) {
            return CKEDITOR.instances[this.element.id].getData();
        }

        return (this.$element.val() || this.$element.text());
    };

    Plugin.prototype.setText = function (text) {
        if (!this.$element) {
            //CKEDITOR.instances[this.element.id].setData(text);    Wipes events out
            CKEDITOR.instances[this.element.id].document.getBody().setHtml(text);
            return;
        }

        if (this.$element.is('input,textarea')) {
            this.$element.val(text);
        } else {
            this.$element.html(text);
        }
    };

    Plugin.prototype.onKeyUp = function (e) {
        if (this.$element) {
            var startPos = this.$element.getCursorPosition();
            var val = this.getText().substring(0, startPos);
        } else {
            var range = CKEDITOR.instances[this.element.id].getSelection().getRanges()[0];
            if (range === undefined) return; // Out of focus :S
            var allText = this.getText();
            allText = allText.replace(/\u200B/, '');
            var textNode = range.startContainer.$;
            var selectedText = textNode.nodeValue ? textNode.nodeValue : textNode.textContent;
            selectedText = selectedText.replace(/\u200B/, '');
            var startPos = allText.lastIndexOf(selectedText);
            if (startPos == -1) return; // Could not correlate, maybe some weird HTML encoding problem
            startPos += range.startOffset; // A but of a fudge. We assume the last occurrence of the element we're on, in the overall HTML, is the one we're working in ; no API to get true cursor position
            var val = allText.substring(0, startPos);
        }
        var matches = val.match(this.expression);

        if (!matches && this.matched) {
            this.matched = false;
            this.dontFilter = false;
            this.hideList();
            return;
        }

        if (matches) {
            this.startPos = startPos;

            this.currentToken = matches[2];

            if (this.currentToken != matches[2] && this.currentToken) {
                this.currentToken = matches[2];

                if (this.options.onFilterChanged) {
                    this.options.values = [];
                    this.reset();
                }
            }

            if (this.options.onFilterChanged) {
                if (this.options.onFilterChanged) {
                    this.options.onFilterChanged(this, matches[3], matches[2]);
                }
            }

            if (!this.matched) {
                this.displayList();
                this.lastFilter = '\n';
                this.matched = true;
            }

            if (!this.dontFilter) {
                this.filterList(matches[3]);
            }
        }
    };

    Plugin.prototype.onKeyDown = function (e) {
        if (this.$element) {
            var keyCode = e.keyCode;
        } else {
            var keyCode = e.data.getKey();
        }

        var listVisible = this.$itemList.is(':visible');
        if (!listVisible || (Plugin.KEYS.indexOf(keyCode) < 0)) return;

        switch (keyCode) {
            case 9:
            case 13:
                this.select();
                break;
            case 40:
                this.next();
                break;
            case 38:
                this.prev();
                break;
            case 27:
                this.$itemList.hide();
                this.dontFilter = true;
                break;
        }

        if (this.$element) {
            e.preventDefault();
        }
    };

    Plugin.prototype.onItemClick = function (element, e) {
        if (this.cleanupHandle) window.clearTimeout(this.cleanupHandle);

        try {
            if (this.$element) {
                this.$element.focus();
            } else {
                CKEDITOR.instances[this.element.id].focus();
            }
        }
        catch (ex) {
        }
        this.replace(element.val);
        if (this.$element)
            this.$element.trigger('mention-selected', this.filtered[this.index]);
        this.hideList();
    };

    Plugin.prototype.onItemHover = function (index, e) {
        this.index = index;
        this.highlightItem();
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName) || this.element && $cms.form.isWysiwygField(this.element)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });
    };
}(jQuery, window));

(function ($cms, $util, $dom) {
    'use strict';

    var $jqueryAutocomplete = window.$jqueryAutocomplete = {};

    /**
     * @param {jQuery} element the target element (LI)
     * @param {*} e object containing the val and meta properties (from the input list)
     */
    function autoCompleteElementFactory(element,e) {
        var customItemTemplate = '<div><span />&nbsp;<small /></div>';
        var template = window.jQuery(customItemTemplate).find('span')
            .text('@' + e.val).end()
            .find('small')
            .text((e.meta == '') ? '' : '(' + e.meta + ')').end();
        element.append(template);
    }

    /* Composr binder code */
    /**
     * @param name
     * @param wysiwyg
     */
    $jqueryAutocomplete.setUpComcodeAutocomplete = function setUpComcodeAutocomplete(name, wysiwyg) {
        if (wysiwyg && window.$editing && window.$editing.wysiwygOn() && ((window.CKEDITOR == null) ||  (!window.CKEDITOR.instances[name]))) {
            return;
        }

        if (!window.jQuery || !window.jQuery.fn.sew) {
            $util.fatal('$jqueryAutocomplete.setUpComcodeAutocomplete(): jQuery.fn.sew plugin is not loaded');
            return;
        }

        $(document).ready(function() {
            window.jQuery('#' + name).sew({
                values: [],
                token: '@',
                elementFactory: autoCompleteElementFactory,
                onFilterChanged: function (sew, token, expression) {
                    $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,namelike}?id=' + encodeURIComponent(token) + $cms.keep()).then(function (response) {
                        var responseXML = response.responseXML;
                        var listContents = responseXML && responseXML.querySelector('result');
                        if (!listContents) {
                            return;
                        }

                        var newValues = [];
                        for (var i = 0; i < listContents.childNodes.length; i++) {
                            newValues.push({
                                val: listContents.childNodes[i].getAttribute('value'),
                                meta: listContents.childNodes[i].getAttribute('displayname')
                            });
                        }
                        sew.setValues(newValues);
                    });
                }
            });
        });
    };
}(window.$cms, window.$util, window.$dom));
