// Carefully work out toolbar
// Look to see if this Comcode button is here as a hint whether we are doing an advanced editor. Unfortunately we cannot put contextual Tempcode inside a JavaScript file, so this trick is needed.
var precisionEditing = $cms.isStaff() || (document.body.querySelectorAll('.js-comcode-button-box').length > 1);
var toolbar = [];
if (precisionEditing) {
    toolbar.push(['Source', '-']);
}
var toolbarEditActions = ['Cut', 'Copy', 'Paste', precisionEditing ? 'PasteText' : null, precisionEditing ? 'PasteFromWord' : null, precisionEditing ? 'PasteCode' : null];
if (boolVal('{$VALUE_OPTION;,commercial_spellchecker}')) {
    toolbarEditActions.push('-', 'SpellChecker', 'Scayt');
}
toolbar.push(toolbarEditActions);
toolbar.push(['Undo', 'Redo', precisionEditing ? '-' : null, precisionEditing ? 'Find' : null, precisionEditing ? 'Replace' : null, ((document.body.spellcheck !== undefined) ? 'spellchecktoggle' : null), '-', precisionEditing ? 'SelectAll' : null, 'RemoveFormat']);
toolbar.push(['Link', 'Unlink', 'Anchor', (precisionEditing ? 'composr_page' : null)]);
toolbar.push(precisionEditing ? '/' : '-');
var formatting = ['Bold', 'Italic', 'Strike', '-', precisionEditing ? 'Subscript' : null, (precisionEditing ? 'Superscript' : null)];
toolbar.push(formatting);
toolbar.push(['NumberedList', 'BulletedList', precisionEditing ? '-' : null, precisionEditing ? 'Outdent' : null, precisionEditing ? 'Indent' : null]);
if (precisionEditing) {
    toolbar.push(['JustifyLeft', 'JustifyCenter', 'JustifyRight', precisionEditing ? 'JustifyBlock' : null]);
}
toolbar.push([precisionEditing ? 'composr_image' : null, 'Table']);
if (precisionEditing) {
    toolbar.push(['HorizontalRule', 'SpecialChar']);
}
if (precisionEditing) {
    toolbar.push('/');
}
toolbar.push(['Format', 'Font', 'FontSize']);
toolbar.push(['TextColor', 'BGColor']);
if (precisionEditing) {
    toolbar.push(['Maximize', 'ShowBlocks', 'autoFormat']);
}
var useCMSToolbar = true;
if (useCMSToolbar) {
    toolbar.push(['composr_block', 'composr_comcode', 'composr_quote', (precisionEditing ? 'composr_box' : null), 'composr_code']);
}
var editorSettings = {
    skin: 'kama',
    uiColor: wysiwygColor,
    ocpTheme: $cms.getTheme(),
    removePlugins: 'sourcearea',
    extraPlugins: 'showcomcodeblocks,spellchecktoggle' + (useCMSToolbar ? ',composr' : ''),
    /*{+START,IF,{$NEQ,{$CKEDITOR_PATH},data_custom/ckeditor}}*/
    customConfig: '',
    /*{+END}*/
    bodyId: 'wysiwyg-editor',
    baseHref: $util.rel(),
    linkShowAdvancedTab: !$cms.configOption('eager_wysiwyg'),
    imageShowAdvancedTab: !$cms.configOption('eager_wysiwyg'),
    imageShowLinkTab: !$cms.configOption('eager_wysiwyg'),
    imageShowSizing: !$cms.configOption('eager_wysiwyg'),
    autoUpdateElement: true,
    contentsCss : pageStylesheets,
    cssStatic: css,
    language: $cms.userLang() ? $cms.userLang().toLowerCase() : 'en',
    emailProtection: false,
    resize_enabled: true,
    resize_minWidth : 320,
    resize_dir : 'both', /* Useful for testing responsive content */
    width: 'auto',
    height: ($cms.getPageName() === 'cms_comcode_pages') ? 250 : 500,
    toolbar: toolbar,
    allowedContent: true,
    browserContextMenuOnCtrl: true,
    comcodeXMLBlockTags: '{$COMCODE_TAGS;,{$WYSIWYG_COMCODE__XML_BLOCK}}',
    comcodeXMLInlineTags: '{$COMCODE_TAGS;,{$WYSIWYG_COMCODE__XML_INLINE}}',
    magicline_everywhere: true,
    sourceAreaTabSize: 4,
    font_names: '{$REPLACE;,\,,;,{$FONTS}}',
    linkDefaultProtocol: 'https://',
};

if ($cms.configOption('eager_wysiwyg') === 1) {
    editorSettings.enterMode = window.CKEDITOR.ENTER_BR;
} else {
    editorSettings.enterMode = window.CKEDITOR.ENTER_P;
    editorSettings.autoParagraph = false;
}

if ($cms.configOption('outline_blocks') === 1) {
    editorSettings.startupOutlineBlocks = true;
}

if ($cms.configOption('autogrow') === 1) {
    editorSettings.autoGrow_onStartup = true;
} else {
    editorSettings.removePlugins += ',autogrow';
}

if ($cms.configOption('wysiwyg_spellchecker_default') === 1) {
    editorSettings.wysiwygSpellcheckerDefault = true;
}

if ($cms.configOption('wysiwyg_font_units') === 'em') {
    editorSettings.fontSize_sizes = '0.6em;0.85em;1em;1.1em;1.2em;1.3em;1.4em;1.5em;1.6em;1.7em;1.8em;2em';
}

// Fix auto-formatting issue
CKEDITOR.dtd.$block.object = 1;
CKEDITOR.dtd.$block.param = 1;
