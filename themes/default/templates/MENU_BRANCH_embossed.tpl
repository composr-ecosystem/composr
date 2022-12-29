<li class="{$?,{CURRENT},current,non-current}">
	<a {+START,INCLUDE,MENU_LINK_PROPERTIES}{+END}{+START,IF,{LAST}} class="last"{+END}>{+START,IF_NON_EMPTY,{IMG}}<img alt="" width="24" height="24" src="{IMG*}" /> {+END}<span>{CAPTION}</span></a>
</li>
{CHILDREN}
