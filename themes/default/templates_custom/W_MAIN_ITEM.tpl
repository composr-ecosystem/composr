<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}">
	<td>
		{+START,IF_NON_EMPTY,{PICTURE_URL}}<a title="{NAME*} {!LINK_NEW_WINDOW}" target="_blank" data-open-as-overlay="{}" href="{PICTURE_URL*}">{+END}{NAME*}{+START,IF_NON_EMPTY,{PICTURE_URL}}</a>{+END}{AUX*} <span class="associated-details">({COUNT*})</span>
	</td>
	{+START,IF_PASSED,PRICE}
		<td>
			{!W_PRICE_POINTS,{PRICE*}}
		</td>
	{+END}
	<td>
		<form data-cms-tooltip="{ contents: '{DESCRIPTION;^*}', triggers: 'hover focus', delay: 0 }" class="inline" action="{+START,IF_PASSED,PRICE}{$PAGE_LINK*,_SELF:_SELF:buy:item={NAME}:member={MEMBER}}{+END}{+START,IF_NON_PASSED,PRICE}{$PAGE_LINK*,_SELF:_SELF:take:item={NAME}:member={MEMBER}}{+END}" method="post"><button class="button-hyperlink" type="submit">{ACTION*}</button></form>

		{+START,IF,{EDIT_ACCESS}}<a class="associated-link suggested-link" title="{!EDIT}: {NAME*}" href="{$PAGE_LINK*,_SELF:_SELF:edititemcopy:item={NAME}:owner={MEMBER}}">{!EDIT}</a>{+END}

		{+START,IF_PASSED,SELLER}
			{!W_FROM,{SELLER*}}
		{+END}
	</td>
</tr>
