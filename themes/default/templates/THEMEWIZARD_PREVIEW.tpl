<div class="clearfix">
	<div class="themewizard-info-box">
		<div class="box box---themewizard-2-screen"><div class="box-inner">
			<h2>{!SEED_COLOUR}</h2>

			<div>#<span class="themewizard-info-box-label">{SEED*}</span></div>
			<div>({!RED}: <span class="themewizard-colour">{RED*}</span>, {!GREEN}: <span class="themewizard-colour">{GREEN*}</span>, {!BLUE}: <span class="themewizard-colour">{BLUE*}</span>)</div>
		</div></div>
	</div>

	<p>{!THEMEWIZARD_PREVIEW_DOMINANT,{DOMINANT*}}</p>

	<p>{!THEMEWIZARD_PREVIEW_LIGHT_DARK,{LD*}}</p>
</div>

<div class="themewizard-preview-wrap">
	<div class="box box---themewizard-2-screen"><div class="box-inner">
		<h2>{!PREVIEW}</h2>

		<iframe {$?,{$BROWSER_MATCHES,ie}, frameBorder="0" scrolling="no"} title="{!PREVIEW}" class="themewizard-preview" src="{$PAGE_LINK*,::keep_theme_seed={SEED#}:keep_theme_dark={DARK#}:keep_theme_source={SOURCE_THEME#}:keep_theme={SOURCE_THEME#}:keep_theme_algorithm={ALGORITHM#}:wide=1:keep_theme=default}">{!PREVIEW}</iframe>
	</div></div>
</div>

<nav>
	<ul class="actions-list">
		<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a target="_blank" title="{!PREVIEW} {!LINK_NEW_WINDOW}" href="{$PAGE_LINK*,::keep_theme_seed={SEED#}:keep_theme_dark={DARK#}:keep_theme_source={SOURCE_THEME#}:keep_theme={SOURCE_THEME#}:keep_theme_algorithm={ALGORITHM#}}">{!PREVIEW}</a></li>
	</ul>
</nav>
