{$REQUIRE_JAVASCRIPT,plupload}
<div data-view="ZoneEditorPanel" data-view-params="{+START,PARAMS_JSON,COMCODE,CLASS,ID,CURRENT_ZONE}{_*}{+END}">
	<div class="block-mobile">
		 <h2>{ID*}</h2>
	</div>

	{$,Tab buttons}
	<div class="clearfix">
		<div class="ze-tabs tabs" role="tablist">
			{+START,IF_PASSED,PREVIEW}
				<a aria-controls="view-{ID*}" role="tab" title="{!PREVIEW}: {ID*}" href="#!" id="view-tab-{ID*}" class="tab tab-first tab-selected js-click-select-tab" data-js-tab="view">{+START,INCLUDE,ICON}
					NAME=buttons/preview
					ICON_SIZE=24
				{+END}<span>{!PREVIEW}</span></a>{+END}
			{+START,IF_PASSED,COMCODE}
				<a aria-controls="edit-{ID*}" role="tab" title="{!EDIT}: {ID*}" href="#!" id="edit-tab-{ID*}" class="tab{+START,IF_NON_PASSED,PREVIEW} tab-first{+END} js-click-select-tab" data-js-tab="edit">{+START,INCLUDE,ICON}
					NAME=admin/edit
					ICON_SIZE=24
				{+END}<span>{!EDIT}</span></a>
			{+END}
			<a aria-controls="info-{ID*}" role="tab" title="{!DETAILS}: {ID*}" href="#!" id="info-tab-{ID*}" class="tab{+START,IF_NON_PASSED,SETTINGS} tab-last{+END}{+START,IF_NON_PASSED,PREVIEW}{+START,IF_NON_PASSED,COMCODE} tab-first{+END}{+END} js-click-select-tab" data-js-tab="info">{+START,INCLUDE,ICON}
				NAME=content_types/page
				ICON_SIZE=24
			{+END}<span>{!DETAILS}</span></a>
			{+START,IF_PASSED,SETTINGS}
				<a aria-controls="settings-{ID*}" role="tab" title="{!SETTINGS}: {ID*}" href="#!" id="settings-tab-{ID*}" class="tab tab-last js-click-select-tab" data-js-tab="settings">{+START,INCLUDE,ICON}
					NAME=buttons/settings
					ICON_SIZE=24
				{+END}<span>{!SETTINGS}</span></a>
			{+END}
		</div>
	</div>

	{$,Actual tab' contents follows}

	{+START,IF_PASSED,PREVIEW}
		<div id="view-{ID*}" style="display: block" aria-labelledby="view-tab-{ID*}" role="tabpanel">
			{+START,IF_EMPTY,{PREVIEW}}
				<p class="nothing-here">{!NONE}</p>
			{+END}
			{+START,IF_NON_EMPTY,{PREVIEW}}
				{$PARAGRAPH,{PREVIEW}}
			{+END}
		</div>
	{+END}

	{+START,IF_PASSED,COMCODE}
		<div id="edit-{ID*}" style="{+START,IF_NON_PASSED,PREVIEW}display: block{+END}{+START,IF_PASSED,PREVIEW}display: none{+END}" aria-labelledby="edit-tab-{ID*}" role="tabpanel">
			<form title="{ID*}: {!COMCODE}" action="index.php" method="post" class="js-form-zone-editor-comcode">
				{$INSERT_FORM_POST_SECURITY}

				<p>
					<label for="edit_{ID*}_textarea">{!COMCODE}:</label> <a data-open-as-overlay="{}" class="link-exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{$PAGE_LINK*,_SEARCH:userguide_comcode}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a>
					{+START,IF,{$IN_STR,{CLASS},wysiwyg}}
						<span class="horiz-field-sep associated-link">
							<a id="toggle-wysiwyg-edit-{ID*}-textarea" href="#!" class="js-a-toggle-wysiwyg" title="{!comcode:ENABLE_WYSIWYG}"><abbr title="{!TOGGLE_WYSIWYG_2}">{+START,INCLUDE,ICON}NAME=editor/wysiwyg_on{+END}</abbr></a>
						</span>
					{+END}
				</p>
				{+START,IF_NON_EMPTY,{COMCODE_EDITOR}}
					<div>
						<div class="post-special-options--{ID*}">
							<div class="clearfix" role="toolbar">
								{COMCODE_EDITOR}
							</div>
						</div>
					</div>
				{+END}
				<div>
					<textarea rows="50" cols="20" class="form-control {$?,{IS_PANEL},ze-textarea,ze-textarea-middle} {CLASS*} js-ta-ze-comcode textarea-scroll" id="edit_{ID*}_textarea" name="edit_{ID*}_textarea">{COMCODE*}</textarea>

					{+START,IF_PASSED,DEFAULT_PARSED}
						<textarea cols="1" rows="1" style="display: none" readonly="readonly" disabled="disabled" name="edit_{ID*}_textarea_parsed">{DEFAULT_PARSED*}</textarea>
					{+END}
				</div>
			</form>
		</div>
	{+END}

	<div id="info-{ID*}" style="{+START,IF_NON_PASSED,PREVIEW}display: block{+END}{+START,IF_PASSED,PREVIEW}display: none{+END}" aria-labelledby="info-tab-{ID*}" role="tabpanel">
		<p class="lonely-label">
			<span class="field-name">{!PAGE_TYPE}:</span>
		</p>
		<p>{TYPE*}</p>

		<p class="lonely-label">
			<span class="field-name">{!NAME}:</span>
		</p>
		<p><kbd>{ID*}</kbd></p>

		{+START,IF_NON_EMPTY,{EDIT_URL}}
			<p class="lonely-label">
				<span class="field-name">{!ACTIONS}:</span>
			</p>
			<ul class="actions-list">
				<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a title="{!EDIT_IN_FULL_PAGE_EDITOR}: {ID*} {!LINK_NEW_WINDOW}" target="_blank" href="{EDIT_URL*}">{!EDIT_IN_FULL_PAGE_EDITOR}</a></li>
			</ul>
		{+END}

		{$,Choosing where to redirect to, same page name but in a different zone}
		{+START,IF_PASSED,ZONES}
			{+START,IF,{$ADDON_INSTALLED,redirects_editor}}
				<form title="{ID*}: {!DRAWS_FROM}" action="index.php" method="post">
					{$INSERT_FORM_POST_SECURITY}

					<p class="lonely-label">
						<label for="redirect_{ID*}" class="field-name">{!DRAWS_FROM}:</label>
					</p>
					{+START,IF_NON_EMPTY,{ZONES}}
						<select class="form-control js-sel-zones-draw" id="redirect_{ID*}" name="redirect_{ID*}">
							<option value="{ZONE*}">{!NA}</option>
							{ZONES}
						</select>
					{+END}
					{+START,IF_EMPTY,{ZONES}}
						<input maxlength="80" class="form-control js-inp-zones-draw" size="20" id="redirect_{ID*}" name="redirect_{ID*}" value="{CURRENT_ZONE*}" type="text" />
					{+END}
				</form>
			{+END}
		{+END}
	</div>

	{+START,IF_PASSED,SETTINGS}
		<div id="settings-{ID*}" style="display: none" aria-labelledby="settings-tab-{ID*}" role="tabpanel">
			<form title="{ID*}: {!SETTINGS}" id="middle-fields" action="index.php">
				{$INSERT_FORM_POST_SECURITY}

				<div class="wide-table-wrap"><table class="map-table form-table wide-table">
					{+START,IF,{$DESKTOP}}
						<colgroup>
							<col class="field-name-column" />
							<col class="field-input-column" />
						</colgroup>
					{+END}

					<tbody>
						{SETTINGS}
					</tbody>
				</table></div>
			</form>
		</div>
	{+END}
</div>
