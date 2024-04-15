<div onkeypress="return null;">
	{+START,IF,{SLIDESHOW}}
		<label for="slideshow_from" class="slideshow_speed">
			{!SPEED_IN_SECS}
			<input onclick="cancel_bubbling(event);" onchange="reset_slideshow_countdown();" onmousedown="stop_slideshow_timer('{!STOPPED;=}');" onkeypress="cancel_bubbling(event);" type="number" name="slideshow_from" id="slideshow_from" value="5" />
		</label>
		<input type="hidden" id="next_slide" name="next_slide" value="{SLIDESHOW_NEXT_URL*}" />
		<input type="hidden" id="previous_slide" name="previous_slide" value="{SLIDESHOW_PREVIOUS_URL*}" />
	{+END}

	{$REQUIRE_JAVASCRIPT,ajax}

	<div class="trinav_wrap" id="gallery_nav">
		<div class="trinav_left" onclick="/*Access-note: code has other activation*/ cancel_bubbling(event);">
			{$,Back}
			{+START,IF_NON_EMPTY,{BACK_URL}}
				<a class="button_screen buttons__previous"{+START,IF,{SLIDESHOW}} onclick="return slideshow_backward();"{+END} rel="prev" accesskey="j" href="{BACK_URL*}"><span>{!PREVIOUS}</span></a>
			{+END}
			{+START,IF_EMPTY,{BACK_URL}}
				<span class="button_screen buttons__previous_none"><span>{!PREVIOUS}</span></span>
			{+END}
		</div>

		<div class="trinav_right" onclick="/*Access-note: code has other activation*/ cancel_bubbling(event);">
			{$,Start slideshow}
			{+START,IF_NON_EMPTY,{SLIDESHOW_URL}}
				{+START,IF,{$NOT,{$MOBILE}}}
					{+START,IF,{$JS_ON}}{+START,IF,{$NOT,{SLIDESHOW}}}
						<a class="button_screen buttons__slideshow" rel="nofollow" target="_blank" title="{!SLIDESHOW} {!LINK_NEW_WINDOW}" href="{SLIDESHOW_URL*}"><span>{!_SLIDESHOW}</span></a>
					{+END}{+END}
				{+END}
			{+END}

			{$,Next}
			{+START,IF_NON_EMPTY,{NEXT_URL}}
				<a class="button_screen buttons__next"{+START,IF,{SLIDESHOW}} onclick="return slideshow_forward();"{+END} rel="next" accesskey="k" href="{NEXT_URL*}"><span>{!NEXT}</span></a>
			{+END}
			{+START,IF_EMPTY,{NEXT_URL}}
				<span class="button_screen buttons__next_none"><span>{!NEXT}</span></span>
			{+END}
		</div>

		<div class="trinav_mid text"><span>
			<script>// <![CDATA[
				add_event_listener_abstract(window,'real_load',function() {
					window.slideshow_current_position={_X%}-1;
					window.slideshow_total_slides={_N%};

					{+START,IF,{SLIDESHOW}}
						initialise_slideshow();
					{+END}
				});
			//]]></script>

			{+START,IF,{SLIDESHOW}}
				{!VIEWING_SLIDE,{X*},{N*}}

				{+START,IF_NON_EMPTY,{SLIDESHOW_NEXT_URL}}
					<span id="changer_wrap">{!CHANGING_IN,xxx}</span>
				{+END}

				{+START,IF_EMPTY,{NEXT_URL}}
					{!LAST_SLIDE}
				{+END}
			{+END}

			{+START,IF,{$NOT,{SLIDESHOW}}}
				{!GALLERY_MOBILE_PAGE_OF,{X*},{N*}}
			{+END}
		</span></div>
	</div>

	{$,Different positioning of slideshow button for mobiles, due to limited space}
	{+START,IF_NON_EMPTY,{SLIDESHOW_URL}}
		{+START,IF,{$MOBILE}}
			{+START,IF,{$JS_ON}}{+START,IF,{$NOT,{SLIDESHOW}}}
				<div class="float_surrounder">
					<div class="right">
						<a class="button_screen buttons__slideshow" rel="nofollow" target="_blank" title="{!SLIDESHOW} {!LINK_NEW_WINDOW}" href="{SLIDESHOW_URL*}"><span>{!_SLIDESHOW}</span></a>
					</div>
				</div>
			{+END}{+END}
		{+END}
	{+END}
</div>
