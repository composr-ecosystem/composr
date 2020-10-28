<?php
/*!
* Hybridauth
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*  (c) 2017 Hybridauth authors | https://hybridauth.github.io/license.html
*/

namespace Hybridauth\Provider;

use Hybridauth\Adapter\OAuth1;
use Hybridauth\Adapter\AtomInterface;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\Data\Collection;
use Hybridauth\User;
use Hybridauth\Atom\Atom;
use Hybridauth\Atom\Enclosure;
use Hybridauth\Atom\Category;
use Hybridauth\Atom\Author;
use Hybridauth\Atom\AtomFeedBuilder;
use Hybridauth\Atom\AtomHelper;
use Hybridauth\Atom\Filter;

/**
 * Twitter OAuth1 provider adapter.
 * Uses OAuth1 not OAuth2 because many Twitter endpoints are built around OAuth1.
 *
 * Example:
 *
 *   $config = [
 *       'callback'  => Hybridauth\HttpClient\Util::getCurrentUrl(),
 *       'keys'      => [ 'key' => '', 'secret' => '' ], // OAuth1 uses 'key' not 'id'
 *       'authorize' => true // Needed to perform actions on behalf of users (see below link)
 *         // https://developer.twitter.com/en/docs/authentication/oauth-1-0a/obtaining-user-access-tokens
 *   ];
 *
 *   $adapter = new Hybridauth\Provider\Twitter( $config );
 *
 *   try {
 *       $adapter->authenticate();
 *
 *       $userProfile = $adapter->getUserProfile();
 *       $tokens = $adapter->getAccessToken();
 *       $contacts = $adapter->getUserContacts(['screen_name' =>'andypiper']); // get those of @andypiper
 *       $activity = $adapter->getUserActivity('me');
 *   }
 *   catch( Exception $e ){
 *       echo $e->getMessage() ;
 *   }
 */
class Twitter extends OAuth1 implements AtomInterface
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.twitter.com/1.1/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://api.twitter.com/oauth/authenticate';

    /**
     * {@inheritdoc}
     */
    protected $requestTokenUrl = 'https://api.twitter.com/oauth/request_token';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://api.twitter.com/oauth/access_token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://dev.twitter.com/web/sign-in/implementing';

    /**
     * {@inheritdoc}
     */
    protected function getAuthorizeUrl($parameters = [])
    {
        if ($this->config->get('authorize') === true) {
            $this->authorizeUrl = 'https://api.twitter.com/oauth/authorize';
        }

        return parent::getAuthorizeUrl($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserProfile()
    {
        $response = $this->apiRequest('account/verify_credentials.json', 'GET', [
            'include_email' => $this->config->get('include_email') === false ? 'false' : 'true',
        ]);

        $data = new Collection($response);

        if (!$data->exists('id_str')) {
            throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
        }

        $userProfile = new User\Profile();

        $userProfile->identifier    = $data->get('id_str');
        $userProfile->displayName   = $data->get('screen_name');
        $userProfile->description   = $data->get('description');
        $userProfile->firstName     = $data->get('name');
        $userProfile->email         = $data->get('email');
        $userProfile->emailVerified = $data->get('email');
        $userProfile->webSiteURL    = $data->get('url');
        $userProfile->region        = $data->get('location');

        $userProfile->profileURL    = $data->exists('screen_name')
                                        ? ('http://twitter.com/' . $data->get('screen_name'))
                                        : '';

        $photoSize = $this->config->get('photo_size') ?: 'original';
        $photoSize = $photoSize === 'original' ? '' : "_{$photoSize}";
        $userProfile->photoURL      = $data->exists('profile_image_url_https')
                                        ? str_replace('_normal', $photoSize, $data->get('profile_image_url_https'))
                                        : '';

        $userProfile->data = [
          'followed_by' => $data->get('followers_count'),
          'follows' => $data->get('friends_count'),
        ];

        return $userProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserContacts($parameters = [])
    {
        $parameters = [ 'cursor' => '-1'] + $parameters;

        $response = $this->apiRequest('friends/ids.json', 'GET', $parameters);

        $data = new Collection($response);

        if (! $data->exists('ids')) {
            throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
        }

        if ($data->filter('ids')->isEmpty()) {
            return [];
        }

        $contacts = [];

        // 75 id per time should be okey
        $contactsIds = array_chunk((array) $data->get('ids'), 75);

        foreach ($contactsIds as $chunk) {
            $parameters = [ 'user_id' => implode(',', $chunk) ];

            try {
                $response = $this->apiRequest('users/lookup.json', 'GET', $parameters);

                if ($response && count($response)) {
                    foreach ($response as $item) {
                        $contacts[] = $this->fetchUserContact($item);
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $contacts;
    }

    /**
     * @param $item
     *
     * @return User\Contact
     */
    protected function fetchUserContact($item)
    {
        $item = new Collection($item);

        $userContact = new User\Contact();

        $userContact->identifier  = $item->get('id_str');
        $userContact->displayName = $item->get('name');
        $userContact->photoURL    = $item->get('profile_image_url');
        $userContact->description = $item->get('description');

        $userContact->profileURL  = $item->exists('screen_name')
                                        ? ('http://twitter.com/' . $item->get('screen_name'))
                                        : '';

        return $userContact;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserStatus($status)
    {
        if (is_string($status)) {
            $status = ['status' => $status];
        }

        // Prepare request parameters.
        $params = [];
        if (isset($status['status'])) {
            $params['status'] = $status['status'];
        }
        if (isset($status['picture'])) {
            $media = $this->apiRequest('https://upload.twitter.com/1.1/media/upload.json', 'POST', [
                'media' => base64_encode(file_get_contents($status['picture'])),
            ]);
            $params['media_ids'] = $media->media_id;
        }

        $response = $this->apiRequest('statuses/update.json', 'POST', $params);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserActivity($stream = 'me')
    {
        $apiUrl = ($stream == 'me')
                    ? 'statuses/user_timeline.json'
                    : 'statuses/home_timeline.json';

        $response = $this->apiRequest($apiUrl);

        if (!$response) {
            return [];
        }

        $activities = [];

        foreach ($response as $item) {
            $activities[] = $this->fetchUserActivity($item);
        }

        return $activities;
    }

    /**
     * @param $item
     * @return User\Activity
     */
    protected function fetchUserActivity($item)
    {
        $item = new Collection($item);

        $userActivity = new User\Activity();

        $userActivity->id   = $item->get('id_str');
        $userActivity->date = $item->get('created_at');
        $userActivity->text = $item->get('text');

        $userActivity->user->identifier   = $item->filter('user')->get('id_str');
        $userActivity->user->displayName  = $item->filter('user')->get('name');
        $userActivity->user->photoURL     = $item->filter('user')->get('profile_image_url');

        $userActivity->user->profileURL   = $item->filter('user')->get('screen_name')
                                                ? ('http://twitter.com/' . $item->filter('user')->get('screen_name'))
                                                : '';

        return $userActivity;
    }

    /**
     * {@inheritdoc}
     */
    public function buildAtomFeed($limit = 12, $filter = null)
    {
        $userProfile = $this->getUserProfile();
        list($atoms) = $this->getAtoms($limit, $filter);

        $utility = new AtomFeedBuilder();
        $title = 'Twitter feed of ' . $userProfile->displayName;
        $feedId = 'urn:hybridauth:twitter:' . $userProfile->identifier . ':' . md5(serialize(func_get_args()));
        $urnStub = 'urn:hybridauth:twitter:';
        $url = $userProfile->profileURL;
        return $utility->buildAtomFeed($title, $url, $feedId, $urnStub, $atoms);
    }

    /**
     * {@inheritdoc}
     */
    public function getAtoms($limit = 12, $filter = null)
    {
        if ($filter === null) {
            $filter = new Filter();
        }

        $apiUrl = 'statuses/user_timeline.json';

        $response = $this->apiRequest($apiUrl);

        if (!$response) {
            return [];
        }

        $atoms = [];

        foreach ($response as $item) {
            $atom = $this->parseTweet($item);

            if ($filter->passesEnclosureTest($atom->enclosures)) {
                $atoms[] = $atom;
            }
        }

        return [$atoms, !empty($response)];
    }

    /**
     * {@inheritdoc}
     */
    public function getAtomFull($identifier)
    {
        $apiUrl = 'statuses/show/' . $identifier . '.json';

        $item = $this->apiRequest($apiUrl);

        return $this->parseTweet($item);
    }

    /**
     * Convert a Tweet into an atom.
     *
     * @param object $item
     *
     * @return \Hybridauth\Atom\Atom
     */
    protected function parseTweet($item)
    {
        $atom = new Atom();

        $atom->identifier = $item->id_str;
        $atom->isIncomplete = false;
        $atom->published = new \DateTime($item->created_at);
        $atom->url = "https://twitter.com/{$item->user->screen_name}/status/{$item->id_str}";

        $urlUsernames = '<a href="http://twitter.com/$1">@$1</a>';
        $urlHashtags = '<a href="https://twitter.com/hashtag/$1?src=hash">#$1</a>';
        $detectUrls = true;
        list($text, $repped) = AtomHelper::processCodes($item->text, $urlUsernames, $urlHashtags, $detectUrls);
        if ((!$repped) && (AtomHelper::plainTextToHtml(AtomHelper::htmlToPlainText($text)) == $text)) {
            $atom->title = $text;
        } else {
            $atom->content = $text;
        }

        $atom->author = new Author();
        $atom->author->identifier = strval($item->user->id);
        $atom->author->displayName = $item->user->screen_name;
        $atom->author->profileURL = $item->user->url;
        if (!empty($data->profile_image_url_https)) {
            $atom->author->photoURL = str_replace('_normal', '_original', $data->profile_image_url_https);
        }

        $enclosures = [];
        if (isset($item->extended_entities)) {
            foreach ($item->extended_entities->media as $entity) {
                $enclosure = new Enclosure();

                switch ($entity->type) {
                    case 'photo':
                        $enclosure->type = Enclosure::ENCLOSURE_IMAGE;
                        $enclosure->url = $entity->media_url_https;
                        $enclosure->mimeType = Enclosure::guessMimeType($enclosure->url);
                        $enclosure->thumbnailUrl = $entity->media_url_https . '?name=thumb';
                        break;

                    case 'video':
                    case 'animated_gif':
                        $enclosure->type = Enclosure::ENCLOSURE_VIDEO;
                        $enclosure->url = $entity->video_info->variants[0]->url;
                        $enclosure->mimeType = $entity->video_info->variants[0]->content_type;
                        $enclosure->thumbnailUrl = $entity->media_url_https;
                        break;

                    default:
                        $enclosure->type = Enclosure::ENCLOSURE_BINARY;
                        $enclosure->url = $entity->media_url_https;
                        break;
                }

                $enclosures[] = $enclosure;
            }
        }
        $atom->enclosures = $enclosures;

        return $atom;
    }

    /**
     * {@inheritdoc}
     */
    public function getAtomFullFromURL($url)
    {
        $matches = [];
        if (preg_match('#^https://twitter\.com/[^/]+/status/(\d+)/?$#', $url, $matches) != 0) {
            $identifier = $matches[1];
            return $this->getAtomFull($identifier);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function saveAtom($atom)
    {
        // TODO
        throw new NotImplementedException('Provider does not support this feature.');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAtom($identifier)
    {
        // TODO
        throw new NotImplementedException('Provider does not support this feature.');
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function saveCategory($category)
    {
        throw new NotImplementedException('Provider does not support this feature.');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCategory($identifier)
    {
        throw new NotImplementedException('Provider does not support this feature.');
    }
}
