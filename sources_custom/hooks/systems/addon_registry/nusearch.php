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

[title=\"2\"]Composr fast custom index[/title]

When searching large amounts of content it is important for the content to be pre-indexed for search, as searching through all the bytes is too slow. Composr traditionally makes uses of \"fulltext search\" capabilities present in most database software, i.e. the database software's own search engine. For the sake of simplicity in this documentation we will assume all users are using MySQL.

As of version 11, Composr now features its own search engine, which can run as a separate option triggered to run instead of MySQL fulltext search in a number of configurable situations (by default it does not run). The search engine is implemented for forum posts (public and private), catalogue entries, and Comcode pages. For simplicity we'll just talk about public forum post search.

The problem with MySQL fulltext search is 2-fold:
1) The search index is totally separated out from other indexing. If you want to do a search, and then filter it down to say a particular forum, or a particular poster, then it has to cleave a big chunk out of the search index and then cross-reference that with other index(es). It can be very inefficient.
2) If there are common terms on a website that are not so common/irrelevant as to be filtered out by standard, e.g. 'car' on a car website, then when someone searches for those words it means an enormous amount is going to be cleaved out of the search index.
And the worst is when these things combine. Let's say 40% of your forum posts contain the word 'car', and the user is searching for 'car maintenance' but the user is filtering to a forum with only 5% of the posts in it. Basically MySQL would cleave out 40% of its search index, calculate the ranking from all those rows and sort by that, and then cursor through almost all those rows until it gets just the top 30 that cross-reference with the forum index.

There's no real way around this with MySQL fulltext search indexing.

The Composr fast custom index takes a totally different approach. The forum posts database table gets a matching search indexing table, which indexes all the common search filters (poster ID, forum ID, etc) directly against individual keywords extracted from the posts.

So basically a row in that search indexing table might be like (keyword=car, forum_id=4, poster_id=300). The search indexing table is then also database-indexed against all the fields so that the database can very efficiently query out stuff from it.

Of course searches may have multiple keywords, so it revisits the table for each keyword, basically, and it has a ranking algorithm. There's a lot more to it than that, but I'm keeping it simple here. It basically ranks by how prevalent the most obscure word in the search query is in the forum post.

In terms of performance, the end result is the Composr fast custom index search system is a bit slower for most \"straight\" searches (searches with no additional filtering), but immensely faster for searches with additional filtering.

There are a few minor downsides to the Composr fast custom index:
 - If you want to allow multi-word quoted phrases it will use a lot more disk space because it has to separately store each combination of adjacent keywords, up to the limit you configure. That's due to how Composr fast custom index is designed: it is not building a data structure for the keywords in a document, it's separately indexing each keyword against all possible search filters.
 - 'Fuzzy' searching' for large databases (basically analogous to the 'natural' MySQL fulltext search) is very slow. Without fuzzy searching every keyword in the search will either be ANDed, ignored (stop words like 'is'), or excluded (if preceded with '-'). i.e. it doesn't take individual words as suggestions for match ranking, they all have to be taken into account.
 - Ranking accuracy isn't going to be as good, as the Composr fast custom index ranks based on just the most obscure keyword, not a blend of all keywords. This is necessary to avoid having to do cross-computation between each keyword, instead it can rank rows using direct indexing. Actually you can configure all-keyword ranking, but it is not recommended.
 - You cannot do 'blank' searches.
 - There will be a short lag before new content is indexed.

And some upsides:
 - Radically better performance for filtered queries, as discussed. While MySQL fulltext performance [i]degrades[/i] as filters are added, the Composr fast custom index performance is radically [i]improved[/i] with additional filtering.
 - Configurable stop word list without requring server admin access.
 - No minimum or maximum word sizes. So you can search for numbers, for example.
 - Great multilingual support.
 - Some database backends may not even provide fulltext search of their own, so Composr fast custom index would fill the gap.

[title=\"3\"]Stop words[/title]

Stop words are words that will be ignored by the search engine because they convey no meaning and just add noise to the search.
A default list is provided for English, and can be edited by copying the [tt]text/EN/too_common_words.txt[/tt] file to [tt]text_custom/EN/too_common_words.txt[/tt] and customising it.

[title=\"3\"]Index generation[/title]

There's a background task within the system scheduler that populates the indexing tables.
The first time it runs it:
1) Indexes all existing data.
2) Builds up a database table of ngram frequency (keyword frequency, basically) across all the supported searchable data (the [tt]<table-prefix>ft_index_commonality[/tt] table). This frequency data is used for ranking purposes.
Subsequently it just adds indexing for new content created/changed since the last indexing run.

If you want to reindex (perhaps you have changed some settings, or changed stop words, or want to regenerate commonality data to reflect the current status of your site), you need to manually wipe out the contents of the following database tables:
 - [tt]<table-prefix>ce_fulltext_index[/tt]
 - [tt]<table-prefix>cpages_fulltext_index[/tt]
 - [tt]<table-prefix>f_posts_fulltext_index[/tt]
 - [tt]<table-prefix>f_pposts_fulltext_index[/tt]
Composr will automatically see this as a signal that they need rebuilding.

[title=\"3\"]Internationalisation[/title]

Content is indexed against each language, according to translations of that language. So for example if you search for 'gift' in German you'd get results for the German meaning of that character string (very different to the English meaning!).

When translating content, you need to either edit the content in the language you are translating to -- of if you use the translation queue you'll need to force reindexing for those changes to show up.

Programmers can add grammar rules for different languages a lot more easily than they can to MySQL. Look at [tt]sources/lang_stemmer_EN.php[/tt] and [tt]sources/lang_tokeniser_EN.php[/tt] and just make equivalent files for your language.
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
