<form title="{!TERMS}" class="installer-cms-licence" action="install.php" method="post">
	<div class="installer-terms-title"><label for="licence">{!TERMS}</label></div>
	<div>
		<textarea readonly="readonly" class="mono-textbox form-control form-control-wide" id="licence" name="licence" cols="90" rows="17">{LICENCE*}</textarea>
	</div>
</form>

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
	{HIDDEN}

	<div class="clearfix">
		<div id="install-newsletter">
			<p class="accessibility-hidden"><label for="email">{!EMAIL_ADDRESS}</label></p>
			<div>
				<input maxlength="255" class="form-control form-control-wide" id="email" name="email" type="email" placeholder="{!EMAIL_ADDRESS_FOR_NEWSLETTER}" size="25" />
			</div>

			<p>
				<input type="checkbox" checked="checked" value="1" name="advertise_on" id="advertise_on" />
				<label for="advertise_on">{!ADVERTISE_ON_COMPOSR}</label>
			</p>
		</div>

		<p>{!EMAIL_NEWSLETTER}</p>
	</div>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--yes" data-disable-on-click="1" type="submit">{+START,INCLUDE,ICON}NAME=buttons/yes{+END} <span>{!I_AGREE}</span></button>
	</p>
</form>
