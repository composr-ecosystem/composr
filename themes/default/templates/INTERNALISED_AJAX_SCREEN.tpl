{$REQUIRE_JAVASCRIPT,checking}
{$SET,ajax_internalised_ajax_wrapper,ajax-internalised-ajax-wrapper-{$RAND%}}

<div id="{$GET*,ajax_internalised_ajax_wrapper}" data-tpl="internalisedAjaxScreen" data-tpl-params="{+START,PARAMS_JSON,CHANGE_DETECTION_URL,REFRESH_TIME,REFRESH_IF_CHANGED,URL}{_*}{+END}"
	data-ajaxify="{ callUrl: '{URL;*}', callParamsFromTarget: ['.*'], targetsSelector: 'a[target=_self], form[target=_self]', updateURL: true }">
	{SCREEN_CONTENT}
</div>
