(function () {
    'use strict';

    window.$googleAnalytics || (window.$googleAnalytics = {});

    var gaLoadPromise;
    /**
     * @returns { Promise }
     */
    window.$googleAnalytics.load = function () {
        if (gaLoadPromise != null) {
            return gaLoadPromise;
        }

        gaLoadPromise = new Promise(function (resolve) {
            window.gapi || (window.gapi = {});

            if (!window.gapi.analytics) {
                window.gapi.analytics = {
                    q: [],
                    ready: function (cb) {
                        this.q.push(cb);
                    }
                };
            }

            window.gapi.analytics.ready(resolve);

            $cms.requireJavascript(['https://www.google.com/jsapi', 'https://apis.google.com/js/platform.js']).then(function () {
                window.gapi.load('analytics');
            });
        });
        return gaLoadPromise;
    };

    $cms.templates.googleAnalyticsTabs = function (params, container) {
        setTimeout(function () {
            var firstTabId = params.firstTabId;

            if (firstTabId) {
                $dom.trigger(document.getElementById(firstTabId), 'cms:ga:init');
            }
        }, 0);

        $dom.on(container, 'click', '.js-onclick-trigger-tab-ga-init', function (e, clicked) {
            var tabId = clicked.dataset.tpTabId;

            if (tabId && document.getElementById(tabId)) {
                $dom.trigger(document.getElementById(tabId), 'cms:ga:init');
            }
        });
    };

    var SIMULTANEOUS_LIMIT = 5;
    var pendingPromises = new Set();
    var queuedPromiseFactoryFns = [];

    // See MANTIS-4028: Google Analytics graphs don't all load
    // Google doesn't like too many simultaneous API requests so we need to rate limit ourselves
    function rateLimitedPromiseCall(promiseFactoryFn) {
        queuedPromiseFactoryFns.push(promiseFactoryFn);
        processPromiseQueue();
    }

    function processPromiseQueue() {
        if ((pendingPromises.size >= SIMULTANEOUS_LIMIT) || !queuedPromiseFactoryFns.length) {
            return;
        }

        var fns = queuedPromiseFactoryFns.splice(0, SIMULTANEOUS_LIMIT);

        fns.forEach(function (promiseFactoryFn) {
            var promise = promiseFactoryFn();

            function onFinally() {
                pendingPromises.delete(promise);
                processPromiseQueue();
            }

            promise.then(onFinally, onFinally);

            pendingPromises.add(promise);
        });
    }

    var alreadyInitializedGas = new WeakSet();
    $cms.templates.googleAnalytics = function (params, container) {
        $dom.on(container, 'cms:ga:init cms:ga:reinit', function (e) {
            if (e.target !== container) {
                return;
            }

            if (e.type === 'cms:ga:reinit') {
                alreadyInitializedGas.delete(container);
            }

            if (alreadyInitializedGas.has(container)) {
                return;
            }

            alreadyInitializedGas.add(container);

            window.$googleAnalytics.load().then(function () {
                var GID = { 'query': { 'ids': 'ga:' + params.propertyId } };

                // Authorize the user==
                window.gapi.analytics.auth.authorize({
                    'container': 'auth-button-' + params.id,
                    'clientid': params.clientId,
                    'serverAuth': { access_token: params.accessToken }, // eslint-disable-line camelcase
                });

                var query = {
                    'dimensions': strVal(params.dimension),
                    'metrics': (params.metrics || []).join(','),
                    'start-date': strVal((e.days == null) ? params.days : e.days) + 'daysAgo',
                    'end-date': 'yesterday',
                };

                if (params.extra != null) {
                    Object.assign(query, params.extra);
                }

                var chartOptions = {
                    'reportType': 'ga',
                    'query': query,
                    'chart': {
                        'type': params.chartType,
                        'container': 'timeline-' + params.id,
                        'options': {'width': '100%'},
                    },
                };

                rateLimitedPromiseCall(function () {
                    var isResolved = false;
                    return new Promise(function (resolve, reject) {
                        // Create the timeline chart
                        var timeline = new window.gapi.analytics.googleCharts.DataChart(chartOptions);

                        timeline.set(GID).execute();
                        timeline.once('success', function () {
                            document.getElementById('loading-' + params.id).style.display = 'none';
                            isResolved = true;
                            resolve();
                        });

                        setTimeout(function () {
                            if (!isResolved) {
                                // Some error may have occurred, reject so the queue can continue
                                reject();
                            }
                        }, 10000);
                    });
                });
            });
        });

        if (!params.underTab) {
            $dom.trigger(container, 'cms:ga:init');
        }
    };

    $cms.templates.googleTimePeriods = function (params, container) {
        $dom.on(container, 'change', '.js-select-onchange-trigger-tab-ga-reinit', function (e, select) {
            var tabId = select.dataset.tpTabId;

            if (tabId && document.getElementById(tabId) && select.value) {
                $dom.trigger(document.getElementById(tabId), 'cms:ga:reinit', { days: select.value });
            }
        });
    };
}());
