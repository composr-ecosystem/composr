{$REQUIRE_JAVASCRIPT,fractional_edit}
{+START,SET,fractional_edit}
	{+START,IF,{$NOT,{EXPLICIT_EDITING_LINKS}}}
		<span data-tpl="fractionalEdit" data-tpl-params="{+START,PARAMS_JSON,EXPLICIT_EDITING_LINKS,edit_type,URL,EDIT_TEXT,EDIT_PARAM_NAME}{_*}{+END}" class="fractional-edit-nonover">{VALUE}</span>
	{+END}

	{+START,IF,{EXPLICIT_EDITING_LINKS}}
		<span>{VALUE}</span>

		<a data-tpl="fractionalEdit" data-tpl-params="{+START,PARAMS_JSON,EXPLICIT_EDITING_LINKS,edit_type,URL,EDIT_TEXT,EDIT_PARAM_NAME}{_*}{+END}" href="#!" class="associated-link">{!EDIT_TEXT}</a>
	{+END}
{+END}
