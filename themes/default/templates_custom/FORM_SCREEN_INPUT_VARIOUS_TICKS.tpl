{$REQUIRE_JAVASCRIPT,workflows}
{$SET,early_description,1}

<div data-tpl="formScreenInputVariousTicks" data-tpl-params="{+START,PARAMS_JSON,CUSTOM_ACCEPT_MULTIPLE,CUSTOM_NAME}{_*}{+END}" data-toggleable-tray="{}">
	{+START,IF_PASSED,EXPANDED}
		<h4 class="comcode-quote-h4 js-tray-header">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{+START,INCLUDE,ICON}
				NAME=trays/{$?,{EXPANDED},contract,expand}
				ICON_SIZE=20
			{+END}</a>

			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{SECTION_TITLE*}</a>
		</h4>

		<div class="toggleable-tray js-tray-content" style="display: {$?,{EXPANDED},block,none}"{+START,IF,{$NOT,{EXPANDED}}} aria-expanded="false"{+END}>
	{+END}

	{+START,IF,{$NOT,{SIMPLE_STYLE}}}
		<div class="various-ticks clearfix">
			{+START,LOOP,OUT}
				<div class="input-individual-tick">
					<label for="i_{NAME*}"><input title="{DESCRIPTION*}" tabindex="{TABINDEX*}" class="input-tick"{+START,IF,{CHECKED}} checked="checked"{+END} type="checkbox" id="i_{NAME*}" name="{NAME*}" value="1"{+START,IF,{DISABLED}} disabled="disabled"{+END} /> {PRETTY_NAME*}</label>
					<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{PRETTY_NAME*}}" />
					<input name="tick_on_form__{NAME*}" value="0" type="hidden" />
				</div>
			{+END}
		</div>
	{+END}
	{+START,IF,{SIMPLE_STYLE}}
		{+START,LOOP,OUT}
			<p>
				<label for="i_{NAME*}"><input title="{DESCRIPTION*}" tabindex="{TABINDEX*}" class="input-tick"{+START,IF,{CHECKED}} checked="checked"{+END} type="checkbox" id="i_{NAME*}" name="{NAME*}" value="1" /> {PRETTY_NAME*}</label>
				<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{PRETTY_NAME*}}" />
				<input name="tick_on_form__{NAME*}" value="0" type="hidden" />
			</p>
		{+END}
	{+END}

	{+START,IF_PASSED,CUSTOM_NAME}
		<div class="input-other-tick">
			{+START,IF,{CUSTOM_ACCEPT_MULTIPLE}}
				{+START,LOOP,CUSTOM_VALUE}
					<div><div>
						{+START,IF,{$EQ,{_loop_key},0}}
							<label for="{CUSTOM_NAME*}_value_{_loop_key*}">{!OTHER}</label>&hellip;<br />
						{+END}
						<input id="{CUSTOM_NAME*}_value_{_loop_key*}" class="form-control js-keypress-ensure-next-field" name="{CUSTOM_NAME*}_value[]" value="{_loop_var*}" size="15" type="text" />
					</div></div>
				{+END}
			{+END}

			{+START,IF,{$NOT,{CUSTOM_ACCEPT_MULTIPLE}}}
				<div><div>
					<input value="1" class="input-tick js-click-value-input-toggle-disabled" id="{CUSTOM_NAME*}" name="{CUSTOM_NAME*}" type="checkbox" />
					<label for="{CUSTOM_NAME*}">{!OTHER}</label> <label for="{CUSTOM_NAME*}_value"><span class="associated-details">({!fields:PLEASE_STATE})</span></label>
					<input id="{CUSTOM_NAME*}_value" class="form-control js-change-value-checkbox-toggle-checked" name="{CUSTOM_NAME*}_value" value="{+START,IF_PASSED,CUSTOM_VALUE}{CUSTOM_VALUE*}{+END}" size="15" type="text" />
				</div></div>
			{+END}
		</div>
	{+END}

	{+START,IF_PASSED,EXPANDED}
		</div>
	{+END}
</div>
