{$REQUIRE_JAVASCRIPT,checking}
{$REQUIRE_JAVASCRIPT,core_adminzone_dashboard}

<div class="form-ajax-target" data-view="BlockMainNotes" data-view-params="{+START,PARAMS_JSON,BLOCK_NAME,MAP}{_*}{+END}">
	<section id="tray-{TITLE|}" data-toggleable-tray="{ save: true }" class="box box---block-main-notes">
		<div class="box-inner">
			<h3 class="toggleable-tray-title">
				<a class="toggleable-tray-button" data-click-tray-toggle="#tray-{TITLE|}" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
					NAME=trays/contract
					ICON_SIZE=24
				{+END}</a>

				{+START,IF_NON_EMPTY,{TITLE}}
					<a class="toggleable-tray-button" data-click-tray-toggle="#tray-{TITLE|}" href="#!">{TITLE*}</a>
				{+END}
			</h3>

			<div class="toggleable-tray js-tray-content">
				<form title="{$STRIP_TAGS,{TITLE}}" method="post" action="{URL*}" class="js-form-block-main-notes">
					{$INSERT_FORM_POST_SECURITY}

					<div class="accessibility-hidden"><label for="n-block-{TITLE|}">{!NOTES}</label></div>
					<div>
						<textarea class="form-control form-control-wide js-focus-textarea-expand js-blur-textarea-contract{+START,IF,{SCROLLS}} textarea-scroll{+END}" cols="80" id="n-block-{TITLE|}" rows="10" name="new">{CONTENTS*}</textarea>
					</div>

					<div class="buttons-group">
						<div class="buttons-group-inner">
							<button data-disable-on-click="1" class="btn btn-primary btn-scri buttons--save js-hover-disable-textarea-size-change{+START,IF,{$HAS_PRIVILEGE,comcode_dangerous}} js-click-headless-submit{+END}" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>
