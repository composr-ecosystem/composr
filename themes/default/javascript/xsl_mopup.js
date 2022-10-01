/*{$,parser hint: .innerHTML okay}*/

/* LEGACY https://bugzilla.mozilla.org/show_bug.cgi?id=98168 */

// A workaround for XSL-to-XHTML systems that don't
//  implement XSL 'disable-output-escaping="yes"'.
//
// sburke@cpan.org, Sean M. Burke.
//  - I hereby release this JavaScript code to the public domain.

var is_decoding;
var DEBUG = 0;

function complaining(s) {
    console.log(s);
    return s;
}

if (!(document.getElementById && document.getElementsByName)) {
    throw complaining("Your browser is too old to render this page properly."
        + "  Consider going to getfirefox.com to upgrade.");
}

function checkDecoding() {
    var d = document.getElementById('cometestme');
    if (!d) {
        throw complaining("Can't find an id='cometestme' element?");
    } else if (d.textContent === undefined) {
        // It's a browser with a halfassed DOM implementation (like IE6)
        // that doesn't implement textContent!  Assume that if it's that
        // dumb, it probably doesn't implement disable-content-encoding.

    } else {
        var ampy = d.textContent;
        if (DEBUG > 1) {
            console.log("Got " + ampy);
        }

        if (ampy == undefined) throw complaining("'cometestme' element has undefined text content?!");
        if (ampy == '') throw complaining("'cometestme' element has empty text content?!");

        if (ampy == "\x26") {
            is_decoding = true;
        }
        else if (ampy == "\x26amp;") {
            is_decoding = false;
        }
        else {
            throw complaining('Insane value: "' + ampy + '"!');
        }
    }

    var msg =
            (is_decoding == undefined) ? "I can't tell whether the XSL processor supports disable-content-encoding!D"
                : is_decoding ? "The XSL processor DOES support disable-content-encoding"
                    : "The XSL processor does NOT support disable-content-encoding"
        ;
    if (DEBUG) console.log(msg);
    return msg;
}


function goDecoding() {
    checkDecoding();

    if (is_decoding) {
        if (DEBUG) console.log("No work needs doing -- already decoded!");
        return;
    }

    var toDecode = document.getElementsByName('decodeable');
    if (!( toDecode && toDecode.length )) {
        if (DEBUG) console.log("No work needs doing -- no elements to decode!");
        return;
    }


    var s;
    for (var i = toDecode.length - 1; i >= 0; i--) {
        s = toDecode[i].textContent;

        if (
            s == undefined ||
            (s.indexOf('&') == -1 && s.indexOf('<') == -1)
        ) {
            // the null or markupless element needs no reworking
        } else {
            toDecode[i].innerHTML = s; // that's the magic
        }
    }

    return;
}


//End


//utility function for appending USM param on existing URL, parameterized or otherwise

function encodeUSMParam(theURL) {

    var rslt = "XXX";

    if (theURL.indexOf('?') > 0) {
        rslt = encodeURI(theURL + "&format=usm");
    } else {
        rslt = theURL + "?format=usm";
    }

    return rslt;

}
