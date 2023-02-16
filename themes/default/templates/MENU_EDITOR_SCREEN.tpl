{$REQUIRE_JAVASCRIPT,core_menus}

<div data-tpl="menuEditorScreen" data-tpl-params="{+START,PARAMS_JSON,ALL_MENUS,MENU_TYPE}{_*}{+END}">
	{TITLE}

	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<div class="menu-editor-page docked js-el-menu-editor-wrap" id="menu-editor-wrap">
		<div class="menu-editor-page-inner">
			<form title="" action="{URL*}" method="post">
				<!-- In separate form due to mod_security -->
				<textarea aria-hidden="true" cols="30" rows="3" style="display: none" name="template" id="template">{CHILD_BRANCH_TEMPLATE*}</textarea>
			</form>

			<form title="{!PRIMARY_PAGE_FORM}" id="edit-form" action="{URL*}" method="post" data-submit-modsecurity-workaround="1">
				{$INSERT_FORM_POST_SECURITY}

				<div class="clearfix menu-edit-main">
					<div class="menu-editor-rh-side">
						<h2>{!HELP}</h2>

						<p>{!BRANCHES_DESCRIPTION,{$PAGE_LINK*,_SEARCH:admin_sitemap:browse}}</p>

						<p>{!ENTRY_POINTS_DESCRIPTION}</p>
					</div>

					<div class="menu-editor-lh-side">
						<h2>{!BRANCHES}</h2>

						<input type="hidden" name="highest_order" id="highest_order" value="{HIGHEST_ORDER*}" />

						<div class="menu-editor-root">
							{ROOT_BRANCH}
						</div>
					</div>

					<p class="proceed-button">
						<button accesskey="p" class="btn btn-primary btn-scr buttons--preview js-click-preview-menu" type="submit">{+START,INCLUDE,ICON}NAME=buttons/preview{+END} {!PREVIEW}</button>
						<button accesskey="u" class="btn btn-primary btn-scr buttons--save js-click-save-menu" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>
					</p>
				</div>

				<div id="mini-form-hider" style="display: none" class="clearfix">
					<div class="menu-editor-rh-side">
						<a title="{!TOGGLE_DOCKED_FIELD_EDITING}" class="dock-button js-click-toggle-docked-field-editing">{+START,INCLUDE,ICON}
							NAME=arrow_box/arrow_box_hover
							ICON_SIZE=13
						{+END}</a>

						<h2>{!CHOOSE_ENTRY_POINT}</h2>

						<div class="accessibility-hidden"><label for="tree-list">{!ENTRY}</label></div>
						<input class="form-control js-tree-input-change-update-selection" style="display: none" type="text" id="tree-list" name="tree_list" />
						<div id="tree-list--root-tree-list">
							<!-- List put in here -->
						</div>

						<p class="associated-details">
							{!CLICK_ENTRY_POINT_TO_USE}
						</p>

						<nav>
							<ul class="actions-list">
								<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="#!" class="js-click-menu-editor-add-new-page">{!SPECIFY_NEW_PAGE}</a></li>
							</ul>
						</nav>
					</div>

					<div class="menu-editor-lh-side">
						<h2>{!EDIT_SELECTED_FIELD}</h2>

						<table class="map-table form-table wide-table">
							{+START,IF,{$DESKTOP}}
								<colgroup>
									<col class="field-name-column" />
									<col class="field-input-column" />
								</colgroup>
							{+END}

							<tbody>
								{FIELDS_TEMPLATE}
							</tbody>
						</table>
					</div>
				</div>

				<input type="hidden" name="confirm" value="1" />
			</form>

			<div class="box box---menu-editor-screen" data-toggleable-tray="{}">
				<div class="box-inner">
					<h2 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=24
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!DELETE_MENU}</a>
					</h2>

					<div class="toggleable-tray js-tray-content" id="delete-menu" style="display: none">
						<p>{!ABOUT_DELETE_MENU}</p>

						<form title="{!DELETE}" action="{DELETE_URL*}" method="post">
							{$INSERT_FORM_POST_SECURITY}

							<p class="proceed-button">
								<input type="hidden" name="confirm" value="1" />
								<input type="hidden" name="delete_confirm" value="1" />

								<button class="btn btn-danger btn-scri" type="submit" data-cms-confirm-click="{!CONFIRM_DELETE*,{MENU_NAME}}">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{!DELETE}</span></button>
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
