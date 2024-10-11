{+START,IF,{$TAPATALK}}
	<blockquote>
		{$PREG_REPLACE,<blockquote.*>.*</blockquote>,,{CONTENT},s}
	</blockquote>
{+END}

{+START,IF,{$NOT,{$TAPATALK}}}
	<blockquote class="comcode-quote{+START,IF_PASSED_AND_TRUE,CITE_BROKEN} broken-link{+END}"{+START,IF_PASSED,CITE} cite="{CITE*}"{+END}>
		<div class="comcode-quote-inner">
			<div class="clearfix">
				{+START,IF,{$MOBILE}}
					{$PREG_REPLACE,<blockquote.*>.*</blockquote>,,{CONTENT},s}
				{+END}
				{+START,IF,{$NOT,{$MOBILE}}}
					{CONTENT}
				{+END}
			</div>
		</div>
	</blockquote>
{+END}
