<form action="{$SELF_URL*}" method="post">
	{$INSERT_SPAMMER_BLACKHOLE}

	<p>
		<span class="field_name">Filter:</span>
		<ul class="actions_list">
			{+START,LOOP,Freelance designer\,Freelance developer\,Freelance consultant\,Small Agency (2-5 full time employees)\,Agency (over 5 full time employees)\,Web host\,Other}
				<li>
					<label for="partner_type_{_loop_var|*}"><input onchange="this.form.submit();" type="checkbox" name="filter_partner_type[]" id="partner_type_{_loop_var|*}" value="{_loop_var*}"{+START,IF,{$IN_STR,{$_POST,filter_partner_type},{_loop_var}}} checked="checked"{+END} /> {_loop_var*}</label>
				</li>
			{+END}
		</ul>
	</p>
</form>
