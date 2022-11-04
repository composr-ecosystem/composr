(function ($cms, $util) {
    'use strict';

    var $pulse = window.$pulse = {};

    $pulse.processWave = function processWave(el) {
        if (!el) {
            return;
        }

        var pos = window[el.id][0],
            maxColor = window[el.id][1],
            minColor = window[el.id][2],
            textNodes = window[el.id][4];

        if (textNodes.length === 0) { // Setup
            var textNodesTemp = findTextNodes(el);

            // Now split up the nodes so each is actually wrapped by a span
            for (var i = 0; i < textNodesTemp.length; i++) {
                var parent = textNodesTemp[i].parentNode;
                parent.removeChild(textNodesTemp[i]);

                var text = textNodesTemp[i].data;
                for (var j = 0; j < text.length; j++) {
                    var span = document.createElement('span');
                    var te = document.createTextNode(text.substr(j, 1));
                    span.appendChild(te);
                    textNodes.push(span);
                    parent.appendChild(span);
                }
            }

            window[el.id][4] = textNodes;
        }

        var range = textNodes.length;

        // Apply colour wave
        for (var k = 0; k < textNodes.length; k++) {
            var distLeftwards = k - pos;
            if (distLeftwards < 0) {
                distLeftwards = k + range - pos;
            }
            var distRightwards = pos - k;
            if (distRightwards < 0) {
                distRightwards = pos + range - k;
            }

            var diff = (distLeftwards < distRightwards) ? distLeftwards : distRightwards;
            var fraction = diff / (range / 2);
            textNodes[k].style.color = '#' + colorInterpolation(maxColor, minColor, fraction);
        }

        // Cycle around
        window[el.id][0]++;
        if (window[el.id][0] > textNodes.length) {
            window[el.id][0] = 0;
        }

        function colorInterpolation(maxColor, minColor, fraction) {
            var minColorR = $util.hexToDec(minColor.substr(0, 2)),
                minColorG = $util.hexToDec(minColor.substr(2, 2)),
                minColorB = $util.hexToDec(minColor.substr(4, 2)),
                maxColorR = $util.hexToDec(maxColor.substr(0, 2)),
                maxColorG = $util.hexToDec(maxColor.substr(2, 2)),
                maxColorB = $util.hexToDec(maxColor.substr(4, 2)),
                colorR = minColorR + fraction * (maxColorR - minColorR),
                colorG = minColorG + fraction * (maxColorG - minColorG),
                colorB = minColorB + fraction * (maxColorB - minColorB);

            return $util.decToHex(parseInt(colorR)) + $util.decToHex(parseInt(colorG)) + $util.decToHex(parseInt(colorB));
        }

        function findTextNodes(e) {
            var found = [];
            for (var i = 0; i < e.childNodes.length; i++) {
                // eslint-disable-next-line no-restricted-properties
                if (e.childNodes[i].nodeName === '#text') {
                    found.push(e.childNodes[i]);
                }
                if (e.childNodes[i] !== undefined) {
                    var found2 = findTextNodes(e.childNodes[i]);
                    for (var i2 = 0; i2 < found2.length; i2++) {
                        found.push(found2[i2]);
                    }
                }
            }
            return found;
        }
    };
}(window.$cms, window.$util));
