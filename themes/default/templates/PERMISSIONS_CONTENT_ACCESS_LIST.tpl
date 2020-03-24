<label for="perm__{POST_NAME*}" class="accessibility-hidden">{LABEL*}</label>
<select name="perm__{POST_NAME*}" id="perm__{POST_NAME*}">
	<option value="-1"{+START,IF_NON_PASSED,HAS_ACCESS} selected="selected"{+END}>&ndash;</option>
	<option value="0"{+START,IF_PASSED,HAS_ACCESS}{+START,IF,{$NOT,{HAS_ACCESS}}} selected="selected"{+END}{+END}>{!NO_COMPACT}</option>
	<option value="1"{+START,IF_PASSED,HAS_ACCESS}{+START,IF,{HAS_ACCESS}} selected="selected"{+END}{+END}>{!YES_COMPACT}</option>
</select>
