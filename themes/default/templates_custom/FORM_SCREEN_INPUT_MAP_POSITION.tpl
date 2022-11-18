{$REQUIRE_JAVASCRIPT,data_mappr}
{$SET,google_apis_api_key,{$CONFIG_OPTION,google_apis_api_key}}
<div data-tpl="formScreenInputMapPosition" data-tpl-params="{+START,PARAMS_JSON,LATITUDE,LONGITUDE,NAME,google_apis_api_key}{_*}{+END}">
	<div id="map-position-{NAME*}" style="width:100%; height:300px"></div>

	<label for="{NAME*}_latitude">
		{!LATITUDE}
		<input class="form-control js-change-set-place-marker {+START,IF,{REQUIRED}}hidden-required{+END}" type="number" step="any" id="{NAME*}_latitude" name="latitude" value="{LATITUDE*}" />
	</label>

	<label for="{NAME*}_longitude">
		{!LONGITUDE}
		<input class="form-control js-change-set-place-marker {+START,IF,{REQUIRED}}hidden-required{+END}" type="number" step="any" id="{NAME*}_longitude" name="longitude" value="{LONGITUDE*}" />
	</label>

	<button class="btn btn-primary btn-sm buttons--search js-click-geolocate-user-for-map-field" data-click-pd="1" type="button">{+START,INCLUDE,ICON}NAME=buttons/search{+END} <span>{!FIND_ME}</span></button>
</div>
