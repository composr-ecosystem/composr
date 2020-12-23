{$REQUIRE_CSS,composr_homesite__community_sites}
{$REQUIRE_JAVASCRIPT,composr_homesite__community_sites}

<a id="entry_{ID*}"></a>

<div class="box"><div class="box_inner">
	<h2><a title="{FIELD_0_PLAIN*} {!LINK_NEW_WINDOW}" target="_blank" href="{FIELD_3_PLAIN*}">{FIELD_0}</a></h2>

	<div class="float_surrounder">
		{+START,IF_NON_EMPTY,{FIELD_4_PLAIN}}
			<a title="{FIELD_0_PLAIN*} {!LINK_NEW_WINDOW}" target="_blank" href="{FIELD_1_PLAIN*}"><img class="right" src="{$THUMBNAIL*,{FIELD_4_PLAIN},160,,,,width}" alt="Logo for {FIELD_0_PLAIN*}" style="padding-left: 10px" /></a>
		{+END}

		<div style="margin-right: 170px">
			{$PARAGRAPH,{FIELD_2}}{$,Description}

			{$PARAGRAPH,{FIELD_3}}{$,Features}
		</div>
	</div>

	{+START,IF_NON_PASSED_OR_FALSE,ENTRY_SCREEN}
		<div class="float_surrounder" style="margin-top: 1em">
			<div class="right">
				<a title="{FIELD_0_PLAIN*} {!LINK_NEW_WINDOW}" target="_blank" class="buttons__more button_screen_item" href="{FIELD_1_PLAIN*}"><span>Visit website</span></a>
			</div>
			{+START,IF_NON_EMPTY,{FIELD_5_PLAIN}}
				<div class="right">
					<a title="Webmaster of {FIELD_0_PLAIN*} {!LINK_NEW_WINDOW}" target="_blank" class="author_button button_screen_item" href="{$PAGE_LINK*,site:authors:browse:{FIELD_5_PLAIN}}"><span>By {FIELD_5_PLAIN*}</span></a>
				</div>
			{+END}
			{+START,IF_EMPTY,{VIEW_URL}}{+START,IF_NON_EMPTY,{EDIT_URL}}
				<div class="right">
					<a class="buttons__edit button_screen_item" href="{EDIT_URL*}" title="Edit {FIELD_0_PLAIN*}"><span>Edit</span></a>
				</div>
			{+END}{+END}
			<div class="right">
				<a class="buttons__report button_screen_item" href="{$PAGE_LINK*,site:contact:report_community_site:site_name={FIELD_0_PLAIN}}" title="Report {FIELD_0_PLAIN*}"><span>Report</span></a>
			</div>

			{+START,IF,{$HAS_PRIVILEGE,rate}}{+START,IF,{$OR,{$IS_GUEST},{$NEQ,{SUBMITTER},{$MEMBER}}}}
				<div class="left">
					<form action="{$SELF_URL*}#entry_{ID*}" method="post" style="margin-right: 5px" onsubmit="return rate_community_site({ID%},'{$SELF_URL;*}','{FIELD_0_PLAIN;*}',10,this.parentNode);" class="inline">
						{$INSERT_SPAMMER_BLACKHOLE}
						<input type="hidden" name="rating__catalogues__community_sites____{ID*}" value="5" />
						<input type="image" src="{$IMG*,composr_homesite/vote-up}" title="Vote up" />
					</form>
					<form action="{$SELF_URL*}#entry_{ID*}" method="post" onsubmit="return rate_community_site({ID%},'{$SELF_URL;*}','{FIELD_0_PLAIN;*}',1,this.parentNode);" class="inline">
						{$INSERT_SPAMMER_BLACKHOLE}
						<input type="hidden" name="rating__catalogues__community_sites____{ID*}" value="1" />
						<input type="image" src="{$IMG*,composr_homesite/vote-down}" title="Vote down" />
					</form>
				</div>
			{+END}{+END}
		</div>
	{+END}
</div></div>
