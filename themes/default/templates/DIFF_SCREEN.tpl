{TITLE}

<div class="diff">{DIFF}</div>

<div class="box box---diff-screen"><div class="box-inner">
	<form title="{!PRIMARY_PAGE_FORM}" action="{$SELF_URL*}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<p>
			<label for="without_whitespace">{!DIFF_WITHOUT_WHITESPACE}:</label>
			<input type="checkbox" id="without_whitespace" name="without_whitespace" value="1"{+START,IF,{WITHOUT_WHITESPACE}} checked="checked"{+END} />
		</p>

		<p>
			<label for="without_html_tags">{!DIFF_WITHOUT_HTML_TAGS}:</label>
			<input type="checkbox" id="without_html_tags" name="without_html_tags" value="1"{+START,IF,{WITHOUT_HTML_TAGS}} checked="checked"{+END} />
		</p>

		<p>
			<label for="unified_diff">Unified diff:</label>
			<input type="checkbox" id="unified_diff" name="unified_diff" value="1"{+START,IF,{UNIFIED_DIFF}} checked="checked"{+END} />
		</p>

		<button data-disable-on-click="1" class="btn btn-primary btn-sm buttons--filter" type="submit" title="{!PROCEED}">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!PROCEED}</button>
	</form>
</div></div>
