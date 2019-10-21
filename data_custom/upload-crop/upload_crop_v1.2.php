<?php

/**
 * @package    thumbnail_editor
 */

/*
* http://www.webmotionuk.com/php-jquery-image-upload-and-crop/
* Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
* "PHP & Jquery image upload & crop"
* Date: 2008-11-21
* Ver 1.2
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
* IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF
* THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*/

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$deep = 'data_custom/upload-crop/';
$FILE_BASE = str_replace($deep, '', $FILE_BASE);
$FILE_BASE = str_replace(str_replace('/', '\\', $deep), '', $FILE_BASE);
if (substr($FILE_BASE, -4) == '.php') {
    $a = strrpos($FILE_BASE, '/');
    $b = strrpos($FILE_BASE, '\\');
    $FILE_BASE = dirname($FILE_BASE);
}
$RELATIVE_PATH = '';
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<html><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('thumbnail_editor')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('thumbnail_editor')));
}

if (!function_exists('imagepng')) {
    warn_exit(do_lang_tempcode('GD_NEEDED'));
}

require_code('developer_tools');
destrictify();

css_enforce('global');

if (!has_zone_access(get_member(), 'adminzone')) {
    exit('Security error (did you get logged out?)');
}

require_code('images');

$max_width = 800;
$large_image_location = $_GET['file'];
$thumb_image_location = $_GET['thumb'];
if (preg_match('#^uploads/(attachments|attachments_thumbs|filedump)/#', $large_image_location) == 0) {
    exit('Security error on' . $large_image_location);
}
if (strpos($large_image_location, '..') !== false) {
    exit('Security error on' . $large_image_location);
}
if (preg_match('#^uploads/(attachments|attachments_thumbs|filedump)/#', $thumb_image_location) == 0) {
    exit('Security error on' . $thumb_image_location);
}
if (strpos($thumb_image_location, '..') !== false) {
    exit('Security error on' . $thumb_image_location);
}
$thumb_width = intval(isset($_POST['thumb_width']) ? $_POST['thumb_width'] : $_GET['thumb_width']);                        // Width of thumbnail image
$thumb_height = intval(isset($_POST['thumb_height']) ? $_POST['thumb_height'] : $_GET['thumb_height']);                        // Height of thumbnail image
// Only one of these image types should be allowed for upload
$allowed_image_types = array('image/pjpeg' => "jpg", 'image/jpeg' => "jpg", 'image/jpg' => "jpg", 'image/png' => "png", 'image/x-png' => "png", 'image/gif' => "gif");
$allowed_image_ext = array_unique($allowed_image_types); // do not change this
$image_ext = "";    // initialise variable, do not change this.
foreach ($allowed_image_ext as $mime_type => $ext) {
    $image_ext .= strtoupper($ext) . " ";
}


#########################################################################################################
# IMAGE FUNCTIONS                                                                                       #
# You do not need to alter these functions                                                              #
#########################################################################################################
function resizeImage($image, $width, $height, $scale)
{
    list($imagewidth, $imageheight) = cms_getimagesize($image);
    $newImageWidth = ceil($width * $scale);
    $newImageHeight = ceil($height * $scale);
    $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);

    $source = cms_imagecreatefrom($image);

    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);

    cms_imagesave($newImage, $image) or intelligent_write_error($image);

    return $image;
}

//You do not need to alter these functions
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
{
    $newImageWidth = ceil($width * $scale);
    $newImageHeight = ceil($height * $scale);

    $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);

    $source = cms_imagecreatefrom($image);

    imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);

    cms_imagesave($newImage, $image) or intelligent_write_error($image);

    return $thumb_image_name;
}

//You do not need to alter these functions
function getHeight($image)
{
    $size = cms_getimagesize($image);
    $height = $size[1];
    return $height;
}

//You do not need to alter these functions
function getWidth($image)
{
    $size = cms_getimagesize($image);
    $width = $size[0];
    return $width;
}

//Check to see if any images with the same name already exist
if (file_exists($large_image_location)) {
    if (file_exists($thumb_image_location)) {
        $thumb_photo_exists = "<img src=\"../../" . $thumb_image_location . "\" alt=\"Thumbnail Image\"/>";
    } else {
        $thumb_photo_exists = "";
    }
    $large_photo_exists = "<img src=\"../../" . $large_image_location . "\" alt=\"Large Image\"/>";
} else {
    $large_photo_exists = "";
    $thumb_photo_exists = "";
}

// Check our large file is not too large
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width) {
    $scale = $max_width / $width;
    $uploaded = resizeImage($large_image_location, $width, $height, $scale);
} else {
    $scale = 1;
    $uploaded = resizeImage($large_image_location, $width, $height, $scale);
}

$message = '';

if (isset($_POST["upload_thumbnail"]) && strlen($large_photo_exists) > 0) {
    // Get the new coordinates to crop the image.
    $x1 = $_POST["x1"];
    $y1 = $_POST["y1"];
    $x2 = $_POST["x2"];
    $y2 = $_POST["y2"];
    $w = $_POST["w"];
    $h = $_POST["h"];
    // Scale the image to the thumb_width set above
    $scale = $thumb_width / $w;
    $cropped = resizeThumbnailImage($thumb_image_location, $large_image_location, $w, $h, $x1, $y1, $scale);

    $message = 'Saved!';
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <meta name="generator" content="WebMotionUK"/>
    <title>Thumbnail generator</title>
    <link href="../../themes/default/templates_cached/EN/global.css" rel="stylesheet"/>
    <script src="js/jquery-pack.js"></script>
    <script src="js/jquery.imgareaselect.min.js"></script>
</head>
<body>
<!--
* Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
* Date: 2008-11-21
* "PHP & Jquery image upload & crop"
* Ver 1.2
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
* IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF
* THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
-->
<?php
//Only display the javacript if an image has been uploaded
if (strlen($large_photo_exists) > 0) {
    $current_large_image_width = getWidth($large_image_location);
    $current_large_image_height = getHeight($large_image_location); ?>
    <script>
        function preview(img, selection) {
            var scaleX =
            <?= $thumb_width ?> /
            selection.width;
            var scaleY =
            <?= $thumb_height ?> /
            selection.height;

            $('#thumbnail + div > img').css({
                width: Math.round(scaleX * <?= $current_large_image_width ?>) + 'px',
                height: Math.round(scaleY * <?= $current_large_image_height ?>) + 'px',
                marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
                marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
            });
            $('#x1').val(selection.x1);
            $('#y1').val(selection.y1);
            $('#x2').val(selection.x2);
            $('#y2').val(selection.y2);
            $('#w').val(selection.width);
            $('#h').val(selection.height);
        }

        $(document).ready(function () {
            $('#save_thumb').click(function () {
                var x1 = $('#x1').val();
                var y1 = $('#y1').val();
                var x2 = $('#x2').val();
                var y2 = $('#y2').val();
                var w = $('#w').val();
                var h = $('#h').val();
                if (x1 == "" || y1 == "" || x2 == "" || y2 == "" || w == "" || h == "") {
                    alert("You must make a selection first");
                    return false;
                } else {
                    return true;
                }
            });
        });

        $(window).load(function () {
            $('#thumbnail').imgAreaSelect({
                aspectRatio: '1:<?= $thumb_height/$thumb_width ?>',
                onSelectChange: preview
            });
            <?php
                if (isset($_POST['w']))
                    {echo 'preview($(\'#thumbnail\'), { width:'.$_POST['w'].', height:'.$_POST['h'].', x1:'.$_POST['x1'].', x2:'.$_POST['x2'].', y1:'.$_POST['y1'].', y2:'.$_POST['y2'].' });';}
            ?>
        });

    </script>
<?php } ?>
<h1>Thumbnail generator</h1>
<?php
/*if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){
    echo $large_photo_exists."&nbsp;".$thumb_photo_exists;
}else*/
{
    if (strlen($large_photo_exists) > 0) {
        ?>
        <?php
        echo '<span id="message" style="float: right">' . $message . '</span>';
        ?>
        <h2>Create Thumbnail</h2>
        <div align="center">
            <img src="../../<?= $large_image_location ?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail"/>

            <div style="border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?= $thumb_width ?>px; height:<?= $thumb_height ?>px;">
                <img src="../../<?= $large_image_location ?>" style="position: relative;" alt="Thumbnail Preview"/>
            </div>
            <br style="clear:both;"/>

            <form style="float: left; width: 400px" onsubmit="document.getElementById('message').innerHTML='Changing size';" action="<?= htmlentities($_SERVER["SCRIPT_NAME"] . '?' . $_SERVER['QUERY_STRING']) ?>" method="post">
                <p>
                    <input size="5" type="text" name="thumb_width" value="<?= $thumb_width ?>" id="thumb_width"/>
                    &times;
                    <input size="5" type="text" name="thumb_height" value="<?= $thumb_height ?>" id="thumb_height"/>
                    <button type="submit" name="change_size" id="change_size">Change thumbnail size</button>
                </p>
            </form>
            <form style="margin-right: 400px" onsubmit="document.getElementById('message').innerHTML='...';" action="<?= htmlentities($_SERVER["SCRIPT_NAME"] . '?' . $_SERVER['QUERY_STRING']) ?>" method="post">
                <input type="hidden" name="thumb_width" value="<?= $thumb_width ?>" id="thumb_width"/>
                <input type="hidden" name="thumb_height" value="<?= $thumb_height ?>" id="thumb_height"/>
                <input type="hidden" name="x1" value="" id="x1"/>
                <input type="hidden" name="y1" value="" id="y1"/>
                <input type="hidden" name="x2" value="" id="x2"/>
                <input type="hidden" name="y2" value="" id="y2"/>
                <input type="hidden" name="w" value="" id="w"/>
                <input type="hidden" name="h" value="" id="h"/>

                <p><button type="submit" name="upload_thumbnail" id="save_thumb">Save Thumbnail</button></p>
            </form>
        </div>
    <?php } ?>
<?php } ?>
<!-- Copyright (c) 2008 http://www.webmotionuk.com -->
</body>
</html>
