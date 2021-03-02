{TITLE}

<p>
	{!W_ANGRY_TROLL,{TROLL*}}
</p>

<form action="{$PAGE_LINK*,_SELF:_SELF}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	{QUESTIONS}
	<p><button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button></p>
</form>
