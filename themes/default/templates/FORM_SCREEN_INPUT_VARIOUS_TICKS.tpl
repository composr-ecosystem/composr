{$SET,early_description,1}

<div data-toggleable-tray="{}" data-tpl="formScreenInputVariousTicks" data-tpl-params="{+START,PARAMS_JSON,CUSTOM_NAME,CUSTOM_ACCEPT_MULTIPLE}{_*}{+END}">
	{+START,IF_PASSED,EXPANDED}
		<h3 class="toggleable-tray-title">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{$?,{EXPANDED},{!CONTRACT},{!EXPAND}}">{+START,INCLUDE,ICON}
				NAME=trays/{$?,{EXPANDED},contract,expand}
				ICON_SIZE=20
			{+END}</a>
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{SECTION_TITLE*}</a>
		</h3>
		<div class="toggleable-tray js-tray-content" style="display: {$?,{EXPANDED},block,none}">
			<div>
	{+END}
			{+START,IF,{$NOT,{SIMPLE_STYLE}}}
				<div class="various-ticks clearfix">
				{+START,LOOP,OUT}
					<div class="input-individual-tick">
						<label for="i-{NAME*}"><input title="{DESCRIPTION*}" tabindex="{TABINDEX*}" class="input-tick"{+START,IF,{CHECKED}} checked="checked"{+END}{+START,IF,{DISABLED}} disabled="disabled"{+END} type="checkbox" id="i-{NAME*}" name="{+START,IF,{DISABLED}}disabled__{+END}{NAME*}" value="{VALUE*}" />
						<span>{PRETTY_NAME*}</span></label>
						{+START,IF,{DISABLED}}
							<input type="hidden" name="{NAME*}" value="{VALUE*}" />
						{+END}
						<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{PRETTY_NAME*}}" />
						<input name="tick_on_form__{NAME*}" value="0" type="hidden" />
					</div>
				{+END}
				</div>
			{+END}
			{+START,IF,{SIMPLE_STYLE}}
				<div class="various-ticks-simple">
					{+START,LOOP,OUT}
						<p>
							<label for="i-{NAME*}"><input title="{DESCRIPTION*}" tabindex="{TABINDEX*}" class="input-tick"{+START,IF,{CHECKED}} checked="checked"{+END}{+START,IF,{DISABLED}} disabled="disabled"{+END} type="checkbox" id="i-{NAME*}" name="{+START,IF,{DISABLED}}disabled__{+END}{NAME*}" value="{VALUE*}" />
							<span>{PRETTY_NAME*}</span></label>
							{+START,IF,{DISABLED}}
								<input type="hidden" name="{NAME*}" value="{VALUE*}" />
							{+END}
							<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{PRETTY_NAME*}}" />
							<input name="tick_on_form__{NAME*}" value="0" type="hidden" />
						</p>
					{+END}
				</div>
			{+END}

			{+START,IF_PASSED,CUSTOM_NAME}
				<div class="input-other-tick">
					{+START,IF,{CUSTOM_ACCEPT_MULTIPLE}}
						{+START,LOOP,CUSTOM_VALUE}
							<div><div>
								{+START,IF,{$EQ,{_loop_key},0}}
									<label for="{CUSTOM_NAME*}_value_{_loop_key*}">{!OTHER}</label>&hellip;<br />
								{+END}
								<input id="{CUSTOM_NAME*}_value_{_loop_key*}" name="{CUSTOM_NAME*}_value[]" value="{_loop_var*}" size="15" type="text" class="form-control js-keypress-input-ensure-next-field" />
							</div></div>
						{+END}
					{+END}

					{+START,IF,{$NOT,{CUSTOM_ACCEPT_MULTIPLE}}}
						<div><div>
							<input value="1" class="input-tick js-click-checkbox-toggle-value-field" id="{CUSTOM_NAME*}" name="{CUSTOM_NAME*}" type="checkbox" />
							<label for="{CUSTOM_NAME*}">{!OTHER}</label> <label for="{CUSTOM_NAME*}_value"><span class="associated-details">({!fields:PLEASE_STATE})</span></label>
							<input id="{CUSTOM_NAME*}_value" name="{CUSTOM_NAME*}_value" value="{+START,IF_PASSED,CUSTOM_VALUE}{CUSTOM_VALUE*}{+END}" class="form-control js-change-input-toggle-value-checkbox" size="15" type="text" />
						</div></div>
					{+END}
				</div>
			{+END}


	{+START,IF_PASSED,EXPANDED}
			</div>
		</div>
	{+END}
</div>
