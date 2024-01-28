/* This file contains CMS-wide Behaviors */

(function ($cms, $util, $dom) {
    'use strict';

    var IN_MINIKERNEL_VERSION = document.documentElement.classList.contains('in-minikernel-version');

    /**
     * Addons will add "behaviors" under this namespace
     * @namespace $cms.behaviors
     */
    $cms.behaviors = {};

    // Implementation for [data-view]
    $cms.behaviors.initializeViews = {
        attach: function (context) {
            $util.once($dom.$$$(context, '[data-view]'), 'behavior.initializeViews').forEach(function (el) {
                var params = objVal($dom.data(el, 'viewParams')),
                    viewName = el.dataset.view,
                    viewOptions = {el: el};

                if (typeof $cms.views[viewName] !== 'function') {
                    $util.fatal('$cms.behaviors.initializeViews.attach(): Missing view constructor "' + viewName + '" for', el);
                    return;
                }

                try {
                    $dom.data(el).viewObject = new $cms.views[viewName](params, viewOptions);
                    //$util.inform('$cms.behaviors.initializeViews.attach(): Initialized view "' + el.dataset.view + '" for', el, view);
                } catch (ex) {
                    $util.fatal('$cms.behaviors.initializeViews.attach(): Exception thrown while initializing view "' + el.dataset.view + '" for', el, ex);
                }
            });
        }
    };

    // Implementation for [data-tpl]
    $cms.behaviors.initializeTemplates = {
        attach: function (context) {
            $util.once($dom.$$$(context, '[data-tpl]'), 'behavior.initializeTemplates').forEach(function (el) {
                var template = el.dataset.tpl,
                    params = objVal($dom.data(el, 'tplParams'));

                if (typeof $cms.templates[template] !== 'function') {
                    $util.fatal('$cms.behaviors.initializeTemplates.attach(): Missing template function "' + template + '" for', el);
                    return;
                }

                try {
                    $cms.templates[template].call(el, params, el);
                    //$util.inform('$cms.behaviors.initializeTemplates.attach(): Initialized template "' + template + '" for', el);
                } catch (ex) {
                    $util.fatal('$cms.behaviors.initializeTemplates.attach(): Exception thrown while calling the template function "' + template + '" for', el, ex);
                }
            });
        }
    };

    $cms.behaviors.initializeAnchors = {
        attach: function (context) {
            var anchors = $util.once($dom.$$$(context, 'a'), 'behavior.initializeAnchors'),
                hasBaseEl = Boolean(document.querySelector('base'));

            anchors.forEach(function (anchor) {
                var href = strVal(anchor.getAttribute('href'));
                // So we can change base tag especially when on debug mode
                if (hasBaseEl && href.startsWith('#') && (href !== '#!')) {
                    anchor.href = window.location.href.replace(/#.*$/, '') + href;
                }

                if ($cms.configOption('js_overlays')) {
                    // Lightboxes
                    if (anchor.rel && anchor.rel.includes('lightbox')) {
                        anchor.title = anchor.title.replace('{!LINK_NEW_WINDOW;^}', '').trim();
                    }

                    // Convert <a> title attributes into composr tooltips
                    if (!anchor.classList.contains('no-tooltip')) {
                        convertTooltip(anchor);
                    }
                }

                if (boolVal('{$VALUE_OPTION;,js_keep_params}')) {
                    // Keep parameters need propagating
                    if (anchor.href && anchor.href.startsWith($cms.getBaseUrl() + '/')) {
                        anchor.href = $cms.addKeepStub(anchor.href);
                    }
                }
            });
        }
    };

    $cms.behaviors.initializeForms = {
        attach: function (context) {
            var forms = $util.once($dom.$$$(context, 'form'), 'behavior.initializeForms');

            forms.forEach(function (form) {
                // HTML editor
                if (window.$editing !== undefined) {
                    window.$editing.loadHtmlEdit(form);
                }

                // Remove tooltips from forms as they are for screen-reader accessibility only
                form.title = '';

                // Convert form element title attributes into composr tooltips
                if ($cms.configOption('js_overlays')) {
                    // Convert title attributes into composr tooltips
                    var elements = $util.toArray(form.elements), j;

                    elements = elements.concat(form.querySelectorAll('input[type="image"]')); // JS DOM does not include input[type="image"] elements in form.elements

                    for (j = 0; j < elements.length; j++) {
                        if (elements[j].title && !elements[j].classList.contains('no-tooltip')) {
                            convertTooltip(elements[j]);
                        }
                    }
                }

                if (boolVal('{$VALUE_OPTION;,js_keep_params}')) {
                    /* Keep parameters need propagating */
                    if (form.action && form.action.startsWith($cms.getBaseUrl() + '/')) {
                        form.action = $cms.addKeepStub(form.action);
                    }
                }

                // This "proves" that JS is running, which is an anti-spam heuristic (bots rarely have working JS)
                if ((form.elements['csrf_token'] != null) && (form.elements['js_token'] == null)) {
                    var jsToken = document.createElement('input');
                    jsToken.type = 'hidden';
                    jsToken.name = 'js_token';
                    jsToken.value = form.elements['csrf_token'].value.split('').reverse().join(''); // Reverse the CSRF token for our JS token
                    form.appendChild(jsToken);
                }
            });
        }
    };

    $cms.behaviors.initializeTables = {
        attach: function attach(context) {
            // We could have an image-gd-text being transformed into a js-th-label-text-override, so wait until next frame to initialize tables.
            setTimeout(function () {
                var tables = $util.once($dom.$$$(context, 'table, .fake-table'), 'behavior.initializeTables');

                tables.forEach(function (table) {
                    // Responsive table prep work
                    if (table.classList.contains('responsive-table')) {
                        var trs = $dom.$$(table, 'tr, .fake-tr'),
                            thsFirstRow = $dom.$$(trs[0], ':scope > th, :scope > td, :scope > .fake-th, :scope > .js-th-label-text-override, :scope > .fake-td'),
                            i, tds, j, data;

                        for (i = 0; i < trs.length; i++) {
                            tds = $dom.$$(trs[i], ':scope > th, :scope > td, :scope > .fake-th, :scope > .js-th-label-text-override, :scope > .fake-td');
                            for (j = 0; j < tds.length; j++) {
                                // Always set a data-th attribute (unless it already exists) even if we set it to blank; a blank data-th tells CSS not to add a colon
                                if (!tds[j].hasAttribute('data-th')) {
                                    data = '';
                                    if (!tds[j].classList.contains('responsive-table-no-prefix-no-indent')/*This class specifies no indentation for a th/td label, so we must forcefully keep data-th as blank*/ && (thsFirstRow[j] != null)) {
                                        data = thsFirstRow[j].textContent.replace(/^\s+/, '').replace(/\s+$/, '');
                                    }
                                    tds[j].setAttribute('data-th', data);
                                }
                            }
                        }
                    }
                });
            }, 0);
        }
    };

    // We need to keep track of faded in elements so we can apply fade out later in CSS
    var trackingCssFadeIn = false;
    $cms.behaviors.trackCssFadeIn = {
        attach: function () {
            if (trackingCssFadeIn) {
                return;
            }

            trackingCssFadeIn = true;

            document.addEventListener('animationstart', function (e) {
                if (e.animationName === 'cms-fade-in') {
                    e.target && e.target.classList && e.target.classList.add('did-fade-in');
                }
            });

            document.addEventListener('animationend', function (e) {
                if (e.animationName === 'cms-fade-out') {
                    e.target && e.target.classList && e.target.classList.remove('did-fade-in');
                }
            });
        }
    };

    // Implementation for [data-focus-class="css-class-name"]
    // Toggles a class name based on whether the "focus" is inside an element
    // Needs to be used until we have wider browser support for the ":focus-within" pseudo class
    $cms.behaviors.focusClass = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-focus-class]'), 'behavior.focusClass');

            els.forEach(function (el) {
                var cssClass = strVal(el.dataset.focusClass);

                if (document.querySelector(':focus') && el.contains(document.querySelector(':focus'))) {
                    el.classList.add(cssClass);
                }

                $dom.on(el, 'focusin focusout', function (e) {
                    if (el.contains(e.relatedTarget)) {
                        return;
                    }

                    el.classList.toggle(cssClass, (e.type === 'focusin'));
                });
            });
        }
    };

    // Implementation for [data-click-pd]
    // Prevent-default for JS-activated elements (which may have noscript fallbacks as default actions)
    $cms.behaviors.onclickPreventDefault = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-click-pd]'), 'behavior.onclickPreventDefault');
            els.forEach(function (el) {
                $dom.on(el, 'click', function (e) {
                    e.preventDefault();
                });
            });
        }
    };

    // Implementation for input[data-cms-unchecked-is-indeterminate]
    $cms.behaviors.uncheckedIsIndeterminate = {
        attach: function (context) {
            var inputs = $util.once($dom.$$$(context, 'input[data-cms-unchecked-is-indeterminate]'), 'behavior.uncheckedIsIndeterminate');

            inputs.forEach(function (input) {
                if (input.type === 'checkbox') {
                    if (!input.checked) {
                        input.indeterminate = true;
                    }

                    $dom.on(input, 'change', function uncheckedIsIndeterminate() {
                        if (!input.checked) {
                            input.indeterminate = true;
                        }
                    });
                }
            });
        }
    };

    // Implementation for [data-submit-on-enter]
    // Text inputs on forms with a button will automatically submit on enter due to well-established browser functionality.
    // This behavior is useful for placing on lists, or forms without an actual button.
    $cms.behaviors.submitOnEnter = {
        attach: function (context) {
            var inputs = $util.once($dom.$$$(context, '[data-submit-on-enter]'), 'behavior.submitOnEnter');

            inputs.forEach(function (input) {
                $dom.on(input, (input.localName === 'select') ? 'keyup' : 'keypress', function submitOnEnter(e) {
                    if ($dom.keyPressed(e, 'Enter')) {
                        e.preventDefault();

                        var submitButton = $dom.$(input.form, 'input[type="submit"], button[type="submit"]');
                        if (submitButton) {
                            $dom.trigger(submitButton, 'click');
                        } else {
                            input.form.submit();
                        }
                    }
                });
            });
        }
    };

    /**
     * @param { string } type - 'mouseover' or 'mouseout'
     * @returns { Function }
     */
    function hoverClassBehaviorFn(type) {
        return function (context) {
            var els = $util.once($dom.$$$(context, '[data-' + type + '-class]'), 'behavior.' + type + 'Class');

            els.forEach(function (el) {
                $dom.on(el, type, function (e) {
                    var classes = objVal($dom.data(el, type + 'Class')), key, bool;

                    if (!e.relatedTarget || !el.contains(e.relatedTarget)) {
                        for (key in classes) {
                            bool = Boolean(classes[key]) && (classes[key] !== '0');
                            el.classList.toggle(key, bool);
                        }
                    }
                });
            });
        };
    }

    // Implementation for [data-mouseover-class="{ 'some-class' : 1|0 }"]
    // Toggle classes based on mouse location
    $cms.behaviors.mouseoverClass = {
        attach: hoverClassBehaviorFn('mouseover')
    };

    // Implementation for [data-mouseout-class="{ 'some-class' : 1|0 }"]
    // Toggle classes based on mouse location
    $cms.behaviors.mouseoutClass = {
        attach: hoverClassBehaviorFn('mouseout')
    };

    // Implementation for [data-cms-confirm-click="<Message>"]
    // Show a confirmation dialog for clicks on a link (is higher up for priority)
    var _confirmedClick;
    $cms.behaviors.confirmClick = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-cms-confirm-click]'), 'behavior.confirmClick');

            els.forEach(function (el) {
                var uid = $util.uid(el),
                    message = strVal(el.dataset.cmsConfirmClick);

                $dom.on(el, 'click', function (e) {
                    if ($dom.isCancelledSubmit(e)) {
                        return;
                    }

                    if (_confirmedClick === uid) {
                        // Confirmed, let it through
                        return;
                    }

                    e.preventDefault();

                    $cms.ui.confirm(message, function (result) {
                        if (result) {
                            _confirmedClick = uid;
                            $dom.trigger(el, 'click');
                        } else {
                            $dom.cancelSubmit(e);
                        }
                    });
                });
            });
        }
    };

    // Implementation for form[data-submit-modsecurity-workaround]
    // mod_security workaround
    $cms.behaviors.submitModSecurityWorkaround = {
        attach: function (context) {
            var forms = $util.once($dom.$$$(context, 'form[data-submit-modsecurity-workaround]'), 'behavior.submitModSecurityWorkaround');

            forms.forEach(function (form) {
                $dom.on(form, 'submit', function (e) {
                    // Note this does not check if the form submission is cancelled by something else, we do not support that as we assume that any validation logic is placed on the buttons and not the form
                    //  Submit handlers are then called on the forms and can take over actual submission if needed
                    if ($cms.form.isModSecurityWorkaroundEnabled()) {
                        e.preventDefault();
                        $cms.form.modSecurityWorkaround(form);
                    }
                });
            });
        }
    };

    // Implementation for form[data-disable-buttons-on-submit]
    // Disable form buttons on submit
    $cms.behaviors.disableButtonsOnFormSubmit = {
        attach: function (context) {
            var forms = $util.once($dom.$$$(context, 'form[data-disable-buttons-on-submit]'), 'behavior.disableButtonsOnFormSubmit');

            forms.forEach(function (form) {
                $dom.on(form, 'submit', function () {
                    $cms.ui.disableFormButtons(form);
                });
            });
        }
    };

    // Implementation for [data-disable-on-click]
    // Disable button after click
    $cms.behaviors.disableOnClick = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-disable-on-click]'), 'behavior.disableOnClick');

            els.forEach(function (el) {
                $dom.on(el, 'click', function () {
                    $cms.ui.disableButton(el);
                });
            });
        }
    };

    // Implementation for [data-cms-select2]
    $cms.behaviors.select2Plugin = {
        attach: function (context) {
            if (IN_MINIKERNEL_VERSION) {
                return;
            }

            $cms.requireJavascript(['jquery', 'select2']).then(function () {
                var els = $util.once($dom.$$$(context, '[data-cms-select2]'), 'behavior.select2Plugin');

                // Select2 plugin hook
                els.forEach(function (el) {
                    var options = objVal($dom.data(el, 'cmsSelect2'));
                    if (window.jQuery && window.jQuery.fn.select2) {
                        window.jQuery(el).select2(options);
                    }
                });
            });
        }
    };

    // Implementation for img[data-gd-text]
    // LEGACY
    $cms.behaviors.gdTextImages = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, 'img[data-gd-text]'), 'behavior.gdTextImages');

            els.forEach(function (img) {
                gdImageTransform(img);
            });

            function gdImageTransform(el) {
                /* GD text maybe can do with transforms */
                var span = document.createElement('span');
                if (typeof span.style.transform === 'string') {
                    if (el.parentNode.localName === 'th') {
                        span.classList.add('js-th-label-text-override');
                    }
                    el.style.display = 'none';
                    $dom.css(span, {
                        transform: 'rotate(90deg)',
                        transformOrigin: 'bottom left',
                        top: '-1em',
                        left: '0.5em',
                        position: 'relative',
                        display: 'inline-block',
                        whiteSpace: 'nowrap',
                        paddingRight: '0.5em'
                    });

                    el.parentNode.style.textAlign = 'left';
                    el.parentNode.style.width = '1em';
                    el.parentNode.style.overflow = 'hidden'; // LEGACY Needed due to https://bugzilla.mozilla.org/show_bug.cgi?id=456497
                    el.parentNode.style.verticalAlign = 'top';
                    span.textContent = el.alt;
                    span.title = el.alt;

                    el.parentNode.insertBefore(span, el);
                    var spanProxy = span.cloneNode(true); // So we can measure width even with hidden tabs
                    spanProxy.style.position = 'absolute';
                    spanProxy.style.visibility = 'hidden';
                    document.body.appendChild(spanProxy);

                    setTimeout(function () {
                        var width = spanProxy.offsetWidth + 15;
                        spanProxy.parentNode.removeChild(spanProxy);
                        if (el.parentNode.localName === 'th' || el.parentNode.localName === 'td') {
                            el.parentNode.style.height = width + 'px';
                        } else {
                            el.parentNode.style.minHeight = width + 'px';
                        }
                    }, 0);
                }
            }
        }
    };

    // Implementation for [data-toggleable-tray]
    $cms.behaviors.toggleableTray = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-toggleable-tray]'), 'behavior.toggleableTray');

            els.forEach(function (el) {
                var options = $dom.data(el, 'toggleableTray') || {};

                /**
                 * @type { $cms.views.ToggleableTray }
                 */
                $dom.data(el).toggleableTrayObject = new $cms.views.ToggleableTray(options, {el: el});
            });
        }
    };

    // Implementation for [data-click-tray-toggle="<SELECTOR FOR TRAY ELEMENT>"]
    // Toggle a tray on click on an element
    $cms.behaviors.clickToggleTray = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-click-tray-toggle]'), 'behavior.clickToggleTray');

            els.forEach(function (el) {
                $dom.on(el, 'click', function () {
                    var trayId = strVal(el.dataset.clickTrayToggle),
                        trayEl = $dom.$(trayId);

                    if (!trayEl) {
                        return;
                    }

                    var ttObj = $dom.data(trayEl).toggleableTrayObject;
                    if (ttObj) {
                        ttObj.toggleTray();
                    }
                });
            });
        }
    };

    // Implementation for [data-textarea-auto-height]
    $cms.behaviors.textareaAutoHeight = {
        attach: function (context) {
            if ($cms.isMobile()) {
                return;
            }

            var textareas = $util.once($dom.$$$(context, '[data-textarea-auto-height]'), 'behavior.textareaAutoHeight');
            textareas.forEach(function (textarea) {
                $cms.ui.manageScrollHeight(textarea);

                $dom.on(textarea, 'click input change keyup keydown', function manageScrollHeight() {
                    $cms.ui.manageScrollHeight(textarea);
                });
            });
        }
    };

    var _invalidPatternCache = Object.create(null);
    // Implementation for [data-prevent-input="<REGEX FOR DISALLOWED CHARACTERS>"]
    // Prevents input of matching characters
    $cms.behaviors.preventInput = {
        attach: function (context) {
            var inputs = $util.once($dom.$$$(context, 'data-prevent-input'), 'behavior.preventInput');

            inputs.forEach(function (input) {
                var pattern = input.dataset.preventInput, regex;

                regex = _invalidPatternCache[pattern] || (_invalidPatternCache[pattern] = new RegExp(pattern, 'g'));

                $dom.on(input, 'input keydown keypress', function (e) {
                    if (e.type === 'input') {
                        if (input.value.length === 0) {
                            input.value = ''; // value.length is also 0 if invalid value is entered for input[type=number] et al., clear that
                        } else if (input.value.search(regex) !== -1) {
                            input.value = input.value.replace(regex, '');
                        }
                    } else if ($dom.keyOutput(e, regex)) { // keydown/keypress event
                        // pattern matched, prevent input
                        e.preventDefault();
                    }
                });
            });
        }
    };

    // Implementation for [data-change-submit-form]
    // Submit form when the change event is fired on an input element
    $cms.behaviors.changeSubmitForm = {
        attach: function (context) {
            var inputs = $util.once($dom.$$$(context, '[data-change-submit-form]'), 'behavior.changeSubmitForm');

            inputs.forEach(function (input) {
                $dom.on(input, 'change', function () {
                    input.form.submit();
                });
            });
        }
    };

    // Implementation for [data-cms-btn-go-back]
    // Go back in browser history
    $cms.behaviors.btnGoBack = {
        attach: function (context) {
            var btns = $util.once($dom.$$$(context, '[data-cms-btn-go-back]'), 'behavior.btnGoBack');

            btns.forEach(function (btn) {
                $dom.on(btn, 'click', function () {
                    window.history.back();
                });
            });
        }
    };

    // Implementation for [data-click-stats-event-track]
    $cms.behaviors.clickStatsEventTrack = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-click-stats-event-track]'), 'behavior.clickStatsEventTrack');

            els.forEach(function (el) {
                $dom.on(el, 'click', function (e) {
                    var options = objVal($dom.data(el, 'clickStatsEventTrack'));

                    $cms.statsEventTrack(el, options.category, options.action, options.label, e, options.nativeTracking);
                });
            });
        }
    };

    // Implementation for [data-click-toggle-checked="<SELECTOR FOR TARGET CHECKBOX(ES)>"]
    $cms.behaviors.onclickToggleCheckboxes = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-click-toggle-checked]'), 'behavior.onclickToggleCheckboxes');

            els.forEach(function (el) {
                $dom.on(el, 'click', function () {
                    var selector = strVal(el.dataset.clickToggleChecked),
                        checkboxes = $dom.$$(selector);

                    checkboxes.forEach(function (checkbox) {
                        $dom.toggleChecked(checkbox);
                    });
                });
            });
        }
    };

    // Implementation for [data-click-alert]
    $cms.behaviors.onclickShowModalAlert = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-click-alert]'), 'behavior.onclickShowModalAlert');

            els.forEach(function (el) {
                $dom.on(el, 'click', function onclickShowModalAlert() {
                    var options = objVal($dom.data(el, 'clickAlert'), {}, 'notice');
                    $cms.ui.alert(options.notice);
                });
            });
        }
    };

    // Implementation for [data-keypress-alert]
    $cms.behaviors.onkeypressShowModalAlert = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-keypress-alert]'), 'behavior.onkeypressShowModalAlert');

            els.forEach(function (el) {
                $dom.on(el, 'keypress', function onkeypressShowModalAlert() {
                    var options = objVal($dom.data(el, 'keypressAlert'), {}, 'notice');
                    $cms.ui.alert(options.notice);
                });
            });
        }
    };

    // Implementation for [data-cms-rich-tooltip]
    // "Rich semantic tooltips"
    $cms.behaviors.cmsRichTooltip = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-cms-rich-tooltip]'), 'behavior.cmsRichTooltip');

            els.forEach(function (el) {
                var options = objVal($dom.data(el, 'cmsRichTooltip'));

                $dom.on(el, 'click mouseover keypress', function (e) {
                    if (el.ttitle === undefined) {
                        el.ttitle = (el.attributes['data-title'] ? el.getAttribute('data-title') : el.title);
                        el.title = '';
                    }

                    if ((e.type === 'mouseover') && options.haveLinks) {
                        return;
                    }

                    if (el.ttitle === '') {
                        return;
                    }

                    if (options.haveLinks && el.tooltipId && $dom.$id(el.tooltipId) && $dom.isDisplayed($dom.$id(el.tooltipId))) {
                        $cms.ui.deactivateTooltip(el);
                        return;
                    }

                    try {
                        //arguments: el, event, tooltip, width, pic, height, bottom, delay, lightsOff, forceWidth, win, haveLinks
                        $cms.ui.activateTooltip(el, e, el.ttitle, options.width || 'auto', null, options.height || null, false, 0, false, options.forceWidth || false, window, (options.haveLinks === undefined) ? true : options.haveLinks);
                    } catch (ex) {
                        //$util.fatal('$cms.behaviors.cmsRichTooltip.attach(): Exception thrown by $cms.ui.activateTooltip()', ex, 'called with args:', args);
                    }
                });
            });
        }
    };

    // Convert img title attributes into Composr tooltips
    $cms.behaviors.imageTooltips = {
        attach: function (context) {
            if (!$cms.configOption('js_overlays')) {
                return;
            }

            $util.once($dom.$$$(context, 'img:not([data-cms-rich-tooltip])'), 'behavior.imageTooltips').forEach(function (img) {
                convertTooltip(img);
            });
        }
    };

    // Convert svg title elements into Composr tooltips
    $cms.behaviors.svgTooltips = {
        attach: function (context) {
            if (!$cms.configOption('js_overlays')) {
                return;
            }

            $util.once($dom.$$$(context, 'svg:not([data-cms-rich-tooltip])'), 'behavior.svgTooltips').forEach(function (svg) {
                if (svg.querySelector('title')) {
                    convertTooltip(svg);
                }
            });
        }
    };

    // Convert abbr title attributes into Composr tooltips
    $cms.behaviors.abbrTooltips = {
        attach: function (context) {
            if (!$cms.configOption('js_overlays')) {
                return;
            }

            $util.once($dom.$$$(context, 'abbr:not([data-cms-rich-tooltip])'), 'behavior.abbrTooltips').forEach(function (abbr) {
                convertTooltip(abbr);
            });
        }
    };

    // Implementation for [data-cms-tooltip="{ ...options... }"]
    $cms.behaviors.cmsTooltip = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-cms-tooltip]'), 'behavior.cmsTooltip');

            els.forEach(function (el) {
                var options = $dom.data(el, 'cmsTooltip');

                if (typeof options !== 'object') {
                    options = {
                        contents: options,
                    };
                } else {
                    options.contents = strVal(options.contents); // Tooltip contents
                }

                if (options.delay != null) { // Delay before showing tooltip
                    options.delay = Number(options.delay) || 0;
                }

                if (options.triggers == null) { // What triggers the tooltip, values: 'hover', 'click', and 'focus'.
                    options.triggers = ['hover'];
                } else if (!Array.isArray(options.triggers)) {
                    options.triggers = strVal(options.triggers).trim().split(/\s+/);
                }

                if (options.triggers.includes('hover')) {
                    $dom.on(el, 'mouseenter', function (e) {
                        // Arguments: el, event, tooltip, width, pic, height, bottom, delay, lightsOff, forceWidth, win, haveLinks
                        $cms.ui.activateTooltip(el, e, options.contents, options.width, options.img, options.height, options.position === 'bottom', options.delay, options.dimImg, false, null, true);
                    });
                }

                if (options.triggers.includes('click')) {
                    $dom.on(el, 'click', function (e) {
                        // Arguments: el, event, tooltip, width, pic, height, bottom, delay, lightsOff, forceWidth, win, haveLinks
                        $cms.ui.activateTooltip(el, e, options.contents, options.width, options.img, options.height, options.position === 'bottom', options.delay, options.dimImg, false, null, true);
                    });
                }

                if (options.triggers.includes('focus')) {
                    $dom.on(el, 'focus', function (e) {
                        // Arguments: el, event, tooltip, width, pic, height, bottom, delay, lightsOff, forceWidth, win, haveLinks
                        $cms.ui.activateTooltip(el, e, options.contents, options.width, options.img, options.height, options.position === 'bottom', options.delay, options.dimImg, false, null, true);
                    });

                    $dom.on(el, 'blur', function () {
                        $cms.ui.deactivateTooltip(el);
                    });
                }
            });
        }
    };

    // Implementation for [data-click-forward="{ child: '.some-selector' }"]
    $cms.behaviors.onclickForwardTo = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-click-forward]'), 'behavior.onclickForwardTo');

            els.forEach(function (el) {
                $dom.on(el, 'click', function (e) {
                    var options = objVal($dom.data(el, 'clickForward'), {}, 'child'),
                        child = strVal(options.child), // Selector for target child element
                        except = strVal(options.except), // Optional selector for excluded elements to let pass-through
                        childEl = $dom.$(el, child);

                    if (!childEl) {
                        // Nothing to do
                        return;
                    }

                    if (!childEl.contains(e.target) && (!except || !$dom.closest(e.target, except, el.parentElement))) {
                        // ^ Make sure the child isn't the current event's target already, and check for excluded elements to let pass-through
                        e.preventDefault();
                        $dom.trigger(childEl, 'click');
                    }
                });
            });
        }
    };

    // Implementation for [data-click-ui-open]
    $cms.behaviors.onclickUiOpen = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-click-ui-open]'), 'behavior.onclickUiOpen');
            els.forEach(function (el) {
                $dom.on(el, 'click', function () {
                    var args = arrVal($dom.data(el, 'clickUiOpen'));
                    $cms.ui.open($util.rel($cms.maintainThemeInLink(args[0])), args[1], args[2], args[3], args[4]);
                });
            });
        }
    };

    // Implementation for [data-click-do-input]
    $cms.behaviors.onclickDoInput = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-click-do-input]'), 'behavior.onclickDoInput');

            els.forEach(function (el) {
                $dom.on(el, 'click', function () {
                    var args = arrVal($dom.data(el, 'clickDoInput')),
                        type = strVal(args[0]),
                        fieldName = strVal(args[1]),
                        tag = strVal(args[2]),
                        fnName = 'doInput' + $util.ucFirst($util.camelCase(type));

                    if (typeof window[fnName] === 'function') {
                        window[fnName](fieldName, tag);
                    } else {
                        $util.fatal('$cms.behaviors.onclickDoInput.attach(): Function not found "window.' + fnName + '()"');
                    }
                });
            });
        }
    };

    // Implementation for [data-open-as-overlay]
    // Open page in overlay
    $cms.behaviors.openAsOverlay = {
        attach: function (context) {
            if (!$cms.configOption('js_overlays')) {
                return;
            }

            var els = $util.once($dom.$$$(context, '[data-open-as-overlay]'), 'behavior.openAsOverlay');

            els.forEach(function (el) {
                el.setAttribute('aria-haspopup', 'dialog');

                $dom.on(el, 'click', function (e) {
                    var options, url = (el.href === undefined) ? el.action : el.href;

                    if ($util.url(url).hostname !== window.location.hostname) {
                        return; // Cannot overlay, different domain
                    }

                    e.preventDefault();

                    options = objVal($dom.data(el, 'openAsOverlay'));
                    options.el = el;

                    openLinkAsOverlay(options);
                });
            });
        }
    };

    // Implementation for `click a[rel*="lightbox"]`
    // Open link in a lightbox
    $cms.behaviors.onclickOpenLightbox = {
        attach: function (context) {
            if (!($cms.configOption('js_overlays'))) {
                return;
            }

            var els = $util.once($dom.$$$(context, 'a[rel*="lightbox"]'), 'behavior.onclickOpenLightbox');

            els.forEach(function (el) {
                $dom.on(el, 'click', function (e) {
                    el.setAttribute('aria-haspopup', 'dialog');

                    e.preventDefault();

                    if (el.querySelector('img, video')) {
                        openImageIntoLightbox(el, el.href.includes('.mp4') || el.href.includes('.m4v'));
                    } else {
                        openLinkAsOverlay({ el: el });
                    }

                    function openImageIntoLightbox(el, isVideo) {
                        var hasFullButton = (el.firstElementChild == null) || (el.href !== el.firstElementChild.src);
                        $cms.ui.openImageIntoLightbox(el.href, ((el.cmsTooltipTitle !== undefined) ? el.cmsTooltipTitle : el.title), null, null, hasFullButton, isVideo);
                    }
                });
            });
        }
    };

    // Implementation for [data-cms-href="<URL>"]
    // Simulated [href] for non <a> elements
    $cms.behaviors.cmsHref = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-cms-href]'), 'behavior.cmsHref');

            els.forEach(function (el) {
                $dom.on(el, 'click', function (e) {
                    var anchorClicked = Boolean($dom.closest(e.target, 'a', el));

                    // Make sure a child <a> element wasn't clicked and default wasn't prevented
                    if (!anchorClicked && !e.defaultPrevented) {
                        $util.navigate(el);
                    }
                });
            });
        }
    };

    // Implementation for [data-ajaxify="{...}"] and [data-ajaxify-target="1"]
    // Mark ajaxified containers with [data-ajaxify="{...}"]
    // Mark links and forms to ajaxify with [data-ajaxify-target="1"] or specify a selector with the "targetsSelector" option
    // Was previously known as $dom.internaliseAjaxBlockWrapperLinks()/internalise_ajax_block_wrapper_links()
    $cms.behaviors.ajaxify = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-ajaxify]'), 'behavior.ajaxify');

            els.forEach(function (ajaxifyContainer) {
                var options = objVal($dom.data(ajaxifyContainer, 'ajaxify')),
                    callUrl = $util.url(options.callUrl),
                    // ^ Block call URL
                    callParams = options.callParams,
                    // ^ Can be a string or a map of additional query string parameters that will be added to the call URL.
                    callParamsFromTarget = arrVal(options.callParamsFromTarget),
                    // ^ An array of regexes that we will match with query string params in the target's [href] or [action] URL and if matched, pass them along with the block call.
                    targetsSelector = strVal(options.targetsSelector),
                    // ^ A selector can be provided for additional targets, by default only child elements with [data-ajaxify-target="1"] will be ajaxified.
                    updateURL = boolVal(options.updateURL);
                    // ^ Whether the URL in the browser should be updated on an AJAX request

                if (typeof callParams === 'string') {
                    var _callParams = $util.iterableToArray((new URLSearchParams(callParams)).entries());
                    callParams = {};
                    _callParams.forEach(function (param) {
                        callParams[param[0]] = param[1];
                    });
                }

                if (callParams != null) {
                    for (var key in callParams) {
                        callUrl.searchParams.set(key, callParams[key]);
                    }
                }

                $dom.on(ajaxifyContainer, 'click', 'a[data-ajaxify-target]', doAjaxify);
                $dom.on(ajaxifyContainer, 'submit', 'form[data-ajaxify-target]', doAjaxify);

                if (targetsSelector !== '') {
                    $dom.on(ajaxifyContainer, 'click', 'a', function (e, clicked) {
                        if ((clicked.dataset.ajaxifyTarget != null) || ($util.url(clicked.href).origin !== window.location.origin)) {
                            return;
                        }
                        var targets = $util.toArray(ajaxifyContainer.querySelectorAll(targetsSelector));
                        if (targets.includes(clicked)) {
                            doAjaxify(e, clicked);
                        }
                    });

                    $dom.on(ajaxifyContainer, 'submit', 'form', function (e, submitted) {
                        if ((submitted.dataset.ajaxifyTarget != null) || ($util.url(submitted.action).origin !== window.location.origin)) {
                            return;
                        }
                        var targets = $util.toArray(ajaxifyContainer.querySelectorAll(targetsSelector));
                        if (targets.includes(submitted)) {
                            doAjaxify(e, submitted);
                        }
                    });
                }

                function doAjaxify(e, target) {
                    if (
                        e.defaultPrevented || // Default may have been prevented by a form validation function
                        ($dom.parent(target, '[data-ajaxify]') !== ajaxifyContainer) || // Make sure we aren't dealing with the child of a different [data-ajaxify] container
                        strVal(target.getAttribute((target.localName === 'a') ? 'href' : 'action')).startsWith('#') // Fragment identifier href/action
                    ) {
                        return;
                    }

                    e.preventDefault();

                    var thisCallUrl = $util.url(callUrl),
                        postParams = null,
                        targetUrl = $util.url((target.localName === 'a') ? target.href : target.action);

                    if (callParamsFromTarget.length > 0) {
                        // Any parameters matching a pattern must be sent in the URL to the AJAX block call
                        $util.iterableToArray(targetUrl.searchParams.entries()).forEach(function (param) {
                            var paramName = param[0],
                                paramValue = param[1];

                            callParamsFromTarget.forEach(function (pattern) {
                                pattern = new RegExp(pattern);

                                if (pattern.test(paramName)) {
                                    thisCallUrl.searchParams.set(paramName, paramValue);
                                }
                            });
                        });
                    }

                    var newWindowUrl = $cms.pageUrl(),
                        rgxSkipParams = /^(zone|page|type|id|raw|cache|auth_key|block_map|snippet|utheme|ajax)$/; // Params that shouldn't be added to the window URL
                    $util.iterableToArray(targetUrl.searchParams.entries()).forEach(function (param) {
                        if (!rgxSkipParams.test(param[0])) {
                            newWindowUrl.searchParams.set(param[0], param[1]);
                        }
                    });

                    if (target.localName === 'form') {
                        if (target.method.toLowerCase() === 'post') {
                            postParams = '';
                        }

                        $util.toArray(target.elements).forEach(function (element) {
                            var paramValue;

                            if (!element.name) {
                                return;
                            }

                            if (element.disabled || ['submit', 'reset', 'button', 'file'].includes(element.type) || (['radio', 'checkbox'].includes(element.type) && !element.checked)) {
                                // ^ Skip disabled fields, certain types and non-checked radio and checkbox fields
                                newWindowUrl.searchParams.delete(element.name); // Element value might have been previously added to the window URL
                                return;
                            }

                            paramValue = $cms.form.cleverFindValue(target, element);

                            if (target.method.toLowerCase() === 'post') {
                                if (postParams !== '') {
                                    postParams += '&';
                                }
                                postParams += element.name + '=' + encodeURIComponent(paramValue);
                            } else {
                                thisCallUrl.searchParams.set(element.name, paramValue);
                                if (!rgxSkipParams.test(element.name)) {
                                    newWindowUrl.searchParams.set(element.name, paramValue);
                                }
                            }
                        });
                    }

                    $cms.ui.clearOutTooltips();

                    // Make AJAX block call
                    $cms.callBlock($util.rel(thisCallUrl), '', ajaxifyContainer, false, false, postParams).then(function () {
                        window.setTimeout(function() {
                            window.scrollTo(0, $dom.findPosY(ajaxifyContainer, true));
                        }, 25);
                        window.hasJsState = true;
                        if (updateURL) {
                            window.history.pushState({}, document.title, newWindowUrl.toString()); // Update window URL
                        }
                    });
                }
            });
        }
    };

    // Only for debugging purposes, finds and logs orphan [data-ajaxify-target] instances
    $cms.behaviors.ajaxifyTarget = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-ajaxify-target]'), 'behavior.ajaxifyTarget');

            els.forEach(function (ajaxifyTarget) {
                if (!$dom.parent(ajaxifyTarget, '[data-ajaxify]')) {
                    $util.error('[data-ajaxify-target] instance found without a corresponding [data-ajaxify] container.', ajaxifyTarget);
                }
            });
        }
    };

    // Implementation for [data-stuck-nav]
    // Pinning to top if scroll out (LEGACY: CSS is going to have a better solution to this soon)
    $cms.behaviors.stuckNav = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-stuck-nav]'), 'behavior.stuckNav');

            els.forEach(function (stuckNav) {
                window.addEventListener('scroll', $util.throttle(function () {
                    scrollListener(stuckNav);
                }, 100));
            });

            /**
             * @param { Element } stuckNav
             */
            function scrollListener(stuckNav) {
                var stuckNavHeight = (stuckNav.realHeight == null) ? $dom.contentHeight(stuckNav) : stuckNav.realHeight;

                stuckNav.realHeight = stuckNavHeight;
                var posY = $dom.findPosY(stuckNav.parentNode, true),
                    footerHeight = document.querySelector('footer') ? document.querySelector('footer').offsetHeight : 0,
                    panelBottom = $dom.$('#panel-bottom'),
                    globalNavigation = $dom.$('#global-navigation');

                if (globalNavigation) {
                    posY -= globalNavigation.offsetHeight; // Subtract the top navigation bar so we start floating when we reach it opposed to the top of the page
                    footerHeight += globalNavigation.offsetHeight; // Also add more to the footer height so the nav bar does not overlap the footer
                }
                if (panelBottom) {
                    footerHeight += panelBottom.offsetHeight;
                }
                panelBottom = $dom.$('#global-messages-2');
                if (panelBottom) {
                    footerHeight += panelBottom.offsetHeight;
                }
                if (stuckNavHeight < ($dom.getWindowHeight() - footerHeight)) { // If there's space in the window to make it "float" between header/footer
                    var extraHeight = (window.pageYOffset - posY);
                    if (extraHeight > 0) {
                        var width = $dom.contentWidth(stuckNav),
                            height = $dom.contentHeight(stuckNav),
                            stuckNavWidth = $dom.contentWidth(stuckNav);

                        if (!window.getComputedStyle(stuckNav).getPropertyValue('width')) { // May be centered or something, we should be careful
                            stuckNav.parentNode.style.width = width + 'px';
                        }
                        stuckNav.parentNode.style.height = height + 'px';
                        stuckNav.style.position = 'fixed';
                        stuckNav.style.top = document.querySelector('.header.is-sticky') ? document.querySelector('.header.is-sticky').offsetHeight + 'px' : '0px';
                        stuckNav.style.zIndex = '50';
                        stuckNav.style.width = stuckNavWidth + 'px';
                    } else {
                        stuckNav.parentNode.style.width = '';
                        stuckNav.parentNode.style.height = '';
                        stuckNav.style.position = '';
                        stuckNav.style.top = '';
                        stuckNav.style.width = '';
                    }
                } else {
                    stuckNav.parentNode.style.width = '';
                    stuckNav.parentNode.style.height = '';
                    stuckNav.style.position = '';
                    stuckNav.style.top = '';
                    stuckNav.style.width = '';
                }
            }
        }
    };

    // Documentation for [data-cms-embedded-media={ ...options... }]
    // This behavior is used to mark embedded media of different types and platforms.
    // It's currently used by the gallery slideshows to detect and trigger media playback.
    // Example options:
    //    ready: true|false, <-- Whether it's ready to receive events
    //    video: true|false, <-- Whether it's a video.
    //    aspectRatio: '4:3' <-- Aspect ratio specified as a ratio of 'width:height' or a decimal fraction (like '1.25').
    //    listens: [] <-- Array of cms:media:* events this media responds to.
    //    emits: [] <-- Array of cms:media:* events this media emits.
    $cms.behaviors.cmsEmbeddedMedia = {
        attach: function () {},
    };

    // Implementation for [data-calc-height]
    var initialCalcHeight = true;
    var settingHeight = new WeakMap();
    $cms.behaviors.calcHeight = {
        attach: function (context) {
            var els = $util.once($dom.$$$(context, '[data-calc-height]'), 'behavior.calcHeight');

            els.forEach(function (el) {
                setHeight(el);

                // var mo = new MutationObserver(function () {
                //     mo.disconnect();
                //     setHeight(el);
                //     mo.observe(el, { characterData: true, childList: true, subtree: true });
                // });
                //
                // mo.observe(el, { characterData: true, childList: true, subtree: true });

                if (window.ResizeObserver) {
                    var initialObserve = true;
                    var ro = new window.ResizeObserver(function () {
                        if (initialObserve) {
                            initialObserve = false;
                            return;
                        }

                        ro.disconnect();
                        setHeight(el);
                        initialObserve = true;
                        ro.observe(el);
                    });

                    ro.observe(el);
                }
            });

            if (initialCalcHeight) {
                initialCalcHeight = false;

                window.addEventListener('resize', function () {
                    document.querySelectorAll('[data-calc-height]').forEach(setHeight);
                });

                window.addEventListener('orientationchange', function () {
                    document.querySelectorAll('[data-calc-height]').forEach(setHeight);
                });
            }

            function setHeight(el) {
                if (settingHeight.get(el)) {
                    return; // Prevent infinite loop
                }

                settingHeight.set(el, true);

                var cs = getComputedStyle(el),
                    wasHidden = (cs.display === 'none'),
                    prevDisplay, prevVisibility;

                if (wasHidden) {
                    // Get the element position to restore it later
                    prevDisplay = el.style.display;
                    prevVisibility = el.style.visibility;

                    // Place it so it displays as usually
                    el.style.display = $dom.initial(el, 'display');
                    el.style.visibility = 'hidden';
                }


                el.style.setProperty('height', 'auto', 'important');
                el.offsetHeight; // Redraw
                el.style.height = cs.height;

                if (wasHidden) {
                    el.style.display = prevDisplay;
                    el.style.visibility = prevVisibility;
                }

                settingHeight.delete(el);
            }
        }
    };

    // Implementation for [data-cms-slider]
    // Port of Bootstrap 4 Carousel http://getbootstrap.com/docs/4.1/components/carousel/
    $cms.behaviors.cmsSlider = {
        attach: function (context) {
            var sliders = $util.once($dom.$$$(context, '[data-cms-slider]'), 'behavior.cmsSlider');

            sliders.forEach(function (slider) {
                $dom.load.then(function () {
                    $dom.Slider._interface(slider, $dom.data(slider));
                });
            });
        }
    };

    (function () {
        var DATA_KEY = 'cms.slider';
        var EVENT_KEY = '.' + DATA_KEY;
        var DATA_API_KEY = '.data-api';
        var ARROW_LEFT_KEYCODE = 37; // KeyboardEvent.which value for left arrow key
        var ARROW_RIGHT_KEYCODE = 39; // KeyboardEvent.which value for right arrow key
        var TOUCHEVENT_COMPAT_WAIT = 500; // Time for mouse compat events to fire after touch

        var Default = {
            interval      : 5000,
            keyboard      : true,
            slide         : false,
            pause         : 'hover',
            wrap          : true,
            animateHeight : 600,
            disableIntervalOnMobile: false,
        };

        var Direction = {
            NEXT     : 'next',
            PREV     : 'prev',
            LEFT     : 'left',
            RIGHT    : 'right'
        };

        var Event = {
            SLIDE          : 'cms:slider:slide',
            SLID           : 'cms:slider:slid',
            KEYDOWN        : 'keydown' + EVENT_KEY,
            MOUSEENTER     : 'mouseenter' + EVENT_KEY,
            MOUSELEAVE     : 'mouseleave' + EVENT_KEY,
            TOUCHEND       : 'touchend' + EVENT_KEY,
            LOAD_DATA_API  : 'load' + EVENT_KEY + DATA_API_KEY,
            CLICK_DATA_API : 'click' + EVENT_KEY + DATA_API_KEY,
        };

        var ClassName = {
            SLIDER   : 'cms-slider',
            ACTIVE   : 'active',
            SLIDE    : 'cms-slider-slide',
            RIGHT    : 'cms-slider-item-right',
            LEFT     : 'cms-slider-item-left',
            NEXT     : 'cms-slider-item-next',
            PREV     : 'cms-slider-item-prev',
            ITEM     : 'cms-slider-item'
        };

        var Selector = {
            ACTIVE      : '.active',
            ACTIVE_ITEM : '.active.cms-slider-item',
            ITEM        : '.cms-slider-item',
            NEXT_PREV   : '.cms-slider-item-next, .cms-slider-item-prev',
            INDICATORS  : '.cms-slider-indicators',
            DATA_SLIDE  : '[data-slide], [data-slide-to]'
        };

        // class="cms-slider-caption cms-slider-fade cms-slider-fullscreen cms-slider-item cms-slider-item-left cms-slider-item-next cms-slider-item-prev cms-slider-item-right"
        // ^ Above comment serves to mark the classes as _used_ for the 'css_file' unit test

        $dom.Slider = Slider;
        /**
         * @constructor Slider
         */
        function Slider(element, config) {
            this._items = null;
            this._interval = null;
            this._intervalStartedAt = null;
            this._activeElement = null;

            this._isPaused = false;
            this._isSliding = false;

            this.touchTimeout = null;

            this._config = this._getConfig(config);

            this.el = element;
            this._indicatorsElement = this.el.querySelector(Selector.INDICATORS);
            this._progressBarFillElement = this.el.querySelector('.cms-slider-progress-bar-fill');
            this._scrollDownElement = this.el.querySelector('.cms-slider-scroll-button');

            this._addEventListeners();

            if (this._progressBarFillElement != null) {
                this._setProgressBar();
            }
        }

        Slider.Default = Default;

        $util.properties(Slider.prototype, /**@lends Slider#*/{
            // Public
            isSliding: function () {
                return this._isSliding;
            },

            next: function next() {
                if (!this._isSliding) {
                    return this._slide(Direction.NEXT);
                } else {
                    return Promise.resolve(false);
                }
            },

            nextWhenVisible: function nextWhenVisible() {
                // Don't call next when the page isn't visible
                // or the slider or its parent isn't visible
                if (!document.hidden && ($dom.isVisible(this.el) && $dom.css(this.el, 'visibility') !== 'hidden')) {
                    this.next();
                }
            },

            prev: function prev() {
                if (!this._isSliding) {
                    return this._slide(Direction.PREV);
                } else {
                    return Promise.resolve(false);
                }
            },

            pause: function pause(event) {
                if (!event) {
                    this._isPaused = true;
                }

                if (this.el.querySelector(Selector.NEXT_PREV)) {
                    $dom.trigger(this.el, 'transitionend');
                    this.cycle(true);
                }

                clearInterval(this._interval);
                this._interval = null;
                this._intervalStartedAt = null;
            },

            cycle: function cycle(event) {
                if (!event) {
                    this._isPaused = false;
                }

                if (this._interval) {
                    clearInterval(this._interval);
                    this._interval = null;
                    this._intervalStartedAt = null;
                }

                if (this._config.interval && !this._isPaused && (this.el.querySelectorAll('.cms-slider-item').length > 1) && (!this._config.disableIntervalOnMobile || !$cms.isCssMode('mobile'))) {
                    var self = this;
                    self._intervalStartedAt = Date.now();
                    this._interval = setInterval(function () {
                        self.nextWhenVisible();
                    }, this._config.interval);
                }
            },

            to: function to(index) {
                var self = this;

                this._activeElement = this.el.querySelector(Selector.ACTIVE_ITEM);

                var activeIndex = this._getItemIndex(this._activeElement);

                if (index > this._items.length - 1 || index < 0) {
                    return;
                }

                if (this._isSliding) {
                    $dom.one(this.el, Event.SLID, function () { self.to(index); });
                    return;
                }

                if (activeIndex === index) {
                    this.pause();
                    this.cycle();
                    return;
                }

                var direction = index > activeIndex ? Direction.NEXT : Direction.PREV;

                this._slide(direction, this._items[index]);
            },

            dispose: function dispose() {
                $dom.off(this.el, EVENT_KEY);
                $dom.removeData(this.el, DATA_KEY);

                this._items = null;
                this._config = null;
                this.el = null;
                this._interval = null;
                this._isPaused = null;
                this._isSliding = null;
                this._activeElement = null;
                this._indicatorsElement = null;
            },

            // Private

            _getConfig: function _getConfig(config) {
                config = $util.extend({}, Default, config);
                return config;
            },

            _addEventListeners: function _addEventListeners() {
                var self = this;

                if (self._scrollDownElement) {
                    // Hide "Scroll Down" button when slider is larger than viewport or it's mobile mode
                    $dom.toggle(self._scrollDownElement, (self.el.offsetHeight >= window.innerHeight) && $cms.isCssMode('desktop'));

                    $dom.on(window, 'resize orientationchange', function () {
                        $dom.toggle(self._scrollDownElement, (self.el.offsetHeight >= window.innerHeight) && $cms.isCssMode('desktop'));
                    });
                }

                $dom.on(this.el, 'click' + EVENT_KEY, '.cms-slider-scroll-button', function () {
                    $dom.smoothScroll(self.el.nextElementSibling);
                });

                if (this._config.keyboard) {
                    $dom.on(this.el, Event.KEYDOWN, function (event) { self._keydown(event); });
                }

                if (this._config.pause === 'hover') {
                    $dom.on(this.el, Event.MOUSEENTER, function (event) { self.pause(event); });
                    $dom.on(this.el, Event.MOUSELEAVE, function (event) { self.cycle(event); });

                    if ('ontouchstart' in document.documentElement) {
                        // If it's a touch-enabled device, mouseenter/leave are fired as
                        // part of the mouse compatibility events on first tap - the slider
                        // would stop cycling until user tapped out of it;
                        // here, we listen for touchend, explicitly pause the slider
                        // (as if it's the second time we tap on it, mouseenter compat event
                        // is NOT fired) and after a timeout (to allow for mouse compatibility
                        // events to fire) we explicitly restart cycling
                        $dom.on(this.el, Event.TOUCHEND, function () {
                            self.pause();
                            if (self.touchTimeout) {
                                clearTimeout(self.touchTimeout);
                            }
                            self.touchTimeout = setTimeout(function (event) { self.cycle(event); }, TOUCHEVENT_COMPAT_WAIT + self._config.interval);
                        });
                    }
                }
            },

            _keydown: function _keydown(event) {
                if (/input|textarea/i.test(event.target.tagName)) {
                    return;
                }

                switch (event.which) {
                    case ARROW_LEFT_KEYCODE:
                        event.preventDefault();
                        this.prev();
                        break;
                    case ARROW_RIGHT_KEYCODE:
                        event.preventDefault();
                        this.next();
                        break;
                    default:
                }
            },

            _getItemIndex: function _getItemIndex(element) {
                this._items = element && element.parentNode ? [].slice.call(element.parentNode.querySelectorAll(Selector.ITEM)) : [];
                return this._items.indexOf(element);
            },

            _getItemByDirection: function _getItemByDirection(direction, activeElement) {
                var isNextDirection = direction === Direction.NEXT;
                var isPrevDirection = direction === Direction.PREV;
                var activeIndex = this._getItemIndex(activeElement);
                var lastItemIndex = this._items.length - 1;
                var isGoingToWrap = isPrevDirection && activeIndex === 0 ||
                    isNextDirection && activeIndex === lastItemIndex;

                if (isGoingToWrap && !this._config.wrap) {
                    return activeElement;
                }

                var delta = direction === Direction.PREV ? -1 : 1;
                var itemIndex = (activeIndex + delta) % this._items.length;

                return itemIndex === -1 ? this._items[this._items.length - 1] : this._items[itemIndex];
            },

            _triggerSlideEvent: function _triggerSlideEvent(relatedTarget, eventDirectionName) {
                var targetIndex = this._getItemIndex(relatedTarget);
                var fromIndex = this._getItemIndex(this.el.querySelector(Selector.ACTIVE_ITEM));
                var slideEvent = $dom.createEvent(Event.SLIDE, {
                    relatedTarget: relatedTarget,
                    direction: eventDirectionName,
                    from: fromIndex,
                    to: targetIndex
                });

                return $dom.trigger(this.el, slideEvent);
            },

            _setActiveIndicatorElement: function _setActiveIndicatorElement(element) {
                if (this._indicatorsElement) {
                    var indicators = this._indicatorsElement.querySelectorAll(Selector.ACTIVE);
                    indicators.forEach(function (indicator) {
                        indicator.classList.remove(ClassName.ACTIVE);
                    });

                    var nextIndicator = this._indicatorsElement.children[this._getItemIndex(element)];

                    if (nextIndicator) {
                        nextIndicator.classList.add(ClassName.ACTIVE);
                    }
                }
            },

            _setProgressBar: function _setProgressBar() {
                if (this._intervalStartedAt == null) {
                    this._progressBarFillElement.style.removeProperty('width');
                } else {
                    var progressPercentage = (Date.now() - this._intervalStartedAt) / this._config.interval;
                    this._progressBarFillElement.style.width = (progressPercentage * 100) + '%';
                }
                requestAnimationFrame(this._setProgressBar.bind(this));
            },

            _slide: function _slide(direction, element) {
                var self = this;

                return new Promise(function (resolve) {
                    var activeElement = self.el.querySelector(Selector.ACTIVE_ITEM);
                    var activeElementIndex = self._getItemIndex(activeElement);
                    var nextElement = element || activeElement && self._getItemByDirection(direction, activeElement);
                    var nextElementIndex = self._getItemIndex(nextElement);
                    var isCycling = Boolean(self._interval);

                    var directionalClassName;
                    var orderClassName;
                    var eventDirectionName;

                    if (direction === Direction.NEXT) {
                        directionalClassName = ClassName.LEFT;
                        orderClassName = ClassName.NEXT;
                        eventDirectionName = Direction.LEFT;
                    } else {
                        directionalClassName = ClassName.RIGHT;
                        orderClassName = ClassName.PREV;
                        eventDirectionName = Direction.RIGHT;
                    }

                    if (nextElement && nextElement.classList.contains(ClassName.ACTIVE)) {
                        self._isSliding = false;
                        resolve(false);
                        return;
                    }

                    var isDefaultPrevented = !self._triggerSlideEvent(nextElement, eventDirectionName);

                    if (isDefaultPrevented) {
                        resolve(false);
                        return;
                    }

                    if (!activeElement || !nextElement) {
                        // Some weirdness is happening, so we bail
                        resolve(false);
                        return;
                    }

                    self._isSliding = true;

                    if (isCycling) {
                        self.pause();
                    }

                    self._setActiveIndicatorElement(nextElement);

                    var slidEvent = $dom.createEvent(Event.SLID, {
                        relatedTarget: nextElement,
                        direction: eventDirectionName,
                        from: activeElementIndex,
                        to: nextElementIndex
                    });

                    if (self.el.classList.contains(ClassName.SLIDE)) {
                        nextElement.classList.add(orderClassName);

                        reflow(nextElement);
                        activeElement.classList.add(directionalClassName);
                        nextElement.classList.add(directionalClassName);

                        if (self._config.animateHeight && (activeElement.offsetHeight !== nextElement.offsetHeight)) {
                            self.el.animate([
                                { height: activeElement.offsetHeight + 'px' },
                                { height: nextElement.offsetHeight + 'px' }
                            ], Number(self._config.animateHeight));
                        }

                        $dom.on(activeElement, 'transitionend', function listener(e) {
                            if (e.target !== activeElement) {
                                return; // Skip transitions on child elements
                            }

                            $dom.off(activeElement, 'transitionend', listener); // Listen once only

                            nextElement.classList.remove(directionalClassName);
                            nextElement.classList.remove(orderClassName);
                            nextElement.classList.add(ClassName.ACTIVE);

                            activeElement.classList.remove(ClassName.ACTIVE);
                            activeElement.classList.remove(orderClassName);
                            activeElement.classList.remove(directionalClassName);

                            self._isSliding = false;

                            setTimeout(function () { $dom.trigger(self.el, slidEvent); resolve(true); }, 0);
                        });
                    } else {
                        activeElement.classList.remove(ClassName.ACTIVE);
                        nextElement.classList.add(ClassName.ACTIVE);

                        self._isSliding = false;

                        $dom.trigger(self.el, slidEvent);

                        resolve(true);
                    }

                    if (isCycling) {
                        self.cycle();
                    }
                });
            }
        });

        Slider._interface = function _interface(el, config) {
            var instance = $dom.data(el, DATA_KEY);
            var _config = Object.assign({}, Default, $dom.data(el, 'cmsSlider'));

            if (typeof config === 'object') {
                _config = Object.assign({}, _config, config);
            }

            var action = typeof config === 'string' ? config : _config.slide;

            if (!instance) {
                instance = new Slider(el, _config);
                $dom.data(el, DATA_KEY, instance);
            }

            if (typeof config === 'number') {
                instance.to(config);
            } else if (typeof action === 'string') {
                if (instance[action] === undefined) {
                    throw new TypeError('No method named "' + action + '"');
                }
                instance[action]();
            } else if (_config.interval) {
                instance.pause();
                instance.cycle();
            }
        };

        function getSelectorFromElement(element) {
            var selector = element.getAttribute('data-target');
            if (!selector || selector === '#') {
                selector = element.getAttribute('href') || '';
            }

            try {
                return document.querySelector(selector) ? selector : null;
            } catch (err) {
                return null;
            }
        }

        function reflow(element) {
            return element.offsetHeight;
        }

        /**
         * ------------------------------------------------------------------------
         * Data Api implementation
         * ------------------------------------------------------------------------
         */

        $dom.on(document, Event.CLICK_DATA_API, Selector.DATA_SLIDE, function (event) {
            var selector = getSelectorFromElement(this);

            if (!selector) {
                return;
            }

            var target = document.querySelector(selector);

            if (!target || !target.classList.contains(ClassName.SLIDER)) {
                return;
            }

            var config = Object.assign({}, $dom.data(target), $dom.data(this));

            var slideIndex = this.getAttribute('data-slide-to');

            if (slideIndex) {
                config.interval = false;
            }

            Slider._interface(target, config);

            if (slideIndex) {
                $dom.data(target, DATA_KEY).to(slideIndex);
            }

            event.preventDefault();
        });
    }());

    function openLinkAsOverlay(options) {
        options = $util.defaults({
            width: '800',
            height: 'auto',
            target: '_top',
            el: null
        }, options);

        var width = strVal(options.width);

        if (width.match(/^\d+$/)) { // Restrain width to viewport width
            width = String(Math.min(parseInt(width), $dom.getWindowWidth() - 60));
        }

        var el = options.el,
            url = (el.href === undefined) ? el.action : el.href,
            urlStripped = url.replace(/#.*/, ''),
            newUrl = urlStripped + (!urlStripped.includes('?') ? '?' : '&') + 'wide_high=1' + url.replace(/^[^#]+/, '');

        $cms.ui.open(newUrl, null, 'width=' + width + ';height=' + options.height, options.target);
    }

    function convertTooltip(el) {
        var title = el.title ? el.title : ((el.localName === 'img') ? '' : (((el.localName === 'svg') && el.querySelector('title')) ? $dom.html(el.querySelector('title')) : ''));

        if (!title || $cms.browserMatches('touch_enabled') || el.classList.contains('leave-native-tooltip') || el.classList.contains('cms-keep-ui-controlled') || el.parentNode && el.parentNode.classList.contains('tooltip') || el.dataset['mouseoverActivateTooltip']) {
            return;
        }

        if ((el.localName === 'img') && !el.alt) {
            el.alt = el.title;
        }

        // Remove old tooltip
        if ((el.localName === 'svg') && el.querySelector('title')) {
            el.querySelector('title').remove();
        } else {
            el.title = '';
        }

        if (el.onmouseover || (el.firstElementChild && (el.firstElementChild.onmouseover || el.firstElementChild.title))) {
            // Only put on new tooltip if there's nothing with a tooltip inside the element
            return;
        }

        if ($dom.parent(el, '.tooltip')) {
            title = '';
        }

        if (el.textContent) {
            var prefix = el.textContent + ': ';
            if (title.substr(0, prefix.length) === prefix) {
                title = title.substring(prefix.length, title.length);
            } else {
                if (title === el.textContent) {
                    title = '';
                }
            }
        }

        if (title === '') {
            return;
        }

        // And now define nice listeners for it all...
        var global = $cms.getMainCmsWindow(true);

        el.cmsTooltipTitle = $cms.filter.html(title);

        $dom.on(el, 'mouseover.convertTooltip', function (event) {
            global.$cms.ui.activateTooltip(el, event, el.cmsTooltipTitle, 'auto', '', null, false, null, false, false, global);
        });
    }
}(window.$cms, window.$util, window.$dom));
