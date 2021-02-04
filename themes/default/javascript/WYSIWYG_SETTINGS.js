// Carefully work out toolbar
var precision_editing=((typeof window.cms_is_staff!='undefined') && window.cms_is_staff) || (get_elements_by_class_name(document.body,'comcode_button_box').length>1); // Look to see if this Comcode button is here as a hint whether we are doing an advanced editor. Unfortunately we cannot put contextual Tempcode inside a JavaScript file, so this trick is needed.
var toolbar=[];
if (precision_editing)
	toolbar.push(['Source','-']);
toolbar.push(['Cut','Copy','Paste',precision_editing?'PasteText':null,precision_editing?'PasteFromWord':null,precision_editing?'PasteCode':null{+START,IF,{$VALUE_OPTION,commercial_spellchecker}},'-','SpellChecker', 'Scayt'{+END}]);
toolbar.push(['Undo','Redo',precision_editing?'-':null,precision_editing?'Find':null,precision_editing?'Replace':null,(typeof document.body.spellcheck!='undefined')?'spellchecktoggle':null,'-',precision_editing?'SelectAll':null,'RemoveFormat']);
toolbar.push(['Link','Unlink']);
toolbar.push(precision_editing?'/':'-');
var formatting=['Bold','Italic','Strike','-',precision_editing?'Subscript':null,precision_editing?'Superscript':null];
toolbar.push(formatting);
toolbar.push(['NumberedList','BulletedList',precision_editing?'-':null,precision_editing?'Outdent':null,precision_editing?'Indent':null]);
if (precision_editing)
	toolbar.push(['JustifyLeft','JustifyCenter','JustifyRight',precision_editing?'JustifyBlock':null]);
toolbar.push([precision_editing?'composr_image':null,'Table']);
if (precision_editing)
	toolbar.push(['HorizontalRule','SpecialChar']);
if (precision_editing)
	toolbar.push('/');
toolbar.push(['Format','Font','FontSize']);
toolbar.push(['TextColor','BGColor']);
if (precision_editing)
	toolbar.push(['Maximize', 'ShowBlocks']);
var use_composr_toolbar=true;
if (use_composr_toolbar)
	toolbar.push(['composr_block','composr_comcode',precision_editing?'composr_page':null,'composr_quote',precision_editing?'composr_box':null,'composr_code']);

var editor_settings={
	skin: 'kama',
	enterMode : window.CKEDITOR.ENTER_BR,
	uiColor : wysiwyg_color,
	ocpTheme : '{$THEME;^}',
	{+START,IF,{$NOT,{$VALUE_OPTION,wysiwyg_normal_font_sizes}}}
		fontSize_sizes : '0.6em;0.85em;1em;1.1em;1.2em;1.3em;1.4em;1.5em;1.6em;1.7em;1.8em;2em',
	{+END}
	removePlugins: '{+START,IF,{$NOT,{$CONFIG_OPTION,autogrow}}}autogrow{+END}',
	extraPlugins: 'showcomcodeblocks,spellchecktoggle'+(use_composr_toolbar?',composr':''),
	{+START,IF,{$NEQ,{$CKEDITOR_PATH},data_custom/ckeditor}}
		customConfig: '',
	{+END}
	bodyId : 'wysiwyg_editor',
	baseHref : get_base_url()+'/',
	linkShowAdvancedTab : {$?,{$CONFIG_OPTION,eager_wysiwyg},false,true},
	imageShowAdvancedTab : {$?,{$CONFIG_OPTION,eager_wysiwyg},false,true},
	imageShowLinkTab : {$?,{$CONFIG_OPTION,eager_wysiwyg},false,true},
	imageShowSizing : {$?,{$CONFIG_OPTION,eager_wysiwyg},false,true},
	autoUpdateElement : true,
	contentsCss : page_stylesheets,
	cssStatic : css,
	startupOutlineBlocks : true,
	language : (window.cms_lang)?cms_lang.toLowerCase():'en',
	emailProtection : false,
	resize_enabled : true,
	width : 'auto',
	height : (window.location.href.indexOf('cms-comcode-pages')==-1)?250:500,
	toolbar : toolbar,
	allowedContent: true,
	browserContextMenuOnCtrl: true,
	comcodeXMLBlockTags: '{$COMCODE_TAGS;,{$WYSIWYG_COMCODE__XML_BLOCK}}',
	comcodeXMLInlineTags: '{$COMCODE_TAGS;,{$WYSIWYG_COMCODE__XML_INLINE}}',
	{+START,IF,{$CONFIG_OPTION,autogrow}}
		autoGrow_onStartup: true,
	{+END}
	{+START,IF,{$VALUE_OPTION,wysiwyg_spellchecker_default}}
		wysiwygSpellcheckerDefault: true,
	{+END}
	magicline_everywhere: true
};

// Fix auto-formatting issue
CKEDITOR.dtd.$block.object = 1;
CKEDITOR.dtd.$block.param = 1;
