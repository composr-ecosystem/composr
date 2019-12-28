<div class="points-boxes">
	<div class="points-box box">
		{+START,IF,{$HAS_PRIVILEGE,use_points,{MEMBER}}}
			<p class="intro">{!CURRENT_POINTS}:</p>
			<p>{!POINTS_TO_SPEND,<span class="figure">{REMAINING*}</span>}</p>
		{+END}
		{+START,IF,{$NOT,{$HAS_PRIVILEGE,use_points,{MEMBER}}}}
			{!NO_PERMISSION_TO_USE_POINTS}
		{+END}
	</div>

	<div class="points-box box">
		<p class="intro">{!COUNT_GIFT_POINTS_LEFT}:</p>
		<p>{!POINTS_TO_GIVE,<span class="figure">{GIFT_POINTS_AVAILABLE*}</span>}</p>
	</div>
</div>

<div class="points-earned">
	<h2>{!POINTS_EARNED}</h2>

	<p>
		{!VIEWING_POINTS_PROFILE_OF,<a href="{PROFILE_URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>}
	</p>

	<table class="columned-table autosized-table points-summary-table">
		<thead>
			<tr>
				<th>{!ACTIVITY}</th>
				<th>{!AMOUNT}</th>
				<th>{!COUNT_TOTAL}</th>
			</tr>
		</thead>

		<tbody>
		{+START,LOOP,POINTS_RECORDS}
			{+START,IF,{$NEQ,{COUNT},0}}
				<tr>
					<td>&bull;&nbsp;{LABEL*}:</td>
					<td class="equation">{COUNT*} &times; {POINTS_EACH*} {!POINTS_UNIT}</td>
					<td class="answer">= {POINTS_TOTAL*} {!POINTS_UNIT}</td>
				</tr>
			{+END}
		{+END}
		</tbody>
	</table>
</div>

{+START,IF_NON_EMPTY,{TO}}
	<p>{!POINTS_IN_ADDITION,{$DISPLAYED_USERNAME*,{USERNAME}},{POINTS_GAINED_GIVEN*}}</p>
{+END}

<h2>{!POINTS_RECEIVED}</h2>

{+START,IF_NON_EMPTY,{TO}}
	{$SET,ajax_points_profile_to_wrapper,ajax-points-profile-to-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_to_wrapper}">
		{TO}
	</div>
{+END}
{+START,IF_EMPTY,{TO}}
	<p class="nothing-here">{!NONE}</p>
{+END}

{+START,IF_NON_EMPTY,{GIVE}}
	<div class="box box---points-profile"><div class="box-inner">
		{GIVE}
	</div></div>
{+END}

{+START,IF_NON_EMPTY,{FROM}}
	<h2>{!POINTS_GIFTED}</h2>

	<p>{!_POINTS_GIFTED,{$DISPLAYED_USERNAME*,{USERNAME}},{GIFT_POINTS_USED*}}</p>

	{$SET,ajax_points_profile_from_wrapper,ajax-points-profile-from-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_from_wrapper}">
		{FROM}
	</div>
{+END}

{+START,IF_NON_EMPTY,{CHARGELOG_DETAILS}}
	<h2>{!POINTS_SPENT}</h2>

	<p>{!_POINTS_SPENT,{$DISPLAYED_USERNAME*,{USERNAME}},{POINTS_USED*}}</p>

	{$SET,ajax_points_profile_chargelog_wrapper,ajax-points-profile-chargelog-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_chargelog_wrapper}">
		{CHARGELOG_DETAILS}
	</div>
{+END}
