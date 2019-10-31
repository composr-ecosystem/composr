{$REQUIRE_JAVASCRIPT,core_adminzone_dashboard}

<div class="checklist-row">
	<div class="clearfix">
		{+START,IF_PASSED,INFO}{+START,IF_NON_EMPTY,{INFO}}
			<p class="checklist-task-status">{INFO*}
				{+START,IF_PASSED,CONFIG_URL}
					<a href="{CONFIG_URL*}" title="{!CHANGE_REGULARITY}">{+START,INCLUDE,ICON}
						NAME=checklist/toggle2
						ICON_SIZE=14
					{+END}</a>
				{+END}
				{+START,IF_NON_PASSED,CONFIG_URL}
					<img alt="" width="14" height="14" src="{$IMG*,blank}" />
				{+END}
			</p>
		{+END}{+END}

		<p class="checklist-task">
			{STATUS}

			{+START,IF_NON_EMPTY,{URL}}
				<a href="{URL*}">{TASK}</a>
			{+END}
			{+START,IF_EMPTY,{URL}}
				<span>{TASK}</span>
			{+END}
		</p>
	</div>
</div>
