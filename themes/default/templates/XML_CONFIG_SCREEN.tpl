{TITLE}

<form title="{!PRIMARY_PAGE_FORM}" action="{POST_URL*}" method="post" autocomplete="off" onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1">
	{$INSERT_SPAMMER_BLACKHOLE}

	<div class="constrain_field">
		<label for="xml" class="accessibility_hidden">XML</label>
		<textarea name="xml" id="xml" cols="30" rows="30" class="wide_field">{XML*}</textarea>
	</div>

	<p class="proceed_button">
		<input class="button_screen buttons__save" id="submit_button" accesskey="u" type="submit" value="{!SAVE}" />
	</p>
</form>

<script language="javascript" src="{$BASE_URL*}/data/editarea/edit_area_full.js"></script>
<script>// <![CDATA[
	editAreaLoader.init({
		id : "xml"
		,syntax: "xml"
		,start_highlight: true
		,language: "en"
		,allow_resize: true
		,toolbar: "search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, reset_highlight, word_wrap"
	});
//]]></script>
