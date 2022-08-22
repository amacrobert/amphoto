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
            ],
            'og' => $content->getOpenGraph(),
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction(WebContent $content)
    {
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
    public function contactAction(WebContent $content)
    {
        return $this->renderWithNav('default/bookings.html.twig', [
            'og' => $content->getOpenGraph('bookings'),
            'contactNavActive' => true,
        ]);
    }

    /**
     * @Route("/equipment", name="equipment")
     */
    public function equipmentAction(WebContent $content)
    {
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
    public function videoAction(WebContent $content)
    {
        return $this->renderWithNav('default/video.html.twig', [
            'og' => $content->getOpenGraph('video'),
            'videoNavActive' => true,
            'portfolioNavActive' => true,
        ]);
    }

    /**
     * @Route("/photos/{category}", name="category")
     */
    public function categoryAction(PhotoCategory $category_service, $category, string $project_dir)
    {
        $this->active_portfolio = $category;

        $portfolio_repo = $this->em->getRepository(Portfolio::class);
        $portfolio = $portfolio_repo->findOneBy(['machine_name' => $category]);
        $directory = 'images/' . $category;
        $directory_absolute = $project_dir . '/public/' . $directory;

        if (!$portfolio || !file_exists($directory_absolute)) {
            throw new \Exception(sprintf(
                'Cannot resolve photo category "%s" because dir "%s" does not exist',
                $category, $directory_absolute
            ));
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

    protected function renderWithNav($view, array $parameters = array(), Response $response = null)
    {
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
