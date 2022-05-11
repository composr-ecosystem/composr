{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<div class="float_surrounder">
	<div style="display: none" id="selectors">
		<div id="selectors_inner">
			<p class="lonely_label">{!SELECTORS_PARENT_PAGE}:</p>
		</div>

		<div class="css_editor_selector_tips"><section class="box"><div class="box_inner"><h2>{!HELP}</h2><p>{!CSS_EDITOR_SELECTOR_TIPS}</p></div></section></div>

		<div class="css_editor_selector_tips"><section class="box"><div class="box_inner">
			{+START,IF,{$EQ,{FILE},global.css}}{+START,IF,{$JS_ON}}
				<h2>{!QUICK_CSS_CHANGE_LINKS}:</h2>
				<ul>
					<li>
						<a onclick="do_editarea_search('font-family'); return false;" href="#">{!CHANGE_FONT}</a>
					</li>
					<li>
						<a onclick="do_editarea_search('inner_background'); return false;" href="#">{!CHANGE_INNER_BACKGROUND}</a>
					</li>
					<li>
						<a onclick="do_editarea_search('block_background'); return false;" href="#">{!CHANGE_BLOCK_BACKGROUND}</a>
					</li>
					{+START,IF,{$CONFIG_OPTION,fixed_width}}
						<li>
							<a onclick="do_editarea_search('outer_background'); return false;" href="#">{!CHANGE_OUTER_BACKGROUND}</a>
						</li>
						<li>
							<a onclick="do_editarea_search('logo_outer'); return false;" href="#">{!CHANGE_HEADER_IMAGE}</a>
						</li>
						<li>
							<a onclick="do_editarea_search('#main_website #main_website_inner'); return false;" href="#">{!CHANGE_FIXED_WIDTH}</a>
						</li>
					{+END}
				</ul>
			{+END}{+END}

			<h3>{!COMMON_CSS_PROPERTIES}:</h3>

			<div>
				<h4 class="toggleable_tray_title">
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: " title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">Background Properties</a>
				</h4>
				<div class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
					<table class="columned_table results_table" cellspacing="0" cellpadding="0" border="1" width="100%">
						<tbody><tr>
							<th width="28%" align="left">Property</th>
							<th width="67%" align="left">Description</th>
							<th width="5%" align="left">CSS</th>
						</tr>
						<tr>
							<td><a target="_blank" title="background {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/css3_pr_background.asp">background</a></td>
							<td>Sets all the background properties in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="background-color {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_background-color.asp">background-color</a></td>
							<td>Sets the background color of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="background-image {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_background-image.asp">background-image</a></td>
							<td>Sets the background image for an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="background-position {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_background-position.asp">background-position</a></td>
							<td>Sets the starting position of a background image</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="background-repeat {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_background-repeat.asp">background-repeat</a></td>
							<td>Sets how a background image will be repeated</td>
							<td>1</td>
						</tr>
					</tbody></table>
				</div>
			</div>

			<div>
				<h4 class="toggleable_tray_title">
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: Background Properties" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">Border Properties</a>
				</h4>
				<div class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
					<table class="columned_table results_table" cellspacing="0" cellpadding="0" border="1" width="100%">
						<tbody><tr>
							<th width="28%" align="left">Property</th>
							<th width="67%" align="left">Description</th>
							<th width="5%" align="left">CSS</th>
						</tr>
						<tr>
							<td><a target="_blank" title="border {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_border.asp">border</a></td>
							<td>Sets all the border properties in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="border-bottom {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_border-bottom.asp">border-bottom</a></td>
							<td>Sets all the bottom border properties in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="border-color {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_border-color.asp">border-color</a></td>
							<td>Sets the color of the four borders</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="border-left {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_border-left.asp">border-left</a></td>
							<td>Sets all the left border properties in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="border-right {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_border-right.asp">border-right</a></td>
							<td>Sets all the right border properties in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="border-style {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_border-style.asp">border-style</a></td>
							<td>Sets the style of the four borders</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="border-top {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_border-top.asp">border-top</a></td>
							<td>Sets all the top border properties in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="border-width {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_border-width.asp">border-width</a></td>
							<td>Sets the width of the four borders</td>
							<td>1</td>
						</tr>
					</tbody></table>
				</div>
			</div>

			<div>
				<h4 class="toggleable_tray_title">
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: Dimension Properties" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">Dimension Properties</a>
				</h4>
				<div class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
					<table class="columned_table results_table" cellspacing="0" cellpadding="0" border="1" width="100%">
						<tbody><tr>
							<th width="28%" align="left">Property</th>
							<th width="67%" align="left">Description</th>
							<th width="5%" align="left">CSS</th>
						</tr>
						<tr>
							<td><a target="_blank" title="height {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_dim_height.asp">height</a></td>
							<td>Sets the height of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="max-height {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_dim_max-height.asp">max-height</a></td>
							<td>Sets the maximum height of an element</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="max-width {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_dim_max-width.asp">max-width</a></td>
							<td>Sets the maximum width of an element</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="min-height {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_dim_min-height.asp">min-height</a></td>
							<td>Sets the minimum height of an element</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="min-width {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_dim_min-width.asp">min-width</a></td>
							<td>Sets the minimum width of an element</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="width {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_dim_width.asp">width</a></td>
							<td>Sets the width of an element</td>
							<td>1</td>
						</tr>
					</tbody></table>
				</div>
			</div>

			<div>
				<h4 class="toggleable_tray_title">
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: List Properties" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">List Properties</a>
				</h4>
				<div class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
					<table class="columned_table results_table" cellspacing="0" cellpadding="0" border="1" width="100%">
						<tbody><tr>
							<th width="28%" align="left">Property</th>
							<th width="67%" align="left">Description</th>
							<th width="5%" align="left">CSS</th>
						</tr>
						<tr>
							<td><a target="_blank" title="list-style {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_list-style.asp">list-style</a></td>
							<td>Sets all the properties for a list in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="list-style-type {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_list-style-type.asp">list-style-type</a></td>
							<td>Specifies the type of list-item marker</td>
							<td>1</td>
						</tr>
					</tbody></table>
				</div>
			</div>

			<div>
				<h4 class="toggleable_tray_title">
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: Margin/Padding Properties" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">Margin/Padding Properties</a>
				</h4>
				<div class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
					<table class="columned_table results_table" cellspacing="0" cellpadding="0" border="1" width="100%">
						<tbody><tr>
							<th width="28%" align="left">Property</th>
							<th width="67%" align="left">Description</th>
							<th width="5%" align="left">CSS</th>
						</tr>
						<tr>
							<td><a target="_blank" title="margin {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_margin.asp">margin</a></td>
							<td>Sets all the margin properties in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="margin-bottom {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_margin-bottom.asp">margin-bottom</a></td>
							<td>Sets the bottom margin of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="margin-left {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_margin-left.asp">margin-left</a></td>
							<td>Sets the left margin of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="margin-right {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_margin-right.asp">margin-right</a></td>
							<td>Sets the right margin of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="margin-top {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_margin-top.asp">margin-top</a></td>
							<td>Sets the top margin of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="padding {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_padding.asp">padding</a></td>
							<td>Sets all the padding properties in one declaration</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="padding-bottom {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_padding-bottom.asp">padding-bottom</a></td>
							<td>Sets the bottom padding of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="padding-left {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_padding-left.asp">padding-left</a></td>
							<td>Sets the left padding of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="padding-right {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_padding-right.asp">padding-right</a></td>
							<td>Sets the right padding of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="padding-top {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_padding-top.asp">padding-top</a></td>
							<td>Sets the top padding of an element</td>
							<td>1</td>
						</tr>
					</tbody></table>
				</div>
			</div>

			<div>
				<h4 class="toggleable_tray_title">
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: Positioning Properties" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">Positioning Properties</a>
				</h4>
				<div class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
					<table class="columned_table results_table" cellspacing="0" cellpadding="0" border="1" width="100%">
						<tbody><tr>
							<th width="28%" align="left">Property</th>
							<th width="67%" align="left">Description</th>
							<th width="5%" align="left">CSS</th>
						</tr>
						<tr>
							<td><a target="_blank" title="bottom {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_pos_bottom.asp">bottom</a></td>
							<td>Sets the bottom margin edge for a positioned box</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="clear {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_class_clear.asp">clear</a></td>
							<td>Specifies which sides of an element where other floating elements are not allowed</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="display {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_class_display.asp">display</a></td>
							<td>Specifies the type of box an element should generate</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="float {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_class_float.asp">float</a></td>
							<td>Specifies whether or not a box should float</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="left {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_pos_left.asp">left</a></td>
							<td>Sets the left margin edge for a positioned box</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="overflow {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_pos_overflow.asp">overflow</a></td>
							<td>Specifies what happens if content overflows an element's box</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="position {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_class_position.asp">position</a></td>
							<td>Specifies the type of positioning for an element</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="right {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_pos_right.asp">right</a></td>
							<td>Sets the right margin edge for a positioned box</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="top {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_pos_top.asp">top</a></td>
							<td>Sets the top margin edge for a positioned box</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="visibility {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_class_visibility.asp">visibility</a></td>
							<td>Specifies whether or not an element is visible</td>
							<td>2</td>
						</tr>
						<tr>
							<td><a target="_blank" title="z-index {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_pos_z-index.asp">z-index</a></td>
							<td>Sets the stack order of an element</td>
							<td>2</td>
						</tr>
					</tbody></table>
				</div>
			</div>

			<div>
				<h4 class="toggleable_tray_title">
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: Text/Font Properties" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
					<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);">Text/Font Properties</a>
				</h4>
				<div class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
					<table class="columned_table results_table" cellspacing="0" cellpadding="0" border="1" width="100%">
						<tbody><tr>
							<th width="28%" align="left">Property</th>
							<th width="67%" align="left">Description</th>
							<th width="5%" align="left">CSS</th>
						</tr>
						<tr>
							<td><a target="_blank" title="font-family {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_font_font-family.asp">font-family</a></td>
							<td>Specifies the font family for text</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="font-size {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_font_font-size.asp">font-size</a></td>
							<td>Specifies the font size of text</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="font-style {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_font_font-style.asp">font-style</a></td>
							<td>Specifies the font style for text</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="font-weight {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_font_weight.asp">font-weight</a></td>
							<td>Specifies the weight of a font</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="color {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_text_color.asp">color</a></td>
							<td>Sets the color of text</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="line-height {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_dim_line-height.asp">line-height</a></td>
							<td>Sets the line height</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="text-align {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_text_text-align.asp">text-align</a></td>
							<td>Specifies the horizontal alignment of text</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="text-decoration {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_text_text-decoration.asp">text-decoration</a></td>
							<td>Specifies the decoration added to text</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="text-transform {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_text_text-transform.asp">text-transform</a></td>
							<td>Controls the capitalisation of text</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="vertical-align {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_pos_vertical-align.asp">vertical-align</a></td>
							<td>Sets the vertical alignment of an element</td>
							<td>1</td>
						</tr>
						<tr>
							<td><a target="_blank" title="white-space {!LINK_NEW_WINDOW}" href="http://www.w3schools.com/cssref/pr_text_white-space.asp">white-space</a></td>
							<td>Specifies how white-space inside an element is handled</td>
							<td>1</td>
						</tr>
					</tbody></table>
				</div>
			</div>
		</div></section></div>
	</div>
</div>

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post" autocomplete="off" onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1">
	{$INSERT_SPAMMER_BLACKHOLE}

	<div>
		<p class="lonely_label accessibility_hidden"><label for="css">CSS</label>:</p>

		<div class="constrain_field">
			<textarea onkeydown="if (key_pressed(event,9)) { insert_textbox(this,'	'); return false; }" id="css" class="wide_field textarea_scroll" cols="70" rows="30" name="css">{CSS*}</textarea>
		</div>

		{+START,IF_NON_EMPTY,{ENTRIES}}
			<div id="colours_go_here">
			</div>
			<script>// <![CDATA[
				if (typeof window.do_color_chooser!='undefined')
				{
					{ENTRIES`}
					do_color_chooser();
				}
			//]]></script>
		{+END}

		<input type="hidden" name="theme" value="{THEME*}" />
		<input type="hidden" name="tempcodecss__css" value="1" />
		<input type="hidden" name="file" value="{FILE*}" />

		<div class="float_surrounder buttons_group">
			<div class="right">
				<input id="save_button" onclick="disable_button_just_clicked(this); this.form.target='_self'; this.form.action='{URL;*}';" class="button_screen buttons__save" type="submit" value="{!SAVE}" />
			</div>
			{+START,IF,{$JS_ON}}
				<div class="right">
					<input onclick="disable_button_just_clicked(this); this.form.target='save_frame'; this.form.action='{URL;*}{$?,{$IN_STR,{URL},?},&amp;,?}save_and_stay=1';" class="button_screen buttons__save_and_stay" type="submit" accesskey="U" value="{!SAVE_AND_STAY}" />
				</div>
			{+END}
		</div>
	</div>
</form>

<div class="original_template">
	<div class="box">
		<h2 class="toggleable_tray_title">
			<a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode);"><img alt="{!EXPAND}: {!ORIGINAL}" title="{!EXPAND}" src="{$IMG*,1x/trays/expand2}" srcset="{$IMG*,2x/trays/expand2} 2x" /></a>
			<label for="f_old"><a class="toggleable_tray_button" href="#" onclick="return toggleable_tray(this.parentNode.parentNode.parentNode);">{!ORIGINAL}</a>:</label>
		</h2>
		<div class="toggleable_tray" style="display: {$JS_ON,none,block}" aria-expanded="false">
			<form title="{!ORIGINAL}" action="{$BASE_URL*}/index.php" method="post" autocomplete="off">
				<div class="constrain_field">
					<textarea id="f_old" name="f_old" cols="70" rows="23" readonly="readonly" class="wide_field">{OLD_CONTENTS*}</textarea>
				</div>
			</form>
		</div>
	</div>
</div>

{+START,IF,{$JS_ON}}
	<p id="switch_button" class="buttons_group">
		<a class="{SWITCH_ICON*} button_screen" href="{SWITCH_URL*}">{SWITCH_STRING*}</a>
	</p>
{+END}

{REVISIONS}

<iframe name="save_frame" id="save_frame" title="{!SAVE_AND_STAY}" class="hidden_save_frame" src="{$BASE_URL*}/uploads/index.html">{!SAVE_AND_STAY}</iframe>

<script>// <![CDATA[
	add_event_listener_abstract(window,'load',function() {
		if (window.location.hash) window.setTimeout(function() {
			document.getElementById('frame_css').contentWindow.editArea.execCommand('show_search');
			document.getElementById('frame_css').contentWindow.document.getElementById('area_search').value=window.location.hash.substr(1,window.location.hash.length-1);
			document.getElementById('frame_css').contentWindow.editArea.execCommand('area_search');
		} , 2000);

		// If this is a contextual edit, start talking to the parent window
		if ((window.opener) && (window.opener.document))
		{
			load_contextual_css_editor('{FILE;/}');
			document.getElementById('save_button').style.display='none';
			if (document.getElementById('switch_button')) document.getElementById('switch_button').style.display='none';
		}
	});
//]]></script>

{+START,IF,{$JS_ON}}
	<section class="box"><div class="box_inner">
		<h2>{!CSS_EQUATION_HELPER}</h2>

		<p>{!DESCRIP_CSS_EQUATION_HELPER}</p>

		<form title="{!CSS_EQUATION_HELPER}" action="{$BASE_URL*}/index.php" onsubmit="return false;" method="post" autocomplete="off">
			<p class="vertical_alignment">
				<label for="css_equation">{!CSS_EQUATION_HELPER}</label>
				<input name="css_equation" id="css_equation" type="text" value="100% seed" />

				<input onclick="cancel_bubbling(event,this); var result=load_snippet('themewizard_equation&amp;theme={THEME;*}&amp;css_equation='+window.encodeURIComponent(document.getElementById('css_equation').value)); if (result=='' || result.indexOf('&lt;html')!=-1) window.fauxmodal_alert('{!ERROR_OCCURRED;}'); else document.getElementById('css_result').value=result; return false;" class="button_screen_item buttons__calculate" type="submit" value="{!CALCULATE}" />

				&rarr;

				<label class="accessibility_hidden" for="css_result">{!RESULT}</label>
				<output><input readonly="readonly" name="css_result" id="css_result" type="text" value="({!RESULT})" /></output>
			</p>
		</form>
	</div></section>
{+END}
