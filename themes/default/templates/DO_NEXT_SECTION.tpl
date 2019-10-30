{+START,IF,{$EQ,{TITLE},{!GLOBAL_NAVIGATION}}}
	<hr class="spaced-rule" />
{+END}

<nav class="do-next-section-wrap">
	{+START,IF_NON_EMPTY,{TITLE}}{+START,IF,{$NEQ,{TITLE},{$PAGE_TITLE},{!ENTRIES}}}
		<h2>{TITLE}</h2>
	{+END}{+END}

	<div class="do-next-section">
		{CONTENT}
	</div>
</nav>
