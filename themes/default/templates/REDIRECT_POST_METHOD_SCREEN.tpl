{TITLE}

{$REQUIRE_CSS,messages}

<div class="site-special-message">
	<div class="site-special-message-inner">
		<form title="{!PROCEED}" action="{URL*}" id="redir-form" method="post">
			{$INSERT_FORM_POST_SECURITY}

			<div class="box box---login-redirect-screen"><div class="box-inner">
				{TEXT}

				<p>
					{!PROCEED_TEXT,<button accesskey="c" class="button-hyperlink" type="submit">{!HERE}</button>}
				</p>

				{POST}
			</div></div>
		</form>
	</div>
</div>

{REFRESH}
