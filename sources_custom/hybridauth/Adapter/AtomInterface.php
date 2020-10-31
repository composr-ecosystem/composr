<?php
/*!
* Hybridauth
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*  (c) 2020 Hybridauth authors | https://hybridauth.github.io/license.html
*/

namespace Hybridauth\Adapter;

use Hybridauth\Atom\Category;

/**
 * AtomInterface is a standardized interface that roughly corresponds with Atom.
 * In the ideal world every provider would implement AtomPub, or at least Atom or RSS feeds.
 * In the real world, they have a disincentive to be cross-compatible,
 * so we must make them cross compatible via our middleware.
 * The interface is generic enough to work for
 * feeds/streams, content repositories, and file systems.
 */
interface AtomInterface
{
    /**
     * Build an Atom feed.
     *
     * @param int $limit Number of elements per page (fewer if the $*Filter parameters are implemented on our end)
     * @param \Hybridauth\Atom\Filter $filter Filters
     *
     * @return string Atom feed
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function buildAtomFeed($limit = 12, $filters = null);

    /**
     * Get a list of atoms matching the filters (if given), in recency order.
     *
     * @param int $limit Number of elements per page (fewer if the $*Filter parameters are implemented on our end)
     * @param \Hybridauth\Atom\Filter $filter Filters
     *
     * @return array A pair: Array of atoms, Whether there were results [needed in case all were filtered out]
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function getAtoms($limit = 12, $filters = null);

    /**
     * Get all details of an individual atom.
     *
     * @param string $identifier Atom ID
     *
     * @return \Hybridauth\Atom\Atom Atom
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function getAtomFull($identifier);

    /**
     * Get all details of an individual atom, by URL.
     * Useful for oEmbed-style link display.
     *
     * @param string $url URL
     *
     * @return ?\Hybridauth\Atom\Atom Atom (or null if we do not recognise the URL)
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function getAtomFullFromURL($url);

    /**
     * Save an atom.
     * If the ID element of the atom is null then it will be an insert,
     * otherwise it will depend if an existing ID exists.
     *
     * @param \Hybridauth\Atom\Atom $atom Atom
     *
     * @return string Atom ID
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function saveAtom($atom);

    /**
     * Delete an atom.
     *
     * @param string $identifier Atom ID
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function deleteAtom($identifier);

    /**
     * Get a list of categories.
     *
     * @return array List of Category objects; array keys should be category identifiers
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function getCategories();

    /**
     * Save a category.
     * If the ID element of the category is null then it will be an insert,
     * otherwise it will depend if an existing ID exists.
     *
     * @param Category $category Category
     *
     * @return string Category ID
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function saveCategory($category);

    /**
     * Delete a category.
     *
     * @param string $identifier Category ID
     *
     * @throws \Hybridauth\Exception\NotImplementedException
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     * @throws \Hybridauth\Exception\UnexpectedApiResponseException
     */
    public function deleteCategory($identifier);
}
