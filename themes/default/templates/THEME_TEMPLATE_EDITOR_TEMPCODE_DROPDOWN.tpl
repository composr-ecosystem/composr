{$REQUIRE_JAVASCRIPT,core_themeing}

<div class="clearfix {$CYCLE,tep,tpl-dropdown-row-a,tpl-dropdown-row-b}" data-tpl="themeTemplateEditorTempcodeDropdown" data-tpl-params="{+START,PARAMS_JSON,FILE_ID,STUB}{_*}{+END}">
	<div class="left">
		<div class="accessibility-hidden"><label for="b_{FILE_ID*}_{STUB*}">{STUB*}</label></div>
		<select name="b_{FILE_ID*}_{STUB*}" id="b_{FILE_ID*}_{STUB*}" class="form-control">
			<option>---</option>
			{PARAMETERS}
		</select>
	</div>
	<div class="right">
		<button class="btn btn-primary btn-sm admin--add js-click-template-insert-parameter" type="button">{+START,INCLUDE,ICON}NAME=admin/add{+END} <span>{LANG*}</span></button>
	</div>
</div>
