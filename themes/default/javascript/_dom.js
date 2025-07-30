/* This file contains DOM manipulation functionality, basic visual setting and getting, and visual niceties such as animations */

(function ($cms, $util, $dom) {
    'use strict';
    /**
     * @param el
     * @returns {*}
     */
    function getStyles(el) {
        // Support: IE <=11 only, Firefox <=30 (#15098, #14150)
        // IE throws on elements created in pop-ups
        // FF meanwhile throws on frame elements through "defaultView.getComputedStyle"
        var view = el.ownerDocument.defaultView;

        if (!view || !view.opener) {
            view = window;
        }

        return view.getComputedStyle(el);
    }

    var rgxIdSelector = /^#[\w-]+$/,
        rgxSimpleSelector = /^[#.]?[\w-]+$/,
        jsTypeRE = /^(?:|application\/javascript|text\/javascript)$/i,
        // Special attributes that should be set via method calls
        methodAttributes = { value: true, css: true, html: true, text: true, data: true, width: true, height: true, offset: true },
        rgxNotWhite = /\S+/g;

    setTimeout(function () {
        if ($dom._resolveInit) {
            $dom._resolveInit();
            delete $dom._resolveInit;
        }

        if (document.readyState === 'interactive') {
            // Workaround for browser bug, document.readyState == 'interactive' before [defer]'d <script>s are loaded.
            // See: https://github.com/jquery/jquery/issues/3271
            $dom.waitForResources($util.toArray(document.querySelectorAll('script[src][defer]'))).then(function () {
                if ($dom._resolveReady) {
                    $dom._resolveReady();
                    delete $dom._resolveReady;
                }
            });
        } else if (document.readyState === 'complete') {
            if ($dom._resolveReady) {
                $dom._resolveReady();
                delete $dom._resolveReady;
            }
        } else {
            document.addEventListener('DOMContentLoaded', function listener() {
                document.removeEventListener('DOMContentLoaded', listener);
                if ($dom._resolveReady) {
                    $dom._resolveReady();
                    delete $dom._resolveReady;
                }
            });
        }

        if (document.readyState === 'complete') {
            if ($dom._resolveLoad) {
                $dom._resolveLoad();
                delete $dom._resolveLoad;
            }
        } else {
            window.addEventListener('load', function listener() {
                window.removeEventListener('load', listener);
                if ($dom._resolveLoad) {
                    $dom._resolveLoad();
                    delete $dom._resolveLoad;
                }
            });
        }
    }, 0);

    var _privateData = new WeakMap();
    function privateData(object) {
        var data = _privateData.get(object);

        if (data == null) {
            data = Object.create(null);
            _privateData.set(object, data);
        }

        return data;
    }

    /**@namespace $dom*/
    /**
     * @param windowOrNodeOrSelector
     * @returns { Window|Node }
     */
    $dom.domArg = function domArg(windowOrNodeOrSelector) {
        var el;

        if (windowOrNodeOrSelector != null) {
            if ($util.isWindow(windowOrNodeOrSelector) || $util.isNode(windowOrNodeOrSelector)) {
                return windowOrNodeOrSelector;
            }

            if (typeof windowOrNodeOrSelector === 'string') {
                el = $dom.$(windowOrNodeOrSelector);

                if (el == null) {
                    throw new Error('$dom.domArg(): No element found for selector "' + strVal(windowOrNodeOrSelector) + '".');
                }

                return el;
            }
        }

        throw new TypeError('$dom.domArg(): Argument 1 must be a {' + 'Window|Node|string}, "' + $util.typeName(windowOrNodeOrSelector) + '" provided.');
    };

    /**
     * @param nodeOrSelector
     * @returns { Node }
     */
    $dom.nodeArg = function nodeArg(nodeOrSelector) {
        var el;

        if (nodeOrSelector != null) {
            if ($util.isNode(nodeOrSelector)) {
                return nodeOrSelector;
            }

            if (typeof nodeOrSelector === 'string') {
                el = $dom.$(nodeOrSelector);

                if (el == null) {
                    throw new Error('$dom.nodeArg(): No element found for selector "' + strVal(nodeOrSelector) + '".');
                }

                return el;
            }
        }

        throw new TypeError('$dom.nodeArg(): Argument 1 must be a {' + 'Node|string}, "' + $util.typeName(nodeOrSelector) + '" provided.');
    };

    /**
     * @param { string|Element } elementOrSelector
     * @returns { Element }
     */
    $dom.elArg = function elArg(elementOrSelector) {
        var el;

        if (elementOrSelector != null) {
            if ($util.isEl(elementOrSelector)) {
                return elementOrSelector;
            }

            if (typeof elementOrSelector === 'string') {
                el = $dom.$(elementOrSelector);

                if (el == null) {
                    throw new Error('$dom.elArg(): No element found for selector "' + strVal(elementOrSelector) + '".');
                }

                return el;
            }
        }

        throw new TypeError('$dom.elArg(): Argument 1 must be a {' + 'Element|string}, "' + $util.typeName(elementOrSelector) + '" provided.');
    };

    /**
     * Ensures the passed `el` has an id attribute and returns the id
     * @param { Element } el
     * @param {string} prefix
     * @return {string}
     */
    $dom.id = function id(el, prefix) {
        el = $dom.elArg(el);
        prefix = strVal(prefix) || 'rand-';

        if (el.id === '') {
            el.id = prefix + $util.random();
        }

        return el.id;
    };

    /**
     * Returns a single matching element
     * @memberof $dom
     * @param {string} id
     * @returns { HTMLElement|null }
     */
    $dom.$id = function $id(id) {
        id = strVal(id);

        var element = document.getElementById(id);

        if (element) {
            return element;
        }


        // Try a hyphen and underscore-insensitive search
        // TODO: Remove the following code once there's no mismatch of hyphens and underscores in element IDs

        var idHasUnderscores = id.includes('_');
        var idHasHyphens = id.includes('-');

        if (!idHasUnderscores && !idHasHyphens) {
            return null;
        }

        if (idHasUnderscores) {
            element = document.getElementById(id.replaceAll('_', '-'));

            if (element) {
                return element;
            }
        }

        if (idHasHyphens) {
            element = document.getElementById(id.replaceAll('-', '_'));

            if (element) {
                return element;
            }
        }

        // Go even deeper

        var idPrefix = id.split(/[-_]/, 1)[0];

        var possibleElements = document.querySelectorAll('[id^="' + idPrefix + '"]');

        if (!possibleElements.length) {
            return null;
        }

        var insensitiveIdPattern = $util.escapeRegExp(id).replace(/[-_]/g, '[-_]');

        var rgx = new RegExp('^' + insensitiveIdPattern + '$');

        for (var i = 0, len = possibleElements.length; i < len; i++) {
            var el = possibleElements[i];

            if (rgx.test(el.id)) {
                $util.warn('$dom.$id(): Resorted to hyphen and underscore-insensitive search for provided `id`: "' + id + '". Found id is: "' + el.id + '"');

                return el;
            }
        }

        return null;
    };

    /**
     * Returns a single matching element
     * @memberof $dom
     * @param {string} id
     * @returns { HTMLElement }
     */
    $dom.$requireById = function $requireById(id) {
        var element = $dom.$id(id);

        if (element == null) {
            throw new Error('$dom.$requireById(): No element found for selector: #' + id);
        }

        return element;
    };

    /**
     * Returns a single matching child element, defaults to 'document' as parent
     * @memberof $dom
     * @param contextOrName
     * @param name
     * @returns { HTMLElement|null }
     */
    $dom.$byName = function $byName(contextOrName, name) {
        var context;

        if (name === undefined) {
            name = contextOrName;
            context = document;
        } else {
            context = $dom.nodeArg(contextOrName);
        }
        name = strVal(name);

        return ('getElementsByName' in context) ? (context.getElementsByName(name)[0] || null) : context.querySelector('#' + name);
    };


    /**
     * Returns a single matching child element, defaults to 'document' as parent
     * Throws an error if not found
     * @memberof $dom
     * @param contextOrName
     * @param name
     * @returns { HTMLElement }
     */
    $dom.$requireByName = function $requireByName(contextOrName, name) {
        var context;

        if (name === undefined) {
            name = contextOrName;
            context = document;
        } else {
            context = $dom.nodeArg(contextOrName);
        }
        name = strVal(name);

        var element = ('getElementsByName' in context) ? context.getElementsByName(name)[0] : context.querySelector('[name="' + name + '"]');

        if (element == null) {
            throw new Error('$dom.$requireByName(): No element found with selector [name="' + name + '"]');
        }

        return element;
    };
    /**
     * Returns a single matching child element, `context` defaults to 'document'
     * @memberof $dom
     * @param context
     * @param selector
     * @returns {*}
     */
    $dom.$ = function $(context, selector) {
        if (selector === undefined) {
            selector = context;
            context = document;
        } else {
            context = $dom.nodeArg(context);
        }
        selector = strVal(selector);

        return (rgxIdSelector.test(selector) && ('getElementById' in context)) ? context.getElementById(selector.substr(1)) : context.querySelector(selector);
    };
    /**
     * `$dom.$$` is a CSS selector implementation which uses `document.querySelectorAll` and optimizes for some special cases, like `#id`, `.someclass` and `div`.
     * @memberof $dom
     * @param context
     * @param selector
     * @returns { Array<HTMLElement> }
     */
    $dom.$$ = function $$(context, selector) {
        var found;

        if (selector === undefined) {
            selector = context;
            context = document;
        } else {
            context = $dom.nodeArg(context);
        }
        selector = strVal(selector);

        // DocumentFragment is missing getElementById and getElementsBy(Tag|Class)Name in some implementations
        // <svg> is missing getElementsByClassName() in IE: https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/11729645/
        if (rgxSimpleSelector.test(selector) && ($util.isDoc(context) || $util.isEl(context)) && (context.localName !== 'svg')) {
            switch (selector[0]) {
                case '#': // selector is an ID
                    return ((found = (('getElementById' in context) ? context.getElementById(selector.substr(1)) : context.querySelector(selector)))) ? [found] : [];
                case '.': // selector is a class name
                    return $util.toArray(context.getElementsByClassName(selector.substr(1)));
                default: // selector is a tag name
                    return $util.toArray(context.getElementsByTagName(selector));
            }
        }

        return $util.toArray(context.querySelectorAll(selector));
    };
    /**
     * @memberof $dom
     * @param context
     * @param selector
     * @returns { Element }
     */
    $dom.$last = function $last(context, selector) {
        return $dom.$$(context, selector).pop();
    };
    /**
     * This one (3 dollars) also includes the context element (at offset 0) if it matches the selector
     * @memberof $dom
     * @param context
     * @param selector
     * @returns { Array }
     */
    $dom.$$$ = function $$$(context, selector) {
        if (selector === undefined) {
            selector = context;
            context = document;
        } else {
            context = $dom.nodeArg(context);
        }
        selector = strVal(selector);

        var els = $dom.$$(context, selector);

        if ($util.isEl(context) && $dom.matches(context, selector)) {
            els.unshift(context);
        }

        return els;
    };
    /**
     * @memberof $dom
     * @param tag
     * @param properties
     * @param attributes
     * @returns { Element }
     */
    $dom.create = function create(tag, properties, attributes) {
        var el = document.createElement(strVal(tag));

        if ($util.isObj(properties)) {
            $util.each(properties, function (key, value) {
                if (key in methodAttributes) {
                    $dom[key](el, value);
                } else if ($util.isObj(el[key]) && $util.isObj(value)) {
                    $util.extendDeep(el[key], value);
                } else {
                    el[key] = value;
                }
            });
        }

        if ($util.isObj(attributes)) {
            $util.each(attributes, function (key, value) {
                $dom.attr(el, key, value);
            });
        }

        return el;
    };

    // LEGACY
    $dom.support = {};
    /**
     *  Web animations API support (https://developer.mozilla.org/de/docs/Web/API/Element/animate)
     * @type {boolean}
     */
    $dom.support.animation = ('animate' in document.createElement('div'));
    /**
     * If the browser has support for an input[type=???]
     */
    $dom.support.inputTypes = {
        search: false, tel: false, url: false, email: false, datetime: false, date: false, month: false,
        week: false, time: false, 'datetime-local': false, number: false, range: false, color: false
    };
    (function () {
        var type, bool, inputEl = document.createElement('input'), smile = ':)';

        for (type in $dom.support.inputTypes) {
            inputEl.setAttribute('type', type);
            bool = inputEl.type !== 'text';

            if (bool && (type !== 'search') && (type !== 'tel')) {
                inputEl.value = smile;
                inputEl.style.cssText = 'position:absolute;visibility:hidden;';

                if ((type === 'range') && (inputEl.style.WebkitAppearance !== undefined)) {
                    document.documentElement.appendChild(inputEl);
                    bool = (getComputedStyle(inputEl).WebkitAppearance !== 'textfield') && (inputEl.offsetHeight !== 0);
                    document.documentElement.removeChild(inputEl);
                } else if ((type === 'url') || (type === 'email')) {
                    bool = (inputEl.checkValidity != null) && (inputEl.checkValidity() === false);
                } else {
                    bool = inputEl.value !== smile;
                }
            }

            $dom.support.inputTypes[type] = bool;
        }
    }());

    /**
     * Elements are considered visible if they consume space in the document. Visible elements have a width or height that is greater than zero.
     * Elements with visibility: hidden or opacity: 0 are considered to be visible, since they still consume space in the layout.
     * @memberof $dom
     * @param el
     * @return {boolean} - Whether the passed element is visible
     */
    $dom.isVisible = function (el) {
        el = $dom.elArg(el);

        return Boolean($dom.width(el) || $dom.height(el)) && ($dom.css(el, 'display') !== 'none');
    };
    /**
     * @memberof $dom
     * @param el
     * @return {boolean} - Whether the passed element is visible
     */
    $dom.isHidden = function (el) {
        el = $dom.elArg(el);

        return !$dom.isVisible(el);
    };
    /**
     * @memberof $dom
     * @param el
     * @param value
     * @returns {*}
     */
    $dom.value = function value(el, value) {
        el = $dom.elArg(el);

        if (value === undefined) {
            if ((el.localName !== 'select') || !el.multiple) {
                return el.value;
            }

            // Special case: <select [multiple]>
            var values = [], i;
            for (i = 0; i < el.options.length; i++) {
                if (el.options[i].selected) {
                    values.push(el.options[i].value);
                }
            }

            return values;
        }

        el.value = strVal((typeof value === 'function') ? value.call(el, $dom.value(el), el) : value);
    };
    /**
     * Also triggers the 'change' event
     * @memberof $dom
     * @param el
     * @param value
     * @returns {*}
     */
    $dom.changeValue = function changeValue(el, value) {
        el = $dom.elArg(el);

        el.value = strVal((typeof value === 'function') ? value.call(el, $dom.value(el), el) : value);
        $dom.trigger(el, 'change');
    };

    /**
     * Triggers the 'change' event after changing checked state
     * @memberof $dom
     * @param el
     * @param bool
     * @returns {*}
     */
    $dom.changeChecked = function changeChecked(el, bool) {
        el = $dom.elArg(el);

        el.checked = strVal((typeof bool === 'function') ? bool.call(el, el.checked, el) : bool);
        $dom.trigger(el, 'change');
    };

    $dom.waitForResources = function waitForResources(resourceEls) {
        if (resourceEls == null) {
            return Promise.resolve();
        }

        if ($util.isEl(resourceEls)) {
            resourceEls = [resourceEls];
        }

        if (!Array.isArray(resourceEls)) {
            $util.fatal('$dom.waitForResources(): Argument 1 must be of type {array|HTMLElement}, "' + $util.typeName(resourceEls) + '" provided.');
            return Promise.reject();
        }

        if (resourceEls.length < 1) {
            return Promise.resolve();
        }

        //$util.inform('$dom.waitForResources(): Waiting for resources', resourceEls);

        var resourcesToLoad = new window.Set();
        resourceEls.forEach(function (el) {
            if (!$util.isEl(el)) {
                $util.fatal('$dom.waitForResources(): Invalid item of type "' + $util.typeName(el) + '" in the `resourceEls` parameter.');
                return;
            }

            if ($dom.hasElementLoaded(el)) {
                //$util.inform('$dom.waitForResources() Resource already loaded', el);
                return;
            }

            switch (el.localName) {
                case 'script':
                    if (el.src && jsTypeRE.test(el.type)) {
                        resourcesToLoad.add(el);
                    }
                    break;

                case 'link':
                    if (el.rel === 'stylesheet') {
                        resourcesToLoad.add(el);
                    }
                    break;

                case 'img':
                case 'iframe':
                    resourcesToLoad.add(el);
                    break;
            }
        });

        if (resourcesToLoad.size < 1) {
            return Promise.resolve();
        }

        return new Promise(function (resolve) {
            document.addEventListener('load', resourceLoadListener, true);
            document.addEventListener('error', resourceLoadListener, true);

            function resourceLoadListener(event) {
                var loadedEl = event.target;

                if (!resourcesToLoad.has(loadedEl)) {
                    return;
                }

                // if (event.type === 'load') {
                //     $util.inform('$dom.waitForResources(): Resource loaded successfully', loadedEl);
                // }

                if (event.type === 'error') {
                    $util.fatal('$dom.waitForResources(): Resource failed to load', loadedEl);
                }

                resourcesToLoad.delete(loadedEl);

                if (resourcesToLoad.size < 1) {
                    resolve();
                }
            }
        });
    };

    var domDataMap = new WeakMap();
    /**
     * @param el
     * @return { object }
     */
    function dataCache(el) {
        // Check if the el object already has a cache
        var value = domDataMap.get(el), key;
        if (!value) { // If not, create one with the dataset
            value = {};
            domDataMap.set(el, value);
            for (key in el.dataset) {
                dataAttr(el, key);
            }
        }

        return value;
    }

    /**
     * @param el
     * @param key
     * @return {(object|number|string)}
     */
    function dataAttr(el, key) {
        var data, trimmed;
        // If nothing was found internally, try to fetch any
        // data from the HTML5 data-* attribute
        if (typeof (data = el.dataset[key]) === 'string') {
            trimmed = data.trim();

            if ((trimmed.startsWith('{') && trimmed.endsWith('}')) || (trimmed.startsWith('[') && trimmed.endsWith(']'))) { // Object or array?
                try {
                    data = $util.parseJson5(data);
                } catch (e) {
                    //$util.inform('dataAttr(): Ignoring invalid JSON5 on data attribute "' + key + '" of element', el);
                }
            } else if ((Number(data).toString() === data) && isFinite(data)) { // Only convert to a number if it doesn't change the string
                data = Number(data);
            }

            // Make sure we set the data so it isn't changed later
            dataCache(el)[key] = data;
        }
        return data;
    }

    /**
     * Data retrieval and storage
     * @memberof $dom
     * @param el
     * @param [key]
     * @param [value]
     * @returns {(object|string|number)}
     */
    $dom.data = function data(el, key, value) {
        // Note: We have internalised caching here. You must not change data-* attributes manually and expect this API to pick up on it.

        var data, prop;

        el = $dom.elArg(el);

        // Gets all values
        if (key === undefined) {
            return dataCache(el);
        }

        // Sets multiple values
        if ($util.isObj(key)) {
            data = dataCache(el);
            // Copy the properties one-by-one to the cache object
            for (prop in key) {
                data[$util.camelCase(key)] = key[prop];
            }

            return data;
        }

        if (value === undefined) {
            // Attempt to get data from the cache
            // The key will always be camelCased in Data
            data = dataCache(el)[$util.camelCase(key)];

            return (data !== undefined) ? data : dataAttr(el, key); // Check in el.dataset.* too
        }

        // Set the data...
        // We always store the camelCased key
        dataCache(el)[$util.camelCase(key)] = value;
        return value;
    };

    /**
     * @memberof $dom
     * @param owner
     * @param key
     */
    $dom.removeData = function removeData(owner, key) {
        owner = $dom.elArg(owner);

        var i, cache = domDataMap.get(owner);

        if (cache === undefined) {
            return;
        }

        if (key !== undefined) {
            // Support array or space separated string of keys
            if (Array.isArray(key)) {
                // If key is an array of keys...
                // We always set camelCase keys, so remove that.
                key = key.map($util.camelCase);
            } else {
                key = $util.camelCase(key);
                // If a key with the spaces exists, use it.
                // Otherwise, create an array by matching non-whitespace
                key = (key in cache) ? [key] : (key.match(rgxNotWhite) || []);
            }

            i = key.length;

            while (i--) {
                delete cache[key[i]];
            }
        }

        // Remove if there's no more data
        if ((key === undefined) || !$util.hasEnumerable(cache)) {
            domDataMap.delete(owner);
        }
    };

    var pageMetaCache;
    $dom.pageMeta = function pageMeta(name) {
        if (pageMetaCache === undefined) {
            pageMetaCache = {};
        }

        name = strVal(name);

        var metaEl = document.querySelector('meta[name="' + name + '"]'),
            data = pageMetaCache[name], trimmed;

        if (metaEl == null) {
            return '';
        }

        // If nothing was found internally, try to fetch any
        if ((data === undefined) && (typeof (data = metaEl.content) === 'string')) {
            trimmed = data.trim();

            if ((trimmed.startsWith('{') && trimmed.endsWith('}')) || (trimmed.startsWith('[') && trimmed.endsWith(']'))) {
                data = $util.parseJson5(data);
            } else if ((Number(data).toString() === data) && isFinite(data)) { // Only convert to a number if it doesn't change the string
                data = Number(trimmed);
            }

            pageMetaCache[name] = data;
        }

        return data;
    };

    /**
     * Get viewport width excluding scrollbars
     * @memberof $dom
     * @param win
     * @returns {number}
     */
    $dom.getWindowWidth = function getWindowWidth(win) {
        return (win || window).document.documentElement.clientWidth;
    };

    /**
     * Get viewport height excluding scrollbars
     * @memberof $dom
     * @param win
     * @returns {number}
     */
    $dom.getWindowHeight = function getWindowHeight(win) {
        return (win || window).document.documentElement.clientHeight;
    };

    /**
     * @memberof $dom
     * @param win
     * @returns {number}
     */
    $dom.getWindowScrollHeight = function getWindowScrollHeight(win) {
        win || (win = window);

        var rectHtml = win.document.documentElement.getBoundingClientRect(),
            rectBody = win.document.body.getBoundingClientRect(),
            a = (rectHtml.bottom - rectHtml.top),
            b = (rectBody.bottom - rectBody.top);

        return (a > b) ? a : b;
    };

    function findRelative(el) {
        while (el) {
            if ($dom.isCss(el, 'position', ['absolute', 'relative', 'fixed'])) {
                return el;
            }
            el = el.parentElement;
        }
    }
    /**
     * @memberof $dom
     * @deprecated
     * @param { Element } el
     * @param {boolean} notRelative - If true it gets the position relative to the browser window, else it will be relative to the most recent position:absolute/relative/fixed going up the element tree.
     * @returns {number}
     */
    $dom.findPosX = function findPosX(el, notRelative) {
        if (!el) {
            return 0;
        }

        el = $dom.elArg(el);

        var left = el.getBoundingClientRect().left + window.scrollX;

        if (!notRelative) {
            var relative = findRelative(el);
            if (relative != null) {
                left -= $dom.findPosX(relative, true);
            }
        }

        return left;
    };

    /**
     * @memberof $dom
     * @deprecated
     * @param { Element } el
     * @param {boolean} notRelative - If true it gets the position relative to the browser window, else it will be relative to the most recent position:absolute/relative/fixed going up the element tree.
     * @returns {number}
     */
    $dom.findPosY = function findPosY(el, notRelative) {
        if (!el) {
            return 0;
        }

        el = $dom.elArg(el);

        var top = el.getBoundingClientRect().top + window.scrollY;

        if (!notRelative) {
            var relative = findRelative(el);
            if (relative != null) {
                top -= $dom.findPosY(relative, true);
            }
        }

        return top;
    };

    /**
     * Also see $dom.contentWidth.
     * @memberof $dom
     * @param obj
     * @param value
     * @returns {number}
     */
    $dom.width = function width(obj, value) {
        var offset;

        obj = $dom.domArg(obj);

        if (value === undefined) {
            return $util.isWindow(obj) ? obj.innerWidth :
                $util.isDoc(obj) ? obj.documentElement.scrollWidth :
                    (offset = $dom.offset(obj)) && (Number(offset.width) || 0);
        }

        $dom.css(obj, 'width', (typeof value === 'function') ? value.call(obj, $dom.width(obj)) : value);
    };

    /**
     * Also see $dom.contentHeight.
     * @memberof $dom
     * @param obj
     * @param value
     * @returns {number}
     */
    $dom.height = function height(obj, value) {
        var offset;

        obj = $dom.domArg(obj);

        if (value === undefined) {
            return $util.isWindow(obj) ? obj.innerHeight :
                $util.isDoc(obj) ? obj.documentElement.scrollHeight :
                    (offset = $dom.offset(obj)) && (Number(offset.height) || 0);
        }

        $dom.css(obj, 'height', (typeof value === 'function') ? value.call(obj, $dom.height(obj)) : value);
    };

    /**
     * @memberof $dom
     * @param el
     * @param coordinates
     * @returns {*}
     */
    $dom.offset = function offset(el, coordinates) {
        el = $dom.elArg(el);

        if (coordinates === undefined) {
            if (!el.ownerDocument.documentElement.contains(el)) {
                return { top: 0, left: 0 };
            }

            var rect = el.getBoundingClientRect();
            return {
                left: rect.left + window.scrollX,
                top: rect.top + window.scrollY,
                width: Math.round(rect.width),
                height: Math.round(rect.height)
            };
        }

        var coords = (typeof coordinates === 'function') ? coordinates.call(el, $dom.offset(el)) : coordinates,
            parentOffset = $dom.offset($dom.offsetParent(el)),
            props = {
                top: coords.top - parentOffset.top,
                left: coords.left - parentOffset.left
            };

        if ($dom.css(el, 'position') === 'static') {
            props.position = 'relative';
        }

        $dom.css(el, props);
    };

    /**
     * @memberof $dom
     * @param el
     * @returns { Element }
     */
    $dom.offsetParent = function offsetParent(el) {
        el = $dom.elArg(el);

        var parent = el.offsetParent || el.ownerDocument.body;
        while (parent && (parent.localName !== 'html') && (parent.localName !== 'body') && ($dom.css(parent, 'position') === 'static')) {
            parent = parent.offsetParent;
        }
        return parent;
    };

    $dom.hasElementLoaded = function hasElementLoaded(el) {
        el = $dom.elArg(el);

        return $dom.elementsLoaded.has(el);
    };

    /**
     * @memberof $dom
     * @param iframe
     * @returns {boolean}
     */
    $dom.hasIframeAccess = function hasIframeAccess(iframe) {
        try {
            return (iframe.contentWindow['access' + $util.random()] = true) === true;
        } catch (ignore) {}

        return false;
    };

    var _emptyEl = document.createElement('div'),
        _matchesFnName = ('matches' in _emptyEl) ? 'matches'
            : ('webkitMatchesSelector' in _emptyEl) ? 'webkitMatchesSelector'
                : ('msMatchesSelector' in _emptyEl) ? 'msMatchesSelector'
                    : 'matches';
    /**
     * Check if the given element matches selector
     * @memberof $dom
     * @param el
     * @param selector
     * @returns {boolean}
     */
    $dom.matches = function matches(el, selector) {
        el = $dom.elArg(el);

        return ((selector === '*') || el[_matchesFnName](selector));
    };

    /**
     * Gets closest parent (or itself) element matching selector
     * @memberof $dom
     * @param el
     * @param selector
     * @param context (exclusive)
     * @returns {*}
     */
    $dom.closest = function closest(el, selector, context) {
        el = $dom.elArg(el);

        while (el && (el !== context)) {
            if ($dom.matches(el, selector)) {
                return el;
            }
            el = el.parentElement;
        }

        return null;
    };

    /**
     * @memberof $dom
     * @param el
     * @param selector
     * @returns { Array }
     */
    $dom.parents = function parents(el, selector) {
        el = $dom.elArg(el);

        var parents = [];

        while ((el = el.parentElement)) {
            if ((selector === undefined) || $dom.matches(el, selector)) {
                parents.push(el);
            }
        }

        return parents;
    };

    /**
     * @memberof $dom
     * @param el
     * @param selector
     * @returns { HTMLElement }
     */
    $dom.parent = function parent(el, selector) {
        el = $dom.elArg(el);

        while ((el = el.parentElement)) {
            if ((selector === undefined) || $dom.matches(el, selector)) {
                return el;
            }
        }

        return null;
    };

    /**
     * @memberof $dom
     * @param el
     * @param selector
     * @returns {*}
     */
    $dom.next = function next(el, selector) {
        el = $dom.elArg(el);

        var sibling = el.nextElementSibling;

        if (selector === undefined) {
            return sibling;
        }

        while (sibling) {
            if ($dom.matches(sibling, selector)) {
                return sibling;
            }

            sibling = sibling.nextElementSibling;
        }

        return null;
    };

    /**
     * @memberof $dom
     * @param el
     * @param selector
     * @returns {*}
     */
    $dom.prev = function prev(el, selector) {
        el = $dom.elArg(el);

        var sibling = el.previousElementSibling;

        if (selector === undefined) {
            return sibling;
        }

        while (sibling) {
            if ($dom.matches(sibling, selector)) {
                return sibling;
            }

            sibling = sibling.previousElementSibling;
        }

        return null;
    };

    /**
     * Find if the first argument contains the second argument (identity does not count).
     *
     * @memberof $dom
     * @param parentNode
     * @param childNode
     * @returns {boolean|*}
     */
    $dom.contains = function contains(parentNode, childNode) {
        parentNode = $dom.nodeArg(parentNode);

        return (parentNode !== childNode) && parentNode.contains(childNode);
    };

    var eventHandlers = {},
        focusEvents = { focus: 'focusin', blur: 'focusout' },
        focusinSupported = 'onfocusin' in window;

    function parseEventName(event) {
        var parts = (String(event)).split('.');
        return { e: parts[0], ns: parts.slice(1).sort().join(' ') };
    }

    function matcherFor(ns) {
        return new RegExp('(?:^| )' + ns.replace(' ', ' .* ?') + '(?: |$)');
    }

    function eventCapture(handler, captureSetting) {
        return (Boolean(handler.del) && (!focusinSupported && (handler.e in focusEvents))) || Boolean(captureSetting);
    }

    function realEvent(type) {
        return (focusinSupported && focusEvents[type]) || type;
    }

    function addEvent(el, events, fn, selector, delegator, capture) {
        var id = $util.uid(el),
            set = eventHandlers[id] || (eventHandlers[id] = []);

        events.split(/\s/).forEach(function (event) {
            var handler = parseEventName(event);
            handler.fn = fn;
            handler.sel = selector;
            handler.del = delegator;
            handler.i = set.length;
            handler.proxy = function proxy(e) {
                var result = (delegator || fn).call(el, e, el);
                if (result === false) {
                    e.stopPropagation();
                    e.preventDefault();
                }
                return result;
            };
            set.push(handler);

            el.addEventListener(realEvent(handler.e), handler.proxy, eventCapture(handler, capture));
        });
    }

    function findHandlers(element, event, fn, selector) {
        var matcher;
        event = parseEventName(event);
        if (event.ns) {
            matcher = matcherFor(event.ns);
        }
        return (eventHandlers[$util.uid(element)] || []).filter(function (handler) {
            return handler &&
                (!event.e || (handler.e === event.e)) &&
                (!event.ns || matcher.test(handler.ns)) &&
                (!fn || ($util.uid(handler.fn) === $util.uid(fn))) &&
                (!selector || (handler.sel === selector));
        });
    }

    function removeEvent(element, events, fn, selector, capture) {
        var id = $util.uid(element);

        (events || '').split(/\s/).forEach(function (event) {
            findHandlers(element, event, fn, selector).forEach(function (handler) {
                delete eventHandlers[id][handler.i];
                element.removeEventListener(realEvent(handler.e), handler.proxy, eventCapture(handler, capture));
            });
        });
    }

    // Enable for debugging only
    // $dom.findHandlers = findHandlers;

    /**
     * Wraps the regular W3C addEventListener, with the ability to mass-add multiple handlers at once, track handlers, and have single handlers.
     * Using W3C addEventListener is okay too.
     * @memberof $dom
     * @param el { Window|Document|Element|string }
     * @param event {string|object}
     * @param [selector] {string|function} (or may be omitted, and callback used here instead)
     * @param [callback] {function}
     * @param [one] {number}
     */
    $dom.on = function on(el, event, selector, callback, one) {
        var autoRemove, delegator;

        el = $dom.domArg(el);

        if (event && (typeof event !== 'string')) {
            $util.each(event, function (type, fn) {
                $dom.on(el, type, selector, fn, one);
            });
            return;
        }

        if ((typeof selector !== 'string') && (typeof callback !== 'function') && (callback !== false)) {
            callback = selector;
            selector = undefined;
        }

        if (callback === false) {
            callback = function () { return false; };
        }

        if (one) {
            autoRemove = function (e) {
                removeEvent(el, e.type, callback);
                return callback.apply(this, arguments);
            };
        }

        if (selector) {
            delegator = function (e) {
                var target = e.target;

                if (!target.nodeType && target.correspondingUseElement) {// SVGElementInstance?
                    // Patch buggy behavior in IE for `event.target`s inside the shadow DOM in SVG <use> elements
                    // https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/7998724/
                    // https://bugs.jquery.com/ticket/13180
                    target = target.correspondingUseElement;
                }

                var match = $dom.closest(target, selector, el);

                if (match == null) {
                    return;
                }

                var args = $util.toArray(arguments);

                args[1] = match; // Set the `element` arg to the matched element

                return (autoRemove || callback).apply(match, args);
            };
        }

        addEvent(el, event, callback, selector, (delegator || autoRemove));
    };

    /**
     * Attach an event to be called just once.
     * @memberof $dom
     * @param { Window|Document|Element|string } el
     * @param {string|object} event
     * @param {string|function} selector
     * @param {function} [callback]
     */
    $dom.one = function one(el, event, selector, callback) {
        el = $dom.domArg(el);

        return $dom.on(el, event, selector, callback, true);
    };

    /**
     * Disable an event.
     * @memberof $dom
     * @param { Window|Document|Element|string } el
     * @param {string|object} event
     * @param {string|function} [selector]
     * @param {function} [callback]
     */
    $dom.off = function off(el, event, selector, callback) {
        el = $dom.domArg(el);

        if (event && (typeof event !== 'string')) {
            $util.each(event, function (type, fn) {
                $dom.off(el, type, selector, fn);
            });
            return;
        }

        if ((typeof selector !== 'string') && (typeof callback !== 'function') && (callback !== false)) {
            callback = selector;
            selector = undefined;
        }

        if (callback === false) {
            callback = function () { return false; };
        }

        removeEvent(el, event, callback, selector);
    };

    var mouseEvents = { click: true, mousedown: true, mouseup: true, mousemove: true };

    /**
     * @memberof $dom
     * @param {string} type
     * @param eventInit
     * @returns { Event }
     */
    $dom.createEvent = function createEvent(type, eventInit) {
        var event = document.createEvent((type in mouseEvents) ? 'MouseEvents' : 'Events'),
            bubbles = true,
            cancelable = true;

        type = strVal(type);

        if (eventInit) {
            for (var key in eventInit) {
                if (key === 'bubbles') {
                    bubbles = Boolean(eventInit.bubbles);
                } else if (key === 'cancelable') {
                    cancelable = Boolean(eventInit.cancelable);
                } else if (key !== 'type') {
                    event[key] = eventInit[key];
                }
            }
        }
        event.initEvent(type, bubbles, cancelable);
        return event;
    };

    /**
     * We use this instead of native JS (createEvent) because it's simple and consistent.
     * @memberof $dom
     * @param el
     * @param event
     * @param [eventInit]
     * @returns {boolean}
     */
    $dom.trigger = function trigger(el, event, eventInit) {
        el = $dom.domArg(el);
        event = $util.isObj(event) ? event : $dom.createEvent(event, eventInit);

        // handle focus(), blur() by calling them directly
        if ((event.type in focusEvents) && (typeof el[event.type] === 'function')) {
            return el[event.type]();
        } else if ((event.type === 'click') && (el.localName === 'input') && (el.type === 'checkbox') && el.click) {
            // For checkbox, fire native event so checked state will be right
            return el.click();
        } else {
            return el.dispatchEvent(event);
        }
    };

    var cancelledSubmitEvents = new WeakSet();
    /**
     * Identifies a form submission will be cancelled, so we can bailout future promises or events, or reach a form.submit()/$dom.trigger(el, 'submit') after promises pass.
     * Wraps preventDefault.
     * @param { Event } submitEvent
     * @param { HTMLButtonElement } buttonToEnable
     */
    $dom.cancelSubmit = function cancelSubmit(submitEvent, buttonToEnable) {
        if (!submitEvent.defaultPrevented) {
            submitEvent.preventDefault();
        }

        cancelledSubmitEvents.add(submitEvent);

        if (buttonToEnable != null) {
            buttonToEnable.disabled = false;
        }
    };
    /**
     * @param { Event } submitEvent
     * @returns {boolean}
     */
    $dom.isCancelledSubmit = function isCancelledSubmit(submitEvent) {
        return cancelledSubmitEvents.has(submitEvent);
    };

    var validationPromisesByEvent = new WeakMap();
    var resolvedValueByPromise = new WeakMap();

    /**
     * @param { Event } submitEvent
     * @param { Promise<Boolean>} validationPromise
     * @param { HTMLButtonElement } buttonToDisable
     * @param { HTMLFormElement } form
     */
    $dom.awaitValidationPromiseAndSubmitForm = function awaitValidationPromiseAndSubmitForm(submitEvent, validationPromise, buttonToDisable, form) {
        var promises = validationPromisesByEvent.get(submitEvent);

        if (promises == null) {
            promises = [];
            validationPromisesByEvent.set(submitEvent, promises);
        }

        promises.push(validationPromise);

        if (buttonToDisable) {
            window.setTimeout(function () {
                buttonToDisable.disabled = true; // Has to be in a timeout else on Chrome it blocks form submissions
            }, 0);
        }

        validationPromise.then(function (resolvedValue) {
            resolvedValueByPromise.set(validationPromise, resolvedValue);
            resubmitIfValidationSuccess(submitEvent, buttonToDisable, form);
        }, function () {
            $dom.cancelSubmit(submitEvent, buttonToDisable);
        });
    };

    function resubmitIfValidationSuccess(submitEvent, buttonToEnable, form) {
        if ($dom.isCancelledSubmit(submitEvent)) {
            return;
        }

        var promises = validationPromisesByEvent.get(submitEvent);

        for (var i = 0, len = promises.length; i < len; i++) {
            if (!resolvedValueByPromise.has(promises[i])) {
                // Promise not resolved yet
                return;
            }

            var resolvedValue = resolvedValueByPromise.get(promises[i]);

            if (!resolvedValue) {
                // Promise resolved with falsy value
                $dom.cancelSubmit(submitEvent, buttonToEnable);
                return;
            }
        }

        // All promises resolved
        if (!form) {
            form = submitEvent.target;
            if ((form.localName !== 'form') && (form.localName !== 'input') && (form.localName !== 'button')) {
                var button = $dom.closest(form, 'input[type="submit"], button[type="submit"]');
                if (button) {
                    button = button.form;
                } else {
                    button = $dom.closest(button, 'form');
                }
                form = button;
            }
        }

        if (form.localName === 'form') {
            form.submit();
        } else {
            $dom.trigger(form, 'click');
        }
    }

    /**
     * @param str
     * @returns {string}
     */
    function camelize(str) {
        return strVal(str).replace(/-+(.)?/g, function (match, chr) {
            return chr ? chr.toUpperCase() : '';
        });
    }

    /**
     * @param str
     * @returns {string}
     */
    function dasherize(str) {
        return strVal(str).replace(/::/g, '/')
            .replace(/([A-Z]+)([A-Z][a-z])/g, '$1_$2')
            .replace(/([a-z\d])([A-Z])/g, '$1_$2')
            .replace(/_/g, '-')
            .toLowerCase();
    }


    // Don't automatically add "px" to these possibly-unitless properties
    var cssNumber = {
        "animationIterationCount": true,
        "columnCount": true,
        "fillOpacity": true,
        "flexGrow": true,
        "flexShrink": true,
        "fontWeight": true,
        "lineHeight": true,
        "opacity": true,
        "order": true,
        "orphans": true,
        "widows": true,
        "zIndex": true,
        "zoom": true
    };
    /**
     * @param name
     * @param value
     * @returns {*}
     */
    function maybeAddPx(name, value) {
        return ((typeof value === 'number') && !(name in cssNumber)) ? (value + 'px') : value;
    }

    // Inspired by jQuery.cssHooks
    function getCss(el, property) {
        var cs = getStyles(el),
            value = cs.getPropertyValue(property) || cs[property]; // getPropertyValue is needed for: '--customProperty'

        if ((property === 'opacity') && (value === '')) {
            // We should always get a number back from opacity
            value = '1';
        }

        if (((property === 'width') || (property === 'height')) && (value === 'auto') && (cs.display === 'none')) { // 'auto' is returned when an element is display none
            var oldPosition = el.style.position,
                oldVisiblity = el.style.visibility,
                oldDisplay = el.style.display;

            el.style.position = 'absolute';
            el.style.visibility = 'hidden';
            el.style.display = $dom.initial(el, 'display');

            el.offsetHeight; // Redraw

            if (property === 'width') {
                value = cs.width;
            } else if (property === 'height') {
                value = cs.height;
            }

            el.style.position = oldPosition;
            el.style.visibility = oldVisiblity;
            el.style.display = oldDisplay;
        }

        return value;
    }

    /**
     * @memberof $dom
     * @param el
     * @param property
     * @param value
     * @returns {*}
     */
    $dom.css = function css(el, property, value) {
        var key;

        el = $dom.elArg(el);

        if (value === undefined) {
            if (typeof property === 'string') {
                return getCss(el, property);
            } else if (Array.isArray(property)) {
                var props = {};
                property.forEach(function (prop) {
                    props[prop] = getCss(el, prop);
                });
                return props;
            }
        }

        var css = '';
        if (typeof property === 'string') {
            if (!value && (value !== 0)) {
                el.style.removeProperty(dasherize(property));
            } else {
                css = dasherize(property) + ':' + maybeAddPx(camelize(property), value);
            }
        } else {
            for (key in property) {
                if (!property[key] && (property[key] !== 0)) {
                    el.style.removeProperty(dasherize(key));
                } else {
                    css += dasherize(key) + ':' + maybeAddPx(camelize(key), property[key]) + ';';
                }
            }
        }

        el.style.cssText += ';' + css;
    };

    /**
     * @memberof $dom
     * @param {Element} el
     * @param {string} property
     * @param {string|Array} values
     * @returns {boolean}
     */
    $dom.isCss = function isCss(el, property, values) {
        el = $dom.elArg(el);
        values = arrVal(values);

        return values.includes($dom.css(el, property));
    };

    /**
     * @memberof $dom
     * @param el
     * @returns {boolean}
     */
    $dom.isDisplayed = function isDisplayed(el) {
        el = $dom.elArg(el);
        return $dom.css(el, 'display') !== 'none';
    };

    var _initial = {};
    /**
     * Gets the 'initial' value for an element type's CSS property as specified by the existing style rules (only 'display' supported as of now)
     * @memberof $dom
     * @param el
     * @param property
     * @returns {*}
     */
    $dom.initial = function initial(el, property) {
        el = $dom.elArg(el);

        var tag = el.localName, doc;

        _initial[tag] || (_initial[tag] = {});

        if (_initial[tag][property] === undefined) {
            doc = el.ownerDocument;

            if (property === 'display') {
                var tmp, display;

                tmp = doc.body.appendChild(doc.createElement(tag));
                display = $dom.css(tmp, 'display');
                tmp.parentNode.removeChild(tmp);
                if (display === 'none') {
                    display = 'block';
                }

                _initial[tag][property] = display;
            }
        }

        return _initial[tag][property];
    };

    /* NB: Following animation code is heavily inspired from jQuery (v3.3.2), so if you're confused, you know where to look. */
    /* Unlike jQuery, this uses the newer Web Animations API */

    // Generate parameters to create a standard animation
    function genFx(type, includeWidth) {
        var attrs = { height: type };

        attrs.marginTop = attrs.marginBottom = attrs.paddingTop = attrs.paddingBottom = type;

        if (includeWidth) {
            attrs.marginLeft = attrs.marginRight = attrs.paddingLeft = attrs.paddingRight = type;
            attrs.opacity = attrs.width = type;
        }

        return attrs;
    }

    function showHide(el, show) {
        var priv, display, newValue;

        priv = privateData(el);

        // Determine new display value for elements that need to change
        display = el.style.display;
        if (show) {
            // Since we force visibility upon cascade-hidden elements, an immediate (and slow)
            // check is required in this first loop unless we have a nonempty display value (either
            // inline or about-to-be-restored)
            if (display === 'none') {
                newValue = priv.display || null;
                if (!newValue) {
                    el.style.display = '';
                }
            }
            if ((el.style.display === '') && !$dom.isDisplayed(el)) {
                newValue = $dom.initial(el, 'display');
            }
        } else {
            if (display !== 'none') {
                newValue = 'none';

                // Remember what we're overwriting
                priv.display = display;
            }
        }

        el.style.display = newValue;

        return el;
    }

    function isHiddenWithinTree(el) {
        // Inline style trumps all
        return el.style.display === "none" ||
            el.style.display === "" &&
            // Otherwise, check computed style
            // Support: Firefox <=43 - 45
            // Disconnected elements can have computed display: none, so first confirm that elem is in the document.
            el.ownerDocument && el.ownerDocument.contains(el) && $dom.css(el, "display") === "none";
    }

    var rfxtypes = /^(?:toggle|show|hide)$/;

    function queueAnimation(el, props, duration, easing, callback) {
        var priv = privateData(el);

        priv.queue || (priv.queue = []);

        var doAnimation = function () {
            var onDone = [],
                onAlways = [],
                empty = !$util.hasEnumerable(props),
                computed = getStyles(el),
                startKeyframe = {},
                endKeyframe = {},
                hidden = isHiddenWithinTree(el),
                isTogglingWidth = (('width' in props) && (props.width === 'toggle'));

            function defaultPrefilter() {
                var isBox = (('width' in props) || ('height' in props)),
                    orig = {},
                    prop, toggle, hasProps, origOverflow, restoreDisplay, display;

                // Detect show/hide animations
                for (prop in props) {
                    var value = props[prop];

                    if (!rfxtypes.test(value)) {
                        continue;
                    }

                    delete props[prop];
                    toggle = toggle || value === "toggle";
                    if (value === (hidden ? "hide" : "show")) {

                        // Pretend to be hidden if this is a "show" and
                        // there is still data from a stopped show/hide
                        if (value === "show" && priv.fxshow && priv.fxshow[ prop ] !== undefined) {
                            hidden = true;

                            // Ignore all other no-op show/hide data
                        } else {
                            continue;
                        }
                    }
                    orig[prop] = priv.fxshow && priv.fxshow[ prop ] || el.style[prop];
                }

                // Bail out if this is a no-op like .hide().hide()
                hasProps = $util.hasEnumerable(props);
                if (!hasProps && !$util.hasEnumerable(orig)) {
                    return;
                }

                // Restrict "overflow" and "display" styles during box animations
                if (isBox) {
                    // Support: IE <=9 - 11, Edge 12 - 13
                    // Record all 3 overflow attributes because IE does not infer the shorthand
                    // from identically-valued overflowX and overflowY
                    origOverflow = [el.style.overflow, el.style.overflowX, el.style.overflowY];
                    // Identify a display type, preferring old show/hide data over the CSS cascade
                    restoreDisplay = priv.fxshow && priv.fxshow.display;
                    if (restoreDisplay == null) {
                        restoreDisplay = priv.display;
                    }
                    display = $dom.css(el, "display");
                    if (display === "none") {
                        if (restoreDisplay) {
                            display = restoreDisplay;
                        } else {
                            // Get nonempty value(s) by temporarily forcing visibility
                            showHide(el, true);
                            restoreDisplay = el.style.display || restoreDisplay;
                            display = $dom.css(el, "display");
                            showHide(el, false);
                        }
                    }

                    // Animate inline elements as inline-block
                    if (display === "inline" || display === "inline-block" && restoreDisplay != null) {
                        if ($dom.css(el, "float") === "none") {

                            // Restore the original display value at the end of pure show/hide animations
                            if (!hasProps) {
                                if (restoreDisplay == null) {
                                    display = el.style.display;
                                    restoreDisplay = display === "none" ? "" : display;
                                }
                            }
                            el.style.display = "inline-block";
                        }
                    }
                }

                if (origOverflow != null) {
                    el.style.overflow = 'hidden';
                    onAlways.push(function () {
                        el.style.overflow = origOverflow[0];
                        el.style.overflowX = origOverflow[1];
                        el.style.overflowY = origOverflow[2];
                    });
                }

                // General show/hide setup for this element animation
                if (!hasProps) {
                    if (priv.fxshow) {
                        if ("hidden" in priv.fxshow) {
                            hidden = priv.fxshow.hidden;
                        }
                    } else {
                        priv.fxshow = { display: restoreDisplay };
                    }

                    // Store hidden/visible for toggle so `.stop().toggle()` "reverses"
                    if (toggle) {
                        priv.fxshow.hidden = !hidden;
                    }

                    // Show elements before animating them
                    if (hidden) {
                        showHide(el, true);
                    }

                    // eslint-disable-next-line no-loop-func
                    onDone.push(function () {
                        // The final step of a "hide" animation is actually hiding the element
                        if (!hidden) {
                            showHide(el);
                        }

                        priv.fxshow = null;

                        for (var prop in orig) {
                            el.style[prop] = orig[prop];
                        }
                    });
                }

                // Implement show/hide animations
                for (prop in orig) {
                    // Per-property setup
                    var fromValue = $dom.css(el, prop),
                        toValue = hidden ? priv.fxshow[prop] : 0;

                    if (toValue === undefined) {
                        priv.fxshow[prop] = fromValue;
                        if (hidden) {
                            toValue = fromValue;
                            fromValue = 0;
                        }
                    }

                    startKeyframe[prop] = fromValue;
                    endKeyframe[prop] = toValue;
                }
            }

            defaultPrefilter();

            for (var property in props) {
                var toValue = props[property];

                startKeyframe[property] = computed[property];
                endKeyframe[property] = toValue;
            }

            if (isTogglingWidth) {
                // Hack to prevent odd wrapping of child elements during width animations (needs accompanying CSS)
                el.classList.add('is-dom-animate-toggling-width');
                el.style.setProperty('--dom-animate-toggle-width', hidden ? endKeyframe.width : startKeyframe.width);

                onAlways.push(function () {
                    el.classList.remove('is-dom-animate-toggling-width');
                    el.style.removeProperty('--dom-animate-toggle-width');
                });
            }

            var keyFrames = [startKeyframe, endKeyframe],
                options = { duration: duration, easing: easing, fill: 'forwards' },
                animation = el.animate(keyFrames, options);

            animation.addEventListener('finish', listener);
            animation.addEventListener('cancel', listener);

            doAnimation.finish = function finish() {
                animation.finish();
            };

            doAnimation.stop = function stop() {
                // Preserve all the progress thus far
                for (var property in startKeyframe) {
                    el.style[property] = $dom.css(el, property);
                }

                animation.cancel(); // Cancel animation (listener() executed)
            };

            // Empty animations, or finishing flag resolves immediately (see $dom.finish())
            if (empty || priv.finish) {
                doAnimation.finish();
            }

            function listener(e) {
                if (e.type === 'finish') {
                    for (var property in endKeyframe) {
                        el.style[property] = endKeyframe[property];
                    }

                    animation.cancel(); // Remove animation fill (doesn't fire cancel event at this stage).

                    onDone.forEach(function (func) {
                        func();
                    });
                }

                onAlways.forEach(function (func) {
                    func();
                });

                priv.queue.shift();

                if (priv.queue.length > 0) {
                    priv.queue[0]();
                }

                doAnimation.callbacks.forEach(function (cb) {
                    cb(e.type === 'finish');
                });
            }
        };

        doAnimation.callbacks = [];

        doAnimation.callbacks.push(callback);

        priv.queue.push(doAnimation);

        if (priv.queue.length === 1) {
            priv.queue[0]();
        }
    }

    $dom.fx || ($dom.fx = {});

    $dom.fx.speeds = {
        slow: 600,
        normal: 400,
        fast: 200,
        // Default speed
        _default: 400
    };

    var DOM_ANIMATE_DEFAULT_EASING = 'ease-in-out'; // Possible values: https://developer.mozilla.org/en-US/docs/Web/API/AnimationEffectTimingProperties/easing

    /**
     * @param { Element|String } el
     * @param { Object } props
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.animate = function animate(el, props, duration, easing) {
        el = $dom.elArg(el);
        duration = $dom.fx.off ? 0 : ((typeof duration === 'string') ? ((duration in $dom.fx.speeds) ? $dom.fx.speeds[duration] : $dom.fx.speeds._default) : intVal(duration, $dom.fx.speeds._default));
        easing = strVal(easing) || DOM_ANIMATE_DEFAULT_EASING;

        return new Promise(function (resolve) {
            queueAnimation(el, props, duration, easing, function (completed) {
                resolve(completed);
            });
        });
    };

    /**
     * Stop the currently-running animation on the matched element.
     *
     * When .stop() is called on an element, the currently-running animation (if any) is immediately stopped.
     * If, for instance, an element is being hidden with .slideUp() when .stop() is called, the element will now still be displayed,
     * but will be a fraction of its previous height. Callback functions are not called.
     * If more than one animation method is called on the same element, the later animations are placed in the effects queue for the element.
     * These animations will not begin until the first one completes. When .stop() is called, the next animation in the queue begins immediately.
     *
     * If the clearQueue parameter is provided with a value of true, then the rest of the animations in the queue are removed and never run.
     *
     * If the jumpToEnd argument is provided with a value of true, the current animation stops, but the element is immediately given its target values for each CSS property.
     * In our above .slideUp() example, the element would be immediately hidden. The callback function is then immediately called, if provided.
     *
     * @param { Element|String } el
     * @param { Boolean } [clearQueue] - Whether to remove queued animation as well.
     * @param { Boolean } [jumpToEnd] - Whether to complete the current animation immediately
     * @returns { Promise }
     */
    $dom.stop = function stop(el, clearQueue, jumpToEnd) {
        el = $dom.elArg(el);

        var priv = privateData(el);

        if (!priv.queue || (priv.queue.length === 0)) {
            return Promise.resolve();
        }

        return new Promise(function (resolve) {
            if (clearQueue) {
                priv.queue.splice(1);
            }

            if (jumpToEnd) {
                priv.queue[0].finish();
            } else {
                priv.queue[0].stop();
            }

            // Wait for the animation to finish
            requestAnimationFrame(function wait() {
                if (priv.queue.length > 0) {
                    requestAnimationFrame(wait);
                    return;
                }

                // All done
                resolve();
            });
        });
    };

    /**
     * Stop the currently-running animation, remove all queued animations, and complete all animations.
     * The .finish() method is similar to .stop(true, true) in that it clears the queue and the current animation jumps to its end value.
     * It differs, however, in that .finish() also causes the CSS property of all queued animations to jump to their end values, as well.
     *
     * @param { Element|String } el
     * @returns { Promise }
     */
    $dom.finish = function finish(el) {
        el = $dom.elArg(el);

        var priv = privateData(el);

        if (!priv.queue || (priv.queue.length === 0)) {
            return Promise.resolve();
        }

        return new Promise(function (resolve) {
            // Enable finishing flag on private data for pending animations to finish instantly
            priv.finish = true;

            priv.queue[0].finish(); // The first animation in the queue is always the one currently running

            // Wait for all animations to finish
            requestAnimationFrame(function wait() {
                if (priv.queue.length > 0) {
                    requestAnimationFrame(wait);
                    return;
                }

                // Turn off finishing flag
                delete priv.finish;

                // All done
                resolve();
            });
        });
    };

    /**
     * Removes all animations that have not been executed from the queue.
     *
     * @param { Element|String } el
     */
    $dom.clearQueue = function clearQueue(el) {
        el = $dom.elArg(el);

        var priv = privateData(el);
        priv.queue && priv.queue.splice(1);
    };

    /**
     * @memberof $dom
     * @param el
     * @param { Number|String }[duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.show = function show(el, duration, easing) {
        el = $dom.elArg(el);

        if (duration != null) {
            return $dom.animate(el, genFx('show', true), duration, easing);
        }

        if (el.style.display === 'none') {
            el.style.removeProperty('display');
        }

        if (getStyles(el).display === 'none') { // Still hidden (with CSS?)
            el.style.display = $dom.initial(el, 'display');
        }

        return Promise.resolve();
    };

    /**
     * @memberof $dom
     * @param el
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.hide = function hide(el, duration, easing) {
        el = $dom.elArg(el);

        if (duration != null) {
            return $dom.animate(el, genFx('hide', true), duration, easing);
        }

        $dom.css(el, 'display', 'none');

        return Promise.resolve();
    };

    /**
     * @memberof $dom
     * @param el
     * @param { Boolean } [show]
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.toggle = function toggle(el, show, duration, easing) {
        el = $dom.elArg(el);
        show = (show !== undefined) ? Boolean(show) : ($dom.css(el, 'display') === 'none');

        if (show) {
            return $dom.show(el, duration, easing);
        } else {
            return $dom.hide(el, duration, easing);
        }
    };

    /**
     * @param { Element } el
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.slideDown = function slideDown(el, duration, easing) {
        return $dom.animate(el, genFx('show'), duration, easing);
    };

    /**
     * @param { Element|String } el
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.slideUp = function slideUp(el, duration, easing) {
        return $dom.animate(el, genFx('hide'), duration, easing);
    };

    /**
     * @param { Element|String } el
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.slideToggle = function slideToggle(el, duration, easing) {
        return $dom.animate(el, genFx('toggle'), duration, easing);
    };

    /**
     * @param { Element } el
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.slideSide = function slideSide(el, duration, easing) {
        var type = 'toggle',
            attrs = {
                width: type,
                marginLeft: type,
                marginRight: type,
                paddingLeft: type,
                paddingRight: type,
                opacity: type,
            };

        return $dom.animate(el, attrs, duration, easing);
    };

    /**
     * @param { Element|String } el
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.fadeIn = function fadeIn(el, duration, easing) {
        return $dom.animate(el, { opacity: 'show' }, duration, easing);
    };

    /**
     * @param { Element|String } el
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.fadeOut = function fadeOut(el, duration, easing) {
        return $dom.animate(el, { opacity: 'hide' }, duration, easing);
    };

    /**
     * @param { Element|String } el
     * @param { Number|String } [duration]
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.fadeToggle = function fadeToggle(el, duration, easing) {
        return $dom.animate(el, { opacity: 'toggle' }, duration, easing);
    };

    /**
     * @param { Element|String } el
     * @param { Number|String } duration
     * @param { Number } opacity
     * @param { String } [easing]
     * @returns { Promise }
     */
    $dom.fadeTo = function fadeTo(el, duration, opacity, easing) {
        if (opacity == null) {
            throw new TypeError('$dom.fadeTo(): Argument `opacity` is required.');
        }

        return $dom.animate(el, { opacity: opacity }, duration, easing);
    };

    /**
     * @memberof $dom
     * @param el
     * @param show
     */
    $dom.toggleWithAria = function toggleWithAria(el, show) {
        el = $dom.elArg(el);
        show = (show !== undefined) ? Boolean(show) : ($dom.css(el, 'display') === 'none');

        if (show) {
            $dom.show(el);
            el.setAttribute('aria-hidden', 'false');
        } else {
            $dom.hide(el);
            el.setAttribute('aria-hidden', 'true');
        }
    };

    /**
     * @memberof $dom
     * @param el
     * @param disabled
     */
    $dom.toggleDisabled = function toggleDisabled(el, disabled) {
        el = $dom.elArg(el);
        disabled = (disabled !== undefined) ? Boolean(disabled) : !el.disabled;

        el.disabled = disabled;
    };

    /**
     * @memberof $dom
     * @param el
     * @param checked
     */
    $dom.toggleChecked = function toggleChecked(el, checked) {
        el = $dom.elArg(el);
        checked = (checked !== undefined) ? Boolean(checked) : !el.checked;

        el.checked = checked;
    };

    /**
     * Animate the loading of an iframe
     * @memberof $dom
     * @param pf
     * @param frame
     * @param leaveGapTop
     * @param leaveHeight
     */
    $dom.animateFrameLoad = function animateFrameLoad(pf, frame, leaveGapTop, leaveHeight) {
        if (!pf) {
            return;
        }

        leaveGapTop = Number(leaveGapTop) || 0;
        leaveHeight = Boolean(leaveHeight);

        if (!leaveHeight) {
            // Enough to stop jumping around
            pf.style.height = window.top.$dom.getWindowHeight() + 'px';
        }

        $dom.illustrateFrameLoad(frame);

        var ifuob = window.top.$dom.$('#iframe-under'),
            extra = ifuob ? ((window !== window.top) ? $dom.findPosY(ifuob) : 0) : 0;

        if (ifuob) {
            ifuob.scrolling = 'no';
        }

        if (window === window.top) {
            window.top.$dom.smoothScroll($dom.findPosY(pf) + extra - leaveGapTop);
        }
    };

    /**
     * @memberof $dom
     * @param iframeId
     */
    $dom.illustrateFrameLoad = function illustrateFrameLoad(iframeId) {
        var iframe = $dom.$id(iframeId), doc;

        if (!$cms.configOption('enable_animations') || !iframe || !iframe.contentDocument || !iframe.contentDocument.documentElement) {
            return;
        }

        iframe.style.minHeight = '80px';

        try {
            doc = iframe.contentDocument;
        } catch (e) {
            // May be connection interference somehow
            iframe.scrolling = 'auto';
            return;
        }

        doc.body.classList.add('website-body', 'main-website-faux');

        $dom.html(doc.body, '<div aria-busy="true" class="spaced"><div class="ajax-loading"><img id="loading-image" class="vertical-alignment" width="20" height="20" src="' + $util.srl('{$IMG*;,loading}') + '" alt="{!LOADING;^}" /> <span class="vertical-alignment">{!LOADING;^}</span></div></div>');

        // Stupid workaround for Google Chrome not loading an image on unload even if in cache
        setTimeout(function () {
            if (!doc.getElementById('loading-image')) {
                return;
            }

            var iDefault = doc.getElementById('loading-image'),
                iNew = doc.createElement('img');

            iNew.src = iDefault.src;
            iNew.className = iDefault.className;
            iNew.alt = iDefault.alt;
            iNew.id = iDefault.id;
            iDefault.parentNode.replaceChild(iNew, iDefault);
        });
    };

    /**
     * Smoothly scroll to another position on the page
     * @memberof $dom
     * @param { Element|number} destY
     * @param [callback]
     * @param [previousTargetScrollY] - Internal only
     * @param [_recursing] - Internal only
     */
    $dom.smoothScroll = function smoothScroll(destY, callback, previousTargetScrollY, _recursing) {
        if ($util.isEl(destY)) {
            destY = $dom.findPosY(destY, true);
        } else if (typeof destY === 'string') {
            destY = $dom.findPosY($dom.$id(destY), true);
        }

        if (!_recursing && document.querySelector('.header.is-sticky')) {
            var stickyHeaderHeight = document.querySelector('.header.is-sticky').offsetHeight;
            if (destY < stickyHeaderHeight) {
                destY -= stickyHeaderHeight;
            }
        }

        if (destY < 0) {
            destY = 0;
        }

        var currentScrollY = window.scrollY;

        if ((!$cms.configOption('enable_animations')) || (destY - currentScrollY > 2000)) {
            // Termination step: Animations not enabled, so just do simplest scroll

            window.setTimeout(function() {
                try {
                    window.scrollTo(0, destY);
                } catch (ignore) {}

                if (callback) {
                    callback();
                }
            }, 25);

            return;
        }

        if (_recursing) {
            if (Math.abs(currentScrollY - previousTargetScrollY) > 2) {
                // Termination step: No scroll happened, maybe outside scrollable bounds like too far to bottom of page, or maybe interrupted by doing their own scrolling

                //$util.log('Terminated smooth scroll due to unexpected scroll position (' + currentScrollY + ' vs ' + previousTargetScrollY + ')');

                if (callback) {
                    callback();
                }

                return;
            }
        }

        var direction = (destY > currentScrollY) ? 1 : -1;

        if (((direction === 1) && (currentScrollY >= destY)) || ((direction === -1) && (currentScrollY <= destY))) {
            // Termination step: SUCCESS!

            //$util.log('Terminated smooth scroll due to reaching target of ' + destY + ' (currently at ' + currentScrollY + ')');

            try {
                window.scrollTo(0, destY);
            } catch (e) {}

            if (callback) {
                callback();
            }

            return;
        }

        var scrollJumpAmount = 5;

        var scrollPixels = direction * scrollJumpAmount;
        var targetScrollY = currentScrollY + scrollPixels;
        if (((direction === 1) && (targetScrollY > destY)) || ((direction === -1) && (targetScrollY < destY))) {
            targetScrollY = destY;
        }

        try {
            window.scrollTo(0, targetScrollY);
        } catch (e) {
            // Termination step: May be stopped by pop-up blocker

            $util.log('Terminated smooth scroll due to pop-up blocker');

            window.setTimeout(function() {
                try {
                    window.scrollTo(0, destY);
                } catch (ignore) {}

                if (callback) {
                    callback();
                }
            }, 25);

            return;
        }

        // Runs at about 60 fps
        requestAnimationFrame(function () {
            $dom.smoothScroll(destY, callback, /*previousTargetScrollY=*/targetScrollY, true);
        });
    };

    /**
     * @memberof $dom
     * @param keyboardEvent
     * @param checkKey
     * @returns {*}
     */
    $dom.keyPressed = function keyPressed(keyboardEvent, checkKey) {
        var key = keyboardEvent.key;

        if (checkKey !== undefined) {
            // Key(s) to check against passed

            if (typeof checkKey === 'number') {
                checkKey = strVal(checkKey);
            }

            if (typeof checkKey === 'string') {
                return key === checkKey;
            }

            if ($util.isRegExp(checkKey)) {
                return key.search(checkKey) !== -1;
            }

            if ($util.isArrayLike(checkKey, 1)) {
                return Array.prototype.includes.call(checkKey, key);
            }

            return false;
        }

        return key;
    };

    /**
     * Returns the output character produced by a KeyboardEvent, or empty string if none
     * @memberof $dom
     * @param keyboardEvent
     * @param checkOutput
     * @returns {*}
     */
    $dom.keyOutput = function keyOutput(keyboardEvent, checkOutput) {
        var key = keyboardEvent.key;

        if ((typeof key !== 'string') || (key.length !== 1)) {
            key = '';
        }

        if (checkOutput !== undefined) {
            // Key output(s) to check against passed
            if (typeof checkOutput === 'string') {
                return key === checkOutput;
            }

            if ($util.isRegExp(checkOutput)) {
                return key.search(checkOutput) !== -1;
            }

            if ($util.isArrayLike(checkOutput, 1)) {
                return Array.prototype.includes.call(checkOutput, key);
            }

            return false;
        }

        return key;
    };

    function setAttr(el, name, value) {
        if (value != null) {
            try {
                el.setAttribute(name, value);
            } catch (e) {}
        } else {
            el.removeAttribute(name);
        }
    }

    /**
     * @memberof $dom
     * @param el
     * @param name
     * @param value
     * @returns {*|string}
     */
    $dom.attr = function attr(el, name, value) {
        var key;

        el = $dom.elArg(el);

        if ((typeof name === 'string') && (value === undefined)) {
            return el.getAttribute(name);
        }

        if ($util.isObj(name)) {
            for (key in name) {
                setAttr(el, key, name[key]);
            }
        } else {
            setAttr(el, name, value);
        }
    };

    /**
     * @memberof $dom
     * @param el
     * @param name
     */
    $dom.removeAttr = function removeAttr(el, name) {
        el = $dom.elArg(el);
        name = strVal(name);

        name.split(' ').forEach(function (attribute) {
            setAttr(el, attribute, null);
        });
    };

    var fragmentRE = /^\s*<(\w+|!)[^>]*>/,
        singleTagRE = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
        tagExpanderRE = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/ig,
        table = document.createElement('table'),
        tableRow = document.createElement('tr'),
        containers = {
            'tr': document.createElement('tbody'),
            'tbody': table, 'thead': table, 'tfoot': table,
            'td': tableRow, 'th': tableRow,
            '*': document.createElement('div')
        };

    // `$dom.fragment` takes an html string and an optional tag name
    // to generate DOM nodes from the given html string.
    // The generated DOM nodes are returned as an array.
    // This function can be overridden in plugins for example to make
    // it compatible with browsers that don't support the DOM fully.
    $dom.fragment = function (html, name, properties) {
        var container, dom, i;

        html = strVal(html);

        // A special case optimization for a single tag
        if (singleTagRE.test(html)) {
            dom = [document.createElement(RegExp.$1)];
        } else {
            html = html.replace(tagExpanderRE, "<$1></$2>");

            if (name === undefined) {
                name = fragmentRE.test(html) && RegExp.$1;
            }
            if (!(name in containers)) {
                name = '*';
            }

            container = containers[name];
            container.innerHTML = strVal(html);
            $util.toArray(container.childNodes).forEach(function (child) {
                if (!$util.isEl(child)) {
                    return;
                }

                // Code below looks for stylesheet and script elements that are theme resources and removes them if already loaded, to avoid duplicate loads.
                // It also ensures any other script elements are actually loaded by cloning them.

                var stylesheetEls = $dom.$$$(child, 'link[rel="stylesheet"]'),
                    scriptEls = $dom.$$$(child, 'script[src]');

                stylesheetEls.forEach(function (stylesheetEl) {
                    if (((stylesheetEl.type === '') || (stylesheetEl.type === 'text/css')) && stylesheetEl.id.startsWith('css-')) {
                        var cssName = stylesheetEl.id.match(/^css-(.*?)(?:_non_minified)?(?:_ssl)?(?:_mobile)?$/);
                        if (cssName && cssName[1] && $cms.hasCss(cssName[1])) {
                            stylesheetEl.remove();
                        }
                    }
                });

                scriptEls.forEach(function (scriptEl) {
                    if (jsTypeRE.test(scriptEl.type) && scriptEl.src && scriptEl.id.startsWith('javascript-')) {
                        var javascriptName = scriptEl.id.match(/^javascript-(.*?)(?:_non_minified)?(?:_ssl)?(?:_mobile)?$/);
                        if (javascriptName && javascriptName[1] && $cms.hasJavascript(javascriptName[1])) {
                            scriptEl.remove();
                        }
                    }
                });
            });

            dom = $util.toArray(container.childNodes);

            for (i = 0; i < dom.length; i++) {
                dom[i].remove();

                // Cloning script[src] elements inserted using innerHTML is required for them to actually be loaded and executed
                if ((dom[i].localName === 'script') && jsTypeRE.test(dom[i].type) && dom[i].src) {
                    dom[i] = cloneScriptEl(dom[i]);
                }
            }
        }

        if ($util.isPlainObj(properties)) {
            $util.each(properties, function (key, value) {
                dom.forEach(function (node) {
                    if (!$util.isEl(node)) {
                        return;
                    }

                    if (methodAttributes[key]) {
                        $dom[key](node, value);
                    } else {
                        $dom.attr(node, key, value);
                    }
                });
            });
        }

        return dom;
    };

    // Generates the `after`, `prepend`, `before` and `append` methods
    function createInsertionFunction(funcName) {
        var inside = (funcName === 'prepend') || (funcName === 'append');

        return function insertionFunction(target, /*...*/args) {// `args` can be nodes, arrays of nodes and HTML strings
            target = $dom.elArg(target);
            args = $util.toArray(arguments, 1);

            var nodes = [],
                newParent = inside ? target : target.parentNode;

            args.forEach(function (arg) {
                if (Array.isArray(arg)) {
                    arg.forEach(function (el) {
                        if (Array.isArray(el)) {
                            nodes = nodes.concat(el);
                        } else if ($util.isNode(el)) {
                            nodes.push(el);
                        } else {
                            // Probably an html string
                            var html = strVal(el);
                            nodes = nodes.concat($dom.fragment(html));
                        }
                    });
                } else if ($util.isNode(arg)) {
                    nodes.push(arg);
                } else {
                    // Probably an html string
                    var html = strVal(arg);
                    nodes = nodes.concat($dom.fragment(html));
                }
            });

            if (nodes.length < 1) {
                return Promise.resolve();
            }

            // convert all methods to a "before" operation
            target = funcName === 'after' ? target.nextSibling :
                funcName === 'prepend' ? target.firstChild :
                    funcName === 'before' ? target :
                        null;

            var parentInDocument = $dom.contains(document.documentElement, newParent),
                scriptEls = [];

            nodes.forEach(function (node) {
                if (!$util.isNode(node)) {
                    return;
                }

                if (!newParent) {
                    node.remove();
                    return;
                }
                // Insert the node
                newParent.insertBefore(node, target);

                if (!$util.isEl(node)) {
                    return;
                }

                if (parentInDocument) {
                    var tmp = $dom.$$$(node, 'script');
                    if (tmp.length > 0) {
                        scriptEls = scriptEls.concat(tmp);
                    }
                }
            });

            if (scriptEls.length > 0) {
                return new Promise(function (resolve) {
                    $dom.waitForResources(scriptEls).then(function () {
                        // Patch stupid DOM behavior when dynamically inserting inline script elements
                        scriptEls.forEach(function (el) {
                            if (!el.src && jsTypeRE.test(el.type)) {
                                var win = el.ownerDocument ? el.ownerDocument.defaultView : window;
                                (function () {
                                    // Clone to a new script element
                                    var newScript = document.createElement('script');
                                    Array.from(el.attributes).forEach(function (attr) {
                                        newScript.setAttribute(attr.name, attr.value);
                                    });
                                    newScript.textContent = el.innerHTML;

                                    if (el.parentNode) {
                                        el.parentNode.replaceChild(newScript, el);
                                    }
                                }).call(win); // Set `this` context for eval
                            }
                        });

                        nodes.forEach(function (node) {
                            if ($util.isEl(node)) {
                                $cms.attachBehaviors(node);
                            }
                        });

                        resolve();
                    });
                });
            }

            return new Promise(function (resolve) {
                setTimeout(function () {
                    nodes.forEach(function (node) {
                        if ($util.isEl(node)) {
                            $cms.attachBehaviors(node);
                        }
                    });

                    resolve();
                }, 0);
            });
        };
    }

    function cloneScriptEl(scriptEl) {
        scriptEl = $dom.elArg(scriptEl);

        var clone = document.createElement('script');

        if (scriptEl.id) {
            clone.id = scriptEl.id;
        }

        if (scriptEl.className) {
            clone.className = scriptEl.className;
        }

        if (scriptEl.dataset) {
            for (var key in scriptEl.dataset) {
                clone.dataset[key] = scriptEl.dataset[key];
            }
        }

        clone.defer = scriptEl.defer;
        clone.async = scriptEl.async;
        if (scriptEl.src !== '') {
            clone.src = scriptEl.src;
        }

        return clone;
    }

    /**
     * @memberof $dom
     * @method
     * @param el
     * @param html
     * @returns { Promise }
     */
    $dom.after = createInsertionFunction('after');

    /**
     * @memberof $dom
     * @method
     * @param el
     * @param html
     * @returns { Promise }
     */
    $dom.prepend = createInsertionFunction('prepend');

    /**
     * @memberof $dom
     * @method
     * @param el
     * @param html
     * @returns { Promise }
     */
    $dom.before = createInsertionFunction('before');

    /**
     * @memberof $dom
     * @method append
     * @param el
     * @param html
     * @returns { Promise }
     */
    $dom.append = createInsertionFunction('append');

    /**
     * @memberof $dom
     * @param el
     */
    $dom.empty = function empty(el) {
        el = $dom.elArg(el);

        $util.toArray(el.children).forEach(function (child) {
            $cms.detachBehaviors(child);
        });

        el.innerHTML = '';
    };

    /**
     * @memberof $dom
     * @param el
     * @param html
     * @returns {string|Promise}
     */
    $dom.html = function html(el, html) {
        el = $dom.elArg(el);

        if (html === undefined) {
            return el.innerHTML;
        }

        $dom.empty(el);
        return $dom.append(el, html);
    };

    /**
     * @memberof $dom
     * @param el
     * @param {string|Node|Array} html
     * @returns { Promise }
     */
    $dom.replaceWith = function replaceWith(el, html) {
        el = $dom.elArg(el);

        var promise = $dom.before(el, html);
        $dom.remove(el);
        return promise;
    };

    /**
     * @memberof $dom
     * @param node
     */
    $dom.remove = function remove(node) {
        node = $dom.nodeArg(node);

        if (node.parentNode != null) {
            node.parentNode.removeChild(node);
        }
    };

    /**
     * Returns the provided element's width excluding padding and borders.
     * Also see $dom.width.
     * @memberof $dom
     * @param el
     * @returns {number}
     */
    $dom.contentWidth = function contentWidth(el) {
        el = $dom.elArg(el);

        var cs = getStyles(el),
            padding = parseFloat(cs.paddingLeft) + parseFloat(cs.paddingRight),
            border = parseFloat(cs.borderLeftWidth) + parseFloat(cs.borderRightWidth);

        return el.offsetWidth - padding - border;
    };

    /**
     * Returns the provided element's height excluding padding and border.
     * Also see $dom.height.
     * @memberof $dom
     * @param el
     * @returns {number}
     */
    $dom.contentHeight = function contentHeight(el) {
        el = $dom.elArg(el);

        var cs = getStyles(el),
            padding = parseFloat(cs.paddingTop) + parseFloat(cs.paddingBottom),
            border = parseFloat(cs.borderTopWidth) + parseFloat(cs.borderBottomWidth);

        return el.offsetHeight - padding - border;
    };

    var serializeExcludedTypes = { submit: 1, reset: 1, button: 1, file: 1 };
    /**
     * @memberof $dom
     * @param form
     * @returns {Array}
     */
    $dom.serializeArray = function serializeArray(form) {
        var name, result = [];

        form = $dom.elArg(form);

        $util.toArray(form.elements).forEach(function (field) {
            name = field.name;
            if (name && (field.localName !== 'fieldset') && !field.disabled && !(field.type in serializeExcludedTypes) && (!['radio', 'checkbox'].includes(field.type) || field.checked)) {
                add($dom.value(field));
            }
        });

        function add(value) {
            if (Array.isArray(value)) {
                return value.forEach(add);
            }
            result.push({name: name, value: value});
        }

        return result;
    };

    /**
     * @memberof $dom
     * @param form
     * @returns {string}
     */
    $dom.serialize = function serialize(form) {
        var result = [];

        form = $dom.elArg(form);

        $dom.serializeArray(form).forEach(function (el) {
            result.push(encodeURIComponent(el.name) + '=' + encodeURIComponent(el.value));
        });
        return result.join('&');
    };

    /**
     * Automatic resizing to make frames seamless. The software calls this automatically. Make sure id&name attributes are defined on your iframes!
     * @memberof $dom
     * @deprecated
     * @param name
     * @param minHeight
     */
    $dom.resizeFrame = function resizeFrame(name, minHeight) {
        minHeight = Number(minHeight) || 0;

        var frameElement = $dom.$id(name),
            frameWindow = frameElement ? frameElement.contentWindow : null;

        if (frameElement && frameWindow && frameWindow.document && frameWindow.document.body) {
            var h = $dom.getWindowScrollHeight(frameWindow);

            if ((h === 0) && (frameElement.parentElement.style.display === 'none')) {
                h = minHeight ? minHeight : 100;

                if (frameWindow.parent) {
                    setTimeout(function () {
                        if (frameWindow.parent) {
                            frameWindow.parent.$dom.triggerResize();
                        }
                    }, 0);
                }
            }

            if ((h + 'px') !== frameElement.style.height) {
                if ((frameElement.scrolling !== 'auto' && frameElement.scrolling !== 'yes') || (frameElement.style.height === '0px')) {
                    frameElement.style.height = ((h >= minHeight) ? h : minHeight) + 'px';
                    if (frameWindow.parent) {
                        setTimeout(function () {
                            if (frameWindow.parent) {
                                frameWindow.parent.$dom.triggerResize();
                            }
                        });
                    }
                    frameElement.scrolling = 'no';
                    frameWindow.onscroll = function (event) {
                        if (event == null) {
                            return false;
                        }
                        try {
                            frameWindow.scrollTo(0, 0);
                        } catch (ignore) {}
                    };
                }
            }
        }
    };

    /**
     * @memberof $dom
     * @deprecated
     * @param andSubframes
     */
    $dom.triggerResize = function triggerResize(andSubframes) {
        andSubframes = Boolean(andSubframes);

        if (!window.parent || !window.parent.document) {
            return;
        }
        var i, iframes;

        if (window.parent !== window) {
            iframes = window.parent.document.querySelectorAll('iframe');
            for (i = 0; i < iframes.length; i++) {
                if ((iframes[i].src === window.location.href) || (iframes[i].contentWindow === window) || ((iframes[i].id !== '') && (window.parent.frames[iframes[i].id] === window))) {
                    if (iframes[i].style.height === '900px') {
                        iframes[i].style.height = 'auto';
                    }
                    window.parent.$dom.resizeFrame(iframes[i].name);
                }
            }
        }

        if (andSubframes) {
            iframes = document.querySelectorAll('iframe');
            for (i = 0; i < iframes.length; i++) {
                if ((iframes[i].name !== '') && ((iframes[i].classList.contains('expandable-iframe')) || (iframes[i].classList.contains('dynamic-iframe')))) {
                    $dom.resizeFrame(iframes[i].name);
                }
            }
        }
    };
}(window.$cms || (window.$cms = {}), window.$util, window.$dom));
