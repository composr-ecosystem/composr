{$REQUIRE_JAVASCRIPT,redirects_editor}

<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}" data-tpl="redirectETableRedirect">
	<td>
		<div class="accessibility-hidden"><label for="from_zone_{I*}">{!REDIRECT_FROM_ZONE}</label></div>
		{+START,IF_NON_EMPTY,{FROM_ZONES}}
			<select class="form-control quite-wide-field" id="from_zone_{I*}" name="from_zone_{I*}">{FROM_ZONES}</select>
		{+END}
		{+START,IF_EMPTY,{FROM_ZONES}}
			<input data-prevent-input="[^\*\w\.\-]" maxlength="80" size="10" class="form-control quite-wide-field" id="from_zone_{I*}" name="from_zone_{I*}" value="{FROM_ZONE*}" type="text" />
		{+END}
		:
	</td>
	<td>
		<div class="accessibility-hidden"><label for="from_page_{I*}">{!REDIRECT_FROM_PAGE}</label></div>
		<input data-prevent-input="[^\*\w\.\-]" maxlength="80" class="form-control quite-wide-field" id="from_page_{I*}" name="from_page_{I*}" value="{FROM_PAGE*}" type="text" />
		&rarr;
	</td>
	<td>
		<div class="accessibility-hidden"><label for="to_zone_{I*}">{!REDIRECT_TO_ZONE}</label></div>
		{+START,IF_NON_EMPTY,{TO_ZONES}}
			<select class="form-control quite-wide-field" id="to_zone_{I*}" name="to_zone_{I*}">{TO_ZONES}</select>
		{+END}
		{+START,IF_EMPTY,{TO_ZONES}}
			<input data-prevent-input="[^\*\w\.\-]" maxlength="80" size="10" class="form-control quite-wide-field" id="to_zone_{I*}" name="to_zone_{I*}" value="{TO_ZONE*}" type="text" />
		{+END}
		:
	</td>
	<td>
		<div class="accessibility-hidden"><label for="to_page_{I*}">{!REDIRECT_TO_PAGE}</label></div>
		<input data-prevent-input="[^\*\w\.\-]" maxlength="80" class="form-control quite-wide-field" id="to_page_{I*}" name="to_page_{I*}" value="{TO_PAGE*}" type="text" />
	</td>
	<td>
		<div class="accessibility-hidden"><label for="is_transparent_{I*}">{!IS_TRANSPARENT_REDIRECT}</label></div>
		<input data-cms-tooltip="{ contents: '{!IS_TRANSPARENT_REDIRECT;^=}', width: '40%' }" title="{!IS_TRANSPARENT_REDIRECT}" class="input-tick" type="checkbox" id="is_transparent_{I*}" name="is_transparent_{I*}"{+START,IF,{IS_TRANSPARENT}} checked="checked"{+END} value="1" />
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
