<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    transcoder
 */

/*EXTRA FUNCTIONS: shell_exec|gethostname|escapeshellarg|escapeshellcmd|transcode|tr_get_mime_type*/

if (function_exists('set_time_limit')) {
    @set_time_limit(0);
}
ini_set('allow_url_fopen', '1');
ini_set('display_errors', '1');
error_reporting(E_ALL);

// This may want hard-coding if it does not detect correctly
$transcoder_server = 'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (function_exists('gethostname') ? gethostname() : 'localhost')) . str_replace(DIRECTORY_SEPARATOR, '/', dirname($_SERVER['SCRIPT_NAME']));
$liveserver = str_replace('/transcoder', '', $transcoder_server);

//mencoder path
define('MENCODER_PATH', '/usr/local/bin/');

//video width to be set
define('VIDEO_WIDTH_SETTING', 720);

//video height to be set
define('VIDEO_HEIGHT_SETTING', 480);

//audio bitrate to be set
define('AUDIO_BITRATE', 192);

//video bitrate to be set
define('VIDEO_BITRATE', 1500);

/**
 * Find the mime type for the given file extension. It does not take into account whether the file type has been white-listed or not, and returns a binary download mime type for any unknown extensions.
 *
 * @param  string $extension The file extension (no dot)
 * @return string The MIME type
 */
function tr_get_mime_type($extension)
{
    $mime_types = array(

        // Movies
        'avi' => 'video/mpeg',//'video/x-ms-asf' works with the plugin on Windows Firefox but nothing else,//'video/x-msvideo' is correct but does not get recognised by Microsoft Firefox WMV plugin and confuses RealMedia Player if it sees data transferred under that mime type,
        'mpg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'm4v' => 'video/mpeg',
        'mp4' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'flv' => 'video/x-flv',

        // Proprietary movie formats
        'mov' => 'video/quicktime',
        'qt' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'asf' => 'video/x-ms-asf',

        // Audio
        'ra' => 'audio/x-pn-realaudio-plugin',
        'wma' => 'audio/x-ms-wma',
        'wav' => 'audio/x-wav',
        'm4a' => 'audio/x-mpeg',
        'mp3' => 'audio/mpeg',
        'ogg' => 'application/ogg',
        'mid' => 'audio/midi',
    );

    if (array_key_exists($extension, $mime_types)) {
        return $mime_types[$extension];
    }

    return 'application/octet-stream';
}

/**
 * Transcode a video.
 *
 * @param  PATH $path Video to transcoded
 * @return PATH Transcoded file (or original URL if no change was made)
 */
function transcode($path)
{
    //if there is a locally uploaded file, that is not in flv/m4v/mp3 format go transcode it
    if ((preg_match('#http\:\/\/#i', $path) == 0) && (preg_match('#\.(m4v|mp4|flv|mp3)$#i', $path) == 0)) {
        //mencoder path
        $mencoder_path = MENCODER_PATH;//'C:/Program Files/MPlayer/';

        //video width to be set
        $video_width_setting = VIDEO_WIDTH_SETTING;//320;

        //video height to be set
        $video_height_setting = VIDEO_HEIGHT_SETTING;//240;

        //audio bitrate to be set
        $audio_bitrate = AUDIO_BITRATE;//56;

        //video bitrate to be set
        $video_bitrate = VIDEO_BITRATE;//250;

        $file_path = preg_replace('#\\\#', '/', $path);

        $matches = array();
        preg_match('/[^?]*/', $file_path, $matches);
        $string = $matches[0];

        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);

        $file_ext = '';
        if (count($pattern) > 1) {
            $filenamepart = $pattern[count($pattern) - 1][0];
            preg_match('/[^?]*/', $filenamepart, $matches);
            $file_ext = $matches[0];
        }

        // get mime type
        $input_mime_type = tr_get_mime_type(strtolower($file_ext));
        $is_video = preg_match('#video\/#i', $input_mime_type) != 0;
        $is_audio = preg_match('#audio\/#i', $input_mime_type) != 0;
        if ((!$is_audio) && (!$is_video)) {
            if (filesize($path) > 1024 * 1024 * 30) {
                $is_video = true;
            } else {
                $is_audio = true;
            }
        }

        if ($is_video) {
            $file_type = 'm4v';
            $path = preg_replace('#' . preg_quote($file_ext, '#') . '$#', '', $path) . $file_type;
            $path = str_replace('/queue/', '/done/', $path);
        } elseif ($is_audio) {
            $path = preg_replace('#' . preg_quote($file_ext, '#') . '$#', '', $path) . 'mp3';
            $path = str_replace('/queue/', '/done/', $path);
        }

        if ($is_video) {
            $output_path = preg_replace('#' . preg_quote($file_ext, '#') . '$#', '', $file_path) . $file_type;
            $output_path = str_replace('/queue/', '/done/', $output_path);

            /* mencoder too buggy
            if ($file_type == 'm4v') {
                $shell_command = '"' . $mencoder_path . 'mencoder" ' . escapeshellarg($file_path) . ' -noskip -of lavf -ofps 24000/1001 -ni -o ' . escapeshellarg($output_path) . ' -ovc x264 -oac mp3lame -x264encopts bitrate=' . escapeshellcmd($video_bitrate) . ' -lameopts abr:br=' . escapeshellcmd($audio_bitrate) . ' -vf scale=' . escapeshellcmd($video_width_setting . ':' . $video_height_setting) . ' -srate 22050 -af lavcresample=22050';
                echo '[' . date('d/m/Y h:i:s') . '] DOING SHELL COMMAND: ' . $shell_command . "\n";
                shell_exec($shell_command . ' 2>&1 >> log.txt');
            } else // flv
            {
                $shell_command = '"' . $mencoder_path . 'mencoder" ' . escapeshellarg($file_path) . ' -noskip -of lavf -ofps 24000/1001 -ni -o ' . escapeshellarg($output_path) . ' -ovc lavc -oac mp3lame -lavcopts vcodec=flv:vbitrate=' . escapeshellcmd($video_bitrate) . ':autoaspect:acodec=libmp3lame -lameopts abr:br=' . escapeshellcmd($audio_bitrate) . ' -vf scale=' . escapeshellcmd($video_width_setting . ':' . $video_height_setting) . ' -srate 22050 -af lavcresample=22050';
                echo '[' . date('d/m/Y h:i:s') . '] DOING SHELL COMMAND: ' . $shell_command . "\n";
                shell_exec($shell_command . ' 2>&1 >> log.txt');
            }
            */

            if ($file_type == 'm4v') {
                $shell_command = '"' . $mencoder_path . 'ffmpeg" -i ' . escapeshellarg($file_path) . ' -y -f mp4 -vcodec libx264 -b ' . escapeshellcmd($video_bitrate) . 'K -ab ' . escapeshellcmd($audio_bitrate) . 'K -r ntsc-film -g 240 -qmin 2 -qmax 15 -vpre libx264-default -acodec aac -strict experimental -ar 22050 -ac 2 -aspect 16:9 -s ' . escapeshellcmd($video_width_setting . ':' . $video_height_setting) . ' ' . escapeshellarg($output_path);
                foreach (array($shell_command . ' -map 0.1:0.0 -map 0.0:0.1', $shell_command . ' -map 0.0:0.0 -map 0.1:0.1') as $shell_command) {
                    echo '[' . date('d/m/Y h:i:s') . '] DOING SHELL COMMAND: ' . $shell_command . "\n";
                    shell_exec($shell_command . ' 2>&1 >> log.txt');
                    if (@filesize($output_path)) {
                        break;
                    }
                }
                shell_exec('"' . $mencoder_path . 'MP4Box" -inter 500 ' . ' ' . escapeshellarg($output_path) . ' 2>&1 >> log.txt');
            } else { // flv
                $shell_command = '"' . $mencoder_path . 'ffmpeg" -i ' . escapeshellarg($file_path) . ' -y -f flv -vcodec flv -b ' . escapeshellcmd($video_bitrate) . 'K -ab ' . escapeshellcmd($audio_bitrate) . 'K -r ntsc-film -g 240 -qmin 2 -qmax 15 -acodec libmp3lame -ar 22050 -ac 2 -aspect 16:9 -s ' . escapeshellcmd($video_width_setting . ':' . $video_height_setting) . ' ' . escapeshellarg($output_path);
                foreach (array($shell_command . ' -map 0.1:0.0 -map 0.0:0.1', $shell_command . ' -map 0.0:0.0 -map 0.1:0.1') as $shell_command) {
                    echo '[' . date('d/m/Y h:i:s') . '] DOING SHELL COMMAND: ' . $shell_command . "\n";
                    shell_exec($shell_command . ' 2>&1 >> log.txt');
                    if (@filesize($output_path)) {
                        break;
                    }
                }
            }
        } elseif ($is_audio) {
            $output_path = preg_replace('#' . preg_quote($file_ext, '#') . '$#', '', $file_path) . 'mp3';
            $output_path = str_replace('/queue/', '/done/', $output_path);

            //it is audio
            $shell_command = '"' . $mencoder_path . 'ffmpeg" -y -i ' . escapeshellarg($file_path) . ' -ab ' . escapeshellcmd($audio_bitrate) . 'K ' . escapeshellarg($output_path);

            echo '[' . date('d/m/Y h:i:s') . '] DOING SHELL COMMAND: ' . $shell_command . "\n";
            shell_exec($shell_command . ' 2>&1 >> log.txt');
        } else {
            return $path;
        }
    }

    return $path;
}

while (true) {
    $queue_dir = preg_replace('#\\\#', '/', getcwd()) . '/queue';
    $fail_dir = preg_replace('#\\\#', '/', getcwd()) . '/fail';
    $dh = opendir($queue_dir);

    while (($file = readdir($dh)) !== false) {
        if (($file != '.') && ($file != '..') && (is_file($queue_dir . '/' . $file))) {
            echo '[' . date('d/m/Y h:i:s') . '] Doing: "' . $file . '"' . "\n";

            $done_path = transcode($queue_dir . '/' . $file); // Hopefully this will only create the actual file once it is fully done, if not we might need to have a temp directory also

            clearstatcache();
            if ((file_exists($done_path)) && (filesize($done_path) != 0)) {
                echo '[' . date('d/m/Y h:i:s') . '] Done: "' . $done_path . '"' . "\n";

                unlink($queue_dir . '/' . $file);

                if (is_string(strstr($done_path, '/done/'))) {
                    $relative_url = 'done/' . rawurlencode(str_replace(dirname($done_path) . '/', '', $done_path));
                    $call = $liveserver . '/data_custom/receive_transcoded_file.php?url=' . urlencode($transcoder_server . $relative_url);
                    echo '[' . date('d/m/Y h:i:s') . '] Calling: ' . $call . "\n";
                    file_get_contents($call);
                }
            } else {
                echo '[' . date('d/m/Y h:i:s') . '] Could not find output file (' . $done_path . ')' . "\n";

                @unlink($queue_dir . '/' . $file);
                rename($queue_dir . '/' . $file, $fail_dir . '/' . $file);
            }
        }
    }

    closedir($dh);

    if (isset($_SERVER['argv']['once'])) {
        break;
    }

    usleep(10000000);
}
