{$,Load classList and ES6 Promise polyfill for Internet Explorer LEGACY}
{+START,IF,{$BROWSER_MATCHES,ie}}
	<script {$CSP_NONCE_HTML} src="{FROM*}/class_list.js"></script>
	<script {$CSP_NONCE_HTML} src="{FROM*}/promise.js"></script>
{+END}

{$,Required for $cms.requireJavascript() to work properly as DOM does not currently provide any way to check if a particular script has been already loaded}
{$,Loaded early so we can track document load properly, needed for $dom.ready}
<script {$CSP_NONCE_HTML} src="{FROM*}/dom_init.js"></script>
