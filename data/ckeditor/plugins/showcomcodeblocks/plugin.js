( function() {
	'use strict';

	CKEDITOR.plugins.add( 'showcomcodeblocks', {
		init: function( editor ) {
			var tagsBlock = editor.config.comcodeXMLBlockTags.split(','),
				tagsInline = editor.config.comcodeXMLInlineTags.split(','),
				cssBlock, cssInline, cssImg, cssLtr, cssRtl, cssWhenDisabled,
				tag, trailing, url;

			cssBlock = cssInline = cssImg = cssLtr = cssRtl = cssWhenDisabled = '';

			trailing = ',';
			cssBlock += 'tempcode' + trailing;
			cssLtr += '.cke_contents_ltr tempcode' + trailing;
			cssRtl += '.cke_contents_rtl tempcode' + trailing;
			url = '{$FIND_SCRIPT_NOHTTP;,gd_text}?text=Tempcode%20tag&direction=horizontal&size=9&fg_color=seed-' + editor.config.ocpTheme + '&font=FreeMonoBold';
			cssImg += 'tempcode{' +
				'background-image:url(\'' + url + '\')' +
			'}';

			while ( ( tag = tagsBlock.pop() ) ) {
				trailing = tagsBlock.length ? ',' : '';

				CKEDITOR.dtd.$editable['comcode-' + tag] = 1;
				CKEDITOR.dtd.$block['comcode-' + tag] = 1;
				CKEDITOR.dtd['comcode-' + tag] = CKEDITOR.dtd.div;

				cssBlock += 'comcode-' + tag + trailing;
				cssLtr += '.cke_contents_ltr comcode-' + tag + trailing;
				cssRtl += '.cke_contents_rtl comcode-' + tag + trailing;
				url = '{$FIND_SCRIPT_NOHTTP,gd_text}?text=Comcode%20' + tag.toUpperCase() + '%20tag&direction=horizontal&size=9&fg_color=seed-' + editor.config.ocpTheme + '&font=FreeMonoBold';
				cssImg += 'comcode-' + tag + '{' +
					'background-image:url(\'' + url + '\')' +
				'}';
			}

			while ( ( tag = tagsInline.pop() ) ) {
				trailing = tagsInline.length ? ',' : '';

				CKEDITOR.dtd.$editable['comcode-' + tag] = 1;
				CKEDITOR.dtd.$inline['comcode-' + tag] = 1;
				CKEDITOR.dtd['comcode-' + tag] = CKEDITOR.dtd.span;

				cssInline += 'comcode-' + tag + trailing;
				cssLtr += '.cke_contents_ltr comcode-' + tag + trailing;
				cssRtl += '.cke_contents_rtl comcode-' + tag + trailing;
				url = '{$FIND_SCRIPT_NOHTTP,gd_text}?text=Comcode%20' + tag.toUpperCase() + '%20tag&direction=horizontal&size=7&fg_color=seed-' + editor.config.ocpTheme + '&font=FreeMonoBold';
				cssImg += 'comcode-' + tag + '{' +
					'background-image:url(\'' + url + '\');' +
					'font-size:0.8em;' +
					'min-width:' + ((tag.length + 12) * 0.7/*fudge factor*/) + 'em' +
				'}';
			}

			// Allow our new tags under everywhere with spans
			for (var i in CKEDITOR.dtd) {
				if (typeof CKEDITOR.dtd[i].span !== 'undefined' && CKEDITOR.dtd[i].span == 1) {
					tagsBlock = editor.config.comcodeXMLBlockTags.split(',');
					while ( ( tag = tagsBlock.pop() ) ) {
						CKEDITOR.dtd[i]['comcode-' + tag] = 1;
					}
					tagsInline = editor.config.comcodeXMLInlineTags.split(',');
					while ( ( tag = tagsInline.pop() ) ) {
						CKEDITOR.dtd[i]['comcode-' + tag] = 1;
					}
				}
			}
			CKEDITOR.dtd[i]['tempcode'] = 1;

			// <tag> { ... }
			cssBlock += '{' +
				'background-repeat:no-repeat;' +
				'border:1px dotted gray;' +
				'padding:14px 0 0 0;' +
				'display:block' +
			'}';

			// <tag> { ... }
			cssInline += '{' +
				'background-repeat:no-repeat;' +
				'border:1px dotted gray;' +
				'padding:10px 0 0 0;' +
				'display:inline-block' +
			'}';

			// .cke_contents_ltr <tag> { ... }
			cssLtr += '{' +
				'background-position:2px 2px;' +
				'padding-left:8px' +
			'}';

			// .cke_contents_rtl <tag> { ... }
			cssRtl += '{' +
				'background-position:top right;' +
				'padding-right:8px' +
			'}';

			cssWhenDisabled = '[contenteditable=false]{' +
				'border:none;' +
				'padding:0;' +
				'background-image:none' +
			'}';

			CKEDITOR.addCss( cssBlock.concat( cssInline, cssImg, cssLtr, cssRtl, cssWhenDisabled ) );
		}
	} );
} )();
