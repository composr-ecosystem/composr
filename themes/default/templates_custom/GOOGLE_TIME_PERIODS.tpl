<p data-tpl="googleTimePeriods">
	<label for="time-period-{ID*}">Period to show:</label>
	<select id="time-period-{ID*}" name="time-period-{ID*}" class="js-select-onchange-trigger-tab-ga-reinit" data-tp-tab-id="ga-{ID*}">
		{+START,IF,{$NEQ,{DAYS},7,14,31,62,92,183,365,730,1825}}
			<option selected="selected" value="{DAYS*}">Past {$NUMBER_FORMAT*,{DAYS}} {$?,{$EQ,
				{DAYS},1},day,days} (default)</option>
		{+END}

		<option {+START,IF,{$EQ,{DAYS},7}} selected="selected"{+END} value="7">Past week</option>
		<option {+START,IF,{$EQ,{DAYS},14}} selected="selected"{+END} value="14">Past 2 weeks</option>
		<option {+START,IF,{$EQ,{DAYS},31}} selected="selected"{+END} value="31">Past month</option>
		<option {+START,IF,{$EQ,{DAYS},62}} selected="selected"{+END} value="62">Past 2 months</option>
		<option {+START,IF,{$EQ,{DAYS},92}} selected="selected"{+END} value="92">Past 3 months</option>
		<option {+START,IF,{$EQ,{DAYS},183}} selected="selected"{+END} value="183">Past 6 months</option>
		<option {+START,IF,{$EQ,{DAYS},365}} selected="selected"{+END} value="365">Past year</option>
		<option {+START,IF,{$EQ,{DAYS},730}} selected="selected"{+END} value="730">Past 2 years</option>
		<option {+START,IF,{$EQ,{DAYS},1825}} selected="selected"{+END} value="1825">Past 5 years</option>
	</select>
</p>
