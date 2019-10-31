{$REQUIRE_JAVASCRIPT,realtime_rain}

<div class="webstandards-checker-off">
	{$SET,RAND_ID,bubble-id-{$RAND}}

	<div id="{$GET,RAND_ID}" class="bubble-wrap attitude-{$REPLACE%,_,-,{TYPE}}{$?,{IS_POSITIVE},-positive,}{$?,{IS_NEGATIVE},-negative,}" data-tpl="realtimeRainBubble" data-tpl-params="{+START,PARAMS_JSON,TICKER_TEXT,RELATIVE_TIMESTAMP,GROUP_ID,SPECIAL_ICON,MULTIPLICITY}{_*}{+END}">
		<div id="{$GET,RAND_ID}-main" class="bubble bubble-{$LCASE%,{$REPLACE,_,-,{TYPE}}}">
			<div class="clearfix">
				<div class="special-icon">
					{+START,IF_PASSED,SPECIAL_ICON}
						<a title="{SPECIAL_TOOLTIP*}">{+START,INCLUDE,ICON}
							NAME=realtime_rain/{SPECIAL_ICON}
							ICON_SIZE=36
						{+END}</a>
					{+END}
				</div>

				<div class="avatar-icon">
					{+START,IF_NON_EMPTY,{IMAGE}}
						<img src="{$ENSURE_PROTOCOL_SUITABILITY*,{IMAGE}}" alt="" />
					{+END}
				</div>
			</div>

			<h1>{TITLE*}</h1>

			<div class="linkage">
				{+START,IF_PASSED,URL}
					<a href="{URL*}">{!VIEW}</a>
				{+END}
			</div>
		</div>
	</div>
</div>
