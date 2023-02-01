{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$REQUIRE_JAVASCRIPT,cns_forum}
<div data-view="CnsForumTopicWrapper">
	<table class="columned-table wide-table cns-topic-list">
		{+START,IF,{$DESKTOP}}
			<colgroup>
				{+START,IF,{$CONFIG_OPTION,is_on_topic_emoticons}}
					<col class="cns-forum-topic-wrapper-column-column1 column-desktop" />
				{+END}
				<col class="cns-forum-topic-wrapper-column-column2" />
				<col class="cns-forum-topic-wrapper-column-column3 column-desktop" />
				<col class="cns-forum-topic-wrapper-column-column4 column-desktop" />
				<col class="cns-forum-topic-wrapper-column-column5 column-desktop" />
				{+START,IF,{$OR,{$EQ,{$LANG},EN},{$LT,{$LENGTH,{!COUNT_POSTS}{!COUNT_VIEWS}},12}}}
					<col class="cns-forum-topic-wrapper-column-column6{$?,{$MATCH_KEY_MATCH,_WILD:members},-shorter}" />
				{+END}
				{+START,IF_NON_EMPTY,{MODERATOR_ACTIONS}}{+START,IF,{$NOT,{$_GET,overlay}}}
					<col class="cns-forum-topic-wrapper-column-column7 column-desktop" />
				{+END}{+END}
			</colgroup>
		{+END}

		<thead>
			<tr>
				{+START,IF,{$DESKTOP}}
					{+START,IF,{$CONFIG_OPTION,is_on_topic_emoticons}}
						<th class="cns-forum-box-left column-desktop"><span class="accessibility-hidden">{!TOPIC_EMOTICON}</span></th>
					{+END}
				{+END}
				<th>{!TITLE}</th>
				{+START,IF,{$DESKTOP}}
					<th class="column-desktop">{!STARTER}{STARTER_TITLE*}</th>
					<th class="column-desktop">{!COUNT_POSTS}</th>
					{+START,IF,{$OR,{$EQ,{$LANG},EN},{$LT,{$LENGTH,{!COUNT_POSTS}{!COUNT_VIEWS}},12}}}
						<th class="cell-desktop">{!COUNT_VIEWS}</th>
					{+END}
				{+END}
				<th {+START,IF_EMPTY,{MODERATOR_ACTIONS}} class="cns-forum-box-right"{+END}>{!LAST_POST}</th>
				{+START,IF,{$DESKTOP}}
					{+START,IF_NON_EMPTY,{MODERATOR_ACTIONS}}{+START,IF_NON_EMPTY,{MODERATOR_ACTIONS}}{+START,IF,{$NOT,{$_GET,overlay}}}
						<th class="cns-forum-box-right cell-desktop">
							<a href="#!" class="js-click-mark-all-topics" title="{!TOGGLE_SELECTION}">{+START,INCLUDE,ICON}
								NAME=cns_topic_modifiers/unvalidated
								ICON_SIZE=14
							{+END}</a>
						</th>
					{+END}{+END}{+END}
				{+END}
			</tr>
		</thead>

		<tbody>
			{TOPICS}

			<tr class="cns-table-footer">
				{+START,IF,{$DESKTOP}}
					{+START,IF,{$CONFIG_OPTION,is_on_topic_emoticons}}
						<td class="cns-column1 cns-forum-box-bleft cell-desktop"></td>
					{+END}
				{+END}
				<td class="cns-column1{+START,IF,{$MOBILE}} cns-forum-box-bleft{+END}"></td>
				<td class="cns-column1"></td>
				{+START,IF,{$DESKTOP}}
					<td class="cns-column1 cell-desktop"></td>
					{+START,IF,{$OR,{$EQ,{$LANG},EN},{$LT,{$LENGTH,{!COUNT_POSTS}{!COUNT_VIEWS}},12}}}
						<td class="cns-column1 cell-desktop"></td>
					{+END}
				{+END}
				<td class="cns-column1{+START,IF,{$OR,{$MOBILE},{$IS_EMPTY,{MODERATOR_ACTIONS}}}} cns-forum-box-bright{+END} cell-desktop"></td>
				{+START,IF,{$DESKTOP}}
					{+START,IF_NON_EMPTY,{MODERATOR_ACTIONS}}{+START,IF,{$NOT,{$_GET,overlay}}}
						<td class="cns-column1 cns-forum-box-bright cell-desktop"></td>
					{+END}{+END}
				{+END}
			</tr>
		</tbody>
	</table>

{+START,IF_NON_EMPTY,{PAGINATION}}
	<div class="clearfix pagination-spacing ajax-block-wrapper-links">
		{PAGINATION}
	</div>
{+END}

{+START,IF,{$NOT,{$WIDE_HIGH}}}
	{+START,IF_NON_EMPTY,{MODERATOR_ACTIONS}}
		{+START,IF,{$DESKTOP}}
			<div class="box cns-topic-actions block-desktop"><div class="box-inner">
				<form title="{!TOPIC_ACTIONS}" action="{$URL_FOR_GET_FORM*,{ACTION_URL}}" method="get">
					{$HIDDENS_FOR_GET_FORM,{ACTION_URL}}
					<label for="fma-type">{!TOPIC_ACTIONS}: </label>
					<select class="form-control form-control-sm dropdown-actions js-moderator-action-submit-form" name="type" id="fma-type">
						<option value="browse">-</option>
						{MODERATOR_ACTIONS}
					</select>
				</form>

				{+START,IF,{MAY_CHANGE_MAX}}
					<form title="{!PER_PAGE}" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}{+START,IF,{$EQ,{TYPE},pt}}#tab--pts{+END}" method="get">
						{$HIDDENS_FOR_GET_FORM,{$SELF_URL},forum_max}

						<label for="forum_max">{!PER_PAGE}:</label>
						<select name="forum_max" id="forum_max" class="form-control form-control-sm js-max-change-submit-form">
							<option value="10"{$?,{$EQ,{MAX},10}, selected="selected",}>10</option>
							<option value="20"{$?,{$EQ,{MAX},20}, selected="selected",}>20</option>
							<option value="30"{$?,{$EQ,{MAX},30}, selected="selected",}>30</option>
							<option value="50"{$?,{$EQ,{MAX},50}, selected="selected",}>50</option>
							<option value="100"{$?,{$EQ,{MAX},100}, selected="selected",}>100</option>
							<option value="300"{$?,{$EQ,{MAX},300}, selected="selected",}>300</option>
						</select>
					</form>

					<form title="{!PER_PAGE}" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}{+START,IF,{$EQ,{TYPE},pt}}#tab--pts{+END}" method="get">
						{$HIDDENS_FOR_GET_FORM,{$SELF_URL},max,forum_sort}

						<label for="order">{!SORT}:</label>
						<select class="form-control form-control-sm js-order-change-submit-form" name="forum_sort" id="forum_sort">
							<option value="last_post"{$?,{$EQ,{SORT},last_post}, selected="selected",}>{!FORUM_ORDER_BY_LAST_POST}</option>
							<option value="first_post"{$?,{$EQ,{SORT},first_post}, selected="selected",}>{!FORUM_ORDER_BY_FIRST_POST}</option>
							<option value="title"{$?,{$EQ,{SORT},title}, selected="selected",}>{!FORUM_ORDER_BY_TITLE}</option>
							<option value="views"{$?,{$EQ,{SORT},views}, selected="selected",}>{!COUNT_VIEWS}</option>
							<option value="posts"{$?,{$EQ,{SORT},posts}, selected="selected",}>{!COUNT_POSTS}</option>
						</select>
					</form>
				{+END}
			</div></div>
		{+END}
	{+END}
{+END}
</div>
