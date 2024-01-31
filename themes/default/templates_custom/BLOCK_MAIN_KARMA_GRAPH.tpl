{$REQUIRE_CSS,karma}

<div class="block-main-karma-graph" title="{KARMA_TITLE*}">
	{+START,IF,{LARGE_IS_BAD}}
		<div class="karma-bar really-bad-karma" style="width: {KARMA_LARGE*}%"></div>
		<div class="karma-bar bad-karma" style="width: {KARMA_SMALL*}%"></div>
	{+END}
	
	{+START,IF,{$NOT,{LARGE_IS_BAD}}}
		<div class="karma-bar good-karma" style="width: {KARMA_LARGE*}%"></div>
		<div class="karma-bar bad-karma" style="width: {KARMA_SMALL*}%"></div>
	{+END}
</div>
