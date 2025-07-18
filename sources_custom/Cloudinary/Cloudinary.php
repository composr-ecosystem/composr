<?php

/**
 * Converts an uploaded file into a URL, by moving it to an appropriate place on the CDN.
 *
 * @param  ID_TEXT $attach_name The name of the HTTP file parameter storing the upload.
 * @param  ID_TEXT $upload_folder The folder name in uploads/ where we would normally put this upload, if we weren't transferring it to the CDN
 * @param  string $filename Filename to upload with. May not be respected, depending on service implementation
 * @param  integer $obfuscate Whether to obfuscate file names so the URLs can not be guessed/derived (0=do not, 1=do, 2=make extension .bin as well)
 * @set    0 1 2
 * @param  boolean $accept_errors Whether to accept upload errors
 * @return ?array A pair: the URL and the filename (NULL: did nothing)
 */
function cloudinary_transfer_upload($attach_name, $upload_folder, $filename, $obfuscate = 0, $accept_errors = false)
{
    require_code('Cloudinary/Uploader');

    \Cloudinary::config(array(
        'cloud_name' => $cloud_name,
        'api_key' => $api_key,
        'api_secret' => $api_secret,
    ));

    $tags = array(
        $GLOBALS['FORUM_DRIVER']->get_username(get_member()),
        get_site_name(),
        get_zone_name(),
        get_page_name(),
    );

    $options = array(
        'resource_type' => 'auto',
        'tags' => $tags,
        'angle' => 'exif',
    );

    if ($obfuscate != 0) {
        $options['public_id'] = $upload_folder . '/' . preg_replace('#\.[^\.]*$#', '', $filename);
    } else {
        $options['use_filename'] = true;
        $options['unique_filename'] = true;
    }

    $filearrays = array();
    get_upload_filearray($attach_name, $filearrays);

    try {
        $result = \Cloudinary\Uploader::upload(
            $filearrays[$attach_name]['tmp_name'],
            $options
        );
        if (get_param_integer('keep_fatalistic', 0) == 1) {
            attach_message(serialize($result), 'inform');
        }
    } catch (Exception $e) {
        if ($accept_errors) {
            attach_message($e->getMessage(), 'warn');
            return false;
        }
        warn_exit($e->getMessage());
    }

    if (strpos(get_base_url(), 'https://') === false) {
        $url = $result['url'];
    } else {
        $url = $result['secure_url'];
    }

    if (is_image($filename)) {
        // 1024 version
        $url = preg_replace('#^(.*/image/upload/)(.*)$#', '$1c_limit,w_1024/$2', $url);
    }

    return $url;
}

// IDEA: Support deletion. This is hard though, as we would need to track upload ownership somewhere or uniqueness (else temporary URL "uploads" could be used as a vector to hijack other people's original uploads).



/* Original Cloudinary class code follows... */

class Cloudinary {

    const CF_SHARED_CDN = "d3jpl91pxevbkh.cloudfront.net";
    const OLD_AKAMAI_SHARED_CDN = "cloudinary-a.akamaihd.net";
    const AKAMAI_SHARED_CDN = "res.cloudinary.com";
    const SHARED_CDN = "res.cloudinary.com";
    const VERSION = "1.0.17";
    const USER_AGENT = "cld-php-1.0.17";
    const BLANK = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
    const RANGE_VALUE_RE = '/^(?P<value>(\d+\.)?\d+)(?P<modifier>[%pP])?$/';
    const RANGE_RE = '/^(\d+\.)?\d+[%pP]?\.\.(\d+\.)?\d+[%pP]?$/';

    public static $DEFAULT_RESPONSIVE_WIDTH_TRANSFORMATION = array("width"=>"auto", "crop"=>"limit");

    private static $config = NULL;
    public static $JS_CONFIG_PARAMS = array("api_key", "cloud_name", "private_cdn", "secure_distribution", "cdn_subdomain");

    public static function config($values = NULL) {
        if (self::$config == NULL) {
            self::reset_config();
        }
        if ($values != NULL) {
            self::$config = array_merge(self::$config, $values);
        }
        return self::$config;
    }

    public static function reset_config() {
        self::config_from_url(getenv("CLOUDINARY_URL"));
    }

    public static function config_from_url($cloudinary_url) {
        self::$config = array();
        if ($cloudinary_url) {
            $uri = parse_url($cloudinary_url);
            $q_params = array();
            if (isset($uri["query"])) {
                parse_str($uri["query"], $q_params);
            }
            $private_cdn = isset($uri["path"]) && $uri["path"] != "/";
            $config = array_merge($q_params, array(
                            "cloud_name" => $uri["host"],
                            "api_key" => $uri["user"],
                            "api_secret" => $uri["pass"],
                            "private_cdn" => $private_cdn));
            if ($private_cdn) {
                $config["secure_distribution"] = substr($uri["path"], 1);
            }
            self::$config = array_merge(self::$config, $config);
        }
    }

    public static function config_get($option, $default=NULL) {
        return Cloudinary::option_get(self::config(), $option, $default);
    }

    public static function option_get($options, $option, $default=NULL) {
        if (isset($options[$option])) {
            return $options[$option];
        } else {
            return $default;
        }
    }

    public static function option_consume(&$options, $option, $default=NULL) {
        if (isset($options[$option])) {
            $value = $options[$option];
            unset($options[$option]);
            return $value;
        } else {
            unset($options[$option]);
            return $default;
        }
    }

    public static function build_array($value) {
        if (is_array($value) && !Cloudinary::is_assoc($value)) {
            return $value;
        } else if ($value === NULL) {
            return array();
        } else {
            return array($value);
        }
    }

    public static function encode_array($array) {
      return implode(",", Cloudinary::build_array($array));
    }

    public static function encode_double_array($array) {
      $array = Cloudinary::build_array($array);
      if (count($array) > 0 && !is_array($array[0])) {
        return Cloudinary::encode_array($array);
      } else {
        $array = array_map('Cloudinary::encode_array', $array);
      }

      return implode("|", $array);
    }

    public static function encode_assoc_array($array) {
      if (Cloudinary::is_assoc($array)){
        $encoded = array();
        foreach ($array as $key => $value) {
          array_push($encoded, $key . '=' . $value);
        }
        return implode("|", $encoded);
      } else {
        return $array;
      }
    }

    private static function is_assoc($array) {
      if (!is_array($array)) return FALSE;
      return $array != array_values($array);
    }

    private static function generate_base_transformation($base_transformation) {
        return is_array($base_transformation) ?
               Cloudinary::generate_transformation_string($base_transformation) :
               Cloudinary::generate_transformation_string(array("transformation"=>$base_transformation));
    }

    // Warning: $options are being destructively updated!
    public static function generate_transformation_string(&$options=array()) {
        $generate_base_transformation = "Cloudinary::generate_base_transformation";
        if (is_string($options)) {
            return $options;
        }
        if ($options == array_values($options)) {
            return implode("/", array_map($generate_base_transformation, $options));
        }

        $responsive_width = Cloudinary::option_consume($options, "responsive_width", Cloudinary::config_get("responsive_width"));

        $size = Cloudinary::option_consume($options, "size");
        if ($size) list($options["width"], $options["height"]) = preg_split("/x/", $size);

        $width = Cloudinary::option_get($options, "width");
        $height = Cloudinary::option_get($options, "height");

        $has_layer = Cloudinary::option_get($options, "underlay") || Cloudinary::option_get($options, "overlay");
        $angle = implode(Cloudinary::build_array(Cloudinary::option_consume($options, "angle")), ".");
        $crop = Cloudinary::option_consume($options, "crop");

        $no_html_sizes = $has_layer || !empty($angle) || $crop == "fit" || $crop == "limit" || $responsive_width;

        if (strlen($width) == 0 || $width && ($width == "auto" || floatval($width) < 1 || $no_html_sizes)) unset($options["width"]);
        if (strlen($height) == 0 || $height && (floatval($height) < 1 || $no_html_sizes)) unset($options["height"]);

        $background = Cloudinary::option_consume($options, "background");
        if ($background) $background = preg_replace("/^#/", 'rgb:', $background);
        $color = Cloudinary::option_consume($options, "color");
        if ($color) $color = preg_replace("/^#/", 'rgb:', $color);

        $base_transformations = Cloudinary::build_array(Cloudinary::option_consume($options, "transformation"));
        if (count(array_filter($base_transformations, "is_array")) > 0) {
            $base_transformations = array_map($generate_base_transformation, $base_transformations);
            $named_transformation = "";
        } else {
            $named_transformation = implode(".", $base_transformations);
            $base_transformations = array();
        }

        $effect = Cloudinary::option_consume($options, "effect");
        if (is_array($effect)) $effect = implode(":", $effect);

        $border = Cloudinary::option_consume($options, "border");
        if (is_array($border)) {
          $border_width = Cloudinary::option_get($border, "width", "2");
          $border_color = preg_replace("/^#/", 'rgb:', Cloudinary::option_get($border, "color", "black"));
          $border = $border_width . "px_solid_" . $border_color;
        }

        $flags = implode(Cloudinary::build_array(Cloudinary::option_consume($options, "flags")), ".");
        $dpr = Cloudinary::option_consume($options, "dpr", Cloudinary::config_get("dpr"));

        $duration = Cloudinary::norm_range_value(Cloudinary::option_consume($options, "duration"));
        $start_offset = Cloudinary::norm_range_value(Cloudinary::option_consume($options, "start_offset"));
        $end_offset = Cloudinary::norm_range_value(Cloudinary::option_consume($options, "end_offset"));
        $offset = Cloudinary::split_range(Cloudinary::option_consume($options, "offset"));
        if (!empty($offset)) {
            $start_offset = Cloudinary::norm_range_value($offset[0]);
            $end_offset = Cloudinary::norm_range_value($offset[1]);
        }

        $video_codec = Cloudinary::process_video_codec_param(Cloudinary::option_consume($options, "video_codec"));

        $params = array(
          "a"   => $angle,
          "b"   => $background,
          "bo"  => $border,
          "c"   => $crop,
          "co"  => $color,
          "dpr" => $dpr,
          "du"  => $duration,
          "e"   => $effect,
          "eo"  => $end_offset,
          "fl"  => $flags,
          "h"   => $height,
          "so"  => $start_offset,
          "t"   => $named_transformation,
          "vc"  => $video_codec,
          "w"   => $width);

        $simple_params = array(
          "ac" => "audio_codec",
          "af" => "audio_frequency",
          "br" => "bit_rate",
          "cs" => "color_space",
          "d"  => "default_image",
          "dl" => "delay",
          "dn" => "density",
          "f"  => "fetch_format",
          "g"  => "gravity",
          "l"  => "overlay",
          "o"  => "opacity",
          "p"  => "prefix",
          "pg" => "page",
          "q"  => "quality",
          "r"  => "radius",
          "u"  => "underlay",
          "vs" => "video_sampling",
          "x"  => "x",
          "y"  => "y",
          "z"  => "zoom");

        foreach ($simple_params as $param=>$option) {
            $params[$param] = Cloudinary::option_consume($options, $option);
        }

        $param_filter = function($value) { return $value === 0 || $value === '0' || trim($value) == true; };
        $params = array_filter($params, $param_filter);
        ksort($params);
        $join_pair = function($key, $value) { return $key . "_" . $value; };
        $transformation = implode(",", array_map($join_pair, array_keys($params), array_values($params)));
        $raw_transformation = Cloudinary::option_consume($options, "raw_transformation");
        $transformation = implode(",", array_filter(array($transformation, $raw_transformation)));
        array_push($base_transformations, $transformation);
        if ($responsive_width) {
            $responsive_width_transformation = Cloudinary::config_get("responsive_width_transformation", Cloudinary::$DEFAULT_RESPONSIVE_WIDTH_TRANSFORMATION);
            array_push($base_transformations, Cloudinary::generate_transformation_string($responsive_width_transformation));
        }
        if ($width == "auto" || $responsive_width) {
            $options["responsive"] = true;
        }
        if ($dpr == "auto") {
            $options["hidpi"] = true;
        }
        return implode("/", array_filter($base_transformations));
    }

    private static function split_range($range) {
        if (is_array($range) && count($range) >= 2) {
            return array($range[0], end($range));
        } else if (is_string($range) && preg_match(Cloudinary::RANGE_RE, $range) == 1) {
            return explode("..", $range, 2);
        } else {
            return NULL;
        }
    }

    private static function norm_range_value($value) {
        if (empty($value)) {
          return NULL;
        }

        preg_match(Cloudinary::RANGE_VALUE_RE, $value, $matches);

        if (empty($matches)) {
          return NULL;
        }

        $modifier = '';
        if (!empty($matches['modifier'])) {
          $modifier = 'p';
        }
        return $matches['value'] . $modifier;
    }

    private static function process_video_codec_param($param) {
        $out_param = $param;
        if (is_array($out_param)) {
          $out_param = $param['codec'];
          if (array_key_exists('profile', $param)) {
              $out_param = $out_param . ':' . $param['profile'];
              if (array_key_exists('level', $param)) {
                  $out_param = $out_param . ':' . $param['level'];
              }
          }
        }
        return $out_param;
    }

    // Warning: $options are being destructively updated!
    public static function cloudinary_url($source, &$options=array()) {
        $source = self::check_cloudinary_field($source, $options);
        $type = Cloudinary::option_consume($options, "type", "upload");

        if ($type == "fetch" && !isset($options["fetch_format"])) {
            $options["fetch_format"] = Cloudinary::option_consume($options, "format");
        }
        $transformation = Cloudinary::generate_transformation_string($options);

        $resource_type = Cloudinary::option_consume($options, "resource_type", "image");
        $version = Cloudinary::option_consume($options, "version");
        $format = Cloudinary::option_consume($options, "format");

        $cloud_name = Cloudinary::option_consume($options, "cloud_name", Cloudinary::config_get("cloud_name"));
        if (!$cloud_name) throw new InvalidArgumentException("Must supply cloud_name in tag or in configuration");
        $secure = Cloudinary::option_consume($options, "secure", Cloudinary::config_get("secure"));
        $private_cdn = Cloudinary::option_consume($options, "private_cdn", Cloudinary::config_get("private_cdn"));
        $secure_distribution = Cloudinary::option_consume($options, "secure_distribution", Cloudinary::config_get("secure_distribution"));
        $cdn_subdomain = Cloudinary::option_consume($options, "cdn_subdomain", Cloudinary::config_get("cdn_subdomain"));
        $secure_cdn_subdomain = Cloudinary::option_consume($options, "secure_cdn_subdomain", Cloudinary::config_get("secure_cdn_subdomain"));
        $cname = Cloudinary::option_consume($options, "cname", Cloudinary::config_get("cname"));
        $shorten = Cloudinary::option_consume($options, "shorten", Cloudinary::config_get("shorten"));
        $sign_url = Cloudinary::option_consume($options, "sign_url", Cloudinary::config_get("sign_url"));
        $api_secret = Cloudinary::option_consume($options, "api_secret", Cloudinary::config_get("api_secret"));
        $url_suffix = Cloudinary::option_consume($options, "url_suffix", Cloudinary::config_get("url_suffix"));
        $use_root_path = Cloudinary::option_consume($options, "use_root_path", Cloudinary::config_get("use_root_path"));

        if (!$private_cdn and !empty($url_suffix)) {
            throw new InvalidArgumentException("URL Suffix only supported in private CDN");
        }

        if (!$source) return $source;

        if (preg_match("/^https?:\//i", $source)) {
          if ($type == "upload") return $source;
        }

        $resource_type_and_type = Cloudinary::finalize_resource_type($resource_type, $type, $url_suffix, $use_root_path, $shorten);
        $sources = Cloudinary::finalize_source($source, $format, $url_suffix);
        $source = $sources["source"];
        $source_to_sign = $sources["source_to_sign"];

        if (strpos($source_to_sign, "/") && !preg_match("/^https?:\//", $source_to_sign) && !preg_match("/^v[0-9]+/", $source_to_sign) && empty($version)) {
            $version = "1";
        }
        $version = $version ? "v" . $version : NULL;

        $signature = NULL;
        if ($sign_url) {
          $to_sign = implode("/", array_filter(array($transformation, $source_to_sign)));
          $signature = str_replace(array('+','/','='), array('-','_',''), base64_encode(sha1($to_sign . $api_secret, TRUE)));
          $signature = 's--' . substr($signature, 0, 8) . '--';
        }

        $prefix = Cloudinary::unsigned_download_url_prefix($source, $cloud_name, $private_cdn, $cdn_subdomain, $secure_cdn_subdomain,
          $cname, $secure, $secure_distribution);

        return preg_replace("/([^:])\/+/", "$1/", implode("/", array_filter(array($prefix, $resource_type_and_type,
          $signature, $transformation, $version, $source))));
    }

    private static function finalize_source($source, $format, $url_suffix) {
      $source = preg_replace('/([^:])\/\//', '$1/', $source);
      if (preg_match('/^https?:\//i', $source)) {
        $source = Cloudinary::smart_escape($source);
        $source_to_sign = $source;
      } else {
        $source = Cloudinary::smart_escape(rawurldecode($source));
        $source_to_sign = $source;
        if (!empty($url_suffix)) {
          if (preg_match('/[\.\/]/i', $url_suffix)) throw new InvalidArgumentException("url_suffix should not include . or /");
          $source = $source . '/' . $url_suffix;
        }
        if (!empty($format)) {
          $source = $source . '.' . $format ;
          $source_to_sign = $source_to_sign . '.' . $format ;
        }
      }
      return array("source" => $source, "source_to_sign" => $source_to_sign);
    }

    private static function finalize_resource_type($resource_type, $type, $url_suffix, $use_root_path, $shorten) {
      if (empty($type)) {
        $type = "upload";
      }

      if (!empty($url_suffix)) {
        if ($resource_type == "image" && $type == "upload") {
          $resource_type = "images";
          $type = NULL;
        } else if ($resource_type == "raw" && $type == "upload") {
          $resource_type = "files";
          $type = NULL;
        } else {
          throw new InvalidArgumentException("URL Suffix only supported for image/upload and raw/upload");
        }
      }

      if ($use_root_path) {
        if (($resource_type == "image" && $type == "upload") || ($resource_type == "images" && empty($type))) {
          $resource_type = NULL;
          $type = NULL;
        } else {
          throw new InvalidArgumentException("Root path only supported for image/upload");
        }
      }
      if ($shorten && $resource_type == "image" && $type == "upload") {
        $resource_type = "iu";
        $type = NULL;
      }
      $out = "";
      if (!empty($resource_type)) {
        $out = $resource_type;
      }
      if (!empty($type)) {
        $out = $out . '/' . $type;
      }
      return $out;
    }

    // cdn_subdomain and secure_cdn_subdomain
    // 1) Customers in shared distribution (e.g. res.cloudinary.com)
    //   if cdn_domain is true uses res-[1-5].cloudinary.com for both http and https. Setting secure_cdn_subdomain to false disables this for https.
    // 2) Customers with private cdn
    //   if cdn_domain is true uses cloudname-res-[1-5].cloudinary.com for http
    //   if secure_cdn_domain is true uses cloudname-res-[1-5].cloudinary.com for https (please contact support if you require this)
    // 3) Customers with cname
    //   if cdn_domain is true uses a[1-5].cname for http. For https, uses the same naming scheme as 1 for shared distribution and as 2 for private distribution.
    private static function unsigned_download_url_prefix($source, $cloud_name, $private_cdn, $cdn_subdomain, $secure_cdn_subdomain, $cname, $secure, $secure_distribution) {
      $shared_domain = !$private_cdn;
      $prefix = NULL;
      if ($secure) {
        if (empty($secure_distribution) || $secure_distribution == Cloudinary::OLD_AKAMAI_SHARED_CDN) {
          $secure_distribution = $private_cdn ? $cloud_name . '-res.cloudinary.com' : Cloudinary::SHARED_CDN;
        }

        if (empty($shared_domain)) {
          $shared_domain = ($secure_distribution == Cloudinary::SHARED_CDN);
        }

        if (empty($secure_cdn_subdomain) && $shared_domain) {
          $secure_cdn_subdomain = $cdn_subdomain ;
        }

        if ($secure_cdn_subdomain) {
          $secure_distribution = str_replace('res.cloudinary.com', "res-" . ((crc32($source) % 5) + 1) . "cloudinary.com", $secure_distribution);
        }

        $prefix = "https://" . $secure_distribution;
      } else if ($cname) {
        $subdomain = $cdn_subdomain ? "a" . ((crc32($source) % 5) + 1) . '.' : "";
        $prefix = "http://" . $subdomain . $cname;
      } else {
        $host = implode(array($private_cdn ? $cloud_name . "-" : "", "res", $cdn_subdomain ? "-" . ((crc32($source) % 5) + 1) : "", ".cloudinary.com"));
        $prefix = "http://" . $host;
      }
      if ($shared_domain) {
        $prefix = $prefix . '/' . $cloud_name;
      }
      return $prefix;
    }

    // [<resource_type>/][<image_type>/][v<version>/]<public_id>[.<format>][#<signature>]
    // Warning: $options are being destructively updated!
    public static function check_cloudinary_field($source, &$options=array()) {
        $IDENTIFIER_RE = "~" .
            "^(?:([^/]+)/)??(?:([^/]+)/)??(?:(?:v(\\d+)/)(?:([^#]+)/)?)?" .
            "([^#/]+?)(?:\\.([^.#/]+))?(?:#([^/]+))?$" .
            "~";
        $matches = array();
        if (!(is_object($source) && method_exists($source, 'identifier'))) {
            return $source;
        }
        $identifier = $source->identifier();
        if (!$identifier || strstr(':', $identifier) !== false || !preg_match($IDENTIFIER_RE, $identifier, $matches)) {
            return $source;
        }
        $optionNames = array('resource_type', 'type', 'version', 'folder', 'public_id', 'format');
        foreach ($optionNames as $index => $optionName) {
            if (@$matches[$index+1]) {
                $options[$optionName] = $matches[$index+1];
            }
        }
        return Cloudinary::option_consume($options, 'public_id');
    }

    // Based on http://stackoverflow.com/a/1734255/526985
    private static function smart_escape($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%3A'=>':', '%2F'=>'/');
        return strtr(rawurlencode($str), $revert);
    }

    public static function cloudinary_api_url($action = 'upload', $options = array()) {
        $cloudinary = Cloudinary::option_get($options, "upload_prefix", Cloudinary::config_get("upload_prefix", "https://api.cloudinary.com"));
        $cloud_name = Cloudinary::config_get("cloud_name");
        if (!$cloud_name) throw new InvalidArgumentException("Must supply cloud_name in tag or in configuration");
        $resource_type = Cloudinary::option_get($options, "resource_type", "image");
        return implode("/", array($cloudinary, "v1_1", $cloud_name, $resource_type, $action));
    }

    public static function random_public_id() {
        return substr(sha1(uniqid(Cloudinary::config_get("api_secret", "") . mt_rand())), 0, 16);
    }

    public static function signed_preloaded_image($result) {
        return $result["resource_type"] . "/upload/v" . $result["version"] . "/" . $result["public_id"] .
               (isset($result["format"]) ? "." . $result["format"] : "") . "#" . $result["signature"];
    }

    public static function zip_download_url($tag, $options=array()) {
        $params = array("timestamp"=>time(), "tag"=>$tag, "transformation" => \Cloudinary::generate_transformation_string($options));
        $params = Cloudinary::sign_request($params, $options);
        return Cloudinary::cloudinary_api_url("download_tag.zip", $options) . "?" . http_build_query($params);
    }

    public static function private_download_url($public_id, $format, $options = array()) {
        $cloudinary_params = Cloudinary::sign_request(array(
          "timestamp"=>time(),
          "public_id"=>$public_id,
          "format"=>$format,
          "type"=>Cloudinary::option_get($options, "type"),
          "attachment"=>Cloudinary::option_get($options, "attachment"),
          "expires_at"=>Cloudinary::option_get($options, "expires_at")
        ), $options);

        return Cloudinary::cloudinary_api_url("download", $options) . "?" . http_build_query($cloudinary_params);
    }

    public static function sign_request($params, &$options) {
        $api_key = Cloudinary::option_get($options, "api_key", Cloudinary::config_get("api_key"));
        if (!$api_key) throw new \InvalidArgumentException("Must supply api_key");
        $api_secret = Cloudinary::option_get($options, "api_secret", Cloudinary::config_get("api_secret"));
        if (!$api_secret) throw new \InvalidArgumentException("Must supply api_secret");

        # Remove blank parameters
        $params = array_filter($params, function($v){ return isset($v) && $v !== "";});

        $params["signature"] = Cloudinary::api_sign_request($params, $api_secret);
        $params["api_key"] = $api_key;

        return $params;
    }

    public static function api_sign_request($params_to_sign, $api_secret) {
        $params = array();
        foreach ($params_to_sign as $param => $value) {
            if (isset($value) && $value !== "") {
                $params[$param] = is_array($value) ? implode(",", $value) : $value;
            }
        }
        ksort($params);
	      $join_pair = function($key, $value) { return $key . "=" . $value; };
        $to_sign = implode("&", array_map($join_pair, array_keys($params), array_values($params)));
        return sha1($to_sign . $api_secret);
    }

    public static function html_attrs($options, $only = NULL) {
        $attrs = array();
        foreach($options as $k => $v) {
          $key = $k;
          $value = $v;
          if (is_int($k)) {
            $key = $v;
            $value = "";
          }
          if (is_array($only) && array_search($key, $only) !== FALSE || !is_array($only)) {
            $attrs[$key] = $value;
          }
        }
        ksort($attrs);

        $join_pair = function($key, $value) {
          $out = $key;
          if (!empty($value)) {
            $out .= '=\'' . $value . '\'';
          }
          return $out;
        };
        return implode(" ", array_map($join_pair, array_keys($attrs), array_values($attrs)));
    }
}

require_once(join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), 'Helpers.php')));
