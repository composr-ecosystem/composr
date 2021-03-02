{TITLE}

{+START,IF_EMPTY,{CATEGORIES}}
	<p class="nothing-here">{!NO_CATEGORIES}</p>
{+END}

{+START,IF_NON_EMPTY,{CATEGORIES}}
	<p>{!BOOKING_START}</p>
{+END}

{+START,IF_NON_EMPTY,{SHARED_MESSAGES}}
	<p class="lonely-label">{!NOTICES}:</p>
	<ul>
		{+START,LOOP,SHARED_MESSAGES}
			<li>{_loop_var}</li>
		{+END}
	</ul>
{+END}

{+START,IF_NON_EMPTY,{CATEGORIES}}
	<form action="{POST_URL*}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			{HIDDEN}

			{+START,IF,{$NOT,{HAS_MIXED_DATE_TYPES}}}
				<h2>{!DATES}</h2>

				<span><strong>{!FROM}</strong></span>
				{+START,INCLUDE,BOOK_DATE_CHOOSE}
					NAME=bookable_date_from
					CURRENT_DAY={DATE_FROM_DAY}
					CURRENT_MONTH={DATE_FROM_MONTH}
					CURRENT_YEAR={DATE_FROM_YEAR}
					MIN_DATE_DAY={MIN_DATE_DAY}
					MIN_DATE_MONTH={MIN_DATE_MONTH}
					MIN_DATE_YEAR={MIN_DATE_YEAR}
					MAX_DATE_DAY={MAX_DATE_DAY}
					MAX_DATE_MONTH={MAX_DATE_MONTH}
					MAX_DATE_YEAR={MAX_DATE_YEAR}
				{+END}

				{+START,IF,{HAS_DATE_RANGES}}
					<span><strong>{!TO}</strong></span>
					{+START,INCLUDE,BOOK_DATE_CHOOSE}
						NAME=bookable_date_to
						CURRENT_DAY={DATE_FROM_DAY}
						CURRENT_MONTH={DATE_FROM_MONTH}
						CURRENT_YEAR={DATE_FROM_YEAR}
						MIN_DATE_DAY={MIN_DATE_DAY}
						MIN_DATE_MONTH={MIN_DATE_MONTH}
						MIN_DATE_YEAR={MIN_DATE_YEAR}
						MAX_DATE_DAY={MAX_DATE_DAY}
						MAX_DATE_MONTH={MAX_DATE_MONTH}
						MAX_DATE_YEAR={MAX_DATE_YEAR}
					{+END}
				{+END}
			{+END}

			<p class="associated-details">{!ALL_DATES_IN,{$TIMEZONE*}}</p>

			{+START,LOOP,CATEGORIES}
				<h2>{CATEGORY_TITLE*}</h2>

				<div class="wide-table-wrap">
					<table class="wide-table results-table columned-table spaced-table responsive-table">
						<thead>
							<tr>
								<th>{!ITEM}</th>
								<th>{!QUANTITY_WANTED}</th>
								{+START,IF,{HAS_MIXED_DATE_TYPES}}
									<th>{!FROM}</th>
									<th>{!TO}</th>
								{+END}
								{+START,IF,{HAS_DETAILS}}
									<th>{!DETAILS}</th>
								{+END}
								<th>{!PRICE}</th>
							</tr>
						</thead>

						<tbody>
							{+START,LOOP,BOOKABLES}
								<tr>
									<th class="de-th vertical-alignment">
										<strong>{BOOKABLE_TITLE*}</strong>
									</th>

									<td class="vertical-alignment">
										<label class="accessibility-hidden" for="bookable_{BOOKABLE_ID*}_quantity">{!QUANTITY}, {BOOKABLE_TITLE*}</label>
										<select name="bookable_{BOOKABLE_ID*}_quantity" id="bookable_{BOOKABLE_ID*}_quantity" class="form-control">
											{$SET,quantity,0}
											{+START,WHILE,{$LT,{$GET,quantity},{$ADD,{BOOKABLE_QUANTITY_AVAILABLE},1}}}
												<option {+START,IF,{$EQ,{BOOKABLE_QUANTITY},{$GET,quantity}}} selected="selected"{+END} value="{$GET*,quantity}">{!UNIT_TYPE,{$NUMBER_FORMAT*,{$GET,quantity}}}</option>
												{$INC,quantity}
											{+END}
										</select>
									</td>

									{+START,IF,{HAS_MIXED_DATE_TYPES}}
										<td class="vertical-alignment">
											{+START,INCLUDE,BOOK_DATE_CHOOSE}
												NAME=bookable_{BOOKABLE_ID}_date_from
												CURRENT_DAY={BOOKABLE_DATE_FROM_DAY}
												CURRENT_MONTH={BOOKABLE_DATE_FROM_MONTH}
												CURRENT_YEAR={BOOKABLE_DATE_FROM_YEAR}
												MIN_DATE_DAY={BOOKABLE_MIN_DATE_DAY}
												MIN_DATE_MONTH={BOOKABLE_MIN_DATE_MONTH}
												MIN_DATE_YEAR={BOOKABLE_MIN_DATE_YEAR}
												MAX_DATE_DAY={BOOKABLE_MAX_DATE_DAY}
												MAX_DATE_MONTH={BOOKABLE_MAX_DATE_MONTH}
												MAX_DATE_YEAR={BOOKABLE_MAX_DATE_YEAR}
											{+END}
										</td>

										<td class="vertical-alignment">
											{+START,IF,{BOOKABLE_SELECT_DATE_RANGE}}
												{+START,INCLUDE,BOOK_DATE_CHOOSE}
													NAME=bookable_{BOOKABLE_ID}_date_to
													CURRENT_DAY={BOOKABLE_DATE_TO_DAY}
													CURRENT_MONTH={BOOKABLE_DATE_TO_MONTH}
													CURRENT_YEAR={BOOKABLE_DATE_TO_YEAR}
													MIN_DATE_DAY={BOOKABLE_MIN_DATE_DAY}
													MIN_DATE_MONTH={BOOKABLE_MIN_DATE_MONTH}
													MIN_DATE_YEAR={BOOKABLE_MIN_DATE_YEAR}
													MAX_DATE_DAY={BOOKABLE_MAX_DATE_DAY}
													MAX_DATE_MONTH={BOOKABLE_MAX_DATE_MONTH}
													MAX_DATE_YEAR={BOOKABLE_MAX_DATE_YEAR}
												{+END}
											{+END}
										</td>
									{+END}

									{+START,IF,{HAS_DETAILS}}
										<td class="vertical-alignment">
											{BOOKABLE_DESCRIPTION}

											{+START,IF_NON_EMPTY,{BOOKABLE_MESSAGES}}
												<ul>
													{+START,LOOP,BOOKABLE_MESSAGES}
														<li>{_loop_var}</li>
													{+END}
												</ul>
											{+END}
										</td>
									{+END}

									<td class="vertical-alignment">
										{$CURRENCY,{BOOKABLE_PRICE},{CURRENCY},{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}
										<span class="associated-details">{!BOOKING_PER}</span>
									</td>
								</tr>
							{+END}
						</tbody>
					</table>
				</div>
			{+END}
		</div>

		<p class="proceed-button">
			<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
		</p>
	</form>
{+END}
