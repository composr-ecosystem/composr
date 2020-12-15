{$,Forum/private topic search}
{+START,IF,{$EQ,{$PAGE},forumview}}
	{+START,IF,{$EQ,{$_GET,type},pt}}
		<div class="cns-search-box">
			<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{$PAGE_LINK,_SEARCH:search:results:cns_own_pt,1}}" method="get">
				{$HIDDENS_FOR_GET_FORM,{$PAGE_LINK,_SEARCH:search:results:cns_own_pt,1}}

				<div class="vertical-alignment">
					<label class="accessibility-hidden" for="member-bar-search">{!_SEARCH_PRIVATE_TOPICS}</label>
					<input maxlength="255" type="text" name="content" id="member-bar-search" class="form-control form-control-sm" placeholder="{!_SEARCH_PRIVATE_TOPICS}" />
					<button class="btn btn-primary btn-sm buttons--search" type="submit" data-disable-on-click="1">{+START,INCLUDE,ICON}NAME=buttons/search{+END} {!SEARCH}</button>
					<a class="associated-link" href="{$PAGE_LINK*,_SEARCH:search:browse:cns_own_pt}" title="{!MORE}: {!search:SEARCH_OPTIONS}">{!MORE}</a>
				</div>
			</form>
		</div>
	{+END}
	{+START,IF,{$NEQ,{$_GET,type},pt}}
		<div class="cns-search-box">
			<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{$PAGE_LINK,_SEARCH:search:results:cns_posts:search_under={$_GET,id},1}}" method="get">
				{$HIDDENS_FOR_GET_FORM,{$PAGE_LINK,_SEARCH:search:results:cns_posts:search_under_children={$_GET,id},1}}

				<div class="vertical-alignment">
					<label class="accessibility-hidden" for="member-bar-search">{!SEARCH_FORUM_POSTS}</label>
					<input maxlength="255" type="text" name="content" id="member-bar-search" class="form-control form-control-sm" placeholder="{!SEARCH_FORUM_POSTS}" />
					<button class="btn btn-primary btn-sm buttons--search" type="submit" data-disable-on-click="1">{+START,INCLUDE,ICON}NAME=buttons/search{+END} {!SEARCH}</button>
					<a class="associated-link" href="{$PAGE_LINK*,_SEARCH:search:browse:cns_posts:search_under={$_GET,id}}" title="{!MORE}: {!search:SEARCH_OPTIONS}">{!MORE}</a>
				</div>
			</form>
		</div>
	{+END}
{+END}

{$,Topic search}
{+START,IF,{$EQ,{$PAGE},topicview}}
	<div class="cns-search-box">
		<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{$PAGE_LINK,_SEARCH:search:results:cns_within_topic:search_under={$_GET,id}}}" method="get">
			{$HIDDENS_FOR_GET_FORM,{$PAGE_LINK,_SEARCH:search:results:cns_within_topic:search_under={$_GET,id}}}

			<div class="vertical-alignment">
				<label class="accessibility-hidden" for="member-bar-search">{!SEARCH_POSTS_WITHIN_TOPIC}</label>
				<input maxlength="255" type="text" name="content" id="member-bar-search" class="form-control form-control-sm" placeholder="{!SEARCH_POSTS_WITHIN_TOPIC}" />
				<button class="btn btn-primary btn-sm buttons--search" type="submit" data-disable-on-click="1">{+START,INCLUDE,ICON}NAME=buttons/search{+END} {!SEARCH}</button>
				<a class="associated-link" href="{$PAGE_LINK*,_SEARCH:search:browse:cns_within_topic:search_under={$_GET,id}}" title="{!MORE}: {!search:SEARCH_OPTIONS}">{!MORE}</a>
			</div>
		</form>
	</div>
{+END}

{$,General search}
{+START,IF,{$NEQ,{$PAGE},forumview,topicview}}
	<div class="cns-search-box">
		<form title="{!SEARCH}" action="{$URL_FOR_GET_FORM*,{$PAGE_LINK,_SEARCH:search:results}}" method="get">
			{$HIDDENS_FOR_GET_FORM,{$PAGE_LINK,_SEARCH:search:results}}

			<div class="vertical-alignment">
				<label class="accessibility-hidden" for="member-bar-search">{!SEARCH}</label>
				<input maxlength="255" type="text" name="content" id="member-bar-search" class="form-control form-control-sm" placeholder="{!SEARCH}" />
				<button class="btn btn-primary btn-sm buttons--search" type="submit" data-disable-on-click="1">{+START,INCLUDE,ICON}NAME=buttons/search{+END} {!SEARCH}</button>
				<a class="associated-link" href="{$PAGE_LINK*,_SEARCH:search:browse}" title="{!MORE}: {!search:SEARCH_OPTIONS}">{!MORE}</a>
			</div>
		</form>
	</div>
{+END}
