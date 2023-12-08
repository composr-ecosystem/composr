<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class lang_tokeniser_test_set extends cms_test_case
{
    public function testLangStemmer()
    {
        require_code('lang_stemmer_' . fallback_lang());
        $stemmer = object_factory('Stemmer_' . fallback_lang());

        $this->assertTrue($stemmer->stem('cakes') == 'cake'); // Simple stemming case
        $this->assertTrue($stemmer->stem('CaKes') == 'CaKe'); // Preserve mid-word case
        $this->assertTrue($stemmer->stem('CaKeS') == 'CaKeS'); // Won't stem upper-case
        $this->assertTrue($stemmer->stem('CAKES') == 'CAKES'); // Won't stem upper-case

        $this->assertTrue($stemmer->stem('fish') == 'fish'); // Simple stemming non-case
        $this->assertTrue($stemmer->stem('FisH') == 'FisH'); // Preserve mid-word case
    }

    public function testLangTokeniser()
    {
        require_code('lang_tokeniser_' . fallback_lang());
        $tokeniser = object_factory('LangTokeniser_' . fallback_lang());

        // Querying...

        $got = $tokeniser->query_to_search_tokens('One Two Three Four Five Six', true, 5);
        $this->assertTrue(array_keys($got[0]) == array('one', 'two', 'three', 'four', 'five'));

        $got = $tokeniser->query_to_search_tokens("it's 'its'");
        $this->assertTrue(array_keys($got[0]) == array("it's", 'its'));

        $got = $tokeniser->query_to_search_tokens('a "test search"');
        $this->assertTrue(array_keys($got[0]) == array('a', 'test search'));

        $got = $tokeniser->query_to_search_tokens('"test search"');
        $this->assertTrue(array_keys($got[0]) == array('test search'));

        $got = $tokeniser->query_to_search_tokens('test" search');
        $this->assertTrue(array_keys($got[0]) == array('test', 'search'));

        $search = 'This, is; an    example- SEARCH +YES -No - + Blah-blah "Go Go Go" +"And this"';
        $expected = array(
            array('this' => true, 'is' => true, 'an' => true, 'example' => true, 'search' => true, 'blah' => true, 'go go go' => false),
            array('yes' => true, 'and this' => false),
            array('no' => true),
        );
        $got = $tokeniser->query_to_search_tokens($search);
        $this->assertTrue($got == $expected);

        // Indexing...

        $got = $tokeniser->text_to_ngrams("it's 'its'");
        $this->assertTrue(array_keys($got) == array("it's", 'its'));

        $total_singular_ngram_tokens = 0;
        $expected = array(
            'this' => true,
            'this is' => false,
            'this is a' => false,
            'is' => true,
            'is a' => false,
            'is a test' => false,
            'a' => true,
            'a test' => false,
            'a test sentence' => false,
            'test' => true,
            'test sentence' => false,
            'sentence' => true,
            'another' => true,
            'another test' => false,
            'another test sentence' => false,
        );
        $got = $tokeniser->text_to_ngrams('This is a test sentence. Another test sentence', 3, $total_singular_ngram_tokens);
        $this->assertTrue($got == $expected);
        $this->assertTrue($total_singular_ngram_tokens == 8);
    }
}
