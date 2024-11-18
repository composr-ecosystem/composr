{+START,IF,{$NOT,{$HAS_PRIVILEGE,use_points,{MEMBER}}}}
	{!NO_PERMISSION_TO_USE_POINTS}
{+END}
{+START,IF,{$HAS_PRIVILEGE,use_points,{MEMBER}}}
	<div class="flex-wrapper points-boxes">
		<div class="points-box points-box-primary box">
			<p class="intro">{!COUNT_POINTS_BALANCE}:</p>
			<p><span class="figure">{POINTS_BALANCE*}</span></p>
		</div>

		{+START,IF,{$EQ,{$CONFIG_OPTION,enable_gift_points},1}}
			<div class="points-box points-box-primary box">
				<p class="intro">{!COUNT_GIFT_POINTS_BALANCE}:</p>
				<p><span class="figure">{GIFT_POINTS_BALANCE*}</span></p>
			</div>
		{+END}

		{+START,IF_NON_EMPTY,{VOTING_POWER}}
			<div class="points-box box">
				<p class="intro">{!cns_polls:VOTING_POWER}:</p>
				<p><span class="figure">{VOTING_POWER*}</span></p>
			</div>
		{+END}

		<div class="points-box box">
			<p class="intro">{!COUNT_RANK_POINTS}:</p>
			<p><span class="figure">{POINTS_RANK*}</span></p>
		</div>

		{+START,IF_EMPTY,{SENT_TABLE_AGGREGATE}{SENT_TABLE}}
			<div class="points-box box">
				<p class="intro">{!COUNT_POINTS_SENT}:</p>
				<p><span class="figure">{POINTS_SENT*}</span></p>
			</div>
		{+END}

		{+START,IF_EMPTY,{SPENT_TABLE_AGGREGATE}{SPENT_TABLE}}
			<div class="points-box box">
				<p class="intro">{!COUNT_POINTS_SPENT}:</p>
				<p><span class="figure">{POINTS_SPENT*}</span></p>
			</div>
		{+END}
	</div>
{+END}

{+START,IF_NON_EMPTY,{RECEIVED_TABLE_AGGREGATE}{RECEIVED_TABLE}{GIVE}}
	<h2>{!POINTS_RECEIVED}</h2>
{+END}
{+START,IF_NON_EMPTY,{RECEIVED_TABLE_AGGREGATE}}
	<p>{!POINTS_RECEIVED_AGGREGATE,{$DISPLAYED_USERNAME*,{USERNAME}},{POINTS_RECEIVED_AGGREGATE*}}</p>

	{$SET,ajax_points_profile_received_table_aggregate_wrapper,ajax-points-profile-received-table-aggregate-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_received_table_aggregate_wrapper}">
		{RECEIVED_TABLE_AGGREGATE}
	</div>
{+END}
{+START,IF_NON_EMPTY,{RECEIVED_TABLE}}
	<p>{!_POINTS_RECEIVED,{$DISPLAYED_USERNAME*,{USERNAME}},{POINTS_RECEIVED*}}</p>

	{$SET,ajax_points_profile_received_table_wrapper,ajax-points-profile-received-table-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_received_table_wrapper}">
		{RECEIVED_TABLE}
	</div>
{+END}

{+START,IF_NON_EMPTY,{GIVE}}
	<div class="box box---points-profile"><div class="box-inner">
		{GIVE}
	</div></div>
{+END}

{+START,IF_NON_EMPTY,{ESCROW_DETAILS}{ESCROW}}
	<h2>{!ESCROW_TRANSACTIONS}</h2>
{+END}

{+START,IF_NON_EMPTY,{ESCROW_DETAILS}}
	{$SET,ajax_points_profile_escrow_wrapper,ajax-points-profile-escrow-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_escrow_wrapper}">
		{ESCROW_DETAILS}
	</div>
{+END}
{+START,IF_NON_EMPTY,{ESCROW}}
	<div class="box box---points-profile"><div class="box-inner">
		{ESCROW}
	</div></div>
{+END}

{+START,IF_NON_EMPTY,{SENT_TABLE_AGGREGATE}{SENT_TABLE}}
	<h2>{!POINTS_SENT}</h2>
{+END}
{+START,IF_NON_EMPTY,{SENT_TABLE_AGGREGATE}}
	<p>{!POINTS_SENT_AGGREGATE,{$DISPLAYED_USERNAME*,{USERNAME}},{POINTS_SENT_AGGREGATE*}}</p>
	{$SET,ajax_points_profile_sent_table_aggregate_wrapper,ajax-points-profile-sent-table-aggregate-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_sent_table_aggregate_wrapper}">
		{SENT_TABLE_AGGREGATE}
	</div>
{+END}
{+START,IF_NON_EMPTY,{SENT_TABLE}}
	<p>{!_POINTS_SENT,{$DISPLAYED_USERNAME*,{USERNAME}},{POINTS_SENT*}}</p>
	{$SET,ajax_points_profile_sent_table_wrapper,ajax-points-profile-sent-table-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_sent_table_wrapper}">
		{SENT_TABLE}
	</div>
{+END}

{+START,IF_NON_EMPTY,{SPENT_TABLE_AGGREGATE}{SPENT_TABLE}}
	<h2>{!POINTS_SPENT}</h2>
{+END}
{+START,IF_NON_EMPTY,{SPENT_TABLE_AGGREGATE}}
	<p>{!POINTS_SPENT_AGGREGATE,{$DISPLAYED_USERNAME*,{USERNAME}},{POINTS_SPENT_AGGREGATE*}}</p>
	{$SET,ajax_points_profile_spent_table_aggregate_wrapper,ajax-points-profile-spent-table-aggregate-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_spent_table_aggregate_wrapper}">
		{SPENT_TABLE_AGGREGATE}
	</div>
{+END}
{+START,IF_NON_EMPTY,{SPENT_TABLE}}
	<p>{!_POINTS_SPENT,{$DISPLAYED_USERNAME*,{USERNAME}},{POINTS_SPENT*}}</p>
	{$SET,ajax_points_profile_spent_table_wrapper,ajax-points-profile-spent-table-wrapper-{$RAND%}}
	<div id="{$GET*,ajax_points_profile_spent_table_wrapper}">
		{SPENT_TABLE}
	</div>
{+END}

{+START,IF_NON_EMPTY,{EXPORT_POINTS_LEDGER}}
	<h2>{!EXPORT_POINTS_LEDGER}</h2>
	<p>{!DESCRIPTION_EXPORT_MEMBER_POINTS_LEDGER,{$DISPLAYED_USERNAME*,{USERNAME}}}</p>
	{EXPORT_POINTS_LEDGER}
{+END}
