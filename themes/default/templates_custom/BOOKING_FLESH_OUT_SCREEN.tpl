{$REQUIRE_JAVASCRIPT,booking}

<div data-tpl="bookingFleshOutScreen">
	{TITLE}

	{+START,SET,fleshed}
		{+START,LOOP,BOOKABLES}
			{+START,IF,{$OR,{BOOKABLE_SUPPORTS_NOTES},{$IS_NON_EMPTY,{BOOKABLE_SUPPLEMENTS}}}}
				<h2>{BOOKABLE_TITLE*}</h2>

				{+START,IF,{BOOKABLE_SUPPORTS_NOTES}}
					{+START,INCLUDE,BOOKABLE_NOTES}{+END}
				{+END}

				{+START,LOOP,BOOKABLE_SUPPLEMENTS}
					<div style="padding-left: 50px">
						<p>
							<label for="bookable_{BOOKABLE_ID*}_supplement_{SUPPLEMENT_ID*}_quantity">
								<h3>{SUPPLEMENT_TITLE*} ({!OPTIONAL_SUPPLEMENT})</h3>

								{+START,IF,{SUPPLEMENT_SUPPORTS_QUANTITY}}
									{!QUANTITY}:

									<select class="form-control js-change-recalculate-booking-price" id="bookable_{BOOKABLE_ID*}_supplement_{SUPPLEMENT_ID*}_quantity" name="bookable_{BOOKABLE_ID*}_supplement_{SUPPLEMENT_ID*}_quantity">
										{$SET,quantity,0}
										{+START,WHILE,{$LT,{$GET,quantity},51}}
											<option {+START,IF,{$EQ,{SUPPLEMENT_QUANTITY},{$GET,quantity}}} selected="selected"{+END} value="{$GET*,quantity}">{$INTEGER_FORMAT*,{$GET,quantity}}</option>
											{$INC,quantity}
										{+END}
									</select>
								{+END}

								{+START,IF,{$NOT,{SUPPLEMENT_SUPPORTS_QUANTITY}}}
									{!I_WANT_THIS}

									<input class="js-change-recalculate-booking-price"{+START,IF,{$GT,{SUPPLEMENT_QUANTITY},0}} checked="checked"{+END} type="checkbox" id="bookable_{BOOKABLE_ID*}_supplement_{SUPPLEMENT_ID*}_quantity" name="bookable_{BOOKABLE_ID*}_supplement_{SUPPLEMENT_ID*}_quantity" value="1" />
								{+END}
							</label>
						</p>

						{+START,IF,{SUPPLEMENT_SUPPORTS_NOTES}}
							<p class="lonely-label"><label for="bookable_{BOOKABLE_ID*}_supplement_{SUPPLEMENT_ID*}_notes">{!NOTES_FOR_US}:</label></p>
							<textarea cols="50" rows="1" id="bookable_{BOOKABLE_ID*}_supplement_{SUPPLEMENT_ID*}_notes" name="bookable_{BOOKABLE_ID*}_supplement_{SUPPLEMENT_ID*}_notes" class="form-control">{SUPPLEMENT_NOTES*}</textarea>
						{+END}
					</div>
				{+END}
			{+END}
		{+END}
	{+END}

	{+START,IF_NON_EMPTY,{$TRIM,{$GET,fleshed}}}
		<p>{!BOOKING_FLESH_OUT}</p>
	{+END}
	{+START,IF_EMPTY,{$TRIM,{$GET,fleshed}}}
		<p>{!_BOOKING_FLESH_OUT}</p>
	{+END}

	<form action="{POST_URL*}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			{HIDDEN}

			{$GET,fleshed}
		</div>

		{+START,IF_NON_EMPTY,{$TRIM,{$GET,fleshed}}}
			<hr class="spaced-rule" />
		{+END}

		<div class="box box---booking-flesh-out-screen"><div class="box-inner">
			<strong>{!PRICE_AUTO_CALC}:</strong> {$CURRENCY_SYMBOL,{CURRENCY}} <span id="price">{PRICE*}</span>
		</div></div>

		<p class="proceed-button">
			<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {$?,{$IS_GUEST},{!PROCEED},{!BOOK}}</button>
		</p>
	</form>

	<form action="{BACK_URL*}" method="post">
		<div>
			{HIDDEN}
			<button type="submit" title="{!NEXT_ITEM_BACK}"><img alt="{!NEXT_ITEM_BACK}" width="48" height="48" src="{$IMG*,icons/admin/back}"></button>
		</div>
	</form>
</div>
