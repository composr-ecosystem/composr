{$SET,credits,{$CPF_VALUE,support_credits}}
<p>{!SUPPORT_CREDITS_REMAINING,{$GET,credits}}</p>
<div class="wide_table_wrap"><table class="columned_table wide_table solidborder">
	<tr><th>{!SUPPORT_PRIORITY}</th><th>{!SUPPORT_TIME_AVAILABLE}</th><th>{!DESCRIPTION}</th></tr>
	<tr><th>{!SUPPORT_PRIORITY_regular}</th><td>{+START,IF,{$LT,{$GET,credits},5}}{!NOT_ENOUGH_CREDITS,{$PAGE_LINK*,_SEARCH:purchase}}{+END}{+START,IF,{$NOT,{$LT,{$GET,credits},4}}}{$TIME_PERIOD,{$MULT,{$GET,credits},480}}{+END}{$ 8 minutes }</td><td>{!SUPPORT_PRIORITY_DESCRIPTION_normal}</td></tr>
	<tr><th>{!SUPPORT_PRIORITY_backburner}</th><td>{+START,IF,{$LT,{$GET,credits},3}}{!NOT_ENOUGH_CREDITS,{$PAGE_LINK*,_SEARCH:purchase}}{+END}{+START,IF,{$NOT,{$LT,{$GET,credits},3}}}{$TIME_PERIOD,{$MULT,{$GET,credits},600}}{+END}{$ 10 minutes }</td><td>{!SUPPORT_PRIORITY_DESCRIPTION_budget}</td></tr>
</table></div>
<p>{!SUPPORT_CREDITS_EXPLANATION,{$CURRENCY,{$CONFIG_OPTION,support_credit_value},{$CONFIG_OPTION,currency},,1},{$PAGE_LINK*,_SEARCH:purchase},{$PAGE_LINK*,_SEARCH:tickets:ticket:default={$GET,request_payment_update}}}</p>
