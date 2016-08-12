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
        $category = $this->get('photo_category');

        return $this->render('default/index.html.twig', [
            'bodyclass' => 'slideshow',
            'slideshow' => $category->getPhotos('featured'),
            'og' => $content->getOpenGraph(),
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction() {
        return $this->render('default/about.html.twig', [
            'og' => $this->get('web_content')->getOpenGraph('about'),
            'aboutNavActive' => true,
        ]);
    }

    /**
     * @Route("/photos/{category}", name="category")
     */
    public function categoryAction($category) {

        $directory = 'images/' . $category;

        // If the category doesn't exist, redirect home
        if (!file_exists($directory)) {
            return $this->redirectToRoute('homepage');
        }

        $photos = $this->get('photo_category')->getPhotos($category);

        switch ($category) {
            case 'nightlife':
                $title = 'Nightlife';
                $description = '';
                $active_nav = 'nightlifeNavActive';
                break;

            case 'day-parties':
                $title = 'Day Parties';
                $description = '';
                $active_nav = 'dayPartiesNavActive';
                break;

            case 'festivals':
                $title = 'Festivals';
                $description = '';
                $active_nav = 'festivalsNavActive';
                break;

            case 'portraiture':
                $title = 'Portraiture';
                $description = 'Fashion shoots, publicity shoots, editorials, and just-for-fun shoots of human beings.';
                $active_nav = 'portraitureNavActive';
                break;

            case 'tour-life':
                $title = 'Tour Life';
                $description = "Being on tour is living in an alternate reality, and it's not all about the performance each night. A big part of tour photography is capturing candid moments of artists in various cities, exposing their personality to fans on social media in ways they don't see on stage.";
                $active_nav = 'tourLifeNavActive';
                break;

            case 'weddings':
                $title = 'Weddings';
                $description = 'Wedding ceremonies, receptions, bridal, and engagement shoots.';
                $active_nav = 'weddingsNavActive';
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
            $active_nav => true,
        ]);
    }
}
