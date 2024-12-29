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
				<input type="radio" id="telemetry_2" name="telemetry" value="2"{+START,IF,{$NOT,{OFFICIAL_GIT}}}checked="checked" {+END}>
				<label for="telemetry_2">{!TELEMETRY_HOMESITE_2,{$BRAND_BASE_URL}}</label><br>
				<input type="radio" id="telemetry_1" name="telemetry" value="1">
				<label for="telemetry_1">{!TELEMETRY_HOMESITE_1,{$BRAND_BASE_URL}}</label><br>
				<input type="radio" id="telemetry_0" name="telemetry" value="0"{+START,IF,{OFFICIAL_GIT}}checked="checked" {+END}>
				<label for="telemetry_0">{!TELEMETRY_HOMESITE_0,{$BRAND_BASE_URL}}</label><br>
				<input type="checkbox" id="telemetry_may_feature" name="telemetry_may_feature" value="1"{+START,IF,{OFFICIAL_GIT}}checked="checked" {+END}>
				<label for="telemetry_may_feature">{!TELEMETRY_MAY_FEATURE_HOMESITE,{$BRAND_BASE_URL}}</label><br>
				<p><small>{!TELEMETRY_HOMESITE_CAN_CHANGE_LATER}</small></p>
				<p><small>{!DESCRIPTION_TELEMETRY_HOMESITE}</small></p>
			</p>
		</div>

		<p>{!EMAIL_NEWSLETTER}</p>
	</div>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--yes" data-disable-on-click="1" type="submit">{+START,INCLUDE,ICON}NAME=buttons/yes{+END} <span>{!I_AGREE}</span></button>
	</p>
</form>
