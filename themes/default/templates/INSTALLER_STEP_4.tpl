<form data-tpl="installerStep4" id="form-installer-step-4" title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
	{HIDDEN}

	<div>
		<div class="installer-main-min">
			{MESSAGE}

			{SECTIONS}
		</div>

		<p class="proceed-button">
			<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!INSTALL} Composr</span></button>
		</p>
	</div>
</form>
