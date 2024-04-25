<div class="contrast-box">
	<p class="h1">{HEADLINE}</p>

	<p class="h3">{SUBLINE}</p>
	
	<p class="h5">
		{TEXT}
	</p>
	
	<div>
		{+START,IF_PASSED,LINK1_URL}<a rel="noopener" href="{LINK1_URL*}" class="btn btn-lg btn-outline-light">{LINK1_TEXT}</a>{+END}
		{+START,IF_PASSED,LINK2_URL}<a rel="noopener" href="{LINK2_URL*}" class="btn btn-lg btn-light">{LINK2_TEXT}</a>{+END}
	</div>
</div>
