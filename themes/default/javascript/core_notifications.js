(function ($cms, $util, $dom) {
    'use strict';

    var $coreNotifications = window.$coreNotifications = {};

    /*
     Poll for notifications (and unread PTs)
     */
    window.notificationsAlreadyPresented || (window.notificationsAlreadyPresented = {});
    (window.NOTIFICATION_POLL_FREQUENCY != null) || (window.NOTIFICATION_POLL_FREQUENCY = $cms.configOption('notification_poll_frequency'));
    (window.notificationsTimeBarrier != null) || (window.notificationsTimeBarrier = 0);

    $cms.views.NotificationButtons = NotificationButtons;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function NotificationButtons(params) {
        NotificationButtons.base(this, 'constructor', arguments);

        this.disableFormEl = this.$('#ndisable-' + params.notificationId);
        this.enableFormEl = this.$('#nenable-' + params.notificationId);
    }

    $util.inherits(NotificationButtons, $cms.View, /**@lends NotificationButtons#*/{
        events: function () {
            return {
                'click .js-show-disable-form': 'showDisableForm',
                'click .js-show-enable-form': 'showEnableForm'
            };
        },
        showDisableForm: function () {
            $dom.show(this.disableFormEl);
            $dom.hide(this.enableFormEl);
        },
        showEnableForm: function () {
            $dom.hide(this.disableFormEl);
            $dom.show(this.enableFormEl);
        }
    });

    $cms.templates.notificationPoller = function notificationPoller(params) {
        notificationPollerInit(params.timestamp);

        function notificationPollerInit(timeBarrier) {
            $cms.requireJavascript('sound');

            window.notificationsTimeBarrier = timeBarrier;

            setInterval($coreNotifications.pollForNotifications, window.NOTIFICATION_POLL_FREQUENCY * 1000);

            var webNotificationsButton = document.getElementById('web-notifications-button');
            if (webNotificationsButton) {
                webNotificationsButton.addEventListener('click', explicitNotificationsEnableRequest);
            }
        }

        // We attach to an onclick handler, to enable desktop notifications later on; we need this as we cannot call requestPermission out of the blue
        function explicitNotificationsEnableRequest() {
            if ($cms.configOption('notification_desktop_alerts')) {
                window.notify.requestPermission();
            }
        }
    };

    $cms.templates.blockTopNotifications_webNotifications = function (params, container) { // eslint-disable-line camelcase
        window.maxNotificationsToShow = Number(params.max) || 0;

        $dom.on(container, 'click', '.js-click-notifications-mark-all-read', function (e) {
            e.preventDefault();
            notificationsMarkAllRead();
        });

        $dom.on(container, 'click', '.js-click-find-url-tab', function () {
            $cms.ui.findUrlTab();
        });

        function notificationsMarkAllRead() {
            var eCount = document.getElementById('live-notifications-count');
            if (eCount) {
                $dom.html(eCount, '0');
                eCount.parentNode.className = eCount.parentNode.className.replace(/count-\d+/, 'count-0');
            }

            var url = '{$FIND_SCRIPT_NOHTTP;,notifications}?type=mark_all_read';
            if (window.maxNotificationsToShow !== undefined) {
                url += '&max=' + window.maxNotificationsToShow;
            }
            url += '&time_barrier=' + encodeURIComponent(window.notificationsTimeBarrier);
            url += '&forced_update=1';
            url += $cms.keep();
            $cms.doAjaxRequest(url, [_pollForNotifications]);
        }
    };

    $cms.templates.blockTopNotifications_pts = function (params, container) { // eslint-disable-line camelcase
        $dom.on(container, 'click', '.js-click-find-url-tab', function () {
            $cms.ui.findUrlTab();
        });
    };

    $cms.templates.notificationsTree = function notificationsTree(params, tableRow) {
        $dom.on(tableRow, 'click', '.js-click-copy-advanced-notifications', function () {
            advancedNotificationsCopyUnder(tableRow);
        });

        function advancedNotificationsCopyUnder(row) {
            var inputsFrom = row.querySelectorAll('input'),
                parentDepth = $dom.css(row.querySelector('th'), 'padding-left'),
                childDepth, inputsTo;

            // eslint-disable-next-line no-constant-condition
            while (true) {
                row = $dom.next(row, 'tr');

                if (!row) {
                    return; // Should not happen
                }

                childDepth = $dom.css(row.querySelector('th'), 'padding-left');

                if (childDepth === parentDepth) {
                    break; // Don't allow to progress one further
                }

                inputsTo = row.querySelectorAll('input');
                for (var j = 0; j < inputsTo.length; j++) {
                    if (inputsTo[j].type === 'checkbox') {
                        inputsTo[j].checked = inputsFrom[j].checked;
                    }
                }
            }
        }
    };

    $cms.templates.notificationWeb = function notificationWeb(params, container) {
        $dom.on(container, 'click', '.js-click-poll-for-notifications', function () {
            $coreNotifications.pollForNotifications(true, true);
        });
    };

    $cms.templates.notificationTypes_item = function notificationTypes_item(params, container) { // eslint-disable-line camelcase
        $dom.on(container, 'click', '.js-click-handle-ntype-tick', function (e, checkbox) {
            var raw = Number(checkbox.dataset.tpRaw) || 0,
                parentRow = $dom.closest(checkbox, 'tr'),
                inputEls = $dom.$$(parentRow, 'input'),
                firstInput = inputEls[0],
                secondInput = inputEls[1];

            if ((raw === -1) || (raw === -2) || (raw === -4)) { // Statistical/choice/default (from admin_notifications lock-down) will unselect all else
                for (var i = 0; i < inputEls.length; i++) {
                    if ((inputEls[i] !== checkbox) && (inputEls[i].type === 'checkbox')) {
                        inputEls[i].checked = false;
                    }
                }
            } else {
                if (firstInput && (firstInput.name.includes('CHOICE') || firstInput.name.includes('INBUILT_DEFAULT'))) {
                    firstInput.checked = false;
                }

                if (secondInput && secondInput.name.includes('STATISTICAL')) {
                    secondInput.checked = false;
                }
            }
        });
    };

    var lockedPolling = false;
    $coreNotifications.pollForNotifications = function pollForNotifications(forcedUpdate, delay) {
        forcedUpdate = Boolean(forcedUpdate);
        delay = Boolean(delay);

        if (delay) {
            setTimeout(function () {
                $coreNotifications.pollForNotifications(forcedUpdate);
            }, 1000);
            return;
        }

        if (document.hidden) {
            return; /* Don't hurt server performance needlessly when running in a background tab - let an e-mail notification alert them instead */
        }

        if (lockedPolling) {
            return;
        }
        lockedPolling = true;

        var url = '{$FIND_SCRIPT_NOHTTP;,notifications}?type=poller';
        if (window.maxNotificationsToShow !== undefined) {
            url += '&max=' + window.maxNotificationsToShow;
        }
        url += '&time_barrier=' + encodeURIComponent(window.notificationsTimeBarrier);
        if (forcedUpdate) {
            url += '&forced_update=1';
        }
        url += $cms.keep();
        $cms.doAjaxRequest(url, [_pollForNotifications]);
    };

    function _pollForNotifications(responseXml) {
        lockedPolling = false;

        if (!responseXml || responseXml.getElementsByTagName === undefined) {
            return; // Some kind of error
        }

        var timeNode = responseXml.querySelector('time');
        window.notificationsTimeBarrier = parseInt($dom.html(timeNode));

        // HTML5 notification API

        var alerts;

        alerts = responseXml.getElementsByTagName('web_notification');
        for (var i = 0; i < alerts.length; i++) {
            displayAlert(alerts[i]);
        }

        alerts = responseXml.getElementsByTagName('pt');
        for (var j = 0; j < alerts.length; j++) {
            displayAlert(alerts[j]);
        }

        // Show in the software directly, if possible

        var spot, display, button, count, unread;

        spot = $dom.$('#web-notifications-spot');
        if (spot) {
            display = responseXml.getElementsByTagName('display_web_notifications');
            button = $dom.$('#web-notifications-button');
            count = $dom.$('#live-notifications-count');
            if (display[0]) {
                unread = responseXml.getElementsByTagName('unread_web_notifications');
                $dom.html(spot, $dom.html(display[0]));
                $dom.html(count, $dom.html(unread[0]));
                button.className = button.className.replace(/count-\d+/, 'count-' + $dom.html(unread[0]));
            }
        }

        spot = $dom.$('#pts-spot');
        if (spot) {
            display = responseXml.getElementsByTagName('display_pts');
            button = $dom.$('#pts-button');
            count = $dom.$('#live-pts-count');
            if (display[0]) {
                unread = responseXml.getElementsByTagName('unread_pts');
                $dom.html(spot, $dom.html(display[0]));
                $dom.html(count, $dom.html(unread[0]));
                button.className = button.className.replace(/count-\d+/, 'count-' + $dom.html(unread[0]));
            }
        }

        function displayAlert(notification) {
            var id = notification.getAttribute('id');

            if (window.notificationsAlreadyPresented[id] !== undefined) {
                // Already handled this one
                return;
            }

            // Play sound, if requested
            var sound = notification.getAttribute('sound');
            if (!sound) {
                sound = (parseInt(notification.getAttribute('priority')) < 3) ? 'on' : 'off';
            }
            var notificationCode = notification.getAttribute('notification_code');
            if (sound === 'on' && notificationCode !== 'ticket_reply' && notificationCode !== 'ticket_reply_staff') {
                var goFunc = function goFunc() {
                    var soundObject = window.soundManager.createSound({url: $util.rel('data/sounds/message_received.mp3') });
                    if (soundObject && document.hasFocus()/*don't want multiple tabs all pinging*/) {
                        soundObject.play();
                    }
                };

                if (!window.soundManager.setupOptions.url) {
                    window.soundManager.setup({onready: goFunc, url: $util.rel('data/soundmanager'), debugMode: false});
                } else {
                    goFunc();
                }
            }

            // Show desktop notification
            if ($cms.configOption('notification_desktop_alerts') && window.notify.isSupported) {
                var icon = $util.srl('{$IMG;,favicon}');
                var title = '{!notifications:DESKTOP_NOTIFICATION_SUBJECT;^}';
                title = title.replace(/\\{1\\}/, notification.getAttribute('subject'));
                title = title.replace(/\\{2\\}/, notification.getAttribute('from_username'));
                var body = '';//notification.getAttribute('rendered'); Looks ugly
                if (window.notify.permissionLevel() === window.notify.PERMISSION_GRANTED) {
                    var notificationWrapper = window.notify.createNotification(title, { icon: icon, body: body, tag: $cms.getSiteName() + '__' + id });
                    if (notificationWrapper) {
                        window.addEventListener('focus', function () {
                            notificationWrapper.close();
                        });

                        notificationWrapper.notification.addEventListener('click', function () {
                            try {
                                focus();
                            } catch (ignore) {
                                // continue
                            }
                        });
                    }
                } else {
                    window.notify.requestPermission(); // Probably won't actually work (silent fail), as we're not running via a user-initiated event; this is why we have explicit_notifications_enable_request called elsewhere
                }
            }

            // Mark done
            window.notificationsAlreadyPresented[id] = true;
        }
    }
}(window.$cms, window.$util, window.$dom));

// LEGACY

/**
 * Copyright 2012 Tsvetan Tsvetkov
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Author: Tsvetan Tsvetkov (tsekach@gmail.com)
 */
(function () {
    'use strict';

    if (!$cms.configOption('notification_desktop_alerts')) {
        return;
    }

    /*
     Safari native methods required for Notifications do NOT run in strict mode.
     */
    //"use strict";
    var PERMISSION_DEFAULT = "default",
        PERMISSION_GRANTED = "granted",
        PERMISSION_DENIED = "denied",
        PERMISSION = [PERMISSION_GRANTED, PERMISSION_DEFAULT, PERMISSION_DENIED],
        defaultSetting = {
            pageVisibility: false,
            autoClose: 5000
        },
        empty = {},
        emptyString = "",
        isSupported = (function () {
            var isSupported = false;
            /*
             * Use try {} catch() {} because the check for IE may throws an exception
             * if the code is run on browser that is not Safari/Chrome/IE or
             * Firefox with html5notifications plugin.
             *
             * Also, we canNOT detect if msIsSiteMode method exists, as it is
             * a method of host object. In IE check for existing method of host
             * object returns undefined. So, we try to run it - if it runs
             * successfully - then it is IE9+, if not - an exceptions is thrown.
             */
            try {
                isSupported = Boolean(window.Notification || /* Chrome & ff-html5notifications plugin */window.webkitNotifications || /* Firefox Mobile */navigator.mozNotification || /* IE9+ */(window.external && window.external.msIsSiteMode() !== undefined));
            } catch (e) {
                // continue
            }
            return isSupported;
        }()),
        ieVerification = Math.floor((Math.random() * 10) + 1),
        isFunction = function (value) {
            return (value && (value).constructor === Function);
        },
        isString = function (value) {
            return (value && (value).constructor === String);
        },
        isObject = function (value) {
            return (value && (value).constructor === Object);
        },
        /**
         * Dojo Mixin
         */
        mixin = function (target, source) {
            var name, s;
            for (name in source) {
                s = source[name];
                if ((target[name] === undefined) || (!target.name) || (target[name] !== s && ((empty[name] === undefined) || (!empty[name]) || empty[name] !== s))) {
                    target[name] = s;
                }
            }
            return target; // Object
        },
        noop = function () {
        },
        settings = defaultSetting;

    function getNotification(title, options) {
        var notification;
        if (window.Notification) {
            notification = new window.Notification(title, {
                /* The notification's icon - For Chrome in Windows, Linux & Chrome OS */
                icon: isString(options.icon) ? options.icon : options.icon.x32,
                /* The notification's subtitle. */
                body: options.body || emptyString,
                /*
                 The notification's unique identifier.
                 This prevents duplicate entries from appearing if the user has multiple instances of your website open at once.
                 */
                tag: options.tag || emptyString
            });
        } else if (window.webkitNotifications) { /* FF with html5Notifications plugin installed */
            notification = window.webkitNotifications.createNotification(options.icon, title, options.body);
            notification.tag = options.tag || emptyString;
            notification.show();
        } else if (navigator.mozNotification) { /* Firefox Mobile */
            notification = navigator.mozNotification.createNotification(title, options.body, options.icon);
            notification.tag = options.tag || emptyString;
            notification.show();
        } else if (window.external && window.external.msIsSiteMode()) { /* IE9+ */
            //Clear any previous notifications
            window.external.msSiteModeClearIconOverlay();
            window.external.msSiteModeSetIconOverlay($util.srl('{$IMG;,notifications/notifications}'), title);
            window.external.msSiteModeActivate();
            notification = {
                "ieVerification": ++ieVerification
            };
        } else {
            if (window.focus !== undefined) {
                try {
                    window.focus();
                }
                catch (e) {
                    // continue
                }
            }
        }
        return notification;
    }

    function getWrapper(notification) {
        return {
            notification: notification,
            close: function () {
                if (notification) {
                    if (notification.close) {
                        //http://code.google.com/p/ff-html5notifications/issues/detail?id=58
                        notification.close();
                    } else if (window.external && window.external.msIsSiteMode()) {
                        if (notification.ieVerification === ieVerification) {
                            window.external.msSiteModeClearIconOverlay();
                        }
                    }
                }
            }
        };
    }

    function requestPermission(callback) {
        if (!isSupported) {
            return;
        }
        var callbackFunction = isFunction(callback) ? callback : noop;
        if (window.webkitNotifications && window.webkitNotifications.checkPermission) {
            /*
             * Chrome 23 supports window.Notification.requestPermission, but it
             * breaks the browsers, so use the old-webkit-prefixed
             * window.webkitNotifications.checkPermission instead.
             *
             * Firefox with html5notifications plugin supports this method
             * for requesting permissions.
             */
            window.webkitNotifications.requestPermission(callbackFunction);
        } else if (window.Notification && window.Notification.requestPermission) {
            window.Notification.requestPermission(callbackFunction);
        }
    }

    function permissionLevel() {
        var permission;
        if (!isSupported) {
            return;
        }
        if (window.Notification && window.Notification.permissionLevel) {
            //Safari 6
            permission = window.Notification.permissionLevel();
        } else if (window.webkitNotifications && window.webkitNotifications.checkPermission) {
            //Chrome & Firefox with html5-notifications plugin installed
            permission = PERMISSION[window.webkitNotifications.checkPermission()];
        } else if (navigator.mozNotification) {
            //Firefox Mobile
            permission = PERMISSION_GRANTED;
        } else if (window.Notification && window.Notification.permission) {
            // Firefox 23+
            permission = window.Notification.permission;
        } else if (window.external && (window.external.msIsSiteMode() !== undefined)) { /* keep last */
            //IE9+
            permission = window.external.msIsSiteMode() ? PERMISSION_GRANTED : PERMISSION_DEFAULT;
        }
        return permission;
    }

    function config(params) {
        if (params && isObject(params)) {
            mixin(settings, params);
        }
        return settings;
    }

    function isDocumentHidden() {
        return settings.pageVisibility ? (document.hidden || document.msHidden || document.mozHidden || document.webkitHidden) : true;
    }

    function createNotification(title, options) {
        var notification,
            notificationWrapper;
        /*
         Return undefined if notifications are not supported.

         Return undefined if no permissions for displaying notifications.

         Title and icons are required. Return undefined if not set.
         */
        if (isSupported && isDocumentHidden() && isString(title) && (options && (isString(options.icon) || isObject(options.icon))) && (permissionLevel() === PERMISSION_GRANTED)) {
            notification = getNotification(title, options);
        }
        notificationWrapper = getWrapper(notification);
        //Auto-close notification
        if (Number(settings.autoClose) !== 0 && notification && !notification.ieVerification && notification.addEventListener) {
            notification.addEventListener('show', function () {
                var notification = notificationWrapper;
                setTimeout(function () {
                    notification.close();
                }, settings.autoClose);
            });
        }
        return notificationWrapper;
    }

    window.notify = {
        PERMISSION_DEFAULT: PERMISSION_DEFAULT,
        PERMISSION_GRANTED: PERMISSION_GRANTED,
        PERMISSION_DENIED: PERMISSION_DENIED,
        isSupported: isSupported,
        config: config,
        createNotification: createNotification,
        permissionLevel: permissionLevel,
        requestPermission: requestPermission
    };

    if (isFunction(Object.seal)) {
        Object.seal(window.notify);
    }
}());
