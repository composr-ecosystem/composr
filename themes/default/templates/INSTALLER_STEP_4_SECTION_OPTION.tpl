<tr>
	<th class="de-th form-table-field-name">
		<span class="field-name">{NICE_NAME*}</span>
		{+START,IF,{$NOT,{$GET,no_required_stars}}}
			<span style="display: {$?,{REQUIRED},inline,none}"><span class="required-star">*</span> <span class="accessibility-hidden">{!REQUIRED}</span></span>
		{+END}
		{+START,IF_NON_EMPTY,{DESCRIPTION}}
			<div class="associated-details">{DESCRIPTION}</div>
		{+END}
	</th>

	<td class="form-table-field-input">
		<div class="accessibility-hidden"><label for="{NAME*}">{NICE_NAME*}</label></div>
		<div>
			{INPUT}
		</div>
	</td>
</tr>
