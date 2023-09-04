{$REQUIRE_JAVASCRIPT,core_addon_management}

<tr>
	<td>
		{+START,SET,description}
			{DESCRIPTION_PARSED}

			<h2>{!metadata:METADATA}</h2>

			{+START,IF_NON_EMPTY,{ORGANISATION}}
				<p>
					<span class="field-name">{!ORGANISATION}:</span>
					{ORGANISATION*}
				</p>
			{+END}
			{+START,IF_NON_EMPTY,{COPYRIGHT_ATTRIBUTION}}
				<span class="field-name">{!COPYRIGHT_ATTRIBUTION}:</span>
				<div class="whitespace-visible">{COPYRIGHT_ATTRIBUTION*}</div>
			{+END}
			{+START,IF_NON_EMPTY,{LICENCE}}
				<p>
					<span class="field-name">{!LICENCE}:</span>
					{LICENCE*}
				</p>
			{+END}
			<p>
				<span class="field-name">{!CATEGORY}:</span>
				{CATEGORY*}
			</p>

			<p class="lonely-label">{!FILES}:</p>
			<ul>
				{+START,LOOP,FILE_LIST}
					<li><kbd>{_loop_var*}</kbd></li>
				{+END}
			</ul>
		{+END}

		{+START,IF_PASSED,IMAGE_URL}
			<img width="16" height="16" src="{IMAGE_URL*}" alt="" />
		{+END}

		<span class="addon-name"{+START,IF,{$DESKTOP}} data-cms-tooltip="{ contents: '{$TRUNCATE_LEFT;^*,{$GET,description},800,0,1}', width: '50%' }"{+END} data-addon-details="{$GET*,description}">{NAME*}</span>
	</td>
	<td class="{$?,{BUNDLED},bundled,non_bundled}">
		{AUTHOR*}
	</td>
	<td>
		{VERSION*}
	</td>
	<td class="status-{COLOUR*}">
		{STATUS*}
	</td>
	<td class="column-mobile">
		{$GET,description}
	</td>
	<td class="column-mobile">
		{+START,IF,{$EQ,{TYPE},install}}
			{$GET,FILE_LIST}
		{+END}
	</td>
	<td class="results-table-field addon-actions">
		{ACTIONS}

		<label class="accessibility-hidden" for="install_{NAME*}">{!INSTALL} {NAME*}</label>
		<input title="{!INSTALL} {NAME*}" type="checkbox" name="install_{NAME*}" id="install_{NAME*}" value="{PASSTHROUGH*}"{$?,{$EQ,{TYPE},install},, disabled="disabled"}/>

		<label class="accessibility-hidden" for="uninstall_{NAME*}">{!UNINSTALL} {NAME*}</label>
		<input title="{!UNINSTALL} {NAME*}" type="checkbox" name="uninstall_{NAME*}" id="uninstall_{NAME*}" value="{PASSTHROUGH*}"{$?,{$EQ,{TYPE},uninstall},, disabled="disabled"}/>
	</td>
</tr>
