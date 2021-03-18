{$REQUIRE_JAVASCRIPT,cns_forum}

<div class="clearfix" data-tpl="cnsVirtualForumFiltering">
	<form title="{!FILTER}" target="_self" class="right" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}" method="get">
		{$HIDDENS_FOR_GET_FORM,{$SELF_URL},seconds_back}

		<p>
			<label for="seconds_back" class="accessibility-hidden">{!FILTER}</label>
			<select name="seconds_back" id="seconds_back" class="form-control js-select-change-form-submit">
				<option value="">{!POSTS_SINCE_LAST_VISIT}</option>
				{+START,LOOP,5\,10\,30}
					<option {+START,IF,{$EQ,{$_GET,seconds_back},{$MULT*,60,{_loop_var}}}} selected="selected"{+END} value="{$MULT*,60,{_loop_var}}">{!POSTS_SINCE_MINUTES,{$INTEGER_FORMAT*,{_loop_var},0}}</option>
				{+END}
				{+START,LOOP,1\,2\,6\,12}
					<option {+START,IF,{$EQ,{$_GET,seconds_back},{$MULT*,3600,{_loop_var}}}} selected="selected"{+END} value="{$MULT*,3600,{_loop_var}}">{!POSTS_SINCE_HOURS,{$INTEGER_FORMAT*,{_loop_var},0}}</option>
				{+END}
				{+START,LOOP,1\,2\,3\,4\,5\,6}
					<option {+START,IF,{$EQ,{$_GET,seconds_back},{$MULT*,86400,{_loop_var}}}} selected="selected"{+END} value="{$MULT*,86400,{_loop_var}}">{!POSTS_SINCE_DAYS,{$INTEGER_FORMAT*,{_loop_var},0}}</option>
				{+END}
				{+START,LOOP,1\,2}
					<option {+START,IF,{$EQ,{$_GET,seconds_back},{$MULT*,604800,{_loop_var}}}} selected="selected"{+END} value="{$MULT*,604800,{_loop_var}}">{!POSTS_SINCE_WEEKS,{$INTEGER_FORMAT*,{_loop_var},0}}</option>
				{+END}
			</select>
		</p>
	</form>
</div>
