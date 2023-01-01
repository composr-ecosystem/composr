{$SET,RAND_ID_TICKER,rand{$RAND}}
{$REQUIRE_JAVASCRIPT,core_rich_media}

<div class="ticker-wrap" role="marquee" id="ticktickticker{$GET,RAND_ID_TICKER}" data-tpl="comcodeTicker" data-tpl-params="{+START,PARAMS_JSON,RAND_ID_TICKER,WIDTH,SPEED,TEXT}{_*}{+END}"></div>
