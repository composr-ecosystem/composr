<tr>
	<td class="cns-topic-poll-result-column2">
		<span>
			{+START,IF_PASSED,VOTERS_URL}<a data-open-as-overlay="{height: 600}" href="{VOTERS_URL*}">{+END}{!VOTES,{NUM_VOTES*}}{+START,IF_PASSED,VOTERS_URL}</a>{+END}
		</span>
	</td>
	<th class="de-th cns-topic-poll-result cns-topic-poll-relative">
		<div class="cns-topic-poll-progress" style="width: {WIDTH*}%;"></div>
		<div class="cns-topic-poll-relative">{ANSWER*}</div>
	</th>
</tr>
