{+START,IF_PASSED,URL}
	{+START,IF,{$NOT,{IMMEDIATE}}}<a class="btn btn-primary btn-scr {$REPLACE,_,-,{$REPLACE,/,--,{IMG}}}"{+START,IF_PASSED,REL} rel="{REL*}"{+END} href="{URL*}"><span>{+START,IF_PASSED,IMG}{+START,INCLUDE,ICON}NAME={IMG}{+END}{+END} {TITLE*}</span></a>{+END}
	{+START,IF,{IMMEDIATE}}<form title="{TITLE*}" class="inline" action="{URL*}" method="post">{+START,IF_PASSED,HIDDEN}{$INSERT_FORM_POST_SECURITY}{HIDDEN}{+END}<button type="submit" class="btn btn-primary btn-scr {$REPLACE,_,-,{$REPLACE,/,--,{IMG}}}">{+START,IF_PASSED,IMG}{+START,INCLUDE,ICON}NAME={IMG}{+END}{+END} {TITLE*}</button></form>{+END}
{+END}
{+START,IF_NON_PASSED,URL}
	<button class="btn btn-primary btn-scr {$REPLACE,_,-,{$REPLACE,/,--,{IMG}}}{+START,IF_PASSED,JS_BTN} js-btn-{JS_BTN*}{+END}" name="{NAME*}" id="{NAME*}" rel="{REL*}"><span>{+START,IF_PASSED,IMG}{+START,INCLUDE,ICON}NAME={IMG}{+END}{+END} {TITLE*}</span></button>
{+END}