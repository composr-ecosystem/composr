<li>
	<p>
		<a href="{URL*}">{LINK_CAPTION}</a>
	</p>

	<p>
		{DESCRIPTION}
	</p>

	{+START,IF_NON_EMPTY,{USAGE}}
		<p>
			<strong>{!BLOCK_USED_BY}</strong>:
			<span class="associated-details">{+START,LOOP,USAGE}{+START,IF,{$NEQ,{_loop_key},0}}, {+END}<kbd>{_loop_var*}</kbd>{+END}</span>
		</p>
	{+END}

	{+START,IF_PASSED,SEED_LINKS}
		<ul class="horizontal-links">
			{+START,LOOP,SEED_LINKS}
				<li><a href="{_loop_key*}">{_loop_var*}</a></li>
			{+END}
		</ul>
	{+END}
</li>
