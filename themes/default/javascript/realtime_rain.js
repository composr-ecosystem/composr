(function ($cms, $util, $dom) {
    'use strict';

    var POLL_FREQUENCY = 10000;

    var $realtimeRain = window.$realtimeRain = {};

    $cms.templates.realtimeRainOverlay = function (params, container) {
        window.minTime = Number(params.minTime) || 0;
        window.paused = false;
        window.bubbleGroups = {};
        window.totalLines = 0;
        window.bubbleTimer1 = null;
        window.bubbleTimer2 = null;

        startRealtimeRain();

        $dom.on(container, 'mouseover mouseout', '.js-hover-window-pause', function (e, target) {
            if (target.contains(e.relatedTarget)) {
                return;
            }

            if (e.type === 'mouseover') {
                if (!window.paused) {
                    target.pausing = true;
                    window.paused = true;
                }
            } else {
                if (target.pausing) {
                    target.pausing = false;
                    window.paused = false;
                }
            }
        });

        $dom.on(container, 'mouseover', '.js-mouseover-set-time-line-position', function () {
            setTimeLinePosition(window.currentTime);
        });

        $dom.on(container, 'click', '.js-click-toggle-window-pausing', function (e, btn) {
            window.paused = !window.paused;
            btn.classList.toggle('paused', window.paused);
        });

        $dom.on(container, 'mousemove', '.js-mousemove-timeline-click', function () {
            timelineClick(true);
        });

        $dom.on(container, 'click', '.js-click-timeline-click', function () {
            timelineClick(false);
        });

        $dom.on(container, 'mouseover mouseout', '.js-hover-toggle-real-time-indicator', function (e, target) {
            if (!target.contains(e.relatedTarget)) {
                window.disableRealTimeIndicator = (e.type === 'mouseover');
            }
        });

        $dom.on(container, 'click', '.js-click-rain-slow-down', function () {
            window.timeWindow = window.timeWindow / 1.2;
        });

        $dom.on(container, 'click', '.js-click-rain-slow-up', function () {
            window.timeWindow = window.timeWindow * 1.2;
        });
    };

    $cms.templates.realtimeRainBubble = function (params) {
        window.pendingEvalFunction = function (el) { // In webkit you can't get a node until it's been closed, so we need to set our code into a function and THEN run it
            if (params.tickerText !== undefined) {
                setTimeout(function () {
                    if (document.getElementById('news-go-here')) {
                        $dom.html('#news-go-here', params.tickerText);
                    }
                }, params.relativeTimestamp * 1000);
            }
            // Set up extra attributes
            el.timeOffset = params.relativeTimestamp;
            el.linesFor = [];

            if (params.groupId !== undefined) {
                el.linesFor.push(params.groupId);
            }

            if ((params.specialIcon !== undefined) && (params.specialIcon === 'special-icon')) {
                el.iconMultiplicity = params.multiplicity;
            }
        };
    };

    // Handle the realtime_rain button on the bottom bar
    $realtimeRain.load = function load() {
        var img = $dom.$('#realtime-rain-img');

        var e = $dom.$('#real-time-surround');
        if (e) { // Clicked twice - so now we close it
            bubblesTidyUp();
            if (window.bubbleTimer1) {
                clearInterval(window.bubbleTimer1);
                window.bubbleTimer1 = null;
            }

            if (window.bubbleTimer2) {
                clearInterval(window.bubbleTimer2);
                window.bubbleTimer2 = null;
            }

            if (e.parentNode) {
                e.parentNode.remove();
            }

            $cms.setIcon(img, 'tool_buttons/realtime_rain_on', '{$IMG;,icons/tool_buttons/realtime_rain_on}');
            return false;
        }

        $cms.setIcon(img, 'tool_buttons/realtime_rain_off', '{$IMG;,icons/tool_buttons/realtime_rain_off}');

        var tmpElement = document.getElementById('realtime-rain-img-loader');
        if (tmpElement) {
            tmpElement.parentNode.removeChild(tmpElement);
        }

        img.classList.remove('footer-button-loading');

        var x = document.createElement('div');
        document.body.appendChild(x);

        $cms.loadSnippet('realtime_rain_load').then(function (html) {
            $dom.html(x, html);
            e = document.getElementById('real-time-surround');
            e.style.position = 'absolute';
            e.style.zIndex = 100;
            e.style.left = 0;
            e.style.top = 0;
            e.style.width = '100%';
            e.style.height = ($dom.getWindowScrollHeight() - 70) + 'px';
            $dom.smoothScroll(0);

            startRealtimeRain();
        });
    };

    // Called to start the animation
    function startRealtimeRain() {
        var newsTicker = document.getElementById('news-ticker'),
            loadingIcon = document.getElementById('loading-icon');

        if (loadingIcon) {
            loadingIcon.style.display = 'block';
        }

        // Initial timing
        window.currentTime = timeNow() - 10;
        window.timeWindow = 10;

        // Initial events
        getMoreEvents(window.currentTime + 1, window.currentTime + window.timeWindow); // note the +1 is because the time window is inclusive

        // Querying events regularly
        window.bubbleTimer1 = setInterval(function () {
            if (window.paused) {
                return;
            }

            getMoreEvents(window.currentTime + 1, window.currentTime + window.timeWindow);
        }, POLL_FREQUENCY);

        // Updating timeline
        window.bubbleTimer2 = setInterval(function () {
            if (window.paused) {
                return;
            }
            if (window.timeWindow + window.currentTime > timeNow()) {
                window.timeWindow = 10;
                window.currentTime = timeNow() - 10;
            }
            if ((window.disableRealTimeIndicator === undefined) || (!window.disableRealTimeIndicator)) {
                setTimeLinePosition(window.currentTime);
            }
            window.currentTime += window.timeWindow / 10.0;
        }, 1000);
    }

    function getMoreEvents(from, to) {
        if (document.hidden) {
            return; /* Don't hurt server performance needlessly when running in a background tab - let an e-mail notification alert them instead */
        }

        from = Math.round(from);
        to = Math.round(to);

        var url = '{$FIND_SCRIPT_NOHTTP;,realtime_rain}?from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to) + $cms.keep();
        $cms.doAjaxRequest(url, receivedEvents);
    }

    function receivedEvents(responseXml) {
        var ajaxResult = responseXml && responseXml.querySelector('result'),
            loadingIcon = document.getElementById('loading-icon');

        if (!ajaxResult) {
            return;
        }

        if (loadingIcon) {
            loadingIcon.style.display = 'none';
        }

        var bubbles = document.getElementById('bubbles-go-here');
        if (!bubbles) {
            return; // Unloaded
        }

        var maxHeight = bubbles.parentNode.offsetHeight;
        var totalVerticalSlots = maxHeight / 183;
        var heightPerSecond = maxHeight / 10;
        var frameDelay = (1000 / heightPerSecond) / 1.1; // 1.1 is a fudge factor to reduce chance of overlap (creates slight inaccuracy in spacing though)

        var windowWidth = $dom.getWindowWidth(),
            elements = Array.from(ajaxResult.children),
            leftPos = 25;

        elements.some(function (element) {
            var _clonedMessage, clonedMessage;

            if (element.localName !== 'div') {
                return; // (continue)
            }

            // Set up HTML (difficult, as we are copying from XML)
            _clonedMessage = element;

            try {
                _clonedMessage = document.importNode(element, true);
            } catch (ignore) {}

            clonedMessage = $dom.create('div', {
                id: _clonedMessage.getAttribute('id'),
                className: _clonedMessage.getAttribute('class'),
                html: $dom.html(_clonedMessage)
            });

            leftPos += (windowWidth > 900) ? 200 : 20;
            if (leftPos >= windowWidth) {
                // Too much!
                return true; // (break)
            }
            setTimeout(function () {
                window.pendingEvalFunction(clonedMessage);

                // Set positioning (or break-out if we have too many bubbles to show)
                clonedMessage.style.position = 'absolute';
                clonedMessage.style.zIndex = 50;
                clonedMessage.style.left = leftPos + 'px';
                bubbles.appendChild(clonedMessage);
                var verticalSlot = Math.round(totalVerticalSlots * clonedMessage.timeOffset / window.timeWindow);
                clonedMessage.style.top = (-(verticalSlot + 1) * clonedMessage.offsetHeight) + 'px';

                // JS events, for pausing and changing z-index
                clonedMessage.addEventListener('mouseover', function () {
                    clonedMessage.style.zIndex = 160;
                    if (!window.paused) {
                        clonedMessage.pausing = true;
                        window.paused = true;
                    }
                });
                clonedMessage.addEventListener('mouseout', function () {
                    clonedMessage.style.zIndex = 50;
                    if (clonedMessage.pausing) {
                        clonedMessage.pausing = false;
                        window.paused = false;
                    }
                });

                // Draw lines and e-mails animation (after delay, so that we know it's rendered by then and hence knows full coordinates)
                setTimeout(function () {
                    if ((clonedMessage.linesFor === undefined) || (clonedMessage.iconMultiplicity === undefined)) {
                        return;
                    }

                    var num = clonedMessage.iconMultiplicity,
                        mainIcon = clonedMessage.querySelector('.special-icon'),
                        iconSpot = $dom.$('#real-time-surround');

                    if ($dom.findPosY(iconSpot, true) > 0) {
                        iconSpot = iconSpot.parentNode;
                    }
                    for (var x = 0; x < num; x++) {
                        setTimeout(function () {
                            var nextIcon = document.createElement('div');
                            nextIcon.className = mainIcon.className;
                            $dom.html(nextIcon, $dom.html(mainIcon));
                            nextIcon.style.position = 'absolute';
                            nextIcon.style.left = $dom.findPosX(mainIcon, true) + 'px';
                            nextIcon.style.top = $dom.findPosY(mainIcon, true) + 'px';
                            nextIcon.style.zIndex = 80;
                            nextIcon.xVector = 5 - Math.random() * 10;
                            nextIcon.yVector = -Math.random() * 6;
                            nextIcon.opacity = 1.0;
                            iconSpot.appendChild(nextIcon);
                            nextIcon.animationTimer = setInterval(function () {
                                if (window.paused) {
                                    return;
                                }

                                var _left = ((parseInt(nextIcon.style.left) || 0) + nextIcon.xVector);
                                nextIcon.style.left = _left + 'px';
                                var _top = ((parseInt(nextIcon.style.top) || 0) + nextIcon.yVector);
                                nextIcon.style.top = _top + 'px';
                                nextIcon.style.opacity = nextIcon.opacity;
                                nextIcon.opacity *= 0.98;
                                nextIcon.yVector += 0.2;
                                if ((_top > maxHeight) || (nextIcon.opacity < 0.05) || (_left + 50 > windowWidth) || (_left < 0)) {
                                    clearInterval(nextIcon.animationTimer);
                                    nextIcon.animationTimer = null;
                                    nextIcon.parentNode.removeChild(nextIcon);
                                }
                            }, 50);
                        }, 7000 + 500 * x);
                    }
                }, 100);

                // Set up animation timer
                clonedMessage.timer = setInterval(function () {
                    animateDown(clonedMessage);
                }, frameDelay);
            }, 0);
        });
    }

    function animateDown(el, avoidRemove) {
        if (window.paused) {
            return;
        }

        avoidRemove = Boolean(avoidRemove);

        var bubbles = document.getElementById('bubbles-go-here');
        if (!bubbles) {
            return; // Unloaded
        }

        var maxHeight = bubbles.parentNode.offsetHeight;
        var jumpSpeed = 1;
        var newPos = (parseInt(el.style.top) || 0) + jumpSpeed;
        el.style.top = newPos + 'px';

        if ((newPos > maxHeight) || (!el.parentNode)) {
            if (!avoidRemove) {
                if (el.parentNode) {
                    window.totalLines -= el.querySelectorAll('.line').length;
                    el.parentNode.removeChild(el);
                }
                clearInterval(el.timer);
                el.timer = null;
            }
        }
    }

    function timeNow() {
        return Math.round(Date.now() / 1000);
    }

    function timelineClick(prospective) {
        prospective = Boolean(prospective);

        var pos = window.currentMouseX - $dom.findPosX(document.getElementById('time-line-image'), true);
        var timelineLength = 808;
        var minTime = window.minTime;
        var maxTime = timeNow();
        var time = minTime + pos * (maxTime - minTime) / timelineLength;
        if (!prospective) {
            window.currentTime = time;
            bubblesTidyUp();
            $dom.html('#real-time-date', '{!SET;^}');
            $dom.html('#real-time-time', '');

            var loadingIcon = document.getElementById('loading-icon');
            if (!loadingIcon) {
                loadingIcon.style.display = 'block';
            }
        } else {
            setTimeLinePosition(time);
        }
    }

    function bubblesTidyUp() {
        var bubblesGoHere = document.getElementById('bubbles-go-here');
        if (!bubblesGoHere) {
            return;
        }
        var bubbles = document.getElementById('real-time-surround').parentNode.querySelectorAll('.bubble-wrap');
        for (var i = 0; i < bubbles.length; i++) {
            if (bubbles[i].timer) {
                clearInterval(bubbles[i].timer);
                bubbles[i].timer = null;
            }
        }
        $dom.html(bubblesGoHere, '');
        window.bubbleGroups = [];
        window.totalLines = 0;
        var icons = document.getElementById('real-time-surround').parentNode.querySelectorAll('.special-icon');
        for (var j = 0; j < icons.length; j++) {
            if (icons[j].animationTimer) {
                clearInterval(icons[j].animationTimer);
                icons[j].animationTimer = null;
            }
            icons[j].parentNode.removeChild(icons[j]);
        }
    }

    function setTimeLinePosition(time) {
        time = Math.round(time);

        var marker = document.getElementById('real-time-indicator'),
            timelineLength = 808,
            minTime = window.minTime,
            maxTime = timeNow(),
            timelineRange = maxTime - minTime,
            timelineOffsetTime = time - minTime,
            timelineOffsetPosition = timelineOffsetTime * timelineLength / timelineRange;

        if (!marker) {
            return; // Unloaded
        }
        marker.style.marginLeft = (50 + timelineOffsetPosition) + 'px';

        var dateObject = new Date();
        dateObject.setTime(time * 1000);
        var realtimedate = document.getElementById('real-time-date');
        var realtimetime = document.getElementById('real-time-time');
        if (!realtimedate) {
            return;
        }

        $dom.html(realtimedate, dateObject.getFullYear() + '/' + (String(dateObject.getMonth())) + '/' + (String(dateObject.getDate())));

        var hours = (String(dateObject.getHours()));
        var minutes = (String(dateObject.getMinutes()));
        if (minutes.length == 1) {
            minutes = '0' + minutes;
        }
        var seconds = (String(dateObject.getSeconds()));
        if (seconds.length == 1) {
            seconds= '0' + seconds;
        }
        $dom.html(realtimetime, hours + ':' + minutes + ':' + seconds);
    }

}(window.$cms, window.$util, window.$dom));
