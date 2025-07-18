<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/*EXTRA FUNCTIONS: shell_exec*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/**
 * Render an 'IMG_WIDTH'/'IMG_HEIGHT' symbol.
 *
 * @param  array $param Symbol parameters
 * @return array A pair: Image dimensions
 *
 * @ignore
 */
function _symbol_image_dims($param)
{
    $value = array('', '');

    if (!function_exists('imagecreatefromstring')) {
        return $value;
    }

    if (running_script('install')) {
        return $value;
    }

    if (isset($param[0])) {
        $path = $param[0];
        if ($path == '') {
            return $value;
        }

        $cacheable = (isset($param[1]) && $param[1] == '1');

        if ($cacheable) {
            $cache = persistent_cache_get('IMAGE_DIMS');
            if (isset($cache[$path])) {
                return $cache[$path];
            }
        }

        $only_if_local = (isset($param[2]) && $param[2] == '1');

        $base_url = get_base_url();
        $custom_base_url = get_custom_base_url();

        if ((strpos($path, '.php') === false) && (substr($path, 0, strlen($base_url)) == $base_url) && (is_image($path))) {
            $details = cms_getimagesize(get_file_base() . '/' . urldecode(substr($path, strlen($base_url) + 1)));
        } elseif ((strpos($path, '.php') === false) && (substr($path, 0, strlen($custom_base_url)) == $custom_base_url) && (is_image($path))) {
            $details = cms_getimagesize(get_custom_file_base() . '/' . urldecode(substr($path, strlen($custom_base_url) + 1)));
        } else {
            if ($only_if_local) {
                return $value;
            }

            $from_file = http_download_file($path, 1024 * 1024 * 20/*reasonable limit*/, false);
            $details = cms_getimagesizefromstring($from_file, get_file_extension($path));
        }

        if ($details !== false) {
            $value = array(strval($details[0]), strval($details[1]));
        }

        if ($cacheable) {
            $cache[$path] = $value;
            persistent_cache_set('IMAGE_DIMS', $cache);
        }
    }
    return $value;
}

/**
 * Render a 'THUMBNAIL' symbol.
 *
 * @param  array $param Symbol parameters
 * @return string Rendered symbol
 *
 * @ignore
 */
function _symbol_thumbnail($param)
{
    $value = '';

    // Usage: {$THUMBNAIL,source,widthxheight,directory,filename,fallback,type,where,option,only_make_smaller}
    // source: URL of image to thumbnail
    // widthxheight: The desired width and height, separated by a lowercase x. Can be -1 to mean "don't care", but better to use type as "width" or "height"
    // directory: Where to save the thumbnail to
    // filename: A suggested filename to use (if the default choice causes problems)
    // fallback: The URL to an image we can use if the thumbnail fails (eg. the original)
    // type: One of "box" (scale both dimensions in proportional so it fits a box), "width" (scale until the width matches), "height" (scale until the height matches), "crop" (scale until one dimension's right, cut off the remainder),
    //      "pad" (scale down until the image fits completely inside the dimensions, then optionally fill the gaps), "pad_horiz_crop_horiz" (fit to height, cropping or
    //      padding as needed) or "pad_vert_crop_vert" (fit to width, padding or cropping as needed)
    // where: If padding or cropping, specifies where to crop or pad. One of "start", "end", "both", "start_if_vertical", "end_if_vertical", "start_if_horizontal", or "end_if_vhorizontal"
    // option: An extra option if desired. If type is "pad" then this can be a hex colour for the padding
    // only_make_smaller: Whether to avoid growing small images to fit (smaller images are better for the Web). One of 0 (false) or 1 (true)
    if (!empty($param[0])) {
        disable_php_memory_limit();

        $only_make_smaller = isset($param[8]) ? ($param[8] == '1') : false;
        $orig_url = $param[0]; // Source for thumbnail generation
        if (url_is_local($orig_url)) {
            $orig_url = get_custom_base_url() . '/' . $orig_url;
        }
        if (!array_key_exists(1, $param)) {
            $param[1] = get_option('thumb_width');
        }
        $dimensions = $param[1]; // Generation dimensions.
        $exp_dimensions = explode('x', $dimensions);
        if ((count($exp_dimensions) == 1) || (!is_numeric($exp_dimensions[1]))) {
            $exp_dimensions[1] = '-1';
        }

        if (!is_numeric($exp_dimensions[0])) {
            $exp_dimensions[0] = '-1';
        }

        $algorithm = 'box';
        if (isset($param[5]) && in_array(trim($param[5]), array('width', 'height', 'crop', 'pad', 'pad_horiz_crop_horiz', 'pad_vert_crop_vert'))) {
            $algorithm = trim($param[5]);
        }

        $fallback = (trim(isset($param[4]) ? $param[4] : '') == '') ? $orig_url : $param[4];

        if (isset($param[2]) && $param[2] != '') { // Where we are saving to
            $thumb_save_dir = $param[2];
            if (strpos($thumb_save_dir, '/') === false) {
                $thumb_save_dir = 'uploads/' . $thumb_save_dir;
            }
        } else {
            //$thumb_save_dir = dirname(rawurldecode(preg_replace('#' . preg_quote(get_custom_base_url() . '/', '#') . '#', '', $orig_url)));  Annoying
            $thumb_save_dir = 'uploads/auto_thumbs';
        }
        if (!is_dir(get_custom_file_base() . '/' . $thumb_save_dir)) {
            $thumb_save_dir = 'uploads/website_specific';
        }
        if (isset($param[3]) && $param[3] != '') { // We can take a third parameter that hints what filename to save with (useful to avoid filename collisions within the thumbnail filename subspace). Otherwise we based on source's filename
            $filename = $param[3];
        } else {
            $ext = get_file_extension($orig_url);
            if (!is_image('example.' . $ext)) {
                $ext = 'png';
            }
            $filename = url_to_filename($orig_url);
            if (substr($filename, -4) != '.' . $ext) {
                $filename .= '.' . $ext;
            }
        }
        if (!is_saveable_image($filename)) {
            $filename .= '.png';
        }
        $file_prefix = '/' . $thumb_save_dir . '/thumb__' . $dimensions . '__' . $algorithm;
        if (isset($param[6])) {
            $file_prefix .= '__' . trim($param[6]);
        }
        if (isset($param[7])) {
            $file_prefix .= '__' . trim(str_replace('#', '', $param[7]));
        }
        $save_path = get_custom_file_base() . $file_prefix . '__' . $filename;
        $value = get_custom_base_url() . $file_prefix . '__' . rawurlencode($filename);

        // Only bother calculating the image if we've not already
        // made one with these options
        if ((!is_file($save_path)) && (!is_file($save_path . '.png'))) {
            if (!function_exists('imagepng')) {
                if (($fallback != '') && ($fallback != $param[0])) {
                    $param[0] = $fallback;
                    $param[4] = '';
                    return _symbol_thumbnail($param);
                }

                return $fallback;
            }

            // Branch based on the type of thumbnail we're making
            if ($algorithm == 'box') {
                // We just need to scale to the given dimension
                $result = @convert_image($orig_url, $save_path, -1, -1, intval($exp_dimensions[0]), false, null, false, $only_make_smaller);
            } elseif ($algorithm == 'width' || $algorithm == 'height') {
                // We just need to scale to the given dimension
                $result = @convert_image($orig_url, $save_path, ($algorithm == 'width') ? intval($exp_dimensions[0]) : -1, ($algorithm == 'height') ? intval($exp_dimensions[1]) : -1, -1, false, null, false, $only_make_smaller);
            } elseif ($algorithm == 'crop' || $algorithm == 'pad' || $algorithm == 'pad_horiz_crop_horiz' || $algorithm == 'pad_vert_crop_vert') {
                // We need to shrink a bit and crop/pad
                require_code('files');

                // Find dimensions of the source
                $converted_to_path = convert_url_to_path($orig_url);
                if (!is_null($converted_to_path)) {
                    $sizes = cms_getimagesize($converted_to_path);
                    if ($sizes === false) {
                        if (($fallback != '') && ($fallback != $param[0])) {
                            $param[0] = $fallback;
                            $param[4] = '';
                            return _symbol_thumbnail($param);
                        }

                        return $fallback;
                    }
                    list($source_x, $source_y) = $sizes;
                } else {
                    $source = cms_imagecreatefromstring(http_download_file($orig_url, null, false), get_file_extension($orig_url));
                    if ($source === false) {
                        if (($fallback != '') && ($fallback != $param[0])) {
                            $param[0] = $fallback;
                            $param[4] = '';
                            return _symbol_thumbnail($param);
                        }

                        return $fallback;
                    }
                    $source_x = imagesx($source);
                    $source_y = imagesy($source);
                    imagedestroy($source);
                }

                // We only need to crop/pad if the aspect ratio
                // differs from what we want
                $source_aspect = floatval($source_x) / floatval($source_y);
                if ($exp_dimensions[1] == '0') {
                    $exp_dimensions[1] = '1';
                }

                if ($exp_dimensions[0] == '-1') {
                    $exp_dimensions[0] = float_to_raw_string(round((floatval($exp_dimensions[1]) * $source_aspect)));
                }
                if ($exp_dimensions[1] == '-1') {
                    $exp_dimensions[1] = float_to_raw_string(round((floatval($exp_dimensions[0]) * (1 / $source_aspect))));
                }

                $destination_aspect = floatval($exp_dimensions[0]) / floatval($exp_dimensions[1]);

                // We test the scaled sizes, rather than the ratios
                // directly, so that differences too small to affect
                // the integer dimensions will be tolerated.
                if ($source_aspect > $destination_aspect) {
                    // The image is wider than the output.
                    if (($algorithm == 'crop') || ($algorithm == 'pad_horiz_crop_horiz')) {
                        // Is it too wide, requiring cropping?
                        $scale = floatval($source_y) / floatval($exp_dimensions[1]);
                        $modify_image = intval(round(floatval($source_x) / $scale)) != intval($exp_dimensions[0]);
                        if ($modify_image) {
                            $algorithm = 'crop';
                        }
                    } else {
                        // Is the image too short, requiring padding?
                        $scale = floatval($source_x) / floatval($exp_dimensions[0]);
                        $modify_image = intval(round(floatval($source_y) / $scale)) != intval($exp_dimensions[1]);
                        if ($modify_image) {
                            $algorithm = 'pad';
                        }
                    }
                } elseif ($source_aspect < $destination_aspect) {
                    // The image is taller than the output
                    if (($algorithm == 'crop') || ($algorithm == 'pad_vert_crop_vert')) {
                        // Is it too tall, requiring cropping?
                        $scale = floatval($source_x) / floatval($exp_dimensions[0]);
                        $modify_image = intval(round(floatval($source_y) / $scale)) != intval($exp_dimensions[1]);
                        if ($modify_image) {
                            $algorithm = 'crop';
                        }
                    } else {
                        // Is the image too narrow, requiring padding?
                        $scale = floatval($source_y) / floatval($exp_dimensions[1]);
                        $modify_image = intval(round(floatval($source_x) / $scale)) != intval($exp_dimensions[0]);
                        if ($modify_image) {
                            $algorithm = 'pad';
                        }
                    }
                } else {
                    // They're the same, within the tolerances of
                    // floating point arithmetic. Just scale it.
                    if ($source_x != intval($exp_dimensions[0]) || $source_y != intval($exp_dimensions[1])) {
                        $scale = floatval($source_x) / floatval($exp_dimensions[0]);
                        $modify_image = true;
                    } else {
                        $modify_image = false;
                    }
                }

                // We have a special case here, since we can "pad" an
                // image with nothing, ie. shrink it to fit in the
                // output dimensions. This means we don't need to
                // modify the image contents either, just scale it.
                if (($algorithm == 'pad' || $algorithm == 'pad_horiz_crop_horiz' || $algorithm == 'pad_vert_crop_vert') && isset($algorithm) && (!isset($param[6]) || trim($param[6]) == '')) {
                    $modify_image = false;
                }

                // Now do the cropping, padding and scaling
                if ($modify_image) {
                    $result = @convert_image($orig_url, $save_path, intval($exp_dimensions[0]), intval($exp_dimensions[1]), -1, false, null, false, $only_make_smaller, array('type' => $algorithm, 'background' => (isset($param[7]) ? trim($param[7]) : null), 'where' => (isset($param[6]) ? trim($param[6]) : 'both'), 'scale' => $scale));
                } else {
                    // Just resize
                    $result = @convert_image($orig_url, $save_path, intval($exp_dimensions[0]), intval($exp_dimensions[1]), -1, false, null, false, $only_make_smaller);
                }
            }

            // If the conversion failed then give back the fallback,
            // or if it's empty then give back the original image
            if (!$result) {
                if (($fallback != '') && ($fallback != $param[0])) {
                    $param[0] = $fallback;
                    $param[4] = '';
                    return _symbol_thumbnail($param);
                }

                $value = $fallback;
            }
        }

        if ((!file_exists($save_path)) && (file_exists($save_path . '.png'))) {
            $value .= '.png';
        }
    }

    return $value;
}

/**
 * Find image dimensions. Better than PHP's built-in getimagesize as it gets the correct size for animated gifs.
 *
 * @param  string $path The path to the image file
 * @param  ?string $ext File extension (null: get from path, even if not detected this function will mostly work)
 * @return ~array The width and height (false: error)
 */
function cms_getimagesize($path, $ext = null)
{
    if ($ext === null) {
        $ext = get_file_extension($path);
    }

    if ($ext == 'gif') {
        $data = @cms_file_get_contents_safe($path);
        if ($data === false) {
            return false;
        }
        return cms_getimagesizefromstring($data, $ext);
    }

    if (function_exists('getimagesize')) {
        $details = @getimagesize($path);
        if ($details !== false) {
            return array(max(1, $details[0]), max(1, $details[1]));
        }
    }

    return false;
}

/**
 * Find image dimensions from a string. Better than PHP's built-in getimagesize as it gets the correct size for animated gifs.
 *
 * @param  string $data The image file data
 * @param  ?string $ext File extension (null: unknown)
 * @return ~array The width and height (false: error)
 */
function cms_getimagesizefromstring($data, $ext = null)
{
    if ($ext === 'gif') { // Workaround problem with animated gifs
        $header = @unpack('@6/' . 'vwidth/' . 'vheight', $data);
        if ($header !== false) {
            $sx = $header['width'];
            $sy = $header['height'];
            return array(max(1, $sx), max(1, $sy));
        }
    }

    if (function_exists('getimagesizefromstring')) {
        $details = @getimagesizefromstring($data);
        if ($details !== false) {
            return array(max(1, $details[0]), max(1, $details[1]));
        }
    } else {
        $img_res = cms_imagecreatefromstring($data, $ext);
        if ($img_res !== false) {
            $sx = imagesx($img_res);
            $sy = imagesy($img_res);

            imagedestroy($img_res);

            return array(max(1, $sx), max(1, $sy));
        }
    }

    return false;
}

/**
 * Get the maximum allowed image size, as set in the configuration.
 *
 * @param  boolean $consider_php_limits Whether to consider limitations in PHP's configuration
 * @return integer The maximum image size, in bytes
 */
function get_max_image_size($consider_php_limits = true)
{
    require_code('files');
    $a = php_return_bytes(ini_get('upload_max_filesize'));
    $b = php_return_bytes(ini_get('post_max_size'));
    $c = intval(get_option('max_download_size')) * 1024;
    if (has_privilege(get_member(), 'exceed_filesize_limit')) {
        $c = 0;
    }

    $possibilities = array();
    if ($consider_php_limits) {
        if ($a != 0) {
            $possibilities[] = $a;
        }
        if ($b != 0) {
            $possibilities[] = $b;
        }
    }
    if ($c != 0) {
        $possibilities[] = $c;
    }

    return (count($possibilities) == 0) ? (1024 * 1024 * 1024 * 1024) : min($possibilities);
}

/**
 * Get the Tempcode for an image thumbnail.
 *
 * @param  URLPATH $url The URL to the image thumbnail
 * @param  mixed $caption The caption for the thumbnail (string or Tempcode)
 * @param  boolean $js_tooltip Whether to use a JS tooltip. Forcibly set to true if you pass Tempcode
 * @param  boolean $is_thumbnail_already Whether already a thumbnail (if not, function will make one)
 * @param  ?integer $width Thumbnail width to use (null: default)
 * @param  ?integer $height Thumbnail height to use (null: default)
 * @param  boolean $only_make_smaller Whether to apply a 'never make the image bigger' rule for thumbnail creation (would affect very small images)
 * @return Tempcode The thumbnail
 */
function do_image_thumb($url, $caption, $js_tooltip = false, $is_thumbnail_already = true, $width = null, $height = null, $only_make_smaller = false)
{
    if (is_object($caption)) {
        $js_tooltip = true;
    }

    $url = preg_replace('#' . preg_quote(get_custom_base_url() . '/', '#') . '#', '', $url);

    $default_size = ($width === null) && ($height === null);
    $box_size = $default_size;

    if ($width === null) {
        $width = intval(get_option('thumb_width'));
    }
    if ($height === null) {
        $height = intval(get_option('thumb_width'));
    }

    if (!$is_thumbnail_already) {
        $new_name = '';
        if (!$default_size) {
            $new_name .= strval($width) . '_' . strval($height) . '_';
        }
        if ($only_make_smaller) {
            $new_name .= 'only_smaller_';
        }
        $new_name .= url_to_filename($url);

        if ((!is_saveable_image($new_name)) && (get_file_extension($new_name) != 'svg')) {
            $new_name .= '.png';
        }

        $file_thumb = get_custom_file_base() . '/uploads/auto_thumbs/' . $new_name;

        if (url_is_local($url)) {
            $url = get_custom_base_url() . '/' . $url;
        }

        if (!file_exists($file_thumb)) {
            convert_image($url, $file_thumb, $box_size ? -1 : $width, $box_size ? -1 : $height, $box_size ? $width : -1, false, null, false, $only_make_smaller);
            if (!file_exists($file_thumb) && file_exists($file_thumb . '.png')/*convert_image maybe had to change the extension*/) {
                $new_name .= '.png';
            }
        }

        $url = get_custom_base_url() . '/uploads/auto_thumbs/' . rawurlencode($new_name);
    }

    if (url_is_local($url)) {
        $url = get_custom_base_url() . '/' . $url;
    }

    if ((!is_object($caption)) && ($caption == '')) {
        $caption = do_lang('THUMBNAIL');
        $js_tooltip = false;
    }
    return do_template('IMG_THUMB', array('_GUID' => 'f1c130b7c3b2922fe273596563cb377c', 'JS_TOOLTIP' => $js_tooltip, 'CAPTION' => $caption, 'URL' => $url));
}

/**
 * Take some image/thumbnail info, and if needed make and caches a thumbnail, and return a thumb url whatever the situation.
 *
 * @param  URLPATH $full_url The full URL to the image which will-be/is thumbnailed
 * @param  URLPATH $thumb_url The URL to the thumbnail (blank: no thumbnail yet)
 * @param  ID_TEXT $thumb_dir The directory, relative to the Composr install's uploads directory, where the thumbnails are stored. MINUS "_thumbs"
 * @param  ID_TEXT $table The name of the table that is storing what we are doing the thumbnail for
 * @param  AUTO_LINK $id The ID of the table record that is storing what we are doing the thumbnail for
 * @param  ID_TEXT $thumb_field_name The name of the table field where thumbnails are saved
 * @param  ?integer $thumb_width The thumbnail width to use (null: default)
 * @param  boolean $only_make_smaller Whether to apply a 'never make the image bigger' rule for thumbnail creation (would affect very small images)
 * @return URLPATH The URL to the thumbnail
 */
function ensure_thumbnail($full_url, $thumb_url, $thumb_dir, $table, $id, $thumb_field_name = 'thumb_url', $thumb_width = null, $only_make_smaller = false)
{
    if ($full_url == $thumb_url) {
        // Special case
        return $thumb_url;
    }

    if ($thumb_width === null) {
        $thumb_width = intval(get_option('thumb_width'));
    }

    if ((!function_exists('imagetypes')) || ($full_url == '')) {
        if ((url_is_local($thumb_url)) && ($thumb_url != '')) {
            return get_custom_base_url() . '/' . $thumb_url;
        }
        return $thumb_url;
    }

    // Ensure existing path still exists
    if ($thumb_url != '') {
        if (url_is_local($thumb_url)) {
            $thumb_path = get_custom_file_base() . '/' . rawurldecode($thumb_url);
            if (!file_exists($thumb_path)) {
                $from = str_replace(' ', '%20', $full_url);
                if (url_is_local($from)) {
                    $from = get_custom_base_url() . '/' . $from;
                }

                if (is_image($from)) {
                    convert_image($from, $thumb_path, -1, -1, intval($thumb_width), false);
                } else {
                    if (addon_installed('galleries')) {
                        require_code('galleries2');
                        create_video_thumb($full_url, $thumb_path);
                    }
                }
            }
            return get_custom_base_url() . '/' . $thumb_url;
        }
        return $thumb_url;
    }

    require_code('images2');
    return _ensure_thumbnail($full_url, $thumb_url, $thumb_dir, $table, $id, $thumb_field_name, $thumb_width, $only_make_smaller);
}

/**
 * Resize an image to the specified size, but retain the aspect ratio. Does not retain EXIF data, see copy_exif_data for that.
 *
 * @param  string $from The URL to the image to resize. May be either relative or absolute. If $using_path is set it is actually a path
 * @param  PATH $to The file path (including filename) to where the resized image will be saved
 * @param  integer $width The maximum width we want our new image to be (-1 means "don't factor this in")
 * @param  integer $height The maximum height we want our new image to be (-1 means "don't factor this in")
 * @param  integer $box_width This is only considered if both $width and $height are -1. If set, it will fit the image to a box of this dimension (suited for resizing both landscape and portraits fairly)
 * @param  boolean $exit_on_error Whether to exit Composr if an error occurs
 * @param  ?string $ext2 The file extension to save with (null: same as our input file)
 * @param  boolean $using_path Whether $from was in fact a path, not a URL
 * @param  boolean $only_make_smaller Whether to apply a 'never make the image bigger' rule for thumbnail creation (would affect very small images)
 * @param  ?array $thumb_options This optional parameter allows us to specify cropping or padding for the image. See comments in the function. (null: no details passed)
 * @return boolean Success
 */
function convert_image($from, $to, $width, $height, $box_width = -1, $exit_on_error = true, $ext2 = null, $using_path = false, $only_make_smaller = true, $thumb_options = null)
{
    // TODO: Make sure in v11 $to is returned by reference, as it may get changed if the output file type has to be changed for feature-preservation

    require_code('images2');
    cms_profile_start_for('convert_image');
    $ret = _convert_image($from, $to, $width, $height, $box_width, $exit_on_error, $ext2, $using_path, $only_make_smaller, $thumb_options);
    cms_profile_end_for('convert_image', $from);
    return $ret;
}

/**
 * Find whether the image specified is actually an image, based on file extension
 *
 * @param  string $name A URL or file path to the image
 * @param  boolean $mime_too Whether to check mime too
 * @return boolean Whether the string pointed to a file appeared to be an image
 */
function is_image($name, $mime_too = false)
{
    if (substr(basename($name), 0, 1) == '.') {
        return false; // Temporary file that some OS's make
    }

    $ext = get_file_extension($name);

    static $types = null;
    if ($types === null) {
        $types = explode(',', get_option('valid_images'));
    }
    foreach ($types as $val) {
        if (strtolower($val) == $ext) {
            return true;
        }
    }

    if (($mime_too) && (looks_like_url($name))) {
        http_download_file($name, 0, false);
        global $HTTP_DOWNLOAD_MIME_TYPE;
        if (preg_match('#^image/(png|gif|jpeg)$#', $HTTP_DOWNLOAD_MIME_TYPE) != 0) {
            return true;
        }
    }

    return false;
}

/**
 * Use the image extension to determine if the specified image is of a format (extension) saveable by Composr or not.
 *
 * @param  string $name A URL or file path to the image
 * @return boolean Whether the string pointed to a file that appeared to be a saveable image
 */
function is_saveable_image($name)
{
    $ext = get_file_extension($name);
    if (function_exists('imagetypes')) {
        $gd = imagetypes();
        if (($ext == 'gif') && (($gd & IMG_GIF) != 0) && (function_exists('imagegif'))) {
            return true;
        }
        if (($ext == 'jpg') && (($gd & IMG_JPEG) != 0)) {
            return true;
        }
        if (($ext == 'jpeg') && (($gd & IMG_JPEG) != 0)) {
            return true;
        }
        if (($ext == 'png') && (($gd & IMG_PNG) != 0)) {
            return true;
        }
        return false;
    } else {
        return (($ext == 'jpg') || ($ext == 'jpeg') || ($ext == 'png'));
    }
}

/*
What follows are other media types, not images. However, we define them here to avoid having to explicitly load the full media rendering API.
*/

/**
 * Find whether the video specified is actually a 'video', based on file extension
 *
 * @param  string $name A URL or file path to the video
 * @param  boolean $as_admin Whether there are admin privileges, to render dangerous media types (client-side risk only)
 * @param  boolean $must_be_true_video Whether it really must be an actual video/audio, not some other kind of rich media which we may render in a video spot
 * @return boolean Whether the string pointed to a file appeared to be a video
 */
function is_video($name, $as_admin, $must_be_true_video = false)
{
    $allow_audio = (get_option('allow_audio_videos') == '1');

    if (is_image($name)) {
        return false;
    }

    if ($must_be_true_video) {
        require_code('mime_types');
        $ext = get_file_extension($name);
        if (($ext == 'rm') || ($ext == 'ram')) {
            return true; // These have audio mime types, but may be videos
        }
        $mime_type = get_mime_type($ext, $as_admin);
        return ((substr($mime_type, 0, 6) == 'video/') || (($allow_audio) && (substr($mime_type, 0, 6) == 'audio/')));
    }

    require_code('media_renderer');
    $acceptable_media = $allow_audio ? (MEDIA_TYPE_VIDEO | MEDIA_TYPE_AUDIO | MEDIA_TYPE_OTHER /* but not images */) : MEDIA_TYPE_VIDEO;
    $hooks = find_media_renderers($name, array(), $as_admin, null, $acceptable_media);
    $hooks = array_diff($hooks, array('hyperlink', 'code'));
    return !empty($hooks);
}

/**
 * Find whether the video specified is actually a 'video', based on file extension
 *
 * @param  string $name A URL or file path to the video
 * @param  boolean $as_admin Whether there are admin privileges, to render dangerous media types (client-side risk only)
 * @param  boolean $definitive_over_video Whether to favour "no" if it could also be a format with video in it
 * @return boolean Whether the string pointed to a file appeared to be an audio file
 */
function is_audio($name, $as_admin, $definitive_over_video = false)
{
    require_code('files');
    require_code('mime_types');
    $mime_type = get_mime_type(get_file_extension($name), $as_admin);
    if (substr($mime_type, 0, 6) == 'video/') {
        return false;
    }

    require_code('media_renderer');
    $acceptable_media = MEDIA_TYPE_AUDIO;
    $hooks = find_media_renderers($name, array(), $as_admin, null, $acceptable_media);
    return !is_null($hooks);
}

/**
 * Find whether the video specified is actually a 'video', based on file extension
 *
 * @param  string $name A URL or file path to the video
 * @param  boolean $as_admin Whether there are admin privileges, to render dangerous media types (client-side risk only)
 * @return boolean Whether the string pointed to a file appeared to be an audio file
 */
function is_media($name, $as_admin)
{
    require_code('media_renderer');
    $hooks = find_media_renderers($name, array(), $as_admin, null);
    return !is_null($hooks);
}

/**
 * Get a comma-separated list of allowed file types for audio upload.
 *
 * @return string Allowed file types
 */
function get_allowed_image_file_types()
{
    $supported = str_replace(' ', '', get_option('valid_images'));
    return $supported;
}

/**
 * Get a comma-separated list of allowed file types for video upload.
 *
 * @return string Allowed file types
 */
function get_allowed_video_file_types()
{
    $supported = str_replace(' ', '', get_option('valid_videos'));
    if (get_option('allow_audio_videos') == '1') {
        $supported .= ',' . get_allowed_audio_file_types();
    }
    $supported .= ',pdf';
    if (has_privilege(get_member(), 'use_very_dangerous_comcode')) {
        $supported .= ',swf';
    }
    return $supported;
}

/**
 * Get a comma-separated list of allowed file types for audio upload.
 *
 * @return string Allowed file types
 */
function get_allowed_audio_file_types()
{
    $supported = str_replace(' ', '', get_option('valid_audios'));
    return $supported;
}

/**
 * Load a GD image resource from a path.
 *
 * @param  PATH $path Path to load from
 * @param  ?string $ext File extension (null: get from path, even if not detected this function will mostly work)
 * @return ~resource Image resource (false: error)
 */
function cms_imagecreatefrom($path, $ext = null)
{
    if ($ext === null) {
        $ext = get_file_extension($path);
    }

    if ((function_exists('imagecreatefromgif')) && ($ext == 'gif')) {
        $image = @imagecreatefromgif($path);
    } elseif ($ext == 'jpg' || $ext == 'jpeg') {
        $image = @imagecreatefromjpeg($path);
    } elseif ($ext == 'png') {
        $image = @imagecreatefrompng($path);
        if ($image !== false) {
            _fix_corrupt_png_alpha($image, $path);
        }
    } else {
        return cms_imagecreatefromstring(cms_file_get_contents_safe($path), null); // Maybe it can be autodetected
    }

    return $image;
}

/**
 * Load a GD image resource from a string.
 *
 * @param  string $data String to load from
 * @param  ?string $ext File extension (null: unknown)
 * @return ~resource Image resource (false: error)
 */
function cms_imagecreatefromstring($data, $ext = null)
{
    if (!function_exists('imagecreatefromstring')) {
        return false;
    }

    $image = @imagecreatefromstring($data);

    if ($ext === 'png') {
        if ($image !== false) {
            if (_will_fix_corrupt_png_alpha($image)) {
                $path = cms_tempnam();
                file_put_contents($path, $data);

                _fix_corrupt_png_alpha($image, $path);

                unlink($path);
            }
        }
    }

    return $image;
}

/**
 * GD may have a bug with not loading up non-alpha transparency properly. Find if we need to fix that.
 *
 * @param  resource $image Image resource
 * @return boolean Whether we need to do a fix
 */
function _will_fix_corrupt_png_alpha($image)
{
    if ((function_exists('imageistruecolor')) && (function_exists('imagecreatetruecolor'))) {
        if ((php_function_allowed('shell_exec')) && (php_function_allowed('escapeshellarg'))) {
            if (!imageistruecolor($image)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * GD may have a bug with not loading up non-alpha transparency properly. Fix that.
 *
 * @param  resource $image Image resource
 * @param  PATH $path Path to PNG file
 */
function _fix_corrupt_png_alpha(&$image, $path)
{
    if (_will_fix_corrupt_png_alpha($image)) {
        require_code('images2');
        $imagemagick = get_option('imagemagick_path');
        if ($imagemagick != '') {
            if ((php_function_allowed('shell_exec')) && (php_function_allowed('escapeshellarg'))) {
                $tempnam = cms_tempnam();
                shell_exec($imagemagick . ' -depth 32 ' . escapeshellarg($path) . ' PNG32:' . $tempnam);
                if ((is_file($tempnam)) && (filesize($tempnam) > 0)) {
                    $image = @imagecreatefrompng($tempnam);
                    @unlink($tempnam);
                }
            }
        }
    }
}

/**
 * Save a GD image.
 *
 * @param  resource $image Image resource
 * @param  PATH $path Path to save to
 * @param  ?string $ext File extension (null: get from path)
 * @param  boolean $lossy Allow optional lossy compression
 * @param  ?boolean $unknown_format Returned by reference as true if the file format was unknown (null: not passed)
 * @return ~resource Image resource (false: error)
 */
function cms_imagesave($image, $path, $ext = null, $lossy = false, &$unknown_format = null)
{
    if ($ext === null) {
        $ext = get_file_extension($path);
    }

    imagealphablending($image, false);
    if (function_exists('imagesavealpha')) {
        imagesavealpha($image, true);
    }

    if ((function_exists('imagepng')) && ($ext == 'png')) {
        $test = @imagepng($image, $path, 9);
        if ($test !== false) {
            require_code('images_cleanup_pipeline');
            png_compress($path, $lossy);
        }
    } elseif ((function_exists('imagejpeg')) && (($ext == 'jpg') || ($ext == 'jpeg'))) {
        $test = @imagejpeg($image, $path, intval(get_option('jpeg_quality')));
    } elseif ((function_exists('imagegif')) && ($ext == 'gif')) {
        $test = @imagegif($image, $path);
    } elseif ((function_exists('imagebmp')) && ($ext == 'bmp')) {
        $test = @imagebmp($image, $path);
    } elseif ((function_exists('imagewebp')) && ($ext == 'webp')) {
        $test = @imagewebp($image, $path);
    } else {
        $unknown_format = true;
        $test = false;
    }

    if ($test) {
        sync_file($path);
        fix_permissions($path);
    }

    return $test;
}
