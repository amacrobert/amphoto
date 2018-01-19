<?php
/**
 * WebContent
 *
 * Service for fetching web content.
 * @todo: Create a database and administrative backend for CRUD operations instead
 *   of SFTP & editing this code. 
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
                    'title' => 'About Andrew MacRobert Photography',
                    'image' => 'http://andrewmacrobert.com/images/profile2.jpg',
                ];
                break;

            case 'contact':
                $ogp = [
                    'title' => 'Contact Andrew MacRobert Photography',
                    'image' => 'http://andrewmacrobert.com/images/banner/panorama1.jpg'
                ];
                break;

            case 'equipment':
                $ogp = [
                    'title' => 'My Equipment - Andrew MacRobert Photography',
                ];
                break;

            case 'video':
                $ogp = [
                    'title' => 'Video - Andrew MacRobert Photography',
                ];
                break;

            default:
                $ogp = [
                ];
                break;
        }

        return (object)$ogp;
    }
}
