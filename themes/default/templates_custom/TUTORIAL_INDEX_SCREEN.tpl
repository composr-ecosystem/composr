{TITLE}

<div class="clearfix">
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

	<div class="left" style="max-width: 54em">
		<h2>Tutorials</h2>

		{+START,LOOP,TUTORIALS}
			<div class="box box---tutorials-screen-tutorial"><div class="box-inner">
				<h3><a href="{URL*}">{TITLE*}</h3>

				<a class="left spaced tutorial-thumbnail" href="{URL*}"><img width="100" height="100" src="{ICON*}" alt="" /></a>

				<div class="meta-details" role="note" style="width: auto">
					<dl class="meta-details-list">
						<dt class="field-name">{!RATING}:</dt> <dd>{RATING_TPL}</dd>

						{+START,IF_NON_EMPTY,{AUTHOR}}
							<dt class="field-name">{!BY}:</dt> <dd>{AUTHOR*}</dd>
						{+END}

						<dt class="field-name">{!ADDED}:</dt> <dd>{ADD_DATE*}</dd>

						{+START,IF,{$NEQ,{ADD_DATE},{EDIT_DATE}}}
							<dt class="field-name">{!EDITED}:</dt> <dd>{EDIT_DATE*}</dd>
						{+END}

						{+START,IF,{$NEQ,{MEDIA_TYPE},document}}
							<dt class="field-name">Media type:</dt> <dd>{$UCASE*,{MEDIA_TYPE},1}</dd>
						{+END}

						<dt class="field-name">Difficulty:</dt> <dd>{$UCASE*,{DIFFICULTY_LEVEL},1}</dd>

						<dt class="field-name">Tutorial type:</dt> <dd>{$?,{CORE},Core documentation,Auxillary}</dd>

						<dt class="field-name">Tags:</dt>
						<dd>
							<ul class="horizontal-meta-details" style="width: auto">
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
			<li>If you have found documentation problems that you'd like someone else to solve log an <a target="_blank" href="https://composr.app/tracker/set_project.php?project_id=7" title="Report issue on tracker {!LINK_NEW_WINDOW}">issue to the tracker</a>.</li>
			<li>If you'd like to contribute a chunk of documentation to go into a tutorial, also log an <a target="_blank" href="https://composr.app/tracker/set_project.php?project_id=7" title="Report issue on tracker {!LINK_NEW_WINDOW}">issue to the tracker</a>.</li>
			<li>If you want to contribute a new tutorial you can <a href="{$PAGE_LINK*,_SEARCH:cms_tutorials}">submit a link</a>.</li>
		</ul>
	</div>
</div>
