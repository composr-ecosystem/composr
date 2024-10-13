{$SET,rndx,{$RAND}}

<li>
	<label for="banned_{$GET*,rndx}"><a href="{LOOKUP_URL*}">{IP*}</a> <span class="associated-details">({DATE*})</span>{+START,IF_NON_EMPTY,{RISK_SCORE}} <span class="associated-details">({!security:RISK*} {RISK_SCORE*})</span>{+END}
	{+START,IF,{$ADDON_INSTALLED,securitylogging}}
		<span class="horiz-field-sep"><em>{!BANNED}: <input type="checkbox" id="banned_{$GET*,rndx}" name="banned[]" value="{IP*}"{+START,IF,{BANNED}} checked="checked"{+END} /></em></span>
	{+END}
	</label>
</li>
