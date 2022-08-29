{TITLE}

<h2>{!W_SENTRY_QUESTION}</h2>

<form method="post" enctype="multipart/form-data" action="{$PAGE_LINK*,_SELF:_SELF}">
	{$INSERT_FORM_POST_SECURITY}

	<input type="hidden" name="dx" value="{DX*}" />
	<input type="hidden" name="dy" value="{DY*}" />
	<input type="hidden" name="type" value="answered" />

	<p>
		<label for="param">{QUESTION*}: <input type="text" id="param" class="form-control" name="param" /></label>
	</p>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
	</p>
</form>
