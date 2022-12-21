{$REQUIRE_JAVASCRIPT,core_rich_media}

{$SET,RAND_ID_RANDOM,rand{$RAND}}
<div id="comcoderandom{$GET,RAND_ID_RANDOM}" data-tpl="comcodeRandom" data-tpl-params="{+START,PARAMS_JSON,PARTS,MAX,RAND_ID_RANDOM}{_*}{+END}"></div>
