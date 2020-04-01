CKEDITOR.plugins.add('imagepaste', {
    hidpi: true,

    init: function (editor) {
        // For Chrome / browsers that decide to implement the new clipboard API
        editor.on('instanceReady', function () {
            editor.document.$.onpaste = function (event) {
                if (!event) event = window.event;

                var items = (event.clipboardData || event.originalEvent.clipboardData).items;
                if (typeof items !== 'undefined' && items[0].type.substr(0, 6) === 'image/') {
                    var blob = items[0].getAsFile();
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        editor.insertHtml('<img src="' + event.target.result + '" />');
                    };
                    reader.readAsDataURL(blob);

                    if (typeof event.stopImmediatePropagation !== 'undefined') {
                        event.stopImmediatePropagation();
                    }
                }
            }
        });

        // Hook into what CKEditor already does, stripping out for browsers not supporting proper pasting yet put junk in
        editor.on('paste', function (e) {
            var data = e.data,
                html = (data.html || (data.type && data.type == 'html' && data.dataValue));
            if (!html)
                return;

            // Safari cannot work
            if (CKEDITOR.env.webkit && (html.indexOf('webkit-fake-url') > 0)) {
                $cms.ui.alert(window.lang_NO_IMAGE_PASTE_SAFARI);
                html = html.replace(/<img src="webkit-fake-url:.*?">/g, '');
            }

            if (e.data.html) {
                e.data.html = html;
            } else {
                e.data.dataValue = html;
            }
        });
    }
});
