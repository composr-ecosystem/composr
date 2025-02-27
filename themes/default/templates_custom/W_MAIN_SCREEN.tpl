{$REQUIRE_JAVASCRIPT,buildr}

<div data-tpl="wMainScreen">
	{TITLE}

	<section class="box box---realm-main clearfix"><div class="box-inner">
		<h2>&ldquo;{REALM_NAME*}&rdquo;, &lsquo;{ROOM_NAME*}&rsquo;, <kbd>{REALM*}:{X*}:{Y*}</kbd></h2>

		<div class="buildr-navigation">
			<p class="accessibility-hidden">
				{!W_MOVEMENT_TABLE}
			</p>

			<table class="results-table columned-table">
				<thead>
					<tr>
						<th colspan="3" style="text-align: center">
							{!NAVIGATION}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							&nbsp;
						</td>
						<td>
							<div class="head">
								{+START,IF,{HAS_UP_ROOM}}
									<div>
										&uarr;<br />
										<form target="_self" class="inline" method="post" action="{$PAGE_LINK*,_SELF:_SELF}">{$INSERT_FORM_POST_SECURITY}<input type="hidden" name="type" value="up" /><button class="button-hyperlink" type="submit">{UP_ROOM*}</button></form>
									</div>
								{+END}
								{+START,IF,{$NOT,{HAS_UP_ROOM}}}
									<div class="buildr-fadedtext">
										&uarr;<br />
										<em>{!W_NO_ROOM}</em>
									</div>
								{+END}
							</div>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td>
							{+START,IF,{HAS_LEFT_ROOM}}
								<div style="text-align: right">
									&larr;&nbsp;<form target="_self" class="inline" method="post" action="{$PAGE_LINK*,_SELF:_SELF}">{$INSERT_FORM_POST_SECURITY}<input type="hidden" name="type" value="left" /><button class="button-hyperlink" type="submit">{LEFT_ROOM*}</button></form>
								</div>
							{+END}
							{+START,IF,{$NOT,{HAS_LEFT_ROOM}}}
								<div style="text-align: right" class="buildr-fadedtext">
									&larr;&nbsp;<em>{!W_NO_ROOM}</em>
								</div>
							{+END}
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							{+START,IF,{HAS_RIGHT_ROOM}}
								<div style="text-align: left">
									&nbsp;<form target="_self" class="inline" method="post" action="{$PAGE_LINK*,_SELF:_SELF}">{$INSERT_FORM_POST_SECURITY}<input type="hidden" name="type" value="right" /><button class="button-hyperlink" type="submit">{RIGHT_ROOM*}</button></form>&nbsp;&rarr;
								</div>
							{+END}
							{+START,IF,{$NOT,{HAS_RIGHT_ROOM}}}
								<div style="text-align: left" class="buildr-fadedtext">
									&nbsp;<em>{!W_NO_ROOM}</em>&nbsp;&rarr;
								</div>
							{+END}
						</td>
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
						<td>
							<div style="text-align: center">
								{+START,IF,{HAS_DOWN_ROOM}}
									<div>
										<form target="_self" class="inline" method="post" action="{$PAGE_LINK*,_SELF:_SELF}">{$INSERT_FORM_POST_SECURITY}<input type="hidden" name="type" value="down" /><button class="button-hyperlink" type="submit">{DOWN_ROOM*}</button></form><br />
										<div class="buildr-arrow">
											&darr;
										</div>
									</div>
								{+END}
								{+START,IF,{$NOT,{HAS_DOWN_ROOM}}}
									<div class="buildr-fadedtext">
										<em>{!W_NO_ROOM}</em><br />
										<div class="buildr-arrow">
											&darr;
										</div>
									</div>
								{+END}
							</div>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="buildr-room-info">
			<p>
				<em>{ROOM_TEXT}</em>
			</p>
			{+START,IF_NON_EMPTY,{PIC_URL}}
				<img alt="{!W_ROOM_PICTURE}" style="max-width: 430px" src="{PIC_URL*}" />
			{+END}

			{+START,IF_NON_EMPTY,{PORTALS}}
				<h3>{!W_PORTALS}</h3>

				{PORTALS}
			{+END}
		</div>
	</div></section>

	<br />

	<section class="box box---room-info"><div class="box-inner">
		<h3>{!W_ROOM_INFORMATION}</h3>

		<div class="clearfix">
			<div class="buildr-room-chat results-table">
				<h4 class="buildr-posttop">{!MESSAGES}</h4>
				<div>
					<iframe {$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"} title="{!MESSAGES}" src="{$FIND_SCRIPT*,wmessages}{$KEEP*,1}">{!MESSAGES}</iframe> {EMOTICON_CHOOSER}
				</div>
				<form method="post" id="posting-form" action="{$PAGE_LINK*,_SELF:_SELF}">
					{$INSERT_FORM_POST_SECURITY}

					<div><input id="post" class="form-control" name="post" type="text" /> <input name="type" type="hidden" value="message" /> <select name="tmember" size="1">
						<option value="0">
							{!W_EVERYONE}
						</option>
					</select> <button class="btn btn-primary btn-scri buttons--send" type="submit">{+START,INCLUDE,ICON}NAME=buttons/send{+END} <span>{!SEND_MESSAGE}</span></button></div>
				</form>
			</div>

			<div class="buildr-in-room">
				<h4>{!W_MEMBERS_IN_ROOM}</h4>

				<table class="columned-table wide-table results-table autosized-table responsive-table">
					<thead>
						<tr>
							<th>
								{!NAME}
							</th>
							<th>
								{!W_HEALTH}
							</th>
							<th>
								{!W_INVENTORY}
							</th>
						</tr>
					</thead>
					{MEMBERS}
				</table>
			</div>
		</div>

		{+START,IF_NON_EMPTY,{ITEMS}{ITEMS_SALE}}
			<div class="clearfix">
				{+START,IF_NON_EMPTY,{ITEMS}}
					<h4>{!W_ITEMS_IN_ROOM}</h4>

					<table class="columned-table buildr-centered-contents wide-table results-table responsive-table">
						<thead>
							<tr>
								<th>
									{!NAME}
								</th>
								<th>
									{!OPTIONS}
								</th>
							</tr>
						</thead>
						{ITEMS}
					</table>
				{+END}

				{+START,IF_NON_EMPTY,{ITEMS_SALE}}
					<br />

					<table class="columned-table buildr-centered-contents wide-table results-table responsive-table">
						<thead>
							<tr>
								<th colspan="3" class="buildr-posttop">
									{!W_ITEMS_FOR_SALE_IN_ROOM}
								</th>
							</tr>
							<tr>
								<th>
									{!NAME}
								</th>
								<th>
									{!COST}
								</th>
								<th>
									{!OPTIONS}
								</th>
							</tr>
						</thead>
						{ITEMS_SALE}
					</table>
				{+END}
			</div>
		{+END}
	</div></section>

	<br />

	<div class="buildr-centered-contents">
		<section class="box"><div class="box-inner buildr-icons-wrap">
			<h3>{!W_TOOLS}</h3>

			<div class="buildr-icon">
				<a title="{!W_INVENTORY} {!LINK_NEW_WINDOW}" target="_blank" href="{$PAGE_LINK*,_SELF:_SELF:inventory}"><img alt="{!W_INVENTORY}" width="68" height="68" src="{$IMG*,buildr/inventory}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:inventory}">{!W_INVENTORY}</a>
			</div>
			<div class="buildr-icon">
				<a title="{!W_MAP} {!LINK_NEW_WINDOW}" target="_blank" href="{$FIND_SCRIPT*,map}{$KEEP*,1,1}"><img alt="{!W_MAP}" width="68" height="68" src="{$IMG*,buildr/map}" /></a><br /><a href="{$FIND_SCRIPT*,map}{$KEEP*,1,1}">{!W_MAP}</a>
			</div>
			<div class="buildr-icon">
				<a href="{$PAGE_LINK*,_SELF:_SELF:realms}"><img alt="{!W_REALMS}" width="68" height="68" src="{$IMG*,buildr/realms}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:realms}">{!W_REALMS}</a>
			</div>
			<div class="buildr-icon">
				<a href="{$PAGE_LINK*,_SELF:_SELF}"><img alt="{!W_REFRESH}" width="68" height="68" src="{$IMG*,buildr/refresh}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF}">{!W_REFRESH}</a>
			</div>
			<div class="buildr-icon">
				<a href="{$PAGE_LINK*,_SELF:_SELF:confirm:btype=emergency}"><img alt="{!W_TELEPORT}" width="68" height="68" src="{$IMG*,buildr/emergencyteleport}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:emergency}">{!W_TELEPORT}</a>
			</div>
			<div class="buildr-icon">
				<a title="{!RULES} {!LINK_NEW_WINDOW}" target="_blank" href="{$PAGE_LINK*,_SELF:rules}"><img alt="{!RULES}" width="68" height="68" src="{$IMG*,buildr/rules}" /></a><br /><a href="{$PAGE_LINK*,_SELF:rules}">{!RULES}</a>
			</div>
			<div class="buildr-icon">
				<a title="{!HELP} {!LINK_NEW_WINDOW}" target="_blank" href="{$PAGE_LINK*,_SELF:docs}"><img alt="{!HELP}" width="68" height="68" src="{$IMG*,buildr/help}" /></a><br /><a href="{$PAGE_LINK*,_SELF:docs}">{!HELP}</a>
			</div>
		</div></section>

		<div class="box box---w-main-screen" data-toggleable-tray="{}">
			<div class="box-inner">
				<h2 class="toggleable-tray-title">
					<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!W_ENVIRONMENT_ACTIONS}</a>
					<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
						NAME=trays/expand
						ICON_SIZE=24
					{+END}</a>
				</h2>

				<div class="toggleable-tray js-tray-content" style="{HIDE_ACTIONS*}">
					<table class="columned-table results-table wide-table autosized-table">
						<colgroup>
							<col style="width: 20%" />
							<col style="width: 20%" />
							<col style="width: 20%" />
							<col style="width: 20%" />
							<col style="width: 20%" />
						</colgroup>
						<thead>
						<tr class="buildr-posttop">
							<th>
								{!ACTION}
							</th>
							<th>
								{!W_TARGET_PERSON}
							</th>
							<th>
								{!W_ITEM_INVOLVED}
							</th>
							<th>
								{!W_PARAMETER}
							</th>
							<th>
								{!PROCEED}
							</th>
						</tr>
						</thead>
						{+START,IF_NON_EMPTY,{ITEMS_HELD}}
							<tr>
								<td>
									{!W_DROP}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{ITEMS_HELD}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="drop" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_GIVE}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{ITEMS_HELD}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="give" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_PICKPOCKET}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="btype" value="pickpocket" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_USE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{ITEMS_HELD}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="use" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_DELETE_MATCHING_MESSAGES}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									&nbsp;
								</td>
								<td>
									<input type="text" size="20" name="param" class="form-control" />
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="btype" value="delete-message-by-person" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>
						{+END}

						<tr>
							<td>
								{!W_FIND_PERSON}
							</td>
							<td>
								{PEOPLE}
							</td>
							<td>
								{!NA}
							</td>
							<td>
								<input type="text" size="20" name="param" class="form-control" />
							</td>
							<td>
								<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
									{$INSERT_FORM_POST_SECURITY}
									<input type="hidden" name="type" value="findperson" />
									<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
								</form>
							</td>
						</tr>

						{+START,IF,{IS_STAFF}}
							<tr>
								<td>
									{!W_TELEPORT_PERSON}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<input type="text" size="20" name="param" class="form-control" />
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="teleport-person" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_IMPRISON_PERSON}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="imprison-person" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_HURT_PERSON}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="hurt-person" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_HEAL_PERSON}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="dehurt-person" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_BAN_PERSON}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="ban-person" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_UNBAN_PERSON}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="unban-person" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>

							<tr>
								<td>
									{!W_TAKE_FROM_PERSON}
								</td>
								<td>
									{PEOPLE}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									{!NA}
								</td>
								<td>
									<form method="post" class="inline" action="{$PAGE_LINK*,_SELF:_SELF}">
										{$INSERT_FORM_POST_SECURITY}
										<input type="hidden" name="type" value="take-from-person" />
										<button class="btn btn-primary btn-scri buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
									</form>
								</td>
							</tr>
						{+END}
					</table>
				</div>
			</div>
		</div>

		{+START,IF,{MAY_DO_STUFF}}
			<div class="box box---w-main-screen" data-toggleable-tray="{}">
				<div class="box-inner">
					<h2 class="toggleable-tray-title">
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!W_ENVIRONMENT_ADDITIONS}</a>
						<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
							NAME=trays/expand
							ICON_SIZE=24
						{+END}</a>
					</h2>

					<div class="toggleable-tray js-tray-content" style="{HIDE_ADDITIONS*}">
						<div class="clearfix buildr-icons-wrap">
							<div class="buildr-icon" style="width: {$?,{MAY_ADD_PORTAL},20%,25%};">
								<a href="{$PAGE_LINK*,_SELF:_SELF:addrealm}"><img alt="{!W_ADD_REALM}" width="68" height="68" src="{$IMG*,buildr/realms}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:addrealm}">{!W_ADD_REALM}</a>
							</div>
							<div class="buildr-icon" style="width: {$?,{MAY_ADD_PORTAL},20%,25%};">
								<a href="{$PAGE_LINK*,_SELF:_SELF:addroom}"><img alt="{!W_ADD_ADJOINING_ROOM}" width="68" height="68" src="{$IMG*,buildr/addroom}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:addroom}">{!W_ADD_ADJOINING_ROOM}</a>
							</div>
							{+START,IF,{MAY_ADD_PORTAL}}
								<div class="buildr-icon" style="width: {$?,{MAY_ADD_PORTAL},20%,25%};">
									<a href="{$PAGE_LINK*,_SELF:_SELF:addportal}"><img alt="{!W_ADD_PORTAL}" width="68" height="68" src="{$IMG*,buildr/addportal}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:addportal}">{!W_ADD_PORTAL}</a>
								</div>
							{+END}
							<div class="buildr-icon" style="width: {$?,{MAY_ADD_PORTAL},20%,25%};">
								<a href="{$PAGE_LINK*,_SELF:_SELF:additem}"><img alt="{!W_ADD_ITEM}" width="68" height="68" src="{$IMG*,buildr/additem}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:additem}">{!W_ADD_ITEM}</a>
							</div>
							<div class="buildr-icon" style="width: {$?,{MAY_ADD_PORTAL},20%,25%};">
								<a href="{$PAGE_LINK*,_SELF:_SELF:additemcopy}"><img alt="{!W_ADD_ITEM_COPY}" width="68" height="68" src="{$IMG*,buildr/additemcopy}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:additemcopy}">{!W_ADD_ITEM_COPY}</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		{+END}

		<div class="box box---w-main-screen" data-toggleable-tray="{}">
			<div class="box-inner">
				<h2 class="toggleable-tray-title">
					<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!W_ENVIRONMENT_MODIFICATIONS}</a>
					<a class="js-click-set-hidemod-cookie toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
						NAME=trays/expand
						ICON_SIZE=24
					{+END}</a>
				</h2>

				<div class="toggleable-tray js-tray-content" style="{HIDE_MODIFICATIONS*}">
					{+START,IF_NON_EMPTY,{ITEMS_OWNED}}
						<form method="post" action="{$PAGE_LINK*,_SELF:_SELF}"><div>
							{$INSERT_FORM_POST_SECURITY}

							<label for="item">{!W_ITEMS}</label>: {ITEMS_OWNED} <input type="hidden" name="type" value="edititem" />
							<button class="btn btn-primary btn-scri js-click-set-type-edititem admin--edit" type="submit">{+START,INCLUDE,ICON}NAME=admin/edit{+END} <span>{!W_EDIT_ITEM}</span></button>
							<button class="btn btn-danger btn-scri js-click-set-type-confirm" type="submit">{!W_DELETE_ITEM}</button>
						</div></form>

						{+START,IF,{IS_STAFF}}
							<form method="post" action="{$PAGE_LINK*,_SELF:_SELF}"><div>
								{$INSERT_FORM_POST_SECURITY}

								<label for="item">{!FROM}</label>: {ITEMS_OWNED} <label for="item2">{!TO}</label>: {ITEMS_OWNED_2} <input type="hidden" name="type" value="mergeitems" /> <button class="btn btn-primary btn-scri admin--merge" type="submit">{!W_MERGE_ITEMS}</button>
							</div></form>
						{+END}
					{+END}

					{+START,IF,{$OR,{$IS_NON_EMPTY,{IS_ROOM_OWNER}},{$IS_NON_EMPTY,{IS_REALM_OWNER}}}}
						<br />
						<div class="clearfix buildr-icons-wrap">
							{+START,IF,{IS_ROOM_OWNER}}
								<div class="buildr-icon" style="width: {$?,{IS_REALM_OWNER},25%,50%};">
									<a href="{$PAGE_LINK*,_SELF:_SELF:editroom}"><img alt="{!W_EDIT_ROOM}" width="68" height="68" src="{$IMG*,buildr/addroom}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:editroom}">{!W_EDIT_ROOM}</a>
								</div>
								<div class="buildr-icon" style="width: {$?,{IS_REALM_OWNER},25%,50%};">
									<a href="{$PAGE_LINK*,_SELF:_SELF:confirm:btype=deleteroom}"><img alt="{!W_DELETE_ROOM}" width="68" height="68" src="{$IMG*,buildr/delroom}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:deleteroom}">{!W_DELETE_ROOM}</a>
								</div>
							{+END}
							{+START,IF,{IS_REALM_OWNER}}
								<div class="buildr-icon" style="width: {$?,{IS_ROOM_OWNER},25%,50%};">
									<a href="{$PAGE_LINK*,_SELF:_SELF:editrealm}"><img alt="{!W_EDIT_REALM}" width="68" height="68" src="{$IMG*,buildr/realms}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:editrealm}">{!W_EDIT_REALM}</a>
								</div>
								<div class="buildr-icon" style="width: {$?,{IS_ROOM_OWNER},25%,50%};">
									<a href="{$PAGE_LINK*,_SELF:_SELF:confirm:btype=deleterealm}"><img alt="{!W_DELETE_REALM}" width="68" height="68" src="{$IMG*,buildr/delrealm}" /></a><br /><a href="{$PAGE_LINK*,_SELF:_SELF:deleterealm}">{!W_DELETE_REALM}</a>
								</div>
							{+END}
						</div>
					{+END}
				</div>
			</div>
		</div>
	</div>
</div>
