{+START,IF,{$TAPATALK}}
	<blockquote>
		<h4>{+START,IF,{SAIDLESS}}{BY*}{+END}{+START,IF,{$NOT,{SAIDLESS}}}{!SAID,{BY*}}{+END}</h4>

		{$PREG_REPLACE,<blockquote.*>.*</blockquote>,,{CONTENT},s}
	</blockquote>
{+END}

{+START,IF,{$NOT,{$TAPATALK}}}
	<blockquote class="comcode-quote{+START,IF_PASSED_AND_TRUE,CITE_BROKEN} broken-link{+END}"{+START,IF_PASSED,CITE} cite="{CITE*}"{+END}>
		<h4>{+START,IF,{SAIDLESS}}{BY*}{+END}{+START,IF,{$NOT,{SAIDLESS}}}{!SAID,{BY*}}{+END}</h4>

		<div class="comcode-quote-inner comcode-quote-inner-titled">
			{+START,IF,{$MOBILE}}
				{$PREG_REPLACE,<blockquote.*>.*</blockquote>,,{CONTENT},s}
			{+END}
			{+START,IF,{$NOT,{$MOBILE}}}
				{CONTENT}
			{+END}
		</div>
	</blockquote>
{+END}
