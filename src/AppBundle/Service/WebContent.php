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

            case 'equipment':
                $ogp = [
                    'title' => 'My Equipment',
                ];
                break;

            case 'video':
                $ogp = [
                    'title' => 'Video',
                    'image' => 'http://andrewmacrobert.com/images/nightlife/royale-go-001.jpg',
                ];

            default:
                $ogp = [
                    'image' => 'http://andrewmacrobert.com/images/nightlife/royale-go-001.jpg',
                ];
                break;
        }

        return (object)$ogp;
    }
}
