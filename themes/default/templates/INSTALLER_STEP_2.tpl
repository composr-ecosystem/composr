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
				<input type="radio" id="advertise_on_2" name="advertise_on" value="2"{+START,IF,{$NOT,{OFFICIAL_GIT}}}checked="checked" {+END}>
				<label for="advertise_on_2">{!ADVERTISE_ON_COMPOSR_2}</label><br>
				<input type="radio" id="advertise_on_1" name="advertise_on" value="1">
				<label for="advertise_on_1">{!ADVERTISE_ON_COMPOSR_1}</label><br>
				<input type="radio" id="advertise_on_0" name="advertise_on" value="0"{+START,IF,{OFFICIAL_GIT}}checked="checked" {+END}>
				<label for="advertise_on_0">{!ADVERTISE_ON_COMPOSR_0}</label><br>
				<small>{!DESCRIPTION_ADVERTISE_ON_COMPOSR}</small>
			</p>
		</div>

		<p>{!EMAIL_NEWSLETTER}</p>
	</div>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--yes" data-disable-on-click="1" type="submit">{+START,INCLUDE,ICON}NAME=buttons/yes{+END} <span>{!I_AGREE}</span></button>
	</p>
</form>
