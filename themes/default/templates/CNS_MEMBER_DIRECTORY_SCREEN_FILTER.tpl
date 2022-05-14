<div class="search_option">
	<label for="filter_{BLOCK_ID*}_{NAME|*}">{LABEL*}</label>

	{+START,IF_NON_EMPTY,{$CPF_LIST,{NAME}}}
		<select class="wide_field search_option_value" id="filter_{BLOCK_ID*}_{NAME|*}" name="filter_{BLOCK_ID*}_{NAME|*}">
			<option value="">---</option>
			{+START,LOOP,{$CPF_LIST,{NAME}}}
				<option{+START,IF,{$AND,{$NEQ,{_loop_key},},{$EQ,{$_GET,filter_{BLOCK_ID}_{NAME|}},{_loop_key}}}} selected="selected"{+END} value="{_loop_key*}">{_loop_var*}</option>
			{+END}
		</select>
	{+END}
	{+START,IF_EMPTY,{$CPF_LIST,{NAME}}}
		{+START,IF,{$EQ,{LABEL},{!USERNAME}}}<span class="invisible_ref_point"></span>{+END}
		<input{+START,IF,{$MOBILE}} autocorrect="off"{+END} autocomplete="off" class="wide_field search_option_value"{+START,IF,{$EQ,{LABEL},{!USERNAME}}} onkeyup="update_ajax_member_list(this,null,false,event);"{+END} type="text" value="{$_GET*,filter_{BLOCK_ID}_{NAME|}}" id="filter_{BLOCK_ID*}_{NAME|*}" name="filter_{BLOCK_ID*}_{NAME|*}" />
	{+END}
</div>
