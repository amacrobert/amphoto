<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Freebie;
use AppBundle\Entity\Portfolio;

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
     * @Route("/downloads/{freebie_id}", name="download")
     */
    public function downloadsAction(Request $request, $freebie_id) {

        $freebie_repo = $this->get('doctrine.orm.entity_manager')->getRepository(Freebie::class);

        if (is_numeric($freebie_id)) {
            $freebie = $freebie_repo->find($freebie_id);
        }
        else {
            $freebie = $freebie_repo->findOneBy(['name' => str_replace('-', ' ', $freebie_id)]);
        }

        if (!$freebie) {
            $this->redirectToRoute('homepage');
        }

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, ['attr' => ['placeholder' => 'EMAIL']])
            ->add('submit', SubmitType::class, ['label' => 'SUBMIT'])
            ->getForm()
        ;

        $form->handleRequest($request);

        $submitted = $form->isSubmitted() && $form->isValid();

        if ($submitted) {
            $email = $form->getData()['email'];

            $this->get('freebie')->mail($email, $freebie);
            $this->get('freebie')->subscribe($email);
        }

        return $this->render('default/freebie.html.twig', [
            'freebie'   => $freebie,
            'submitted' => $submitted,
            'form'      => $form->createView(),
            'email'     => $submitted ? $email : null,
        ]);
    }

    /**
     * @Route("/blog", name="blog_index")
     */
    public function blogIndexAction() {
        $posts = $this->get('wordpress_client')->getPosts(['post_status' => 'publish']);

        return $this->render('default/blog.html.twig', [
            'blogNavActive' => true,
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/blog/{post_id}", name="blog_post")
     */
    public function blogPostAction($post_id) {

        $post = (object)$this->get('blog')->getPost($post_id);
        $additional_posts = $this->get('wordpress_client')->getPosts([
            'post_status' => 'publish',
            'number' => 3
        ]);

        foreach ($additional_posts as $index => $additional_post) {
            if ($additional_post['post_id'] == $post->post_id) {
                unset($additional_posts[$index]);
            }
        }

        return $this->render('default/blog_post.html.twig', [
            'blogNavActive' => true,
            'post' => $post,
            'posts' => $additional_posts,
            'og' => [
                'title' => $post->post_title,
                'images' => [$post->featured_image],
                'description' => $post->post_excerpt,
            ],
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction() {
        return $this->render('default/about.html.twig', [
            'endorsements' => $this->get('web_content')->getEndorsements(),
            'og' => $this->get('web_content')->getOpenGraph('about'),
            'moreNavActive' => true,
            'aboutNavActive' => true,
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction() {
        return $this->render('default/contact.html.twig', [
            'og' => $this->get('web_content')->getOpenGraph('contact'),
            'contactNavActive' => true,
        ]);
    }

    /**
     * @Route("/equipment", name="equipment")
     */
    public function equipmentAction() {
        return $this->render('default/equipment.html.twig', [
            'og' => $this->get('web_content')->getOpenGraph('equipment'),
            'moreNavActive' => true,
            'equipmentNavActive' => true,
        ]);
    }

    /**
     * @Route("/video", name="video")
     */
    public function videoAction() {
        return $this->render('default/video.html.twig', [
            'og' => $this->get('web_content')->getOpenGraph('video'),
            'videoNavActive' => true,
            'portfolioNavActive' => true,
        ]);
    }

    /**
     * @Route("/photos/{category}", name="category")
     */
    public function categoryAction($category) {

        $portfolio_repo = $this->get('doctrine.orm.entity_manager')->getRepository(Portfolio::class);
        $portfolio = $portfolio_repo->findOneBy(['machine_name' => $category]);
        $directory = 'images/' . $category;

        // If the portfolio doesn't exist, redirect home
        if (!$portfolio || !file_exists($directory)) {
            return $this->redirectToRoute('homepage');
        }

        $photos = $this->get('photo_category')->getPhotos($category);

        switch ($category) {
            case 'nightlife-events':
                $title = 'Nightlife & Events';
                $active_nav = 'nightlifeNavActive';
                $portfolio_nav_active = true;
                break;

            case 'day-parties':
                $title = 'Day Parties';
                $active_nav = 'dayPartiesNavActive';
                $portfolio_nav_active = true;
                break;

            case 'festivals':
                $title = 'Festivals';
                $description = 'I am currently looking for music festival bookings. If you are an organizer for a festival and interested in media coverage that captures it in the best possible light, <a href="/contact" style="text-decoration:underline">reach out!</a>';
                $active_nav = 'festivalsNavActive';
                $portfolio_nav_active = true;
                break;

            case 'portraits':
                $title = 'Portraits';
                $active_nav = 'portraitsNavActive';
                $portfolio_nav_active = true;
                break;

            case 'portraits-women':
                $title = 'Portraiture: Women';
                $description = '<a href="/photos/portraits-men">Switch to men</a>';
                $active_nav = 'portraitsWomenNavActive';
                break;

            case 'portraits-men':
                $title = 'Portraiture: Men';
                $description = '<a href="/photos/portraits-women">Switch to women</a>';
                $active_nav = 'portraitsMenNavActive';
                break;

            case 'tour-life':
                $title = 'Tour Life';
                $description = "Being on tour is living in an alternate reality, and it's not all about the performance each night. A big part of tour photography is capturing candid moments of artists in various cities, exposing their personality to fans on social media in ways they don't see on stage.";
                $active_nav = 'tourLifeNavActive';
                break;

            case 'weddings':
                $title = 'Weddings';
                $active_nav = 'weddingsNavActive';
                $portfolio_nav_active = true;
                break;

            default:
                $title = 'Huh?';
                $description = 'How did you get here?';
                break;
        }

        return $this->render('default/category.html.twig', [
            'portfolio' => $portfolio,
            'photos' => $photos,
            'endorsements' => $this->get('web_content')->getEndorsements($category),
            'og' => [
                'title' => $title . ' - Andrew MacRobert Photography',
                'images' => [
                    'http://andrewmacrobert.com/' . $photos[0]->uri,
                    'http://andrewmacrobert.com/' . $photos[1]->uri,
                    'http://andrewmacrobert.com/' . $photos[2]->uri,
                ]
            ],
            $active_nav => true,
            'portfolioNavActive' => empty($portfolio_nav_active) ? false : $portfolio_nav_active,
        ]);
    }
}
