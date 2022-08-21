(function ($cms, $util, $dom) {
    'use strict';

    $cms.behaviors.btnLoadRealtimeRain = {
        attach: function (context) {
            $util.once($dom.$$$(context, '[data-btn-load-realtime-rain]'), 'behavior.btnLoadRealtimeRain').forEach(function (btn) {
                $dom.on(btn, 'click', function (e) {
                    e.preventDefault();
                    loadRealtimeRain();
                });
            });
        }
    };

    function loadRealtimeRain() {
        if (window.$realtimeRain != null) {
            window.$realtimeRain.load();
            return;
        }

        if (document.getElementById('realtime-rain-img-loader')) {
            setTimeout(loadRealtimeRain, 200);
            return;
        }

        var img = document.getElementById('realtime-rain-img');
        img.classList.add('footer-button-loading');
        var tmpEl = document.createElement('img');
        tmpEl.id = 'realtime-rain-img-loader';
        tmpEl.src = $util.srl('{$IMG;,loading}');
        tmpEl.width = '20';
        tmpEl.height = '20';
        tmpEl.style.position = 'absolute';
        tmpEl.style.left = ($dom.findPosX(img) + 2) + 'px';
        tmpEl.style.top = ($dom.findPosY(img) + 1) + 'px';
        img.parentNode.appendChild(tmpEl);

        $cms.requireCss('realtime_rain');
        $cms.requireJavascript('realtime_rain');
        setTimeout(loadRealtimeRain, 200);
    }

}(window.$cms, window.$util, window.$dom));
