/* This file contains software-specific utility functions */

(function ($cms, $util, $dom) {
    'use strict';

    /** @namespace $cms */
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.inMinikernelVersion = $util.constant(document.documentElement.classList.contains('in-minikernel-version'));

    var symbols = (!$cms.inMinikernelVersion() ? JSON.parse(document.getElementById('cms-symbol-data').content) : {});
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isGuest = $util.constant(boolVal(symbols.IS_GUEST));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isStaff = $util.constant(boolVal(symbols.IS_STAFF));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isAdmin = $util.constant(boolVal(symbols.IS_ADMIN));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isHttpauthLogin = $util.constant(boolVal(symbols.IS_HTTPAUTH_LOGIN));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isACookieLogin = $util.constant(boolVal(symbols.IS_A_COOKIE_LOGIN));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isDevMode = $util.constant($cms.inMinikernelVersion() || boolVal(symbols.DEV_MODE));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isJsOn = $util.constant(boolVal(symbols.JS_ON));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isMobile = $util.constant(boolVal(symbols.MOBILE));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.isForcePreviews = $util.constant(boolVal(symbols.FORCE_PREVIEWS));
    /**
     * @memberof $cms
     * @method
     * @returns {number}
     */
    $cms.httpStatusCode = $util.constant(Number(symbols.HTTP_STATUS_CODE));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getPageName = $util.constant(strVal(symbols.PAGE));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getZoneName = $util.constant(strVal(symbols.ZONE));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getMember = $util.constant(strVal(symbols.MEMBER));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getUsername = $util.constant(strVal(symbols.USERNAME));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getTheme = $util.constant(strVal(symbols.THEME));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.userLang = $util.constant(strVal(symbols.LANG));
    /**
     * Find the active ISO country for the current user.
     * @memberof $cms
     * @method
     * @returns {string|null} The active region (null: none found, unfiltered)
     */
    $cms.getCountry = $util.constant((symbols['COUNTRY'] != null) ? strVal(symbols['COUNTRY']) : null);
    /**
     * Get URL stub to propagate keep_* parameters
     * @memberof $cms
     * @param [starting]
     * @return {string}
     */
    $cms.keep = function keep(starting) {
        var keep = $cms.pageKeepSearchParams().toString();

        if (keep === '') {
            return '';
        }

        return (starting ? '?' : '&') + keep;
    };
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getPreviewUrl = function getPreviewUrl() {
        var value = '{$FIND_SCRIPT_NOHTTP;,preview}';
        value += '?page=' + urlencode($cms.getPageName());
        value += '&type=' + urlencode(symbols['page_type']);
        return value;
    };
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getSiteName = $util.constant(strVal('{$SITE_NAME;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getBaseUrl = $util.constant(strVal('{$BASE_URL;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getCustomBaseUrl = $util.constant(strVal('{$CUSTOM_BASE_URL;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getForumBaseUrl = $util.constant(strVal('{$FORUM_BASE_URL;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.brandName = $util.constant(strVal('{$BRAND_NAME;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getBrandBaseUrl = $util.constant(strVal('{$BRAND_BASE_URL;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getSessionCookie = $util.constant(strVal('{$SESSION_COOKIE_NAME;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getCookiePath = $util.constant(strVal('{$COOKIE_PATH;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getCookieDomain = $util.constant(strVal('{$COOKIE_DOMAIN;}'));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.runningScript = $util.constant(strVal(symbols.RUNNING_SCRIPT));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.getCspNonce = $util.constant(strVal(symbols.CSP_NONCE));

    var configOptionsJson = JSON.parse('{$PUBLIC_CONFIG_OPTIONS_JSON;}');
    /**
     * WARNING: This is a limited subset of the $CONFIG_OPTION tempcode symbol
     * @memberof $cms
     * @method
     * @param {string} optionName
     * @returns {boolean|string|number}
     */
    $cms.configOption = function configOption(optionName) {
        if ($cms.inMinikernelVersion()) {
            // Installer, likely executing global.js
            return '';
        }

        if ($util.hasOwn(configOptionsJson, optionName)) {
            return configOptionsJson[optionName];
        }

        $util.fatal('$cms.configOption(): Option "' + optionName + '" is either unsupported in JS or doesn\'t exist. Please try using the actual Tempcode symbol.');
    };
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.seesJavascriptErrorAlerts = $util.constant(boolVal(symbols['sees_javascript_error_alerts']));
    /**
     * @memberof $cms
     * @method
     * @returns {boolean}
     */
    $cms.canTryUrlSchemes = $util.constant(boolVal(symbols['can_try_url_schemes']));
    /**
     * @memberof $cms
     * @method
     * @returns {string}
     */
    $cms.zoneDefaultPage = $util.constant(strVal(symbols['zone_default_page']));
    /**
     * @memberof $cms
     * @method
     * @returns {object}
     */
    $cms.staffTooltipsUrlPatterns = $util.constant(objVal(JSON.parse('{$STAFF_TOOLTIPS_URL_PATTERNS_JSON;}')));
    /**
     * @memberof $cms
     * @method
     * @returns {integer}
     */
    $cms.pageGenerationTimestamp = Date.now();

    /**
     * Addons can add functions under this namespace
     * @namespace $cms.functions
     */
    $cms.functions = {};

    var mobileModeMql = window.matchMedia('(max-width: 982.98px)'),
        desktopModeMql = window.matchMedia('(min-width: 983px)');
    /**
     * Refer to $CSS_MODE calls in global.css
     *
     * @param {string} modeName
     * @return {boolean}
     */
    $cms.isCssMode = function (modeName) {
        modeName = strVal(modeName);

        switch (modeName) {
            case 'mobile':
                return $cms.isMobile() || mobileModeMql.matches;
            case 'desktop':
                return !$cms.isMobile() && desktopModeMql.matches;
        }

        return false;
    };

    /**
     * Returns a { URL } instance for the current page
     * @see https://developer.mozilla.org/en-US/docs/Web/API/URL
     * @memberof $cms
     * @return { URL }
     */
    $cms.pageUrl = function pageUrl() {
        return new URL(window.location);
    };

    /**
     * @memberof $cms
     * @return { URLSearchParams }
     */
    $cms.pageKeepSearchParams = function pageKeepSearchParams() {
        var keepSp = new window.URLSearchParams();

        $util.iterableToArray($cms.pageUrl().searchParams.entries()).forEach(function (entry) {
            var name = entry[0],
                value = entry[1];

            if (name.startsWith('keep_')) {
                keepSp.set(name, value);
            }
        });

        return keepSp;
    };

    var validIdRE = /^[a-zA-Z][\w:.-]*$/;

    /**
     * @private
     * @param sheetNameOrHref
     */
    function _requireCss(sheetNameOrHref) {
        var sheetName, sheetHref, sheetEl;

        if (validIdRE.test(sheetNameOrHref)) {
            sheetName = sheetNameOrHref;
            sheetHref = $util.srl('{$FIND_SCRIPT_NOHTTP;,sheet}?sheet=' + sheetName + $cms.keep());
        } else {
            sheetHref = sheetNameOrHref;
        }

        if (sheetName != null) {
            sheetEl = _findCssByName(sheetName);
        }

        if (sheetEl == null) {
            sheetEl = _findCssByHref(sheetHref);
        }

        if (sheetEl == null) {
            sheetEl = document.createElement('link');
            if (sheetName != null) {
                sheetEl.id = 'css-' + sheetName;
            }
            sheetEl.rel = 'stylesheet';
            sheetEl.nonce = $cms.getCspNonce();
            sheetEl.href = sheetHref;
            document.head.appendChild(sheetEl);
        }

        return $dom.waitForResources(sheetEl);
    }

    function _findCssByName(stylesheetName) {
        stylesheetName = strVal(stylesheetName);

        var els = $dom.$$('link[id^="css-' + stylesheetName + '"]'), scriptEl;

        for (var i = 0; i < els.length; i++) {
            scriptEl = els[i];
            if ((new RegExp('^css-' + stylesheetName + '(?:_non_minified)?(?:_ssl)?(?:_mobile)?$', 'i')).test(scriptEl.id)) {
                return scriptEl;
            }
        }

        return null;
    }

    function _findCssByHref(href) {
        var els = $dom.$$('link[rel="stylesheet"][href]'), el;

        href = $util.srl(href);

        for (var i = 0; i < els.length; i++) {
            el = els[i];
            if ($util.srl(el.href) === href) {
                return el;
            }
        }

        return null;
    }

    /**
     * @memberof $cms
     * @param sheetNames
     * @returns { Promise }
     */
    $cms.requireCss = function requireCss(sheetNames) {
        sheetNames = arrVal(sheetNames);

        return Promise.all(sheetNames.map(_requireCss));
    };

    $cms.hasCss = function hasCss(sheetNameOrHref) {
        return (validIdRE.test(sheetNameOrHref) ? _findCssByName(sheetNameOrHref) : _findCssByHref(sheetNameOrHref)) != null;
    };

    /**
     * @private
     * @param scriptNameOrSrc
     * @returns { Promise }
     */
    function _requireJavascript(scriptNameOrSrc) {
        scriptNameOrSrc = strVal(scriptNameOrSrc);

        var scriptName, scriptSrc, scriptEl;

        if (validIdRE.test(scriptNameOrSrc)) {
            scriptName = scriptNameOrSrc;
            scriptSrc = $util.srl('{$FIND_SCRIPT_NOHTTP;,script}?script=' + scriptName + $cms.keep());
        } else {
            scriptSrc = scriptNameOrSrc;
        }

        if (scriptName != null) {
            scriptEl = _findScriptByName(scriptName);
        }

        if (scriptEl == null) {
            scriptEl = _findScriptBySrc(scriptSrc);
        }

        if (scriptEl == null) {
            scriptEl = document.createElement('script');
            scriptEl.defer = true;
            if (scriptName != null) {
                scriptEl.id = 'javascript-' + scriptName;
            }
            scriptEl.nonce = $cms.getCspNonce();
            scriptEl.src = scriptSrc;
            document.body.appendChild(scriptEl);
        }

        return $dom.waitForResources(scriptEl);
    }

    function _findScriptByName(scriptName) {
        scriptName = strVal(scriptName);

        var els = $dom.$$('script[id^="javascript-' + scriptName + '"]'), el;

        for (var i = 0; i < els.length; i++) {
            el = els[i];
            if ((new RegExp('^javascript-' + scriptName + '(?:_non_minified)?(?:_ssl)?(?:_mobile)?$', 'i')).test(el.id)) {
                return el;
            }
        }

        return null;
    }

    function _findScriptBySrc(src) {
        var els = $dom.$$('script[src]'), el;

        src = $util.srl(src);

        for (var i = 0; i < els.length; i++) {
            el = els[i];
            if ($util.srl(el.src) === src) {
                return el;
            }
        }

        return null;
    }

    /**
     * @memberof $cms
     * @param scripts
     * @returns { Promise }
     */
    $cms.requireJavascript = function requireJavascript(scripts) {
        var calls = [];

        scripts = arrVal(scripts);

        scripts.forEach(function (script) {
            calls.push(function () {
                return _requireJavascript(script);
            });
        });

        return $util.promiseSequence(calls);
    };

    $cms.hasJavascript = function hasJavascript(scriptNameOrSrc) {
        return (validIdRE.test(scriptNameOrSrc) ? _findScriptByName(scriptNameOrSrc) : _findScriptBySrc(scriptNameOrSrc)) != null;
    };

    /**
     * @memberof $cms
     * @param flag
     */
    $cms.setPostDataFlag = function setPostDataFlag(flag) {
        flag = strVal(flag);

        var forms = $dom.$$('form'),
            form, postData;

        for (var i = 0; i < forms.length; i++) {
            form = forms[i];

            if (form.elements['post_data'] == null) {
                postData = document.createElement('input');
                postData.type = 'hidden';
                postData.name = 'post_data';
                postData.value = '';
                form.appendChild(postData);
            } else {
                postData = form.elements['post_data'];
                if (postData.value !== '') {
                    postData.value += ',';
                }
            }

            postData.value += flag;
        }
    };

    /**
     * @memberof $cms
     * @return {string}
     */
    $cms.getCsrfToken = function getCsrfToken() {
        return new Promise(function (resolve) {
            $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,generate_csrf_token}' + $cms.keep(true)).then(function (xhr) {
                resolve(xhr.responseText);
            });
        });
    };

    /* Cookies */

    var alertedCookieConflict = false;
    /**
     * @memberof $cms
     * @param cookieName
     * @param cookieValue
     * @param numDays
     */
    $cms.setCookie = function setCookie(cookieName, cookieValue, numDays) {
        var expires = new Date(),
            output;

        cookieName = strVal(cookieName);
        cookieValue = strVal(cookieValue);
        numDays = Number(numDays) || 1;

        expires.setDate(expires.getDate() + numDays); // Add days to date

        output = cookieName + '=' + encodeURIComponent(cookieValue) + ';expires=' + expires.toUTCString();

        if ($cms.getCookiePath() !== '') {
            output += ';path=' + $cms.getCookiePath();
        }

        if ($cms.getCookieDomain() !== '') {
            output += ';domain=' + $cms.getCookieDomain();
        }

        document.cookie = output;

        var read = $cms.readCookie(cookieName);

        if (read && (read !== cookieValue) && $cms.isDevMode() && !alertedCookieConflict) {
            $cms.ui.alert('{!COOKIE_CONFLICT_DELETE_COOKIES;^}' + '... ' + document.cookie + ' (' + output + ')', '{!ERROR_OCCURRED;^}');
            alertedCookieConflict = true;
        }
    };

    /**
     * @memberof $cms
     * @param cookieName
     * @param defaultValue
     * @returns {string}
     */
    $cms.readCookie = function readCookie(cookieName, defaultValue) {
        cookieName = strVal(cookieName);
        defaultValue = strVal(defaultValue);

        var cookies = String(document.cookie),
            startIdx = cookies.startsWith(cookieName + '=') ? 0 : cookies.indexOf(' ' + cookieName + '=');

        if ((startIdx === -1) || !cookieName) {
            return defaultValue;
        }

        if (startIdx > 0) {
            startIdx++;
        }

        var endIdx = cookies.indexOf(';', startIdx);
        if (endIdx === -1) {
            endIdx = cookies.length;
        }

        return decodeURIComponent(cookies.substring(startIdx + cookieName.length + 1, endIdx));
    };

    /**
     * @return {string[]}
     */
    function behaviorNamesByPriority() {
        var name, behavior, priority, priorities, i,
            byPriority = {},
            names = [];

        for (name in $cms.behaviors) {
            behavior = $cms.behaviors[name];
            priority = Number(behavior.priority) || 0;

            byPriority[priority] || (byPriority[priority] = []);
            byPriority[priority].push(name);
        }

        priorities = Object.keys(byPriority);
        priorities.sort(function (a, b) {
            // Numerical descending sort
            return b - a;
        });

        for (i = 0; i < priorities.length; i++) {
            priority = priorities[i];
            names = names.concat(byPriority[priority]);
        }

        return names;
    }

    /**
     * @memberof $cms
     * @param context
     */
    $cms.attachBehaviors = function attachBehaviors(context) {
        if (!$util.isDoc(context) && !$util.isEl(context)) {
            throw new TypeError('Invalid argument type: `context` must be of type HTMLDocument or HTMLElement');
        }

        //$dom.waitForResources($dom.$$$(context, 'script[src]')).then(function () { // Wait for <script> dependencies to load
        // Execute all of them.
        var names = behaviorNamesByPriority();

        _attach(0);

        function _attach(i) {
            var name = names[i], ret;

            if ($util.isObj($cms.behaviors[name]) && (typeof $cms.behaviors[name].attach === 'function')) {
                try {
                    ret = $cms.behaviors[name].attach(context);
                    //$util.inform('$cms.attachBehaviors(): attached behavior "' + name + '" to context', context);
                } catch (e) {
                    $util.fatal('$cms.attachBehaviors(): Error while attaching behavior "' + name + '"  to', context, '\n', e);
                }
            }

            ++i;

            if (names.length <= i) {
                return;
            }

            if ($util.isPromise(ret)) { // If the behavior returns a promise, we wait for it before moving on
                ret.then(_attach.bind(undefined, i), _attach.bind(undefined, i));
            } else { // no promise!
                _attach(i);
            }
        }

        //});

        return Promise.all([]);
    };

    /**
     * @memberof $cms
     * @param context
     * @param trigger
     */
    $cms.detachBehaviors = function detachBehaviors(context, trigger) {
        var name;

        if (!$util.isDoc(context) && !$util.isEl(context)) {
            throw new TypeError('Invalid argument type: `context` must be of type HTMLDocument or HTMLElement');
        }

        trigger || (trigger = 'unload');

        // Detach all of them.
        for (name in $cms.behaviors) {
            if ($util.isObj($cms.behaviors[name]) && (typeof $cms.behaviors[name].detach === 'function')) {
                try {
                    $cms.behaviors[name].detach(context, trigger);
                    //$util.inform('$cms.detachBehaviors(): detached behavior "' + name + '" from context', context);
                } catch (e) {
                    $util.fatal('$cms.detachBehaviors(): Error while detaching behavior \'' + name + '\' from', context, '\n', e);
                }
            }
        }

        return Promise.all([]);
    };

    var _blockDataCache = {};

    /**
     * This function will load a block, with options for parameter changes, and render the results in specified way - with optional callback support
     * @memberof $cms
     * @param url
     * @param newBlockParams
     * @param targetDiv
     * @param append
     * @param scrollToTopOfWrapper
     * @param postParams
     * @param inner
     * @param showLoadingAnimation
     * @returns { Promise }
     */
    $cms.callBlock = function callBlock(url, newBlockParams, targetDiv, append, scrollToTopOfWrapper, postParams, inner, showLoadingAnimation) {
        url = strVal(url);
        newBlockParams = strVal(newBlockParams);
        scrollToTopOfWrapper = Boolean(scrollToTopOfWrapper);
        postParams = (postParams != null) ? strVal(postParams) : null;
        inner = Boolean(inner);
        showLoadingAnimation = (showLoadingAnimation != null) ? Boolean(showLoadingAnimation) : true;

        if ((_blockDataCache[url] === undefined) && (newBlockParams !== '')) {
            // Cache start position. For this to be useful we must be smart enough to pass blank newBlockParams if returning to fresh state
            _blockDataCache[url] = $dom.html(targetDiv);
        }

        var ajaxUrl = url;
        if (newBlockParams !== '') {
            ajaxUrl += '&block_map_sup=' + encodeURIComponent(newBlockParams);
        }

        ajaxUrl += '&utheme=' + $cms.getTheme();
        if ((_blockDataCache[ajaxUrl] !== undefined) && (postParams == null)) {
            // Show results from cache
            showBlockHtml(_blockDataCache[ajaxUrl], targetDiv, append, inner);
            return Promise.resolve();
        }

        var loadingWrapper = targetDiv;
        if (!loadingWrapper.id.includes('carousel-') && !$dom.html(loadingWrapper).includes('ajax-loading-block') && showLoadingAnimation) {
            document.body.style.cursor = 'wait';
        }

        return new Promise(function (resolvePromise) {
            // Make AJAX call
            $cms.doAjaxRequest(ajaxUrl + $cms.keep(), null, postParams).then(function (xhr) { // Show results when available
                if (!targetDiv.parentNode) {
                    return; // A prior AJAX result came in and did a set_outer_html, wiping the container
                }

                callBlockRender(xhr, ajaxUrl, targetDiv, append, function () {
                    resolvePromise();
                }, scrollToTopOfWrapper, inner);
            });
        });

        function callBlockRender(rawAjaxResult, ajaxUrl, targetDiv, append, callback, scrollToTopOfWrapper, inner) {
            var newHtml = rawAjaxResult.responseText;
            _blockDataCache[ajaxUrl] = newHtml;

            // Remove loading animation if there is one
            var ajaxLoading = targetDiv.querySelector('.ajax-loading-block');
            if (ajaxLoading) {
                $dom.remove(ajaxLoading.parentNode);
            }

            document.body.style.cursor = '';

            // Put in HTML
            showBlockHtml(newHtml, targetDiv, append, inner);

            // Scroll up if required
            if (scrollToTopOfWrapper) {
                window.setTimeout(function() {
                    try {
                        window.scrollTo(0, $dom.findPosY(targetDiv));
                    } catch (e) {
                        // continue
                    }
                }, 25);
            }

            // Defined callback
            if (callback != null) {
                callback();
            }
        }

        function showBlockHtml(newHtml, targetDiv, append, inner) {
            var rawAjaxGrowSpot = targetDiv.querySelector('.raw-ajax-grow-spot');
            if ((rawAjaxGrowSpot != null) && append) { // If we actually are embedding new results a bit deeper
                targetDiv = rawAjaxGrowSpot;
            }
            if (append) {
                $dom.append(targetDiv, newHtml);
            } else {
                if (inner) {
                    $dom.html(targetDiv, newHtml);
                } else {
                    $dom.replaceWith(targetDiv, newHtml);
                }
            }
        }
    };

    /**
     * Dynamic inclusion
     * @memberof $cms
     * @param snippetHook
     * @param [post]
     * @returns { Promise }
     */
    $cms.loadSnippet = function loadSnippet(snippetHook, post) {
        snippetHook = strVal(snippetHook);

        var title = $dom.html(document.querySelector('title')).replace(/ \u2013 .*/, ''),
            canonical = document.querySelector('link[rel="canonical"]'),
            url = canonical ? canonical.href : window.location.href,
            url2 = '{$FIND_SCRIPT_NOHTTP;,snippet}?snippet=' + snippetHook + '&url=' + encodeURIComponent($cms.protectURLParameter(url)) + '&title=' + encodeURIComponent(title) + $cms.keep();

        return new Promise(function (resolve) {
            $cms.doAjaxRequest($util.rel($cms.maintainThemeInLink(url2)), null, post).then(function (xhr) {
                resolve(xhr.responseText);
            });
        });
    };

    /**
     * Update a URL to maintain the current theme into it, always returns an absolute URL
     * @memberof $cms
     * @param url
     * @returns {string}
     */
    $cms.maintainThemeInLink = function maintainThemeInLink(url) {
        url = $util.url(url);

        if (!url.searchParams.has('utheme') && !url.searchParams.has('keep_theme')) {
            url.searchParams.set('utheme', $cms.getTheme());
        }

        return url.toString();
    };

    /**
     * Alternative to $cms.keep(), accepts a URL and ensures not to cause duplicate keep_* params
     * @memberof $cms
     * @param url
     * @return {string}
     */
    $cms.addKeepStub = function addKeepStub(url) {
        url = $util.url(url);

        var keepSp = $cms.pageKeepSearchParams();

        $util.iterableToArray(keepSp.entries()).forEach(function (entry) {
            var name = entry[0],
                value = entry[1];

            if (!url.searchParams.has(name)) {
                url.searchParams.set(name, value);
            }
        });

        return url.toString();
    };

    /**
     * Analytics platform tracking for events; will integrate with Google Analytics if configured
     * @memberof $cms
     * @param el
     * @param category - This is the 'category' in GA, and combines with action (below) to form the 'event' in inbuilt software tracking
     * @param action
     * @param e - To call e.preventDefault() if the JS event is handled
     * @param nativeTracking - Whether the inbuilt software tracking should register the event (normally we don't do this as we can register events without requiring JavaScript)
     * @returns { Promise }
     */
    $cms.statsEventTrack = function statsEventTrack(el, category, action, label, e, nativeTracking) {
        nativeTracking = nativeTracking || false;
        var useGA = $cms.configOption('google_analytics') && !$cms.isStaff() && !$cms.isAdmin(),
            $ADDON_INSTALLED_stats = boolVal('{$ADDON_INSTALLED,stats}'), // eslint-disable-line camelcase
            promises = [];

        if (useGA) {
            promises.push(new Promise(function (resolve) {
                category = strVal(category) || '{!URL;^}';
                var gaAction = strVal(action) || (el ? el.href : '{!UNKNOWN;^}');

                var okay = true;
                try {
                    $util.log('Beacon', 'send', 'event', category, gaAction);

                    window.ga('send', 'event', category, gaAction, label, { transport: 'beacon', hitCallback: resolve });
                } catch (err) {
                    okay = false;
                }

                if (okay) {
                    if (el) { // pass as null if you don't want this
                        setTimeout(function () {
                            $util.navigate(el);
                        }, 100);
                    }

                    e && e.preventDefault(); // Cancel event because we'll be submitting by ourselves, either via $util.navigate() or on promise resolve
                } else {
                    setTimeout(function () {
                        resolve();
                    }, 100);
                }
            }));
        }

        if ((nativeTracking) && ($ADDON_INSTALLED_stats)) { // eslint-disable-line camelcase
            var snippet = 'stats_event&event=' + encodeURIComponent(category);
            var cmsParam = strVal(action) || (el ? el.href : '');
            if (cmsParam !== '') {
                snippet += '-' + encodeURIComponent(cmsParam);
            }
            promises.push($cms.loadSnippet(snippet));
        }

        return Promise.all(promises);
    };

    /**
     * Used by audio CAPTCHA
     * @memberof $cms
     * @param ob
     * @param soundObject
     */
    $cms.playSelfAudioLink = function playSelfAudioLink(ob, soundObject) {
        if (soundObject) {
            // Some browsers will block the below, because the timer makes it think it is 'autoplay'; even this may fail on Safari
            $util.inform('Playing .wav fully natively');
            soundObject.play().catch(function () {
                $util.inform('Audio playback blocked, reverting to opening .wav in new window');
                window.open(ob.href);
            });
            return false;
        }

        $cms.requireJavascript('sound').then(function () {
            window.soundManager.setup({
                url: $util.rel('data'),
                debugMode: false,
                onready: function () {
                    var soundObject = window.soundManager.createSound({url: ob.href});
                    if (soundObject) {
                        soundObject.play();
                    }
                }
            });
        });
    };

    /**
     * Find out whether an icon is a particular one
     * @param { SVGSVGElement|HTMLImageElement } iconEl
     * @param {string} iconName
     * @returns {boolean}
     */
    $cms.isIcon = function isIcon(iconEl, iconName) {
        iconEl = $dom.elArg(iconEl);

        var src;

        if (iconEl.localName === 'svg') {
            return iconEl.querySelector('use').getAttribute('xlink:href').endsWith('#icon_' + iconName.replace(/\//g, '__'));
        }

        src = $util.url(iconEl.src);

        if (src.pathname.includes('/themewizard.php')) {
            return (src.searchParams.get('show') === 'icons/' + iconName) || (src.searchParams.get('show') === 'icons_monochrome/' + iconName);
        }

        return src.pathname.includes('icons/' + iconName) || src.pathname.includes('icons_monochrome/' + iconName);
    };

    /**
     * @memberof $cms
     * @param functionCallsArray
     * @param [thisRef]
     * @returns {array}
     */
    $cms.executeJsFunctionCalls = function executeJsFunctionCalls(functionCallsArray, thisRef) {
        if (!Array.isArray(functionCallsArray)) {
            $util.fatal('$cms.executeJsFunctionCalls(): Argument 1 must be an array, "' + $util.typeName(functionCallsArray) + '" passed');
            return [];
        }

        var extraChecks = [];

        functionCallsArray.forEach(function (func) {
            var funcName, args;

            if (typeof func === 'string') {
                func = [func];
            }

            if (!Array.isArray(func) || (func.length < 1)) {
                $util.fatal('$cms.executeJsFunctionCalls(): Invalid function call format', func);
                return;
            }

            funcName = strVal(func[0]);
            args = func.slice(1);

            $util.log('$cms.executeJsFunctionCalls(): Calling ' + funcName);

            if (typeof $cms.functions[funcName] === 'function') {
                var result = $cms.functions[funcName].apply(thisRef, args);
                if (Array.isArray(result)) {
                    extraChecks = extraChecks.concat(result);
                }
            } else {
                $util.fatal('$cms.executeJsFunctionCalls(): Function not found: $cms.functions.' + funcName);
            }
        });

        return extraChecks;
    };

    /**
     * Find the main software window
     * @memberof $cms
     * @param anyLargeOk
     * @returns { Window }
     */
    $cms.getMainCmsWindow = function getMainCmsWindow(anyLargeOk) {
        anyLargeOk = Boolean(anyLargeOk);

        if ($dom.$('#main-website')) {
            return window;
        }

        if (anyLargeOk && ($dom.getWindowWidth() > 300)) {
            return window;
        }

        try {
            if (window.parent && (window.parent !== window) && (window.parent.$cms.getMainCmsWindow !== undefined)) {
                return window.parent.$cms.getMainCmsWindow();
            }
        } catch (ignore) {
            // continue
        }

        try {
            if (window.opener && (window.opener.$cms.getMainCmsWindow !== undefined)) {
                return window.opener.$cms.getMainCmsWindow();
            }
        } catch (ignore) {
            // continue
        }

        return window;
    };

    /**
     * Find if the user performed the software "magic keypress" to initiate some action
     * @memberof $cms
     * @param event
     * @returns {boolean}
     */
    $cms.magicKeypress = function magicKeypress(event) {
        // Cmd+Shift works on Mac - cannot hold down control or alt in Mac Firefox at least
        var count = 0;
        if (event.shiftKey) {
            count++;
        }
        if (event.ctrlKey) {
            count++;
        }
        if (event.metaKey) {
            count++;
        }
        if (event.altKey) {
            count++;
        }

        return count >= 2;
    };

    /**
     * Browser sniffing
     * @memberof $cms
     * @param {string} code
     * @returns {boolean}
     */
    $cms.browserMatches = function browserMatches(code) {
        var browser = navigator.userAgent.toLowerCase(),
            os = navigator.platform.toLowerCase() + ' ' + browser;

        var isSafari = browser.includes('applewebkit'),
            isChrome = browser.includes('chrome/'),
            isGecko = browser.includes('gecko') && !isSafari,
            _isIe = browser.includes('msie') || browser.includes('trident') || browser.includes('edge/');

        switch (code) {
            case 'touch_enabled':
                return ('ontouchstart' in document.documentElement);
            case 'simplified_attachments_ui':
                return Boolean($cms.configOption('simplified_attachments_ui') && $cms.configOption('complex_uploader'));
            case 'non_concurrent':
                return browser.includes('iphone') || browser.includes('ipad') || browser.includes('android') || browser.includes('phone') || browser.includes('tablet');
            case 'ios':
                return browser.includes('iphone') || browser.includes('ipad');
            case 'android':
                return browser.includes('android');
            case 'wysiwyg':
                return $cms.configOption('wysiwyg') !== '0';
            case 'windows':
                return os.includes('windows') || os.includes('win32');
            case 'mac':
                return os.includes('mac');
            case 'linux':
                return os.includes('linux');
            case 'ie':
                return _isIe;
            case 'chrome':
                return isChrome;
            case 'gecko':
                return isGecko;
            case 'safari':
                return isSafari;
        }

        // Should never get here
        return false;
    };

    var networkDownAlerted = false;
    var genericAjaxErrorAlerted = false;
    /**
     * @memberof $cms
     * @param {string} url
     * @param {array|function|null} [callback]
     * @param {string|null} [post] - Note that 'post' is not an array, it's a string (a=b)
     * @param {integer} [timeout]
     * @param {boolean} [synchronous]
     * @returns { Promise }
     */
    $cms.doAjaxRequest = function doAjaxRequest(url, callback, post, timeout, synchronous) {
        url = strVal(url);
        timeout = intVal(timeout, 30000);

        var callbackSuccess = null,
            callbackError = null;
        if (Array.isArray(callback)) {
            if (callback[0]) {
                callbackSuccess = callback[0];
            }
            if (callback[1]) {
                // A tip is to not pass this if you want generic error messages generated for you
                callbackError = callback[1];
            }
        } else if (callback != null) {
            // A single callback handle both success and error
            callbackSuccess = callback;
            callbackError = callback;
        }

        var IN_MINIKERNEL_VERSION = document.documentElement.classList.contains('in-minikernel-version');

        if ((typeof post === 'string') && (!post.startsWith('csrf_token=') && !post.includes('&csrf_token=') && !IN_MINIKERNEL_VERSION)) {
            return $cms.getCsrfToken().then(function (text) {
                post += '&csrf_token=' + encodeURIComponent(text);
                return initiateRequestPromise();
            });
        } else {
            return initiateRequestPromise();
        }

        function initiateRequestPromise() {
            return new Promise(function (resolvePromise) {
                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        readyStateChangeListener(xhr, function (responseXml, xhr, success) {
                            if (success && callbackSuccess != null) {
                                callbackSuccess(responseXml, xhr, success);
                            }
                            if (!success && callbackError != null) {
                                callbackError(responseXml, xhr, success);
                            }
                            resolvePromise(xhr, responseXml, success);
                        });
                    }
                };

                if (typeof post === 'string') {
                    xhr.open('POST', url, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    if (!synchronous) {
                        xhr.timeout = timeout;
                    }
                    if ((boolVal('{$VALUE_OPTION;,http2_post_fix}')) && (!synchronous)) {
                        var imgTmp = new Image();
                        imgTmp.onload = function () {
                            xhr.send(post);
                        };
                        imgTmp.src = $cms.getBaseUrl() + '/themes/default/images/blank.gif?rand=' + Math.random();
                    } else {
                        xhr.send(post);
                    }
                } else {
                    xhr.open('GET', url, true);
                    xhr.send(null);
                }
            });
        }

        function readyStateChangeListener(xhr, ajaxCallback) {
            var okStatusCodes = [200];
            // If status is 'OK'
            var responseXML = (xhr.responseXML && xhr.responseXML.firstChild) ? xhr.responseXML : null;

            if (xhr.status && okStatusCodes.includes(xhr.status) || responseXML != null && responseXML.querySelector('message')) {
                // Process the result...

                if (responseXML != null) {
                    var messageEl = responseXML.querySelector('message');
                    if (messageEl) {
                        // A message was returned. As it's our messaging framework, we show the message from here.
                        var message = messageEl.firstChild.textContent;
                        if (responseXML.querySelector('error')) {
                            // It's an error :|
                            $cms.ui.alert({notice: 'An error (' + responseXML.querySelector('error').firstChild.textContent + ') message was returned by the server: ' + message});
                            return;
                        }

                        $cms.ui.alert({notice: 'An informational message was returned by the server: ' + message});

                        ajaxCallback(responseXML, xhr, false);
                    } else {
                        ajaxCallback(responseXML, xhr, true);
                    }
                } else {
                    ajaxCallback(responseXML, xhr, true);
                }
            } else {
                // HTTP error...

                try {
                    $util.fatal('$cms.doAjaxRequest(): {!PROBLEM_AJAX;^}\n' + xhr.status + ': ' + xhr.statusText + '.', xhr);

                    if ((xhr.status === 0) || (xhr.status > 10000)) { // implies site down, or network down
                        if (!networkDownAlerted) {
                            //$cms.ui.alert('{!NETWORK_DOWN;^}');   Annoying because it happens when unsleeping a laptop (for example)
                            networkDownAlerted = true;
                        }
                    } else {
                        // We have no error callback, so we will show the error here.
                        if (callbackError == null) {
                            if (!genericAjaxErrorAlerted) {
                                // There's no callback to handle the error
                                $cms.ui.alert('{!ERROR_OCCURRED;^}');
                                genericAjaxErrorAlerted = true;
                            }
                        }
                    }
                } catch (e) {
                    $util.fatal('$cms.doAjaxRequest(): {!PROBLEM_AJAX;^}', e); // This is probably clicking back
                }

                ajaxCallback(responseXML, xhr, false);
            }
        }
    };

    /**
     * Convert the format of a URL so it can be embedded as a parameter that ModSecurity will not trigger security errors on.
     * @memberof $cms
     * @param {string} parameter
     * @returns {string}
     */
    $cms.protectURLParameter = function protectURLParameter(parameter) {
        parameter = strVal(parameter);

        var baseUrl = $cms.getBaseUrl();

        if (parameter.startsWith('https://')) {
            baseUrl = baseUrl.replace(/^http:\/\//, 'https://');
            if (parameter.startsWith(baseUrl + '/')) {
                return 'https-cms:' + parameter.substr(baseUrl.length + 1);
            }
        } else if (parameter.startsWith('http://')) {
            baseUrl = baseUrl.replace(/^https:\/\//, 'http://');
            if (parameter.startsWith(baseUrl + '/')) {
                return 'http-cms:' + parameter.substr(baseUrl.length + 1);
            }
        }

        return parameter;
    };

    /**
     * Tempcode filters ported to JS
     * @namespace $cms.filter
     * @param str
     * @param {string} filters
     * @returns {string}
     */
    $cms.filter = function filter(str, filters) {
        str = strVal(str);
        filters = strVal(filters);

        for (var i = 0; i < filters.length; i++) {
            switch (filters[i]) {
                case '&':
                    str = $cms.filter.url(str);
                    break;

                case '~':
                    str = $cms.filter.nl(str);
                    break;

                case '|':
                    str = $cms.filter.id(str);
                    break;

                case '=':
                    str = $cms.filter.html(str);
                    break;
            }
        }

        return str;
    };

    /**
     * 1:1 JavaScript port of PHP's urlencode function
     * Credit: http://locutus.io/php/url/urlencode/
     * @param str
     * @returns {string}
     */
    function urlencode(str) {
        return ((str != null) && (str = strVal(str))) ?
            encodeURIComponent(str)
                .replace(/!/g, '%21')
                .replace(/'/g, '%27')
                .replace(/\(/g, '%28')
                .replace(/\)/g, '%29')
                .replace(/\*/g, '%2A')
                .replace(/%20/g, '+')
                .replace(/~/g, '%7E')
            : '';
    }

    /**
     * JS port of the cms_urlencode function used by the tempcode filter '&' (UL_ESCAPED)
     * @memberof $cms.filter
     * @param {string} urlPart
     * @param {boolean} [canTryUrlSchemes]
     * @returns {string}
     */
    $cms.filter.url = function url(urlPart, canTryUrlSchemes) {
        urlPart = strVal(urlPart);
        var urlPartEncoded = urlencode(urlPart);
        canTryUrlSchemes = (canTryUrlSchemes != null) ? Boolean(canTryUrlSchemes) : $cms.canTryUrlSchemes();

        if ((urlPartEncoded !== urlPart) && canTryUrlSchemes) {
            // These interfere with URL Scheme processing because they get pre-decoded and make things ambiguous
            urlPart = urlPart.replace(/\//g, ':slash:').replace(/&/g, ':amp:').replace(/#/g, ':uhash:');
            return urlencode(urlPart);
        }

        return urlPartEncoded;
    };

    /**
     * JS port of the tempcode filter '~' (NL_ESCAPED)
     * @memberof $cms.filter
     * @param {string} str
     * @returns {string}
     */
    $cms.filter.nl = function nl(str) {
        return strVal(str).replace(/[\r\n]/g, '');
    };

    var filterIdReplace = {
        '[': '_opensquare_',
        ']': '_closesquare_',
        '\'': '_apostophe_',
        '-': '_minus_',
        ' ': '_space_',
        '+': '_plus_',
        '*': '_star_',
        '/': '__'
    };

    /**
     * JS port of the tempcode filter '|' (ID_ESCAPED)
     * @memberof $cms.filter
     * @param {string} str
     * @returns {string}
     */
    $cms.filter.id = function id(str) {
        var i, character, ascii, out = '';

        str = strVal(str);

        for (i = 0; i < str.length; i++) {
            character = str[i];

            if (character in filterIdReplace) {
                out += filterIdReplace[character];
            } else {
                ascii = character.charCodeAt(0);

                if (
                    ((i !== 0) && (character === '_')) ||
                    ((ascii >= 48) && (ascii <= 57)) ||
                    ((ascii >= 65) && (ascii <= 90)) ||
                    ((ascii >= 97) && (ascii <= 122))
                ) {
                    out += character;
                } else {
                    out += '_' + ascii + '_';
                }
            }
        }

        if (out === '') {
            out = 'zero_length';
        } else if (out.startsWith('_')) {
            out = 'und_' + out;
        }

        return out;
    };

    /**
     * JS port of the tempcode filter '=' (FORCIBLY_ENTITY_ESCAPED)
     * @memberof $cms.filter
     * @param {string} str
     * @returns {string}
     */
    $cms.filter.html = function html(str) {
        return ((str != null) && (str = strVal(str))) ?
            str.replaceAll('&', '&amp;')
                .replaceAll('"', '&quot;')
                .replaceAll('\'', '&apos;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
            : '';
    };

    /**
     * JS port of the comcode_escape() PHP function
     * @memberof $cms.filter
     * @param {string} str
     * @returns {string}
     */
    $cms.filter.comcode = function comcode(str) {
        return ((str != null) && (str = strVal(str))) ?
            str.replace(/\\/g, '\\\\')
                .replace(/"/g, '\\"')
            : '';
    };
}(window.$cms || (window.$cms = {}), window.$util, window.$dom));
