(function ($cms, $util, $dom) {
    'use strict';

    // ==============
    // COLOUR CHOOSER
    // ==============

    var $themeColours = window.$themeColours = {};

    var namesToNumbers = { length: 0 },
        lastCcI = {};

    $themeColours.makeColourChooser = function makeColourChooser(name, color, context, tabindex, label, className, required) {
        name = strVal(name);
        color = strVal(color);
        context = strVal(context);
        label = strVal(label) || '&lt;color-' + name + '&gt;';
        className = strVal(className);
        required = boolVal(required);

        if (className !== '') {
            className = 'class="' + className + '" ';
        }

        namesToNumbers[name] = namesToNumbers.length;
        namesToNumbers.length++;

        var p = document.getElementById('colours-go-here-' + name);
        if (!p) {
            p = document.getElementById('colours-go-here');
        }

        if ((color !== '') && (color.substr(0, 1) !== '#') && (color.substr(0, 3) !== 'rgb')) {
            if (color.match(/[A-Fa-f\d]\{6\}/)) {
                color = '#' + color;
            } else {
                color = '#000000';
            }
        }

        var _color;
        if (color === '' || color === '#') {
            _color = required ? '#000000' : '';
        } else {
            _color = '#' + color.substr(1);
        }

        var type = required ? 'color' : 'text'; // There is no sense of a color input being empty, it defaults to black and cannot be unset once set

        var t = '';
        t += '<div class="css-colour-chooser">';
        t += '    <div class="css-colour-chooser-name">';
        t += '        <label class="field-name" for="' + name + '"> ' + label + '</label><br />';
        t += '        <input ' + className + 'alt="{!COLOUR;^}" type="' + type + '" value="' + _color + '" id="' + name + '" name="' + name + '" size="7" class="js-change-update-chooser" />';
        t += '    </div>';
        t += '    <div class="css-colour-chooser-widget">';
        t += '        <div class="css-colour-chooser-from" style="background-color: ' + ((color === '') ? '#000' : color) + '" id="cc-source-' + name + '">';
        t += '            <span>{!themes:FROM_COLOUR^#}</span>';
        t += '        </div>';
        t += '        <div class="css-colour-chooser-to" style="background-color: ' + ((color === '') ? '#000' : color) + '" id="cc-target-' + name + '">';
        t += '            <span>{!COLOUR^#}</span>';
        t += '        </div>';
        t += '        <div class="css-colour-chooser-colour">';
        t += '            <div id="cc-0-' + name + '"></div>';
        t += '            <div id="cc-1-' + name + '"></div>';
        t += '            <div id="cc-2-' + name + '"></div>';
        t += '        </div>';
        t += '    </div>';
        t += '</div>';

        if (context !== '') {
            t = t + '<div class="css-colour-chooser-context">' + context + '</div>';
        }

        if (p.id === 'colours-go-here') {
            $dom.append(p, t);
        } else {
            $dom.html(p, t);
        }

        $dom.on(p, 'change', '.js-change-update-chooser', function (e, target) {
            updateChooser(target.id);
        });

        // LEGACY
        if (window.jQuery && window.jQuery.fn.spectrum !== undefined) {
            var test = document.createElement('input');
            try {
                test.type = 'color';
            }
            catch (e) {}
            if (test.type === 'text') {
                window.jQuery("#" + name).spectrum({
                    color: color
                });
            }
        }
    };

    $themeColours.doColorChooser = function doColorChooser() {
        var myElements = document.querySelectorAll('div[id^=cc-target-]'), ce;

        for (ce = 0; ce < myElements.length; ce++) {
            doColorChooserElement(myElements[ce]);
        }

        function doColorChooserElement(element) {
            var id = element.id.substring(10),
                source = document.getElementById('cc-source-' + id),
                bgColor = source.style.backgroundColor;

            if ((bgColor.substr(0, 1) !== '#') && (bgColor.substr(0, 3) !== 'rgb')) {
                bgColor = '#000000';
            }
            if (bgColor.substr(0, 1) === '#') {
                bgColor = 'rgb(' + $util.hexToDec(bgColor.substr(1, 2)) + ',' + $util.hexToDec(bgColor.substr(3, 2)) + ',' + $util.hexToDec(bgColor.substr(5, 2)) + ')';
            }

            var sRgb = bgColor.replace(new RegExp('(r|g|b|(\\()|(\\))|(\\s))*', 'gi'), '');
            var rgb = sRgb.split(',');

            rgb[0] = Math.round(rgb[0] / 4) * 4;
            rgb[1] = Math.round(rgb[1] / 4) * 4;
            rgb[2] = Math.round(rgb[2] / 4) * 4;

            if (rgb[0] >= 256) {
                rgb[0] = 252;
            }
            if (rgb[1] >= 256) {
                rgb[1] = 252;
            }
            if (rgb[2] >= 256) {
                rgb[2] = 252;
            }

            element.style.color = 'rgb(' + (255 - rgb[0]) + ',' + (255 - rgb[1]) + ',' + (255 - rgb[2]) + ')';

            source.style.color = element.style.color;

            var c = [];
            c[0] = document.getElementById('cc-0-' + id);
            c[1] = document.getElementById('cc-1-' + id);
            c[2] = document.getElementById('cc-2-' + id);

            var d, i, _rgb = [], bg, innert, tid, selected, style;
            for (d = 0; d <= 2; d++) {
                lastCcI[d + namesToNumbers[id] * 3] = 0;
                innert = '';

                for (i = 0; i < 256; i += 4) {
                    selected = (i === rgb[d]);
                    if (!selected) {
                        _rgb[0] = 0;
                        _rgb[1] = 0;
                        _rgb[2] = 0;
                        _rgb[d] = i;
                    } else {
                        _rgb[0] = 255;
                        _rgb[1] = 255;
                        _rgb[2] = 255;
                        lastCcI[d + namesToNumbers[id] * 3] = i;
                    }
                    bg = 'rgb(' + _rgb[0] + ',' + _rgb[1] + ',' + _rgb[2] + ')';
                    tid = 'cc_col_' + d + '_' + i + '#' + id;

                    style = ((i === rgb[d]) ? '' : 'cursor: pointer; ') + 'background-color: ' + bg + ';';
                    if (selected) {
                        style += 'outline: 3px solid gray; position: relative;';
                    }
                    innert = innert + '<div class="css-colour-strip js-click-do-color-change" style="' + style + '" id="' + tid + '"></div>';
                }
                $dom.html(c[d], innert);
            }

            $dom.on(c[0], 'click', '.js-click-do-color-change', doColorChange);
            $dom.on(c[1], 'click', '.js-click-do-color-change', doColorChange);
            $dom.on(c[2], 'click', '.js-click-do-color-change', doColorChange);
        }
    };

    function doColorChange(e, target) {
        // Find the colour chooser's ID of this element
        var _id = target.id.substring(target.id.lastIndexOf('#') + 1);

        var finality = document.getElementById(_id);
        if (finality.disabled) {
            return;
        }

        // Get the ID of the element we clicked (format: cc_col_<0-2>_<0-255>#<chooser-id>)
        var id = target.id;

        var b = id.substr(0, id.length - _id.length).lastIndexOf('_');

        var d = parseInt(id.substring(7, b), 10); // Get the colour row (0-2)
        var i = parseInt(id.substring(b + 1, id.lastIndexOf('#')), 10); // Get the colour (0-255)

        var rgb = [];
        rgb[0] = 0;
        rgb[1] = 0;
        rgb[2] = 0;
        rgb[d] = lastCcI[d + namesToNumbers[_id] * 3];
        var tempLastCc = document.getElementById('cc_col_' + d + '_' + rgb[d] + '#' + _id);
        if (tempLastCc !== target) {
            tempLastCc.style.backgroundColor = '#' + $util.decToHex(rgb[0]) + $util.decToHex(rgb[1]) + $util.decToHex(rgb[2]);
            tempLastCc.style.cursor = 'pointer';
            tempLastCc.style.outline = '';
            tempLastCc.style.position = '';
            lastCcI[d + namesToNumbers[_id] * 3] = i;

            // Show a white line over the colour we clicked
            target.style.backgroundColor = '#FFFFFF';
            target.style.cursor = '';
            target.style.outline = '3px solid gray';
            target.style.position = 'relative';

            var element = document.getElementById('cc-target-' + _id);
            var bgColor = element.style.backgroundColor;
            if (bgColor.substr(0, 1) === '#') {
                bgColor = 'rgb(' + $util.hexToDec(bgColor.substr(1, 2)) + ',' + $util.hexToDec(bgColor.substr(3, 2)) + ',' + $util.hexToDec(bgColor.substr(5, 2)) + ')';
            }

            var sRgb = bgColor.replace(new RegExp('(r|g|b|(\\()|(\\))|(\\s))*', 'gi'), '');
            var _rgb = sRgb.split(',');
            _rgb[d] = i;
            element.style.backgroundColor = '#' + $util.decToHex(_rgb[0]) + $util.decToHex(_rgb[1]) + $util.decToHex(_rgb[2]);
            element.style.color = '#' + $util.decToHex(255 - _rgb[0]) + $util.decToHex(255 - _rgb[1]) + $util.decToHex(255 - _rgb[2]);

            finality.value = '#' + $util.decToHex(_rgb[0]) + $util.decToHex(_rgb[1]) + $util.decToHex(_rgb[2]);
        }
    }

    function updateChooser(chooser) {
        var ob = document.getElementById(chooser);
        if (ob.disabled) {
            return false;
        }

        return updateChoose(chooser, 0, ob.value.substr(1, 2))
            && updateChoose(chooser, 1, ob.value.substr(3, 2))
            && updateChoose(chooser, 2, ob.value.substr(5, 2));

        function updateChoose(id, d, i) {
            i = $util.hexToDec(i);
            i = i - i % 4;

            var tid = 'cc_col_' + d + '_' + i + '#' + id;
            var targ = document.getElementById(tid);
            if (!targ) {
                return false;
            }
            var rgb = [];
            rgb[0] = 0;
            rgb[1] = 0;
            rgb[2] = 0;
            rgb[d] = lastCcI[d + namesToNumbers[id] * 3];
            var tempLastCc = document.getElementById('cc_col_' + d + '_' + rgb[d] + '#' + id);
            tempLastCc.style.backgroundColor = '#' + $util.decToHex(rgb[0]) + $util.decToHex(rgb[1]) + $util.decToHex(rgb[2]); // Reset old
            tempLastCc.style.outline = 'none';
            tempLastCc.style.position = 'static';
            lastCcI[d + namesToNumbers[id] * 3] = i;

            var element = document.getElementById('cc-target-' + id);
            var bgColor = element.style.backgroundColor;
            if (bgColor.substr(0, 1) === '#') {
                bgColor = 'rgb(' + $util.hexToDec(bgColor.substr(1, 2)) + ',' + $util.hexToDec(bgColor.substr(3, 2)) + ',' + $util.hexToDec(bgColor.substr(5, 2)) + ')';
            }
            var sRgb = bgColor.replace(new RegExp('(r|g|b|(\\()|(\\))|(\\s))*', 'gi'), '');
            rgb = sRgb.split(',');
            rgb[d] = i;
            element.style.backgroundColor = '#' + $util.decToHex(rgb[0]) + $util.decToHex(rgb[1]) + $util.decToHex(rgb[2]);

            targ.style.backgroundColor = '#FFFFFF';
            targ.style.outline = '3px solid gray';
            targ.style.position = 'relative';

            return true;
        }
    }
}(window.$cms, window.$util, window.$dom));
