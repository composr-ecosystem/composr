<tr>
	<th class="translate_line_first">
		<a id="jmp_{NAME*}"></a>

		<kbd>{NAME*}</kbd>

		{+START,IF_NON_EMPTY,{DESCRIPTION}}
			<img class="vertical_alignment leave_native_tooltip help_icon{+START,IF_PASSED_AND_TRUE,LEFT} left{+END}{+START,IF_PASSED_AND_TRUE,RIGHT} right{+END}" data-title="{DESCRIPTION=}" alt="{$STRIP_HTML*,{DESCRIPTION}}" onmouseover="return preactivate_rich_semantic_tooltip(this,event,true);" src="{$IMG*,icons/24x24/buttons/help}" srcset="{$IMG*,icons/48x48/buttons/help} 2x" />
		{+END}
	</th>
	<td class="translate_line_second" onmouseover="document.getElementById('trans_{NAME;*}').disabled=false;">
		<div class="accessibility_hidden"><label for="old__trans_{NAME*}">{!OLD} {NAME*}</label></div>
		<div class="constrain_field">
			<textarea disabled="disabled" readonly="readonly" class="translate_original_text wide_field" cols="60" rows="{$ADD,{$DIV,{$LENGTH,{OLD}},80},1}" id="old__trans_{NAME*}" name="old__{NAME*}">{OLD*}</textarea>
		</div>

		<div class="arrow_ruler"><img alt="" src="{$IMG*,arrow_ruler_small}" /></div>

		<div class="accessibility_hidden"><label for="trans_{NAME*}">{NAME*}</label></div>
		<div class="constrain_field">
			<textarea{+START,IF,{$AND,{$JS_ON},{$EQ,{OLD},{CURRENT}}}} disabled="disabled"{+END}{+START,IF_PASSED,TRANSLATE_AUTO} onclick="if (this.value=='') this.value='{TRANSLATE_AUTO;^*}';"{+END} class="wide_field translate_field" cols="60" rows="{+START,IF,{$EQ,{CURRENT},}}{$ADD,{$DIV,{$LENGTH,{OLD}},80},1}{+END}{+START,IF,{$NEQ,{CURRENT},}}{$ADD,{$DIV,{$LENGTH,{CURRENT}},80},1}{+END}" id="trans_{NAME*}" name="{NAME*}">{CURRENT*}</textarea>
		</div>
	</td>
	{+START,IF_NON_EMPTY,{ACTIONS}}
		<td>
			{ACTIONS}
		</td>
	{+END}
</tr>
<tr id="rexp_{NAME*}" style="display: none">
	<td colspan="{$?,{$IS_EMPTY,{ACTIONS}},3,4}">
		<div id="exp_{NAME*}"></div>
	</td>
</tr>

