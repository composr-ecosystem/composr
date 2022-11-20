<div class="comcode-concepts" role="complementary" aria-label="{$STRIP_TAGS,{TITLE}}">
	<div class="box box---comcode-concepts"><div class="box-inner">
		{+START,IF_NON_EMPTY,{TITLE}}
			<h2>{TITLE}</h2>
		{+END}

		<dl>
			{+START,LOOP,CONCEPTS}
				<dt class="de-th comcode-concepts-title"><a id="{A*}"></a>{KEY*}</dt>
				<dd class="comcode-concepts-content">{VALUE}</dd>

				{$SET_TUTORIAL_LINK,{A},{$SELF_PAGE_LINK}#{A}}
			{+END}
		</dl>
	</div></div>
</div>
