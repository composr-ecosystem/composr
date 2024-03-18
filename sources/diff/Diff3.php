<?php

/*EXTRA FUNCTIONS: Text_Diff|Text_Diff_Engine_\w+|extension_loaded*/
/*CQC: No API check*/
/*CQC: !FLAG__SOMEWHAT_PEDANTIC*/
/*CQC: !FLAG__ESLINT*/

/**
 * A class for computing three way diffs.
 *
 * $Horde: framework/Text_Diff/Diff3.php,v 1.2.10.7 2009/01/06 15:23:41 jan Exp $
 *
 * Copyright 2007-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://opensource.org/licenses/lgpl-license.php.
 *
 * @package Text_Diff
 * @since   0.3.0
 */

/** Text_Diff */
if (!class_exists('Text_Diff')) {
    require_once('Text/Diff.php');
}

/**
 * A class for computing three way diffs.
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
    public $_conflictingBlocks = 0;

    /**
     * Computes diff between 3 sequences of strings.
     *
     * @param array $orig The original lines to use.
     * @param array $final1 The first version to compare to.
     * @param array $final2 The second version to compare to.
     */
    public function __construct($orig, $final1, $final2)
    {
        if (extension_loaded('xdiff')) {
            $engine = new Text_Diff_Engine_xdiff();
        } else {
            $engine = new Text_Diff_Engine_native();
        }

        $this->_edits = $this->_diff3($engine->diff($orig, $final1),
            $engine->diff($orig, $final2));
    }

    /**
     */
    public function mergedOutput($label1 = false, $label2 = false)
    {
        $lines = [];
        foreach ($this->_edits as $edit) {
            if ($edit->isConflict()) {
                /* FIXME: this should probably be moved somewhere else. */
                $lines = array_merge($lines,
                    ['<<<<<<<' . ($label1 ? (' ' . $label1) : '')],
                    $edit->final1,
                    ["======="],
                    $edit->final2,
                    ['>>>>>>>' . ($label2 ? (' ' . $label2) : '')]);
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
    public function _diff3($edits1, $edits2)
    {
        $edits = [];
        $bb = new Text_Diff3_BlockBuilder();

        $e1 = current($edits1);
        $e2 = current($edits2);
        while ($e1 || $e2) {
            if ($e1 && $e2 && is_a($e1, 'Text_Diff_Op_copy') && is_a($e2, 'Text_Diff_Op_copy')) {
                /* We have copy blocks from both diffs. This is the (only)
                 * time we want to emit a diff3 copy block.  Flush current
                 * diff3 diff block, if any. */
                if ($edit = $bb->finish()) {
                    $edits[] = $edit;
                }

                $ncopy = min($e1->norig(), $e2->norig());
                assert($ncopy > 0);
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
                if ($e1 && $e2) {
                    if ($e1->orig && $e2->orig) {
                        $norig = min($e1->norig(), $e2->norig());
                        $orig = array_splice($e1->orig, 0, $norig);
                        array_splice($e2->orig, 0, $norig);
                        $bb->input($orig);
                    }

                    if (is_a($e1, 'Text_Diff_Op_copy')) {
                        $bb->out1(array_splice($e1->final, 0, $norig));
                    }

                    if (is_a($e2, 'Text_Diff_Op_copy')) {
                        $bb->out2(array_splice($e2->final, 0, $norig));
                    }
                }

                if ($e1 && !$e1->orig) {
                    $bb->out1($e1->final);
                    $e1 = next($edits1);
                }
                if ($e2 && !$e2->orig) {
                    $bb->out2($e2->final);
                    $e2 = next($edits2);
                }
            }
        }

        if ($edit = $bb->finish()) {
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
    public $orig;
    public $final1;
    public $final2;
    protected $_merged;

    public function __construct($orig = false, $final1 = false, $final2 = false)
    {
        $this->orig = $orig ? $orig : [];
        $this->final1 = $final1 ? $final1 : [];
        $this->final2 = $final2 ? $final2 : [];
    }

    public function merged()
    {
        if (!isset($this->_merged)) {
            if ($this->final1 === $this->final2) {
                $this->_merged = &$this->final1;
            } elseif ($this->final1 === $this->orig) {
                $this->_merged = &$this->final2;
            } elseif ($this->final2 === $this->orig) {
                $this->_merged = &$this->final1;
            } else {
                $this->_merged = false;
            }
        }

        return $this->_merged;
    }

    public function isConflict()
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

    public $orig;
    public $final1;
    public $final2;

    public function __construct($lines = false)
    {
        $this->orig = $lines ? $lines : [];
        $this->final1 = &$this->orig;
        $this->final2 = &$this->orig;
    }

    public function merged()
    {
        return $this->orig;
    }

    public function isConflict()
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

    public $orig;
    public $final1;
    public $final2;

    public function __construct()
    {
        $this->_init();
    }

    public function input($lines)
    {
        if ($lines) {
            $this->_append($this->orig, $lines);
        }
    }

    public function out1($lines)
    {
        if ($lines) {
            $this->_append($this->final1, $lines);
        }
    }

    public function out2($lines)
    {
        if ($lines) {
            $this->_append($this->final2, $lines);
        }
    }

    public function isEmpty()
    {
        return !$this->orig && !$this->final1 && !$this->final2;
    }

    public function finish()
    {
        if ($this->isEmpty()) {
            return false;
        } else {
            $edit = new Text_Diff3_Op($this->orig, $this->final1, $this->final2);
            $this->_init();
            return $edit;
        }
    }

    public function _init()
    {
        $this->orig = $this->final1 = $this->final2 = [];
    }

    public function _append(&$array, $lines)
    {
        array_splice($array, count($array), 0, $lines);
    }
}
