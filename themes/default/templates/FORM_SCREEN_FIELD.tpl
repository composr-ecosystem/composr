{$,If editing this template, make sure that the $cms.form.setRequired JavaScript function is updated}

{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$SET,unique_field_id,{$?,{$IS_EMPTY,{NAME*}},{$RAND},{NAME*}}}

<tr id="form-table-field--{$GET*,unique_field_id}" class="field-input">
	<th id="form-table-field-name--{$GET,unique_field_id}" class="form-table-field-name{+START,IF,{REQUIRED}} required{+END}">
		<span class="form-field-name field-name">
			{$SET,show_label,{$AND,{$IS_NON_EMPTY,{NAME}},{$NOT,{SKIP_LABEL}}}}
			{+START,IF,{$GET,show_label}}
				<label for="{NAME*}">{PRETTY_NAME*}</label>

				<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_HTML*,{PRETTY_NAME*}}" />
			{+END}
			{+START,IF,{$NOT,{$GET,show_label}}}
				<span class="faux-label">{PRETTY_NAME*}</span>
			{+END}
		</span>

		{+START,IF,{$NOT,{$GET,no_required_stars}}}
			<span id="required-readable-marker--{$GET,unique_field_id}" style="display: {$?,{REQUIRED},inline,none}"><span class="required-star">*</span> <span class="accessibility-hidden">{!REQUIRED}</span></span>
		{+END}

		{+START,IF_PASSED,DESCRIPTION_SIDE}{+START,IF_NON_EMPTY,{DESCRIPTION_SIDE}}
			<p class="associated-details">{DESCRIPTION_SIDE}</p>
		{+END}{+END}
	</th>

	<td id="form-table-field-input--{$GET,unique_field_id}" class="form-table-field-input{+START,IF,{REQUIRED}} required{+END}" data-tpl="formScreenField_input" data-tpl-params="{+START,PARAMS_JSON,unique_field_id}{_*}{+END}">
		{+START,IF,{$NOT,{$_GET,overlay}}}
			{COMCODE}
		{+END}

		{$SET,input,{INPUT}}

		{+START,IF,{$AND,{$NOT,{$MATCH_KEY_MATCH,_WILD:quiz}},{$GET,early_description}}}
			{+START,INCLUDE,FORM_SCREEN_FIELD_DESCRIPTION}RIGHT=1{+END}
		{+END}

		{$GET,input}

		{+START,IF,{$NAND,{$NOT,{$MATCH_KEY_MATCH,_WILD:quiz}},{$GET,early_description}}}
			{+START,INCLUDE,FORM_SCREEN_FIELD_DESCRIPTION}{+END}
		{+END}
		{$SET,early_description,0}

		<div id="error-{$GET,unique_field_id}" style="display: none" class="input-error-here"{+START,IF_PASSED,PATTERN_ERROR} data-errorRegexp="{PATTERN_ERROR*}"{+END}>
			{+START,INCLUDE,ICON}
				NAME=status/warn
				ICON_SIZE=24
			{+END}
			<span class="js-error-message"></span>
		</div>

		{+START,IF_NON_EMPTY,{NAME}}
			{+START,IF,{REQUIRED}}
				<input type="hidden" id="required-posted--{$GET,unique_field_id}" name="require__{NAME*}" value="1" />
			{+END}
		{+END}
	</td>
</tr>
