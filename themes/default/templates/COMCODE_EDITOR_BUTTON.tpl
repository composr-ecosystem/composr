{$REQUIRE_JAVASCRIPT,core_rich_media}
{+START,IF,{$NAND,{$MATCH_KEY_MATCH,_WILD:admin_zones},{$EQ,{B},code,quote,url}}}
	{+START,IF,{DIVIDER}}<span class="divider"></span>{+END}
	<a href="#!" data-tpl="comcodeEditorButton" data-tpl-params="{+START,PARAMS_JSON,IS_POSTING_FIELD,B,FIELD_NAME}{_*}{+END}" {+START,IF,{$AND,{IS_POSTING_FIELD},{$EQ,{B},thumb,img}}} id="js-attachment-browse-button--{FIELD_NAME*}" style="display: none;"{+END} class="for-field-{FIELD_NAME*} btn btn-primary btn-comcode btn-comcode-{B*} js-comcode-button-{B*}" title="{TITLE*}">{+START,TRIM}
		<span class="btn-comcode-text">
			{$REPLACE, ,<br />,{LABEL}}
		</span>
		{+START,INCLUDE,ICON}NAME=comcode_editor/{B}{+END}
	{+END}</a>
{+END}
