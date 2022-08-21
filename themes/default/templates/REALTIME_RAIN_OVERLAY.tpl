{$REQUIRE_JAVASCRIPT,realtime_rain}

<div data-tpl="realtimeRainOverlay" data-tpl-params="{+START,PARAMS_JSON,MIN_TIME}{_*}{+END}">
	<div id="real-time-surround">
		<div id="real-time" aria-live="polite">
			<div id="news-ticker" class="js-hover-window-pause">
				<h1>{!NEWS_FLASHES}</h1>

				<div class="news-inside">
					<div class="news-content">
						<p id="news-go-here"><em>{!NEWS_WILL_APPEAR_HERE}</em></p>
					</div>
				</div>

				<div class="news-footer"></div>
			</div>

			<div id="bubbles-go-here">
			</div>

			<div id="loading-icon" class="ajax-loading"><img aria-busy="true" width="20" height="20" src="{$IMG*,loading}" alt="{!LOADING}" title="{!LOADING}" /></div>
		</div>

		<div id="timer-outer"><div id="timer-inner">
			<div id="real-time-indicator">
				<span id="real-time-date">{!LOADING}</span> <span id="real-time-time"></span>
			</div>

			<div id="timer">
				<div id="pause-but">
					<a class="js-click-toggle-window-pausing" title="{!PAUSE}" href="#!">{+START,INCLUDE,ICON}
						NAME=buttons/pause
						ICON_SIZE=30
					{+END}</a>
				</div>

				<div id="pre-but">
					<a class="js-click-rain-slow-down" title="{!SLOW_DOWN}" href="#!">{+START,INCLUDE,ICON}
						NAME=realtime_rain/previous_but
						ICON_SIZE=30
					{+END}</a>
				</div>

				<div class="js-hover-toggle-real-time-indicator js-mouseover-set-time-line-position js-mousemove-timeline-click js-click-timeline-click" id="time-line">
					<img id="time-line-image" width="802" height="6" src="{$IMG*,realtime_rain/timeline/time_line}" alt="" />
				</div>

				<div id="next-but">
					<a class="js-click-rain-speed-up" title="{!SPEED_UP}" href="#!">{+START,INCLUDE,ICON}
						NAME=realtime_rain/next_but
						ICON_SIZE=30
					{+END}</a>
				</div>
			</div>
		</div></div>
	</div>
</div>
