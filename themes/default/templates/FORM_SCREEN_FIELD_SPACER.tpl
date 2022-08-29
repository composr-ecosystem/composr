<!-- form-table-field-spacer (don't remove this comment when templating) -->
<tr class="form-table-field-spacer" data-tpl="formScreenFieldSpacer" data-tpl-params="{+START,PARAMS_JSON,TITLE,SECTION_HIDDEN}{_*}{+END}">
	<th colspan="2" class="table-heading-cell vertical-alignment">
		{+START,IF_PASSED,TITLE}
			{+START,IF_NON_PASSED_OR_FALSE,FORCE_OPEN}
				<a class="toggleable-tray-button js-click-toggle-subord-fields js-keypress-toggle-subord-fields" id="fes-{TITLE|}" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
					NAME=trays/contract
					ICON_CLASS=right
					ICON_SIZE=20
				{+END}</a>
			{+END}

			<span class="h3 {+START,IF_NON_PASSED_OR_FALSE,FORCE_OPEN} toggleable-tray-button js-click-toggle-subord-fields js-keypress-toggle-subord-fields{+END}">{TITLE*}</span>

			{+START,IF,{$EQ,{TITLE},{!ADDRESS}}}
				<button class="btn btn-primary btn-sm buttons--search js-click-geolocate-address-fields" type="button">{+START,INCLUDE,ICON}NAME=buttons/search{+END} <span>{!locations:FIND_ME}</span></button>
			{+END}
		{+END}

		{+START,IF_PASSED,HELP}{+START,IF_NON_EMPTY,{HELP}}
			<div {+START,IF_PASSED,TITLE} id="fes-{TITLE|}-help"{+END}>
				{$PARAGRAPH,{HELP*}}
			</div>
		{+END}{+END}
	</th>
</tr>
