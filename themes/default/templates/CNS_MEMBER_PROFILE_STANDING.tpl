{$REQUIRE_CSS,stepper}

<div class="stepper-wrapper">
	{+START,IF,{$EQ,{SIMPLE_STEPPER},0}}
		{+START,LOOP,STEPPERS}
			<div class="stepper-item{+START,IF,{ACTIVE}} active {ACTIVE_COLOR*}{+END}">
				<div class="step-counter" data-cms-tooltip="{ contents: '{EXPLANATION;^=}'}">{+START,INCLUDE,ICON}NAME={ICON*}{+END}</div>
				<div class="step-name">{LABEL*}</div>
			</div>
		{+END}
	{+END}
	{+START,IF,{$EQ,{SIMPLE_STEPPER},1}}
		{+START,LOOP,STEPPERS}
			<div class="stepper-item simple{+START,IF,{ACTIVE}} active {ACTIVE_COLOR*}{+END}">
				<div class="step-counter" data-cms-tooltip="{ contents: '{EXPLANATION;^=}'}"> </div>
				<div class="step-name" data-cms-tooltip="{ contents: '{LABEL;^=}'}">{+START,INCLUDE,ICON}NAME={ICON*}{+END}</div>
			</div>
		{+END}
	{+END}
</div>

{+START,LOOP,INFO}
	<p><img class="icon inline-icon" src="{$IMG,icons_monochrome/{ICON*}}"></img>{TEXT*}</p>
{+END}

<hr>

{WARNINGS}
