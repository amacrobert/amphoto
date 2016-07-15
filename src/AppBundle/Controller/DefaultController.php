<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {

        $content = $this->get('web_content');

        return $this->render('default/index.html.twig', [
            'bodyclass' => 'slideshow',
            'slideshow' => $content->getFeaturedPhotos(),
            'og' => $content->getOpenGraph(),
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction() {
        return $this->render('default/about.html.twig', [
            'og' => $this->get('web_content')->getOpenGraph('about'),
        ]);
    }

    /**
     * @Route("/photos/{category}", name="category")
     */
    public function categoryAction($category) {

        // List allowed file extensions here
        $allowed_extensions = [
            'jpg',
            'jpeg',
        ];

        $directory = 'images/' . $category;

        // If the category doesn't exist, redirect home
        if (!file_exists($directory)) {
            return $this->redirectToRoute('homepage');
        }

        // Scan the contents of the category's directory for photos to display
        $files = scandir($directory, SCANDIR_SORT_DESCENDING);
        $photos = [];

        foreach ($files as $file) {
            // Only accept certain file types and ignore directories
            $pathinfo = pathinfo($file);

            if (in_array(strtolower($pathinfo['extension']), $allowed_extensions)) {

                $uri = 'images/' . $category . '/' . $file;
                
                // Determine if the image is landscape, portrait, or square for proper Freewall rendering
                $dimensions = getimagesize($uri);
                if ($dimensions[0] > $dimensions[1]) {
                    $orientation = 'landscape';
                }
                elseif ($dimensions[0] < $dimensions[1]) {
                    $orientation = 'portrait';
                }
                else {
                    $orientation = 'square';
                }

                $photos[] = (object)[
                    'uri' => $uri,
                    'orientation' => $orientation,
                ];
            }
        }

        switch ($category) {
            case 'nightlife':
                $title = 'Nightlife';
                $description = '';
                break;

            case 'day-parties':
                $title = 'Day Parties';
                $description = '';
                break;

            case 'festivals':
                $title = 'Festivals';
                $description = '';
                break;

            case 'portraiture':
                $title = 'Portraiture';
                $description = 'Fashion shoots, publicity shoots, editorials, and just-for-fun shoots of human beings.';
                break;

            case 'tour-life':
                $title = 'Tour Life';
                $description = "Being on tour is living in an alternate reality, and it's not all about the performance each night. A big part of tour photography is capturing candid moments of artists in various cities, exposing their personality to fans on social media in ways they don't see on stage.";
                break;

            case 'weddings':
                $title = 'Weddings';
                $description = 'Wedding ceremonies, receptions, and engagement shoots.';
                break;

            default:
                $title = 'Huh?';
                $description = 'How did you get here?';
                break;
        }

        return $this->render('default/category.html.twig', [
            'title' => $title,
            'description' => $description,
            'photos' => $photos,
            'og' => [
                'title' => $title,
                'image' => 'http://andrewmacrobert.com/' . $photos[0]->uri,
            ],
        ]);
    }
}
