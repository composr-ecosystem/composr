<h2>{!BOOKINGS}</h2>

{+START,LOOP,DETAILS}
	<h3>{BOOKABLE_TITLE*}</h3>

	<table class="wide-table results-table"><tbody>
		<tr>
			<th>{!QUANTITY}</th>
			<td>{QUANTITY*}</td>
		</tr>
		<tr>
			<th>{!DATE}</th>
			<td>{START*}{+START,IF_NON_EMPTY,{END}} &ndash; {END*}{+END}</td>
		</tr>
		{+START,IF_NON_EMPTY,{NOTES}}
			<tr>
				<th>{!NOTES}</th>
				<td>{NOTES*}</td>
			</tr>
		{+END}
		{+START,IF_NON_EMPTY,{SUPPLEMENTS}}
			<tr>
				<th>{!OPTIONS}</th>
				<td>
					{+START,LOOP,SUPPLEMENTS}
						<div>
							{SUPPLEMENT_TITLE*} &times; {SUPPLEMENT_QUANTITY*}

							{+START,IF_NON_EMPTY,{SUPPLEMENT_NOTES}}
								({SUPPLEMENT_NOTES*})
							{+END}
						</div>
					{+END}
				</td>
			</tr>
		{+END}
	</tbody></table>
{+END}

<h2>{!DETAILS}</h2>

<table class="wide-table results-table"><tbody>
	<tr>
		<th>{!_TOTAL_PRICE}</th>
		<td>{PRICE*}</td>
	</tr>
	<tr>
		<th>{!CUSTOMER_NAME}</th>
		<td>
			{+START,IF,{$CONFIG_OPTION,member_booking_only}}
				<a href="{$MEMBER_PROFILE_URL*,{MEMBER_ID}}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>
			{+END}
			{+START,IF,{$NOT,{$CONFIG_OPTION,member_booking_only}}}
				{USERNAME*}
			{+END}
		</td>
	</tr>
	<tr>
		<th>{!CUSTOMER_EMAIL}</th>
		<td>{EMAIL_ADDRESS*}</td>
	</tr>
	{+START,IF,{$NOT,{$CONFIG_OPTION,member_booking_only}}}
		<tr>
			<th>{!MOBILE_NUMBER}</th>
			<td>{MOBILE_NUMBER*}</td>
		</tr>
		<tr>
			<th>{!PHONE_NUMBER}</th>
			<td>{PHONE_NUMBER*}</td>
		</tr>
	{+END}
</tbody></table>
