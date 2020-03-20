<label for="perm__{POST_NAME*}" class="accessibility-hidden">{LABEL*}</label>
<input type="hidden" name="perm__{POST_NAME*}" value="0" />{$,Fallback if not checked}
<input type="checkbox" name="perm__{POST_NAME*}" id="perm__{POST_NAME*}" value="1"{+START,IF,{HAS_ACCESS}} checked="checked"{+END} />
