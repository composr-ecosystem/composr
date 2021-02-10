(function () {
	CKEDITOR.plugins.add('composr', {
		hidpi: true,

		icons: 'composr_block,composr_comcode,composr_page,composr_quote,composr_box,composr_code,composr_image',

		init: function (editor) {
			var possibles = ['block', 'comcode', 'page', 'quote', 'box', 'code'];
			for (var i = 0; i < possibles.length; i++) {
				var buttonName = possibles[i];
				var elements = get_elements_by_class_name(editor.element.$.parentNode.parentNode, 'comcode_button_' + buttonName);
				if (typeof elements[0] != 'undefined') {
					var func = {
						exec: function (e) {
							return function () {
								e.onclick.call(e);
							}
						}(elements[0])
					};
					var label = elements[0].alt;

					editor.addCommand('composr_' + buttonName, func);
					editor.ui.addButton && editor.ui.addButton('composr_' + buttonName, {
						label: label,
						command: 'composr_' + buttonName
					});

					elements[0].parentNode.parentNode.style.display = 'none';
				}
			}

			var uses_plupload = false;

			var aub = document.getElementById('attachment_upload_button__' + editor.element.$.id);
			var doing_attachment_uploads = (aub) && (aub.className.indexOf('for_field_' + editor.element.$.id + ' ')!=-1);

			if ((typeof window['rebuild_attachment_button_for_next__' + editor.element.$.id] != 'undefined') && (doing_attachment_uploads)) {
				if ((!aub) || (aub.parentNode.parentNode.style.display == 'none')) // If attachment button was not placed elsewhere
				{
					window.setTimeout(function () {
						window['rebuild_attachment_button_for_next__' + editor.element.$.id](document.getElementById('cke_' + editor.element.$.id).getElementsByClassName('cke_button__composr_image')[0].id);
					}, 1000);

					uses_plupload = true;
				}
			}

			var func = {
				exec: function (e) {
					if (doing_attachment_uploads) {
						var has_selection = (e.getSelection().getSelectedElement() != null);
						if (uses_plupload && !has_selection) return; // Not selected an image for editing, so don't show an edit dialogue
					}

					if (typeof window.lang_PREFER_CMS_ATTACHMENTS == 'undefined' || has_selection || !doing_attachment_uploads) {
						editor.execCommand('image');
					} else {
						fauxmodal_alert(window.lang_PREFER_CMS_ATTACHMENTS, function () {
							editor.execCommand('image');
						});
					}
				}
			};
			editor.addCommand('composr_image', func);
			editor.ui.addButton && editor.ui.addButton('composr_image', {
				label: editor.lang.common.image,
				command: 'composr_image'
			});
		}
	});
})();
