/* This file contains various utility functions that are not software-specific */

(function ($util) {
    'use strict';

    /* The *Val functions are typically used when our JavaScript code takes input from the template layer, and we want to force a particular type (or be clear to developers we want a string). */

    /**
     * Port of PHP's boolval() function
     * @param val
     * @param [defaultValue]
     * @returns { Boolean }
     */
    window.boolVal = function boolVal(val, defaultValue) {
        if (defaultValue === undefined) {
            defaultValue = false;
        }

        if (val == null) {
            return defaultValue;
        }

        return Boolean(val) && (val !== '0'); //&& ((typeof val !== 'object') || !((p = $util.isPlainObj(val)) || $util.isArrayLike(val)) || (p ? $util.hasEnumerable(val) : (val.length > 0)));
    };

    /**
     * @param val
     * @param [defaultValue]
     * @returns { Number }
     */
    window.intVal = function intVal(val, defaultValue) {
        if (defaultValue === undefined) {
            defaultValue = 0;
        }

        if (val == null) {
            return defaultValue;
        }

        // if (((typeof val === 'object') && (val.valueOf === Object.prototype.valueOf)) || ((typeof val === 'function') && (val.valueOf === noop.valueOf))) {
        //     throw new TypeError('intVal(): Cannot coerce `val` of type "' + typeName(val) + '" to an integer.');
        // }

        if (isNaN(val) || (val < Number.MIN_SAFE_INTEGER) || (val > Number.MAX_SAFE_INTEGER)) {
            throw new TypeError('intVal(): Invalid integer provided: "' + val + '"');
        }

        val = Math.floor(val);

        return (val && (val !== Infinity) && (val !== -Infinity)) ? val : 0;
    };

    /**
     * @param val
     * @param [defaultValue]
     * @returns { Number }
     */
    window.numVal = function numVal(val, defaultValue) {
        if (defaultValue === undefined) {
            defaultValue = 0;
        }

        if (val == null) {
            return defaultValue;
        }

        // if (((typeof val === 'object') && (val.valueOf === Object.prototype.valueOf)) || ((typeof val === 'function') && (val.valueOf === noop.valueOf))) {
        //     throw new TypeError('numVal(): Cannot coerce `val` of type "' + typeName(val) + '" to a number.');
        // }

        val = Number(val);

        if ((val === Infinity) || (val === -Infinity)) {
            throw new TypeError('numVal(): Number over/underflow detected!');
        }

        return val ? val : 0;
    };

    /**
     * @param val
     * @param [defaultValue]
     * @returns { Array }
     */
    window.arrVal = function arrVal(val, defaultValue) {
        if (defaultValue === undefined) {
            defaultValue = [];
        }

        if (val == null) {
            return defaultValue;
        }

        if ((typeof val === 'object') && (Array.isArray(val) || $util.isArrayLike(val))) {
            return $util.toArray(val);
        }

        return [val];
    };

    /**
     * @param val
     * @param [defaultValue]
     * @param [defaultPropertyName]
     * @returns { Object }
     */
    window.objVal = function objVal(val, defaultValue, defaultPropertyName) {
        if (defaultValue === undefined) {
            defaultValue = {};
        }

        if (val == null) {
            return defaultValue;
        }

        if (!$util.isObj(val)) {
            if (defaultPropertyName != null) {
                val = $util.keyValue(defaultPropertyName, val);
            } else {
                throw new TypeError('objVal(): Cannot coerce `val` of type "' + $util.typeName(val) + '" to an object.');
            }
        }

        return val;
    };

    /**
     * Sensible PHP-like string coercion
     * @param val
     * @param [defaultValue]
     * @returns { string }
     */
    window.strVal = function strVal(val, defaultValue) {
        if (defaultValue === undefined) {
            defaultValue = '';
        }

        if (val == null) {
            return defaultValue;
        }

        var ret;
        if (!val) {
            ret = (val === 0) ? '0' : '';
        } else if (val === true) {
            ret = '1';
        } else if (typeof val === 'string') {
            ret = val;
        } else if (typeof val === 'number') {
            ret = ((val !== Infinity) && (val !== -Infinity)) ? (String(val)) : '';
        } else if ((typeof val === 'object') && (val.toString !== Object.prototype.toString) && (typeof val.toString === 'function')) {
            // `val` has a .toString() implementation other than the useless generic one
            ret = String(val);
        } else {
            throw new TypeError('strVal(): Cannot coerce `val` of type "' + $util.typeName(val) + '" to a string.');
        }

        return ret;
    };

    /**@namespace $util*/
    /**
     * @method
     * @returns {boolean}
     */
    $util.hasOwn = Function.bind.call(Function.call, Object.prototype.hasOwnProperty);
    /**
     * @method
     * @returns { Array }
     */
    $util.toArray = Function.bind.call(Function.call, Array.prototype.slice);
    /**
     * @method
     * @returns { Array }
     */
    $util.pushArray = Function.bind.call(Function.apply, Array.prototype.push);

    // Generate a unique integer id (unique within the entire client session).
    var _uniqueId;
    function uniqueId() {
        if (_uniqueId === undefined) {
            _uniqueId = 0;
        }
        return ++_uniqueId;
    }

    var _uids = new WeakMap();
    /**
     * Provides a unique integer id to uniquely identify objects/functions
     * @param {object|function} obj
     * @returns {number}
     */
    $util.uid = function uid(obj) {
        if ((obj == null) || ((typeof obj !== 'object') && (typeof obj !== 'function'))) {
            throw new TypeError('$util.uid(): Parameter `obj` must be an object or a function.');
        }

        var id = _uids.get(obj);

        if (id === undefined) {
            id = uniqueId();
            _uids.set(obj, id);
        }

        return id;
    };

    /**
     * Creates a function that always returns the same value that is passed as the first argument here
     * @param value
     * @returns { function }
     */
    $util.constant = function constant(value) {
        return function _constant() {
            return value;
        };
    };

    /**
     * @param val
     * @param withEnumerable (boolean)
     * @returns {boolean}
     */
    $util.isObj = function isObj(val, withEnumerable) {
        return (val != null) && (typeof val === 'object') && (!withEnumerable || $util.hasEnumerable(val));
    };

    /**
     * @param val
     * @returns {boolean}
     */
    $util.hasEnumerable = function hasEnumerable(val) {
        if (val != null) {
            for (var key in val) {
                return true;
            }
        }
        return false;
    };

    /**
     * @param val
     * @returns {boolean}
     */
    $util.hasOwnEnumerable = function hasOwnEnumerable(val) {
        if (val != null) {
            for (var key in val) {
                if ($util.hasOwn(val, key)) {
                    return true;
                }
            }
        }
        return false;
    };

    /**
     * @param obj
     * @returns {*|boolean}
     */
    $util.isPlainObj = function isPlainObj(obj) {
        var proto;
        return $util.isObj(obj) && ($util.internalName(obj) === 'Object') && (((proto = Object.getPrototypeOf(obj)) === Object.prototype) || (proto === null));
    };

    /**
     * @param key
     * @param value
     * @returns {object}
     */
    $util.keyValue = function keyValue(key, value) {
        var obj = {};
        obj[key] = value;
        return obj;
    };

    /**
     * @param obj
     * @returns {boolean}
     */
    $util.isPromise = function isPromise(obj) {
        return (obj != null) && (typeof obj === 'object') && (typeof obj.then === 'function');
    };

    /**
     * Used to execute a series promises one after another, in a sequence.
     * @see https://pouchdb.com/2015/05/18/we-have-a-problem-with-promises.html
     * @param {function[]} promiseFactories
     * @returns { Promise }
     */
    $util.promiseSequence = function promiseSequence(promiseFactories) {
        promiseFactories = arrVal(promiseFactories);

        var result = Promise.resolve();
        promiseFactories.forEach(function (promise) {
            result = result.then(promise);
        });

        return result;
    };

    var _haltedPromise;
    /**
     * Use this to halt promise chain execution since using unresolved promises used to stop the execution chain can cause memory leaks.
     * This will simply keep a single unresolved promise around, which will be the only promise that isn't garbage collected.
     * Since then() and catch() are overridden in this new promise, the chain should not build up, and old parts of the chain should be garbage collected.
     * @see https://github.com/elastic/kibana/issues/3015
     * @return { Promise }
     */
    $util.promiseHalt = function promiseHalt() {
        if (_haltedPromise === undefined) {
            _haltedPromise = new Promise(function () {});
            $util.properties(_haltedPromise, {
                then: function then() {
                    return _haltedPromise;
                },
                'catch': function _catch() {
                    return _haltedPromise;
                }
            });
        }

        return _haltedPromise;
    };

    /**
     * Enhances a promise with methods to query its state (Credit: https://stackoverflow.com/a/21489870/362006)
     * @param promise
     * @returns { Promise }
     */
    $util.promiseMakeQuerable = function promiseMakeQuerable(promise) {
        // Don't modify any promise that has been already modified.
        if (typeof promise.isResolved === 'function') {
            return promise;
        }

        // Set initial state
        var isPending = true,
            isRejected = false,
            isResolved = false;

        // Observe the promise, saving the fulfilment in a closure scope.
        var result = promise.then(
            function (v) {
                isResolved = true;
                isPending = false;
                return v;
            },
            function (e) {
                isRejected = true;
                isPending = false;
                throw e;
            }
        );

        result.isResolved = function () {
            return isResolved;
        };
        result.isPending = function () {
            return isPending;
        };
        result.isRejected = function () {
            return isRejected;
        };

        return result;
    };

    /**
     * @param obj
     * @returns {boolean}
     */
    $util.isWindow = function isWindow(obj) {
        return $util.isObj(obj) && (obj === obj.window) && (obj === obj.self) && ($util.internalName(obj) === 'Window');
    };

    /**
     * Returns the node type
     * @param obj
     * @returns {boolean|number}
     */
    $util.nodeType = function nodeType(obj) {
        return $util.isObj(obj) && (typeof obj.nodeName === 'string') && (typeof obj.nodeType === 'number') && obj.nodeType; // eslint-disable-line no-restricted-properties
    };

    var ELEMENT_NODE = 1,
        DOCUMENT_NODE = 9,
        DOCUMENT_FRAGMENT_NODE = 11;

    /**
     * @param obj
     * @returns {boolean}
     */
    $util.isNode = function isNode(obj) {
        return $util.nodeType(obj) !== false;
    };

    /**
     * @param obj
     * @returns {boolean}
     */
    $util.isEl = function isEl(obj) {
        return $util.nodeType(obj) === ELEMENT_NODE;
    };

    /**
     * @param obj
     * @returns {boolean}
     */
    $util.isDoc = function isDoc(obj) {
        return $util.nodeType(obj) === DOCUMENT_NODE;
    };

    /**
     * @param obj
     * @returns {boolean}
     */
    $util.isDocFrag = function isDocFrag(obj) {
        return $util.nodeType(obj) === DOCUMENT_FRAGMENT_NODE;
    };

    /**
     * @param obj
     * @returns {boolean}
     */
    $util.isRegExp = function isRegExp(obj) {
        return (obj != null) && ($util.internalName(obj) === 'RegExp');
    };

    /**
     * @param obj
     * @returns {boolean}
     */
    $util.isDate = function isDate(obj) {
        return (obj != null) && ($util.internalName(obj) === 'Date');
    };

    /**
     * Returns true if `val` is a numeric string
     * @param val
     * @returns {boolean}
     */
    $util.isNumeric = function isNumeric(val) {
        val = strVal(val);

        return (val !== '') && isFinite(val);
    };

    /**
     * Returns true if `obj` is an array-like object
     * @param obj
     * @param minLength
     * @returns {boolean}
     */
    $util.isArrayLike = function isArrayLike(obj, minLength) {
        var len;
        minLength = Number(minLength) || 0;

        return (obj != null) &&
            (typeof obj === 'object') &&
            ($util.internalName(obj) !== 'Window') &&
            (typeof (len = obj.length) === 'number') &&
            (len >= minLength) &&
            ((len === 0) || ((0 in obj) && ((len - 1) in obj)));
    };

    /**
     * Returns a random integer between min (inclusive) and max (inclusive)
     * Using Math.round() will give you a non-uniform distribution!
     * @param [min] {number}
     * @param [max] {number}
     * @returns {number}
     */
    $util.random = function random(min, max) {
        min = intVal(min, 0);
        max = intVal(max, 1000000000000); // 1 Trillion

        return Math.floor(Math.random() * (max - min + 1)) + min;
    };

    /**
     * @param number {number}
     * @returns {string}
     */
    $util.decToHex = function decToHex(number) {
        var hexbase = '0123456789ABCDEF';
        // eslint-disable-next-line no-bitwise
        return hexbase.charAt((number >> 4) & 0xf) + hexbase.charAt(number & 0xf);
    };

    /**
     * @param number {string}
     * @returns {number}
     */
    $util.hexToDec = function hexToDec(number) {
        return parseInt(number, 16);
    };

    /**
     * Iterates over an object
     * @param obj
     * @param callback
     * @returns {*}
     */
    $util.each = function each(obj, callback) {
        if (obj == null) {
            return obj;
        }

        for (var name in obj) {
            if (callback.call(obj, name, obj[name]) === false) {
                return obj;
            }
        }

        return obj;
    };

    var EXTEND_DEEP = 1,
        EXTEND_TGT_OWN_ONLY = 2,
        EXTEND_SRC_OWN_ONLY = 4;

    function _extend(target, source, mask) {
        /*eslint-disable no-bitwise*/
        var key, tgt, src, isSrcArr;

        mask = Number(mask) || 0;

        for (key in source) {
            tgt = target[key];
            src = source[key];

            if (
                (src === undefined) ||
                ((mask & EXTEND_TGT_OWN_ONLY) && !$util.hasOwn(target, key)) ||
                ((mask & EXTEND_SRC_OWN_ONLY) && !$util.hasOwn(source, key))
            ) {
                continue;
            }

            if ((mask & EXTEND_DEEP) && src && (typeof src === 'object') && ((isSrcArr = Array.isArray(src)) || $util.isPlainObj(src))) {
                if (isSrcArr && !Array.isArray(tgt)) {
                    tgt = [];
                } else if (!isSrcArr && !$util.isPlainObj(tgt)) {
                    tgt = {};
                }

                target[key] = _extend(tgt, src, mask);
            } else {
                target[key] = src;
            }
        }
    }

    /**
     * Copy all except undefined properties from one or more objects to the `target` object.
     * @memberof $util
     * @param target
     * @param {...object} sources - Source objects
     * @returns {*}
     */
    $util.extend = function extend(target, /*...*/sources) {
        sources = $util.toArray(arguments, 1);

        for (var i = 0, len = sources.length; i < len; i++) {
            _extend(target, sources[i]);
        }
        return target;
    };

    /**
     * Extends `target` with source own-properties only.
     * @param target
     * @param {...object} sources - Source objects
     * @returns {*}
     */
    $util.extendOwn = function extendOwn(target, /*...*/sources) {
        sources = $util.toArray(arguments, 1);

        for (var i = 0, len = sources.length; i < len; i++) {
            _extend(target, sources[i], EXTEND_SRC_OWN_ONLY);
        }
        return target;
    };

    /**
     * Deep extend, clones any arrays and plain objects found in sources.
     * @param target
     * @param {...object} sources - Source objects
     * @returns {object}
     */
    $util.extendDeep = function extendDeep(target, /*...*/sources) {
        sources = $util.toArray(arguments, 1);

        for (var i = 0, len = sources.length; i < len; i++) {
            _extend(target, sources[i], EXTEND_DEEP);
        }
        return target;
    };

    /**
     * Apply `options` to the `defaults` object. Only copies over properties with keys already defined in the `defaults` object.
     * @param defaults
     * @param {...object} options - Options
     * @returns {*}
     */
    $util.defaults = function defaults(defaults, /*...*/options) {
        options = $util.toArray(arguments, 1);

        for (var i = 0, len = options.length; i < len; i++) {
            _extend(defaults, options[i], EXTEND_TGT_OWN_ONLY);
        }
        return defaults;
    };

    /**
     * If the value of the named `property` is a function then invoke it with the
     * `object` as context; otherwise, return it.
     * @param object
     * @param property
     * @param fallback
     * @returns {*}
     */
    $util.result = function result(object, property, fallback) {
        var value = ((object != null) && (object[property] !== undefined)) ? object[property] : fallback;
        return (typeof value === 'function') ? value.call(object) : value;
    };

    /**
     * Less verbose alternative to Object.defineProperties()
     * @param {string} [mask] - optional, assumed to be `obj` if not of type string.
     * @param {object} obj - the target object to define properties on.
     * @param {object|string} props - is a single property's name if `value` is passed.
     * @returns {Object}
     */
    $util.properties = function properties(mask, obj, props) {
        var key, descriptors, descriptor;

        if (typeof mask !== 'string') {
            props = obj;
            obj = mask;
            mask = 'cw';
        }

        mask = strVal(mask);

        var configurable = mask.includes('c'),
            enumerable = mask.includes('e'),
            writeable = mask.includes('w');

        descriptors = {};
        for (key in props) {
            if (!$util.hasOwn(props, key)) {
                continue;
            }

            descriptor = Object.getOwnPropertyDescriptor(props, key);
            descriptor.configurable = configurable;
            descriptor.enumerable = enumerable;
            if (descriptor.writeable !== undefined) {
                // ^ It's not an accessor property, otherwise we just let descriptor.get/set pass-through
                descriptor.writeable = writeable;
            }
            descriptors[key] = descriptor;
        }

        return Object.defineProperties(obj, descriptors);
    };

    /**
     * Gets the internal type/constructor name of the provided `val`
     * @param val
     * @returns {string}
     */
    $util.internalName = function internalName(val) {
        return Object.prototype.toString.call(val).slice(8, -1); // slice off the surrounding '[object ' and ']'
    };

    /**
     * @param obj
     * @returns {*}
     */
    $util.constructorName = function constructorName(obj) {
        if ((obj != null) && (typeof obj.constructor === 'function') && (typeof obj.constructor.name === 'string')) {
            return obj.constructor.name;
        }
    };

    /**
     *
     * @param obj
     * @returns {*}
     */
    $util.typeName = function typeName(obj) {
        var name = $util.constructorName(obj);
        return ((name !== undefined) && (name !== '')) ? name : $util.internalName(obj);
    };

    /**
     * String interpolation
     * @param str
     * @param { Array|object } values
     * @returns { string }
     */
    $util.format = function format(str, values) {
        str = strVal(str);

        if ((str === '') || (values == null) || (typeof values !== 'object')) {
            return str; // Nothing to do
        }

        if (!$util.isArrayLike(values)) {
            values = [values];
        }

        // Handle basic parameter reference syntax
        str = str.replace(/\{(\d+)\}/g, function (match, key) {
            key--; // So that interpolation starts from '{1}'
            return (key in values) ? strVal(values[key]) : match;
        });

        // Handle pluralisation syntax
        var nonPluralNonVowel = ['1', 'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z', '{'/*for no-op param usage*/];
        str = str.replace(/\{([^,]+)\|([^,]*)\|([^,]*)\}/g, function (match, key, a, b) {
            key--; // So that interpolation starts from '{1}'
            return (key in values) ? (nonPluralNonVowel.includes(strVal(values[key]).substr(0, 1)) ? a : b) : match;
        });

        return str;
    };

    /**
     * Upper case the first letter of a string
     * @param str
     * @returns {string}
     */
    $util.ucFirst = function ucFirst(str) {
        return ((str != null) && (str = strVal(str))) ? str.charAt(0).toUpperCase() + str.substr(1) : '';
    };

    /**
     * Lower case the first letter of a string
     * @param str
     * @returns {string}
     */
    $util.lcFirst = function lcFirst(str) {
        return ((str != null) && (str = strVal(str))) ? str.charAt(0).toLowerCase() + str.substr(1) : '';
    };

    /**
     * Used to match `RegExp`
     * [syntax characters](http://ecma-international.org/ecma-262/7.0/#sec-patterns).
     */
    var reRegExpChar = /[\\^$.*+?()[\]{}|]/g,
        reHasRegExpChar = RegExp(reRegExpChar.source);

    /**
     * Escapes the `RegExp` special characters "^", "$", "\", ".", "*", "+",
     * "?", "(", ")", "[", "]", "{", "}", and "|" in `string`.
     *
     * Credit: https://lodash.com/docs#escapeRegExp
     * @memberOf $util
     * @param {string} [string=''] The string to escape.
     * @returns {string} Returns the escaped string.
     */
    $util.escapeRegExp = function escapeRegExp(string) {
        string = strVal(string);
        return (string && reHasRegExpChar.test(string)) ? string.replace(reRegExpChar, '\\$&') : string;
    };

    /**
     * Returns a camelCased string.
     * Credit: http://stackoverflow.com/a/32604073/362006
     * @param str
     * @returns {string}
     */
    $util.camelCase = function camelCase(str) {
        return ((str != null) && (str = strVal(str))) ?
            str.replace(/[-_]+/g, ' ') // Replaces any - or _ characters with a space
                .replace(/[^\w\s]/g, '') // Removes any non alphanumeric characters
                .replace(/ (.)/g, function ($1) { // Upper cases the first character in each group immediately following a space (delimited by spaces)
                    return $1.toUpperCase();
                })
                .replace(/ /g, '') // Removes spaces
            : '';
    };

    /**
     * Creates and returns a new, throttled version of the passed function, that, when invoked repeatedly,
     * will only actually call the original function at most once per every wait milliseconds.
     * Useful for rate-limiting events that occur faster than you can keep up with.
     * By default, throttle will execute the function as soon as you call it for the first time, and, if you call it again any number of times during
     * the wait period, as soon as that period is over. If you'd like to disable the leading-edge call, pass {leading: false},
     * and if you'd like to disable the execution on the trailing-edge, pass {trailing: false}.
     * @param func
     * @param wait
     * @param options
     * @return {function}
     */
    $util.throttle = function throttle(func, wait, options) {
        var context, args, result,
            timeout = null,
            previous = 0;

        if (!options) {
            options = {};
        }

        var later = function () {
            previous = options.leading === false ? 0 : Date.now();
            timeout = null;
            result = func.apply(context, args);
            if (!timeout) {
                context = args = null;
            }
        };

        return function throttledFn() {
            var now = Date.now();
            if (!previous && options.leading === false) {
                previous = now;
            }
            var remaining = wait - (now - previous);
            context = this;
            args = arguments;
            if (remaining <= 0 || remaining > wait) {
                if (timeout) {
                    clearTimeout(timeout);
                    timeout = null;
                }
                previous = now;
                result = func.apply(context, args);
                if (!timeout) {
                    context = args = null;
                }
            } else if (!timeout && options.trailing !== false) {
                timeout = setTimeout(later, remaining);
            }
            return result;
        };
    };

    /**
     * Creates and returns a new debounced version of the passed function which will postpone its execution until after wait milliseconds have elapsed since the last time it was invoked.
     * Useful for implementing behavior that should only happen after the input has stopped arriving.
     * For example: rendering a preview of a Markdown comment, recalculating a layout after the window has stopped being resized, and so on.
     * At the end of the wait interval, the function will be called with the arguments that were passed most recently to the debounced function.
     * Pass true for the immediate argument to cause debounce to trigger the function on the leading instead of the trailing edge of the wait interval.
     * Useful in circumstances like preventing accidental double-clicks on a "submit" button from firing a second time.
     * @param func
     * @param wait
     * @param immediate
     * @return {function}
     */
    $util.debounce = function debounce(func, wait, immediate) {
        var timeout, args, context, timestamp, result;

        var later = function () {
            var last = Date.now() - timestamp;

            if (last < wait && last >= 0) {
                timeout = setTimeout(later, wait - last);
            } else {
                timeout = null;
                if (!immediate) {
                    result = func.apply(context, args);
                    if (!timeout) {
                        context = args = null;
                    }
                }
            }
        };

        return function debouncedFn() {
            context = this;
            args = arguments;
            timestamp = Date.now();
            var callNow = immediate && !timeout;
            if (!timeout) {
                timeout = setTimeout(later, wait);
            }
            if (callNow) {
                result = func.apply(context, args);
                context = args = null;
            }

            return result;
        };
    };

    var _onced = Object.create(null);
    /**
     * @param objects
     * @param flag
     * @return { Array }
     */
    $util.once = function once(objects, flag) {
        objects = arrVal(objects);
        flag = strVal(flag);

        _onced[flag] || (_onced[flag] = {});

        return objects.filter(function (obj) {
            var uid = $util.uid(obj);
            if (_onced[flag][uid] === true) {
                return false;
            }
            _onced[flag][uid] = true;
            return true;
        });
    };
    /**
     * @param objects
     * @param flag
     * @return { Array.<T> }
     */
    $util.findOnce = function findOnce(objects, flag) {
        objects = arrVal(objects);
        flag = strVal(flag);

        if (!_onced[flag]) {
            return [];
        }

        return objects.filter(function (obj) {
            var uid = $util.uid(obj);
            return _onced[flag][uid] === true;
        });
    };
    /**
     * @param objects
     * @param flag
     */
    $util.removeOnce = function removeOnce(objects, flag) {
        objects = arrVal(objects);
        flag = strVal(flag);

        if (!_onced[flag]) {
            return;
        }

        objects.forEach(function (obj) {
            var uid = $util.uid(obj);
            delete _onced[flag][uid];
        });
    };
    /**
     * @param iterable
     * @returns { Array }
     */
    $util.iterableToArray = function iterableToArray(iterable) {
        var item, array = [];

        if (iterable != null) {
            while (!(item = iterable.next()).done) {
                array.push(item.value);
            }
        }

        return array;
    };

    var rgxHttp = /^https?:(?=\/\/)/i;
    $util.isAbsolute = function isAbsolute(url) {
        url = strVal(url);
        return rgxHttp.test(url);
    };

    $util.isRelative = function isRelative(url) {
        url = strVal(url);
        return !$util.isAbsolute(url) && !$util.isSchemeRelative(url);
    };

    $util.isSchemeRelative = function isSchemeRelative(url) {
        url = strVal(url);
        return url.startsWith('//');
    };

    /**
     * NB: Has a trailing slash when having the base URL only
     * @param {string} url - An absolute or relative URL. If url is a relative URL, `base` will be used as the base URL. If url is an absolute URL, a given `base` will be ignored.
     * @param {string} [base] - The base URL to use in case url is a relative URL. If not specified, it defaults to $cms.getBaseUrl().
     * @return { URL }
     */
    $util.url = function url(url, base) {
        url = strVal(url);
        base = strVal(base) || '{$BASE_URL;}/';

        if (url.startsWith('//')) {
            // URL constructor throws on scheme-relative URLs
            url = window.location.protocol + url;
        }

        return new URL(url, base);
    };

    /*
     * @return { URL }
     */
    $util.pageUrl = function pageUrl() {
        return new URL(window.location);
    };

    var rgxProtocol = /^[a-z0-9-.]+:(?=\/\/)/i;
    /**
     * Make a URL scheme-relative
     * 'http://example.com' -> '//example.com'
     * This allows the URL to load safely on both HTTPS and HTTP
     * We typically use this function for images pulled up using the $IMG symbol (as that provides an absolute URL, which we do not want)
     * @param url
     * @returns {string}
     */
    $util.srl = function srl(url) {
        url = strVal(url);

        return $util.url(url).toString().replace(rgxProtocol, '');
    };

    /**
     * Returns a root-relative URL
     * 'http://example.com/path/to/file' -> '/path/to/file'
     * @param url
     * @return {string}
     */
    $util.rel = function rel(url) {
        url = (url instanceof URL) ? url : $util.url(url);

        return url.pathname + url.search + url.hash;
    };

    /**
     * Force a link to be clicked without user clicking it directly (useful if there's a confirmation dialog in-between their click)
     * LEGACY: Formerly click_link()
     * @param url
     * @param target
     */
    $util.navigate = function navigate(url, target) {
        var el;

        if ($util.isEl(url)) {
            el = url;
            url = '';

            if (el.localName === 'a') {
                url = el.href;
                if ('target' in el) {
                    target = el.target;
                }
            } else if (el.dataset && ('cmsHref' in el.dataset)) {
                url = el.dataset.cmsHref;
                if ('cmsTarget' in el.dataset) {
                    target = el.dataset.cmsTarget;
                }
            }
        }

        url = strVal(url);
        target = strVal(target) || '_self';

        if (!url) {
            return;
        }

        if (target === '_self') {
            window.location = url;
        } else {
            window.open(url, target);
        }
    };

    /**
     * @param source
     * @returns {*}
     */
    $util.parseJson5 = function parseJson5(source) {
        return window.JSON5.parse(strVal(source));
    };

    /**
     * @memberof $util
     */
    $util.inform = $util.log = function log() {
        if (window.$cms && window.$cms.isDevMode()) {
            return console.log.apply(console, arguments); // eslint-disable-line no-console
        }
    };

    /**
     * @memberof $util
     */
    $util.warn = function warn() {
        return console.warn.apply(console, arguments); // eslint-disable-line no-console
    };

    /**
     * @memberof $util
     */
    $util.fatal = $util.error = function error() {
        return console.error.apply(console, arguments); // eslint-disable-line no-console
    };

    /**
     * Emulates super.method() call
     * @param SuperClass
     * @param that
     * @param method
     * @param args
     * @returns {*}
     */
    function base(SuperClass, that, method, args) {
        return (args && (args.length > 0)) ? SuperClass.prototype[method].apply(that, args) : SuperClass.prototype[method].call(that);
    }

    /**
     * Inspired by goog.inherits and Babel's generated output for ES6 classes
     * @memberof $util
     * @param SubClass
     * @param SuperClass
     * @param protoProps
     */
    $util.inherits = function inherits(SubClass, SuperClass, protoProps) {
        Object.setPrototypeOf(SubClass, SuperClass);

        $util.properties(SubClass, { base: base.bind(undefined, SuperClass) });

        // Set the prototype chain to inherit from `SuperClass`
        SubClass.prototype = Object.create(SuperClass.prototype);

        protoProps || (protoProps = {});
        protoProps.constructor = SubClass;

        $util.properties(SubClass.prototype, protoProps);
    };

    /**
     * Formats a large number with commas and k/m shortening
     * @param {Number} value
     * @param {Boolean} force
     * @returns {String}
     */
    $util.makeBigNumberNice = function makeBigNumberNice(value, force) {
        if (!isNumeric(value)) {
            return value;
        }

        if (value >= 1000000) {
            if (value % 100000 == 0) {
                return ((value / 1000000) + 'm');
            }
            if (force) {
                return ((value / 1000000).toFixed(0) + 'm');
            }
        } else if (value >= 1000) {
            if (value % 100 == 0) {
                return ((value / 1000) + 'k');
            }
            if (force) {
                return ((value / 1000).toFixed(0) + 'k');
            }
        }

        return value.toLocaleString("en-US");
    }
}(window.$util || (window.$util = {})));
