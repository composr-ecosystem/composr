<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
	{HIDDEN}

	<div class="installer-main-min">
		<p id="install-welcome">
			<strong>Welcome! &middot; Bienvenue! &middot; Willkommen! &middot; Bienvenidos! &middot; Welkom! &middot; Swaagatam! &middot; Irashaimasu! &middot; Huan yin! &middot; Dobro pozhalovat'! &middot; Witaj!</strong>
		</p>

		{+START,IF_NON_EMPTY,{WARNINGS}}
			{WARNINGS}
		{+END}

		{+START,IF_NON_EMPTY,{LANGUAGES}}
			<table class="map-table form-table wide-table">
				<colgroup>
					<col class="installer-left-column" />
					<col class="installer-right-column" />
				</colgroup>

				<tbody>
					 <tr>
						  <th class="form-table-field-name">{!PLEASE_CHOOSE_LANG} (&dagger;)</th>
						  <td class="form-table-field-input">
								<div class="accessibility-hidden"><label for="default_lang">{!PLEASE_CHOOSE_LANG}</label></div>
								<select id="default_lang" name="default_lang">
									 {LANGUAGES}
								</select>
						  </td>
					 </tr>
				</tbody>
		  </table>
		{+END}
		{+START,IF_EMPTY,{LANGUAGES}}
			<input type="hidden" name="default_lang" value="{$LANG*}" />
		{+END}
	</div>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" data-disable-on-click="1" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
	</p>

	{+START,IF,{$EQ,{$SUBSTR_COUNT,{LANGUAGES},</option>},1}}
		<p>&dagger; Currently we do not directly ship additional languages with Composr. However community translations (made on Transifex) may be distributed in the addon directory.</p>
	{+END}
</form>
