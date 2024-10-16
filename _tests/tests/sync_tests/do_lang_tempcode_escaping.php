<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class do_lang_tempcode_escaping_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }

    public function testMissingEscaping()
    {
        $regexp = "#^(.*)do_lang_tempcode\('[^']+'(, .*)$#m";

        $regexp_positive_clauses = [
            '\'[^\']*\'',
            '\$[\w\->\[\]]+/\*Tempcode\*/',
            'lorem_\w+\(\)',
            '\$breadcrumbs',
            '\$[\w\->]*content_type',
            '->evaluate\(\)',
        ];
        $allowed_functions = [
            'protect_from_escaping',
            'placeholder_number',
            'cms_error_get_last',
            'comcode_to_tempcode',
            'get_translated_tempcode',
            'strval',
            'do_lang',
            'get_timezoned_date_time',
            'do_lang_tempcode',
            'make_fractionable_editable',
            'is_maintained_description',
        ];

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        foreach ($files as $path) {
            $c = file_get_contents(get_file_base() . '/' . $path);

            $matches = [];
            $num_matches = preg_match_all($regexp, $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $line = $matches[0][$i];

                $_stem = $matches[2][$i];
                $stem = '';
                $balance = 0;
                $_stem_len = strlen($_stem);
                for ($j = 0; $j < $_stem_len; $j++) {
                    if ($_stem[$j] == '(') {
                        $balance++;
                    } elseif ($_stem[$j] == ')') {
                        $balance--;
                        if ($balance == -1) {
                            break;
                        }
                    }
                    $stem .= $_stem[$j];
                }

                foreach ($regexp_positive_clauses as $clause) {
                    $stem = preg_replace('#(, |\[)' . $clause . '#', '', $stem);
                }
                foreach ($allowed_functions as $func) {
                    do {
                        $pos = strpos($stem, $func);
                        if ($pos !== false) {
                            $stem_len = strlen($stem);
                            for ($j = $pos + strlen($func); $j < $stem_len; $j++) {
                                if ($stem[$j] == '(') {
                                    $balance++;
                                } elseif ($stem[$j] == ')') {
                                    $balance--;
                                    if ($balance == -1) {
                                        $j++;
                                        break;
                                    }
                                }
                            }
                            $stem = substr($stem, 0, $pos) . substr($stem, $j);
                        }
                    } while ($pos !== false);
                }

                if ((rtrim($stem, ', []') != '') && (strpos($stem, 'escape_html') === false)) {
                    $hash = md5($line);

                    if (in_array($hash, [
                        '86184f044a2aa3d897ce58a84e125877',
                        '7c0df91e1f72fff9ab8b9fcaf14cf66e',
                        '6128a8cd486471675b45cb5d0f2ebad3',
                        'def79530b6e3e1e57a07bffe98ccc1c2',
                        '371017651f2216a9918c2c9237abeef7',
                        '598f753a249537fbc0ed3ed3d788f820',
                        '053a2d4878dc379687995f87d852fdc7',
                        '7c0df91e1f72fff9ab8b9fcaf14cf66e',
                        '08d84e0f2e501b6ea2955f8f044730e5',
                        'ca6088794bcbd3bc578eb255b75bf1dd',
                        'c2611cca05ae762485b8971c04cb5229',
                        '7d74a4f57270ece941ca5816afa1189f',
                        '5c4a443659f7ecbdeba7f405792abde2',
                        '76104f0b8605d134cd66abeb54f594aa',
                        '5ca1a811614916039dc6911357242372',
                        '5d7c539a47208b053bf942975572bfa8',
                        'cad9874f3bd22808ebdbd82568ca99d9',
                        '93e680a9dfa8a37ef5705968aec21af8',
                        '721e9bf85435db253d239613d53d5703',
                        '0e3a428ff2c3550746c072530e809115',
                        'd05186d3261f571b57fed79695f0921b',
                        '23264048db7e7e2efcc0a8e40f23163e',
                        'ecde8b3867fe651c628efaca3d6a1750',
                        '106332fadb9afd606e647a7eaff60316',
                        '71f2e15e22f7d0aca06bd46910b00732',
                        'bac76b04d8c3e0f930513b21a55b3f92',
                        'aeb2c7e75eefb72ebb9aed0635b8c0a6',
                        'ff463d4ac3298fcf1b843951d10b57fb',
                        'aeb2c7e75eefb72ebb9aed0635b8c0a6',
                        'aeb2c7e75eefb72ebb9aed0635b8c0a6',
                        '23ff3db03daa149da1674d99ede2b67f',
                        'ff463d4ac3298fcf1b843951d10b57fb',
                        '4ac8838e8674572ac2a1f83a29b0ed8f',
                        '403d5759794a8cb62f00f4d721616f86',
                        '533c0e82b8c4e055b1b86c44eeeb6d0d',
                        '4ac2f3e0c75a31141194731a04d2de41',
                        'bb7c51debd520a57525e10e2fc1aca02',
                        '2a0ba7f50ba01d184f6369070ca307c3',
                        '191f2566b64e0eb10d55f47916a5487c',
                        'd90d0b5354406a92068f60d0af60acfc',
                        '02e0723f3f2d5a1c96bffee868bbe3b8',
                        '3bf77171ae024535a6365bd3154f6b78',
                        '9f1c02f0f54060e967bb286423f36580',
                        'a62ab3856d9a00c3361cde8252055810',
                        'c2b19862838ebec11193c119cc2db1fb',
                        'f7bc0b85bab42b975cbdb6b54f039ba1',
                        '55b1796a94f6e3f95da288f6e306fbc8',
                        'f7cfc2c9a055692b9501fb5ee1e8efdb',
                        '8c204ecef04230a13f77219ff6d80f87',
                        '49e085c6bf75c26e5d7fa62111cb14a7',
                        'e64659181ff4034a6bc087e4f055d4b4',
                        'ce2033d0eac2c0c54173e0acabde15ef',
                        'ca6fdeb2b9a0be858effa09c654ad821',
                        'b8126e743b5fba4704d84412c0cbd391',
                        'b8126e743b5fba4704d84412c0cbd391',
                        '9ba56da8a103ad0c3d920ba1b313bc6c',
                        'b0c23bd110f4700cadae1dce999725c0',
                        '7f615fc60dc6668954f556ab8cac3405',
                        '06f153ae31e5d5bf981ce6b3dba0a950',
                        'b9efa9fad5adf02fd67e0dd44e33f00e',
                        '8077204db0662e0325829aa327c292ff',
                        'b6ca5d13ace890e1192cae7a8db66299',
                        '212c497425004fb55e9b3b355a88d9e3',
                        '212c497425004fb55e9b3b355a88d9e3',
                        '75ecc33bac196c99f91626fc397c3398',
                        '7a112acd9099d50aa1451371cdb8c309',
                        'd55b56c36c4ecccb424de78cfb929bea',
                        '50f1301db7d46265d66f95ecf0fc9f3d',
                        'ca5a9594f521d13dc6381e7974c29862',
                        '00ad873e27d4fcff3932b57b14f033f6',
                        'b3b5e1fd1d681729b2123ffd41664333',
                        'e276398255f84ec2383e6a9d69cee919',
                        '6c06a0ea2c14dd007a402db03d06464f',
                        '35d691e7c27d427755fa47c6260d3618',
                        '2e85a70d3e203a07928e8f92e9039666',
                        'af81b1bacc3577e99c851653bec74f26',
                        'a8b496a4dab2769617de0113b38b1929',
                        'd59fbcccf4310a3f2e3d3389fa04832c',
                        'af81b1bacc3577e99c851653bec74f26',
                        '2bea046e99c88935cba4c42ede6bf001',
                        'e0d68cfc1ecea68bd19e80e79288f5a4',
                        'b34cc69ce3b65bef3251279343d26f2c',
                        '4dd511c38428977b5258028ae94fade6',
                        '052568d2b5f36e451970bed1070db994',
                        '6462271eb30a2fefeedb1d21c598c1c5',
                        '48590c84418efb80591bd0152fd712a4',
                        '16083bc03fe3e18864720c35a25130ff',
                        '8cefee9849731650084435dc76e5fac0',
                        '833bbadb1c6d9fedcb141db56c361de6',
                        'c6f46e28c72581ee56149f608744c6ab',
                        'c065d34ab8ba8fe8ec4544c106a00d12',
                        'aeb2c7e75eefb72ebb9aed0635b8c0a6',
                        'e4a230ce74e33091c1a2b031a7db9688',
                        '91a47a9198424f9d17fd08e3f50b7be1',
                        '0d29d34745ae40f3c7a6e79450e34ca3',
                        '25c4ea17263af0a430720358b539a30c',
                        '0342c6726847e093b648327c4e9eacaa',
                        '2fac1e74cebabdf920e66f107c84e24f',
                        '44a7c4f5179a0f92fc57f36e6b427f9d',
                    ])) {
                        continue;
                    }

                    $this->assertTrue(false, 'No escaping for do_lang_tempcode parameter in ' . $path . ' for: ' . $line . '; hash: ' . $hash);
                }
            }
        }
    }
}
