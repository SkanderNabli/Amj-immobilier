<?php


namespace App\Controller;


use App\Entity\Property;
use App\Entity\Search;
use App\Form\SearchType;
use App\Repository\PropertyRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{

    /**
     * @var PropertyController
     */
    private $repository;

    /**
     * @var PropertyController
     */
    private $search;


    /**
     * PropertyController constructor.
     * @param PropertyRepository $repository
     */
    public function __construct(PropertyRepository $repository)
    {
        $this->search = new Search();
        $this->repository = $repository;
    }

    /**
     * @Route("/annonces", name="property.annonces")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator,Request $request): Response
    {
        $this->search($request);

        $prop = $paginator->paginate(
            $this->repository->queryFindAllVisible($this->search),
            $request->query->getInt('page', 1),
            12
        );


        return $this->render('property/showAll.html.twig', [
            "property" => $prop,
            "formTier" => true,
            "current_menu" => "annonce",
            "formSearch" => $this->search($request)->createView()
        ]);

    }

    /**
     * @Route("/annonces/{slug}-{id}", name="property.show", requirements={"slug": "[a-zA-Z0-9\-]*", "id": "[0-9]*"})
     * @param Property $property
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function show(Property $property, string $slug, Request $request): Response
    {

        $slugtest = $property->getSlug();

        if ($slugtest !== $slug) {
            return $this->redirectToRoute('property.show', [
                "id" => $property->getId(),
                "slug" => $slugtest
            ], 301);
        }

        return $this->render('property/show.html.twig', [
            "prop" => $property,
            "formTier" => false,
            'formSearch' => $this->search($request)->createView()
        ]);

    }

    /**
     * @param Request $request
     * @return FormInterface
     */
    public function search(Request $request){

        $form = $this->createForm(SearchType::class, $this->search,[
            'action' => $this->generateUrl('property.annonces')
        ]);
        $form->handleRequest($request);

        return $form;
    }


}