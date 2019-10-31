{$REQUIRE_JAVASCRIPT,core_adminzone_dashboard}
{$SET,RAND_WEBSITE_MONITORING,{$RAND}}

<div class="form-ajax-target" data-view="BlockMainStaffWebsiteMonitoring" data-view-params="{+START,PARAMS_JSON,RAND_WEBSITE_MONITORING}{_*}{+END}">
	<section id="tray-{!SITE_WATCHLIST|}" data-toggleable-tray="{ save: true }" class="box box---block-main-staff-website-monitoring">
		<div class="box-inner">
			<h3 class="toggleable-tray-title js-tray-header">
				<a title="{!EDIT}: {!SITE_WATCHLIST}" class="top-left-toggle js-click-staff-block-flip" href="#!">{+START,INCLUDE,ICON}NAME=checklist/toggle{+END} {!EDIT}</a>

				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
					NAME=trays/contract
					ICON_SIZE=24
				{+END}</a>

				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!SITE_WATCHLIST}</a>
			</h3>

			<div class="toggleable-tray js-tray-content">
				<div class="wide-table-wrap" id="website-monitoring-list-{$GET%,RAND_WEBSITE_MONITORING}"><table class="columned-table results-table wide-table autosized-table responsive-table">
					<thead>
					<tr>
						<th>{!config:SITE_NAME}</th>
						<th>Alexa Rank</th>
						<th>Alexa Links</th>
						<th>archive.org</th>
						<th>{!LINKS}</th>
					</tr>
					</thead>
					<tbody>
					{+START,LOOP,GRID_DATA}
						<tr>
							<td>{SITE_NAME*}</td>
							<td>{ALEXA_RANKING`}</td>
							<td><a href="http://www.google.com/search?as_lq={URL&*}">{ALEXA_LINKS`}</a></td>
							<td><a class="suggested-link" href="http://web.archive.org/web/*/{URL*}">{!VIEW}</a></td>
							<td><a class="suggested-link" href="{URL*}">{!VIEW}</a></td>
						</tr>
					{+END}
					</tbody>
				</table></div>

				<form title="{!SITE_WATCHLIST}: {!EDIT}" style="display: none" aria-hidden="true" action="{URL*}" method="post" id="website-monitoring-list-{$GET%,RAND_WEBSITE_MONITORING}-form" class="js-form-site-watchlist">
					{$INSERT_SPAMMER_BLACKHOLE}

					<div>
						<label for="website_monitoring_list_edit" class="accessibility-hidden">{!EDIT}</label>
						<textarea class="form-control form-control-wide" id="website_monitoring_list_edit" name="website_monitoring_list_edit" rows="10" cols="90">{+START,LOOP,SITES_BEING_WATCHED}{_loop_key*}={_loop_var*}&#10;&#10;{+END}</textarea>
					</div>

					<div class="buttons-group">
						<div class="buttons-group-inner">
							<button data-disable-on-click="1" class="btn btn-primary btn-scri buttons--save {+START,IF,{$HAS_PRIVILEGE,comcode_dangerous}}js-click-headless-submit{+END}" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
						</div>
					</div>
				</form>

				{$REQUIRE_JAVASCRIPT,checking}
			</div>
		</div>
	</section>
</div>
