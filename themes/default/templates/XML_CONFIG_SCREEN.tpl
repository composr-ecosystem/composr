{$REQUIRE_JAVASCRIPT,core_configuration}

<div data-view="XmlConfigScreen">
	{TITLE}

	<form title="{!PRIMARY_PAGE_FORM}" action="{POST_URL*}" method="post" class="js-form-xml-config">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			<label for="xml" class="accessibility-hidden">XML</label>
			<textarea name="xml" id="xml" cols="30" rows="30" class="form-control form-control-wide">{XML*}</textarea>
		</div>

		<p class="proceed-button">
			<button class="btn btn-primary btn-scr buttons--save" id="submit-button" accesskey="u" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} <span>{!SAVE}</span></button>
		</p>
	</form>
</div>
<script {$CSP_NONCE_HTML} defer="defer" src="{$BASE_URL*}/data/ace/ace.js"></script>
<script {$CSP_NONCE_HTML} defer="defer" src="{$BASE_URL*}/data/ace/ace_composr.js"></script>
