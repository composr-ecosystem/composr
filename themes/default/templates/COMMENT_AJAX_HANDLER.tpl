{$REQUIRE_JAVASCRIPT,core_feedback_features}
{$SET,infinite_scroll,{$AND,{$NOT,{IS_THREADED}},{$THEME_OPTION,infinite_scrolling}}}
<div class="tpl-placeholder" hidden="hidden" data-tpl="commentAjaxHandler" data-tpl-params="{+START,PARAMS_JSON,OPTIONS,HASH,infinite_scroll,SELF_URL_ENCODED}{_*}{+END}"></div>
