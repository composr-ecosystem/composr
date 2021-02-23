// Source: https://github.com/inexorabletash/polyfill/blob/master/url.js
// License: https://github.com/inexorabletash/polyfill/blob/master/LICENSE.md
// URL Polyfill
// Draft specification: https://url.spec.whatwg.org

// Notes:
// - Primarily useful for parsing URLs and modifying query parameters
// - Should work in IE8+ and everything more modern, with es5.js polyfills

(function () {
    'use strict';

    function isSequence(o) {
        if (!o) {
            return false;
        }
        if ('Symbol' in window && 'iterator' in window.Symbol && typeof o[Symbol.iterator] === 'function') {
            return true;
        }
        return Array.isArray(o);
    }

    function toArray(iter) {
        return ('from' in Array) ? Array.from(iter) : Array.prototype.slice.call(iter);
    }

    (function () {

        // Browsers may have:
        // * No global URL object
        // * URL with static methods only - may have a dummy constructor
        // * URL with members except searchParams
        // * Full URL API support
        var origURL = window.URL;
        var nativeURL;
        try {
            if (origURL) {
                nativeURL = new window.URL('http://example.com');
                if ('searchParams' in nativeURL)
                    return;
                if (!('href' in nativeURL))
                    nativeURL = undefined;
            }
        } catch (_) {
        }

        // NOTE: Doesn't do the encoding/decoding dance
        function urlencoded_serialize(pairs) {
            var output = '', first = true;
            pairs.forEach(function (pair) {
                var name = encodeURIComponent(pair.name);
                var value = encodeURIComponent(pair.value);
                if (!first) output += '&';
                output += name + '=' + value;
                first = false;
            });
            return output.replace(/%20/g, '+');
        }

        // NOTE: Doesn't do the encoding/decoding dance
        function urlencoded_parse(input, isindex) {
            var sequences = input.split('&');
            if (isindex && sequences[0].indexOf('=') === -1)
                sequences[0] = '=' + sequences[0];
            var pairs = [];
            sequences.forEach(function (bytes) {
                if (bytes.length === 0) return;
                var index = bytes.indexOf('=');
                if (index !== -1) {
                    var name = bytes.substring(0, index);
                    var value = bytes.substring(index + 1);
                } else {
                    name = bytes;
                    value = '';
                }
                name = name.replace(/\+/g, ' ');
                value = value.replace(/\+/g, ' ');
                pairs.push({name: name, value: value});
            });
            var output = [];
            pairs.forEach(function (pair) {
                output.push({
                    name: decodeURIComponent(pair.name),
                    value: decodeURIComponent(pair.value)
                });
            });
            return output;
        }

        function URLUtils(url) {
            if (nativeURL)
                return new origURL(url);
            var anchor = document.createElement('a');
            anchor.href = url;
            return anchor;
        }

        window.URLSearchParams = URLSearchParams;
        /**
         * @class { URLSearchParams }
         * @param [init]
         */
        function URLSearchParams(init) {
            var $this = this;
            this._list = [];

            if (init === undefined || init === null) {
                // no-op
            } else if (init instanceof URLSearchParams) {
                // In ES6 init would be a sequence, but special case for ES5.
                this._list = urlencoded_parse(String(init));
            } else if (typeof init === 'object' && isSequence(init)) {
                toArray(init).forEach(function (e) {
                    if (!isSequence(e)) throw TypeError();
                    var nv = toArray(e);
                    if (nv.length !== 2) throw TypeError();
                    $this._list.push({name: String(nv[0]), value: String(nv[1])});
                });
            } else if (typeof init === 'object' && init) {
                Object.keys(init).forEach(function (key) {
                    $this._list.push({name: String(key), value: String(init[key])});
                });
            } else {
                init = String(init);
                if (init.substring(0, 1) === '?')
                    init = init.substring(1);
                this._list = urlencoded_parse(init);
            }

            this._url_object = null;
            this._setList = function (list) {
                if (!updating) $this._list = list;
            };

            var updating = false;
            this._update_steps = function () {
                if (updating) return;
                updating = true;

                if (!$this._url_object) return;

                // Partial workaround for IE issue with 'about:'
                if ($this._url_object.protocol === 'about:' &&
                    $this._url_object.pathname.indexOf('?') !== -1) {
                    $this._url_object.pathname = $this._url_object.pathname.split('?')[0];
                }

                $this._url_object.search = urlencoded_serialize($this._list);

                updating = false;
            };
        }

        Object.defineProperties(URLSearchParams.prototype, {
            append: {
                /**
                 * @memberof URLSearchParams#
                 * @method append
                 * @param name
                 * @param value
                 */
                value: function (name, value) {
                    this._list.push({name: name, value: value});
                    this._update_steps();
                }, writable: true, enumerable: true, configurable: true
            },
            'delete': {
                /**
                 * @memberof URLSearchParams#
                 * @method delete
                 * @param name
                 */
                value: function (name) {
                    for (var i = 0; i < this._list.length;) {
                        if (this._list[i].name === name)
                            this._list.splice(i, 1);
                        else
                            ++i;
                    }
                    this._update_steps();
                }, writable: true, enumerable: true, configurable: true
            },
            get: {
                /**
                 * @memberof URLSearchParams#
                 * @method get
                 * @param name
                 * @return {string|null}
                 */
                value: function (name) {
                    for (var i = 0; i < this._list.length; ++i) {
                        if (this._list[i].name === name)
                            return this._list[i].value;
                    }
                    return null;
                }, writable: true, enumerable: true, configurable: true
            },
            getAll: {
                /**
                 * @memberof URLSearchParams#
                 * @method getAll
                 * @param name
                 * @return {array}
                 */
                value: function (name) {
                    var result = [];
                    for (var i = 0; i < this._list.length; ++i) {
                        if (this._list[i].name === name)
                            result.push(this._list[i].value);
                    }
                    return result;
                }, writable: true, enumerable: true, configurable: true
            },
            has: {
                /**
                 * @memberof URLSearchParams#
                 * @method has
                 * @param name
                 * @return {boolean}
                 */
                value: function (name) {
                    for (var i = 0; i < this._list.length; ++i) {
                        if (this._list[i].name === name)
                            return true;
                    }
                    return false;
                }, writable: true, enumerable: true, configurable: true
            },
            set: {
                /**
                 * @memberof URLSearchParams#
                 * @method set
                 * @param name
                 * @param value
                 */
                value: function (name, value) {
                    var found = false;
                    for (var i = 0; i < this._list.length;) {
                        if (this._list[i].name === name) {
                            if (!found) {
                                this._list[i].value = value;
                                found = true;
                                ++i;
                            } else {
                                this._list.splice(i, 1);
                            }
                        } else {
                            ++i;
                        }
                    }

                    if (!found)
                        this._list.push({name: name, value: value});

                    this._update_steps();
                }, writable: true, enumerable: true, configurable: true
            },
            entries: {
                /**
                 * @memberof URLSearchParams#
                 * @method entries
                 * @return {Iterator}
                 */
                value: function () {
                    return new Iterator(this._list, 'key+value');
                },
                writable: true, enumerable: true, configurable: true
            },
            keys: {
                /**
                 * @memberof URLSearchParams#
                 * @method keys
                 * @return {Iterator}
                 */
                value: function () {
                    return new Iterator(this._list, 'key');
                },
                writable: true, enumerable: true, configurable: true
            },
            values: {
                /**
                 * @memberof URLSearchParams#
                 * @method values
                 * @return Iterator
                 */
                value: function () {
                    return new Iterator(this._list, 'value');
                },
                writable: true, enumerable: true, configurable: true
            },
            forEach: {
                /**
                 * @memberof URLSearchParams#
                 * @method forEach
                 * @param callback
                 */
                value: function (callback) {
                    var thisArg = (arguments.length > 1) ? arguments[1] : undefined;
                    this._list.forEach(function (pair, index) {
                        callback.call(thisArg, pair.value, pair.name);
                    });

                }, writable: true, enumerable: true, configurable: true
            },
            toString: {
                /**
                 * @memberof URLSearchParams#
                 * @method toString
                 * @return {string}
                 */
                value: function () {
                    return urlencoded_serialize(this._list);
                }, writable: true, enumerable: false, configurable: true
            }
        });

        function Iterator(source, kind) {
            var index = 0;
            this['next'] = function () {
                if (index >= source.length)
                    return {done: true, value: undefined};
                var pair = source[index++];
                return {
                    done: false, value: kind === 'key' ? pair.name :
                        kind === 'value' ? pair.value :
                            [pair.name, pair.value]
                };
            };
        }

        if ('Symbol' in window && 'iterator' in window.Symbol) {
            Object.defineProperty(URLSearchParams.prototype, window.Symbol.iterator, {
                value: URLSearchParams.prototype.entries,
                writable: true, enumerable: true, configurable: true
            });
            Object.defineProperty(Iterator.prototype, window.Symbol.iterator, {
                value: function () {
                    return this;
                },
                writable: true, enumerable: true, configurable: true
            });
        }

        window.URL = URL;
        /**
         * @class { URL }
         * @param url
         * @param [base]
         */
        function URL(url, base) {
            if (!(this instanceof window.URL))
                throw new TypeError("Failed to construct 'URL': Please use the 'new' operator.");

            if (base) {
                url = (function () {
                    if (nativeURL) return new origURL(url, base).href;
                    var iframe;
                    try {
                        var doc;
                        // Use another document/base tag/anchor for relative URL resolution, if possible
                        if (Object.prototype.toString.call(window.operamini) === "[object OperaMini]") {
                            iframe = document.createElement('iframe');
                            iframe.style.display = 'none';
                            document.documentElement.appendChild(iframe);
                            doc = iframe.contentWindow.document;
                        } else if (document.implementation && document.implementation.createHTMLDocument) {
                            doc = document.implementation.createHTMLDocument('');
                        } else if (document.implementation && document.implementation.createDocument) {
                            doc = document.implementation.createDocument('http://www.w3.org/1999/xhtml', 'html', null);
                            doc.documentElement.appendChild(doc.createElement('head'));
                            doc.documentElement.appendChild(doc.createElement('body'));
                        } else if (window.ActiveXObject) {
                            doc = new window.ActiveXObject('htmlfile');
                            doc.write('<head><\/head><body><\/body>');
                            doc.close();
                        }

                        if (!doc) throw Error('base not supported');

                        var baseTag = doc.createElement('base');
                        baseTag.href = base;
                        doc.getElementsByTagName('head')[0].appendChild(baseTag);
                        var anchor = doc.createElement('a');
                        anchor.href = url;
                        return anchor.href;
                    } finally {
                        if (iframe)
                            iframe.parentNode.removeChild(iframe);
                    }
                }());
            }

            // An inner object implementing URLUtils (either a native URL
            // object or an HTMLAnchorElement instance) is used to perform the
            // URL algorithms. With full ES5 getter/setter support, return a
            // regular object For IE8's limited getter/setter support, a
            // different HTMLAnchorElement is returned with properties
            // overridden

            var instance = URLUtils(url || '');

            var query_object = new URLSearchParams(instance.search ? instance.search.substring(1) : null);
            query_object._url_object = this;

            Object.defineProperties(this, {
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                href: {
                    get: function () {
                        return instance.href;
                    },
                    set: function (v) {
                        instance.href = v;
                        tidy_instance();
                        update_steps();
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                origin: {
                    get: function () {
                        if ('origin' in instance) return instance.origin;
                        return this.protocol + '//' + this.host;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                protocol: {
                    get: function () {
                        return instance.protocol;
                    },
                    set: function (v) {
                        instance.protocol = v;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                username: {
                    get: function () {
                        return instance.username;
                    },
                    set: function (v) {
                        instance.username = v;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                password: {
                    get: function () {
                        return instance.password;
                    },
                    set: function (v) {
                        instance.password = v;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                host: {
                    get: function () {
                        // IE returns default port in |host|
                        var re = {'http:': /:80$/, 'https:': /:443$/, 'ftp:': /:21$/}[instance.protocol];
                        return re ? instance.host.replace(re, '') : instance.host;
                    },
                    set: function (v) {
                        instance.host = v;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                hostname: {
                    get: function () {
                        return instance.hostname;
                    },
                    set: function (v) {
                        instance.hostname = v;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                port: {
                    get: function () {
                        return instance.port;
                    },
                    set: function (v) {
                        instance.port = v;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                pathname: {
                    get: function () {
                        // IE does not include leading '/' in |pathname|
                        if (instance.pathname.charAt(0) !== '/') return '/' + instance.pathname;
                        return instance.pathname;
                    },
                    set: function (v) {
                        instance.pathname = v;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                search: {
                    get: function () {
                        return instance.search;
                    },
                    set: function (v) {
                        if (instance.search === v) return;
                        instance.search = v;
                        tidy_instance();
                        update_steps();
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type { URLSearchParams }
                 */
                searchParams: {
                    get: function () {
                        return query_object;
                    },
                    enumerable: true, configurable: true
                },
                /**
                 * @memberof URL#
                 * @type {string}
                 */
                hash: {
                    get: function () {
                        return instance.hash;
                    },
                    set: function (v) {
                        instance.hash = v;
                        tidy_instance();
                    },
                    enumerable: true, configurable: true
                },
                toString: {
                    /**
                     * @memberof URL#
                     * @method toString
                     * @return {string}
                     */
                    value: function () {
                        return instance.toString();
                    },
                    enumerable: false, configurable: true
                },
                valueOf: {
                    /**
                     * @memberof URL#
                     * @method valueOf
                     * @return {*}
                     */
                    value: function () {
                        return instance.valueOf();
                    },
                    enumerable: false, configurable: true
                }
            });

            function tidy_instance() {
                var href = instance.href.replace(/#$|\?$|\?(?=#)/g, '');
                if (instance.href !== href)
                    instance.href = href;
            }

            function update_steps() {
                query_object._setList(instance.search ? urlencoded_parse(instance.search.substring(1)) : []);
                query_object._update_steps();
            }

            return this;
        }

        if (origURL) {
            for (var i in origURL) {
                if (origURL.hasOwnProperty(i) && typeof origURL[i] === 'function')
                    URL[i] = origURL[i];
            }
        }
    }());

    // Patch native URLSearchParams constructor to handle sequences/records
    // if necessary.
    (function () {
        if (new window.URLSearchParams([['a', 1]]).get('a') === '1' && new window.URLSearchParams({a: 1}).get('a') === '1') {
            return;
        }
        var orig = window.URLSearchParams;
        window.URLSearchParams = function URLSearchParams(init) {
            if (init && typeof init === 'object' && isSequence(init)) {
                var o = new orig();
                toArray(init).forEach(function (e) {
                    if (!isSequence(e)) throw TypeError();
                    var nv = toArray(e);
                    if (nv.length !== 2) throw TypeError();
                    o.append(nv[0], nv[1]);
                });
                return o;
            } else if (init && typeof init === 'object') {
                o = new orig();
                Object.keys(init).forEach(function (key) {
                    o.set(key, init[key]);
                });
                return o;
            } else {
                return new orig(init);
            }
        };
    }());
}());
