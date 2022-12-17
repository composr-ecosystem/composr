{$SET,button_type,primary}
{+START,IF,{$EQ,{IMG},admin/delete3}}
	{$SET,button_type,danger}
{+END}
{+START,IF,{$EQ,{IMG},admin/warn}}
	{$SET,button_type,warning}
{+END}
{+START,IF,{$OR,{$NEQ,{$PAGE},members},{$NEQ,{IMG},buttons/new_quote}}}
	{+START,IF,{$NOT,{IMMEDIATE}}}
		<a data-tpl="buttonScreenItem" data-tpl-params="{+START,PARAMS_JSON,ONCLICK_CALL_FUNCTIONS,ONMOUSEDOWN_CALL_FUNCTIONS}{_*}{+END}" class="btn btn-{$GET*,button_type} btn-scri {$REPLACE,_,-,{$REPLACE,/,--,{IMG}}}"{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END}{+START,IF_PASSED,REL} rel="{REL*}"{+END}{+START,IF_PASSED,EXTRA_ATTRS} {EXTRA_ATTRS}{+END} href="{URL*}">{+START,INCLUDE,ICON}NAME={IMG}{+END} <span>{TITLE*}</span></a>
	{+END}

	{+START,IF,{IMMEDIATE}}
		<form data-tpl="buttonScreenItem" title="{TITLE*}" class="inline" action="{URL*}"{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END} method="post">
			{+START,IF_PASSED,HIDDEN}{$INSERT_FORM_POST_SECURITY}{HIDDEN}{+END}<button class="btn btn-{$GET*,button_type} btn-scri {$REPLACE,_,-,{$REPLACE,/,--,{IMG}}}" type="submit" title="{FULL_TITLE*}">{+START,INCLUDE,ICON}NAME={IMG}{+END} {TITLE*}</button>
		</form>
	{+END}
{+END}
