<section class="box box---simple-preview-box"><div class="box-inner">
	{+START,IF_NON_EMPTY,{META_TITLE}}
		<h3>
			{META_TITLE`}
		</h3>
	{+END}

	{+START,IF_NON_EMPTY,{IMAGE_URL}}
		<div class="left float-separation"><a href="{URL*}"><img alt="" src="{$THUMBNAIL*,{IMAGE_URL},{WIDTH}}" /></a></div>
	{+END}

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<div class="clearfix">
			{$PARAGRAPH,{$TRUNCATE_LEFT,{DESCRIPTION`},600,0,1}}
		</div>
	{+END}

	<p class="shunted-button">
		<a class="btn btn-primary btn-scri buttons--more" href="{URL*}"{+START,IF_PASSED,REL} rel="{REL*}"{+END}><span>{+START,INCLUDE,ICON}NAME=buttons/more{+END} {!VIEW}</span></a>
	</p>
</div></section>
