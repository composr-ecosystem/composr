<div class="clearfix">
	{+START,IF,{$ADDON_INSTALLED,search}}
		{$SET,search_url,{$SELF_URL}}
		<form class="filedump-filter" role="search" title="{!SEARCH}" data-disable-buttons-on-submit="{}" action="{$URL_FOR_GET_FORM*,{$GET,search_url},search,type_filter,sort,place,recurse,file}#tab--{TAB%}" method="get">
			{$HIDDENS_FOR_GET_FORM,{$GET,search_url},search,type_filter,sort,subpath,recurse,file}

			<p class="left">
				<label class="accessibility-hidden" for="search-filedump-{I*}">{!SEARCH}</label>
				<input {+START,IF,{$MOBILE}} autocorrect="off"{+END} maxlength="255" size="20" type="search" id="search-filedump-{I*}" class="form-control" name="search" placeholder="{!SEARCH}" value="{SEARCH*}" />

				<label class="recurse horiz-field-sep block-mobile" for="recurse-filedump-{I*}">
					{!INCLUDE_SUBFOLDERS}
					<input {+START,IF,{$NEQ,{$_GET,recurse},0}} checked="checked"{+END} type="checkbox" name="recurse" id="recurse-filedump-{I*}" value="1" />
				</label>

				<br class="block-mobile" />

				<label class="type-filter-filedump horiz-field-sep block-mobile" for="type-filter-filedump-{I*}">
					{!SHOW}
					<select id="type-filter-filedump-{I*}" name="type_filter" class="form-control">
						<option {+START,IF,{$EQ,{TYPE_FILTER},}} selected="selected"{+END} value="">{!ALL}</option>
						<option {+START,IF,{$EQ,{TYPE_FILTER},images}} selected="selected"{+END} value="images">{!IMAGES}</option>
						<option {+START,IF,{$EQ,{TYPE_FILTER},videos}} selected="selected"{+END} value="videos">{!VIDEOS}</option>
						<option {+START,IF,{$EQ,{TYPE_FILTER},audios}} selected="selected"{+END} value="audios">{!AUDIOS}</option>
						<option {+START,IF,{$EQ,{TYPE_FILTER},others}} selected="selected"{+END} value="others">{!OTHER}</option>
					</select>
				</label>

				<label class="jump-to-filedump horiz-field-sep block-mobile" for="jump-to-filedump-{I*}">
					{!JUMP_TO_FOLDER}
					<select id="jump-to-filedump-{I*}" name="subpath" class="form-control">
						{+START,IF_NON_EMPTY,{FILTERED_DIRECTORIES_MISSES}}
							<optgroup label="{!FILEDUMP_FOLDER_MATCHING}">
						{+END}
						{+START,LOOP,FILTERED_DIRECTORIES}
							<option {+START,IF,{$EQ,{SUBPATH},/{_loop_var*}{$?,{$IS_NON_EMPTY,{_loop_var}},/}}} selected="selected"{+END} value="/{_loop_var*}{$?,{$IS_NON_EMPTY,{_loop_var}},/}">/{_loop_var*}</option>
						{+END}
						{+START,IF_NON_EMPTY,{FILTERED_DIRECTORIES_MISSES}}
							</optgroup>
						{+END}
						{+START,IF_NON_EMPTY,{FILTERED_DIRECTORIES_MISSES}}
							<optgroup label="{!FILEDUMP_FOLDER_NON_MATCHING}">
								{+START,LOOP,FILTERED_DIRECTORIES_MISSES}
									<option {+START,IF,{$EQ,{SUBPATH},/{_loop_var*}{$?,{$IS_NON_EMPTY,{_loop_var}},/}}} selected="selected"{+END} value="/{_loop_var*}{$?,{$IS_NON_EMPTY,{_loop_var}},/}">/{_loop_var*}</option>
								{+END}
							</optgroup>
						{+END}
					</select>
				</label>

				<br class="block-mobile" />

				<label class="sort-filedump horiz-field-sep block-mobile" for="sort-filedump-{I*}">
					{!SORT_BY}
					<select id="sort-filedump-{I*}" name="sort" class="form-control">
						<option {+START,IF,{$EQ,{SORT},time ASC}} selected="selected"{+END} value="time ASC">{!DATE_TIME},{!_ASCENDING}</option>
						<option {+START,IF,{$EQ,{SORT},time DESC}} selected="selected"{+END} value="time DESC">{!DATE_TIME},{!_DESCENDING}</option>
						<option {+START,IF,{$EQ,{SORT},name ASC}} selected="selected"{+END} value="name ASC">{!FILENAME},{!_ASCENDING}</option>
						<option {+START,IF,{$EQ,{SORT},name DESC}} selected="selected"{+END} value="name DESC">{!FILENAME},{!_DESCENDING}</option>
						<option {+START,IF,{$EQ,{SORT},size ASC}} selected="selected"{+END} value="size ASC">{!FILE_SIZE},{!_ASCENDING}</option>
						<option {+START,IF,{$EQ,{SORT},size DESC}} selected="selected"{+END} value="size DESC">{!FILE_SIZE},{!_DESCENDING}</option>
					</select>
				</label>

				<button class="btn btn-primary btn-sm buttons--filter" type="submit">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} <span>{!FILTER}</span></button>
			</p>
		</form>
	{+END}
</div>
