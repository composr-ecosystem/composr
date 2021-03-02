{TITLE}

{$REQUIRE_CSS,messages}

{+START,INCLUDE,RED_ALERT}
	ROLE=alert
	TEXT={$?,{$IS_NON_EMPTY,{ANSWER}},{!FORUM_INTRO_QUESTION_TEXT},{!FORUM_INTRO_QUESTION_TEXT_ALT}}
{+END}

<div class="box box---cns-forum-intro-question-screen"><div class="box-inner">
	{+START,IF_NON_EMPTY,{ANSWER}}
		<h2>{!QUESTION}</h2>
	{+END}
	{+START,IF_EMPTY,{ANSWER}}
		<h2>{!MESSAGE}</h2>
	{+END}

	{QUESTION}
</div></div>

<form title="{!PROCEED}" action="{URL*}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	{+START,IF_NON_EMPTY,{ANSWER}}
		<div class="cns-intro-question-answer-box"><label for="answer">{!ANSWER}</label>: <input maxlength="255" id="answer" class="form-control" type="text" name="answer" /> <button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--proceed" type="submit">{!PROCEED}</button></div>
	{+END}
	{+START,IF_EMPTY,{ANSWER}}
		<div>
			<input type="hidden" name="answer" value="" />

			<p class="proceed-button">
				<button accesskey="u" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
			</p>
		</div>
	{+END}
</form>
