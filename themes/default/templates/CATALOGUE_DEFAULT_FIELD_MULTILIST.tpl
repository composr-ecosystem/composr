{+START,IF_NON_EMPTY,{ALL}}
	<div class="field-multilist">
		{+START,LOOP,ALL}<p>
			{+START,IF,{SHOW_UNSET_VALUES}}
				{+START,IF,{HAS}}
					<span class="multilist-mark yes" title="{!YES}">&#10003;</span> {$,Checkmark entity}
				{+END}
				{+START,IF,{$NOT,{HAS}}}
					<span class="multilist-mark no" title="{!NO}">&#10007;</span> {$,Cross entity}
				{+END}
			{+END}

			{OPTION*}

			{+START,IF_PASSED_AND_TRUE,IS_OTHER}
				<span class="associated-details">({!fields:ADDITIONAL_CUSTOM})</span>
			{+END}
		</p>{+END}
	</div>
{+END}
