{$REQUIRE_JAVASCRIPT,chat}
<div class="box-wrapper" data-tpl="blockSideShoutbox" data-ajaxify="{ callUrl: '{$FACILITATE_AJAX_BLOCK_CALL;*,{BLOCK_PARAMS}}' }">
	<section class="box box---block-side-shoutbox"><div class="box-inner">
		<h3>{!SHOUTBOX}</h3>

		{MESSAGES}

		<form title="{!SHOUTBOX}" data-ajaxify-target="1" action="{URL*}" method="post">
			{$INSERT_SPAMMER_BLACKHOLE}

			<div>
				<p class="accessibility-hidden"><label for="shoutbox-message">{!MESSAGE}</label></p>
				<p><input type="text" id="shoutbox-message" name="shoutbox_message" class="form-control form-control-wide" /></p>
			</div>

			<p class="proceed-button">
				<button type="submit" class="btn btn-primary btn-scri buttons--send js-onclick-check-message-not-blank">{+START,INCLUDE,ICON}NAME=buttons/send{+END} {!SEND_MESSAGE}</button>
			</p>
		</form>
	</div></section>
</div>
