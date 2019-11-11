{$REQUIRE_JAVASCRIPT,core_rich_media}
{+START,IF,{$NAND,{$MATCH_KEY_MATCH,_WILD:admin_zones},{$EQ,{B},code,quote,url}}}
	{+START,IF,{DIVIDER}}<span class="divider"></span>{+END}
	<a href="#!" data-tpl="comcodeEditorButton" data-tpl-params="{+START,PARAMS_JSON,IS_POSTING_FIELD,B,FIELD_NAME}{_*}{+END}" {+START,IF,{$AND,{IS_POSTING_FIELD},{$EQ,{B},thumb,img}}} id="js-attachment-upload-button"{+END} class="for-field-{FIELD_NAME*} btn btn-primary btn-comcode btn-comcode-{B*} js-comcode-button-{B*}" title="{TITLE*}">{+START,TRIM}
		<div class="btn-comcode-text">
			{$?,{$EQ,{B},thumb},{!ADD_COMCODE_IMAGE}}{$?,{$EQ,{B},block},{!ADD_BLOCK}}{$?,{$EQ,{B},comcode},{!COMCODE_ADD_TAG}}{$?,{$EQ,{B},list},{!LIST}}{$?,{$EQ,{B},url},{!ADD_LINK}}
			{$?,{$EQ,{B},page},{!PAGE}}{$?,{$EQ,{B},quote},{!QUOTE}}{$?,{$EQ,{B},box},{!ADD_BOX}}{$?,{$EQ,{B},code},{!CODE}}{$?,{$EQ,{B},html},{!HTML}}{$?,{$EQ,{B},hide},{!HIDE}}
		</div>
		{+START,INCLUDE,ICON}NAME=comcode_editor/{B}{+END}
	{+END}</a>
{+END}
