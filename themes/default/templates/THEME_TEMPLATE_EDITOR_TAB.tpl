<div data-view="ThemeTemplateEditorTab" data-view-params="{+START,PARAMS_JSON,FILE,FILE_ID,HIGHLIGHTER_TYPE,INCLUDE_CSS_EDITING,THEME,LIVE_PREVIEW_URL,SCREEN_PREVIEW_URL}{_*}{+END}">

{+START,IF_PASSED_AND_TRUE,OWN_FORM}
<form title="{!PRIMARY_PAGE_FORM}" method="post" action="#">
{+END}

{$,Toolbarish}

{+START,IF,{INCLUDE_CSS_EDITING}}
	<div class="template-editing-toolbar" data-toggleable-tray="{ accordion: true }">
		<h3 class="toggleable-tray-unstyled">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=20
			{+END}</a>
			<a class="non-link js-tray-onclick-toggle-tray" href="#!">{!CSS_EDITING_TOOLS}</a>
		</h3>
		<div class="toggleable-tray js-tray-content" style="display: none" id="c-{FILE_ID*}">
			<div class="css-editor-rhs-column"><section class="box"><div class="box-inner">
				<h3>{!COMMON_CSS_PROPERTIES}:</h3>

				<div class="accordion-trayitem js-tray-accordion-item">
					<h4 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!">Background Properties</a>
					</h4>
					<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body" style="display: none">
						<table class="columned-table results-table" width="100%">
							<tbody><tr>
								<th width="28%">Property</th>
								<th width="67%">Description</th>
								<th width="5%">CSS</th>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="background {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/css3_pr_background.asp">background</a></td>
								<td>Sets all the background properties in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="background-color {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_background-color.asp">background-color</a></td>
								<td>Sets the background color of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="background-image {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_background-image.asp">background-image</a></td>
								<td>Sets the background image for an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="background-position {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_background-position.asp">background-position</a></td>
								<td>Sets the starting position of a background image</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="background-repeat {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_background-repeat.asp">background-repeat</a></td>
								<td>Sets how a background image will be repeated</td>
								<td>1</td>
							</tr>
						</tbody></table>
					</div>
				</div>

				<div class="accordion-trayitem js-tray-accordion-item">
					<h4 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!">Border Properties</a>
					</h4>
					<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body" style="display: none">
						<table class="columned-table results-table" width="100%">
							<tbody><tr>
								<th width="28%">Property</th>
								<th width="67%">Description</th>
								<th width="5%">CSS</th>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="border {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_border.asp">border</a></td>
								<td>Sets all the border properties in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="border-bottom {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_border-bottom.asp">border-bottom</a></td>
								<td>Sets all the bottom border properties in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="border-color {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_border-color.asp">border-color</a></td>
								<td>Sets the color of the four borders</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="border-left {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_border-left.asp">border-left</a></td>
								<td>Sets all the left border properties in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="border-right {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_border-right.asp">border-right</a></td>
								<td>Sets all the right border properties in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="border-style {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_border-style.asp">border-style</a></td>
								<td>Sets the style of the four borders</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="border-top {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_border-top.asp">border-top</a></td>
								<td>Sets all the top border properties in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="border-width {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_border-width.asp">border-width</a></td>
								<td>Sets the width of the four borders</td>
								<td>1</td>
							</tr>
						</tbody></table>
					</div>
				</div>

				<div class="accordion-trayitem js-tray-accordion-item">
					<h4 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!">Dimension Properties</a>
					</h4>
					<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body" style="display: none">
						<table class="columned-table results-table" width="100%">
							<tbody><tr>
								<th width="28%">Property</th>
								<th width="67%">Description</th>
								<th width="5%">CSS</th>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="width {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_dim_width.asp">width</a></td>
								<td>Sets the width of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="min-width {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_dim_min-width.asp">min-width</a></td>
								<td>Sets the minimum width of an element</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="max-width {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_dim_max-width.asp">max-width</a></td>
								<td>Sets the maximum width of an element</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="height {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_dim_height.asp">height</a></td>
								<td>Sets the height of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="min-height {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_dim_min-height.asp">min-height</a></td>
								<td>Sets the minimum height of an element</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="max-height {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_dim_max-height.asp">max-height</a></td>
								<td>Sets the maximum height of an element</td>
								<td>2</td>
							</tr>
						</tbody></table>
					</div>
				</div>

				<div class="accordion-trayitem js-tray-accordion-item">
					<h4 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!">List Properties</a>
					</h4>
					<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body" style="display: none">
						<table class="columned-table results-table" width="100%">
							<tbody><tr>
								<th width="28%">Property</th>
								<th width="67%">Description</th>
								<th width="5%">CSS</th>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="list-style {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_list-style.asp">list-style</a></td>
								<td>Sets all the properties for a list in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="list-style-type {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_list-style-type.asp">list-style-type</a></td>
								<td>Specifies the type of list-item marker</td>
								<td>1</td>
							</tr>
						</tbody></table>
					</div>
				</div>

				<div class="accordion-trayitem js-tray-accordion-item">
					<h4 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion">Margin/Padding Properties</a>
					</h4>
					<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body" style="display: none">
						<table class="columned-table results-table" width="100%">
							<tbody><tr>
								<th width="28%">Property</th>
								<th width="67%">Description</th>
								<th width="5%">CSS</th>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="margin {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_margin.asp">margin</a></td>
								<td>Sets all the margin properties in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="margin-bottom {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_margin-bottom.asp">margin-bottom</a></td>
								<td>Sets the bottom margin of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="margin-left {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_margin-left.asp">margin-left</a></td>
								<td>Sets the left margin of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="margin-right {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_margin-right.asp">margin-right</a></td>
								<td>Sets the right margin of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="margin-top {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_margin-top.asp">margin-top</a></td>
								<td>Sets the top margin of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="padding {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_padding.asp">padding</a></td>
								<td>Sets all the padding properties in one declaration</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="padding-bottom {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_padding-bottom.asp">padding-bottom</a></td>
								<td>Sets the bottom padding of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="padding-left {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_padding-left.asp">padding-left</a></td>
								<td>Sets the left padding of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="padding-right {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_padding-right.asp">padding-right</a></td>
								<td>Sets the right padding of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="padding-top {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_padding-top.asp">padding-top</a></td>
								<td>Sets the top padding of an element</td>
								<td>1</td>
							</tr>
						</tbody></table>
					</div>
				</div>

				<div class="accordion-trayitem js-tray-accordion-item">
					<h4 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!">Positioning Properties</a>
					</h4>
					<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body" style="display: none">
						<table class="columned-table results-table" width="100%">
							<tbody><tr>
								<th width="28%">Property</th>
								<th width="67%">Description</th>
								<th width="5%">CSS</th>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="float {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_class_float.asp">float</a></td>
								<td>Specifies whether or not a box should float</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="position {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_class_position.asp">position</a></td>
								<td>Specifies the type of positioning for an element</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="left {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_pos_left.asp">left</a></td>
								<td>Sets the left margin edge for a positioned box</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="right {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_pos_right.asp">right</a></td>
								<td>Sets the right margin edge for a positioned box</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="top {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_pos_top.asp">top</a></td>
								<td>Sets the top margin edge for a positioned box</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="bottom {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_pos_bottom.asp">bottom</a></td>
								<td>Sets the bottom margin edge for a positioned box</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="clear {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_class_clear.asp">clear</a></td>
								<td>Specifies which sides of an element where other floating elements are not allowed</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="display {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_class_display.asp">display</a></td>
								<td>Specifies the type of box an element should generate</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="overflow {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_pos_overflow.asp">overflow</a></td>
								<td>Specifies what happens if content overflows an element's box</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="visibility {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_class_visibility.asp">visibility</a></td>
								<td>Specifies whether or not an element is visible</td>
								<td>2</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="z-index {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_pos_z-index.asp">z-index</a></td>
								<td>Sets the stack order of an element</td>
								<td>2</td>
							</tr>
						</tbody></table>
					</div>
				</div>

				<div class="accordion-trayitem js-tray-accordion-item">
					<h4 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=20
						{+END}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-accordion" href="#!">Text/Font Properties</a>
					</h4>
					<div class="toggleable-tray accordion-trayitem-body js-tray-accordion-item-body" style="display: none">
						<table class="columned-table results-table" width="100%">
							<tbody><tr>
								<th width="28%">Property</th>
								<th width="67%">Description</th>
								<th width="5%">CSS</th>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="font-family {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_font_font-family.asp">font-family</a></td>
								<td>Specifies the font family for text</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="font-size {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_font_font-size.asp">font-size</a></td>
								<td>Specifies the font size of text</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="font-style {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_font_font-style.asp">font-style</a></td>
								<td>Specifies the font style for text</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="font-weight {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_font_weight.asp">font-weight</a></td>
								<td>Specifies the weight of a font</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="color {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_text_color.asp">color</a></td>
								<td>Sets the color of text</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="line-height {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_dim_line-height.asp">line-height</a></td>
								<td>Sets the line height</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="text-align {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_text_text-align.asp">text-align</a></td>
								<td>Specifies the horizontal alignment of text</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="text-decoration {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_text_text-decoration.asp">text-decoration</a></td>
								<td>Specifies the decoration added to text</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="text-transform {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_text_text-transform.asp">text-transform</a></td>
								<td>Controls the capitalisation of text</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="vertical-align {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_pos_vertical-align.asp">vertical-align</a></td>
								<td>Sets the vertical alignment of an element</td>
								<td>1</td>
							</tr>
							<tr>
								<td><a rel="noopener" target="_blank" title="white-space {!LINK_NEW_WINDOW}" href="https://www.w3schools.com/cssref/pr_text_white-space.asp">white-space</a></td>
								<td>Specifies how white-space inside an element is handled</td>
								<td>1</td>
							</tr>
						</tbody></table>
					</div>
				</div>
			</div></section></div>

			<div class="css-editor-lhs-column">
				<div class="css-editor-selectors" id="selectors-{FILE_ID*}" style="display: none"{$,Only becomes visible if it has an anchor page}>
					<div id="selectors-inner-{FILE_ID*}">
						<p class="lonely-label">{!SELECTORS_PARENT_PAGE}:</p>
					</div>

					<section class="box"><div class="box-inner">
						<h2>{!HELP}</h2>
						<p>{!CSS_EDITOR_SELECTOR_TIPS}</p>
					</div></section>
				</div>
			</div>
		</div>
	</div>
{+END}

{+START,IF,{INCLUDE_TEMPCODE_EDITING}}
	<div class="template-editing-toolbar" data-toggleable-tray="{}">
		<h3 class="toggleable-tray-unstyled">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=20
			{+END}</a>
			<a class="non-link js-tray-onclick-toggle-tray" href="#!">{!SYMBOLS_AND_DIRECTIVES}</a>
		</h3>
		<div class="toggleable-tray js-tray-content" style="display: none" id="b-{FILE_ID*}">
			{PARAMETERS}
			{DIRECTIVES}
			{MISC_SYMBOLS}
			{PROGRAMMATIC_SYMBOLS}
			{ABSTRACTION_SYMBOLS}
			{ARITHMETICAL_SYMBOLS}
			{FORMATTING_SYMBOLS}
			{LOGICAL_SYMBOLS}
		</div>
	</div>
{+END}

<div id="e-{FILE_ID*}-wrap" class="main-editor">
	{$,Main editor}

	<label class="accessibility-hidden" for="e_{FILE_ID*}">{!TEMPLATE}</label>
	<div>
		<textarea id="e_{FILE_ID*}" name="e_{FILE_ID*}" cols="70" rows="22" class="form-control form-control-wide js-ta-tpl-editor textarea-scroll">{CONTENTS*}</textarea>
	</div>
</div>

{$,Buttons}

<div class="clearfix buttons-group">
	<div class="buttons-group-inner">
		<button data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save js-btn-save-content" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SAVE}</button>

		{+START,SET,preview_buttons}
			{+START,IF_PASSED,LIVE_PREVIEW_URL}
				<!-- Preview in live rendering -->
				<button class="btn btn-primary btn-scr buttons--preview js-btn-live-preview" type="submit">{+START,INCLUDE,ICON}NAME=buttons/preview{+END} <span>{!LIVE_PREVIEW}</span></button>
			{+END}
			{+START,IF_PASSED,SCREEN_PREVIEW_URL}
				<!-- Preview in screen preview (Lorem ipsum) -->
				<button class="btn btn-primary btn-scr buttons--preview js-btn-screen-preview" type="submit">{+START,INCLUDE,ICON}NAME=buttons/preview{+END} <span>{!LOREM_PREVIEW}</span></button>
			{+END}
		{+END}
		{+START,IF_NON_EMPTY,{$TRIM,{$GET,preview_buttons}}}
			{$GET,preview_buttons}

			<label for="mobile_preview_{FILE_ID*}"><input type="checkbox" name="mobile_preview_{FILE_ID*}" id="mobile_preview_{FILE_ID*}" value="1" /> {!MOBILE_VERSION}</label>
		{+END}
	</div>
</div>

{$,GUIDs}

{+START,IF_NON_EMPTY,{GUIDS}}
	<div class="guids" data-toggleable-tray="{}">
		<h3 class="toggleable-tray-unstyled">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=20
			{+END}</a>
			<a class="non-link js-tray-onclick-toggle-tray" href="#!">{!TEMPLATE_GUIDS}</a>
		</h3>
		<div class="toggleable-tray js-tray-content" style="display: none">
			<table class="columned-table autosized-table revision_box results-table wide-table">
				<thead>
					<tr>
						<th>{!FILENAME}</th>
						<th>{!LINE}</th>
						<th>{!TEMPLATE_GUID}</th>
					</tr>
				</thead>
				<tbody>
					{+START,LOOP,GUIDS}
						<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}">
							<td>
								<kbd>{GUID_FILENAME*}</kbd>
							</td>
							<td>
								{+START,IF,{$ADDON_INSTALLED,code_editor}}
									<a rel="noopener" target="_blank" title="{GUID_LINE*} {!LINK_NEW_WINDOW}" href="{$BASE_URL*}/code_editor.php?path={GUID_FILENAME*}&amp;line={GUID_LINE*}">{GUID_LINE*}</a>
								{+END}
								{+START,IF,{$NOT,{$ADDON_INSTALLED,code_editor}}}
									{GUID_LINE*}
								{+END}
							</td>
							<td>
								<a href="#!" class="js-a-insert-guid" data-insert-guid="{GUID_GUID*}">{$?,{GUID_IS_LIVE},<strong>{GUID_GUID*}</strong>,{GUID_GUID*}}</a>
							</td>
						</tr>
					{+END}
				</tbody>
			</table>
		</div>
	</div>
{+END}

{$,Related templates}

{+START,IF_NON_EMPTY,{RELATED}}
	<div class="related" data-toggleable-tray="{}">
		<h3 class="toggleable-tray-unstyled">
			<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
				NAME=trays/expand
				ICON_SIZE=20
			{+END}</a>
			<a class="non-link js-tray-onclick-toggle-tray" href="#!">{!RELATED_TEMPLATES}</a>
		</h3>
		<div class="toggleable-tray js-tray-content" style="display: none">
			<ul>
				{+START,LOOP,RELATED}
					<li>
						<a href="#!" class="js-a-tpl-editor-add-tab" data-template-file="{_loop_var*}">{_loop_var*}</a>
					</li>
				{+END}
			</ul>
		</div>
	</div>
{+END}

{$,Revisions}

{+START,INCLUDE,THEME_TEMPLATE_EDITOR_TAB_REVISIONS}{+END}

{$,CSS equation helper}

{+START,IF,{INCLUDE_CSS_EDITING}}
	<section class="box box--css-equation-helper"><div class="box-inner">
		<h2>{!CSS_EQUATION_HELPER}</h2>

		<p>{!DESCRIP_CSS_EQUATION_HELPER}</p>

		<p class="vertical-alignment">
			<label for="css-equation-{FILE_ID*}">{!CSS_EQUATION_HELPER}</label>
			<input name="css_equation" id="css-equation-{FILE_ID*}" class="form-control" type="text" value="100% seed" />

			<button class="btn btn-primary btn-scri buttons--calculate js-btn-equation-helper" type="submit">{+START,INCLUDE,ICON}NAME=buttons/calculate{+END} <span>{!CALCULATE}</span></button>

			&rarr;

			<label class="accessibility-hidden" for="css-result-{FILE_ID*}">{!RESULT}</label>
			<output><input readonly="readonly" name="css_result" id="css-result-{FILE_ID*}" class="form-control" type="text" value="({!RESULT})" /></output>
		</p>
	</div></section>
{+END}

{+START,IF_PASSED_AND_TRUE,OWN_FORM}
</form>
{+END}

</div>
