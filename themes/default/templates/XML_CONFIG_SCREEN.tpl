{$REQUIRE_JAVASCRIPT,core_configuration}

<div data-view="XmlConfigScreen">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	{+START,IF_PASSED,DESCRIPTION}
		{DESCRIPTION}
	{+END}

	<form title="{!PRIMARY_PAGE_FORM}" action="{POST_URL*}" method="post" data-submit-modsecurity-workaround="1">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			<label for="xml" class="accessibility-hidden">XML</label>
			<textarea name="xml" id="xml" cols="30" rows="30" class="form-control form-control-wide">{XML*}</textarea>
		</div>

		<p class="proceed-button">
			<button class="btn btn-primary btn-scr buttons--save" id="submit-button" accesskey="u" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} <span>{!SAVE}</span></button>
		</p>
	</form>

	{+START,IF_PASSED,REVISIONS}
		{REVISIONS}
	{+END}
</div>
<script {$CSP_NONCE_HTML} defer="defer" src="{$BASE_URL*}/data/ace/ace.js"></script>
<script {$CSP_NONCE_HTML} defer="defer" src="{$BASE_URL*}/data/ace/ace_composr.js"></script>