/*{+START,INCLUDE,_polyfill_fetch,.js,javascript}{+END}*/

/*{+START,INCLUDE,_polyfill_general,.js,javascript}{+END}*/

/*{+START,INCLUDE,_polyfill_keyboardevent_key,.js,javascript}{+END}*/

/*{+START,INCLUDE,_polyfill_url,.js,javascript}{+END}*/

/*{+START,INCLUDE,_polyfill_web_animations,.js,javascript}{+END}*/

/*{+START,INCLUDE,_json5,.js,javascript}{+END}*/

/*{+START,INCLUDE,_util,.js,javascript}{+END}*/

/*{+START,INCLUDE,_dom,.js,javascript}{+END}*/

/*{+START,INCLUDE,_cms,.js,javascript}{+END}*/

/*{+START,INCLUDE,_cms_form,.js,javascript}{+END}*/

/*{+START,INCLUDE,_cms_ui,.js,javascript}{+END}*/

/*{+START,INCLUDE,_cms_templates,.js,javascript}{+END}*/

/*{+START,INCLUDE,_cms_views,.js,javascript}{+END}*/

/*{+START,INCLUDE,_cms_behaviors,.js,javascript}{+END}*/

/*{+START,IF_NON_EMPTY,{$CONFIG_OPTION,google_fonts}}{+START,IF,{$CONFIG_OPTION,google_fonts_delayed_load}}*/
/*{+START,INCLUDE,webfontloader,.js,javascript}{+END}*/
/*{+END}{+END}*/

(function ($cms, $util, $dom) {
    'use strict';

    $dom.ready.then(function () {
        if ($cms.browserMatches('ie')) {
            /*{+START,SET,icons_sprite_url}{$IMG,icons{$?,{$THEME_OPTION,use_monochrome_icons},_monochrome}_sprite}{+END}*/
            loadSvgSprite('{$GET;,icons_sprite_url}');
        }

        // Start everything
        $cms.attachBehaviors(document);

        // Google Fonts
        if (($cms.configOption('google_fonts_delayed_load')) && ($cms.configOption('google_fonts') != '')) {
            var families = $cms.configOption('google_fonts').split(',').map(function(e) { return e.trim() + ':300,300i,400,400i,500,500i'; });
            families[families.length - 1] += '&display=swap'; // Hack to make Google Lighthouse happy
            WebFont.load({
                google: {
                    families: families
                }
            });
        }
    });

    /**
     * Workaround for IE not supporting external SVG with <use> elements.
     * Loads an SVG sprite using AJAX and appends its contents to the body.
     * Also looks for any <use> elements with external [xlink:href] attributes matching the sprite URL and replaces them with simple #IDs.
     * @param spriteUrl
     */
    function loadSvgSprite(spriteUrl) {
        spriteUrl = $util.srl(spriteUrl);

        var xhr = new XMLHttpRequest();
        xhr.overrideMimeType('text/xml');
        xhr.open('GET', spriteUrl);
        xhr.onload = function () {
            var svg = xhr.responseXML && xhr.responseXML.querySelector('svg');

            if (!svg) {
                return;
            }

            var div = document.createElement('div');
            div.style.cssText = 'position: absolute; width: 0; height: 0; visibility: hidden; overflow: hidden;';
            div.appendChild(svg);
            (document.body || document.documentElement).appendChild(div);

            var uses = document.querySelectorAll('use');

            for (var i = 0; i < uses.length; i++) {
                var use = uses[i],
                    hrefParts = strVal(use.getAttribute('xlink:href')).split('#'),
                    hrefUrl = $util.srl(hrefParts[0]),
                    hrefId = hrefParts[1];

                if (hrefUrl === spriteUrl) {
                    use.setAttribute('xlink:href', '#' + hrefId);
                }
            }
        };
        xhr.send();
    }
}(window.$cms, window.$util, window.$dom));

/*
These are used so frequently that it is better to just include them directly in here.
We still keep the code in separate files to stop this file getting too bloated.
*/

/*{+START,INCLUDE,core_notifications,.js,javascript}{+END}*/
/*{+START,INCLUDE,sound,.js,javascript}{+END}*/
/*{+START,INCLUDE,ajax_people_lists,.js,javascript}{+END}*/
