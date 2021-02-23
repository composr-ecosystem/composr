(function () {
    'use strict';
    // Credit for `isSymbol` and `definePolyfill`: https://github.com/inexorabletash/polyfill/blob/master/es6.js
    function isSymbol(s) {
        return (typeof s === 'symbol') || (('Symbol' in window) && (Object.prototype.toString.call(s) === '[object Symbol]'));
    }

    function definePolyfill(obj, prop, value, override) {
        var isFunc = typeof value === 'function';

        if ((prop in obj) && !override && !window.OVERRIDE_NATIVE_FOR_TESTING) {
            return;
        }

        if (isFunc) {
            // Sanity check that functions are appropriately named (where possible)
            console.assert(isSymbol(prop) || !('name' in value) || (value.name === prop) || (value.name === prop + '_'), 'Expected function name "' + prop.toString() + '", was "' + value.name + '"');
        }

        Object.defineProperty(obj, prop, {
            value: value,
            configurable: isFunc,
            enumerable: false,
            writable: isFunc
        });
    }

    // 20.1.2.6 Number.MAX_SAFE_INTEGER
    definePolyfill(Number, 'MAX_SAFE_INTEGER', 9007199254740991); // 2^53-1

    // 20.1.2.8 Number.MIN_SAFE_INTEGER
    definePolyfill(Number, 'MIN_SAFE_INTEGER', -9007199254740991); // -2^53+1

    // Credit: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isNaN
    definePolyfill(Number, 'isNaN', function isNaN(value) {
        return value !== value;
    });

    // Credit: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isFinite
    definePolyfill(Number, 'isFinite', function isFinite(value) {
        return (typeof value === 'number') && window.isFinite(value);
    });

    // Credit: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isInteger
    definePolyfill(Number, 'isInteger', function isInteger(value) {
        return Number.isFinite(value) && (Math.floor(value) === value);
    });

    // Proposed: https://github.com/tc39/proposal-string-replace-all
    Object.defineProperty(String.prototype, 'replaceAll', {
        /**
         * @param { string } search
         * @param { string } replacement
         * @returns { string }
         */
        value: function replaceAll(search, replacement) {
            return this.split(search).join('' + replacement);
        },
        configurable: true,
        writeable: true
    });

    // Credit: https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/String/includes
    definePolyfill(String.prototype, 'includes', function includes(search, start) {
        start = +start || 0;

        if ((start + search.length) > this.length) {
            return false;
        }

        return this.indexOf(search, start) !== -1;
    });

    // Credit: https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/String/startsWith
    definePolyfill(String.prototype, 'startsWith', function startsWith(searchString, position) {
        position = +position || 0;
        return this.substr(position, searchString.length) === searchString;
    });


    // Credit: https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/String/endsWith
    definePolyfill(String.prototype, 'endsWith', function endsWith(searchString, position) {
        var subjectString = this.toString();
        if (typeof position !== 'number' || !isFinite(position) || Math.floor(position) !== position || position > subjectString.length) {
            position = subjectString.length;
        }
        position -= searchString.length;
        var lastIndex = subjectString.lastIndexOf(searchString, position);
        return lastIndex !== -1 && lastIndex === position;
    });

    // Credit: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/includes
    definePolyfill(Array.prototype, 'includes', function includes(searchElement /*, fromIndex*/) {
        var isSearchNaN = Number.isNaN(searchElement); // Needs a special check since NaN !== NaN

        if (this == null) {
            throw new TypeError('Array.prototype.includes called with invalid context');
        }

        var O = Object(this);
        var len = parseInt(O.length, 10) || 0;
        if (len === 0) {
            return false;
        }
        var n = parseInt(arguments[1], 10) || 0;
        var k;
        if (n >= 0) {
            k = n;
        } else {
            k = len + n;
            if (k < 0) {
                k = 0;
            }
        }
        var currentElement;
        while (k < len) {
            currentElement = O[k];
            if ((searchElement === currentElement) || (isSearchNaN && Number.isNaN(currentElement))) {
                return true;
            }
            k++;
        }
        return false;
    });

    // Source: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign#Polyfill
    // This polyfill doesn't support symbol properties, since ES5 doesn't have symbols anyway:
    if (typeof Object.assign !== 'function') {
        // Must be writable: true, enumerable: false, configurable: true
        Object.defineProperty(Object, 'assign', {
            value: function assign(target, varArgs) { // .length of function is 2
                'use strict';
                if (target == null) { // TypeError if undefined or null
                    throw new TypeError('Cannot convert undefined or null to object');
                }

                var to = Object(target);

                for (var index = 1; index < arguments.length; index++) {
                    var nextSource = arguments[index];

                    if (nextSource != null) { // Skip over if undefined or null
                        for (var nextKey in nextSource) {
                            // Avoid bugs when hasOwnProperty is shadowed
                            if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                                to[nextKey] = nextSource[nextKey];
                            }
                        }
                    }
                }
                return to;
            },
            writable: true,
            configurable: true
        });
    }

    if (NodeList.prototype.forEach === undefined) {
        NodeList.prototype.forEach = Array.prototype.forEach;
    }

    if (Element.prototype.remove === undefined) {
        Element.prototype.remove = function remove() {
            if (this.parentNode !== null) {
                this.parentNode.removeChild(this);
            }
        };
    }

    if (CharacterData.prototype.remove === undefined) {
        CharacterData.prototype.remove = function remove() {
            if (this.parentNode !== null) {
                this.parentNode.removeChild(this);
            }
        };
    }

    if (DocumentType.prototype.remove === undefined) {
        DocumentType.prototype.remove = function remove() {
            if (this.parentNode !== null) {
                this.parentNode.removeChild(this);
            }
        };
    }
}());
