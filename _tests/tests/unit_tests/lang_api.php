<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class lang_api_test_set extends cms_test_case
{
    public function testAPI()
    {
        require_lang('cns');
        $lang_str = 'FORUM_POST_ISOLATED_RESULT'; // A nice one with 4 parameters to test

        set_option('yeehaw', '1');
        require_code('caches3');
        erase_cached_language();

        // If no parameters, no substitutions
        $this->assertTrue(do_lang($lang_str) === 'Post #{1} by {2} on {3} (in topic &ldquo;{4}&rdquo;)');

        // Test without Tempcode...

        // Anything after a leading null should be ignored
        $this->assertTrue(do_lang($lang_str, null, 'y') === 'Post #{1} by {2} on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 1 parameters, 1 substitution
        $this->assertTrue(do_lang($lang_str, 'x') === 'Post #x by {2} on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 2 parameters, 2 substitutions
        $this->assertTrue(do_lang($lang_str, 'x', 'y') === 'Post #x by y on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 3 parameters, 3 substitutions
        $this->assertTrue(do_lang($lang_str, 'x', 'y', 'z') === 'Post #x by y on z (in topic &ldquo;{4}&rdquo;)');

        // If 3 parameters, with 3rd in array format, 3 substitutions
        $this->assertTrue(do_lang($lang_str, 'x', 'y', ['z']) === 'Post #x by y on z (in topic &ldquo;{4}&rdquo;)');

        // If 4 parameters, 4 substitutions
        $this->assertTrue(do_lang($lang_str, 'x', 'y', ['z', 'zz']) === 'Post #x by y on z (in topic &ldquo;zz&rdquo;)');

        // If 5 parameters, 4 substitutions (as no more to do)
        $this->assertTrue(do_lang($lang_str, 'x', 'y', ['z', 'zz']) === 'Post #x by y on z (in topic &ldquo;zz&rdquo;)');

        // Ensure it can work with yeehaw-changes
        $this->assertTrue(do_lang('LICENCE', 'x') === 'License');

        // Test with Tempcode...

        $x = make_string_tempcode('x');
        $y = make_string_tempcode('y');
        $z = make_string_tempcode('z');
        $zz = make_string_tempcode('zz');

        // If 1 parameters, 1 substitution
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x)) === 'Post #x by {2} on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 2 parameters, 2 substitutions
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, $y)) === 'Post #x by y on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 3 parameters, 3 substitutions
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, $y, $z)) === 'Post #x by y on z (in topic &ldquo;{4}&rdquo;)');

        // If 3 parameters, with 3rd in array format, 3 substitutions
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, $y, [$z])) === 'Post #x by y on z (in topic &ldquo;{4}&rdquo;)');

        // If 4 parameters, 4 substitutions
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, $y, [$z, $zz])) === 'Post #x by y on z (in topic &ldquo;zz&rdquo;)');

        // If 5 parameters, 4 substitutions (as no more to do)
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, $y, [$z, $zz])) === 'Post #x by y on z (in topic &ldquo;zz&rdquo;)');

        // Ensure it can work with yeehaw-changes
        $this->assertTrue(static_evaluate_tempcode(do_lang('LICENCE', $x)) === 'License');

        // Leading Tempcode, but rest not Tempcode...

        // If 1 parameters, 1 substitution
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x)) === 'Post #x by {2} on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 2 parameters, 2 substitutions
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, 'y')) === 'Post #x by y on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 3 parameters, 3 substitutions
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, 'y', 'z')) === 'Post #x by y on z (in topic &ldquo;{4}&rdquo;)');

        // If 3 parameters, with 3rd in array format, 3 substitutions
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, 'y', ['z'])) === 'Post #x by y on z (in topic &ldquo;{4}&rdquo;)');

        // If 4 parameters, 4 substitutions
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, 'y', ['z', 'zz'])) === 'Post #x by y on z (in topic &ldquo;zz&rdquo;)');

        // If 5 parameters, 4 substitutions (as no more to do)
        $this->assertTrue(static_evaluate_tempcode(do_lang($lang_str, $x, 'y', ['z', 'zz'])) === 'Post #x by y on z (in topic &ldquo;zz&rdquo;)');

        // Leading string, but rest Tempcode...

        // If 1 parameters, 1 substitution
        $this->assertTrue(do_lang($lang_str, 'x') === 'Post #x by {2} on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 2 parameters, 2 substitutions
        $this->assertTrue(do_lang($lang_str, 'x', $y) === 'Post #x by y on {3} (in topic &ldquo;{4}&rdquo;)');

        // If 3 parameters, 3 substitutions
        $this->assertTrue(do_lang($lang_str, 'x', $y, $z) === 'Post #x by y on z (in topic &ldquo;{4}&rdquo;)');

        // If 3 parameters, with 3rd in array format, 3 substitutions
        $this->assertTrue(do_lang($lang_str, 'x', $y, [$z]) === 'Post #x by y on z (in topic &ldquo;{4}&rdquo;)');

        // If 4 parameters, 4 substitutions
        $this->assertTrue(do_lang($lang_str, 'x', $y, [$z, $zz]) === 'Post #x by y on z (in topic &ldquo;zz&rdquo;)');

        // If 5 parameters, 4 substitutions (as no more to do)
        $this->assertTrue(do_lang($lang_str, 'x', $y, [$z, $zz]) === 'Post #x by y on z (in topic &ldquo;zz&rdquo;)');

        // Also pluralisation syntax...

        $this->assertTrue(do_lang('DAYS', '-1') === '-1 days');
        $this->assertTrue(do_lang('DAYS', '0') === '0 days');
        $this->assertTrue(do_lang('DAYS', '1') === '1 day');
        $this->assertTrue(do_lang('DAYS', '2') === '2 days');
        $this->assertTrue(do_lang('DAYS', '200') === '200 days');
        $this->assertTrue(do_lang('DAYS', 'x') === 'x day');

        $this->assertTrue(static_evaluate_tempcode(do_lang('DAYS', make_string_tempcode('-1'))) === '-1 days');
        $this->assertTrue(static_evaluate_tempcode(do_lang('DAYS', make_string_tempcode('0'))) === '0 days');
        $this->assertTrue(static_evaluate_tempcode(do_lang('DAYS', make_string_tempcode('1'))) === '1 day');
        $this->assertTrue(static_evaluate_tempcode(do_lang('DAYS', make_string_tempcode('2'))) === '2 days');
        $this->assertTrue(static_evaluate_tempcode(do_lang('DAYS', make_string_tempcode('200'))) === '200 days');
        $this->assertTrue(static_evaluate_tempcode(do_lang('DAYS', make_string_tempcode('x'))) === 'x day');

        // Also vowel syntax...

        $this->assertTrue(do_lang('_ADDED_COMMENT_ON_UNTITLED', 'apple') === 'Commented on an apple');
        $this->assertTrue(do_lang('_ADDED_COMMENT_ON_UNTITLED', 'raspberry') === 'Commented on a raspberry');

        $this->assertTrue(static_evaluate_tempcode(do_lang('_ADDED_COMMENT_ON_UNTITLED', make_string_tempcode('apple'))) === 'Commented on an apple');
        $this->assertTrue(static_evaluate_tempcode(do_lang('_ADDED_COMMENT_ON_UNTITLED', make_string_tempcode('raspberry'))) === 'Commented on a raspberry');
    }
}
