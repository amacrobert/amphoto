<?php

namespace App\Controller;

use App\Entity\Freebie;
use App\Entity\Portfolio;
use App\Entity\Endorsement;
use App\Service\BlogService;
use App\Service\PhotoCategory;
use App\Service\WebContent;
use Doctrine\ORM\EntityManagerInterface;
use HieuLe\WordpressXmlrpcClient\WordpressClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private $active_portfolio = '';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, WebContent $content, PhotoCategory $category)
    {
        return $this->renderWithNav('default/index.html.twig', [
            'bodyclass' => 'slideshow',
            'photos' => [
                'concerts' => $category->getPhotos('featured/concerts'),
                'portraits' => $category->getPhotos('featured/portraits'),
                'weddings' => $category->getPhotos('featured/weddings'),
            ],
            'og' => $content->getOpenGraph(),
        ]);
    }

    /**
     * @Route("/downloads/{freebie_id}", name="download")
     */
    public function downloadsAction(Request $request, $freebie_id) {

        $freebie_repo = $this->em->getRepository(Freebie::class);

        if (is_numeric($freebie_id)) {
            $freebie = $freebie_repo->find($freebie_id);
        }
        else {
            $freebie = $freebie_repo->findOneBy(['name' => str_replace('-', ' ', $freebie_id)]);
        }

        if (!$freebie) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, ['attr' => ['placeholder' => 'EMAIL']])
            ->add('submit', SubmitType::class, ['label' => 'SEND'])
            ->getForm()
        ;

        $form->handleRequest($request);

        $submitted = $form->isSubmitted() && $form->isValid();

        if ($submitted) {
            $email = $form->getData()['email'];

            $this->get('freebie')->mail($email, $freebie);
            $this->get('freebie')->subscribe($email);
        }

        return $this->renderWithNav('default/freebie.html.twig', [
            'freebie'   => $freebie,
            'submitted' => $submitted,
            'form'      => $form->createView(),
            'email'     => $submitted ? $email : null,
        ]);
    }

    /**
     * @Route("/blog", name="blog_index")
     */
    public function blogIndexAction(WordpressClient $wordpress_client) {
        $posts = $wordpress_client->getPosts(['post_status' => 'publish']);

        return $this->renderWithNav('default/blog.html.twig', [
            'blogNavActive' => true,
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/blog/{post_id}", name="blog_post")
     */
    public function blogPostAction(WordpressClient $wordpress_client, BlogService $blog_service, $post_id) {

        $post = (object)$blog_service->getPost($post_id);
        $additional_posts = $wordpress_client->getPosts([
            'post_status' => 'publish',
            'number' => 3
        ]);

        foreach ($additional_posts as $index => $additional_post) {
            if ($additional_post['post_id'] == $post->post_id) {
                unset($additional_posts[$index]);
            }
        }

        return $this->renderWithNav('default/blog_post.html.twig', [
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
    public function aboutAction(WebContent $content) {
        return $this->renderWithNav('default/about.html.twig', [
            'og' => $content->getOpenGraph('about'),
            'endorsements' => $this->em->getRepository(Endorsement::class)->findAll(),
            'moreNavActive' => true,
            'aboutNavActive' => true,
        ]);
    }

    /**
     * @Route("/bookings", name="bookings")
     * @Route("/contact", name="bookings_alternate_link")
     */
    public function contactAction(WebContent $content) {
        return $this->renderWithNav('default/bookings.html.twig', [
            'og' => $content->getOpenGraph('bookings'),
            'contactNavActive' => true,
        ]);
    }

    /**
     * @Route("/equipment", name="equipment")
     */
    public function equipmentAction(WebContent $content) {
        return $this->renderWithNav('default/equipment.html.twig', [
            'og' => $content->getOpenGraph('equipment'),
            'moreNavActive' => true,
            'equipmentNavActive' => true,
        ]);
    }

    /**
     * @Route("/video", name="video")
     * @Route("/video/", name="video_trailing_slash")
     */
    public function videoAction(WebContent $content) {
        return $this->renderWithNav('default/video.html.twig', [
            'og' => $content->getOpenGraph('video'),
            'videoNavActive' => true,
            'portfolioNavActive' => true,
        ]);
    }

    /**
     * @Route("/photos/{category}", name="category")
     */
    public function categoryAction(PhotoCategory $category_service, $category) {

        $this->active_portfolio = $category;

        $portfolio_repo = $this->em->getRepository(Portfolio::class);
        $portfolio = $portfolio_repo->findOneBy(['machine_name' => $category]);
        $directory = 'images/' . $category;

        // If the portfolio doesn't exist, redirect home
        if (!$portfolio || !file_exists($directory)) {
            return $this->redirectToRoute('homepage');
        }

        $photos = $category_service->getPhotos($category);

        return $this->renderWithNav('default/category.html.twig', [
            'portfolio' => $portfolio,
            'photos' => $photos,
            'og' => [
                'title' => $portfolio->getName() . ' - Andrew MacRobert Photography',
                'images' => [
                    'https://andrewmacrobert.com/' . $photos[0]->uri,
                    'https://andrewmacrobert.com/' . $photos[1]->uri,
                    'https://andrewmacrobert.com/' . $photos[2]->uri,
                ]
            ],
        ]);
    }

    protected function renderWithNav($view, array $parameters = array(), Response $response = null) {
        $menu = $this->getMenu($this->active_portfolio);
        $parameters = array_merge($parameters, [
            'menu' => $menu->portfolios,
            'portfolioNavActive' => isset($parameters['portfolioNavActive']) ? $parameters['portfolioNavActive'] : $menu->portfolio_nav_active,
        ]);

        return parent::render($view, $parameters, $response);
    }

    private function getMenu($portfolio_machine_name = null)
    {
        $portfolio_repo = $this->em->getRepository(Portfolio::class);
        $portfolios = $portfolio_repo->findBy(['listed' => true], ['ordinal' => 'ASC']);

        $portfolio_nav_active = false;

        foreach ($portfolios as $portfolio) {
            if ($portfolio->getMachineName() == $portfolio_machine_name) {
                $portfolio->setActive(true);
                $portfolio_nav_active = true;
                break;
            }
        }

        return (object)[
            'portfolio_nav_active' => $portfolio_nav_active,
            'portfolios' => $portfolios,
        ];
    }
}
