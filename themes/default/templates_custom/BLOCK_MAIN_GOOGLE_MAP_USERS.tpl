{$REQUIRE_JAVASCRIPT,user_mappr}

{+START,IF_NON_EMPTY,{TITLE}}
<section class="box box---block-main-google-map-users"><div class="box-inner">
	<h3>{TITLE*}</h3>
{+END}

	{$SET,username,{$USERNAME,{$MEMBER},1}}

	<div id="map-canvas" data-tpl="blockMainGoogleMapUsers" data-tpl-params="{+START,PARAMS_JSON,username,DATA,USERNAME_PREFIX,G_DEFAULT_ICON,LATITUDE,LONGITUDE,ZOOM,CLUSTER,CENTER,GEOLOCATE_USER,SET_COORD_URL,REGION}{_*}{+END}" style="width: {WIDTH}; height: {HEIGHT}"></div>

{+START,IF_NON_EMPTY,{TITLE}}
</div></section>
{+END}
