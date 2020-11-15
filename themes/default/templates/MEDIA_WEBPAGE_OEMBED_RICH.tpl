<section class="box box---simple-preview-box webstandards-checker-off"><div class="box-inner">
	{+START,IF_NON_EMPTY,{TITLE}}
		<h3>
			{TITLE`}
		</h3>
	{+END}

	{+START,IF_NON_EMPTY,{HTML}}
		{HTML`}
	{+END}

	<p class="shunted-button">
		<a class="btn btn-primary btn-scri buttons--more" href="{URL*}"{+START,IF_PASSED,REL} rel="{REL*}"{+END}><span>{+START,INCLUDE,ICON}NAME=buttons/more{+END} {!VIEW}</span></a>
	</p>
</div></section>
