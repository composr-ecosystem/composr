<div data-tpl="fatalScreen">
	{TITLE}

	<p role="alert">
		{TEXT*}
	</p>

	{+START,IF,{$CURRENT_FATALISTIC}}
		<div class="box box---fatal-screen"><div class="box-inner">
			{!MAYBE_NOT_FATAL}
		</div></div>
	{+END}

	{+START,IF_PASSED,WEBSERVICE_RESULT}
		<h2>Expanded advice</h2>

		{WEBSERVICE_RESULT}
	{+END}

	{+START,IF,{MAY_SEE_TRACE}}
		<h2>{!STACK_TRACE}</h2>

		{TRACE}
	{+END}
	{+START,IF,{$NOT,{MAY_SEE_TRACE}}}
		<p>
			{!STACK_TRACE_DENIED_ERROR_NOTIFICATION}
		</p>
	{+END}

	{+START,IF,{$NOR,{$CURRENT_FATALISTIC},{$EQ,{$PAGE},admin_config}}}
		{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_config}}
			<hr class="spaced-rule" />

			<ul class="actions-list">
				<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,adminzone:admin_config:category:SITE#group_ERROR_HANDLING}">{!CHANGE_ERROR_HANDLING_SETTINGS}</a></li>
			</ul>
		{+END}
	{+END}
</div>
