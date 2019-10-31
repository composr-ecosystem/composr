<div data-toggleable-tray="{}">
	<h2 class="js-tray-header">
		<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
			NAME=trays/expand
			ICON_SIZE=20
		{+END}</a>
		<span class="js-tray-onclick-toggle-tray">{!GIFTR_TITLE}</span>
	</h2>

	{$REQUIRE_CSS,gifts}

	<div class="toggleable-tray js-tray-content" style="display: none" aria-expanded="false">
		{+START,LOOP,GIFTS}
			<div class="box box---cns-member-screen-gifts-wrap"><div class="box-inner">
				<div class="clearfix">
					{+START,IF_NON_EMPTY,{IMAGE_URL}}
						<img width="50" src="{$THUMBNAIL*,{IMAGE_URL},50}" class="left float-separation" />
					{+END}

					{GIFT_EXPLANATION}
				</div>
			</div></div>
		{+END}

		{+START,IF_EMPTY,{GIFTS}}
			<p class="nothing-here">
				<span class="cns-member-detailer-titler">{!NO_ENTRIES}</span>
			</p>
		{+END}
	</div>
</div>
