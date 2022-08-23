<?php
/**
 * PhotoCategory
 *
 * Service for getting and organizing category photo content
 */

namespace App\Service;

class PhotoCategory
{
    public function __construct(private string $project_dir)
    {}

    // List allowed file extensions here
    public static $allowed_extensions = [
        'jpg',
        'jpeg',
    ];

    /**
     * Sort and return the photos in a category
     */
    public function getPhotos($category)
    {
        $photos = [];
        $directory = 'images/' . $category;
        $directory_thumbnails = 'thumbs/' . $category;
        $directory_absolute =  $this->project_dir . '/public/' . $directory;

        if (!file_exists($directory_absolute)) {
            throw new \Exception(sprintf('Photo directory "%s" does not exist', $directory));
        }

        // If there's a .BridgeSort file in the directory, use its file arrangement
        $bridge_sort_uri = $directory_absolute . '/.BridgeSort';
        if (file_exists($bridge_sort_uri)) {
            $bridge_sort = simplexml_load_file($bridge_sort_uri);
            $sort_order = $bridge_sort->xpath('//@key');

            foreach ($sort_order as $sxml_key) {
                $key = (string)$sxml_key;
                $file = substr($key, 0, strpos($key, ".jpg")) . '.jpg';

                $uri = $directory . '/' . $file;
                $thumb_uri = $directory_thumbnails . '/' . $file;
                $filepath = $directory_absolute . '/' . $file;

                if (file_exists($filepath)) {
                    $photos[] = (object)array_merge([
                        'uri' => $uri,
                        'thumbnail_uri' => $thumb_uri,
                        'caption' => $this->getImageCaption($filepath),
                    ], $this->getDimensions($filepath));
                }
            }
        }

        // No BridgeSort -- scandir and sort by filename
        else {
            $files = scandir($directory, SCANDIR_SORT_DESCENDING);

            foreach ($files as $file) {
                // Only accept certain file types and ignore directories
                $pathinfo = pathinfo($file);
                $filepath = $directory_absolute . '/' . $file;

                if (in_array(strtolower($pathinfo['extension']), self::$allowed_extensions)) {
                    $uri = $directory . '/' . $file;
                    $photos[] = (object)array_merge([
                        'uri' => $uri,
                        'caption' => $this->getImageCaption($filepath),
                    ], $this->getDimensions($filepath));
                }
            }
        }

        return $photos;
    }

    /**
     * Helper function to get the orientation of a photo by its uri
     *
     * @param $uri
     * @return string
     *   landscape, orientation, or square
     */
    private function getDimensions($filepath) {
        // Determine if the image is landscape, portrait, or square for proper Freewall rendering
        $dimensions = getimagesize($filepath);
        return [
            'width' => $dimensions[0],
            'height' => $dimensions[1]
        ];
    }

    private function getImageCaption($filepath) {
        $caption = '';
        if ($exif = exif_read_data($filepath, 'IFD0', true)) {
            if (isset($exif['IFD0']) && isset($exif['IFD0']['ImageDescription'])) {
                $caption = $exif['IFD0']['ImageDescription'];
            }
        }
        return $caption;
    }
}
