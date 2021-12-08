{TITLE}

<div class="float_surrounder">
	<div class="left" style="padding-right: 2em">
		<h2>Tags</h2>

		<ul>
			{+START,LOOP,TAGS}
				<li>
					<a href="{$PAGE_LINK*,_SEARCH:tutorials:{_loop_var}}"{+START,IF,{$EQ,{$_GET,type},{_loop_var}}} class="active"{+END}>{_loop_var*}</a>
				</li>
			{+END}
		</ul>
	</div>

	<div class="left" style="width: 54em">
		<h2>Tutorials</h2>

		{+START,LOOP,TUTORIALS}
			<div class="box"><div class="box_inner">
				<h3>{TITLE*}</h3>

				<a class="left spaced" href="{URL*}"><img src="{ICON*}" alt="" /></a>

				<div class="meta_details" role="note" style="width: auto">
					<dl class="meta_details_list">
						<dt class="field_name">{!RATING}:</dt> <dd>{RATING_TPL}</dd>

						{+START,IF_NON_EMPTY,{AUTHOR}}
							<dt class="field_name">{!BY}:</dt> <dd>{AUTHOR*}</dd>
						{+END}

						<dt class="field_name">{!ADDED}:</dt> <dd>{ADD_DATE*}</dd>

						{+START,IF,{$NEQ,{ADD_DATE},{EDIT_DATE}}}
							<dt class="field_name">{!EDITED}:</dt> <dd>{EDIT_DATE*}</dd>
						{+END}

						{+START,IF,{$NEQ,{MEDIA_TYPE},document}}
							<dt class="field_name">Media type:</dt> <dd>{$UCASE*,{MEDIA_TYPE},1}</dd>
						{+END}

						<dt class="field_name">Difficulty:</dt> <dd>{$UCASE*,{DIFFICULTY_LEVEL},1}</dd>

						<dt class="field_name">Tutorial type:</dt> <dd>{$?,{CORE},Core documentation,Auxillary}</dd>

						<dt class="field_name">Tags:</dt>
						<dd>
							<ul class="horizontal_meta_details" style="width: auto">
								{+START,LOOP,TAGS}
									<li><a href="{$PAGE_LINK*,_SEARCH:tutorials:{_loop_var}}">{_loop_var*}</a></li>
								{+END}
							</ul>
						</dd>
					</dl>
				</div>

				<p>{SUMMARY*}</p>
			</div></div>
		{+END}

		<h2>Need better information{+START,IF_NON_EMPTY,{TAG_SELECTED}} on {TAG_SELECTED*}{+END}?</h2>

		<p>The Composr documentation is user-driven:</p>

		<ul>
			<li>If you have found documentation problems that you'd like someone else to solve log an <a target="_blank" href="http://compo.sr/tracker/set_project.php?project_id=7" title="Report issue on tracker {!LINK_NEW_WINDOW}">issue to the tracker</a>.</li>
			<li>If you'd like to contribute a chunk of documentation to go into a tutorial, also log an <a target="_blank" href="http://compo.sr/tracker/set_project.php?project_id=7" title="Report issue on tracker {!LINK_NEW_WINDOW}">issue to the tracker</a>.</li>
			<li>If you want to contribute a new tutorial you can <a href="{$PAGE_LINK*,_SEARCH:cms_tutorials}">submit a link</a>.</li>
		</ul>
	</div>
</div>
