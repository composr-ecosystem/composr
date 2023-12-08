<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    extended_media_rendering
 */

/**
 * Hook class.
 */
class Hook_media_rendering_video_cnn extends Media_renderer_with_fallback
{
    /**
     * Get the label for this media rendering type.
     *
     * @return string The label
     */
    public function get_type_label()
    {
        require_lang('video_cnn');
        return do_lang('MEDIA_TYPE_' . preg_replace('#^Hook_media_rendering_#', '', __CLASS__));
    }

    /**
     * Find the media types this hook serves.
     *
     * @return integer The media type(s), as a bitmask
     */
    public function get_media_type()
    {
        return MEDIA_TYPE_VIDEO;
    }

    /**
     * See if we can recognise this mime type.
     *
     * @param  ID_TEXT $mime_type The mime type
     * @return integer Recognition precedence
     */
    public function recognises_mime_type($mime_type)
    {
        return MEDIA_RECOG_PRECEDENCE_NONE;
    }

    /**
     * See if we can recognise this URL pattern.
     *
     * @param  URLPATH $url URL to pattern match
     * @return integer Recognition precedence
     */
    public function recognises_url($url)
    {
        if (preg_match('#^https?://(edition\.|www\.)?cnn\.com/.*/video/(.*)\.html#', $url) != 0) {
            return MEDIA_RECOG_PRECEDENCE_HIGH;
        }
        return MEDIA_RECOG_PRECEDENCE_NONE;
    }

    /**
     * If we can handle this URL, get the thumbnail URL.
     *
     * @param  URLPATH $src_url Video URL
     * @return ?string The thumbnail URL (null: no match).
     */
    public function get_video_thumbnail($src_url)
    {
        $matches = array();
        if (preg_match('#^https?://(edition\.|www\.)?cnn\.com/.*/video/(.*)\.html#', $src_url, $matches) != 0) {
            return 'http://i.cdn.turner.com/cnn/video/' . $matches[3] . '.214x122.jpg';
        }
        return null;
    }

    /**
     * Provide code to display what is at the URL, in the most appropriate way.
     *
     * @param  mixed $url URL to render
     * @param  mixed $url_safe URL to render (no sessions etc)
     * @param  array $attributes Attributes (e.g. width, height, length)
     * @param  boolean $as_admin Whether there are admin privileges, to render dangerous media types
     * @param  ?MEMBER $source_member Member to run as (null: current member)
     * @return Tempcode Rendered version
     */
    public function render($url, $url_safe, $attributes, $as_admin = false, $source_member = null)
    {
        $ret = $this->fallback_render($url, $url_safe, $attributes, $as_admin, $source_member, $url);
        if ($ret !== null) {
            return $ret;
        }

        if (is_object($url)) {
            $url = $url->evaluate();
        }
        $attributes['remote_id'] = preg_replace('#^https?://(edition\.|www\.)?cnn\.com/.*/video/(.*)\.html#', '${2}', $url);
        return do_template('MEDIA_VIDEO_CNN', array('_GUID' => '9b6a695ff7556a955a17a07fc4b77bf6', 'HOOK' => 'video_cnn') + _create_media_template_parameters($url, $attributes, $as_admin, $source_member));
    }
}
