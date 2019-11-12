<h2>
	{TITLE}
</h2>

{CONTENT}

{+START,IF,{$EQ,{TITLE},{!MEMBERS}}}
	<ul class="actions-list spaced-list">
		<li>
			{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} {!MEMBER_DIRECTORY_NON_CONFIRMED,{$PAGE_LINK*,_SEARCH:members:include_non_confirmed=exclusively}}
		</li>
	</ul>
{+END}

{+START,IF_EMPTY,{CONTENT}}
	<p class="nothing-here">
		{!NO_ENTRIES}
	</p>
{+END}
