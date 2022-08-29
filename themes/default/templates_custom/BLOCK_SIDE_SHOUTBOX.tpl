{$REQUIRE_JAVASCRIPT,chat}
{$REQUIRE_JAVASCRIPT,shoutr}
{$REQUIRE_CSS,shoutbox}

{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,chat}}
	<section class="box box---block-side-shoutbox" role="marquee" data-tpl="blockSideShoutbox" data-tpl-params="{+START,PARAMS_JSON,CHATROOM_ID,LAST_MESSAGE_ID}{_*}{+END}">
		<div class="box-inner">
			<h3>{!SHOUTBOX}</h3>

			{MESSAGES}

			<form target="_self" action="{$EXTEND_URL*,{URL},posted=1}" method="post" title="{!SHOUTBOX}">
				{$INSERT_FORM_POST_SECURITY}

				<div>
					<p class="accessibility-hidden"><label for="shoutbox-message">{!MESSAGE}</label></p>
					<p><input type="text" id="shoutbox-message" name="shoutbox_message" class="form-control form-control-wide" /></p>
				</div>

				<div class="btn-row btn-row-stretched">
					<button class="btn btn-primary btn-scri buttons--send js-click-btn-send-message" type="submit">{+START,INCLUDE,ICON}NAME=buttons/send{+END} <span>Send</span></button>
					<button class="btn btn-secondary btn-scri spare--8 js-click-btn-shake-screen" type="submit" title="Shake the screen of all active website visitors">{+START,INCLUDE,ICON}NAME=spare/8{+END} <span>Shake</span></button>
				</div>
			</form>
		</div>
	</section>
{+END}
