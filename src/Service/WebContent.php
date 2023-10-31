<?php

/**
 * WebContent
 *
 * Service for fetching web content.
 * @todo: Create a database and administrative backend for CRUD operations instead
 *   of SFTP & editing this code.
 */

namespace App\Service;

class WebContent
{
    /**
     * Get OGP values for a given page
     */
    public function getOpenGraph($page = null)
    {
        switch ($page) {
            case 'about':
                $ogp = [
                    'title' => 'About: Andrew MacRobert Photography',
                    'images' => ['https://andrewmacrobert.com/images/profile2.jpg'],
                    'description' => 'Boston nightlife photographer',
                ];
                break;

            case 'bookings':
                $ogp = [
                    'title' => 'Bookings: Andrew MacRobert Photography',
                    'images' => ['https://andrewmacrobert.com/images/nightlife-events/sander-van-doorn-38.jpg'],
                    'description' => 'Boston nightlife photographer',
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
