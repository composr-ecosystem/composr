{TITLE}

<p>{!Q_SURE}</p>

<form method="post" enctype="multipart/form-data" action="{URL*}">
	{$INSERT_FORM_POST_SECURITY}

	<input type="hidden" name="type" value="{COMMAND*}" />
	<input type="hidden" name="item" value="{ITEM*}" />
	<input type="hidden" name="member" value="{MEMBER*}" />
	<input type="hidden" name="param" value="{PARAM*}" />

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
	</p>
</form>
