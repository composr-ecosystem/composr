{$,Load classList and ES6 Promise polyfill for Internet Explorer LEGACY}
{+START,IF,{$BROWSER_MATCHES,ie}}
<script {$CSP_NONCE_HTML} src="{$BASE_URL*}/data/polyfills/class_list.js"></script>
<script {$CSP_NONCE_HTML} src="{$BASE_URL*}/data/polyfills/promise.js"></script>
{+END}
