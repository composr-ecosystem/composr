{$REQUIRE_JAVASCRIPT,core_addon_management}

<tr>
	<td class="addon-name">
		{+START,SET,description}
			{DESCRIPTION_PARSED}

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
		{+END}
		<p {+START,IF,{$DESKTOP}} data-cms-tooltip="{ contents: '{$TRUNCATE_LEFT;^*,{$GET,description},800,0,1}', width: '50%' }"{+END}>
			{PRETTY_NAME}
		</p>
		{+START,SET,FILE_LIST_PRE}
			<p class="lonely-label">{!FILES}:</p>
		{+END}
		{+START,SET,FILE_LIST}
			<ul>
				{+START,LOOP,FILE_LIST}
					<li>{_loop_var*}</li>
				{+END}
			</ul>
		{+END}
		{+START,IF,{$DESKTOP}}
			<div class="block-desktop">
				<p {+START,IF,{$DESKTOP}} data-cms-tooltip="{ contents: '{$TRUNCATE_LEFT;^*,{$GET,FILE_LIST},800,0,1}', width: '50%' }"{+END}>
					{+START,IF_PASSED,FILENAME}
						{FILENAME*}
					{+END}
					{+START,IF_NON_PASSED,FILENAME}
						{!HOVER_FOR_CONTENTS}
					{+END}
				</p>
			</div>
		{+END}
		{+START,IF_PASSED,FILENAME}
			<div class="block-mobile">
				{FILENAME*}
			</div>
		{+END}
	</td>
	<td>
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
