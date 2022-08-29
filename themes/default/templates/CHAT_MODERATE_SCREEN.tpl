{$REQUIRE_JAVASCRIPT,chat}

<div data-tpl="chatModerateScreen">
	{TITLE}

	{+START,IF_NON_EMPTY,{INTRODUCTION}}<p>{INTRODUCTION}</p>{+END}

	{CONTENT}

	{+START,IF_PASSED,URL}
		<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
			<p class="proceed-button">
				<button class="btn btn-danger btn-scr js-click-btn-delete-marked-posts" type="submit">{+START,INCLUDE,ICON}NAME=admin/delete3{+END} <span>{!DELETE}</span></button>
			</p>
		</form>
	{+END}

	{+START,IF_NON_EMPTY,{LINKS}}
		<hr class="spaced-rule" />

		<p class="lonely-label">{!ACTIONS}:</p>
		<nav>
			<ul class="actions-list">
				{+START,LOOP,LINKS}
					<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} {_loop_var}</li>
				{+END}
			</ul>
		</nav>
	{+END}
</div>
