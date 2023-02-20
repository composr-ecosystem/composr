{$REQUIRE_CSS,composr_homesite__community_sites}
{$REQUIRE_JAVASCRIPT,composr_homesite__community_sites}

<a id="entry_{ID*}"></a>

<div class="box"><div class="box_inner">
	<h2><a title="{FIELD_0_PLAIN*} {!LINK_NEW_WINDOW}" target="_blank" href="{FIELD_1_PLAIN*}">{FIELD_0}</a></h2>

	<div class="float_surrounder">
		{+START,IF_NON_EMPTY,{FIELD_4_PLAIN}}
			<a title="View full screenshot of {FIELD_0_PLAIN*}" rel="lightbox" target="_blank" href="{$BASE_URL*}/{FIELD_4_PLAIN*}"><img class="site_logo" src="{$THUMBNAIL*,{FIELD_4_PLAIN},300,,,,width}" alt="Logo for {FIELD_0_PLAIN*}" /></a>
		{+END}

		<div class="site_details">
			{$PARAGRAPH,{FIELD_2}}{$,Description}

			{$PARAGRAPH,{FIELD_3}}{$,Features}
		</div>
	</div>

	{+START,IF_NON_PASSED_OR_FALSE,ENTRY_SCREEN}
		<div class="site_icons">
			<div class="site_icon">
				<a title="{FIELD_0_PLAIN*} {!LINK_NEW_WINDOW}" target="_blank" class="buttons__more button_screen_item" href="{FIELD_1_PLAIN*}"><span>Visit website</span></a>
			</div>
			{+START,IF_NON_EMPTY,{FIELD_7_PLAIN}}
				<div class="site_icon">
					<a target="_blank" class="buttons__more button_screen_item" href="{FIELD_7_PLAIN*}"><span>{FIELD_5_PLAIN*}'s intro-topic</span></a>
				</div>
			{+END}
			{+START,IF_NON_EMPTY,{FIELD_6_PLAIN}}
				<div class="site_icon">
					<a title="Further details for {FIELD_0_PLAIN*} {!LINK_NEW_WINDOW}" target="_blank" class="buttons__more button_screen_item" href="{FIELD_6_PLAIN*}"><span>Further details</span></a>
				</div>
			{+END}
			{+START,IF_NON_EMPTY,{FIELD_5_PLAIN}}
				<div class="site_icon">
					<a title="Webmaster of {FIELD_0_PLAIN*} {!LINK_NEW_WINDOW}" target="_blank" class="author_button button_screen_item" href="{$PAGE_LINK*,site:authors:browse:{FIELD_5_PLAIN}}"><span>By {FIELD_5_PLAIN*}</span></a>
				</div>
			{+END}
			{+START,IF_EMPTY,{VIEW_URL}}{+START,IF_NON_EMPTY,{EDIT_URL}}
				<div class="site_icon">
					<a class="buttons__edit button_screen_item" href="{EDIT_URL*}" title="Edit {FIELD_0_PLAIN*}"><span>Edit</span></a>
				</div>
			{+END}{+END}
			<div class="site_icon">
				<a class="buttons__report button_screen_item" href="{$PAGE_LINK*,forum:}" title="Report {FIELD_0_PLAIN*}"><span>Report on forum</span></a>
			</div>

			{+START,IF,{$HAS_PRIVILEGE,rate}}{+START,IF,{$OR,{$IS_GUEST},{$NEQ,{SUBMITTER},{$MEMBER}}}}
				<div class="site_vote_buttons">
					<form action="{$SELF_URL*}#entry_{ID*}" method="post" onsubmit="return rate_community_site({ID%},'{$SELF_URL;*}','{FIELD_0_PLAIN;*}',10,this,this.nextSibling);" class="site_upvote_button{+START,IF,{$ALREADY_RATED,catalogues__community_sites,{ID%}}} {$?,{$EQ,{$ALREADY_RATED,catalogues__community_sites,{ID%},1},10},current_rating,not_current_rating}{+END}">
						{$INSERT_SPAMMER_BLACKHOLE}
						<input type="hidden" name="rating__catalogues__community_sites____{ID*}" value="5" />
						<input type="image" src="{$IMG*,composr_homesite/vote-up}" title="Vote up" />
					</form><form action="{$SELF_URL*}#entry_{ID*}" method="post" onsubmit="return rate_community_site({ID%},'{$SELF_URL;*}','{FIELD_0_PLAIN;*}',1,this,this.previousSibling);" class="site_downvote_button{+START,IF,{$ALREADY_RATED,catalogues__community_sites,{ID%}}} {$?,{$EQ,{$ALREADY_RATED,catalogues__community_sites,{ID%},1},1},current_rating,not_current_rating}{+END}">
						{$INSERT_SPAMMER_BLACKHOLE}
						<input type="hidden" name="rating__catalogues__community_sites____{ID*}" value="1" />
						<input type="image" src="{$IMG*,composr_homesite/vote-down}" title="Vote down" />
					</form>
					<span>&larr; Cast vote</span>
				</div>
			{+END}{+END}
		</div>
	{+END}
</div></div>
