{$REQUIRE_JAVASCRIPT,core_rich_media}
{$SET,RAND_ID_OVERLAY,overlay-{$RAND}}
<div role="dialog" class="comcode-overlay box" id="{$GET,RAND_ID_OVERLAY}" data-tpl="comcodeOverlay" data-tpl-params="{+START,PARAMS_JSON,ID,RAND_ID_OVERLAY,TIMEOUT,TIMEIN,X}{_*}{+END}"
	style="display: none; position: absolute; left: {X*}px; top: {Y*}px; width: {WIDTH*}px; height: {HEIGHT*}px">
	<div class="comcode-overlay-inner box-inner">
		<div class="comcode-overlay-main">
			{EMBED}
		</div>

		<div class="comcode-overlay-dismiss">
			<p class="associated-link suggested-link">
				<a href="#!" class="js-click-dismiss-overlay">{!DISMISS}</a>
			</p>
		</div>
	</div>
</div>
