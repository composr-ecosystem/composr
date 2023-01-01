{+START,IF,{USING_TEXTAREA}}
	{$INIT,with_whitespace_id,0}
	{$INC,with_whitespace_id}

	<div class="constrain-field" data-view="TextAreaCopyCode" data-view-params="{+START,PARAMS_JSON,with_whitespace_id}{_*}{+END}">
		<label class="accessibility-hidden" for="with_whitespace_{$GET*,with_whitespace_id}">{!NA_EM}</label>
		<textarea id="with_whitespace_{$GET*,with_whitespace_id}" name="with_whitespace_{$GET*,with_whitespace_id}" readonly="readonly" cols="70" rows="1" class="wide-field js-btn-select">{CONTENT*}</textarea>
	</div>
{+END}
{+START,IF,{$NOT,{USING_TEXTAREA}}}
	{+START,IF,{$NOT,{USING_CODETAG}}}
		<p class="whitespace-visible">{CONTENT*}</p>
	{+END}
	{+START,IF,{USING_CODETAG}}
		<code class="whitespace-visible">{CONTENT*}</code>
	{+END}
{+END}
