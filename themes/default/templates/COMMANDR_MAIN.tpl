{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$REQUIRE_JAVASCRIPT,commandr}

<div id="command-line" data-tpl="commandrMain">
	<div id="commands-go-here">
		<p>{!WELCOME_TO_COMMANDR}</p>
		<hr />
		{+START,IF_NON_EMPTY,{COMMANDS}}{COMMANDS}{+END}
	</div>

	<form title="{!PRIMARY_PAGE_FORM}" action="{SUBMIT_URL*}" method="post" id="commandr-form">
		{$INSERT_FORM_POST_SECURITY}

		<div id="command-prompt">
			<label for="commandr-command">{PROMPT*}</label>
			<input type="text" id="commandr-command" name="command" autofocus="autofocus" class="form-control js-keyup-input-commandr-handle-history" />
			<button class="btn btn-primary btn-sm buttons--proceed js-commandr-button" type="submit" role="textbox">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{$STRIP_TAGS,{!PROCEED_SHORT}}</span></button>
			<img id="commandr-loading-image" style="display: none" width="20" height="20" src="{$IMG*,loading}" title="{!LOADING}" alt="{!LOADING}" />
		</div>
	</form>
</div>
