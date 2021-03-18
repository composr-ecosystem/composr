{TITLE}

<div class="box box---setupwizard"><div class="box-inner">
	<div class="progress-indicator">
		{$SET,progress_step_display,1}{+START,WHILE,{$GT,{$ADD,{NUM_STEPS_ENUMERABLE},1},{$GET,progress_step_display}}}<img alt="" width="9" height="17" src="{$IMG*,progress_indicator/{$?,{$LT,{STEP},{$GET,progress_step_display}},stage_future,{$?,{$EQ,{STEP},{$GET,progress_step_display}},stage_present,stage_past}}}" />{$INC,progress_step_display}{+END}
	</div>

	<h2>{!SETUPWIZARD_STEP,{$INTEGER_FORMAT*,{STEP}},{$INTEGER_FORMAT*,{NUM_STEPS_ENUMERABLE}}}</h2>

	{INNER}

	{+START,IF,{$EQ,{STEP},2}}
		<div>
			<h3>{!WHAT_TO_EXPECT}</h3>

			{!SETUPWIZARD_2_DESCRIBE_EXPECTATIONS}
		</div>
	{+END}
</div></div>
