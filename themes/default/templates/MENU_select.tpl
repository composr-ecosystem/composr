{$REQUIRE_CSS,menu__select}

{$SET,menu_id,r-{MENU|}}

<div class="menu-type--select" data-view="SelectMenu" data-view-params="{+START,PARAMS_JSON,MENU,JAVASCRIPT_HIGHLIGHTING,menu_id}{_*}{+END}">
	<form title="{!MENU} ({!FORM_AUTO_SUBMITS})" method="get" action="#!">
		<div>
			<div class="accessibility-hidden"><label for="menu_select_{MENU|}">{!MENU}</label></div>
			<select id="menu_select_{MENU|}" name="menu_select_{MENU|}" class="form-control form-control-wide js-change-redirect-to-value">
				{CONTENT}
			</select>
		</div>
	</form>
</div>
