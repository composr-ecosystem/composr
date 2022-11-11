{$REQUIRE_JAVASCRIPT,checking}
{$REQUIRE_JAVASCRIPT,core_adminzone_dashboard}

{$SET,RAND_STAFF_LINKS,{$RAND}}

<div class="form-ajax-target" data-view="BlockMainStaffLinks" data-view-params="{+START,PARAMS_JSON,RAND_STAFF_LINKS,BLOCK_NAME,MAP}{_*}{+END}">
	<section id="tray-{!EXTERNAL_LINKS|}" data-toggleable-tray="{ save: true }" class="box box---block-main-staff-links">
		<div class="box-inner">
			<h3 class="toggleable-tray-title">
				<a title="{!EDIT}: {!EXTERNAL_LINKS}" href="#!" class="top-left-toggle js-click-staff-block-flip">{+START,INCLUDE,ICON}NAME=checklist/toggle{+END} <span>{!EDIT}</span></a>

				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
					NAME=trays/contract
					ICON_SIZE=24
				{+END}</a>

				<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!EXTERNAL_LINKS}</a>
			</h3>

			<div class="toggleable-tray js-tray-content">
				<ol id="staff-links-list-{$GET%,RAND_STAFF_LINKS}" class="spaced-list">
					{+START,LOOP,FORMATTED_LINKS}
						<li><a target="_blank" title="{TITLE*} {!LINK_NEW_WINDOW}" href="{URL*}">{TITLE*}</a></li>
					{+END}
				</ol>

				<form id="staff-links-list-{$GET%,RAND_STAFF_LINKS}-form" title="{!EDIT}: {!LINKS}" action="{URL*}" method="post" style="display: none" aria-hidden="true">
					{$INSERT_FORM_POST_SECURITY}

					<div><label for="staff_links_edit" class="accessibility-hidden">{!EDIT}</label><textarea cols="100" rows="30" id="staff_links_edit" name="staff_links_edit" class="form-control form-control-wide">{+START,LOOP,UNFORMATTED_LINKS}{LINKS*}&#10;&#10;{+END}</textarea></div>

					<div class="buttons-group">
						<div class="buttons-group-inner">
							<button data-disable-on-click="1" class="btn btn-primary btn-scri buttons--save {+START,IF,{$HAS_PRIVILEGE,comcode_dangerous}}js-click-form-submit-headless{+END}" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>
