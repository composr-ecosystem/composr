<div data-tpl="pointsEscrow">
	<p class="points-send-box-header">
		<span>{!ESCROW_TO}</span>
		{+START,IF,{GIFT_POINTS_ENABLED}}
			{!ESCROW_TEXT_GIFT_POINTS,{VIEWER_GIFT_POINTS_BALANCE*},{VIEWER_POINTS_BALANCE*}}
		{+END}
		{+START,IF,{$NOT,{GIFT_POINTS_ENABLED}}}
			{!ESCROW_TEXT,{VIEWER_POINTS_BALANCE*}}
		{+END}
	</p>

	{FORM}
</div>
