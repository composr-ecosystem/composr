<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    nusearch
 */

/**
 * Hook class.
 */
class Hook_addon_registry_nusearch
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Development';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return "Introduces the Composr fast custom index, which will be a bundled part of the search addon in version 11. Designed to significantly improve the speed of most slow searches. Currently being prototyped on active pre-v11 websites.

---

When searching large amounts of content it is important for the content to be pre-indexed for search, as searching through all the bytes is too slow. Composr traditionally makes uses of \"fulltext search\" capabilities present in most database software, i.e. the database software's own search engine. For the sake of simplicity in this documentation we will assume all users are using MySQL.

As of version 11, Composr now features its own search engine, which runs as a separate option triggered to run instead of MySQL fulltext search in a number of configurable situations. The search engine is implemented for forum posts (public and private), catalogue entries, and Comcode pages. For simplicity we'll just talk about public forum post search.

The problem with MySQL fulltext search is 2-fold:
1) The search index is totally separated out from other indexing. If you want to do a search, and then filter it down to say a particular forum, or a particular poster, then it has to cleave a big chunk out of the search index and then cross-reference that with other index(es). It can be very inefficient.
2) If there are common terms on a website that are not so common/irrelevant as to be filtered out by standard, e.g. 'car' on a car website, then when someone searches for those words it means an enormous amount is going to be cleaved out of the search index.
And the worst is when these things combine. Let's say 40% of your forum posts contain the word 'car', and the user is searching for 'car maintenance' but the user is filtering to a forum with only 5% of the posts in it. Basically MySQL would cleave out 40% of its search index, calculate the ranking from all those rows and sort by that, and then cursor through almost all those rows until it gets just the top 30 that cross-reference with the forum index.

There's no real way around this with MySQL fulltext search indexing.

The Composr fast custom index takes a totally different approach. The forum posts database table gets a matching search indexing table, which indexes all the common search filters (poster ID, forum ID, etc) directly against individual keywords extracted from the posts. There's a background task within the system scheduler that populates this table progressively.

So basically a row in that search indexing table might be like (keyword=car, forum_id=4, poster_id=300). The search indexing table is then also database-indexed against all the fields so that the database can very efficiently query out stuff from it.

Of course searches may have multiple keywords, so it revisits the table for each keyword, basically, and it has a ranking algorithm. There's a lot more to it than that, but I'm keeping it simple here. It basically ranks by how prevalent the most obscure word in the search query is in the forum post.

In terms of performance, the end result is the Composr fast custom index search system is a bit slower for most \"straight\" searches (searches with no additional filtering), but immensely faster for searches with additional filtering.

There are a few downsides to the Composr fast custom index:
 - If you want to allow multi-word quoted phrases it will use a lot more disk space because it has to separately store each combination of adjacent keywords, up to the limit you configure. That's due to how Composr fast custom index is designed: it is not building a data structure for the keywords in a document, it's separately indexing each keyword against all possible search filters.
 - 'Fuzzy' searching' for large databases (basically analogous to the 'natural' MySQL fulltext search) is very slow. Without fuzzy searching every keyword in the search will either be ANDed, ignored (stop words like 'is'), or excluded (if preceded with '-'). i.e. it doesn't take individual words as suggestions for match ranking, they all have to be taken into account.
 - Ranking accuracy isn't going to be as good, as the Composr fast custom index ranks based on just the most obscure keyword, not a blend of all keywords. This is necessary to avoid having to do cross-computation between each keyword, instead it can rank rows using direct indexing. Actually you can configure all-keyword ranking, but it is not recommended.
 - You cannot do 'blank' searches.

And some upsides:
 - Radically better performance for filtered queries, as discussed. While MySQL fulltext performance [i]degrades[/i] as filters are added, the Composr fast custom index performance is radically [i]improved[/i] with additional filtering.
 - Configurable stop word list without requring server admin access.
 - No minimum or maximum word sizes. So you can search for numbers, for example.
 - Great multilingual support -- indexing is done separately for each language, and programmers can add grammar rules for different languages a lot more easily than they can to MySQL.
 - Some database backends may not even provide fulltext search of their own, so Composr fast custom index would fill the gap.
";
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'search',
            ),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/search.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/nusearch.php',
            'sources_custom/hooks/systems/cron/composr_fulltext_indexer.php',
            'sources_custom/lang_tokeniser_EN.php',
            'sources_custom/search.php',
            'sources_custom/hooks/modules/search/cns_posts.php',
            'sources_custom/hooks/modules/search/cns_own_pt.php',
            'sources_custom/hooks/modules/search/catalogue_entries.php',
            'sources_custom/hooks/modules/search/comcode_pages.php',
            'data_custom/search_test.php',
        );
    }
}
