{$REQUIRE_JAVASCRIPT,core_form_interfaces}
<tr>
	{$SET,unique_field_id,{$?,{$IS_EMPTY,{NAME*}},{$RAND},{NAME*}}}
	<th id="form-table-field-name--{$GET,unique_field_id}" colspan="2" class="form-table-description-above-cell{+START,IF,{REQUIRED}} required{+END}">
		<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{PRETTY_NAME*}}" />

		<p class="field-name lonely-label">
			<label for="{NAME*}">{PRETTY_NAME*}<span class="inline-desktop">:</span></label>
		</p>

		{+START,IF,{REQUIRED}}
			<span id="required-readable-marker--{$?,{$IS_EMPTY,{NAME*}},{$RAND},{NAME*}}" class="inline"><span class="required-star">*</span> <span class="accessibility-hidden">{!REQUIRED}</span></span>
		{+END}

		{+START,INCLUDE,FORM_SCREEN_FIELD_DESCRIPTION}{+END}
	</th>
</tr>

<tr class="field-input" data-tpl="formScreenInputHugeListInput" data-tpl-params="{+START,PARAMS_JSON,INLINE_LIST,unique_field_id}{_*}{+END}">
	<td id="form-table-field-input--{$GET,unique_field_id}" colspan="2" class="form-table-huge-field{+START,IF,{REQUIRED}} required{+END}">
		{+START,IF,{INLINE_LIST}}
		<select {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} size="{+START,IF_PASSED,SIZE}{SIZE*}{+END}{+START,IF_NON_PASSED,SIZE}15{+END}" tabindex="{TABINDEX*}" class="input-huge-list{REQUIRED*} form-control " id="{NAME*}" name="{NAME*}" data-submit-on-enter="1">
		{+END}
		{+START,IF,{$NOT,{INLINE_LIST}}}
		<select {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} tabindex="{TABINDEX*}" class="input-huge-list" id="{NAME*}" name="{NAME*}">
		{+END}
		{CONTENT}
		</select>
	</td>
</tr>
