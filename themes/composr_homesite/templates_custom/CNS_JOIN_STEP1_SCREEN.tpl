{TITLE}

<p>
	Before you may join you must acknowledge our anti-spam measures and agree to our rules. Read through, then at the bottom sign off and &amp; proceed.
</p>

<div class="box box___cns_join_step1_screen"><div class="box_inner">
	<h2>Anti-spam measures</h2>

	<p>We have a number of very effective measures in place to eliminate spam. If you are only considering joining compo.sr to post spam links, <strong>you are wasting your time</strong>; consider how costly and ineffective it will be (for you, not for us):</p>
	<ul>
		<li>We block links in member profiles until members make a legitimate forum post, so stealth outbound links will not be effective</li>
		<li>Any link you post will be attributed with <kbd>rel=nofollow</kbd>, so will not help SEO at all</li>
		<li>Banned spammer accounts are reported to StopForumSpam, which will ban your IP address across many other online forums</li>
		<li>We will contact the owner of any site to which spammers have been linking, to warn them what disreputable companies have been doing in their name</li>
		<li>Spam posts are always deleted quickly, and the responsible accounts banned, so very few people will ever see them</li>
		<li>Suspicious new accounts are automatically shadow-banned from the forums with high accuracy, until manually approved</li>
		<li>Suspicious new accounts are automatically locked out of putting anything in their profile, so stealth advertising will not be effective</li>
		<li>You'll still need to solve CAPTCHAs for a while after joining, and bots will be effectively detected and publicly reported in multiple ways (Project Honey Pot, blackholes, ...)</li>
		<li>Most of the above processes happen automatically by our website software and thus require effectively no human time on our part; that's the true versatility of Composr CMS!</li>
	</ul>
	<p>It's very clear to us who is and is not a spammer, so don't worry at all if you are not. If you are a spammer and this site is on a list of high-profile sites you're paid to spam, get that list adjusted as it is working against you.</p>
</div></div>

<div class="box box___cns_join_step1_screen"><div class="box_inner">
	<h2>Rules</h2>

	<div class="cns_join_rules">
		{RULES}
	</div>
</div></div>

<form title="{!PRIMARY_PAGE_FORM}" class="cns_join_1" method="post" action="{URL*}" autocomplete="off">
	{$INSERT_SPAMMER_BLACKHOLE}

	<p>
		<input type="checkbox" id="anti_spam" name="anti_spam" value="1" onclick="document.getElementById('proceed_button').disabled=!document.getElementById('anti_spam').checked || !document.getElementById('confirm').checked;" /><label for="anti_spam">I acknowledge the anti-spam measures</label><br />
		<input type="checkbox" id="confirm" name="confirm" value="1" onclick="document.getElementById('proceed_button').disabled=!document.getElementById('anti_spam').checked || !document.getElementById('confirm').checked;" /><label for="confirm">{!I_AGREE}</label>
	</p>

	{+START,IF_NON_EMPTY,{GROUP_SELECT}}
		<p>
			<label for="primary_group">{!CHOOSE_JOIN_USERGROUP}
				<select id="primary_group" name="primary_group">
					{GROUP_SELECT}
				</select>
			</label>
		</p>
	{+END}

	<p>
		{+START,IF,{$JS_ON}}
			<button onclick="disable_button_just_clicked(this); window.top.location='{$PAGE_LINK;*,:}'; return false;" class="button_screen buttons__no">{!I_DISAGREE}</button>
		{+END}

		<input accesskey="u" onclick="disable_button_just_clicked(this);" class="button_screen buttons__yes" type="submit" value="{!PROCEED}"{+START,IF,{$JS_ON}} disabled="disabled"{+END} id="proceed_button" />
	</p>
</form>

