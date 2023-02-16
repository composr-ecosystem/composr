{$REQUIRE_JAVASCRIPT,core_feedback_features}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}

{$,Template uses auto-complete}
{$REQUIRE_JAVASCRIPT,jquery}
{$REQUIRE_JAVASCRIPT,jquery_autocomplete}
{$REQUIRE_CSS,autocomplete}

{$SET,GET_NAME,{$AND,{$IS_GUEST},{$CNS}}}

<div data-view="CommentsPostingForm" data-view-params="{+START,PARAMS_JSON,MORE_URL,GET_EMAIL,GET_NAME,GET_TITLE,EMAIL_OPTIONAL,TITLE_OPTIONAL,WYSIWYG,CAPTCHA}{_*}{+END}">
	{+START,IF_NON_EMPTY,{COMMENT_URL}}
	<form role="form" title="{TITLE*}" class="comments-form" id="comments-form" action="{COMMENT_URL*}{+START,IF_NON_EMPTY,{$GET,current_anchor}}#{$GET,current_anchor}{+END}{+START,IF_EMPTY,{$GET,current_anchor}}{+START,IF_PASSED_AND_TRUE,COMMENTS}#last-comment{+END}{+END}" method="post" enctype="multipart/form-data" data-view="SubmissionFlow" data-view-params="{+START,PARAMS_JSON,JS_FUNCTION_CALLS,JAVASCRIPT,ANALYTIC_EVENT_CATEGORY}{_*}{+END}">
		{$INSERT_FORM_POST_SECURITY}
		<input type="hidden" name="_comment_form_post" value="1" />
	{+END}

		{+START,IF_PASSED,HIDDEN}{HIDDEN}{+END}
		<input type="hidden" name="_validated" value="1" />
		<input type="hidden" name="stub" value="" />

		{+START,IF,{$NOT,{$GET,GET_NAME}}}
			<input type="hidden" name="name" value="" />
		{+END}
		{+START,IF,{$NOT,{GET_EMAIL}}}
			<input type="hidden" name="email" value="" />
		{+END}
		{+START,IF,{$NOT,{GET_TITLE}}}
			<input type="hidden" name="title" value="" />
		{+END}

		<div class="box box---comments-posting-form"{+START,IF_PASSED,EXPAND_TYPE} data-toggleable-tray="{}"{+END}>
			<div class="box-inner">
				{+START,IF_NON_EMPTY,{TITLE}}
					<h3 class="toggleable-tray-title">
						{+START,IF_NON_PASSED,EXPAND_TYPE}
							{TITLE*}
						{+END}
						{+START,IF_PASSED,EXPAND_TYPE}
							<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{$?,{$EQ,{EXPAND_TYPE},contract},{!CONTRACT},{!EXPAND}}">{+START,INCLUDE,ICON}
								NAME=trays/{EXPAND_TYPE}
								ICON_SIZE=24
							{+END}</a>
							<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{TITLE*}</a>
						{+END}
					</h3>
				{+END}
				<div class="comments-posting-form-outer {+START,IF_PASSED,EXPAND_TYPE} toggleable-tray js-tray-content{+END}" id="comments-posting-form-outer" style="display: {DISPLAY*}">
					<div class="comments-posting-form-inner">
						<table class="map-table wide-table">
							{+START,IF,{$DESKTOP}}
								<colgroup>
									<col class="comments-field-name-column" />
									<col class="comments-field-input-column" />
								</colgroup>
							{+END}

							<tbody>
								{$GET,EXTRA_COMMENTS_FIELDS_1}

								{+START,IF,{$GET,GET_NAME}}
									<tr>
										<th class="de-th vertical-alignment">
											<label for="poster-name-if-guest">{!YOUR_NAME}:</label>
											{$,Never optional; may not be requested if logged in as we already know}
										</th>

										<td>
											<input id="poster-name-if-guest" class="form-control" name="name" autocomplete="name" type="text" tabindex="1" maxlength="255" size="24" />
											{+START,IF_PASSED,JOIN_BITS}{+START,IF_NON_EMPTY,{JOIN_BITS}}
												<span class="horiz-field-sep">{JOIN_BITS}</span>
											{+END}{+END}
										</td>
									</tr>
								{+END}

								{+START,IF,{GET_EMAIL}}
									<tr>
										<th class="de-th vertical-alignment">
											<label for="email">{!YOUR_EMAIL_ADDRESS}:</label>
											{+START,IF,{EMAIL_OPTIONAL}}<br /><span class="associated-details">({!OPTIONAL})</span>{+END}
										</th>

										<td>
											<div>
												<input aria-errormessage="error-email-msg" id="email" name="email" value="{$MEMBER_EMAIL*}" type="text" tabindex="2" maxlength="255" class="form-control form-control-wide{+START,IF,{$NOT,{EMAIL_OPTIONAL}}} input-text-required{+END}" />
											</div>

											<div id="error-email" style="display: none" class="input-error-here">
												{+START,INCLUDE,ICON}
													NAME=status/warn
													ICON_SIZE=24
												{+END}
												<span id="error-email-msg" class="js-error-message"></span>
											</div>
										</td>
									</tr>
								{+END}

								{+START,IF,{GET_TITLE}}
									<tr>
										<th class="de-th vertical-alignment">
											<label for="title">{!SUBJECT}:</label>
											{+START,IF,{TITLE_OPTIONAL}}<br /><span class="associated-details">({!OPTIONAL})</span>{+END}
										</th>

										<td>
											<div>
												<input aria-errormessage="error-title-msg" id="title" name="title" value="{DEFAULT_TITLE*}" type="text" tabindex="3" maxlength="255" class="form-control form-control-wide" />
											</div>

											<div id="error-title" style="display: none" class="input-error-here">
												{+START,INCLUDE,ICON}
													NAME=status/warn
													ICON_SIZE=24
												{+END}
												<span id="error-title-msg" class="js-error-message"></span>
											</div>
										</td>
									</tr>
								{+END}

								{+START,IF_PASSED,REVIEW_RATING_CRITERIA}{+START,IF_PASSED,TYPE}{+START,IF_PASSED,ID}
								{+START,LOOP,REVIEW_RATING_CRITERIA}
								<tr class="js-container-review-rating">
									<th class="de-th vertical-alignment">
										{+START,IF_EMPTY,{REVIEW_TITLE}}{!RATING}:{+END}{+START,IF_NON_EMPTY,{REVIEW_TITLE}}{REVIEW_TITLE*}:{+END}
									</th>

											<td>
												{+START,INCLUDE,ICON}
													NAME=feedback/rating
													ICON_ID=review-bar-1--{TYPE|*}--{REVIEW_TITLE|*}--{ID|*}
													ICON_CLASS=rating-star js-img-review-bar
													ICON_SIZE=18
													ICON_ATTRS=data-vw-rating="2"
												{+END}
												{+START,INCLUDE,ICON}
													NAME=feedback/rating
													ICON_ID=review-bar-2--{TYPE|*}--{REVIEW_TITLE|*}--{ID|*}
													ICON_CLASS=rating-star js-img-review-bar
													ICON_SIZE=18
													ICON_ATTRS=data-vw-rating="4"
												{+END}
												{+START,INCLUDE,ICON}
													NAME=feedback/rating
													ICON_ID=review-bar-3--{TYPE|*}--{REVIEW_TITLE|*}--{ID|*}
													ICON_CLASS=rating-star js-img-review-bar
													ICON_SIZE=18
													ICON_ATTRS=data-vw-rating="6"
												{+END}
												{+START,INCLUDE,ICON}
													NAME=feedback/rating
													ICON_ID=review-bar-4--{TYPE|*}--{REVIEW_TITLE|*}--{ID|*}
													ICON_CLASS=rating-star js-img-review-bar
													ICON_SIZE=18
													ICON_ATTRS=data-vw-rating="8"
												{+END}
												{+START,INCLUDE,ICON}
													NAME=feedback/rating
													ICON_ID=review-bar-5--{TYPE|*}--{REVIEW_TITLE|*}--{ID|*}
													ICON_CLASS=rating-star js-img-review-bar
													ICON_SIZE=18
													ICON_ATTRS=data-vw-rating="10"
												{+END}

												<input id="review-rating--{TYPE|*}--{REVIEW_TITLE|*}--{ID|*}" class="js-inp-review-rating" type="hidden" name="review_rating__{REVIEW_TITLE|*}" value="" />
											</td>
										</tr>
									{+END}
								{+END}{+END}{+END}

								<tr>
									<th class="de-th">
										{$SET,needs_msg_label,{$OR,{$GET,GET_NAME},{GET_EMAIL},{GET_TITLE}}}
										{+START,IF,{$GET,needs_msg_label}}
											<div class="vertical-alignment">
												<a data-open-as-overlay="{}" class="link-exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{$PAGE_LINK*,_SEARCH:userguide_comcode}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a>
												<label for="post">{!POST_COMMENT}:</label>
											</div>
										{+END}

										{+START,IF_NON_EMPTY,{FIRST_POST}{RULES_TEXT}}
											<ul class="associated-links-block-group">
												{+START,IF_NON_EMPTY,{FIRST_POST}}
													<li><a class="non-link js-click-pd-on-mobile" title="{!cns:FIRST_POST} {!LINK_NEW_WINDOW}" target="_blank" href="{FIRST_POST_URL*}" data-cms-tooltip="{ contents: '{FIRST_POST;^*}', triggers: 'hover focus', delay: 0, width: '320px' }">{!cns:FIRST_POST}</a></li>
												{+END}

												{+START,IF_NON_EMPTY,{RULES_TEXT}}
													<li><a class="non-link js-click-pd-on-mobile" href="{$PAGE_LINK*,:rules}" data-cms-tooltip="{ contents: '{$TRUNCATE_LEFT;^*,{RULES_TEXT},1000,0,1}', triggers: 'hover focus', delay: 0, width: '320px' }">{+START,IF,{$DESKTOP}}<span class="inline-desktop">{!HOVER_MOUSE_IMPORTANT}</span>{+END}<span class="inline-mobile">{!TAP_MOUSE_IMPORTANT}</span></a></li>
												{+END}
											</ul>
										{+END}

										{+START,IF,{$NOT,{$GET,needs_msg_label}}}
											<div>
												<a data-open-as-overlay="{}" class="link-exempt" title="{!COMCODE_MESSAGE,Comcode} {!LINK_NEW_WINDOW}" target="_blank" href="{$PAGE_LINK*,_SEARCH:userguide_comcode}">{+START,INCLUDE,ICON}NAME=editor/comcode{+END}</a>
												<label for="post" class="vertical-alignment">{!POST_COMMENT}:</label>
											</div>
										{+END}

										{+START,IF,{$DESKTOP}}
											{+START,IF_NON_EMPTY,{EMOTICONS}}
												<div class="comments-posting-form-emoticons block-desktop">
													<div class="box box---comments-posting-form"><div class="box-inner">
														{EMOTICONS}

														{+START,IF,{$CNS}}
															<p class="associated-link associated-links-block-group"><a rel="nofollow" href="#!" class="js-click-open-site-emoticon-chooser-window">{!EMOTICONS_POPUP}</a></p>
														{+END}
													</div>
													</div>
												</div>
											{+END}
										{+END}
									</th>

									<td>
										<div>
											<textarea aria-errormessage="error-post-msg" name="post" id="post" data-textarea-auto-height="" tabindex="6" accesskey="x" class="{$?,{TRUE_ATTACHMENT_UI},true-attachment-ui,faux-attachment-ui} form-control form-control-wide js-focus-textarea-post" cols="42" rows="{$?,{$IS_NON_EMPTY,{$GET,COMMENT_POSTING_ROWS}},{$GET,COMMENT_POSTING_ROWS},11}">{POST_WARNING*}{+START,IF_PASSED,DEFAULT_POST}{DEFAULT_POST*}{+END}</textarea>
											<input type="hidden" name="comcode__post" value="1" />
										</div>

										<div id="error-post" style="display: none" class="input-error-here">
											{+START,INCLUDE,ICON}
												NAME=status/warn
												ICON_SIZE=24
											{+END}
											<span id="error-post-msg" class="js-error-message"></span>
										</div>

										{+START,IF_PASSED,ATTACHMENTS}
											<div class="attachments">
												{+START,IF_PASSED,ATTACH_SIZE_FIELD}
													{ATTACH_SIZE_FIELD}
												{+END}
												<input type="hidden" name="posting_ref_id" value="{$RAND%}" />
												{ATTACHMENTS}
											</div>
										{+END}
									</td>
								</tr>

								{$GET,EXTRA_COMMENTS_FIELDS_2}
							</tbody>
						</table>

						<div class="comments-posting-form-end">
							{+START,IF_PASSED_AND_TRUE,USE_CAPTCHA}
								{+START,INCLUDE,COMMENTS_POSTING_FORM_CAPTCHA}
									TABINDEX=7
								{+END}
							{+END}

							{$SET,has_preview_button,{$AND,{$DESKTOP},{$JS_ON},{$CONFIG_OPTION,enable_previews}}}
							{+START,IF_PASSED,SKIP_PREVIEW}{$SET,has_preview_button,0}{+END}

							<div class="proceed-button {$?,{$GET,has_preview_button},contains-preview-button,contains-no-preview-button}">
								{+START,IF,{$DESKTOP}}{+START,IF,{$GET,has_preview_button}}
									<button id="preview-button" accesskey="p" tabindex="250" class="btn btn-primary {$?,{$IS_EMPTY,{COMMENT_URL}},button-scr,button-scri} buttons--preview js-click-do-form-preview desktop-only" type="button">{+START,INCLUDE,ICON}NAME=buttons/preview{+END} {!PREVIEW}</button>
								{+END}{+END}

								{+START,IF_PASSED,MORE_URL}
									<button tabindex="6" accesskey="y" class="btn btn-primary {$?,{$IS_EMPTY,{COMMENT_URL}},button-scr,button-scri} buttons--new-post-full js-btn-full-editor" type="button">{+START,INCLUDE,ICON}NAME=buttons/new_post_full{+END} {+START,IF,{$DESKTOP}}<span class="inline-desktop">{!FULL_EDITOR}</span>{+END}<span class="inline-mobile">{!MORE}</span></button>
								{+END}

								{+START,IF_PASSED,ATTACHMENTS}{+START,IF,{$AND,{TRUE_ATTACHMENT_UI},{$BROWSER_MATCHES,simplified_attachments_ui}}}
									<button tabindex="7" id="js-attachment-browse-button--post" style="display: none;" class="btn btn-primary {$?,{$IS_EMPTY,{COMMENT_URL}},button-scr,button-scri} for-field-post buttons--thumbnail" type="button">{+START,INCLUDE,ICON}NAME=buttons/thumbnail{+END} {!comcode:ADD_COMCODE_IMAGES}</button>
								{+END}{+END}

								{+START,SET,button_title}{+START,IF_PASSED,SUBMIT_NAME}{SUBMIT_NAME*}{+END}{+START,IF_NON_PASSED,SUBMIT_NAME}{+START,IF_NON_EMPTY,{TITLE}}{TITLE*}{+END}{+START,IF_EMPTY,{TITLE}}{!SEND}{+END}{+END}{+END}
								{+START,SET,button_icon}{+START,IF_PASSED,SUBMIT_ICON}{SUBMIT_ICON}{+END}{+START,IF_NON_PASSED,SUBMIT_ICON}{+START,IF_NON_PASSED,MORE_URL}buttons/new_comment{+END}{+START,IF_PASSED,MORE_URL}buttons/new_reply{+END}{+END}{+END}
								<button tabindex="8" accesskey="u" id="submit-button" class="btn btn-primary {$?,{$IS_EMPTY,{COMMENT_URL}},button-scr,button-scri} {$?,{$GET,has_preview_button},near-preview-button,not-near-preview-button} js-btn-save-comment js-btn-main-submit-form" type="button">{+START,INCLUDE,ICON}NAME={$GET,button_icon}{+END} {+START,IF,{$DESKTOP}}<span class="inline-desktop">{$GET,button_title}</span>{+END}<span class="inline-mobile">{$REPLACE,{!cns:REPLY},{!_REPLY},{$GET,button_title}}</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	{+START,IF_NON_EMPTY,{COMMENT_URL}}
	</form>
	{+END}

	{+START,IF,{$CONFIG_OPTION,enable_previews}}
		<iframe {$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"} title="{!PREVIEW}" name="preview-iframe" id="preview-iframe" src="{$BASE_URL*}/data/empty.php" class="hidden-preview-frame">{!PREVIEW}</iframe>
	{+END}
</div>
