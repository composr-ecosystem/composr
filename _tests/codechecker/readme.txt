Code quality checker
====================

The code quality checker is a tool to check web documents against a number of standards, including:
 - PHP quality (including type-checking, which even PHP itself can't do at runtime)
 - de facto JavaScript (Common implementations of DOM, ECMAScript [ECMA-262/JS-1.5], common browser library functions)
 - (X)HTML5
 - XML
 - CSS with solid 2.1, and additional 3.0 properties
 - WCAG-level-3 (version 1, with some stuff from the 2 draft also)
 - Extra web checks (such as checking form mime-types when upload fields are involved)
 - Web compatibility
 - Spelling
The checker consists of a number of linked checkers.

The code quality checker is copyright Christopher Graham, licensed under the CPAL (http://opensource.org/licenses/cpal_1.0), and hence can be extended and re-distributed by third-parties.

The frontend is written in Java, and thus should run wherever Java can run. A minimum of Java 11 is required. Note the latest version of Java from java.com is version 8, as that website was created for the Java web plugin only, which has reached end-of-life some time ago.

The backend is written in PHP, and thus it also requires a command-line PHP interpreter to be installed (e.g. php.exe).

To launch the Code Quality checker open a command line to the code checker directory and run codechecker.sh (Linux/Mac OS) or codechecker.bat (Windows). The correct minimum version of Java needs to be in the system path.

You can drag and drop files into the checker, or you can work from a project directory.

The checker also has the ability to do line counts.

Text editors
------------

The checker is designed to work with the following editors:
 - CodeLobster (Windows)
 - Notepad++ (Windows)
 - TextMate (Mac)
 - Kate (Linux)
 - Geany (Linux)
 - JEdit (Cross Platform)
 - NetBeans (Cross Platform)
 - BBEdit (Mac)
 - Atom (Cross Platform)
 - Visual Studio Code (Cross Platform)
 - PSPad (Windows)
The editor is particularly appropriate as it can be directed to open up code at specific positions, and thus the checker can direct you to exact error locations in your files.

Error sensitivity
-----------------

The checker is not a be-all-and-end-all tool for conformance. Rather, it is designed to find a high number of errors, so that problems won't be missed. It is designed to increase the efficiency and quality of the IT professionals workflow.

In situations where additional code would remove ambiguity with-respect-to what may and may not be an error, the checkers enforce that the extra code to be written. For instance, the PHP checker enforces strict-typing (even though PHP itself does not), so that typing-errors may be found -- typing errors are in fact, often the cause of major security problems, as well as general bugs.

The checkers favour practical quality rather than strict standard compliance.

HTML etc
--------

Some tips:
 - If you need a form field label but can't place it in your design, make it invisible.

If you want to check an HTML e-mail (which requires additional rules), name the file '_mail.html' or '_mail.htm' and load in the files like any other file.

Accessibility
-------------

WCAG cannot be met by scanning alone. In particular, some manual checks are required:
 - Alternatives given to multimedia content
 - Use the clearest and simplest language appropriate for a site's content.
 - Divide large blocks of information into more manageable groups where natural and appropriate.
 - Provide information about document collections (i.e., documents comprising multiple pages.).
 - Specify the expansion of each abbreviation or acronym in a document where it first occurs.
 - Place distinguishing information at the beginning of headings, paragraphs, lists, etc.
 - Create a logical tab order through links, form controls, and objects
 - When plugins are used, info about it must be displayed
 - When an appropriate markup language exists, use markup rather than images to convey information.
 - Mark up lists and list items properly.
 - Ensure that all information conveyed with color is also available without color, for example from context or markup.
 - <blockquote> not used for non-quoting

CSS
---

This is a useful tool that does more checks: http://www.dirtymarkup.com/

Spellcheck
----------

The spellchecker requires that ASpell is installed, and the PHP pspell extension. If it is not, spell checking won't occur.

To install on Windows...

At the time of writing the ASpell distribution had a corrupt file, but the following alternative ZIP version worked:
http://ftp.gnu.org/gnu/aspell/w32/aspell-w32-0.50.3.zip
http://ftp.gnu.org/gnu/aspell/w32/aspell-en-0.50-2.zip (the English dictionary)

Extract to the "C:\Program Files\Aspell" directory, so you have:
C:\Program Files\Aspell\
 bin
 COPYING
 data
 dict
 doc
 README

PHP
---

The code quality checker has been an essential and very successful part of our plan to ensure a high level of stability in Composr. It allows us to do things like:
 - ensure that Composr runs on all supported versions of PHP, by spotting compatibility problems
 - find many types of error which we would otherwise only be able to find by tripping over problem situations in-code. For example, the following situations are not identifiable by PHP just by calling up a script (unless the actual line of code executes) but can be with our checker:
  - Calling a function that does not exist
  - Using the wrong number of parameters to a function
  - Ordering parameters to a function wrongly (Composr can detect when the passed data-types don't align)
  - referencing variables that don't exist
 - identifying areas that need special manual checking for security
 - [i]dozens of other things[/i]

The downside to the checker is that it will not let you use weak-typing, and it will force all PHP functions you use to be explicitly laid out according to their typing properties. This is because if weak-type checking is allowed in PHP, it is literally impossible to properly check the code for a whole range of problems - for example, it is impossible to check if function parameters are passed in an incorrect order (which is extremely useful for the case of a function having its arguments changed, and somewhere a call to it accidentally not updated).

Certain aspects of the PHP language have been left out of the subset supported by the checker. This is because either:
 - they are considered sloppy/error-prone
 - they are considered very bad style
 - they cannot be properly checker against, and hence the checker tightens things up to catch a higher number of errors

The intentionally skipped aspects are:
 - checking whether variables exist before considering heredoc/quote-embedded/now-doc variable references (Reason: can't check, and we avoid using embedded variables in Composr anyway)
 - @ is handled differently to PHP: it is tagged onto string-extraction, array-extraction, and function-calls (Reason: sloppy)
 - if..endif (etc) style (Reason: bad style)
 - non-functional style of include include_once require require_once print unset empty isset declare exit die (Reason: bad style)
 - expressions used as pure commands (Reason: bad style)
 - written AND or OR or XOR (Reason: bad style)
 - dynamic variable referencing ($$foo) (Reason: sloppy, a likely bug)
 - PHP 5.4 Class::{expr}() Syntax (Reason: sloppy, a likely bug)
 - object iteration (Reason: treating objects like arrays hurts type-checking, better to just allow returning an array using an object method)
 - certain functions, including insecure or platform-dependant ones (these can be safelisted per-file though) (Reason: they should not be used)

Some checks we could do but don't:
 - namespace resolution is not implemented, we just match on class names (Reason: We do not use namespaces internally in Composr; if namespaces are involved the class names can just be safelisted)
 - compatibility checks when implementing multiple interfaces (Reason: Complexity and a very rare event that would cause a run-time crash anyway)
 - goto label presence/position checks (Reason: We don't, and shouldn't, use goto)
 - full validation of use of inbuilt PHP objects (Reason: We don't use them much and we'd need to define all their skeletons in phpstub.php)
 - magic method validity checks (Reason: very unlikely to incorrectly define a magic method)
 - checking constants exist (predefined and global and class constants), and knowing their type (Reason: Would be difficult to implement given they can be defined dynamically)
 > see "Comparison to other PHP linters" for more

The PHP checker is very much set up to enforce compatibility across different PHP platforms. It is only assumed that the gd2 extension will be present:
For extension functions you should use function_exists at some point before using these function sets.
For other functions you can use function_exists too. The checker is smart about function_exists, but not very smart. You can do:
function_exists('foo')?foo():bar()
and
if (function_exists('foo')) {
	foo();
}
and
if ((function_exists('foo')) && (whatever) && (whatever) [...]) {
	foo();
}

You can allow a file to use an extra function set by declaring it in a comment like:
/*EXTRA FUNCTIONS: odbc\_.+*/

The PHP checker supports type-checking, via special PHP-doc comments. See phpstub.php for example commenting, as for the inbuilt PHP library.
If you really need to mix types within a single variable, it's recommended that you give the variable an initial value from a mixed() function, that you define as follows:

/**
 * Initialise a mixed variable to NULL.
 *
 * @return ?mixed Mixed type value initialised to NULL.
 */
function mixed()
{
   return null;
}

Comparison to other LAMP linters
--------------------------------

Here are some checks other linters do which we do not do (no duplication of remarks across projects, written in rough tool quality order)...

ESLint:
 > As with PHP CodeSniffer, the CQC can chain to ESLint
 > (We are aware JSHint exists, but we don't use it)

Google Lighthouse:
 > Run manually to find performance problems etc

PhpStorm's Code Inspector:
 - "Unreachable statement" (Reason: Often caught between a rock and a hard place with these, e.g. other lint checks for 'break' always being defined, or other lints insist on 'return' being defined yet a code path exits; the CQC only checks if you have code after an actual 'return' statement)
 - "Variable '$<variable>' is probably undefined" (Reason: Does not do smart branch checks, many false positives)
 - Unused checks (Reason: There are many reasons to not use variables, so many false positives)
 - Import checks of various kinds (Reason: Unreliable)
 - Regexp scanning (Reason: Pedantic and inaccurate)
 - "Missing @return tag" (Reason: We don't explicitly document void returns)
 - "Unhandled exception" / "Redundant catch clause" / "Missing @throws tag" (Reason: We don't force exceptions to be handled in the caller function)
 - Duplicate code detection (Reason: The portions of code detected as duplicated are often too small to handle differently)
 > (Many, lots of false positives in here that we will not mention)
(Excellent tool to use if you are happy to wade through false positives)

PHP CodeSniffer:
 > The CQC can chain a connection to the popular PHP CodeSniffer package, for additional checks. These checks capture more detailed coding standards errors, like putting spaces or line-breaks in the wrong place

Zend Studio's Code Analyzer (Problems):
 - "Assignment in condition" (Reason: The CQC doesn't have a problem with assignments as conditions, but it does something similar - separates the concept of expressions and commands; a command must be bracketed to become an expression)
 - Linting of PHP functions in extensions (Reason: We don't try and define what extension functions exist, it would be too much)
 > (Buggy, but has some limited use)

CodeLobster IDE's Code Validator:
 > (CodeLobster's Code Validator is very limited, not worth using)

NetBeans Inspect (Source -> Inspect):
 > (Basic parsing only, unless 3rd party tools are configured)

PHPStan:
 - Generics (Reason: We do not use them in Composr, we are waiting until we can properly use union types and don't plan anything more complex)
 - Property type and scope validation (Reason: We do not use many properties and we don't define their data types: it would be very time consuming to implement)
 - Always false instanceof (Reason: Very specific and unlikely)
 - Identifying unused return values (Reason: We'd have to flag which function return values are relevant, which would be a lot of overhead; instead we have a manual check for functions where there's a non-boolean/non-mixed return value that is not used)
 > PHPStan will not execute, as it makes assumptions about how autoloading/require's work, incompatible with Composr

Other tools, that are worth just mentioning for completeness:
 - [tt]php -l[/tt]
 - PHP Mess Detector
 - PHP CS Fixer
 - PHP copy and paste detector
 - Exakat (a big complex system, semi-commercial)
 - PHP Insights (pedantic)
 - Phan (very strict type inference)
 - Psalm (very strict type inference)
 - HHVM linting (no longer supports PHP)

Relationship with Composr
--------------------------

We have decided just to fully integrate the CQC as a component of Composr, as we want to streamline development and do more-opinionated checks. Quality standards chosen and enforced are very Composr-centric now.

PHP: handling of return values
------------------------------

PHP gives errors via one of a few methods:
 - Outputted errors (E_NOTICE, E_WARNING, E_ERROR, etc)
 - Error returning functions (e.g. mysql_error)
 - Return values (usually in concert with outputted errors or error returning functions)
 - Error throwing (since PHP5)

Errors being outputted is by far the most common. Unfortunately it's rather difficult to handle these errors programmatically, and in many cases they should be because they could relate to external influences such as corrupt input data. The easy way to handle it is to put "@" in front of the command and then to check if the return value was false, but this leaves ambiguity to what the error actually was. A better way is to use error_get_last() to get the value.

If the PHP checker is asked to do 'checks' then it will check against a number of functions that should either be "@'d" or wrapped with a custom error handler. In the cases when there is no @ing, a warning will be shown stating that the wrapping should happen.

The following cases demonstrate when for which functions the PHP checker will require custom error handling:
 - When we need to catch system-configuration problems (e.g. bad tmp path permissions, mail not set up)
 - When we need to catch problems with untrustable dependencies (e.g. corrupt file , or even corrupt database structure)
 - BUT NOT just to catch things like type errors -- it is assumed typing is correct, and typically this can be picked up by the checker before the code is even run

In addition, the PHP checker will always check that when functions are called that return 'false' upon error, that these values are treated as booleans in at least one point of succeeding code -- in other words, it makes sure you're checking error-sensitive return-values for false.

Style checks
------------

The checker will check Composr style conventions, like correct layout of tabs and braces. You will get errors relating to 'tabbing' or 'bracing' if you don't lay them out correctly.

Pedantic checks, and more
-------------------------

There are various extra checks you can turn on to make the checker give out extra information, to help you find potential problems. You might want to run it on your code after a weeks work, to do a quick check to make sure you didn't forget to check things such as security. You don't need to fix these kind of extra checks (some are just pointers asking you to manually check things).
