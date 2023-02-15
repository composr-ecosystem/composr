<p class="h1 contrast-box">{HEADLINE}</p>

<p class="h3 contrast-box">{SUBLINE}</p>

<div class="h5 contrast-box">
	<div>
		{TEXT}
	</div>

	<div>
		{+START,IF_PASSED,LINK1_URL}<a rel="noopener" href="{LINK1_URL*}" class="btn btn-lg btn-outline-light">{LINK1_TEXT}</a>{+END}
		{+START,IF_PASSED,LINK2_URL}<a rel="noopener" href="{LINK2_URL*}" class="btn btn-lg btn-light">{LINK2_TEXT}</a>{+END}
	</div>
</div>
