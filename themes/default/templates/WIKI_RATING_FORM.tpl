{+START,IF_NON_EMPTY,{ERROR}}
	{ERROR}
{+END}

{+START,IF,{ALLOW_RATING}}
	{+START,LOOP,ALL_RATING_CRITERIA}
		{+START,IF_EMPTY,{TITLE}}<span class="accessibility-hidden">{+END}<label for="rating__{CONTENT_TYPE*}__{TYPE*}__{ID*}"><strong>{+START,IF_EMPTY,{TITLE}}{!RATING}:{+END}{+START,IF_NON_EMPTY,{TITLE}}{TITLE*}:{+END}</strong></label>{+START,IF_EMPTY,{TITLE}}</span>{+END}
		<select id="rating__{CONTENT_TYPE*}__{TYPE*}__{ID*}" name="rating__{CONTENT_TYPE*}__{TYPE*}__{ID*}" class="form-control">
			<option value="10">5</option>
			<option value="8">4</option>
			<option value="6">3</option>
			<option value="4">2</option>
			<option value="2">1</option>
		</select>
		{+START,IF,{SIMPLISTIC}}
			<button data-disable-on-click="1" class="btn btn-primary btn-sm feedback--rate" type="submit">{+START,INCLUDE,ICON}NAME=feedback/rate{+END} {!RATE}</button>
		{+END}
	{+END}
	{+START,IF,{$NOT,{SIMPLISTIC}}}
		<div>
			<button data-disable-on-click="1" class="btn btn-primary btn-sm feedback--rate" type="submit">{+START,INCLUDE,ICON}NAME=feedback/rate{+END} {!RATE}</button>
		</div>
	{+END}
{+END}
