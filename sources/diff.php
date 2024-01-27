<?php

/*NO_API_CHECK*/
/*CQC: No check*/

// Based on PHP:Text_Diff, which is under the LGPL licence.
//
// Modified for Composr. This file remains LGPL.

/**
 * @license    LGPL
 * @package    core
 */

function diff_simple($old_file, $new_file, $unified = false)
{
    return _diff_simple(file($old_file), file($new_file), $unified);
}

function diff_simple_2($old_contents, $new_contents, $unified = false)
{
    return _diff_simple(($old_contents == '') ? array() : explode("\n", $old_contents), ($new_contents == '') ? array() : explode("\n", $new_contents), $unified);
}

function _diff_simple($old, $new, $unified = false)
{
    $diff = new Text_Diff($old, $new);
    if ($unified) {
        $renderer = new Text_Diff_Renderer_unified();
        $diff_text = $rendered_diff = $renderer->render($diff);
        $diff_html = '';
        foreach (explode("\n", $diff_text) as $diff_line) {
            switch (substr($diff_line, 0, 1)) {
                case '+':
                    $diff_html .= '<span style="color: green">' . escape_html($diff_line) . '</span>';
                    break;
                case '-':
                    $diff_html .= '<span style="color: red">' . escape_html($diff_line) . '</span>';
                    break;
                default:
                    $diff_html .= escape_html($diff_line);
                    break;
            }
            $diff_html .= '<br />';
        }
    } else {
        $renderer = new Text_Diff_Renderer_inline();
        $diff_html = $rendered_diff = $renderer->render($diff);
    }
    if ($GLOBALS['XSS_DETECT']) {
        ocp_mark_as_escaped($diff_html);
    }
    return $diff_html;
}

function diff_compute_new($file_1, $file_2, $file_3)
{
    /* Load the lines of each file. */
    $lines1 = file($file_1);
    $lines2 = file($file_2);
    $lines3 = file($file_3);

    /* Create the Diff object. */
    $diff = new Text_Diff3($lines1, $lines2, $lines3);
    //print_r($diff);
    //exit();
    //$renderer=new Text_Diff_Renderer_unified();
    //echo $renderer->render($diff);

    $new = '';
    foreach ($diff->_edits as $ob) {
        $orig = implode("\n", $ob->orig);
        $final1 = implode("\n", $ob->final1);
        $final2 = implode("\n", $ob->final2);
        if (preg_replace('#\s#', '', $orig) != preg_replace('#\s#', '', $final1)) {
            $new .= $orig;
            $new .= "\n";
        } else {
            $new .= $final2;
            $new .= "\n";
        }
    }

    return $new;
}

/**
 * Text_Diff
 *
 * General API for generating and formatting diffs - the differences between
 * two sequences of strings.
 *
 * The PHP diff code used in this package was originally written by Geoffrey
 * T. Dairiki and is used with his permission.
 *
 * $Horde: framework/Text_Diff/Diff.php,v 1.17 2006/02/06 00:16:09 jan Exp $
 *
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 */
class Text_Diff
{

    /**
     * Array of changes.
     *
     * @var array
     */
    var $_edits;

    /**
     * Computes diffs between sequences of strings.
     *
     * @param array $from_lines An array of strings.  Typically these are
     *                                    lines from a file.
     * @param array $to_lines An array of strings.
     */
    function __construct($engine, $params)
    {
        // Backward compatibility workaround.
        if (!is_string($engine)) {
            $params = array($engine, $params);
            $engine = 'auto';
        }

        if ($engine == 'auto') {
            $engine = 'native';
        }
        $engine = basename($engine);

        //require_once 'Text/Diff/Engine/' . $engine . '.php';
        $class = 'Text_Diff_Engine_' . $engine;
        $diff_engine = new $class;
        $this->_edits = call_user_func_array(array($diff_engine, 'diff'), $params);
    }

    /**
     * Returns the array of differences.
     */
    function getDiff()
    {
        return $this->_edits;
    }

    /**
     * Computes a reversed diff.
     *
     * Example:
     * <code>
     * $diff=new Text_Diff($lines1, $lines2);
     * $rev=$diff->reverse();
     * </code>
     *
     * @return Text_Diff A Diff object representing the inverse of the
     *                        original diff.  Note that we purposely don't return a
     *                        reference here, since this essentially is a clone()
     *                        method.
     */
    function &reverse()
    {
        if (version_compare(zend_version(), '2', '>')) {
            $rev = clone($this);
        } else {
            $rev = $this;
        }
        $rev->_edits = array();
        foreach ($this->_edits as $edit) {
            $rev->_edits[] = $edit->reverse();
        }
        return $rev;
    }

    /**
     * Checks for an empty diff.
     *
     * @return boolean True if two sequences were identical.
     */
    function isEmpty()
    {
        foreach ($this->_edits as $edit) {
            if (!is_a($edit, 'Text_Diff_Op_copy')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Computes the length of the Longest Common Subsequence (LCS).
     *
     * This is mostly for diagnostic purposes.
     *
     * @return integer The length of the LCS.
     */
    function lcs()
    {
        $lcs = 0;
        foreach ($this->_edits as $edit) {
            if (is_a($edit, 'Text_Diff_Op_copy')) {
                $lcs += count($edit->orig);
            }
        }
        return $lcs;
    }

    /**
     * Gets the original set of lines.
     *
     * This reconstructs the $from_lines parameter passed to the constructor.
     *
     * @return array The original sequence of strings.
     */
    function getOriginal()
    {
        $lines = array();
        foreach ($this->_edits as $edit) {
            if ($edit->orig) {
                array_splice($lines, count($lines), 0, $edit->orig);
            }
        }
        return $lines;
    }

    /**
     * Gets the final set of lines.
     *
     * This reconstructs the $to_lines parameter passed to the constructor.
     *
     * @return array The sequence of strings.
     */
    function getFinal()
    {
        $lines = array();
        foreach ($this->_edits as $edit) {
            if ($edit->final) {
                array_splice($lines, count($lines), 0, $edit->final);
            }
        }
        return $lines;
    }

    /**
     * Removes trailing newlines from a line of text. This is meant to be used
     * with array_walk().
     *
     * @param string $line The line to trim.
     * @param integer $key The index of the line in the array. Not used.
     */
    static function trimNewlines(&$line, $key)
    {
        $line = str_replace(array("\n", "\r"), '', $line);
    }

    /**
     * Checks a diff for validity.
     *
     * This is here only for debugging purposes.
     */
    function _check($from_lines, $to_lines)
    {
        if (serialize($from_lines) != serialize($this->getOriginal())) {
            trigger_error("Reconstructed original doesn't match", E_USER_ERROR);
        }
        if (serialize($to_lines) != serialize($this->getFinal())) {
            trigger_error("Reconstructed final doesn't match", E_USER_ERROR);
        }

        $rev = $this->reverse();
        if (serialize($to_lines) != serialize($rev->getOriginal())) {
            trigger_error("Reversed original doesn't match", E_USER_ERROR);
        }
        if (serialize($from_lines) != serialize($rev->getFinal())) {
            trigger_error("Reversed final doesn't match", E_USER_ERROR);
        }

        $prevtype = null;
        foreach ($this->_edits as $edit) {
            if ($prevtype == get_class($edit)) {
                trigger_error("Edit sequence is non-optimal", E_USER_ERROR);
            }
            $prevtype = get_class($edit);
        }

        return true;
    }
}

/**
 * $Horde: framework/Text_Diff/Diff.php,v 1.17 2006/02/06 00:16:09 jan Exp $
 *
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 */
class Text_MappedDiff extends Text_Diff
{

    /**
     * Computes a diff between sequences of strings.
     *
     * This can be used to compute things like case-insensitve diffs, or diffs
     * which ignore changes in white-space.
     *
     * @param array $from_lines An array of strings.
     * @param array $to_lines An array of strings.
     * @param array $mapped_from_lines This array should have the same size
     *                                            number of elements as $from_lines.  The
     *                                            elements in $mapped_from_lines and
     *                                            $mapped_to_lines are what is actually
     *                                            compared when computing the diff.
     * @param array $mapped_to_lines This array should have the same number
     *                                            of elements as $to_lines.
     */
    function __construct($from_lines, $to_lines,
                             $mapped_from_lines, $mapped_to_lines)
    {
//		assert(count($from_lines)==count($mapped_from_lines));
//		assert(count($to_lines)==count($mapped_to_lines));

        parent::Text_Diff($mapped_from_lines, $mapped_to_lines);

        $xi = 0;
        $yi = 0;
        for ($i = 0; $i < count($this->_edits); $i++) {
            $orig =& $this->_edits[$i]->orig;
            if (is_array($orig)) {
                $orig = array_slice($from_lines, $xi, count($orig));
                $xi += count($orig);
            }

            $final =& $this->_edits[$i]->final;
            if (is_array($final)) {
                $final = array_slice($to_lines, $yi, count($final));
                $yi += count($final);
            }
        }
    }
}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op
{

    var $orig;
    var $final;

    function &reverse()
    {
        trigger_error('Abstract method', E_USER_ERROR);

        $x = null;
        return $x; // For phalanger
    }

    function norig()
    {
        return $this->orig ? count($this->orig) : 0;
    }

    function nfinal()
    {
        return $this->final ? count($this->final) : 0;
    }
}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op_copy extends Text_Diff_Op
{

    function __construct($orig, $final = false)
    {
        if (!is_array($final)) {
            $final = $orig;
        }
        $this->orig = $orig;
        $this->final = $final;
    }

    function &reverse()
    {
        $reverse = new Text_Diff_Op_copy($this->final, $this->orig);
        return $reverse;
    }
}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op_delete extends Text_Diff_Op
{

    function __construct($lines)
    {
        $this->orig = $lines;
        $this->final = false;
    }

    function &reverse()
    {
        $reverse = new Text_Diff_Op_add($this->orig);
        return $reverse;
    }
}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op_add extends Text_Diff_Op
{

    function __construct($lines)
    {
        $this->final = $lines;
        $this->orig = false;
    }

    function &reverse()
    {
        $reverse = new Text_Diff_Op_delete($this->final);
        return $reverse;
    }
}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff_Op_change extends Text_Diff_Op
{

    function __construct($orig, $final)
    {
        $this->orig = $orig;
        $this->final = $final;
    }

    function &reverse()
    {
        $reverse = new Text_Diff_Op_change($this->final, $this->orig);
        return $reverse;
    }
}

/**
 * A class for computing three way diffs.
 *
 * $Horde: framework/Text_Diff/Diff3.php,v 1.4 2005/07/03 05:10:11 selsky Exp $
 *
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 */
class Text_Diff3 extends Text_Diff
{

    /**
     * Conflict counter.
     *
     * @var integer
     */
    var $_conflictingBlocks = 0;

    /**
     * Computes diff between 3 sequences of strings.
     *
     * @param array $orig The original lines to use.
     * @param array $final1 The first version to compare to.
     * @param array $final2 The second version to compare to.
     */
    function __construct($orig, $final1, $final2)
    {
        $engine = new Text_Diff_Engine_native();

        $this->_edits = $this->_diff3($engine->diff($orig, $final1),
            $engine->diff($orig, $final2));
    }

    function mergedOutput($label1 = false, $label2 = false)
    {
        $lines = array();
        foreach ($this->_edits as $edit) {
            if ($edit->isConflict()) {
                /* FIXME: this should probably be moved somewhere else. */
                $lines = array_merge($lines,
                    array('<<<' . '<<<' . '<' . ($label1 ? ' ' . $label1 : '')),
                    $edit->final1,
                    array("======="),
                    $edit->final2,
                    array('>>>' . '>>>>' . ($label2 ? ' ' . $label2 : '')));
                $this->_conflictingBlocks++;
            } else {
                $lines = array_merge($lines, $edit->merged());
            }
        }

        return $lines;
    }

    /**
     * @access private
     */
    function _diff3($edits1, $edits2)
    {
        $edits = array();
        $bb = new Text_Diff3_BlockBuilder();

        $e1 = current($edits1);
        $e2 = current($edits2);
        while ($e1 !== false || $e2 !== false) {
            if ($e1 !== false && $e2 !== false && is_a($e1, 'Text_Diff_Op_copy') && is_a($e2, 'Text_Diff_Op_copy')) {
                /* We have copy blocks from both diffs. This is the (only)
                * time we want to emit a diff3 copy block.  Flush current
                * diff3 diff block, if any. */
                $edit = $bb->finish();
                if ($edit !== false) {
                    $edits[] = $edit;
                }

                $ncopy = min($e1->norig(), $e2->norig());
//					assert($ncopy > 0);
                $edits[] = new Text_Diff3_Op_copy(array_slice($e1->orig, 0, $ncopy));

                if ($e1->norig() > $ncopy) {
                    array_splice($e1->orig, 0, $ncopy);
                    array_splice($e1->final, 0, $ncopy);
                } else {
                    $e1 = next($edits1);
                }

                if ($e2->norig() > $ncopy) {
                    array_splice($e2->orig, 0, $ncopy);
                    array_splice($e2->final, 0, $ncopy);
                } else {
                    $e2 = next($edits2);
                }
            } else {
                if ($e1 !== false && $e2 !== false) {
                    if ($e1->orig && $e2->orig) {
                        $norig = min($e1->norig(), $e2->norig());
                        $orig = array_splice($e1->orig, 0, $norig);
                        array_splice($e2->orig, 0, $norig);
                        $bb->input($orig);
                    } else {
                        $norig = 0;
                    }

                    if (is_a($e1, 'Text_Diff_Op_copy')) {
                        $bb->out1(array_splice($e1->final, 0, $norig));
                    }

                    if (is_a($e2, 'Text_Diff_Op_copy')) {
                        $bb->out2(array_splice($e2->final, 0, $norig));
                    }
                }

                if ($e1 !== false && empty($e1->orig)) {
                    $bb->out1($e1->final);
                    $e1 = next($edits1);
                }
                if ($e2 !== false && empty($e2->orig)) {
                    $bb->out2($e2->final);
                    $e2 = next($edits2);
                }
            }
        }

        $edit = $bb->finish();
        if ($edit) {
            $edits[] = $edit;
        }

        return $edits;
    }
}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff3_Op
{

    var $_merged;
    var $final1;
    var $final2;
    var $orig;

    function __construct($orig = false, $final1 = false, $final2 = false)
    {
        $this->orig = $orig ? $orig : array();
        $this->final1 = $final1 ? $final1 : array();
        $this->final2 = $final2 ? $final2 : array();
    }

    function merged()
    {
        if (!isset($this->_merged)) {
            if ($this->final1 === $this->final2) {
                $this->_merged =& $this->final1;
            } elseif ($this->final1 === $this->orig) {
                $this->_merged =& $this->final2;
            } elseif ($this->final2 === $this->orig) {
                $this->_merged =& $this->final1;
            } else {
                $this->_merged = false;
            }
        }

        return $this->_merged;
    }

    function isConflict()
    {
        return $this->merged() === false;
    }
}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff3_Op_copy extends Text_Diff3_Op
{

    function __construct($lines = false)
    {
        $this->orig = $lines ? $lines : array();
        $this->final1 =& $this->orig;
        $this->final2 =& $this->orig;
    }

    function merged()
    {
        return $this->orig;
    }

    function isConflict()
    {
        return false;
    }
}

/**
 * @package Text_Diff
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 *
 * @access private
 */
class Text_Diff3_BlockBuilder
{

    function __construct()
    {
        $this->_init();
    }

    function input($lines)
    {
        if ($lines) {
            $this->_append($this->orig, $lines);
        }
    }

    function out1($lines)
    {
        if ($lines) {
            $this->_append($this->final1, $lines);
        }
    }

    function out2($lines)
    {
        if ($lines) {
            $this->_append($this->final2, $lines);
        }
    }

    function isEmpty()
    {
        return empty($this->orig) && empty($this->final1) && empty($this->final2);
    }

    function finish()
    {
        if ($this->isEmpty()) {
            return false;
        } else {
            $edit = new Text_Diff3_Op($this->orig, $this->final1, $this->final2);
            $this->_init();
            return $edit;
        }
    }

    function _init()
    {
        $this->orig = array();
        $this->final1 = array();
        $this->final2 = array();
    }

    function _append(&$array, $lines)
    {
        array_splice($array, count($array), 0, $lines);
    }
}

/**
 * Parses unified or context diffs output from eg. the diff utility.
 *
 * Example:
 * <code>
 * $patch=file_get_contents('example.patch');
 * $diff=new Text_Diff('string', array($patch));
 * $renderer=new Text_Diff_Renderer_inline();
 * echo $renderer->render($diff);
 * </code>
 *
 * @author    ֲjan Persson <o@42mm.org>
 * @copyright Copyright 2005 ֲjan Persson
 * @package    Text_Diff
 * @since    0.2.0
 * @access    private
 */
class Text_Diff_Engine_string
{

    /**
     * Parses a unified or context diff.
     *
     * First param contains the whole diff and the second can be used to force
     * a specific diff type. If the second parameter is 'autodetect', the
     * diff will be examined to find out which type of diff this is.
     *
     * @param string $diff The diff content.
     * @param string $mode The diff mode of the content in $diff. One of
     *                            'context', 'unified', or 'autodetect'.
     *
     * @return array List of all diff operations.
     */
    function diff($diff, $mode = 'autodetect')
    {
        if ($mode != 'autodetect' && $mode != 'context' && $mode != 'unified') {
            warn_exit('Type of diff is unsupported');
        }

        if ($mode == 'autodetect') {
            $context = strpos($diff, '***');
            $unified = strpos($diff, '---');
            if ($context === $unified) {
                warn_exit('Type of diff could not be detected');
            } elseif ($context === false || $unified === false) {
                $mode = $context !== false ? 'context' : 'unified';
            } else {
                $mode = $context < $unified ? 'context' : 'unified';
            }
        }

        // split by new line and remove the diff header
        $diff = explode("\n", $diff);
        array_shift($diff);
        array_shift($diff);

        if ($mode == 'context') {
            return $this->parseContextDiff($diff);
        } else {
            return $this->parseUnifiedDiff($diff);
        }
    }

    /**
     * Parses an array containing the unified diff.
     *
     * @param array $diff Array of lines.
     *
     * @return array List of all diff operations.
     */
    function parseUnifiedDiff($diff)
    {
        $edits = array();
        $end = count($diff) - 1;
        $i = 0;
        while ($i < $end) {
            $diff1 = array();
            switch (substr($diff[$i], 0, 1)) {
                case ' ':
                    do {
                        $diff1[] = substr($diff[$i], 1);
                    } while (++$i < $end && substr($diff[$i], 0, 1) == ' ');
                    $edits[] = new Text_Diff_Op_copy($diff1);
                    break;
                case '+':
                    // get all new lines
                    do {
                        $diff1[] = substr($diff[$i], 1);
                    } while (++$i < $end && substr($diff[$i], 0, 1) == '+');
                    $edits[] = new Text_Diff_Op_add($diff1);
                    break;
                case '-':
                    // get changed or removed lines
                    $diff2 = array();
                    do {
                        $diff1[] = substr($diff[$i], 1);
                    } while (++$i < $end && substr($diff[$i], 0, 1) == '-');
                    while ($i < $end && substr($diff[$i], 0, 1) == '+') {
                        $diff2[] = substr($diff[$i++], 1);
                    }
                    if (count($diff2) == 0) {
                        $edits[] = new Text_Diff_Op_delete($diff1);
                    } else {
                        $edits[] = new Text_Diff_Op_change($diff1, $diff2);
                    }
                    break;
                default:
                    $i++;
                    break;
            }
        }
        return $edits;
    }

    /**
     * Parses an array containing the context diff.
     *
     * @param array $diff Array of lines.
     *
     * @return array List of all diff operations.
     */
    function parseContextDiff(&$diff)
    {
        $edits = array();
        $i = $max_i = $j = $max_j = 0;
        $end = count($diff) - 1;
        while ($i < $end && $j < $end) {
            while ($i >= $max_i && $j >= $max_j) {
                // find the boundaries of the diff output of the two files
                for ($i = $j;
                     $i < $end && substr($diff[$i], 0, 3) == '***';
                     $i++) {
                    ;
                }
                for ($max_i = $i;
                     $max_i < $end && substr($diff[$max_i], 0, 3) != '---';
                     $max_i++) {
                    ;
                }
                for ($j = $max_i;
                     $j < $end && substr($diff[$j], 0, 3) == '---';
                     $j++) {
                    ;
                }
                for ($max_j = $j;
                     $max_j < $end && substr($diff[$max_j], 0, 3) != '***';
                     $max_j++) {
                    ;
                }
            }

            // find what hasn't been changed
            $array = array();
            while ($i < $max_i &&
                   $j < $max_j &&
                   strcmp($diff[$i], $diff[$j]) == 0) {
                $array[] = substr($diff[$i], 2);
                $i++;
                $j++;
            }
            while ($i < $max_i && ($max_j - $j) <= 1) {
                if ($diff[$i] != '' && substr($diff[$i], 0, 1) != ' ') {
                    break;
                }
                $array[] = substr($diff[$i++], 2);
            }
            while ($j < $max_j && ($max_i - $i) <= 1) {
                if ($diff[$j] != '' && substr($diff[$j], 0, 1) != ' ') {
                    break;
                }
                $array[] = substr($diff[$j++], 2);
            }
            if (count($array) > 0) {
                $edits[] = new Text_Diff_Op_copy($array);
            }

            if ($i < $max_i) {
                $diff1 = array();
                switch (substr($diff[$i], 0, 1)) {
                    case '!':
                        $diff2 = array();
                        do {
                            $diff1[] = substr($diff[$i], 2);
                            if ($j < $max_j && substr($diff[$j], 0, 1) == '!') {
                                $diff2[] = substr($diff[$j++], 2);
                            }
                        } while (++$i < $max_i && substr($diff[$i], 0, 1) == '!');
                        $edits[] = new Text_Diff_Op_change($diff1, $diff2);
                        break;
                    case '+':
                        do {
                            $diff1[] = substr($diff[$i], 2);
                        } while (++$i < $max_i && substr($diff[$i], 0, 1) == '+');
                        $edits[] = new Text_Diff_Op_add($diff1);
                        break;
                    case '-':
                        do {
                            $diff1[] = substr($diff[$i], 2);
                        } while (++$i < $max_i && substr($diff[$i], 0, 1) == '-');
                        $edits[] = new Text_Diff_Op_delete($diff1);
                        break;
                }
            }

            if ($j < $max_j) {
                $diff2 = array();
                switch (substr($diff[$j], 0, 1)) {
                    case '+':
                        do {
                            $diff2[] = substr($diff[$j++], 2);
                        } while ($j < $max_j && substr($diff[$j], 0, 1) == '+');
                        $edits[] = new Text_Diff_Op_add($diff2);
                        break;
                    case '-':
                        do {
                            $diff2[] = substr($diff[$j++], 2);
                        } while ($j < $max_j && substr($diff[$j], 0, 1) == '-');
                        $edits[] = new Text_Diff_Op_delete($diff2);
                        break;
                }
            }
        }
        return $edits;
    }
}

/**
 * Class used internally by Diff to actually compute the diffs.  This class is
 * implemented using native PHP code.
 *
 * The algorithm used here is mostly lifted from the perl module
 * Algorithm::Diff (version 1.06) by Ned Konz, which is available at:
 * http://www.perl.com/CPAN/authors/id/N/NE/NEDKONZ/Algorithm-Diff-1.06.zip
 *
 * More ideas are taken from:
 * http://www.ics.uci.edu/~eppstein/161/960229.html
 *
 * Some ideas (and a bit of code) are taken from analyze.c, of GNU
 * diffutils-2.7, which can be found at:
 * ftp://gnudist.gnu.org/pub/gnu/diffutils/diffutils-2.7.tar.gz
 *
 * Some ideas (subdivision by NCHUNKS > 2, and some optimizations) are from
 * Geoffrey T. Dairiki <dairiki@dairiki.org>. The original PHP version of this
 * code was written by him, and is used/adapted with his permission.
 *
 * $Horde: framework/Text_Diff/Diff/Engine/native.php,v 1.3 2006/01/06 15:56:52 jan Exp $
 *
 * @author  Geoffrey T. Dairiki <dairiki@dairiki.org>
 * @package Text_Diff
 *
 * @access private
 */
class Text_Diff_Engine_native
{

    var $seq;
    var $in_seq;
    var $lcs;
    var $xchanged;
    var $ychanged;
    var $xind;
    var $xv;
    var $yind;
    var $yv;

    function diff($from_lines, $to_lines)
    {
        @array_walk($from_lines, array('Text_Diff', 'trimNewlines'));
        @array_walk($to_lines, array('Text_Diff', 'trimNewlines'));

        $n_from = count($from_lines);
        $n_to = count($to_lines);

        $this->xchanged = $this->ychanged = array();
        $this->xv = $this->yv = array();
        $this->xind = $this->yind = array();
        unset($this->seq);
        unset($this->in_seq);
        unset($this->lcs);

        // Skip leading common lines.
        for ($skip = 0; $skip < $n_from && $skip < $n_to; $skip++) {
            if ($from_lines[$skip] !== $to_lines[$skip]) {
                break;
            }
            $this->xchanged[$skip] = $this->ychanged[$skip] = false;
        }

        // Skip trailing common lines.
        $xi = $n_from;
        $yi = $n_to;
        for ($endskip = 0; --$xi > $skip && --$yi > $skip; $endskip++) {
            if ($from_lines[$xi] !== $to_lines[$yi]) {
                break;
            }
            $this->xchanged[$xi] = $this->ychanged[$yi] = false;
        }

        // Ignore lines which do not exist in both files.
        for ($xi = $skip; $xi < $n_from - $endskip; $xi++) {
            $xhash[$from_lines[$xi]] = 1;
        }
        for ($yi = $skip; $yi < $n_to - $endskip; $yi++) {
            $line = $to_lines[$yi];
            if (($this->ychanged[$yi] = empty($xhash[$line]))) {
                continue;
            }
            $yhash[$line] = 1;
            $this->yv[] = $line;
            $this->yind[] = $yi;
        }
        for ($xi = $skip; $xi < $n_from - $endskip; $xi++) {
            $line = $from_lines[$xi];
            if (($this->xchanged[$xi] = empty($yhash[$line]))) {
                continue;
            }
            $this->xv[] = $line;
            $this->xind[] = $xi;
        }

        // Find the LCS.
        $this->_compareseq(0, count($this->xv), 0, count($this->yv));

        // Merge edits when possible.
        $this->_shiftBoundaries($from_lines, $this->xchanged, $this->ychanged);
        $this->_shiftBoundaries($to_lines, $this->ychanged, $this->xchanged);

        // Compute the edit operations.
        $edits = array();
        $xi = $yi = 0;
        while ($xi < $n_from || $yi < $n_to) {
//			assert($yi < $n_to || $this->xchanged[$xi]);
//			assert($xi < $n_from || $this->ychanged[$yi]);

            // Skip matching "snake".
            $copy = array();
            while ($xi < $n_from && $yi < $n_to
                   && @!$this->xchanged[$xi] && @!$this->ychanged[$yi]) {
                $copy[] = $from_lines[$xi++];
                ++$yi;
            }
            if ($copy) {
                $edits[] = new Text_Diff_Op_copy($copy);
            }

            // Find deletes & adds.
            $delete = array();
            while ($xi < $n_from && $this->xchanged[$xi]) {
                $delete[] = $from_lines[$xi++];
            }

            $add = array();
            while ($yi < $n_to && $this->ychanged[$yi]) {
                $add[] = $to_lines[$yi++];
            }

            if ($delete && $add) {
                $edits[] = new Text_Diff_Op_change($delete, $add);
            } elseif ($delete) {
                $edits[] = new Text_Diff_Op_delete($delete);
            } elseif ($add) {
                $edits[] = new Text_Diff_Op_add($add);
            }
        }

        return $edits;
    }

    /**
     * Divides the Largest Common Subsequence (LCS) of the sequences (XOFF,
     * XLIM) and (YOFF, YLIM) into NCHUNKS approximately equally sized
     * segments.
     *
     * Returns (LCS, PTS).  LCS is the length of the LCS. PTS is an array of
     * NCHUNKS+1 (X, Y) indexes giving the diving points between sub
     * sequences.  The first sub-sequence is contained in (X0, X1), (Y0, Y1),
     * the second in (X1, X2), (Y1, Y2) and so on.  Note that (X0, Y0) ==
     * (XOFF, YOFF) and (X[NCHUNKS], Y[NCHUNKS])==(XLIM, YLIM).
     *
     * This function assumes that the first lines of the specified portions of
     * the two files do not match, and likewise that the last lines do not
     * match.  The caller must trim matching lines from the beginning and end
     * of the portions it is going to specify.
     */
    function _diag($xoff, $xlim, $yoff, $ylim, $nchunks)
    {
        $flip = false;

        if ($xlim - $xoff > $ylim - $yoff) {
            /* Things seems faster (I'm not sure I understand why) when the
            * shortest sequence is in X. */
            $flip = true;
            list ($xoff, $xlim, $yoff, $ylim)
                = array($yoff, $ylim, $xoff, $xlim);
        }

        if ($flip) {
            for ($i = $ylim - 1; $i >= $yoff; $i--) {
                $ymatches[$this->xv[$i]][] = $i;
            }
        } else {
            for ($i = $ylim - 1; $i >= $yoff; $i--) {
                $ymatches[$this->yv[$i]][] = $i;
            }
        }

        $this->lcs = 0;
        $this->seq[0] = $yoff - 1;
        $this->in_seq = array();
        $ymids[0] = array();

        $numer = $xlim - $xoff + $nchunks - 1;
        $x = $xoff;
        for ($chunk = 0; $chunk < $nchunks; $chunk++) {
            if ($chunk > 0) {
                for ($i = 0; $i <= $this->lcs; $i++) {
                    $ymids[$i][$chunk - 1] = $this->seq[$i];
                }
            }

            $x1 = $xoff + (int)(($numer + ($xlim - $xoff) * $chunk) / $nchunks);
            for (; $x < $x1; $x++) {
                $line = $flip ? $this->yv[$x] : $this->xv[$x];
                if (empty($ymatches[$line])) {
                    continue;
                }
                $matches = $ymatches[$line];
                foreach ($matches as $y) {
                    if (empty($this->in_seq[$y])) {
                        $k = $this->_lcsPos($y);
//						assert($k > 0);
                        $ymids[$k] = $ymids[$k - 1];
                        break;
                    }
                }

                while ($y = current($matches)) {
                    if ($y > $this->seq[$k - 1]) {
//							assert($y < $this->seq[$k]);
                        /* Optimization: this is a common case: next match is
                        * just replacing previous match. */
                        $this->in_seq[$this->seq[$k]] = false;
                        $this->seq[$k] = $y;
                        $this->in_seq[$y] = 1;
                    } elseif (empty($this->in_seq[$y])) {
                        $k = $this->_lcsPos($y);
//						assert($k > 0);
                        $ymids[$k] = $ymids[$k - 1];
                    }

                    next($matches);
                }
            }
        }

        $seps[] = $flip ? array($yoff, $xoff) : array($xoff, $yoff);
        $ymid = $ymids[$this->lcs];
        for ($n = 0; $n < $nchunks - 1; $n++) {
            $x1 = $xoff + (int)(($numer + ($xlim - $xoff) * $n) / $nchunks);
            $y1 = $ymid[$n] + 1;
            $seps[] = $flip ? array($y1, $x1) : array($x1, $y1);
        }
        $seps[] = $flip ? array($ylim, $xlim) : array($xlim, $ylim);

        return array($this->lcs, $seps);
    }

    function _lcsPos($ypos)
    {
        $end = $this->lcs;
        if ($end == 0 || $ypos > $this->seq[$end]) {
            $this->seq[++$this->lcs] = $ypos;
            $this->in_seq[$ypos] = 1;
            return $this->lcs;
        }

        $beg = 1;
        while ($beg < $end) {
            $mid = (int)(($beg + $end) / 2);
            if ($ypos > $this->seq[$mid]) {
                $beg = $mid + 1;
            } else {
                $end = $mid;
            }
        }

//		assert($ypos!=$this->seq[$end]);

        $this->in_seq[$this->seq[$end]] = false;
        $this->seq[$end] = $ypos;
        $this->in_seq[$ypos] = 1;
        return $end;
    }

    /**
     * Finds LCS of two sequences.
     *
     * The results are recorded in the vectors $this->{x,y}changed[], by
     * storing a 1 in the element for each line that is an insertion or
     * deletion (ie. is not in the LCS).
     *
     * The subsequence of file 0 is (XOFF, XLIM) and likewise for file 1.
     *
     * Note that XLIM, YLIM are exclusive bounds.  All line numbers are
     * origin-0 and discarded lines are not counted.
     */
    function _compareseq($xoff, $xlim, $yoff, $ylim)
    {
        /* Slide down the bottom initial diagonal. */
        while ($xoff < $xlim && $yoff < $ylim
               && $this->xv[$xoff] == $this->yv[$yoff]) {
            ++$xoff;
            ++$yoff;
        }

        /* Slide up the top initial diagonal. */
        while ($xlim > $xoff && $ylim > $yoff
               && $this->xv[$xlim - 1] == $this->yv[$ylim - 1]) {
            --$xlim;
            --$ylim;
        }

        if ($xoff == $xlim || $yoff == $ylim) {
            $lcs = 0;
        } else {
            /* This is ad hoc but seems to work well.  $nchunks =
            * sqrt(min($xlim - $xoff, $ylim - $yoff) / 2.5); $nchunks =
            * max(2,min(8,(int)$nchunks)); */
            $nchunks = min(7, $xlim - $xoff, $ylim - $yoff) + 1;
            list($lcs, $seps)
                = $this->_diag($xoff, $xlim, $yoff, $ylim, $nchunks);
        }

        if ($lcs == 0) {
            /* X and Y sequences have no common subsequence: mark all
            * changed. */
            while ($yoff < $ylim) {
                $this->ychanged[$this->yind[$yoff++]] = 1;
            }
            while ($xoff < $xlim) {
                $this->xchanged[$this->xind[$xoff++]] = 1;
            }
        } else {
            /* Use the partitions to split this problem into subproblems. */
            reset($seps);
            $pt1 = $seps[0];
            while ($pt2 = next($seps)) {
                $this->_compareseq($pt1[0], $pt2[0], $pt1[1], $pt2[1]);
                $pt1 = $pt2;
            }
        }
    }

    /**
     * Adjusts inserts/deletes of identical lines to join changes as much as
     * possible.
     *
     * We do something when a run of changed lines include a line at one end
     * and has an excluded, identical line at the other.  We are free to
     * choose which identical line is included.  `compareseq' usually chooses
     * the one at the beginning, but usually it is cleaner to consider the
     * following identical line to be the "change".
     *
     * This is extracted verbatim from analyze.c (GNU diffutils-2.7).
     */
    function _shiftBoundaries($lines, &$changed, $other_changed)
    {
        $i = 0;
        $j = 0;

//		assert('count($lines)==count($changed)');
        $len = count($lines);
        $other_len = count($other_changed);

        while (1) {
            /* Scan forward to find the beginning of another run of
            * changes. Also keep track of the corresponding point in the
            * other file.
            *
            * Throughout this code, $i and $j are adjusted together so that
            * the first $i elements of $changed and the first $j elements of
            * $other_changed both contain the same number of zeros (unchanged
            * lines).
            *
            * Furthermore, $j is always kept so that $j==$other_len or
            * $other_changed[$j]==false. */
            while ($j < $other_len && $other_changed[$j]) {
                $j++;
            }

            while ($i < $len && @!$changed[$i]) {
//				@assert('$j < $other_len && ! $other_changed[$j]');
                $i++;
                $j++;
                while ($j < $other_len && $other_changed[$j]) {
                    $j++;
                }
            }

            if ($i == $len) {
                break;
            }

            $start = $i;

            /* Find the end of this run of changes. */
            while (++$i < $len && $changed[$i]) {
                continue;
            }

            do {
                /* Record the length of this run of changes, so that we can
                * later determine whether the run has grown. */
                $runlength = $i - $start;

                /* Move the changed region back, so long as the previous
                * unchanged line matches the last changed one.  This merges
                * with previous changed regions. */
                while ($start > 0 && $lines[$start - 1] == $lines[$i - 1]) {
                    $changed[--$start] = 1;
                    $changed[--$i] = false;
                    while ($start > 0 && $changed[$start - 1]) {
                        $start--;
                    }
//						assert('$j > 0');
                    while ($other_changed[--$j]) {
                        continue;
                    }
//						assert('$j >= 0 && !$other_changed[$j]');
                }

                /* Set CORRESPONDING to the end of the changed run, at the
                * last point where it corresponds to a changed run in the
                * other file. CORRESPONDING==LEN means no such point has
                * been found. */
                $corresponding = $j < $other_len ? $i : $len;

                /* Move the changed region forward, so long as the first
                * changed line matches the following unchanged one.  This
                * merges with following changed regions.  Do this second, so
                * that if there are no merges, the changed region is moved
                * forward as far as possible. */
                while ($i < $len && $lines[$start] == $lines[$i]) {
                    $changed[$start++] = false;
                    $changed[$i++] = 1;
                    while ($i < $len && $changed[$i]) {
                        $i++;
                    }

//						assert('$j < $other_len && ! $other_changed[$j]');
                    $j++;
                    if ($j < $other_len && $other_changed[$j]) {
                        $corresponding = $i;
                        while ($j < $other_len && $other_changed[$j]) {
                            $j++;
                        }
                    }
                }
            } while ($runlength != $i - $start);

            /* If possible, move the fully-merged run of changes back to a
            * corresponding run in the other file. */
            while ($corresponding < $i) {
                $changed[--$start] = 1;
                $changed[--$i] = 0;
//					assert('$j > 0');
                while ($other_changed[--$j]) {
                    continue;
                }
//					assert('$j >= 0 && !$other_changed[$j]');
            }
        }
    }
}

/**
 * A class to render Diffs in different formats.
 *
 * This class renders the diff in classic diff format. It is intended that
 * this class be customized via inheritance, to obtain fancier outputs.
 *
 * $Horde: framework/Text_Diff/Diff/Renderer.php,v 1.12 2005/12/16 11:07:33 jan Exp $
 *
 * @package Text_Diff
 */
class Text_Diff_Renderer
{

    /**
     * Number of leading context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses may want to
     * set this to other values.
     */
    var $_leading_context_lines = 0;

    /**
     * Number of trailing context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses may want to
     * set this to other values.
     */
    var $_trailing_context_lines = 0;

    var $v;

    /**
     * Constructor.
     */
    function __construct($params = array())
    {
        foreach ($params as $param => $value) {
            $this->v = '_' . $param;
            if (isset($this->v)) {
                $this->v = $value;
            }
        }
    }

    /**
     * Get any renderer parameters.
     *
     * @return array All parameters of this renderer object.
     */
    function getParams()
    {
        $params = array();
        foreach (get_object_vars($this) as $k => $v) {
            if ($k[0] == '_') {
                $params[substr($k, 1)] = $v;
            }
        }

        return $params;
    }

    /**
     * Renders a diff.
     *
     * @param Text_Diff $diff A Text_Diff object.
     *
     * @return string The formatted output.
     */
    function render($diff)
    {
        $xi = $yi = 1;
        $block = false;
        $context = array();

        $nlead = $this->_leading_context_lines;
        $ntrail = $this->_trailing_context_lines;

        $output = $this->_startDiff();

        $diffs = $diff->getDiff();
        foreach ($diffs as $i => $edit) {
            if (is_a($edit, 'Text_Diff_Op_copy')) {
                if (is_array($block)) {
                    $keep = $i == count($diffs) - 1 ? $ntrail : $nlead + $ntrail;
                    if (count($edit->orig) <= $keep) {
                        $block[] = $edit;
                    } else {
                        if ($ntrail) {
                            $context = array_slice($edit->orig, 0, $ntrail);
                            $block[] = new Text_Diff_Op_copy($context);
                        }
                        $output .= $this->_block($x0, $ntrail + $xi - $x0,
                            $y0, $ntrail + $yi - $y0,
                            $block);
                        $block = false;
                    }
                }
                $context = $edit->orig;
            } else {
                if (!is_array($block)) {
                    $context = array_slice($context, count($context) - $nlead);
                    $x0 = $xi - count($context);
                    $y0 = $yi - count($context);
                    $block = array();
                    if ($context) {
                        $block[] = new Text_Diff_Op_copy($context);
                    }
                }
                $block[] = $edit;
            }

            if ($edit->orig) {
                $xi += count($edit->orig);
            }
            if ((isset($edit->final)) && ($edit->final)) {
                $yi += count($edit->final);
            }
        }

        if (is_array($block)) {
            $output .= $this->_block($x0, $xi - $x0,
                $y0, $yi - $y0,
                $block);
        }

        return $output . $this->_endDiff();
    }

    function _block($xbeg, $xlen, $ybeg, $ylen, &$edits)
    {
        $output = $this->_startBlock($this->_blockHeader($xbeg, $xlen, $ybeg, $ylen));

        foreach ($edits as $edit) {
            switch (strtolower(get_class($edit))) {
                case 'text_diff_op_copy':
                    $output .= $this->_context($edit->orig);
                    break;

                case 'text_diff_op_add':
                    $output .= $this->_added($edit->final);
                    break;

                case 'text_diff_op_delete':
                    $output .= $this->_deleted($edit->orig);
                    break;

                case 'text_diff_op_change':
                    $output .= $this->_changed($edit->orig, $edit->final);
                    break;
            }
        }

        return $output . $this->_endBlock();
    }

    function _startDiff()
    {
        return '';
    }

    function _endDiff()
    {
        return '';
    }

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
        if ($xlen > 1) {
            $xbeg .= ',' . ($xbeg + $xlen - 1);
        }
        if ($ylen > 1) {
            $ybeg .= ',' . ($ybeg + $ylen - 1);
        }

        return $xbeg . ($xlen ? ($ylen ? 'c' : 'd') : 'a') . $ybeg;
    }

    function _startBlock($header)
    {
        return $header . "\n";
    }

    function _endBlock()
    {
        return '';
    }

    function _lines($lines, $prefix = ' ', $encode = true)
    {
        $ret = $prefix;
        $ret .= implode("\n" . $prefix, $lines);
        $ret .= "\n";
        return $ret;
    }

    function _context($lines)
    {
        return $this->_lines($lines);
    }

    function _added($lines)
    {
        return $this->_lines($lines, '>');
    }

    function _deleted($lines, $words = false)
    {
        return $this->_lines($lines, '<');
    }

    function _changed($orig, $final)
    {
        return $this->_deleted($orig) . "---\n" . $this->_added($final);
    }
}

/**
 * "Inline" diff renderer.
 *
 * This class renders diffs in the Wiki-style "inline" format.
 *
 * $Horde: framework/Text_Diff/Diff/Renderer/inline.php,v 1.16 2006/01/08 00:06:57 jan Exp $
 *
 * @author  Ciprian Popovici
 * @package Text_Diff
 */
class Text_Diff_Renderer_inline extends Text_Diff_Renderer
{

    /**
     * Number of leading context "lines" to preserve.
     */
    var $_leading_context_lines = 10000;

    /**
     * Number of trailing context "lines" to preserve.
     */
    var $_trailing_context_lines = 10000;

    /**
     * Prefix for inserted text.
     */
    var $_ins_prefix = '<ins>';

    /**
     * Suffix for inserted text.
     */
    var $_ins_suffix = '</ins>';

    /**
     * Prefix for deleted text.
     */
    var $_del_prefix = '<del>';

    /**
     * Suffix for deleted text.
     */
    var $_del_suffix = '</del>';

    /**
     * Header for each change block.
     */
    var $_block_header = '';

    /**
     * What are we currently splitting on? Used to recurse to show word-level
     * changes.
     */
    var $_split_level = 'lines';

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
        return $this->_block_header;
    }

    function _startBlock($header)
    {
        return $header;
    }

    function _lines($lines, $prefix = ' ', $encode = true)
    {
        if ($encode) {
            array_walk($lines, array(&$this, '_encode'));
        }

        if ($this->_split_level == 'words') {
            return implode('', $lines);
        } else {
            return implode("\n", $lines) . "\n";
        }
    }

    function _added($lines)
    {
        array_walk($lines, array(&$this, '_encode'));
        $lines[0] = $this->_ins_prefix . $lines[0];
        $lines[count($lines) - 1] .= $this->_ins_suffix;
        return $this->_lines($lines, ' ', false);
    }

    function _deleted($lines, $words = false)
    {
        array_walk($lines, array(&$this, '_encode'));
        $lines[0] = $this->_del_prefix . $lines[0];
        $lines[count($lines) - 1] .= $this->_del_suffix;
        return $this->_lines($lines, ' ', false);
    }

    function _changed($orig, $final)
    {
        /* If we've already split on words, don't try to do so again - just
            * display. */
        if ($this->_split_level == 'words') {
            $prefix = '';
            while ($orig[0] !== false && $final[0] !== false &&
                   substr($orig[0], 0, 1) == ' ' &&
                   substr($final[0], 0, 1) == ' ') {
                $prefix .= substr($orig[0], 0, 1);
                $orig[0] = substr($orig[0], 1);
                $final[0] = substr($final[0], 1);
            }
            return $prefix . $this->_deleted($orig) . $this->_added($final);
        }

        $text1 = implode("\n", $orig);
        $text2 = implode("\n", $final);

        /* Non-printing newline marker. */
        $nl = "\0";

        /* We want to split on word boundaries, but we need to
            * preserve whitespace as well. Therefore we split on words,
            * but include all blocks of whitespace in the wordlist. */
        $diff = new Text_Diff($this->_splitOnWords($text1, $nl),
            $this->_splitOnWords($text2, $nl));

        /* Get the diff in inline format. */
        $renderer = new Text_Diff_Renderer_inline(array_merge($this->getParams(),
            array('split_level' => 'words')));

        /* Run the diff and get the output. */
        return str_replace($nl, "\n", $renderer->render($diff)) . "\n";
    }

    function _splitOnWords($string, $newlineEscape = "\n")
    {
        $words = array();
        $length = strlen($string);
        $pos = 0;

        while ($pos < $length) {
            // Eat a word with any preceding whitespace.
            $spaces = strspn(substr($string, $pos), " \n");
            $nextpos = strcspn(substr($string, $pos + $spaces), " \n");
            $words[] = str_replace("\n", $newlineEscape, substr($string, $pos, $spaces + $nextpos));
            $pos += $spaces + $nextpos;
        }

        return $words;
    }

    function _encode(&$string)
    {
        $string = htmlspecialchars($string);
    }
}

/**
 * "Unified" diff renderer.
 *
 * This class renders the diff in classic "unified diff" format.
 *
 * $Horde: framework/Text_Diff/Diff/Renderer/unified.php,v 1.5 2006/01/08 00:06:57 jan Exp $
 *
 * @package Text_Diff
 */
class Text_Diff_Renderer_unified extends Text_Diff_Renderer
{

    /**
     * Number of leading context "lines" to preserve.
     */
    var $_leading_context_lines = 2;

    /**
     * Number of trailing context "lines" to preserve.
     */
    var $_trailing_context_lines = 2;

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
        $_xbeg = strval($xbeg);
        $_ybeg = strval($ybeg);
        if ($xlen != 1) {
            $_xbeg .= ',' . strval($xlen);
        }
        if ($ylen != 1) {
            $_ybeg .= ',' . strval($ylen);
        }
        return '@@ -' . ($_xbeg) . ' +' . ($_ybeg) . ' @@';
    }

    function _added($lines)
    {
        return $this->_lines($lines, '+');
    }

    function _deleted($lines, $words = false)
    {
        return $this->_lines($lines, '-');
    }

    function _changed($orig, $final)
    {
        return $this->_deleted($orig) . $this->_added($final);
    }
}


