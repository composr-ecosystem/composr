{TITLE}

<div class="meta-details" role="note">
	<ul class="meta-details-list">
		<li>
			{!BY_SIMPLE,<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}" itemprop="author">{$USERNAME*,{SUBMITTER},1}}</a>
			{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
		</li>
		<li>{!ADDED_SIMPLE,<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{ADD_DATE_RAW}}" itemprop="datePublished">{ADD_DATE*}</time>}</li>
		{+START,IF,{$INLINE_STATS}}<li>{!VIEWS_SIMPLE,{VIEWS*}}</li>{+END}
	</ul>
</div>

<div class="poll-details">
	{POLL_DETAILS}
</div>

{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,poll,{ID}}}
{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

{+START,IF_NON_EMPTY,{EDIT_DATE_RAW}}
	<div class="edited" role="note">
		<img alt="" width="9" height="6" src="{$IMG*,edited}" />
		<span>{!EDITED}</span>
		<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{EDIT_DATE_RAW}}">{$DATE*,,,,{EDIT_DATE_RAW}}</time>
	</div>
{+END}

{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}

{$REVIEW_STATUS,poll,{ID}}

{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,INCLUDE,STAFF_ACTIONS}
	1_URL={EDIT_URL*}
	1_TITLE={!EDIT_POLL}
	1_ACCESSKEY=q
	1_REL=edit
	1_ICON=admin/edit_this
	{+START,IF,{$ADDON_INSTALLED,tickets}}
		2_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=poll:content_id={ID}:redirect={$SELF_URL&}}
		2_TITLE={!report_content:REPORT_THIS}
		2_ICON=buttons/report
		2_REL=report
	{+END}
{+END}

{+START,IF_NON_EMPTY,{RATING_DETAILS}}
	<div class="clearfix">
		<div class="ratings">
			{RATING_DETAILS}
		</div>
	</div>
{+END}

<div class="content-screen-comments">
	{COMMENT_DETAILS}
</div>

{+START,IF_NON_EMPTY,{TRACKBACK_DETAILS}}
	<div class="clearfix">
		<div class="trackbacks">
			{TRACKBACK_DETAILS}
		</div>
	</div>
{+END}
