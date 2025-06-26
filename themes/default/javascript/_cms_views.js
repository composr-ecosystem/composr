/* This file contains CMS-wide Views */

(function ($cms, $util, $dom) {
    'use strict';

    /**
     * Addons will add $cms.View subclasses under this namespace
     * @namespace $cms.views
     */
    $cms.views = {};

    // List of view options that can be set as properties.
    var viewOptionsList = { el: 1, id: 1, attributes: 1, className: 1, tagName: 1, events: 1 };

    $cms.View = View;
    /**
     * @memberof $cms
     * @class $cms.View
     */
    function View(/*params, viewOptions*/) {
        /** @member {number}*/
        this.uid = $util.uid(this);
        /** @member {string} */
        this.tagName = 'div';
        /** @member { HTMLElement } */
        this.el = null;

        this.initialize.apply(this, arguments);
    }

    // Cached regex to split keys for `delegate`.
    var rgxDelegateEventSplitter = /^(\S+)\s*(.*)$/;
    $util.properties(View.prototype, /**@lends $cms.View#*/{
        /**
         * @method
         */
        initialize: function (params, viewOptions) {
            this.params = objVal(params);

            if ($util.isObj(viewOptions)) {
                for (var key in viewOptionsList) {
                    if (key in viewOptions) {
                        this[key] = viewOptions[key];
                    }
                }
            }

            this._ensureElement();
        },
        /**
         * @method
         */
        $: function (selector) {
            return $dom.$(this.el, selector);
        },
        /**
         * @method
         */
        $$: function (selector) {
            return $dom.$$(this.el, selector);
        },
        /**
         * @method
         */
        $$$: function (selector) {
            return $dom.$$$(this.el, selector);
        },
        /**
         * @method
         */
        $closest: function (el, selector) {
            return $dom.closest(el, selector, this.el);
        },

        /**
         * Remove this view by taking the element out of the DOM.
         * @method
         */
        remove: function () {
            this._removeElement();
            return this;
        },

        /**
         * Remove this view's element from the document and all event listeners
         * attached to it. Exposed for subclasses using an alternative DOM
         * manipulation API.
         * @method
         */
        _removeElement: function () {
            if (this.el && this.el.parentNode) {
                this.el.parentNode.removeChild(this.el);
            }
        },

        /**
         * Change the view's element (`this.el` property) and re-delegate the
         * view's events on the new element.
         * @method
         */
        setElement: function (element) {
            this.undelegateEvents();
            this._setElement(element);
            this.delegateEvents();
            return this;
        },


        /**
         * Creates the `this.el` reference for this view using the
         * given `el`. `el` can be a CSS selector or an HTML element.
         * Subclasses can override this to utilize an
         * alternative DOM manipulation API and are only required to set the `this.el` property.
         * @method
         */
        _setElement: function (el) {
            this.el = (typeof el === 'string') ? $dom.$(el) : el;
        },

        /**
         * @method
         */
        events: function () {
            return {};
        },

        /**
         * Set callbacks, where `this.events` is a hash of
         * *{"event selector": "callback"}*
         * pairs. Callbacks will be bound to the view, with `this` set properly.
         * Uses event delegation for efficiency.
         * Omitting the selector binds the event to `this.el`.
         * @method
         */
        delegateEvents: function (events) {
            var key, method, match;

            if (typeof events === 'function') {
                events = events.call(this);
            } else if ((events == null) && (typeof this.events === 'function')) {
                events = this.events();
            }

            if (typeof events !== 'object') {
                return this;
            }

            this.undelegateEvents();
            for (key in events) {
                method = events[key];
                if (typeof method !== 'function') {
                    method = this[method];
                }
                if (!method) {
                    continue;
                }
                match = key.match(rgxDelegateEventSplitter);
                this.delegate(match[1], match[2], method.bind(this));
            }
            return this;
        },

        /**
         * Add a single event listener to the view's element (or a child element using `selector`).
         * @method
         */
        delegate: function (eventName, selector, listener) {
            //$util.inform('$cms.View#delegate(): delegating event "' + eventName + '" for selector "' + selector + '" with listener', listener, 'and view', this);

            if ((eventName === 'clickout') && !selector) {
                var self = this;
                $dom.on(document.documentElement, ('click.delegateEvents' + $util.uid(this)), function (e) {
                    if (self.el && !self.el.contains(e.target)) {
                        listener(e);
                    }
                });

                return this;
            }

            $dom.on(this.el, (eventName + '.delegateEvents' + $util.uid(this)), selector, listener);
            return this;
        },

        /**
         * Clears all callbacks previously bound to the view by `delegateEvents`.
         * You usually don't need to use this, but may wish to if you have multiple
         * views attached to the same DOM element.
         * @method
         */
        undelegateEvents: function () {
            if (this.el) {
                $dom.off(this.el, '.delegateEvents' + $util.uid(this));
            }
            $dom.off(document.documentElement, 'click.delegateEvents' + $util.uid(this)); // For 'clickout' event handlers if any
            return this;
        },

        /**
         * A finer-grained `undelegateEvents` for removing a single delegated event. `selector` and `listener` are both optional.
         * @method
         */
        undelegate: function (eventName, selector, listener) {
            if ((eventName === 'clickout') && !selector) {
                $dom.off(document.documentElement, ('click.delegateEvents' + $util.uid(this)));
                return this;
            }

            $dom.off(this.el, (eventName + '.delegateEvents' + $util.uid(this)), selector, listener);
            return this;
        },

        /**
         * @method
         */
        _ensureElement: function () {
            var attrs;
            if (!this.el) {
                attrs = Object.assign({}, $util.result(this, 'attributes'));
                if (this.id) {
                    attrs.id = $util.result(this, 'id');
                }
                if (this.className) {
                    attrs.className = $util.result(this, 'className');
                }
                this.setElement($dom.create($util.result(this, 'tagName') || 'div', attrs));
            } else {
                this.setElement($util.result(this, 'el'));
            }
        }
    });

    $cms.views.ToggleableTray = ToggleableTray;
    /**
     * @memberof $cms.views
     * @class $cms.views.ToggleableTray
     * @extends $cms.View
     */
    function ToggleableTray(params) {
        ToggleableTray.base(this, 'constructor', arguments);

        var id;
        this.cookie = null;
        if (params.save) {
            id = $dom.id(this.el, 'tray-');
            this.cookie = id.startsWith('tray') ? id : 'tray-' + id;
        }

        this.contentEl = this.$('.js-tray-content');

        if (this.cookie) {
            this.handleTrayCookie();
        }
    }

    $util.inherits(ToggleableTray, $cms.View, /**@lends $cms.views.ToggleableTray#*/{
        /**@method*/
        events: function () {
            return {
                'click .js-tray-onclick-toggle-tray': 'toggleTray',
                'click .js-tray-onclick-toggle-accordion': 'handleToggleAccordion'
            };
        },

        /**@method*/
        toggleTray: function () {
            var expanded = $cms.ui.toggleableTray(this.contentEl);

            this.el.classList.toggle('is-expanded', expanded);
            this.el.classList.toggle('is-collapsed', !expanded);

            if (this.cookie) {
                $cms.setCookie(this.cookie, expanded ? 'open' : 'closed');
            }
        },

        /**
         * @param toggledAccordionItem - Accordion item to be made active
         */
        toggleAccordion: function (toggledAccordionItem) {
            var accordionItems = this.$$('.js-tray-accordion-item');

            accordionItems.forEach(function (accordionItem) {
                var body = accordionItem.querySelector('.js-tray-accordion-item-body'),
                    expanded;

                if ((accordionItem === toggledAccordionItem) || $dom.isDisplayed(body)) {
                    expanded = $cms.ui.toggleableTray(body);
                    accordionItem.classList.toggle('accordion-trayitem-active', expanded);
                    accordionItem.classList.toggle('is-expanded', expanded);
                    accordionItem.classList.toggle('is-collapsed', !expanded);
                }
            });

        },
        /**@method*/
        handleToggleAccordion: function (e, btn) {
            var accordionItem = $dom.closest(btn, '.js-tray-accordion-item'); // Accordion item to be made active
            this.toggleAccordion(accordionItem);
        },

        /**@method*/
        handleTrayCookie: function () {
            var cookieValue = $cms.readCookie(this.cookie), expanded;

            if ((!$dom.isDisplayed(this.contentEl) && (cookieValue === 'open')) || ($dom.isDisplayed(this.contentEl) && (cookieValue === 'closed'))) {
                expanded = $cms.ui.toggleableTray(this.contentEl, false);

                this.el.classList.toggle('is-expanded', expanded);
                this.el.classList.toggle('is-collapsed', !expanded);
            }
        }
    });

    /*
     Originally...

     Script: modalwindow.js
     ModalWindow - Simple javascript pop-up overlay to replace builtin alert, prompt and confirm, and more.

     License:
     PHP-style license.

     Copyright:
     Copyright (c) 2009 [Kieron Wilson](http://kd3sign.co.uk/).

     Code & Documentation:
     http://kd3sign.co.uk/examples/modalwindow/

     HEAVILY Modified by Chris Graham for composr.

     */

    $cms.views.ModalWindow = ModalWindow;
    /**
     * @memberof $cms.views
     * @class $cms.views.ModalWindow
     * @extends $cms.View
     */
    function ModalWindow(params) {
        // Constants
        this.WINDOW_SIDE_GAP = $cms.isMobile() ? 5 : 25;
        this.WINDOW_TOP_GAP = 25; // Will also be used for bottom gap for percentage heights
        this.BOX_EAST_PERIPHERARY = 4;
        this.BOX_WEST_PERIPHERARY = 4;
        this.BOX_NORTH_PERIPHERARY = 4;
        this.BOX_SOUTH_PERIPHERARY = 4;
        this.BOX_PADDING = 20;
        this.VCENTRE_FRACTION_SHIFT = 0.5; // Fraction of remaining top gap also removed (as overlays look better slightly higher than vertical centre)
        this.LOADING_SCREEN_HEIGHT = 100;

        // Properties
        /** @type { Element }*/
        this.el = null;
        /** @type { Element }*/
        this.overlayEl = null;
        /** @type { Element }*/
        this.containerEl = null;
        /** @type { Element }*/
        this.buttonContainerEl = null;
        this.returnValue = null;
        this.topWindow = null;
        this.iframeRestyleTimer = null;

        // Set params
        params = $util.defaults({ // apply defaults
            type: 'alert',
            opacity: '0.5',
            width: 'auto',
            height: 'auto',
            title: '',
            text: '',
            yesButton: '{!YES;^}',
            noButton: '{!NO;^}',
            cancelButton: '{!INPUTSYSTEM_CANCEL;^}',
            yes: null,
            no: null,
            finished: null,
            cancel: null,
            href: null,
            scrollbars: null,
            defaultValue: null,
            target: '_self',
            inputType: 'text'
        }, params || {});

        for (var key in params) {
            this[key] = params[key];
        }

        this.topWindow = window.top;
        this.opened = true;

        ModalWindow.base(this, 'constructor', arguments);
    }

    $util.inherits(ModalWindow, $cms.View, /**@lends $cms.views.ModalWindow#*/ {
        events: function events() {
            return {
                'click .js-onclick-do-option-yes': 'doOptionYes',
                'click .js-onclick-do-option-no': 'doOptionNo',
                'click .js-onclick-do-option-cancel': 'doOptionCancel',
                'click .js-onclick-do-option-finished': 'doOptionFinished',
                'click .js-onclick-do-option-left': 'doOptionLeft',
                'click .js-onclick-do-option-right': 'doOptionRight',
            };
        },

        doOptionYes: function doOptionYes() {
            this.option('yes');
        },

        doOptionNo: function doOptionNo() {
            this.option('no');
        },

        doOptionCancel: function doOptionCancel() {
            this.option('cancel');
        },

        doOptionFinished: function doOptionFinished() {
            this.option('finished');
        },

        doOptionLeft: function doOptionLeft() {
            this.option('left');
        },

        doOptionRight: function doOptionRight() {
            this.option('right');
        },

        // class="cms-modal-button-container cms-modal-overlay cms-modal-type-alert cms-modal-type-confirm"
        // ^ Above comment serves to mark the classes as _used_ for the 'css_file' unit test

        _setElement: function _setElement() {
            var button;

            this.topWindow.overlayZIndex || (this.topWindow.overlayZIndex = 999999); // Has to be higher than plupload, which is 99999

            this.el = $dom.create('div', { // Black out the background
                'className': 'cms-modal cms-modal-type-' + this.type,
                'tabIndex': '-1', // So that we can enforce focus, see the 'focusin.modalWindow' event listener attached in this._setElement()
                'css': {
                    'zIndex': this.topWindow.overlayZIndex++
                }
            });

            this.topWindow.document.body.appendChild(this.el);

            this.overlayEl = this.el.appendChild($dom.create('div', { // The main overlay
                'className': 'box overlay cms-modal-overlay'
            }, {
                'role': ['lightbox', 'iframe'].includes(this.type) ? 'dialog' : 'alertdialog',
                'aria-modal': 'true'
            }));

            this.containerEl = this.overlayEl.appendChild($dom.create('div', {
                'className': 'box-inner cms-modal-container',
            }));

            var overlayHeader = null;
            if (this.title !== '' || this.type === 'iframe') {
                overlayHeader = $dom.create('h3', {
                    'html': this.title,
                    'css': {
                        'display': (this.title === '') ? 'none' : ''
                    }
                });
                this.containerEl.appendChild(overlayHeader);
            }

            if (this.text !== '') {
                if (this.type === 'prompt') {
                    var div = $dom.create('p');
                    div.appendChild($dom.create('label', {
                        'htmlFor': 'overlay_prompt',
                        'html': this.text
                    }));
                    this.containerEl.appendChild(div);
                } else {
                    this.containerEl.appendChild($dom.create('div', {
                        'html': this.text
                    }));
                }
            }

            this.buttonContainerEl = $dom.create('p', {
                'className': 'proceed-button cms-modal-button-container'
            });

            var bi = document.getElementById('main-website-inner');
            if (bi) {
                bi.classList.add('faded');
            }

            var self = this;

            $dom.on(this.overlayEl, 'click', function () {
                if ($cms.isMobile() && (self.type === 'lightbox')) { // IDEA: Swipe detect would be better, but JS does not have this natively yet
                    self.option('right');
                }
            });

            /*{+START,SET,icon_proceed}{+START,INCLUDE,ICON}NAME=buttons/proceed{+END}{+END}*/
            /*{+START,SET,icon_yes}{+START,INCLUDE,ICON}NAME=buttons/yes{+END}{+END}*/
            /*{+START,SET,icon_no}{+START,INCLUDE,ICON}NAME=buttons/no{+END}{+END}*/

            switch (this.type) {
                case 'iframe':
                    var iframeWidth = (this.width.match(/^[\d.]+$/) !== null) ? ((this.width - 14) + 'px') : this.width,
                        iframeHeight = (this.height.match(/^[\d.]+$/) !== null) ? (this.height + 'px') : ((this.height === 'auto') ? (this.LOADING_SCREEN_HEIGHT + 'px') : this.height);

                    var iframe = $dom.create('iframe', {
                        'frameBorder': '0',
                        'scrolling': this.windowScrollingBlocked() ? 'yes' : 'no',
                        'title': '',
                        'name': 'overlay-iframe',
                        'id': 'overlay-iframe',
                        'className': 'cms-modal-overlay-iframe',
                        'allowTransparency': 'true',
                        //'seamless': 'seamless',// Not supported, and therefore testable yet. Would be great for mobile browsing.
                        'css': {
                            'width': iframeWidth,
                            'height': iframeHeight,
                            'background': 'transparent'
                        }
                    });

                    this.containerEl.appendChild(iframe);

                    setTimeout(function () {
                        if (self.el) {
                            $dom.on(self.el, 'click', function (e) {
                                if ((self.overlayEl !== e.target) && (!self.overlayEl.contains(e.target))) {
                                    // Background overlay clicked
                                    self.option('finished');
                                }
                            });
                        }
                    }, 1000);

                    $dom.on(iframe, 'load', function () {
                        if ($dom.hasIframeAccess(iframe)) {
                            if (iframe.contentDocument.querySelector('h1')) {
                                $dom.html(overlayHeader, $dom.html(iframe.contentDocument.querySelector('h1')));
                                $dom.show(overlayHeader);
                            } else if (iframe.contentDocument.title) {
                                $dom.html(overlayHeader, $cms.filter.html(iframe.contentDocument.title));
                                $dom.show(overlayHeader);
                            }
                        }
                    });

                    // Fiddle it, to behave like a pop-up would
                    setTimeout(function () {
                        $dom.illustrateFrameLoad('overlay-iframe');
                        iframe.src = self.href;
                        self.makeFrameLikePopup(iframe);

                        if (self.iframeRestyleTimer == null) { // In case internal nav changes
                            self.iframeRestyleTimer = setInterval(function () {
                                self.makeFrameLikePopup(iframe);
                            }, 300);
                        }
                    }, 0);
                    break;

                case 'lightbox':
                case 'alert':
                    if (this.yes) {
                        button = $dom.create('button', {
                            'type': 'button',
                            'html': '{$GET;^,icon_proceed} ' + this.yesButton,
                            'className': 'btn btn-primary btn-scri buttons--proceed js-onclick-do-option-yes'
                        });

                        this.buttonContainerEl.appendChild(button);
                    }
                    setTimeout(function () {
                        if (self.el) {
                            $dom.on(self.el, 'click', function (e) {
                                if (!self.containerEl.contains(e.target)) {
                                    // Background overlay clicked
                                    if (self.yes) {
                                        self.option('yes');
                                    } else {
                                        self.option('cancel');
                                    }
                                }
                            });
                        }
                    }, 1000);
                    break;

                case 'confirm':
                    button = $dom.create('button', {
                        'type': 'button',
                        'html': '{$GET;^,icon_yes} ' + this.yesButton,
                        'className': 'btn btn-primary btn-scri buttons--yes js-onclick-do-option-yes'
                    });
                    this.buttonContainerEl.appendChild(button);
                    this.buttonContainerEl.appendChild(document.createTextNode(' '));
                    button = $dom.create('button', {
                        'type': 'button',
                        'html': '{$GET;^,icon_no} ' + this.noButton,
                        'className': 'btn btn-secondary btn-scri buttons--no js-onclick-do-option-no'
                    });
                    this.buttonContainerEl.appendChild(button);
                    break;

                case 'prompt':
                    this.input = $dom.create('input', {
                        'name': 'prompt',
                        'id': 'overlay_prompt',
                        'type': this.inputType,
                        'size': '40',
                        'className': 'form-control form-control-wide',
                        'value': (this.defaultValue === null) ? '' : this.defaultValue
                    });
                    var inputWrap = $dom.create('div');
                    inputWrap.appendChild(this.input);
                    this.containerEl.appendChild(inputWrap);

                    if (this.yes) {
                        button = $dom.create('button', {
                            'type': 'button',
                            'html': '{$GET;^,icon_yes} ' + this.yesButton,
                            'className': 'btn btn-primary btn-scri buttons--yes js-onclick-do-option-yes'
                        });
                        this.buttonContainerEl.appendChild(button);
                    }

                    setTimeout(function () {
                        if (self.el) {
                            $dom.on(self.el, 'click', function (e) {
                                if (!self.containerEl.contains(e.target)) {
                                    // Background overlay clicked
                                    self.option('cancel');
                                }
                            });
                        }
                    }, 1000);
                    break;
            }

            // Cancel button handled either via button in corner (if there's no other buttons) or another button in the panel (if there's other buttons)
            if (this.cancelButton) {
                if (this.buttonContainerEl.firstElementChild) {
                    /*{+START,SET,icon_cancel}{+START,INCLUDE,ICON}NAME=buttons/cancel{+END}{+END}*/
                    button = $dom.create('button', {
                        'type': 'button',
                        'html': '{$GET;^,icon_cancel} ' + this.cancelButton,
                        'className': 'btn btn-secondary btn-scri buttons--cancel ' + (this.cancel ? 'js-onclick-do-option-cancel' : 'js-onclick-do-option-finished')
                    });
                    this.buttonContainerEl.appendChild(button);
                } else {
                    /*{$SET,icon_media_set_lightbox_close,{+START,INCLUDE,ICON}NAME=media_set/lightbox_close{+END}}*/
                    button = $dom.create('a', {
                        'href' : '#!',
                        'html': '{$GET;^,icon_media_set_lightbox_close}',
                        'className': 'overlay-close-button ' + (this.cancel ? 'js-onclick-do-option-cancel' : 'js-onclick-do-option-finished')
                    });
                    this.containerEl.appendChild(button);
                }
            }

            // Put together
            if (this.buttonContainerEl.firstElementChild) {
                if (this.type === 'iframe') {
                    this.containerEl.appendChild($dom.create('hr', {'className': 'spaced-rule'}));
                }
                this.containerEl.appendChild(this.buttonContainerEl);
            }

            // Handle dimensions
            this.resetDimensions(this.width, this.height, true);
            $dom.on(window, 'resize', function () {
                self.resetDimensions(self.width, self.height, false);
            });

            // Focus first button by default
            if (this.input) {
                setTimeout(function () {
                    self.input.focus();
                });
            } else if (this.el.querySelector('button')) {
                this.el.querySelector('button').focus();
            }

            // Enforce focus to stay inside the overlay
            $dom.on(document, 'focusin.modalWindow' + this.uid, function (e) {
                if ((document !== e.target) && !$dom.contains(self.el, e.target) && !$dom.parent(e.target, '.cms-modal')/*Some other modal dialog*/ && !self.el.contains(e.target)) {
                    var toFocus = $dom.$(self.el, 'button,input[type="text"],input[type="button"]');
                    if (toFocus) {
                        toFocus.focus();
                    }
                }
            });

            setTimeout(function () { // Timeout needed else keyboard activation of overlay opener may cause instant shutdown also
                $dom.on(document, 'keyup.modalWindow' + self.uid, self.keyup.bind(self));
                $dom.on(document, 'mousemove.modalWindow' + self.uid, self.mousemove.bind(self));
            }, 100);
        },

        windowScrollingBlocked: function windowScrollingBlocked() {
            return (document.documentElement.style.overflowY === 'hidden') || (document.documentElement.style.position === 'fixed');
        },

        keyup: function keyup(e) {
            if (e.key === 'ArrowLeft') {
                this.option('left');
            } else if (e.key === 'ArrowRight') {
                this.option('right');
            } else if ((e.key === 'Enter') && (this.yes)) {
                this.option('yes');
            } else if ((e.key === 'Enter') && (this.finished)) {
                this.option('finished');
            } else if ((e.key === 'Escape') && (this.cancelButton) && /^(prompt|confirm|lightbox|alert)$/.test(this.type)) {
                this.option('cancel');
            }
        },

        mousemove: function mousemove() {
            var self = this;
            if (!this.overlayEl.classList.contains('mousemove')) {
                this.overlayEl.classList.add('mousemove');
                setTimeout(function () {
                    if (self.overlayEl) {
                        self.overlayEl.classList.remove('mousemove');
                    }
                }, 2000);
            }
        },
        // Methods...
        close: function () {
            if (this.el) {
                this.topWindow.document.body.style.overflow = '';

                this.el.remove();
                this.el = null;

                $dom.off(document, 'focusin.modalWindow' + this.uid);
                $dom.off(document, 'keyup.modalWindow' + this.uid);
                $dom.off(document, 'mousemove.modalWindow' + this.uid);
            }
            this.opened = false;

            var bi = document.getElementById('main-website-inner');
            if (bi) {
                bi.classList.remove('faded');
            }
        },

        option: function (method) {
            if (this[method]) {
                if (this.type === 'prompt') {
                    this[method](this.input.value);
                } else if (this.type === 'iframe') {
                    this[method](this.returnValue);
                } else {
                    this[method]();
                }
            }
            if ((method !== 'left') && (method !== 'right')) {
                this.close();
            }
        },

        /**
         * @param {string} width
         * @param {string} height
         * @param {boolean} [init]
         * @param {boolean} [forceHeight]
         */
        resetDimensions: function (width, height, init, forceHeight) {
            width = strVal(width);
            height = strVal(height);
            init = Boolean(init);
            forceHeight = Boolean(forceHeight);

            if (!this.el) {
                return;
            }

            var topPageHeight = this.topWindow.$dom.getWindowScrollHeight(),
                topWindowWidth = this.topWindow.$dom.getWindowWidth(),
                topWindowHeight = this.topWindow.$dom.getWindowHeight();

            if (this.windowScrollingBlocked()) {
                forceHeight = true;
                height = (topWindowHeight - 60) + 'px';
            }

            var bottomGap = this.WINDOW_TOP_GAP;
            if (this.buttonContainerEl.firstElementChild) {
                bottomGap += this.buttonContainerEl.offsetHeight;
            }

            if (!forceHeight) {
                height = 'auto'; // Actually we always want auto heights, no reason to not for overlays
            }

            // Store for later (when browser resizes for example)
            this.width = width;
            this.height = height;

            // Normalise parameters (we don't have px on the end of pixel units, and these units refer to internal space size [% ones are relative to window though])
            width = width.replace(/px$/, '');
            height = height.replace(/px$/, '');

            // Constrain to window width
            if (width.match(/^\d+$/) !== null) {
                if ((parseInt(width) > topWindowWidth - this.WINDOW_SIDE_GAP * 2 - this.BOX_EAST_PERIPHERARY - this.BOX_WEST_PERIPHERARY) || (width === 'auto')) {
                    width = String(topWindowWidth - this.WINDOW_SIDE_GAP * 2 - this.BOX_EAST_PERIPHERARY - this.BOX_WEST_PERIPHERARY);
                }
            }

            // Auto width means full width
            if (width === 'auto') {
                width = String(topWindowWidth - this.WINDOW_SIDE_GAP * 2 - this.BOX_EAST_PERIPHERARY - this.BOX_WEST_PERIPHERARY);
            }
            // NB: auto height feeds through without a constraint (due to infinite growth space), with dynamic adjustment for iframes

            // Calculate percentage sizes
            var match;
            match = width.match(/^([\d.]+)%$/);
            if (match !== null) {
                width = String(parseFloat(match[1]) * (topWindowWidth - this.WINDOW_SIDE_GAP * 2 - this.BOX_EAST_PERIPHERARY - this.BOX_WEST_PERIPHERARY));
            }
            match = height.match(/^([\d.]+)%$/);
            if (match !== null) {
                height = String(parseFloat(match[1]) * (topPageHeight - this.WINDOW_TOP_GAP - bottomGap - this.BOX_NORTH_PERIPHERARY - this.BOX_SOUTH_PERIPHERARY));
            }

            // Work out box dimensions
            var boxWidth, boxHeight;
            if (width.match(/^\d+$/) !== null) {
                boxWidth = width + 'px';
            } else {
                boxWidth = width;
            }
            if (height.match(/^\d+$/) !== null) {
                boxHeight = height + 'px';
            } else {
                boxHeight = height;
            }

            // Save into HTML
            var detectedBoxHeight;
            this.overlayEl.style.width = boxWidth;
            this.overlayEl.style.height = boxHeight;
            var iframe = this.el.querySelector('iframe');

            if ($dom.hasIframeAccess(iframe) && (iframe.contentDocument.body)) { // Balance iframe height
                iframe.style.width = '100%';
                if (height === 'auto') {
                    if (!init) {
                        detectedBoxHeight = $dom.getWindowScrollHeight(iframe.contentWindow);
                        iframe.style.height = detectedBoxHeight + 'px';
                    }
                } else {
                    iframe.style.height = '100%';
                }
            }

            // Work out box position
            if (!detectedBoxHeight) {
                detectedBoxHeight = this.overlayEl.offsetHeight;
            }
            var boxPosTop, boxPosLeft;

            if (boxHeight === 'auto') {
                if (init) {
                    boxPosTop = (topWindowHeight / (2 + (this.VCENTRE_FRACTION_SHIFT * 2))) - (this.LOADING_SCREEN_HEIGHT / 2) + this.WINDOW_TOP_GAP; // This is just temporary
                } else {
                    boxPosTop = (topWindowHeight / (2 + (this.VCENTRE_FRACTION_SHIFT * 2))) - (detectedBoxHeight / 2) + this.WINDOW_TOP_GAP;
                }

                if (iframe) { // Actually, for frames we'll put at top so things don't bounce about during loading and if the frame size changes
                    boxPosTop = this.WINDOW_TOP_GAP;
                }
            } else {
                boxPosTop = (topWindowHeight / (2 + (this.VCENTRE_FRACTION_SHIFT * 2))) - (parseInt(boxHeight) / 2) + this.WINDOW_TOP_GAP;
            }
            if (boxPosTop < this.WINDOW_TOP_GAP) {
                boxPosTop = this.WINDOW_TOP_GAP;
            }
            boxPosLeft = ((topWindowWidth / 2) - (parseInt(boxWidth) / 2));

            // Save into HTML
            this.overlayEl.style.top = boxPosTop + 'px';
            this.overlayEl.style.left = boxPosLeft + 'px';
            if (this.title === '') {
                this.overlayEl.style.padding = this.BOX_PADDING + 'px';
            }

            var doScroll = false;

            // Absolute positioning instead of fixed positioning
            if ($cms.isMobile() || (detectedBoxHeight > (topWindowHeight - 90)/*We must subtract 90 due to padding on the overlay*/) || (this.el.style.position === 'absolute'/*don't switch back to fixed*/)) {
                var wasFixed = (this.el.style.position === 'fixed');

                this.el.style.position = 'absolute';
                this.el.style.width = '100%';
                this.el.style.height = ((topPageHeight > (detectedBoxHeight + bottomGap + boxPosTop)) ? topPageHeight : (detectedBoxHeight + bottomGap + boxPosTop)) + 'px';
                this.topWindow.document.body.style.overflow = '';

                if (!$cms.isMobile()) {
                    this.overlayEl.style.position = 'absolute';
                    this.overlayEl.style.top = this.WINDOW_TOP_GAP + 'px';
                }

                if (init || wasFixed) {
                    doScroll = true;
                }

                if (iframe && ($dom.hasIframeAccess(iframe)) && (iframe.contentWindow.scrolledUpFor === undefined)) { /*maybe a navigation has happened and we need to scroll back up*/
                    doScroll = true;
                }
            } else { // Fixed positioning, with scrolling turned off until the overlay is closed
                this.el.style.position = 'fixed';
                this.overlayEl.style.position = 'fixed';
                this.topWindow.document.body.style.overflow = 'hidden';
            }

            if (doScroll) {
                try { // Scroll to top to see
                    this.topWindow.scrollTo(0, 0);
                    if (iframe && ($dom.hasIframeAccess(iframe))) {
                        iframe.contentWindow.scrolledUpFor = true;
                    }
                } catch (ignore) {
                    // continue
                }
            }
        },
        /**
         * Fiddle it, to behave like a pop-up would
         * @param { HTMLIFrameElement } iframe
         */
        makeFrameLikePopup: function makeFrameLikePopup(iframe) {
            var mainWebsiteInner, mainWebsite, popupSpacer, baseElement;

            if ((iframe.parentNode.parentNode.parentNode.parentNode == null) && (this.iframeRestyleTimer != null)) {
                clearInterval(this.iframeRestyleTimer);
                this.iframeRestyleTimer = null;
                return;
            }

            var iDoc = iframe.contentDocument;

            if (!$dom.hasIframeAccess(iframe) || !iDoc.body || (iDoc.body.donePopupTrans !== undefined)) {
                if (hasIframeLoaded(iframe) && !hasIframeOwnership(iframe)) {
                    iframe.scrolling = 'yes';
                    iframe.style.height = '500px';
                }

                // Handle iframe sizing
                if (this.height === 'auto') {
                    this.resetDimensions(this.width, this.height, false);
                }
                return;
            }

            iDoc.body.style.background = 'transparent';
            iDoc.body.classList.add('overlay');
            iDoc.body.classList.add('lightbox');

            // Allow scrolling, if we want it
            //iframe.scrolling = (_this.scrollbars === false) ? 'no' : 'auto';  Actually, not wanting this now

            // Remove fixed width
            mainWebsiteInner = iDoc.getElementById('main-website-inner');

            if (mainWebsiteInner) {
                mainWebsiteInner.id = '';
            }

            // Remove main-website marker
            mainWebsite = iDoc.getElementById('main-website');
            if (mainWebsite) {
                mainWebsite.id = '';
            }

            // Remove pop-up spacing
            popupSpacer = iDoc.getElementById('popup-spacer');
            if (popupSpacer) {
                popupSpacer.id = '';
            }

            // Set linking scheme
            baseElement = iDoc.querySelector('base');

            if (!baseElement) {
                baseElement = iDoc.createElement('base');
                if (iDoc.head) {
                    iDoc.head.appendChild(baseElement);
                }
            }

            baseElement.target = this.target;

            // Set frame name
            if (this.name && (iframe.contentWindow.name !== this.name)) {
                iframe.contentWindow.name = this.name;
            }

            var self = this;
            // Create close function
            if (iframe.contentWindow.fauxClose === undefined) {
                iframe.contentWindow.fauxClose = function fauxClose() {
                    if (iframe && iframe.contentWindow && (iframe.contentWindow.returnValue !== undefined)) {
                        self.returnValue = iframe.contentWindow.returnValue;
                    }
                    self.option('finished');
                };
            }

            if ($dom.html(iDoc.body).length > 300) { // Loaded now
                iDoc.body.donePopupTrans = true;
            }

            // Handle iframe sizing
            if (this.height === 'auto') {
                this.resetDimensions(this.width, this.height, false);
            }

            function hasIframeLoaded(iframe) {
                try {
                    return (iframe != null) && (iframe.contentWindow.location.host !== '');
                } catch (ignore) {
                    return true; // Forcefully indicate the iframe is loaded; there is no way to check if it is not same-origin due to contentWindow restrictions
                }
            }

            function hasIframeOwnership(iframe) {
                try {
                    return (iframe != null) && (iframe.contentWindow.location.host === window.location.host) && (iframe.contentWindow.document != null);
                } catch (ignore) {
                    return false; // Not same-origin (contentWindow restriction), so always false
                }
            }
        }
    });

    /**
     * @memberof $cms.views
     * @class $cms.views.Global
     * @extends $cms.View
     * */
    $cms.views.Global = function Global(params) {
        Global.base(this, 'constructor', arguments);

        var pageLinkPrivacy = strVal(params.pageLinkPrivacy);

        /*START JS from HTML_HEAD.tpl*/
        // Google Analytics account, if one set up
        if (strVal($cms.configOption('google_analytics')).trim() && !$cms.isStaff() && !$cms.isAdmin()) {
            this.initializeGoogleAnalytics();
        }

        // Cookie Consent plugin by Osano - https://www.osano.com/cookieconsent
        if (($cms.runningScript() === 'index') && ($dom.$('meta[http-equiv="Refresh"]') === null) && (window.parent === window)) {
            $cms.requireJavascript('cookie_consent').then(function () {
                var cookieConsentOptions = {
                    cookie: {
                        path: $cms.getCookiePath(),
                        domain: $cms.getCookieDomain(),
                    },
                    palette: {
                        popup: {'background': '#000', 'text': '#FFF', 'link': '#FFF'},
                        button: {'background': '#FFF', 'text': '#000'},
                    },
                    theme: 'block',
                    content: {
                        message: $util.format('{!COOKIE_NOTICE;}', [$cms.getSiteName()]),
                        link: '{!READ_MORE;}',
                        href: pageLinkPrivacy,
                        allow: '{!ALLOW_COOKIES;}',
                        dismiss: '{!DENY_COOKIES;}', // e.g. deny cookies
                    },
                    revokable: true,
                    type: 'opt-in', // Required by GDPR
                };

                if ($cms.getCountry()) {
                    cookieConsentOptions['law'] = {
                        countryCode: $cms.getCountry(),
                    };
                }

                new window.CookieConsent(cookieConsentOptions);
            });
        }

        if ($cms.configOption('detect_javascript')) {
            this.detectJavascript();
        }
        /*END JS from HTML_HEAD.tpl*/

        // Mouse/keyboard listening
        this.registerMousePositionListener();

        if (document.getElementById('global-messages-2')) {
            var m1 = document.getElementById('global-messages');
            if (!m1) {
                return;
            }
            var m2 = document.getElementById('global-messages-2');
            $dom.append(m1, $dom.html(m2));
            m2.remove();
        }

        if (boolVal($cms.pageUrl().searchParams.get('wide_print'))) {
            try {
                window.print();
            } catch (ignore) {
                // continue
            }
        }

        if (($cms.getZoneName() === 'adminzone') && $cms.configOption('background_template_compilation')) {
            var page = $cms.filter.url($cms.getPageName());
            $cms.loadSnippet('background_template_compilation&page=' + page, '');
        }

        if (((window === window.top) && !window.opener) || (window.name === '')) {
            window.name = '_site_opener';
        }

        if ($cms.seesJavascriptErrorAlerts()) {
            this.initialiseErrorMechanism();
        }

        // Dynamic images need preloading
        var preloader = new Image();
        preloader.src = $util.srl('{$IMG;,loading}');
        preloader.width = '20';
        preloader.height = '20';

        // Tell the server we have JavaScript, so do not degrade things for reasons of compatibility - plus also set other things the server would like to know
        if ($cms.configOption('detect_javascript')) {
            $cms.setCookie('js_on', 1, 120);
        }

        if ($cms.configOption('is_on_timezone_detection')) {
            if (!window.parent || (window.parent === window)) {
                $cms.setCookie('client_time', (new Date()).toString(), 120);
                $cms.setCookie('client_time_ref', (Date.now() / 1000), 120);
            }
        }

        // If back button pressed back from an AJAX-generated page variant we need to refresh page because we aren't doing full JS state management
        window.addEventListener('popstate', function () {
            setTimeout(function () {
                if (!window.location.hash && window.hasJsState) {
                    window.location.reload();
                }
            });
        });

        // Monitor pasting, for anti-spam reasons
        window.addEventListener('paste', function (event) {
            var clipboardData = event.clipboardData || window.clipboardData;
            var pastedData = clipboardData.getData('Text');
            if (pastedData && (pastedData.length > $cms.configOption('spam_heuristic_pasting'))) {
                $cms.setPostDataFlag('paste');
            }
        });

        if ($cms.isStaff()) {
            this.loadStuffStaff();
        }
    };

    $util.inherits($cms.views.Global, $cms.View, /**@lends $cms.views.Global#*/{
        events: function () {
            return {
                /* Footer links */
                'click .js-global-click-load-software-chat': 'loadSoftwareChat',

                'submit .js-global-submit-staff-actions-select': 'staffActionsSelect',
            };
        },

        // Detecting of JavaScript support
        detectJavascript: function () {
            var url = window.location.href,
                append = '?';

            if ($cms.isJsOn() || boolVal($cms.pageUrl().searchParams.get('keep_has_js')) || url.includes('/upgrader.php') || url.includes('/webdav.php')) {
                return;
            }

            if (window.location.search.length === 0) {
                if (!url.includes('.htm') && !url.includes('.php')) {
                    append = 'index.php?';

                    if (!url.endsWith('/')) {
                        append = '/' + append;
                    }
                }
            } else {
                append = '&';
            }

            append += 'keep_has_js=1';

            if ($cms.isDevMode()) {
                append += '&keep_devtest=1';
            }

            // Redirect with JS on, and then hopefully we can remove keep_has_js after one click. This code only happens if JS is marked off, no infinite loops can happen.
            window.location = url + append;
        },

        registerMousePositionListener: function () {
            window.currentMouseX = 0;
            window.currentMouseY = 0;

            // Guess the initial mouse position approximately if possible:
            var hoveredElement = document.querySelectorAll(':hover');
            hoveredElement = hoveredElement[hoveredElement.length - 1];

            if (hoveredElement != null) {
                var rect = hoveredElement.getBoundingClientRect();
                window.currentMouseX = window.scrollX + rect.x;
                window.currentMouseY = window.scrollY + rect.y;
            }

            // Listen for mouse movements to set the correct values
            document.addEventListener('mousemove', function (e) {
                window.currentMouseX = e.pageX;
                window.currentMouseY = e.pageY;
            });
        },

        initializeGoogleAnalytics: function () {
            (function () {
                window['GoogleAnalyticsObject'] = 'ga';
                window.ga || (window.ga = function () {
                    window.ga.q || (window.ga.q = []);
                    window.ga.q.push(arguments);
                });
                window.ga.l = Number(new Date());
                var script = document.createElement('script'),
                    newSibling = document.getElementsByTagName('script')[0];
                script.async = true;
                script.src = '//www.google-analytics.com/analytics.js';
                newSibling.parentNode.insertBefore(script, newSibling);
            }());

            var aConfig = {};

            if ($cms.getCookieDomain() !== '') {
                aConfig.cookieDomain = $cms.getCookieDomain();
            }
            if (!$cms.configOption('long_google_cookies')) {
                aConfig.cookieExpires = 0;
            }

            window.ga('create', strVal($cms.configOption('google_analytics')).trim(), aConfig);

            if (!$cms.isGuest()) {
                window.ga('set', 'userId', strVal($cms.getMember()));
            }

            if ($cms.pageUrl().searchParams.has('_t')) {
                window.ga('send', 'event', 'tracking__' + strVal($cms.pageUrl().searchParams.get('_t')), window.location.href);
            }

            window.ga('send', 'pageview');
        },

        /* Software Chat */
        loadSoftwareChat: function () {
            $cms.ui.encryptData('Grant me le accezz 2 de chat!').then(function (encryptedData) {
                if (encryptedData == '') {
                    return;
                }

                var url = 'https://composr.app/chat/global_room.htm?wide_high=1&nick='; // TODO: add configuration option for this, possibly in rebrand
                url += encodeURIComponent($cms.getUsername().replace(/[^a-zA-Z0-9_\-\\[]{}^`|]/g, '') + '@' + $cms.getSiteName().replace(/[^a-zA-Z0-9_\-\\[]{}^`|]/g, ''));
                url += '&token=' + encodeURIComponent(encryptedData);

                var bi = document.getElementById('main-website-inner');

                var SOFTWARE_CHAT_EXTRA = $util.format('{!SOFTWARE_CHAT_EXTRA;^}', [$cms.filter.html($cms.getBrandBaseUrl())]);
                var html = /** @lang HTML */'' +
                    '<div class="software-chat">' +
                    '   <h2>{!CMS_COMMUNITY_HELP}</h2>' +
                    '   <ul class="spaced-list">' + SOFTWARE_CHAT_EXTRA + '</ul>' +
                    '   <p class="associated-link associated-links-block-group">' +
                    '       <a title="{!SOFTWARE_CHAT_STANDALONE} {!LINK_NEW_WINDOW;^}" target="_blank" href="' + $cms.filter.html(url) + '">{!SOFTWARE_CHAT_STANDALONE}</a>' +
                    '       <a href="#!" class="js-global-click-load-software-chat">{!HIDE}</a>' +
                    '   </p>' +
                    '</div>' +
                    '<iframe class="software-chat-iframe" style="border: 0" src="' + $cms.filter.html(url) + '"></iframe>';

                var box = $dom.$('#software-chat-box');
                if (box) {
                    box.remove();

                    if (bi) {
                        bi.classList.remove('faded');
                    }
                } else {
                    var width = 950,
                        height = 550;

                    box = $dom.create('div', {
                        id: 'software-chat-box',
                        css: {
                            width: width + 'px',
                            height: height + 'px',
                            background: '#EEE',
                            color: '#000',
                            padding: '5px',
                            border: '3px solid #AAA',
                            position: 'absolute',
                            zIndex: 2000,
                            left: ($dom.getWindowWidth() - width) / 2 + 'px',
                            top: 100 + 'px'
                        },
                        html: html
                    });

                    document.body.appendChild(box);

                    $dom.smoothScroll(0);

                    if (bi) {
                        bi.classList.add('faded');
                    }
                }
            });
        },

        /* Staff Actions links */
        staffActionsSelect: function (e, form) {
            var ob = form.elements['special_page_type'],
                val = ob.value;

            if (val !== 'view') {
                if (form.elements['cache'] !== undefined) {
                    form.elements['cache'].value = (val.substring(val.length - 4, val.length) === '.css') ? '1' : '0';
                }

                var windowName = 'cms_dev_tools' + Math.floor(Math.random() * 10000),
                    windowOptions;

                if (val === 'templates') {
                    windowOptions = 'width=' + window.screen.availWidth + ',height=' + window.screen.availHeight + ',scrollbars=yes';

                    setTimeout(function () { // Do a refresh with magic markers, in a comfortable few seconds
                        var oldUrl = window.location.href;
                        if ($cms.pageUrl().searchParams.get('keep_template_magic_markers') !== '1') {
                            window.location.href = oldUrl + (oldUrl.includes('?') ? '&' : '?') + 'keep_template_magic_markers=1&cache_blocks=0&cache_comcode_pages=0';
                        }
                    }, 10000);
                } else {
                    windowOptions = 'width=1020,height=700,scrollbars=yes';
                }

                var test = window.open('', windowName, windowOptions);

                if (test) {
                    form.target = test.name;
                }
            }
        },

        loadStuffStaff: function () {
            var loc = window.location.href;

            // Theme image editing hovers
            var els = $dom.$$('*:not(.js-no-theme-img-click)'), i, el, isImage;
            for (i = 0; i < els.length; i++) {
                el = els[i];
                isImage = (el.localName === 'img') || ((el.localName === 'input') && (el.type === 'image')) || $dom.css(el, 'background-image').includes('url');

                if (isImage) {
                    $dom.on(el, {
                        mouseover: handleImageMouseOver,
                        mouseout: handleImageMouseOut,
                        click: handleImageClick
                    });
                }
            }

            /* Thumbnail tooltips */
            if (loc.replace($util.rel($cms.getBaseUrl()), '').includes('/cms/')) {
                var urlPatterns = $cms.staffTooltipsUrlPatterns(),
                    links, pattern, hook, patternRgx;

                links = $dom.$$('td a');
                for (pattern in urlPatterns) {
                    hook = urlPatterns[pattern];
                    patternRgx = new RegExp(pattern);

                    links.forEach(function (link) { // eslint-disable-line no-loop-func
                        if (link.href && !link.getAttribute('href').startsWith('#') && (!link.title) && (!link.onmouseover) && (link.href.indexOf('&lang=') === -1) && !link.classList.contains('no-auto-tooltip')) {
                            var id = link.href.match(patternRgx);
                            if (id) {
                                applyComcodeTooltip(hook, id, link);
                            }
                        }
                    });
                }
            }

            /*
             TOOLTIPS FOR THUMBNAILS TO CONTENT, AS DISPLAYED IN CMS ZONE
             */
            function applyComcodeTooltip(hook, id, link) {
                link.addEventListener('mouseout', function () {
                    $cms.ui.deactivateTooltip(link);
                });
                link.addEventListener('mousemove', function (event) {
                    $cms.ui.repositionTooltip(link, event, false, false, null, true);
                });
                link.addEventListener('mouseover', function (event) {
                    var idChopped = id[1];
                    if (id[2] !== undefined) {
                        idChopped += ':' + id[2];
                    }
                    var comcode = '[block="' + hook + '" id="' + decodeURIComponent(idChopped) + '" no_links="1"]main_content[/block]';
                    if (link.renderedTooltip === undefined) {
                        link.isOver = true;

                        $cms.doAjaxRequest($util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,comcode_convert}?css=1&javascript=1&raw_output=1&box_title={!PREVIEW;&}' + $cms.keep())), null, 'data=' + encodeURIComponent(comcode)).then(function (xhr) {
                            if (xhr && xhr.responseText) {
                                link.renderedTooltip = xhr.responseText;
                            }
                            if (link.renderedTooltip !== undefined) {
                                if (link.isOver) {
                                    $cms.ui.activateTooltip(link, event, link.renderedTooltip, '400px', null, null, false, 0, false, true);
                                }
                            }
                        });
                    } else {
                        $cms.ui.activateTooltip(link, event, link.renderedTooltip, '400px', null, null, false, null, false, true);
                    }
                });
            }

            /*
             THEME IMAGE CLICKING
             */
            function handleImageMouseOver(event, target) {
                if (target.previousElementSibling && (target.previousElementSibling.classList.contains('magic-image-edit-link'))) {
                    return;
                }
                if (target.offsetWidth < 130) {
                    return;
                }

                var src = (target.src === undefined) ? $dom.css(target, 'background-image') : target.src;

                if ((target.src === undefined) && (!event.ctrlKey) && (!event.metaKey) && (!event.altKey)) {
                    return;// Needs ctrl key for background images
                }
                if (!src.includes('/themes/') || ($cms.getPageName() === 'admin_themes')) {
                    return;
                }

                if ($cms.configOption('enable_theme_img_buttons')) {
                    // Remove other edit links
                    var old = document.querySelectorAll('.magic-image-edit-link');
                    for (var i = old.length - 1; i >= 0; i--) {
                        old[i].remove();
                    }

                    // Add edit button
                    var ml = document.createElement('input');
                    ml.onclick = function (event) {
                        handleImageClick(event, target, true);
                    };
                    ml.type = 'button';
                    ml.id = 'editimg_' + target.id;
                    ml.value = '{!themes:EDIT_THEME_IMAGE;^}';
                    ml.className = 'btn btn-primary btn-sm magic-image-edit-link';
                    ml.style.position = 'absolute';
                    ml.style.left = $dom.findPosX(target) + 'px';
                    ml.style.top = $dom.findPosY(target) + 'px';
                    ml.style.zIndex = 3000;
                    ml.style.display = 'none';
                    target.parentNode.insertBefore(ml, target);

                    if (target.moLink) {
                        clearTimeout(target.moLink);
                    }
                    target.moLink = setTimeout(function () {
                        if (ml) {
                            ml.style.display = 'block';
                        }
                    }, 2000);
                }

                window.oldStatusImg = window.status;
                window.status = '{!SPECIAL_CLICK_TO_EDIT;^}';
            }

            function handleImageMouseOut(event) {
                var target = event.target;

                if ($cms.configOption('enable_theme_img_buttons')) {
                    if (target.previousElementSibling && (target.previousElementSibling.classList.contains('magic-image-edit-link'))) {
                        if ((target.moLink !== undefined) && (target.moLink)) {// Clear timed display of new edit button
                            clearTimeout(target.moLink);
                            target.moLink = null;
                        }

                        // Time removal of edit button
                        if (target.moLink) {
                            clearTimeout(target.moLink);
                        }

                        target.moLink = setTimeout(function () {
                            if ((target.editWindow === undefined) || (!target.editWindow) || (target.editWindow.closed)) {
                                if (target.previousElementSibling && (target.previousElementSibling.classList.contains('magic-image-edit-link'))) {
                                    target.parentNode.removeChild(target.previousElementSibling);
                                }
                            }
                        }, 3000);
                    }
                }

                if (window.oldStatusImg === undefined) {
                    window.oldStatusImg = '';
                }
                window.status = window.oldStatusImg;
            }

            function handleImageClick(event, ob, force) {
                ob || (ob = this);

                var src = ob.origsrc ? ob.origsrc : ((ob.src == null) ? $dom.css(ob, 'background-image').replace(/.*url\(['"]?(.*)['"]?\).*/, '$1') : ob.src);
                if (src && (force || ($cms.magicKeypress(event)))) {
                    // Bubbling needs to be stopped because shift+click will open a new window on some lower event handler (in Firefox anyway)
                    event.preventDefault();

                    if (src.includes($util.rel($cms.getBaseUrl()) + '/themes/')) {
                        ob.editWindow = window.open('{$BASE_URL;,0}/adminzone/index.php?page=admin_themes&type=edit_image&lang=' + encodeURIComponent($cms.userLang()) + '&theme=' + encodeURIComponent($cms.getTheme()) + '&url=' + encodeURIComponent($cms.protectURLParameter(src.replace('{$BASE_URL;,0}/', ''))) + $cms.keep(), 'edit_theme_image_' + ob.id);
                    } else {
                        $cms.ui.alert('{!NOT_THEME_IMAGE;^}');
                    }

                    return false;
                }

                return true;
            }
        },

        /* Staff JS error display */
        initialiseErrorMechanism: function () {
            window.onerror = function (msg, file, code) {
                msg = strVal(msg);

                if (
                    // Internet Explorer false positives
                    // LEGACY
                    (((msg.includes("'null' is not an object")) || (msg.includes("'undefined' is not a function"))) && ((file === undefined) || (file === undefined))) || // Weird errors coming from outside
                    (((code === 0) || (code === '0')) && (msg.includes('Script error.'))) || // Too generic, can be caused by user's connection error

                    // Firefox false positives
                    (msg.includes("attempt to run compile-and-go script on a cleared scope")) || // Intermittent bugginess
                    (msg.includes('UnnamedClass.toString')) || // Weirdness
                    (msg.includes('ASSERT: ')) || // Something too generic
                    ((file) && (file.includes('TODO: FIXME'))) || // Something too generic / Can be caused by extensions
                    (msg.includes('TODO: FIXME')) || // Something too generic / Can be caused by extensions
                    (msg.includes('Location.toString')) || // Buggy extensions may generate
                    // (msg.includes('Error loading script')) || // User's connection error (actually we should show an error to the user because the page may therefore not be stable)
                    (msg.includes('NS_ERROR_FAILURE')) || // Usually an internal error
                    (msg.includes('moz-extension')) || // An error inside an extension

                    // Google Chrome false positives
                    (msg.includes('can only be used in extension processes')) || // Can come up with MeasureIt
                    (msg.includes('extension.')) || // E.g. "Uncaught Error: Invocation of form extension.getURL() doesn't match definition extension.getURL(string path) schema_generated_bindings"

                    false // Just to allow above lines to be reordered
                ) {
                    // Comes up on due to various Firefox/extension/etc bugs
                    return null;
                }

                if (!window.doneOneError) {
                    window.doneOneError = true;
                    var alert = '{!JAVASCRIPT_ERROR;^}\n\n' + code + ': ' + msg + '\n' + file;
                    if (document.body) { // i.e. if loaded
                        $cms.ui.alert(alert, '{!ERROR_OCCURRED;^}');
                    }
                }
                return false;
            };
        }
    });

    $cms.views.GlobalHelperPanel = GlobalHelperPanel;
    /**
     * @memberof $cms.views
     * @class GlobalHelperPanel
     * @extends $cms.View
     */
    function GlobalHelperPanel() {
        GlobalHelperPanel.base(this, 'constructor', arguments);
        this.contentsEl = this.$('.js-helper-panel-contents');
    }

    $util.inherits(GlobalHelperPanel, $cms.View, /**@lends GlobalHelperPanel#*/{
        events: function () {
            return {
                'click .js-click-toggle-helper-panel': 'toggleHelperPanel'
            };
        },
        toggleHelperPanel: function () {
            var show = !$dom.isDisplayed(this.contentsEl),
                panelRight = $dom.$('#panel-right'),
                helperPanelContents = $dom.$('#helper-panel-contents'),
                helperPanelToggle = $dom.$('#helper-panel-toggle'),
                helperPanelToggleIcon = helperPanelToggle.querySelector('.icon');

            if (show) {
                panelRight.classList.remove('helper-panel-hidden');
                panelRight.classList.add('helper-panel-visible');
                $dom.fadeIn(helperPanelContents);

                if ($cms.readCookie('hide_helper_panel') === '1') {
                    $cms.setCookie('hide_helper_panel', '0', 100);
                }

                helperPanelToggle.title = '{!HELP_OR_ADVICE}: {!HIDE}';
                $cms.ui.setIcon(helperPanelToggleIcon, 'helper_panel/hide', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/helper_panel/hide}');
            } else {
                if ($cms.readCookie('hide_helper_panel') === '') {
                    $cms.ui.confirm('{!CLOSING_HELP_PANEL_CONFIRM;^}').then(function (answer) {
                        if (answer) {
                            _hideHelperPanel(panelRight, helperPanelContents, helperPanelToggle);
                        }
                    });
                } else {
                    _hideHelperPanel(panelRight, helperPanelContents, helperPanelToggle);
                }
            }

            function _hideHelperPanel(panelRight, helperPanelContents, helperPanelToggle) {
                panelRight.classList.remove('helper-panel-visible');
                panelRight.classList.add('helper-panel-hidden');
                helperPanelContents.style.display = 'none';
                $cms.setCookie('hide_helper_panel', '1', 100);
                helperPanelToggle.title = '{!HELP_OR_ADVICE}: {!SHOW}';
                $cms.ui.setIcon(helperPanelToggleIcon, 'helper_panel/show', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/helper_panel/show}');
            }
        }
    });

    $cms.views.Header = Header;
    /**
     * @memberof $cms.views
     * @class Header
     * @extends $cms.View
     */
    function Header() {
        Header.base(this, 'constructor', arguments);

        this.resizeLogic();
        window.addEventListener('orientationchange', this.resizeLogic.bind(this));
        window.addEventListener('resize', this.resizeLogic.bind(this));

        this.scrollLogic();
        window.addEventListener('scroll', this.scrollLogic.bind(this));

        if (this.isSticky()) {
            this.improveStickyForMobile();
        }
    }

    $util.inherits(Header, $cms.View, /**@lends $cms.views.Header#*/{
        events: function () {
            return {
                'click': 'handleClicking',
                'clickout': 'handleClicking',

                'click .btn-side-menu-toggler': 'toggleSideMenu',
                'click .js-click-toggle-button-popup': 'toggleTopButtonPopup'
            };
        },

        isSticky: function () {
            return this.el.classList.contains('is-sticky');
        },

        isClassic: function () {
            return this.el.classList.contains('header-classic');
        },

        isModern: function () {
            return this.el.classList.contains('header-modern');
        },

        isSideMenu: function (andOpen) {
            return this.el.classList.contains('header-side') && (!andOpen || this.el.classList.contains('is-side-menu-open'));
        },

        /**
         * @returns { $cms.views.DropdownMenu }
         */
        getDropdownMenuView: function () {
            return this.$('.menu-dropdown') ? $dom.data(this.$('.menu-dropdown')).viewObject : null;
        },

        toggleSideMenu: function () {
            var sideMenuEl = this.$('.js-side-menu-toggleable'),
                btn = this.$('.btn-side-menu-toggler'),
                promise;

            if ($dom.isDisplayed(sideMenuEl)) {
                this.el.classList.remove('is-side-menu-open');
                $cms.ui.setIcon(btn.querySelector('.icon'), 'menus/mobile_menu', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/menus/mobile_menu}');
                promise = $dom.hide(sideMenuEl, 'normal');

                if (this.getDropdownMenuView() != null) {
                    this.getDropdownMenuView().closeAllSubMenus();
                }
            } else {
                this.el.classList.add('is-side-menu-open');
                $cms.ui.setIcon(btn.querySelector('.icon'), 'admin/delete3', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/admin/delete3}');
                promise = $dom.show(sideMenuEl, 'normal');
            }

            return promise;
        },

        handleClicking: function (e) {
            var topButtonsEl = this.$('.top-buttons'),
                topButtonWrappers = this.$$('.top-button-wrapper');

            if (topButtonsEl != null) {
                topButtonWrappers.forEach(function (wrapperEl) {
                    var popupEl = wrapperEl.querySelector('.top-button-popup');

                    if (wrapperEl.contains(e.target) || !$dom.isDisplayed(popupEl)) {
                        return;
                    }

                    wrapperEl.classList.remove('is-popup-open');
                    $dom.finish(topButtonsEl).then(function () {
                        topButtonsEl.style.marginBottom = '';
                    });
                    $dom.hide(popupEl);
                });
            }

            if (this.isSideMenu(true) && !this.$('.header-inner').contains(e.target)) {
                this.toggleSideMenu();
            }
        },

        // Previously: $cms.ui.toggleTopBox(), _toggle_messaging_box()
        toggleTopButtonPopup: function (e, btn) {
            var topButtonsEl = this.$('.top-buttons'),
                wrapperEl = $dom.parent(btn, '.top-button-wrapper'),
                popupEl = wrapperEl.querySelector('.top-button-popup');

            if (!$dom.isDisplayed(popupEl)) {
                wrapperEl.classList.add('is-popup-open');

                if (this.el.classList.contains('is-touch-interface')) {
                    var popupElHeight;

                    $dom.show(popupEl);
                    popupElHeight = popupEl.offsetHeight;
                    $dom.hide(popupEl);

                    topButtonsEl.style.marginBottom = '';
                    $dom.animate(topButtonsEl, {
                        marginBottom: popupElHeight + 'px'
                    });

                    $dom.slideDown(popupEl);
                } else {
                    $dom.fadeIn(popupEl);
                }
            } else {
                wrapperEl.classList.remove('is-popup-open');

                if (this.el.classList.contains('is-touch-interface')) {
                    $dom.animate(topButtonsEl, {
                        marginBottom: 0
                    });

                    $dom.slideUp(popupEl);
                } else {
                    $dom.hide(popupEl);
                }
            }
        },

        resizeLogic: function () {
            var menuEl = this.$('.menu-dropdown'), // For forwarding CSS classes
                isTouchInterface = $cms.isCssMode('mobile') || this.el.classList.contains('header-side');

            this.el.classList.toggle('is-touch-interface', isTouchInterface);
            this.el.classList.toggle('is-hover-interface', !isTouchInterface);

            menuEl && menuEl.classList.toggle('is-touch-interface', isTouchInterface);
            menuEl && menuEl.classList.toggle('is-hover-interface', !isTouchInterface);

            if (!this.$('.top-button-wrapper.is-popup-open') || !isTouchInterface) {
                if (this.$('.top-buttons')) {
                    this.$('.top-buttons').style.marginBottom = '';
                }
            }

            this.moveTopButtons();

            if (this.wasLastCssModeMobile == null) {
                // First time executing
                this.onCssModeChange(true);
            } else if (this.wasLastCssModeMobile !== $cms.isCssMode('mobile')) {
                // CSS mode changed
                this.onCssModeChange(false);
            }

            this.wasLastCssModeMobile = $cms.isCssMode('mobile');
        },

        onCssModeChange: function (/*initialising*/) {
            if (this.el.classList.contains('header-side')) {
                this.setupSideHeaderToggleables();
            }
        },

        scrollLogic: function () {
            var menuEl = this.$('.menu-dropdown'); // For forwarding CSS classes

            if (
                this.el.classList.contains('header-modern')
                && document.documentElement.classList.contains('has-homepage-slider')
                && (!this.el.classList.contains('is-sticky') || (window.scrollY === 0))
            ) {
                this.el.classList.add('is-see-through');
                menuEl && menuEl.classList.add('is-see-through');
            } else {
                this.el.classList.remove('is-see-through');
                menuEl && menuEl.classList.remove('is-see-through');
            }
        },

        setupSideHeaderToggleables: function () {
            if ($cms.isCssMode('mobile')) {
                $dom.show(this.$('.js-side-menu-toggleable'));
                $dom.hide(this.$('.menu-dropdown-content'));
            } else {
                $dom.hide(this.$('.js-side-menu-toggleable'));
                $dom.show(this.$('.menu-dropdown-content'));
            }
        },

        moveTopButtons: function () {
            var topButtonsEl = this.$('.top-buttons');

            if (!topButtonsEl) {
                return;
            }

            if (this.el.classList.contains('is-touch-interface') && (topButtonsEl.parentElement !== this.$('.menu-dropdown-content'))) {
                $dom.prepend(this.$('.menu-dropdown-content'), topButtonsEl);
            } else if (this.el.classList.contains('is-hover-interface') && (topButtonsEl.parentElement !== this.$('.global-navigation-items'))) {
                $dom.append(this.$('.global-navigation-items'), topButtonsEl);
            }
        },

        // Hides sticky header when scrolling downwards on mobile, shows it again when scrolled upwards
        improveStickyForMobile: function () {
            // Add CSS animation effect (TODO: this blocks out the background transitioning animation between see-through and not)
            this.el.style.transition = 'margin-top 0.3s ease-in-out';

            var lastScrollY = 0,
                movement = 0,
                lastDirection = 0,
                that = this;

            window.addEventListener('scroll', function () {
                if ((window.scrollY === 0) || (that.$('.menu-dropdown') && that.$('.menu-dropdown').classList.contains('is-expanded'))) {
                    movement = 0;
                    that.el.style.marginTop = '';
                    return;
                }

                var headerHeight = $dom.height(that.el);

                if ($cms.isCssMode('mobile')) {
                    // Mobile: hide navbar on scroll down and re-show on scroll up
                    var margin;

                    movement += window.scrollY - lastScrollY;

                    if (window.scrollY > lastScrollY) { // Scrolled down
                        if (lastDirection !== 1) {
                            movement = 0;
                        }
                        margin = -headerHeight;
                        that.el.style.marginTop = margin + 'px';

                        lastDirection = 1;
                    } else { // Scrolled up
                        if (lastDirection !== -1) {
                            movement = 0;
                        }
                        that.el.style.marginTop = '0px';

                        lastDirection = -1;
                    }

                    lastScrollY = window.scrollY;
                }
            });
        }
    });

    $cms.views.Menu = Menu;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function Menu(params) {
        Menu.base(this, 'constructor', arguments);

        /** @var {string} */
        this.menu = strVal(params.menu);
        /** @var {string} */
        this.menuId = strVal(params.menuId);

        if (params.javascriptHighlighting) {
            menuActiveSelection(this.menuId);
        }
    }

    $util.inherits(Menu, $cms.View);

    // Templates:
    // MENU_dropdown.tpl
    // - MENU_BRANCH_dropdown.tpl
    $cms.views.DropdownMenu = DropdownMenu;
    /**
     * @memberof $cms.views
     * @class $cms.views.DropdownMenu
     * @extends Menu
     */
    function DropdownMenu() {
        DropdownMenu.base(this, 'constructor', arguments);

        this.menuContentEl = this.$('.menu-dropdown-content');

        this.wasTouch = this.isTouchInterface();
        this.responsiveLogic();
        window.addEventListener('orientationchange', this.responsiveLogic.bind(this));
        window.addEventListener('resize', this.responsiveLogic.bind(this));

        var that = this;

        this.$('.menu-dropdown-items-main').addEventListener('scroll', function () {
            that.el.classList.toggle('is-items-main-scrolled-inside', (this.scrollTop > 0));
        });
    }

    $util.inherits(DropdownMenu, Menu, /**@lends $cms.views.DropdownMenu#*/{
        events: function () {
            var bothEvents = {
                'click': 'handleClicking',
                'clickout': 'handleClicking',
            };

            var touchEvents = {
                'click .menu-dropdown-toggle-btn': 'toggleMenuContent',
                'click .menu-dropdown-item.has-children > .menu-dropdown-item-a': 'toggleSubMenu',
            };

            var mouseEvents = {
                'mouseover .menu-dropdown-item': 'mouseoverMenuItem',
                'mouseout .menu-dropdown-item': 'mouseoutMenuItem',

                'focusin .menu-dropdown-item.has-children > .menu-dropdown-item-a': 'focusinMenuItemAnchor',
                'focusin .menu-dropdown-item-a': 'toggleFocusClassOnMenuItems',
                'focusout .menu-dropdown-item-a': 'toggleFocusClassOnMenuItems',
            };

            return Object.assign(bothEvents, (this.isTouchInterface() ? touchEvents : mouseEvents));
        },

        /**
         * NB: Menus won't always be inside a header element so this can return null.
         * @returns { $cms.views.Header }
         */
        getHeaderView: function () {
            var headerEl = $dom.parent(this.el, '.header');

            return (headerEl != null) ? $dom.data(headerEl).viewObject : null;
        },

        isTouchInterface: function () {
            return this.el.classList.contains('is-touch-interface');
        },

        responsiveLogic: function () {
            this.maybeMakeMenuItemsScrollable();

            if (this.wasTouch === this.isTouchInterface()) {
                // Interface mode didn't change
                return;
            }

            this.wasTouch = this.isTouchInterface();

            /* Interface mode changed, re-attach event listeners etc. */

            this.delegateEvents();

            if (this.isTouchInterface()) {
                this.$$('.menu-dropdown-items.nlevel').forEach(function (miList) {
                    // Clear up remnants of hover dropdown opening CSS
                    Object.assign(miList.style, {
                        position: '',
                        left: '',
                        minWidth: '',
                        top: '',
                        zIndex: '',
                        display: 'none'
                    });
                });
            } else {
                this.$$('.menu-dropdown-items.nlevel').forEach(function (miList) {
                    // Clear up remnants of touch dropdown opening CSS
                    miList.style.display = 'none';
                });

                this.$$('.menu-dropdown-item.is-expanded').forEach(function (menuItem) {
                    menuItem.classList.remove('is-expanded');
                });

                $cms.ui.setIcon(this.$('.menu-dropdown-toggle-btn .icon'), 'menus/mobile_menu', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/menus/mobile_menu}');

                this.menuContentEl.style.removeProperty('display');
            }
        },

        maybeMakeMenuItemsScrollable: function () {
            var mainMenuItemsList = this.$('.menu-dropdown-items-main');

            if ((this.getHeaderView() != null) && this.getHeaderView().isSticky() && $cms.isCssMode('mobile')) {
                // Need to make the menu items section scrollable on mobile for when it exceeds screen size
                var top = mainMenuItemsList.getBoundingClientRect().top + 'px';
                mainMenuItemsList.style.maxHeight = 'calc(100vh - ' + top + ')';
            } else {
                mainMenuItemsList.style.maxHeight = '';
            }
        },

        handleClicking: function (e) {
            if (!this.isTouchInterface()) {
                if (!this.el.contains(e.target)) {
                    this.unsetActiveMenuInstantly();
                }
            }
        },

        /* Touch methods */

        toggleMenuContent: function (e) {
            e.preventDefault();

            if ($dom.isDisplayed(this.menuContentEl)) {
                $dom.slideUp(this.menuContentEl);
                this.closeAllSubMenus(this.el);
                $cms.ui.setIcon(this.$('.menu-dropdown-toggle-btn .icon'), 'menus/mobile_menu', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/menus/mobile_menu}');
                this.el.classList.remove('is-expanded');
            } else {
                $dom.slideDown(this.menuContentEl);
                $cms.ui.setIcon(this.$('.menu-dropdown-toggle-btn .icon'), 'admin/delete3', '{$IMG;,{$?,{$THEME_OPTION,use_monochrome_icons},icons_monochrome,icons}/admin/delete3}');
                this.el.classList.add('is-expanded');
            }

            this.maybeMakeMenuItemsScrollable();
        },

        toggleSubMenu: function (e, target) {
            var parentMenuItem = $dom.parent(target, '.menu-dropdown-item'),
                miList = parentMenuItem.querySelector('.menu-dropdown-items');

            e.preventDefault();

            if ($dom.isDisplayed(miList)) {
                $dom.slideUp(miList);
                parentMenuItem.classList.remove('is-expanded');
                this.closeAllSubMenus(miList);
            } else {
                $dom.slideDown(miList);
                parentMenuItem.classList.add('is-expanded');
                this.closeAllSubMenus(this.el, miList);
            }
        },

        closeAllSubMenus: function (parentEl, exceptHavingEl) {
            var promises = [];

            parentEl || (parentEl = this.el);

            parentEl.querySelectorAll('.menu-dropdown-item.is-expanded').forEach(function (menuItem) {
                var miList = menuItem.querySelector('.menu-dropdown-items');

                if (!exceptHavingEl || !menuItem.contains(exceptHavingEl)) {
                    promises.push($dom.slideUp(miList));
                    menuItem.classList.remove('is-expanded');
                }
            });

            return Promise.all(promises);
        },

        /* Mouse methods */

        mouseoverMenuItem: function (e, target) {
            if (target.contains(e.relatedTarget)) {
                return;
            }

            var misHovered = $dom.parent(target, '.menu-dropdown-items');
            setActiveMenu($dom.id(misHovered), this.menuId);

            var search = $dom.$(target, 'input[type="search"]');
            if (search) {
                setTimeout(function () {
                    search.focus();
                }, 100);
            }

            if (target.classList.contains('has-children')) {
                popupMenu(target.querySelector('.menu-dropdown-items'), target.classList.contains('toplevel') ? 'below' : 'right', this.menuId);
            }
        },

        mouseoutMenuItem: function (e, menuItem) {
            if (menuItem.contains(e.relatedTarget)) {
                return;
            }

            var itemsList = $dom.parent(menuItem, '.menu-dropdown-items');

            if ($dom.matches(itemsList, ':hover') || itemsList.querySelector(':focus')) {
                return;
            }

            setActiveMenu(null);
            recreateCleanTimeout();
        },

        focusinMenuItemAnchor: function (e, target) {
            var menuItem = $dom.parent(target, '.menu-dropdown-item'),
                popupEl = menuItem.querySelector('.menu-dropdown-items');

            popupMenu(popupEl, menuItem.classList.contains('toplevel') ? 'below' : 'right', this.menuId);
        },

        toggleFocusClassOnMenuItems: function () {
            var menuItems = this.$$('.menu-dropdown-item');

            menuItems.forEach(function (mi) {
                mi.classList.toggle('focus', Boolean(mi.querySelector('.menu-dropdown-item-a:focus')));
            });
        },

        unsetActiveMenuInstantly: function () {
            setActiveMenu(null);
            recreateCleanTimeout(true);
        }
    });

    $cms.views.PopupMenu = PopupMenu;
    /**
     * @memberof $cms.views
     * @class
     * @extends Menu
     */
    function PopupMenu() {
        PopupMenu.base(this, 'constructor', arguments);
    }

    $util.inherits(PopupMenu, Menu, /**@lends PopupMenu#*/{
        events: function () {
            return {
                'click .js-click-unset-active-menu': 'unsetActiveMenu',
                'mouseout .js-mouseout-unset-active-menu': 'unsetActiveMenu'
            };
        },

        unsetActiveMenu: function (e, target) {
            if (!target.contains(e.relatedTarget)) {
                setActiveMenu(null);
                recreateCleanTimeout();
            }
        }
    });

    $cms.views.PopupMenuBranch = PopupMenuBranch;
    /**
     * @memberof $cms.views
     * @class
     * @extends Menu
     */
    function PopupMenuBranch() {
        PopupMenuBranch.base(this, 'constructor', arguments);

        this.rand = this.params.rand;
        this.menu = $cms.filter.id(this.params.menu);
        this.popup = this.menu + '-pexpand-' + this.rand;
    }

    $util.inherits(PopupMenuBranch, $cms.View, /**@lends PopupMenuBranch#*/{
        events: function () {
            return {
                'focus .js-focus-pop-up-menu': 'popUpMenu',
                'mousemove .js-mousemove-pop-up-menu': 'popUpMenu',
                'mouseover .js-mouseover-set-active-menu': 'setActiveMenu'
            };
        },
        popUpMenu: function () {
            popupMenu('#' + this.popup, null, 'r-' + this.menu + '-p');
        },
        setActiveMenu: function () {
            if (getActiveMenu() == null) {
                setActiveMenu(this.popup, 'r-' + this.menu + '-p');
            }
        }
    });

    $cms.views.TreeMenu = TreeMenu;
    /**
     * @memberof $cms.views
     * @class
     * @extends Menu
     */
    function TreeMenu() {
        TreeMenu.base(this, 'constructor', arguments);
    }

    $util.inherits(TreeMenu, Menu, /**@lends TreeMenu#*/{
        events: function () {
            return {
                'click [data-menu-tree-toggle]': 'toggleMenu'
            };
        },

        toggleMenu: function (e, target) {
            var menuId = target.dataset.menuTreeToggle;

            $cms.ui.toggleableTray($dom.$('#' + menuId));
        }
    });

    // Templates:
    // MENU_mobile.tpl
    // - MENU_BRANCH_mobile.tpl
    $cms.views.MobileMenu = MobileMenu;
    /**
     * @memberof $cms.views
     * @class $cms.views.MobileMenu
     * @extends Menu
     */
    function MobileMenu() {
        MobileMenu.base(this, 'constructor', arguments);
        this.menuContentEl = this.$('.js-el-menu-content');
    }

    $util.inherits(MobileMenu, Menu, /**@lends $cms.views.MobileMenu#*/{
        events: function () {
            return {
                'click .js-click-toggle-content': 'toggleContent',
                'click .js-click-toggle-sub-menu': 'toggleSubMenu'
            };
        },
        toggleContent: function (e) {
            e.preventDefault();
            $dom.toggle(this.menuContentEl);
        },
        toggleSubMenu: function (e, link) {
            var subId = link.dataset.vwSubMenuId,
                subEl = this.$('#' + subId),
                href;

            if (!$dom.isDisplayed(subEl)) {
                e.preventDefault();
                $dom.show(subEl);
            } else {
                href = link.type;
                // Second click goes to it
                if (href && !href.startsWith('#')) {
                    return;
                }
                e.preventDefault();
                $dom.hide(subEl);
            }
        }
    });

    $cms.views.SelectMenu = SelectMenu;
    /**
     * @memberof $cms.views
     * @class $cms.views.SelectMenu
     * @extends Menu
     */
    function SelectMenu() {
        SelectMenu.base(this, 'constructor', arguments);
    }

    $util.inherits(SelectMenu, Menu, /**@lends $cms.views.SelectMenu#*/{
        events: function () {
            return {
                'change .js-change-redirect-to-value': 'redirect'
            };
        },
        redirect: function (e, changed) {
            if (changed.value) {
                window.location.href = changed.value;
            }
        }
    });

    /**
     * @param menuId
     */
    function menuActiveSelection(menuId) {
        var menuElement = $dom.$('#' + menuId),
            possibilities = [], isSelected, url, minScore, i;

        if (menuElement.localName === 'select') {
            for (i = 0; i < menuElement.options.length; i++) {
                url = menuElement.options[i].value;
                isSelected = menuItemIsSelected(url);
                if (isSelected !== null) {
                    possibilities.push({
                        url: url,
                        score: isSelected,
                        element: menuElement.options[i]
                    });
                }
            }

            if (possibilities.length > 0) {
                possibilities.sort(function (a, b) {
                    return a.score - b.score;
                });

                minScore = Number(possibilities[0].score);
                for (i = 0; i < possibilities.length; i++) {
                    if (Number(possibilities[i].score) !== minScore) {
                        break;
                    }
                    possibilities[i].element.selected = true;
                }
            }
        } else {
            var menuItems = menuElement.querySelectorAll('.non-current'), a;
            for (i = 0; i < menuItems.length; i++) {
                a = null;
                for (var j = 0; j < menuItems[i].children.length; j++) {
                    if (menuItems[i].children[j].localName === 'a') {
                        a = menuItems[i].children[j];
                    }
                }
                if (!a) {
                    continue;
                }

                url = (a.type === '') ? '' : a.href;
                isSelected = menuItemIsSelected(url);
                if (isSelected !== null) {
                    possibilities.push({
                        url: url,
                        score: isSelected,
                        element: menuItems[i]
                    });
                }
            }

            if (possibilities.length > 0) {
                possibilities.sort(function (a, b) {
                    return a.score - b.score;
                });

                minScore = Number(possibilities[0].score);
                for (i = 0; i < possibilities.length; i++) {
                    if (Number(possibilities[i].score) !== minScore) {
                        break;
                    }
                    possibilities[i].element.classList.remove('non-current');
                    possibilities[i].element.classList.add('current');
                }
            }
        }

        /**
         * @param url
         * @return {number|null}
         */
        function menuItemIsSelected(url) {
            url = strVal(url);

            if (url === '') {
                return null;
            }

            var currentUrl = window.location.href;
            if (currentUrl === url) {
                return 0;
            }
            var globalBreadcrumbs = document.getElementById('global-breadcrumbs');

            if (globalBreadcrumbs) {
                var links = globalBreadcrumbs.querySelectorAll('a');
                for (var i = 0; i < links.length; i++) {
                    if (url === links[links.length - 1 - i].href) {
                        return i + 1;
                    }
                }
            }

            return null;
        }
    }

    var menuHoldTime = 500,
        cleanMenusTimeout,
        activeMenu, // ID of currently hovered branch
        lastActiveMenu; // Actual menu name

    function setActiveMenu(id, menu) {
        activeMenu = id;
        if (menu != null) {
            lastActiveMenu = menu;
        }
    }

    function getActiveMenu() {
        return activeMenu;
    }

    function recreateCleanTimeout(instant) {
        if (cleanMenusTimeout) {
            clearTimeout(cleanMenusTimeout);
            cleanMenusTimeout = null;
        }

        if (instant) {
            cleanMenus();
            return;
        }

        cleanMenusTimeout = setTimeout(cleanMenus, menuHoldTime);
    }

    /**
     * @param popupEl
     * @param place
     * @param menu
     * @param outsideFixedWidth
     */
    function popupMenu(popupEl, place, menu) {
        popupEl = $dom.elArg(popupEl);
        place = strVal(place) || 'right';
        menu = strVal(menu);

        if (cleanMenusTimeout) {
            clearTimeout(cleanMenusTimeout);
        }

        if ($dom.isDisplayed(popupEl)) {
            return;
        }

        setActiveMenu($dom.id(popupEl, 'dropdown-'), menu);
        cleanMenus();

        var left = 0,
            top = 0,
            outsideFixedWidth = ($dom.$('.global-middle') !== null) && (!$dom.contains($dom.$('.global-middle'), popupEl));

        // Our own position computation as we are positioning relatively, as things expand out
        if ($dom.isCss(popupEl.parentElement.parentElement, 'position', 'absolute')) {
            left += popupEl.parentElement.offsetLeft;
            top += popupEl.parentElement.offsetTop;
        } else {
            var offsetParent = popupEl.parentElement;
            while (offsetParent) {
                if (offsetParent && $dom.isCss(offsetParent, 'position', 'relative')) {
                    break;
                }

                left += offsetParent.offsetLeft;
                top += offsetParent.offsetTop - (parseInt(offsetParent.style.borderTop) || 0);
                offsetParent = offsetParent.offsetParent;

                if (offsetParent && $dom.isCss(offsetParent, 'position', 'absolute')) {
                    break;
                }
            }
        }
        if (place === 'below') {
            top += popupEl.parentElement.offsetHeight;
        } else {
            left += popupEl.parentElement.offsetWidth;
        }

        var fullHeight = $dom.getWindowScrollHeight(); // Has to be got before e is visible, else results skewed
        popupEl.style.position = 'absolute';
        popupEl.style.left = '0'; // Setting this lets the browser calculate a more appropriate (larger) width, before we set the correct left for that width will fit
        $dom.fadeIn(popupEl);

        var fullWidth = (window.scrollX === 0) ? $dom.getWindowWidth() : window.document.body.scrollWidth;

        if ($cms.configOption('fixed_width') && !outsideFixedWidth) {
            var mainWebsiteInner = document.getElementById('main-website-inner');
            if (mainWebsiteInner) {
                fullWidth = mainWebsiteInner.offsetWidth;
            }
        }

        var eParentWidth = popupEl.parentElement.offsetWidth;
        var eParentHeight = popupEl.parentElement.offsetHeight;
        var eWidth = popupEl.offsetWidth;

        popupEl.style.minWidth = eParentWidth + 'px';

        positionLeft();
        setTimeout(positionLeft, 0);
        positionTop();
        setTimeout(positionTop, 0);

        popupEl.style.zIndex = 200;

        recreateCleanTimeout();

        function positionLeft() {
            var posLeft = left;
            if (place === 'below') { // Top-level of drop-down
                if (posLeft + eWidth > fullWidth) {
                    posLeft += eParentWidth - eWidth;
                }
            } else { // NB: For non-below, we can't assume 'left' is absolute, as it is actually relative to parent node which is itself positioned
                if (($dom.findPosX(popupEl.parentNode, true) + eWidth + eParentWidth + 10) > fullWidth) {
                    posLeft -= eWidth + eParentWidth;
                }
            }
            popupEl.style.left = posLeft + 'px';
        }

        function positionTop() {
            var posTop = top;
            if (posTop + popupEl.offsetHeight + 10 > fullHeight) {
                var abovePosTop = posTop - $dom.contentHeight(popupEl) + eParentHeight - 10;
                if (abovePosTop > 0) {
                    posTop = abovePosTop;
                }
            }
            popupEl.style.top = posTop + 'px';
        }
    }

    function cleanMenus() {
        cleanMenusTimeout = null;

        var menuEl = document.getElementById(lastActiveMenu);

        if (!menuEl) {
            return;
        }
        var tags = menuEl.querySelectorAll('.menu-dropdown-items.nlevel'),
            activeMenuEl = (getActiveMenu() != null) ? document.getElementById(getActiveMenu()) : null,
            hideable;

        for (var i = 0; i < tags.length; i++) {
            hideable = activeMenuEl ? !tags[i].contains(activeMenuEl) : true;
            if (hideable) {
                tags[i].style.left = '-9999px';
                tags[i].style.display = 'none';
            }
        }
    }

    $cms.views.TextAreaCopyCode = TextAreaCopyCode;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function TextAreaCopyCode(params) {
        TextAreaCopyCode.base(this, 'constructor', arguments);

        this.withWhitespaceId = strVal(params.withWhitespaceId);

        $cms.ui.manageScrollHeight(document.getElementById('with_whitespace_' + this.withWhitespaceId));
    }

    $util.inherits(TextAreaCopyCode, $cms.View, /**@lends TextAreaCopyCode#*/{
        events: function () {
            return {
                'click .js-btn-select': 'select'
            };
        },

        select: function () {
            document.getElementById('with_whitespace_' + this.withWhitespaceId).select();
        }
    });
}(window.$cms, window.$util, window.$dom));
