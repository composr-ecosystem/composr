{$REQUIRE_JAVASCRIPT,jquery}
{$REQUIRE_JAVASCRIPT,jquery_ui}
{$REQUIRE_JAVASCRIPT,jquery_flip}
{$REQUIRE_JAVASCRIPT,comcode_flip_tag}
{$REQUIRE_CSS,flip}

<div class="flipbox" id="flipbox-{$GET%,RAND_FLIP}" data-tpl="comcodeFlip" data-tpl-params="{+START,PARAMS_JSON,SPEED}{_*}{+END}">
	<div class="front">
		{CONTENT}
	</div>
	<div class="back"{+START,IF_NON_EMPTY,{FINAL_COLOR}} style="background-color: #{FINAL_COLOR}"{+END}>
		{$COMCODE,{PARAM},0}
	</div>
</div>
