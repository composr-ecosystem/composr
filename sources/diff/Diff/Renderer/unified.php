<?php

/*EXTRA FUNCTIONS: Text_Diff_Renderer*/
/*CQC: No API check*/
/*CQC: !FLAG__SOMEWHAT_PEDANTIC*/
/*CQC: !FLAG__ESLINT*/

/**
 * "Unified" diff renderer.
 *
 * This class renders the diff in classic "unified diff" format.
 *
 * $Horde: framework/Text_Diff/Diff/Renderer/unified.php,v 1.3.10.7 2009/01/06 15:23:42 jan Exp $
 *
 * Copyright 2004-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://opensource.org/licenses/lgpl-license.php.
 *
 * @author  Ciprian Popovici
 * @package Text_Diff
 */

/** Text_Diff_Renderer */
if (!class_exists('Text_Diff_Renderer')) {
    require_once('Text/Diff/Renderer.php');
}

/**
 * @package Text_Diff
 */
class Text_Diff_Renderer_unified extends Text_Diff_Renderer
{
    /**
     * Number of leading context "lines" to preserve.
     */
    public $_leading_context_lines = 4;

    /**
     * Number of trailing context "lines" to preserve.
     */
    public $_trailing_context_lines = 4;

    public function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
        if ($xlen != 1) {
            $xbeg .= ',' . $xlen;
        }
        if ($ylen != 1) {
            $ybeg .= ',' . $ylen;
        }
        return "@@ -$xbeg +$ybeg @@";
    }

    public function _context($lines)
    {
        return $this->_lines($lines, ' ');
    }

    public function _added($lines)
    {
        return $this->_lines($lines, '+');
    }

    public function _deleted($lines)
    {
        return $this->_lines($lines, '-');
    }

    public function _changed($orig, $final)
    {
        return $this->_deleted($orig) . $this->_added($final);
    }
}
