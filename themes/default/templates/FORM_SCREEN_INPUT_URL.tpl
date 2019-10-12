<input {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} maxlength="255" tabindex="{TABINDEX*}" class="input-line{REQUIRED*} form-control form-control-wide" type="text" placeholder="https://" id="{NAME*}" name="{NAME*}" value="{DEFAULT*}" />
{$,type=url will not work as relative URLs are not accepted}
