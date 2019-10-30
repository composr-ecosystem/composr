<section class="box box---block-bottom-tag-cloud">
	<div class="box-inner">
		<h3 class="box-heading">{TITLE*}</h3>
		<div class="box-body">
			<div class="tag-buttons">
				{+START,LOOP,TAGS}
					<a rel="tag" href="{LINK*}" class="btn btn-secondary btn-tag">{TAG*}</a>
				{+END}
			</div>
		</div>
	</div>
</section>
