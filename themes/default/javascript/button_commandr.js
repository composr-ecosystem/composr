(function ($cms, $util, $dom) {
    'use strict';

    $cms.templates.miniblockMainCalculator = function miniblockMainCalculator(params, container) {
        var message = strVal(container.dataset.tpMessage),
            equation = strVal(container.dataset.tpEquation);


        $dom.on(container, 'click', '.js-btn-click-calculate-sum', function () {
            var form = this.form;
            $cms.form.checkForm(this.form, false).then(function (valid) {
                if (valid) {
                    $cms.ui.alert(message.replace('xxx', calculateSum(form.elements)));
                }
            });
        });

        function calculateSum(elements) {
            for (var i = 0; i < elements.length; i++) {
                if (elements[i].name !== '') {
                    window[elements[i].name] = elements[i].value;
                }
            }
            var ret;
            // eslint-disable-next-line no-eval
            eval('ret = ' + equation);
            return Math.round(ret);
        }
    };

    $cms.behaviors.btnLoadCommandr = {
        attach: function (context) {
            $util.once($dom.$$$(context, '[data-btn-load-commandr]'), 'behavior.btnLoadCommandr').forEach(function (btn) {
                $dom.on(btn, 'click', function (e) {
                    e.preventDefault();
                    loadCommandr();
                });
            });
        }
    };

    function loadCommandr() {
        if (!document.getElementById('commandr-img-loader')) {
            var img = document.querySelector('.commandr-img');
            img.classList.add('footer-button-loading');
            var tmpEl = document.createElement('img');
            tmpEl.id = 'commandr-img-loader';
            tmpEl.src = $util.srl('{$IMG;,loading}');
            tmpEl.width = '20';
            tmpEl.height = '20';
            tmpEl.style.position = 'absolute';
            tmpEl.style.left = ($dom.findPosX(img) + 2) + 'px';
            tmpEl.style.top = ($dom.findPosY(img) + 1) + 'px';
            img.parentNode.appendChild(tmpEl);
        }

        $cms.requireCss('commandr');
        $cms.requireJavascript('commandr').then(function () {
            return $cms.ui.confirmSession();
        }).then(function (sessionConfirmed) {
            // Remove "loading" indicator from button
            var tmpEl = document.getElementById('commandr-img-loader');
            if (tmpEl) {
                tmpEl.remove();
            }

            if (!sessionConfirmed) {
                return;
            }

            // Set up Commandr window
            var commandrBox = document.getElementById('commandr-box');
            if (!commandrBox) {
                commandrBox = $dom.create('div', {
                    id: 'commandr-box',
                    css: {
                        position: 'absolute',
                        zIndex: 2000,
                        left: ($dom.getWindowWidth() - 800) / 2 + 'px',
                        top: Math.max(100, ($dom.getWindowHeight() - 600) / 2) + 'px',
                        display: 'none',
                        width: '800px',
                        height: '500px'
                    }
                });
                document.body.appendChild(commandrBox);
                $cms.loadSnippet('commandr').then(function (result) {
                    $dom.html(commandrBox, result);
                    doCommandrBox();
                });
            } else {
                doCommandrBox();
            }
        });

        function doCommandrBox() {
            var commandrBox = document.getElementById('commandr-box'),
                img = document.querySelector('.commandr-img'),
                bi, cmdLine;

            if ($dom.notDisplayed(commandrBox)) { // Showing Commandr again
                $dom.show(commandrBox);

                if (img) {
                    $cms.setIcon(img, 'tool_buttons/commandr_off', '{$IMG;,icons/tool_buttons/commandr_off}');
                    img.classList.remove('footer-button-loading');
                }

                $dom.smoothScroll(0, null, function () {
                    document.getElementById('commandr-command').focus();
                });

                cmdLine = document.getElementById('command-line');
                cmdLine.style.opacity = 0.0;
                $dom.fadeTo(cmdLine, null, 0.9);

                bi = document.getElementById('main-website-inner');
                if (bi) {
                    bi.classList.add('faded');
                }

                document.getElementById('commandr-command').focus();
            } else { // Hiding Commandr
                if (img) {
                    $cms.setIcon(img, 'tool_buttons/commandr_on', '{$IMG;,icons/tool_buttons/commandr_on}');
                    img.style.opacity = 1.0;
                }

                $dom.hide(commandrBox);
                bi = document.getElementById('main-website-inner');
                if (bi) {
                    bi.classList.remove('faded');
                }
            }
        }
    }
}(window.$cms, window.$util, window.$dom));
