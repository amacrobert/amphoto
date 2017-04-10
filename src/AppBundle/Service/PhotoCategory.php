<?php
/**
 * PhotoCategory
 *
 * Service for getting and organizing category photo content
 */

namespace AppBundle\Service;

class PhotoCategory {

    // List allowed file extensions here
    public static $allowed_extensions = [
        'jpg',
        'jpeg',
    ];

    /**
     * Sort and return the photos in a category
     */
    public function getPhotos($category) {

        $photos = [];
        $directory = 'images/' . $category;

        // If there's a .BridgeSort file in the directory, use it's file arrangement
        $bridge_sort_uri = $directory . '/.BridgeSort';
        if (file_exists($bridge_sort_uri)) {
            $bridge_sort = simplexml_load_file($bridge_sort_uri);
            $sort_order = $bridge_sort->xpath('//@key');

            foreach ($sort_order as $sxml_key) {
                $key = (string)$sxml_key;
                $file = substr($key, 0, strpos($key, ".jpg")) . '.jpg';

                $uri = $directory . '/' . $file;

                if (file_exists($uri)) {
                    $photos[] = (object)array_merge(['uri' => $uri], $this->getDimensions($uri));
                }
            }
        }

        // No BridgeSort -- scandir and sort by filename
        else {
            $files = scandir($directory, SCANDIR_SORT_DESCENDING);

            foreach ($files as $file) {
                // Only accept certain file types and ignore directories
                $pathinfo = pathinfo($file);

                if (in_array(strtolower($pathinfo['extension']), self::$allowed_extensions)) {
                    $uri = $directory . '/' . $file;
                    $photos[] = (object)array_merge(['uri' => $uri], $this->getDimensions($uri));
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
    private function getDimensions($uri) {
        // Determine if the image is landscape, portrait, or square for proper Freewall rendering
        $dimensions = getimagesize($uri);
        return [
            'width' => $dimensions[0],
            'height' => $dimensions[1]
        ];
    }
}
