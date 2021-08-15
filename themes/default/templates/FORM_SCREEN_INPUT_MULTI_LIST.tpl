<select multiple="multiple" size="{SIZE*}" tabindex="{TABINDEX*}" class="input_list wide_field" id="{NAME*}" name="{NAME*}[]">
	{CONTENT}
</select>

{+START,IF_PASSED,CUSTOM_NAME}
	<div class="various_ticks float_surrounder">
		<div class="input_other_tick">
			{+START,IF,{CUSTOM_ACCEPT_MULTIPLE}}
				{+START,LOOP,CUSTOM_VALUE}
					<div><div>
						{+START,IF,{$EQ,{_loop_key},0}}
							<label for="{CUSTOM_NAME*}_value_{_loop_key*}">{!OTHER}</label>&hellip;<br />
						{+END}
						<input id="{CUSTOM_NAME*}_value_{_loop_key*}" name="{CUSTOM_NAME*}_value[]" value="{_loop_var*}" size="15" type="text" onkeypress="_ensure_next_field(event,this);" />
					</div></div>
				{+END}
			{+END}

			{+START,IF,{$NOT,{CUSTOM_ACCEPT_MULTIPLE}}}
				<div><div>
					<label for="{CUSTOM_NAME*}_value">{!OTHER}</label>
					<input id="{CUSTOM_NAME*}_value" name="{CUSTOM_NAME*}_value" value="{+START,IF_PASSED,CUSTOM_VALUE}{CUSTOM_VALUE*}{+END}" size="15" type="text" />
				</div></div>
			{+END}
		</div>
	</div>
{+END}

{+START,IF,{$EQ,{SIZE},5}}
	<script>// <![CDATA[
		add_event_listener_abstract(window,'load',function() {
			if (typeof $("#{NAME#/}").select2!='undefined')
			{
				$("#{NAME#/}").select2({
					dropdownAutoWidth: window.parent==window, /*Otherwise can overflow*/
					containerCssClass: 'wide_field'
				});
			}
		});
	//]]></script>
{+END}
