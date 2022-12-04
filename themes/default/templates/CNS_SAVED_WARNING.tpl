{$SET,question,{!CONFIRM_DELETE,{TITLE}}}
<div data-tpl="cnsSavedWarning" data-tpl-params="{+START,PARAMS_JSON,TITLE,EXPLANATION,MESSAGE,MESSAGE_HTML,question}{_*}{+END}">
	<h3>
		{TITLE*}
	</h3>
	<nav>
		<ul class="actions-list">
			<li>
				{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}

				<form title="{!LOAD} {TITLE*}" action="#" method="post" class="inline">
					{$INSERT_FORM_POST_SECURITY}

					<div class="inline">
						<button type="button" class="button-hyperlink js-use-warning" data-cms-tooltip="{ contents: '{$ESCAPE;^*,<h2>{EXPLANATION*}</h2>{MESSAGE_HTML},NULL_ESCAPED}', width: '700px' }">{!LOAD} {TITLE*}</button>
					</div>
				</form>
			</li>
			<li>
				{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}

				<form title="{!DELETE} {TITLE*}" action="{DELETE_URL*}" method="post" class="inline">
					{$INSERT_FORM_POST_SECURITY}

					<div class="inline">
						<input type="hidden" name="title" value="{TITLE*}" />
						<button type="button" class="button-hyperlink js-delete-warning">{!DELETE} {TITLE*}</button>
					</div>
				</form>
			</li>
		</ul>
	</nav>
</div>
