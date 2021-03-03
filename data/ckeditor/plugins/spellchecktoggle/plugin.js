/*
Spellcheck toggle button. Works on Chrome/Firefox/Safari/IE10
*/

(function() {
    CKEDITOR.plugins.add('spellchecktoggle', {
        enableSpellChecker: function(editor, showMessage) {
            editor.commands.spellchecktoggle.setState(CKEDITOR.TRISTATE_ON);

            editor.config.disableNativeSpellChecker = false;
            editor.element.$.spellcheck = true;
            document.body.spellcheck = true;

            var oldData = editor.getData();
            if (!oldData.match(/<br( \/)?>\s*$/)) {
                oldData += '<br /><br />'; // Aids the editor in deciding to do a spellcheck (CKEditor needs 2 for some reason)
            }
            editor.setData(oldData); // Needed to force spellchecker reset

            if (showMessage) {
                $cms.ui.alert(window.lang_SPELLCHECKER_ENABLED,window.lang_SPELLCHECKER_LABEL).then(function() { editor.focus(); });
            }

            document.body.oncontextmenu = function(event) { // Runs before CKEditor handler
                if (!event) event = window.event;
                // Do not let CKEditor handler happen
                if (typeof event.stopImmediatePropagation != 'undefined') event.stopImmediatePropagation();
                return true; // Let native handler happen
            };
        },

        disableSpellChecker: function(editor, showMessage) {
            editor.commands.spellchecktoggle.setState(CKEDITOR.TRISTATE_OFF);

            editor.config.disableNativeSpellChecker = true;
            editor.element.$.spellcheck = false;

            document.body.spellcheck = false;

            editor.setData(editor.getData()); // Needed to force spellchecker reset

            if (showMessage) {
                $cms.ui.alert(window.lang_SPELLCHECKER_DISABLED,window.lang_SPELLCHECKER_LABEL);
            }

            document.body.oncontextmenu = function() { // Runs before CKEditor handler
                // Let CKEditor handler happen
                return null;
            };
        },

        hidpi: true,

        init: function(editor) {
            var func = {
                exec: function(editor) {
                      var doSpellcheckNow = editor.config.disableNativeSpellChecker;

                      if (doSpellcheckNow)
                      {
                            editor.plugins['spellchecktoggle'].enableSpellChecker(editor, true);
                      } else
                      {
                            editor.plugins['spellchecktoggle'].disableSpellChecker(editor, true);
                      }
                 }
            };
            var label = window.lang_SPELLCHECKER_TOGGLE;

            var command = editor.addCommand('spellchecktoggle', func);
            command.canUndo = false;

            editor.ui.addButton && editor.ui.addButton('spellchecktoggle',{
                label: label,
                command: 'spellchecktoggle'
            });

            var _this = this;
            editor.on('instanceReady', function() {
                if (editor.config.wysiwygSpellcheckerDefault) {
                    _this.enableSpellChecker(editor, false);
                } else {
                    _this.disableSpellChecker(editor, false);
                }
            });
        }
    });
})();
