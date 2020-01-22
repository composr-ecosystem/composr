{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{$PARAGRAPH,{TEXT}}

{+START,IF_NON_EMPTY,{DELETE_URL}}
	{+START,SET,extra_buttons}
		<input type="hidden" id="delete-field" name="delete" value="0" />
		<input class="btn btn-danger btn-scr" id="delete-button" type="button" value="{$?,{IS_TRANSLATION},{!DELETE_TRANSLATION},{!DELETE}}" />
	{+END}
{+END}

{POSTING_FORM}

{REVISIONS}

<script {$CSP_NONCE_HTML}>
	document.getElementById('delete-button').onclick = function() {
		document.getElementById('delete-field').value='1';
		document.getElementById('delete-button').form.submit();
	};
</script>
