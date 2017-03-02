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

    /**
     * Get endorsements, optionally by category
     */
    public function getEndorsements($category = null) {
        $endorsements = [
            [
                'quote' => 'Andrew MacRobert is in my opinion the best nightlife photographer in Boston.',
                'byline' => 'Matthew Keene, manager of Keene Marcil Group',
                'category' => 'nightlife-events',
            ],
            [
                'quote' => 'I want to make the night a huge success, which means I need the best photog guy in the business.',
                'byline' => 'Theodore Green of Green T Enterprises, booking Andrew at Royale Boston',
                'category' => 'nightlife-events',
            ],
            [
                'quote' => 'Boston will be too small for Andrew. I proclaim it.',
                'byline' => 'Cam Preciado, Creative Director at Christian Hill Studios',
                'category' => 'festivals',
            ],
        ];

        if ($category) {
            return array_filter($endorsements, function($endorsement) use ($category) {
                return $category == $endorsement['category'];
            });
        }

        return $endorsements;
    }
}
