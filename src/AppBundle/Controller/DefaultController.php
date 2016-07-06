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
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'bodyclasses' => 'slideshow',
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction() {
        return $this->render('default/about.html.twig');
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
                $description = 'Deliberate pictures of people. This encompasses fashion shoots, publicity shoots, and just-for-fun shoots with human beings.';
                break;

            default:
                $title = '?';
                $description = '';
                break;
        }

        return $this->render('default/category.html.twig', [
            'title' => $title,
            'description' => $description,
            'photos' => $photos,
        ]);
    }
}
