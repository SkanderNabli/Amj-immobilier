<?php


namespace App\Controller\Admin\Dashboard;

use App\Entity\Property;
use App\Form\PropType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("admin/my-ads", name="admin.myads")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function myads(PaginatorInterface $paginator, Request $request):Response
    {

        $prop = $paginator->paginate(
            $this->repository->queryFindAll(),
            $request->query->getInt('page',1),
            12
        );

        return $this->render('admin/dashboard/index.html.twig', [
            "property" => $prop,
            "profilMenu" => "My-ads"
        ]);

    }

    /**
     * @Route("admin/edit/{slug}-{id}", name="admin.property.edit", methods="GET|POST", requirements={"slug": "[a-zA-Z0-9\-]*", "id": "[0-9]*"})
     * @param Property $property
     * @param Request $request
     * @return Response
     */
    public function edit(Property $property, Request $request){

        $form = $this->createForm(PropType::class, $property);
        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid()){
            $property->setUpdateAt();
            $this->em->flush();
            $this->addFlash('success', 'Modification was made.');
            return $this->redirectToRoute('admin.myads');
        }

        return $this->render("admin/dashboard/editProp.html.twig",[
            "property" => $property,
            "formAddProp" => $form->createView()
        ]);

    }

    /**
     * * @Route("/admin/create", name="admin.property.create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request){
        $property = new Property();
        $form = $this->createForm(PropType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $property->setAuthor($this->getUser());
            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success', 'Addition has been made.');
            return $this->redirectToRoute('admin.myads');
        }

        return $this->render("admin/dashboard/new.html.twig",[
            "property" => $property,
            "formAddProp" => $form->createView()
        ]);
    }

    /**
     * * @Route("/admin/delete/{id}", name="admin.property.delete", methods="DELETE")
     * @param Property $property
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Property $property,Request $request){

        if($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))){
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success', 'Delete perform');
        }

        return $this->redirectToRoute('admin.myads');
    }

    /**
     * @Route("/admin/sold/{id}", name="admin.property.sold")
     * @param Property $property
     * @param Request $request
     * @return RedirectResponse
     */
    public function sold(Property $property,Request $request){

        if($this->isCsrfTokenValid('sold' . $property->getId(), $request->get('_token'))){
            $property->setSold(!$property->getSold());
            $property->setUpdateAt();
            $this->em->flush();
            if(!$property->getSold()){
                $this->addFlash('success', 'Adding to list');
            }else{
                $this->addFlash('success', 'Adding to sold');
            }

        }

        return $this->redirectToRoute('admin.myads');
    }


}