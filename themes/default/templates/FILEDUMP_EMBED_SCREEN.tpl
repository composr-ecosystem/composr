<div data-tpl="filedumpEmbedScreen" data-tpl-params="{+START,PARAMS_JSON,GENERATED}{_*}{+END}">
	{TITLE}

	{+START,IF_PASSED,GENERATED}
		<div class="filedump-generated">
			{+START,IF_PASSED,RENDERED}
				<div class="filedump-generated-preview">
					<span class="lonely-label">{!PREVIEW}:</span>
					{RENDERED}
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor nisi non turpis tincidunt vestibulum. Praesent vitae mollis elit. Vestibulum luctus velit urna, et congue justo dapibus id. Nullam in eros ac libero rutrum accumsan in ac mauris.</p>
					<p>Vestibulum orci sem, dictum ut turpis ut, congue varius lorem. Nam eleifend sollicitudin vestibulum. Aliquam erat volutpat. Ut fermentum sodales risus, at ullamcorper sem egestas sed. In interdum lobortis est. Sed rutrum, ligula et luctus scelerisque, libero magna malesuada enim, quis semper lorem eros vel eros.</p>
				</div>
			{+END}

			<div class="filedump-generated-comcode">
				<label for="generated_comcode" class="lonely-label">{!_COMCODE}:</label>
				<form action="#" method="post">
					<div>
						<textarea id="generated_comcode" name="generated_comcode" class="form-control" cols="50" rows="10">{GENERATED*}</textarea>
					</div>
				</form>
			</div>
		</div>
	{+END}

	{+START,IF_NON_PASSED,GENERATED}
		<p>
			{!FILEDUMP_EXISTING_COUNT,{$INTEGER_FORMAT*,{EXISTING_COUNT},0}}
		</p>

		<div class="filedump-image-sizes">
			<p class="lonely-label">
				{!FILEDUMP_IMAGE_URLS}:
			</p>
			<ul class="compact-list image-sizes">
				{+START,LOOP,IMAGE_SIZES}
					<li>
						<label for="img_size_{SIZE_WIDTH*}">{LABEL*}</label>
						<input class="form-control js-click-input-img-size-select" type="text" size="32" id="img_size_{SIZE_WIDTH*}" name="img_size_{SIZE_WIDTH*}" value="{SIZE_URL*}" />
						<span class="associated-details">(<a rel="lightbox" href="{SIZE_URL*}">{!PREVIEW}</a>)</span>
					</li>
				{+END}
			</ul>

			<h2>{!_COMCODE}</h2>
		</div>
	{+END}

	{FORM}
</div>
