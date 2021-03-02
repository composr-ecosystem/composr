{TITLE}

<p>
	{!PT_RULES_PAGE_INTRO,{$DISPLAYED_USERNAME*,{USERNAME}}}
</p>

<div class="box box---cns-member-pt-rules-screen"><div class="box-inner">
	{RULES}
</div></div>

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	<p class="proceed-button">
		<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
	</p>
</form>
