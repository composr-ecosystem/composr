{$SET,early_description,1}

<div>
	{+START,IF_PASSED,EXPANDED}
		<h4 class="comcode_quote_h4">
			<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">
				<img alt="{!EXPAND}/{!CONTRACT}" src="{$IMG*,1x/trays/{$?,{EXPANDED},contract,expand}}" srcset="{$IMG*,2x/trays/{$?,{EXPANDED},contract,expand}} 2x" />
			</a>

			<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">{SECTION_TITLE*}</a>
		</h4>

		<div class="toggleable_tray" style="display: {$JS_ON,{$?,{EXPANDED},block,none},block}"{+START,IF,{$NOT,{EXPANDED}}} aria-expanded="false"{+END}>
	{+END}


			{+START,IF,{$NOT,{SIMPLE_STYLE}}}
				<div class="various_ticks float_surrounder">
					{+START,LOOP,OUT}
						<div class="input_individual_tick">
							<label for="i_{NAME*}"><input title="{DESCRIPTION*}" tabindex="{TABINDEX*}" class="input_tick"{+START,IF,{CHECKED}} checked="checked"{+END} type="checkbox" id="i_{NAME*}" name="{NAME*}" value="1"{+START,IF,{DISABLED}} disabled="disabled"{+END} /> {PRETTY_NAME*}</label>
							<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{PRETTY_NAME*}}" />
							<input name="tick_on_form__{NAME*}" value="0" type="hidden" />
						</div>
					{+END}
				</div>
			{+END}
			{+START,IF,{SIMPLE_STYLE}}
				<div class="various_ticks_simple">
					{+START,LOOP,OUT}
						<p>
							<label for="i_{NAME*}"><input title="{DESCRIPTION*}" tabindex="{TABINDEX*}" class="input_tick"{+START,IF,{CHECKED}} checked="checked"{+END} type="checkbox" id="i_{NAME*}" name="{NAME*}" value="1" /> {PRETTY_NAME*}</label>
							<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{PRETTY_NAME*}}" />
							<input name="tick_on_form__{NAME*}" value="0" type="hidden" />
						</p>
					{+END}
				</div>
			{+END}

			{+START,IF_PASSED,CUSTOM_NAME}
				<div class="input_other_tick">
					{+START,IF,{CUSTOM_ACCEPT_MULTIPLE}}
						{+START,LOOP,CUSTOM_VALUE}
							<div><div>
								{+START,IF,{$EQ,{_loop_key},0}}
									<label for="{CUSTOM_NAME*}_value_{_loop_key*}">{!OTHER}</label>&hellip;<br />
								{+END}
								<input id="{CUSTOM_NAME*}_value_{_loop_key*}" name="{CUSTOM_NAME*}_value[]" value="{_loop_var*}" size="15" type="text" value="" onkeypress="_ensure_next_field(event,this);" />
							</div></div>
						{+END}
					{+END}

					{+START,IF,{$NOT,{CUSTOM_ACCEPT_MULTIPLE}}}
						<div><div>
							<input value="1" class="input_tick" onclick="document.getElementById('{CUSTOM_NAME%}_value').disabled=!this.checked;" id="{CUSTOM_NAME*}" name="{CUSTOM_NAME*}" type="checkbox" />
							<label for="{CUSTOM_NAME*}">{!OTHER}</label> <label for="{CUSTOM_NAME*}_value"><span class="associated_details">({!PLEASE_STATE})</span></label>
							<input id="{CUSTOM_NAME*}_value" name="{CUSTOM_NAME*}_value" value="{+START,IF_PASSED,CUSTOM_VALUE}{CUSTOM_VALUE*}{+END}" onchange="document.getElementById('{CUSTOM_NAME%}').checked=(this.value!=''); this.disabled=(this.value=='');" size="15" type="text" />

							<script>// <![CDATA[
								document.getElementById('{CUSTOM_NAME%}_value').onchange();
							//]]></script>
						</div></div>
					{+END}
				</div>
			{+END}


	{+START,IF_PASSED,EXPANDED}
		</div>
	{+END}
</div>

