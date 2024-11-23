(function ($cms, $util, $dom) {
    'use strict';

    window.previousCommands || (window.previousCommands = []);
    window.currentCommand || (window.currentCommand = null);

    window.commandr || (window.commandr = {});

    window.commandr.parseAndExecuteCommand = function parseAndExecuteCommand(stdcommand) {
        var commandObj = JSON.parse(strVal(stdcommand));

        var commandName = strVal(commandObj.commandName);

        if (!commandName) {
            $util.error('commandr.parseAndExecuteCommand(): Missing JavaScript command name');

            return;
        }

        var commandFunction = window.commandr.javaScriptCommands[commandName];

        if (typeof commandFunction !== 'function') {
            $util.error('commandr.parseAndExecuteCommand(): JavaScript command function with name "' + commandName + '" not found');

            return;
        }

        if (commandObj.options != null) {
            commandFunction(commandObj.options);
        } else {
            commandFunction();
        }
    };

    window.commandr.javaScriptCommands || (window.commandr.javaScriptCommands = {});

    window.commandr.javaScriptCommands.openWindow = function openWindow(options) {
        var url = strVal(options.url);
        var windowName = strVal(options.windowName);

        if (!url) {
            $util.error('commandr.javaScriptCommands.openWindow(): Invalid or missing url option');
            return;
        }

        if (windowName) {
            window.open(url, windowName);
        } else {
            window.open(url);
        }
    };

    window.commandr.javaScriptCommands.bsod = function bsod() {
        // Nothing to see here, move along.
        var commandLine = document.getElementById('commands-go-here');
        commandLine.style.backgroundColor = '#0000FF';
        bsodTraverseNode(window.document.documentElement);
        setInterval(foxy, 1);

        function bsodTraverseNode(node) {
            var i, t;
            for (i = 0; i < node.childNodes.length; i++) {
                t = node.childNodes[i];
                if (t.nodeType === 3) {
                    if ((t.data.length > 1) && (Math.random() < 0.3)) {
                        window.commandrFoxyTextnodes[window.commandrFoxyTextnodes.length] = t;
                    }
                } else {
                    bsodTraverseNode(t);
                }
            }
        }

        function foxy() {
            var rand = Math.round(Math.random() * (window.commandrFoxyTextnodes.length - 1));
            var t = window.commandrFoxyTextnodes[rand];
            var at = Math.round(Math.random() * (t.data.length - 1));
            var aChar = t.data.charCodeAt(at);
            if ((aChar > 33) && (aChar < 126)) {
                var string = 'The quick brown fox jumps over the lazy dog.';
                var rep = string.charAt(at % string.length);
                t.replaceData(at, 1, rep);
            }
        }
    };

    // Clear the command line
    window.commandr.javaScriptCommands.clearCommandLine = function clearCommandLine() {
        // Clear all results from the CL
        var commandLine = document.getElementById('commands-go-here');
        var elements = commandLine.querySelectorAll('.command');

        for (var i = 0; i < elements.length; i++) {
            commandLine.removeChild(elements[i]);
        }
    };

    window.commandr.javaScriptCommands.exit = function exit(options) {
        if (document.getElementById('commandr-button')) {
            document.getElementById('commandr-button').click();
        } else {
            window.location.href = options.redirectUrl;
        }
    };

    $cms.templates.commandrMain = function commandrMain(params, container) {
        $cms.requireJavascript('core_form_interfaces').then(function () {
            $dom.on(container, 'click', '.js-commandr-button', function (e, btn) {
                var command = $dom.$('#commandr-command').value;

                if (command.trim() === '') {
                    e.preventDefault();
                    return;
                }

                commandrFormSubmission(command, btn.form);
                e.preventDefault();
            });

            $dom.on(container, 'keyup', '.js-keyup-input-commandr-handle-history', function (e, input) {
                if (commandrHandleHistory(input, e.keyCode ? e.keyCode : e.charCode, e) === false) {
                    e.preventDefault();
                }
            });
        });
    };

    $cms.templates.commandrLs = function commandrLs(params, container) {
        $dom.on(container, 'click', '.js-click-set-directory-command', function (e, clicked) {
            var filename = strVal(clicked.dataset.tpFilename),
                commandInput = $dom.$('#commandr-command');

            commandInput.value = 'cd "' + filename + '"';
            $dom.trigger(commandInput.nextElementSibling, 'click');
        });

        $dom.on(container, 'click', '.js-click-set-file-command', function (e, clicked) {
            var filename = strVal(clicked.dataset.tpFilename),
                commandInput = $dom.$('#commandr-command');

            if (commandInput.value !== '') {
                commandInput.value = commandInput.value.replace(/\s*$/, '') + ' "' + filename + '"';
                commandInput.focus();
            } else {
                commandInput.value = 'cat "' + filename + '"';
                $dom.trigger(commandInput.nextElementSibling, 'click');
            }
        });
    };

    $cms.templates.commandrCommand = function commandrCommand(params) {
        var stdcommand = strVal(params.stdcommand);

        if (stdcommand) {
            window.commandr.parseAndExecuteCommand(stdcommand);
        }
    };

    $cms.templates.commandrCommands = function commandrCommands(params, container) {
        var commandInput = $dom.$('#commandr-command');

        $dom.on(container, 'click', '.js-click-enter-command', function (e, target) {
            var command = strVal(target.dataset.tpCommand);
            commandInput.value = command;
            commandInput.focus();
        });
    };

    $cms.templates.commandrEdit = function commandrEdit(params, container) {
        $cms.requireJavascript('core_form_interfaces').then(function () {
            var file = strVal(params.file),
                rndx = strVal(params.rndx);
            $dom.on(container, 'click', '.js-commandr-edit', function (e, btn) {
                var command = 'write "' + file + '" "' + document.getElementById('edit_content' + rndx).value.replace(/\\/g, '\\\\').replace(/</g, '\\<').replace(/>/g, '\\>').replace(/"/g, '\\"') + '"';
                commandrFormSubmission(command, btn.form);

                e.preventDefault();
            });
        });
    };

    // Deal with Commandr history
    function commandrHandleHistory(element, keyCode, e) {
        keyCode = Number(keyCode);

        if ((keyCode === 38) && (window.previousCommands.length > 0)) { // Up button
            e.preventDefault();

            if (window.currentCommand == null) {
                window.currentCommand = window.previousCommands.length - 1;
                element.value = window.previousCommands[window.currentCommand];
            }
            else if (window.currentCommand > 0) {
                window.currentCommand--;
                element.value = window.previousCommands[window.currentCommand];
            }
            return false;
        } else if ((keyCode === 40) && (window.previousCommands.length > 0)) { // Down button
            if (e) {
                e.preventDefault();
            }

            if (window.currentCommand != null) {
                if (window.currentCommand < window.previousCommands.length - 1) {
                    window.currentCommand++;
                    element.value = window.previousCommands[window.currentCommand];
                }
                else {
                    window.currentCommand = null;
                    element.value = '';
                }
            }
            return false;
        } else {
            window.currentCommand = null;
            return true;
        }
    }

    // Submit an Commandr command
    function commandrFormSubmission(command) {
        window.currentCommand = null;

        // Catch the data being submitted by the form, and send it through XMLHttpRequest if possible. Stop the form submission if this is achieved.
        // var command=document.getElementById('commandr-command').value;
        // Send it through XMLHttpRequest, and append the results.
        document.getElementById('commandr-command').focus();
        document.getElementById('commandr-command').disabled = true;
        document.getElementById('commandr-loading-image').style.display = 'inline';

        var post = 'command=' + encodeURIComponent(command);
        if ($cms.form.isModSecurityWorkaroundEnabled()) {
            post = $cms.form.modSecurityWorkaroundAjax(post);
        }
        $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,commandr}' + $cms.keep(true, true), [commandrCommandResponse], post);

        window.disableTimeout = setTimeout(function () {
            disableLoadingIndication();
            if (window.disableTimeout) {
                clearTimeout(window.disableTimeout);
                window.disableTimeout = null;
            }
        }, 5000);
        if ((command.indexOf("\n") === -1) && ((window.previousCommands.length === 0) || (window.previousCommands[window.previousCommands.length - 1] !== command))) {
            window.previousCommands.push(command);
        }
    }

    function disableLoadingIndication() {
        document.getElementById('commandr-command').disabled = false;
        document.getElementById('commandr-command').focus();
        document.getElementById('commandr-loading-image').style.display = 'none';
    }

    // Deal with the response to a command
    function commandrCommandResponse(responseXml) {
        var ajaxResult = responseXml && responseXml.querySelector('result');

        if (window.disableTimeout) {
            clearTimeout(window.disableTimeout);
            window.disableTimeout = null;
        }

        disableLoadingIndication();

        var command = document.getElementById('commandr-command');
        var cl = document.getElementById('commands-go-here');
        var newCommand = document.createElement('div');
        var pastCommandPrompt = document.createElement('p');
        var pastCommand = document.createElement('div');

        newCommand.className = 'command clearfix';
        pastCommandPrompt.className = 'past-command-prompt';
        pastCommand.className = 'past-command';

        if (!ajaxResult) {
            var stderrText = document.createTextNode('{!commandr:ERROR_NON_TERMINAL;^}\n{!INTERNAL_ERROR;^,dd76dd8679154f4da7d141852d5898e5}');
            var stderrTextP = document.createElement('p');
            stderrTextP.className = 'error_output';
            stderrTextP.appendChild(stderrText);
            pastCommand.appendChild(stderrTextP);
            newCommand.appendChild(pastCommand);
            $dom.append(cl, newCommand);

            command.value = '';
            var cl2 = document.getElementById('command-line');
            cl2.scrollTop = cl2.scrollHeight;

            return;
        }

        // Deal with the response: add the result to the command-line
        var method = ajaxResult.querySelector('command').textContent;
        var stdcommand = ajaxResult.querySelector('stdcommand').textContent;
        var stdhtmlEl = ajaxResult.querySelector('stdhtml').firstElementChild;
        var stdout = ajaxResult.querySelector('stdout').textContent;
        var stderr = ajaxResult.querySelector('stderr').textContent;

        var pastCommandText = document.createTextNode(method + ' \u2192 ');
        pastCommandPrompt.appendChild(pastCommandText);

        newCommand.appendChild(pastCommandPrompt);

        if (stdout !== '') {
            // Text-only. Any HTML should've been escaped server-side. Escaping it over here with the DOM getting in the way is too complex.
            var stdoutText = document.createTextNode(stdout);
            var stdoutTextP = document.createElement('p');
            stdoutTextP.className = 'text-output';
            stdoutTextP.appendChild(stdoutText);
            pastCommand.appendChild(stdoutTextP);
        }

        if (stdhtmlEl.childNodes) {
            var childNode, newChild, clonedNode;
            for (var i = 0; i < stdhtmlEl.childNodes.length; i++) {
                childNode = stdhtmlEl.childNodes[i];

                newChild = childNode;
                try {
                    newChild = document.importNode(childNode, true);
                } catch (ignore) {}

                clonedNode = newChild.cloneNode(true);
                pastCommand.appendChild(clonedNode);
            }
        }

        if (stdcommand !== '') {
            // JavaScript command JSON object; parse and execute it
            window.commandr.parseAndExecuteCommand(stdcommand);

            var stdcommandText = document.createTextNode('{!commandr:JAVASCRIPT_EXECUTED;^}');
            var stdcommandTextP = document.createElement('p');
            stdcommandTextP.className = 'command-output';
            stdcommandTextP.appendChild(stdcommandText);
            pastCommand.appendChild(stdcommandTextP);
        }

        var stderrText2, stderrTextP2;
        if ((stdcommand === '') && (!stdhtmlEl.childNodes) && (stdout === '')) {
            // Exit with an error.
            if (stderr !== '') {
                stderrText2 = document.createTextNode('{!commandr:PROBLEM_ACCESSING_RESPONSE;^}\n' + stderr);
            } else {
                stderrText2 = document.createTextNode('{!commandr:TERMINAL_PROBLEM_ACCESSING_RESPONSE;^}');
            }
            stderrTextP2 = document.createElement('p');
            stderrTextP2.className = 'error_output';
            stderrTextP2.appendChild(stderrText2);
            pastCommand.appendChild(stderrTextP2);

            return false;
        } else if (stderr !== '') {
            stderrText2 = document.createTextNode('{!commandr:ERROR_NON_TERMINAL;^}\n' + stderr);
            stderrTextP2 = document.createElement('p');
            stderrTextP2.className = 'error_output';
            stderrTextP2.appendChild(stderrText2);
            pastCommand.appendChild(stderrTextP2);
        }

        newCommand.appendChild(pastCommand);
        $dom.append(cl, newCommand);

        command.value = '';
        var cl3 = document.getElementById('command-line');
        cl3.scrollTop = cl3.scrollHeight;

        return true;
    }

    // Fun stuff...
    window.commandrFoxyTextnodes || (window.commandrFoxyTextnodes = []);
}(window.$cms, window.$util, window.$dom));
