<?php
/**
 * WebContent
 *
 * Service for fetching web content.
 * @todo: Create a database and administrative backend for CRUD operations instead of SFTP & editing this code. 
 */

namespace AppBundle\Service;

class WebContent {

    /**
     * Featured photos to be shown on the home page, in order.
     */
    public function getFeaturedPhotos() {
        return [
            'festivals/0001-1.jpg',
            'tour-life/0029.jpg',
            'weddings/0015.jpg',
            'portraiture/0005.jpg',
            'nightlife/0006.jpg',
            'nightlife/0025.jpg',
            'day-parties/0023.jpg',
            'festivals/0033.jpg',
            'festivals/0034.jpg',
            'nightlife/0001.jpg',
            'nightlife/0012.jpg',
            'nightlife/0026.jpg',
            'nightlife/0028.jpg',
            'portraiture/0029.jpg',
            'portraiture/0032.jpg',
            'portraiture/0033.jpg',
        ];
    }

    /**
     * Get OGP values for a given page
     */
    public function getOpenGraph($page = null) {
        switch ($page) {

            case 'about':
                $ogp = [
                    'title' => 'About',
                    'image' => 'http://andrewmacrobert.com/images/profile.jpg',
                ];
                break;

            default:
                $ogp = [
                    'image' => 'http://andrewmacrobert.com/images/festivals/0001-1.jpg',
                ];
                break;
        }

        return (object)$ogp;
    }
}
