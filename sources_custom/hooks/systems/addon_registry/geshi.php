<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    geshi
 */

/**
 * Hook class.
 */
class Hook_addon_registry_geshi
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.1'; // addon_version_auto_update 4924f919fcc9d9093632c07ebd1fa8da
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Information Display';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'The Authors of GeSHi',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'GPL';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Apply syntax highlighting to a block of coding which is pasted inside the Comcode [tt]code[/tt] tag as follows:

[code="Comcode"][codebox="language"]the code goes here[/codebox][/code]

Based off of [url="https://github.com/GeSHi/geshi-1.0"]GeSHI 1.0[/url]. GeSHI 1.1 is still under active development at the time of writing, missing many highlighters present in 1.0.
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/geshi.php',
            'sources_custom/geshi/.gitignore',
            'sources_custom/geshi/.htaccess',
            'sources_custom/geshi/4cs.php',
            'sources_custom/geshi/6502acme.php',
            'sources_custom/geshi/6502kickass.php',
            'sources_custom/geshi/6502tasm.php',
            'sources_custom/geshi/68000devpac.php',
            'sources_custom/geshi/abap.php',
            'sources_custom/geshi/actionscript.php',
            'sources_custom/geshi/actionscript3.php',
            'sources_custom/geshi/ada.php',
            'sources_custom/geshi/aimms.php',
            'sources_custom/geshi/algol68.php',
            'sources_custom/geshi/apache.php',
            'sources_custom/geshi/applescript.php',
            'sources_custom/geshi/apt_sources.php',
            'sources_custom/geshi/arm.php',
            'sources_custom/geshi/asm.php',
            'sources_custom/geshi/asp.php',
            'sources_custom/geshi/asymptote.php',
            'sources_custom/geshi/autoconf.php',
            'sources_custom/geshi/autohotkey.php',
            'sources_custom/geshi/autoit.php',
            'sources_custom/geshi/avisynth.php',
            'sources_custom/geshi/awk.php',
            'sources_custom/geshi/bascomavr.php',
            'sources_custom/geshi/bash.php',
            'sources_custom/geshi/basic4gl.php',
            'sources_custom/geshi/batch.php',
            'sources_custom/geshi/bf.php',
            'sources_custom/geshi/biblatex.php',
            'sources_custom/geshi/bibtex.php',
            'sources_custom/geshi/blitzbasic.php',
            'sources_custom/geshi/bnf.php',
            'sources_custom/geshi/boo.php',
            'sources_custom/geshi/c.php',
            'sources_custom/geshi/c_loadrunner.php',
            'sources_custom/geshi/c_mac.php',
            'sources_custom/geshi/c_winapi.php',
            'sources_custom/geshi/caddcl.php',
            'sources_custom/geshi/cadlisp.php',
            'sources_custom/geshi/ceylon.php',
            'sources_custom/geshi/cfdg.php',
            'sources_custom/geshi/cfm.php',
            'sources_custom/geshi/chaiscript.php',
            'sources_custom/geshi/chapel.php',
            'sources_custom/geshi/cil.php',
            'sources_custom/geshi/clojure.php',
            'sources_custom/geshi/cmake.php',
            'sources_custom/geshi/cobol.php',
            'sources_custom/geshi/coffeescript.php',
            'sources_custom/geshi/cpp-qt.php',
            'sources_custom/geshi/cpp-winapi.php',
            'sources_custom/geshi/cpp.php',
            'sources_custom/geshi/csharp.php',
            'sources_custom/geshi/css.php',
            'sources_custom/geshi/cuesheet.php',
            'sources_custom/geshi/d.php',
            'sources_custom/geshi/dart.php',
            'sources_custom/geshi/dcl.php',
            'sources_custom/geshi/dcpu16.php',
            'sources_custom/geshi/dcs.php',
            'sources_custom/geshi/delphi.php',
            'sources_custom/geshi/diff.php',
            'sources_custom/geshi/div.php',
            'sources_custom/geshi/dos.php',
            'sources_custom/geshi/dot.php',
            'sources_custom/geshi/e.php',
            'sources_custom/geshi/ecmascript.php',
            'sources_custom/geshi/eiffel.php',
            'sources_custom/geshi/email.php',
            'sources_custom/geshi/epc.php',
            'sources_custom/geshi/erlang.php',
            'sources_custom/geshi/euphoria.php',
            'sources_custom/geshi/ezt.php',
            'sources_custom/geshi/f1.php',
            'sources_custom/geshi/falcon.php',
            'sources_custom/geshi/fo.php',
            'sources_custom/geshi/fortran.php',
            'sources_custom/geshi/freebasic.php',
            'sources_custom/geshi/freeswitch.php',
            'sources_custom/geshi/fsharp.php',
            'sources_custom/geshi/gambas.php',
            'sources_custom/geshi/gdb.php',
            'sources_custom/geshi/genero.php',
            'sources_custom/geshi/genie.php',
            'sources_custom/geshi/gettext.php',
            'sources_custom/geshi/glsl.php',
            'sources_custom/geshi/gml.php',
            'sources_custom/geshi/gnuplot.php',
            'sources_custom/geshi/go.php',
            'sources_custom/geshi/groovy.php',
            'sources_custom/geshi/gwbasic.php',
            'sources_custom/geshi/haskell.php',
            'sources_custom/geshi/haxe.php',
            'sources_custom/geshi/hicest.php',
            'sources_custom/geshi/hq9plus.php',
            'sources_custom/geshi/html4strict.php',
            'sources_custom/geshi/html5.php',
            'sources_custom/geshi/icon.php',
            'sources_custom/geshi/idl.php',
            'sources_custom/geshi/index.html',
            'sources_custom/geshi/ini.php',
            'sources_custom/geshi/inno.php',
            'sources_custom/geshi/intercal.php',
            'sources_custom/geshi/io.php',
            'sources_custom/geshi/ispfpanel.php',
            'sources_custom/geshi/j.php',
            'sources_custom/geshi/java.php',
            'sources_custom/geshi/java5.php',
            'sources_custom/geshi/javascript.php',
            'sources_custom/geshi/jcl.php',
            'sources_custom/geshi/jquery.php',
            'sources_custom/geshi/julia.php',
            'sources_custom/geshi/kixtart.php',
            'sources_custom/geshi/klonec.php',
            'sources_custom/geshi/klonecpp.php',
            'sources_custom/geshi/kotlin.php',
            'sources_custom/geshi/latex.php',
            'sources_custom/geshi/lb.php',
            'sources_custom/geshi/ldif.php',
            'sources_custom/geshi/lisp.php',
            'sources_custom/geshi/llvm.php',
            'sources_custom/geshi/locobasic.php',
            'sources_custom/geshi/logtalk.php',
            'sources_custom/geshi/lolcode.php',
            'sources_custom/geshi/lotusformulas.php',
            'sources_custom/geshi/lotusscript.php',
            'sources_custom/geshi/lscript.php',
            'sources_custom/geshi/lsl2.php',
            'sources_custom/geshi/lua.php',
            'sources_custom/geshi/m68k.php',
            'sources_custom/geshi/magiksf.php',
            'sources_custom/geshi/make.php',
            'sources_custom/geshi/mapbasic.php',
            'sources_custom/geshi/mathematica.php',
            'sources_custom/geshi/matlab.php',
            'sources_custom/geshi/mercury.php',
            'sources_custom/geshi/metapost.php',
            'sources_custom/geshi/mirc.php',
            'sources_custom/geshi/mk-61.php',
            'sources_custom/geshi/mmix.php',
            'sources_custom/geshi/modula2.php',
            'sources_custom/geshi/modula3.php',
            'sources_custom/geshi/mpasm.php',
            'sources_custom/geshi/mxml.php',
            'sources_custom/geshi/mysql.php',
            'sources_custom/geshi/nagios.php',
            'sources_custom/geshi/netrexx.php',
            'sources_custom/geshi/newlisp.php',
            'sources_custom/geshi/nginx.php',
            'sources_custom/geshi/nimrod.php',
            'sources_custom/geshi/nsis.php',
            'sources_custom/geshi/oberon2.php',
            'sources_custom/geshi/objc.php',
            'sources_custom/geshi/objeck.php',
            'sources_custom/geshi/ocaml-brief.php',
            'sources_custom/geshi/ocaml.php',
            'sources_custom/geshi/octave.php',
            'sources_custom/geshi/oobas.php',
            'sources_custom/geshi/oorexx.php',
            'sources_custom/geshi/oracle11.php',
            'sources_custom/geshi/oracle8.php',
            'sources_custom/geshi/oxygene.php',
            'sources_custom/geshi/oz.php',
            'sources_custom/geshi/parasail.php',
            'sources_custom/geshi/parigp.php',
            'sources_custom/geshi/pascal.php',
            'sources_custom/geshi/pcre.php',
            'sources_custom/geshi/per.php',
            'sources_custom/geshi/perl.php',
            'sources_custom/geshi/perl6.php',
            'sources_custom/geshi/pf.php',
            'sources_custom/geshi/phix.php',
            'sources_custom/geshi/php-brief.php',
            'sources_custom/geshi/php.php',
            'sources_custom/geshi/pic16.php',
            'sources_custom/geshi/pike.php',
            'sources_custom/geshi/pixelbender.php',
            'sources_custom/geshi/pli.php',
            'sources_custom/geshi/plsql.php',
            'sources_custom/geshi/postgresql.php',
            'sources_custom/geshi/postscript.php',
            'sources_custom/geshi/povray.php',
            'sources_custom/geshi/powerbuilder.php',
            'sources_custom/geshi/powershell.php',
            'sources_custom/geshi/proftpd.php',
            'sources_custom/geshi/progress.php',
            'sources_custom/geshi/prolog.php',
            'sources_custom/geshi/properties.php',
            'sources_custom/geshi/providex.php',
            'sources_custom/geshi/purebasic.php',
            'sources_custom/geshi/pycon.php',
            'sources_custom/geshi/pys60.php',
            'sources_custom/geshi/python.php',
            'sources_custom/geshi/q.php',
            'sources_custom/geshi/qbasic.php',
            'sources_custom/geshi/qml.php',
            'sources_custom/geshi/racket.php',
            'sources_custom/geshi/rails.php',
            'sources_custom/geshi/rbs.php',
            'sources_custom/geshi/rebol.php',
            'sources_custom/geshi/reg.php',
            'sources_custom/geshi/rexx.php',
            'sources_custom/geshi/robots.php',
            'sources_custom/geshi/roff.php',
            'sources_custom/geshi/rpmspec.php',
            'sources_custom/geshi/rsplus.php',
            'sources_custom/geshi/ruby.php',
            'sources_custom/geshi/rust.php',
            'sources_custom/geshi/sas.php',
            'sources_custom/geshi/sass.php',
            'sources_custom/geshi/scala.php',
            'sources_custom/geshi/scheme.php',
            'sources_custom/geshi/scilab.php',
            'sources_custom/geshi/scl.php',
            'sources_custom/geshi/sdlbasic.php',
            'sources_custom/geshi/smalltalk.php',
            'sources_custom/geshi/smarty.php',
            'sources_custom/geshi/spark.php',
            'sources_custom/geshi/sparql.php',
            'sources_custom/geshi/sql.php',
            'sources_custom/geshi/sshconfig.php',
            'sources_custom/geshi/standardml.php',
            'sources_custom/geshi/stonescript.php',
            'sources_custom/geshi/swift.php',
            'sources_custom/geshi/systemverilog.php',
            'sources_custom/geshi/tcl.php',
            'sources_custom/geshi/tclegg.php',
            'sources_custom/geshi/teraterm.php',
            'sources_custom/geshi/texgraph.php',
            'sources_custom/geshi/text.php',
            'sources_custom/geshi/thinbasic.php',
            'sources_custom/geshi/tsql.php',
            'sources_custom/geshi/twig.php',
            'sources_custom/geshi/typoscript.php',
            'sources_custom/geshi/unicon.php',
            'sources_custom/geshi/upc.php',
            'sources_custom/geshi/urbi.php',
            'sources_custom/geshi/uscript.php',
            'sources_custom/geshi/vala.php',
            'sources_custom/geshi/vb.php',
            'sources_custom/geshi/vbnet.php',
            'sources_custom/geshi/vbscript.php',
            'sources_custom/geshi/vedit.php',
            'sources_custom/geshi/verilog.php',
            'sources_custom/geshi/vhdl.php',
            'sources_custom/geshi/vim.php',
            'sources_custom/geshi/visualfoxpro.php',
            'sources_custom/geshi/visualprolog.php',
            'sources_custom/geshi/whitespace.php',
            'sources_custom/geshi/whois.php',
            'sources_custom/geshi/winbatch.php',
            'sources_custom/geshi/wolfram.php',
            'sources_custom/geshi/xbasic.php',
            'sources_custom/geshi/xml.php',
            'sources_custom/geshi/xojo.php',
            'sources_custom/geshi/xorg_conf.php',
            'sources_custom/geshi/xpp.php',
            'sources_custom/geshi/yaml.php',
            'sources_custom/geshi/z80.php',
            'sources_custom/geshi/zxbasic.php',
            'sources_custom/hooks/systems/addon_registry/geshi.php',
        ];
    }
}
