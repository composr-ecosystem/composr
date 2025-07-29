{TITLE}

{$PARAGRAPH,{!cms_homesite:COMMON_ERRORS_TEXT}}

{+START,LOOP,ERRORS}

{+START,SET,comcode_box}
[hide="{ERROR_MESSAGE@}"]
{ERROR_SUMMARY}

[title="3"]{!HOW_DID_THIS_HAPPEN}[/title]
{ERROR_CAUSE}

[title="3"]{!HOW_DO_I_FIX}[/title]
{ERROR_RESOLUTION}
[/hide]
{+END}
	
	{$COMCODE,{$GET,comcode_box}}
{+END}