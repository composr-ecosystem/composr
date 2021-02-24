{$SET,client_id,{$CONFIG_OPTION,google_apis_client_id}}
<div id="ga-{ID*}" data-tpl="googleAnalytics" data-tpl-params="{+START,PARAMS_JSON,ID,PROPERTY_ID,ACCESS_TOKEN,DIMENSION,METRICS,DAYS,CHART_TYPE,UNDER_TAB,EXTRA,client_id}{_*}{+END}">
	<div id="auth-button-{ID*}"></div>

	{+START,INCLUDE,GOOGLE_TIME_PERIODS}{+END}

	<div id="timeline-{ID*}"></div>

	<div id="loading-{ID*}" aria-busy="true" class="spaced">
		<div class="ajax-loading vertical-alignment">
			<img src="{$IMG*,loading}" width="{$IMG_WIDTH*,{$IMG,loading}}" height="{$IMG_HEIGHT*,{$IMG,loading}}" title="{!LOADING}" alt="{!LOADING}" />
			<span>{!LOADING}</span>
		</div>
	</div>
</div>
