{$REQUIRE_JAVASCRIPT,redirects_editor}

<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}" data-tpl="redirectETableRedirect">
	<td>
		<div class="accessibility-hidden"><label for="from_{I*}">{!FROM}</label></div>
		<input maxlength="200" class="form-control form-control-wide" id="from_{I*}" name="from_{I*}" value="{FROM*}" type="text" placeholder="https://" />
	</td>
	<td>
		<div class="accessibility-hidden"><label for="to_{I*}">{!TO}</label></div>
		<input maxlength="200" class="form-control form-control-wide" id="to_{I*}" name="to_{I*}" value="{TO*}" type="text" placeholder="https://" />
	</td>
	<td>
		<div class="accessibility-hidden"><label for="note_{I*}">{!NOTES}</label></div>
		<input maxlength="200" class="form-control form-control-wide" id="note_{I*}" name="note_{I*}" value="{NOTE*}" type="text" />
	</td>
	<td>
		<div class="accessibility-hidden"><label for="type_{I*}">{!TYPE}</label></div>
		<select class="form-control form-control-wide" id="type_{I*}" name="type_{I*}">
			<option value="full"{+START,IF,{$EQ,{TYPE},full}} selected="selected"{+END}>{!URL_REDIRECT_full}</option>
			<option value="prefix"{+START,IF,{$EQ,{TYPE},prefix}} selected="selected"{+END}>{!URL_REDIRECT_prefix}</option>
			<option value="prefix_with_append"{+START,IF,{$EQ,{TYPE},prefix_with_append}} selected="selected"{+END}>{!URL_REDIRECT_prefix_with_append}</option>
		</select>
	</td>
	<td>
		{+START,IF,{$NEQ,{I},new}}
			<a href="#!" class="js-click-confirm-container-deletion" title="{!DELETE}">{+START,INCLUDE,ICON}
				NAME=admin/delete
				ICON_SIZE=14
			{+END}</a>
		{+END}
	</td>
</tr>
