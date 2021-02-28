(function ($dom) {
    'use strict';

    /**
     * @memberof $dom
     * @type { Promise }
     */
    $dom.init = new Promise(function (resolve) {
        $dom._resolveInit = resolve;
    });
    
    /**
     * @memberof $dom
     * @type { Promise }
     */
    $dom.ready = new Promise(function (resolve) {
        $dom._resolveReady = resolve;
    });

    /**
     * @memberof $dom
     * @type { Promise }
     */
    $dom.load = new Promise(function (resolve) {
        $dom._resolveLoad = resolve;
    });
    
    /**
     * Required for $cms.requireCss and $cms.requireJavascript() to work properly as DOM does not currently provide any way to check if a particular element has been already loaded
     * @memberof $dom
     * @type { WeakMap }
     */
    $dom.elementsLoaded = new WeakMap();
    
    document.addEventListener('load', resourceLoadListener, /*useCapture*/true);
    document.addEventListener('error', resourceLoadListener, /*useCapture*/true);
    
    function resourceLoadListener(event) {
        var loadedEl = event.target, 
            hasLoaded = (event.type === 'load');

        if (!loadedEl) {
            return;
        }

        $dom.elementsLoaded.set(loadedEl, hasLoaded);
    }

    // Are we dealing with a touch device?
    var isTouchEnabled = 'ontouchstart' in document.documentElement;

    document.documentElement.classList.toggle('is-touch-enabled', isTouchEnabled);

    // Do we have a mouse?
    // @see https://css-tricks.com/touch-devices-not-judged-size/
    // @see https://medium.com/@ferie/detect-a-touch-device-with-only-css-9f8e30fa1134
    var isMouseEnabled = window.matchMedia('(any-hover:hover) and (any-pointer:fine)').matches || (navigator.userAgent.toLowerCase().indexOf('msie') !== -1) || (navigator.userAgent.toLowerCase().indexOf('trident') !== -1);

    document.documentElement.classList.toggle('is-mouse-enabled', isMouseEnabled);

    if (isTouchEnabled && isMouseEnabled) {
        document.documentElement.classList.add('is-hybrid-device');
    } else if (!isTouchEnabled) {
        document.documentElement.classList.add('is-mouse-only');
    } else if (!isMouseEnabled) {
        document.documentElement.classList.add('is-touch-only');
    }

    // Is the document scrolled down?
    document.documentElement.classList.toggle('is-scrolled', window.scrollY > 0);

    window.addEventListener('scroll', function () {
        document.documentElement.classList.toggle('is-scrolled', window.scrollY > 0);
    });

    // Prevent url change for clicks on anchor tags with a placeholder href
    window.addEventListener('click', function (e) {
        var anchor = e.target;
        
        // A child elemenet within an <a> element might've been clicked
        while ((anchor.localName !== 'a') && anchor.parentElement) {
            anchor = anchor.parentElement;
        }
        
        if ((anchor.localName === 'a') && (anchor.getAttribute('href') === '#!')) {
            e.preventDefault();
        }
    }, /*useCapture*/true);

    // Prevent form submission for forms with a placeholder action
    window.addEventListener('submit', function (e) {
        var form = e.target;
        
        if (form.getAttribute('action') === '#!') {
            e.preventDefault();
        }
    }, /*useCapture*/true);
    
    // Prevent form submission until the DOM is ready
    function preventFormSubmissionListener(e) {
        e.preventDefault();
        window.alert('Please wait for the page to load then try again.');
    }

    window.addEventListener('submit', preventFormSubmissionListener, /*useCapture*/true);

    $dom.ready.then(function () {
        // Allow form submissions after DOM ready
        window.removeEventListener('submit', preventFormSubmissionListener, /*useCapture*/true);
    });
}(window.$dom || (window.$dom = {})));
