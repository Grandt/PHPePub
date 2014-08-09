<?php
namespace PHPePub\External;

/**
 * ePub GIFTool
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2009-2014 A. Grandt
 * @license   GNU LGPL, Attribution required for commercial implementations, requested for everything else.
 *
 * GIFDecoder and GIFEncoder by László Zsidi, can be found here:
 * GIFDecoder: http://www.phpclasses.org/package/3234
 * GIFEncoder: http://www.phpclasses.org/package/3163
 *
 * @version   3.30
 */
class GIFTool {
    const _VERSION = 3.30;

    /**
     * resize AnimatedGIF
     *
     * @param string $imageData
     * @param int    $width
     * @param int    $height
     * @param float  $ratio
     *
     * @return string the gif image
     */
    static function resizeAnimatedGif($imageData, $width, $height, $ratio) {
        if (GIFTool::isAnimatedGif($imageData)) {
            // $gifDecoder = new GIFDecoder(fread(fopen('data://text/plain,' . $imageData,'rb'), strlen($imageData)));
            $gifDecoder = new GIFDecoder($imageData);
            //$gifDisposal = $gifDecoder->GIFGetDisposal();

            $newFrames = array();
            foreach ($gifDecoder->GIFGetFrames() as $frame) {
                $newFrames[] = GIFTool::resizeGif($frame, $width, $height, $ratio);
            }

            $anim = new GIFEncoder($newFrames,
                    $gifDecoder->GIFGetDelays(),
                    $gifDecoder->GIFGetLoop(),
                    2, // (is_array($gifDisposal) && sizeof($gifDisposal) > 0 ? $gifDisposal[0] : 2),
                    0, // $gifDecoder->GIFGetTransparentR(),
                    0, // $gifDecoder->GIFGetTransparentG(),
                    0, // $gifDecoder->GIFGetTransparentB(),
                    "bin");

            return $anim->GetAnimation();
        }
        return GIFTool::resizeGif($imageData, $width, $height, $ratio);
    }

    static function resizeGif($imageData, $width, $height, $ratio) {
        $image_o = imagecreatefromstring($imageData);
        $image_p = imagecreatetruecolor($width * $ratio, $height * $ratio);

        imagealphablending($image_p, false);
        imagesavealpha($image_p, true);
        imagealphablending($image_o, true);

        imagecopyresampled($image_p, $image_o, 0, 0, 0, 0, ($width * $ratio), ($height * $ratio), $width, $height);
        ob_start();
        imagegif($image_p, null, 9);
        $imageData = ob_get_contents();
        ob_end_clean();

        imagedestroy($image_o);
        imagedestroy($image_p);

        return $imageData;
    }

    /**
     * By  "ZeBadger"
     * http://it.php.net/manual/en/function.imagecreatefromgif.php#59787
     *
     * @param string $image image data
     *
     * @return bool
     */
    static function isAnimatedGif($image) {
        $str_loc = 0;
        $count   = 0;
        while ($count < 2) { // There is no point in continuing after we find a 2nd frame

            $where1 = strpos($image, "\x00\x21\xF9\x04", $str_loc);
            if ($where1 === false) {
                break;
            } else {
                $str_loc = $where1 + 1;
                $where2  = strpos($image, "\x00\x2C", $str_loc);

                if ($where2 === false) {
                    break;
                } else {
                    if ($where1 + 8 == $where2) {
                        $count++;
                    }
                    $str_loc = $where2 + 1;
                }
            }
        }

        return $count > 1;
    }
}
