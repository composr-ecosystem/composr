{$REQUIRE_JAVASCRIPT,core_cns}
<section data-tpl="blockMainJoinDone" class="box box---block-main-join-done"><div class="box-inner">
	<h3>{!_JOIN}</h3>

	{+START,IF_PASSED,MESSAGE}
		{MESSAGE}
	{+END}

	{+START,IF,{EMAIL_SENT}}
		<p>{!WHITEPAPER_EMAILED,{EMAIL_ADDRESS}}</p>
	{+END}

	{+START,IF,{LOGGED_IN}}
		{+START,IF,{$NOT,{EMAIL_SENT}}}
			{+START,IF,{HAS_EMAIL_TO_SEND}}
				{+START,IF_NON_EMPTY,{EMAIL_ADDRESS}}
					<form action="{$SELF_URL*}" method="post" class="js-submit-ga-track-dl-whitepaper">
						{$INSERT_SPAMMER_BLACKHOLE}
						<input type="hidden" name="_send_document" value="1" />

						<p class="proceed-button">
							<button class="btn btn-primary btn-scri buttons--send" type="submit">{+START,INCLUDE,ICON}NAME=buttons/send{+END} {!DOWNLOAD_WHITEPAPER}</button>
						</p>
					</form>
				{+END}

				{+START,IF_EMPTY,{EMAIL_ADDRESS}}
					<p>{!WHITEPAPER_NOT_EMAILED,{$PAGE_LINK*,site:members:view:redirect={$SELF_URL&}#tab--edit}}</p>
				{+END}
			{+END}

			{+START,IF,{$NOT,{HAS_EMAIL_TO_SEND}}}
				<p>{!LOGGED_IN_AS,{$USERNAME*}}</p>
			{+END}
		{+END}
	{+END}
</div></section>
