{$SET,question,{!CONFIRM_DELETE,{TITLE}}}
<div data-tpl="cnsSavedWarning" data-tpl-params="{+START,PARAMS_JSON,TITLE,EXPLANATION,MESSAGE,MESSAGE_HTML,question}{_*}{+END}">
	<h3>
		{TITLE*}
	</h3>
	<nav>
		<ul class="actions-list">
			<li>
				{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}
				<form title="{!LOAD} {TITLE*}" action="#" method="post" class="inline" id="saved-use--{TITLE|}">
					{$INSERT_FORM_POST_SECURITY}

					<div class="inline">
						<button type="submit" class="button-hyperlink" data-cms-tooltip="{ contents: '{$ESCAPE;^*,<h2>{EXPLANATION*}</h2>{MESSAGE_HTML},NULL_ESCAPED}', width: '700px' }">{!LOAD} {TITLE*}</button>
					</div>
				</form>
			</li>
			<li id="saved-delete--{TITLE|}">{DELETE_LINK}</li>
		</ul>
	</nav>
</div>
