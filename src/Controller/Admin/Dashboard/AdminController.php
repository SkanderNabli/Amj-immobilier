<?php


namespace App\Controller\Admin\Dashboard;

use App\Entity\Property;
use App\Entity\User;
use App\Form\PropType;
use App\Form\UserType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    private $encoder;
    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder,PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->encoder = $encoder;
        $this->em = $em;
    }

    /**
     * @Route("/my-ads", name="admin.myads")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function myads(PaginatorInterface $paginator, Request $request):Response
    {

        $prop = $paginator->paginate(
            $this->repository->findMyAds($this->getUser()),
            $request->query->getInt('page',1),
            12
        );

        return $this->render('admin/dashboard/index.html.twig', [
            "property" => $prop,
            "profilMenu" => "My-ads"
        ]);

    }

    /**
     * @Route("/bookmarks", name="admin.bookmarks")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function bookmarks(PaginatorInterface $paginator, Request $request):Response
    {


        $prop = $paginator->paginate(
            $this->repository->findBookmarks($this->getUser()),
            $request->query->getInt('page',1),
            12
        );

        return $this->render('admin/dashboard/bookmarks.html.twig', [
            "property" => $prop,
            "profilMenu" => "bookmarks"
        ]);

    }

    /**
     * @Route("/reset-password", name="admin.resetPsw")
     * @param Request $request
     * @param UserInterface $user
     * @return RedirectResponse|Response
     */
    public function resetPsw(Request $request, UserInterface $user)
    {
        if ($request->get("current_password") && $request->get('new_current_password') && $request->get('repeat_new_current_password') != null ){
            
            if(
                $this->encoder->isPasswordValid($user, $request->get('current_password')) &&
                ($request->get('new_current_password') === $request->get('repeat_new_current_password'))
            ){

                $user->setPassword(
                    $this->encoder->encodePassword(
                        $user,
                        $request->get('new_current_password')
                    )
                );
                $this->em->flush();
                $this->addFlash('success', 'Modification was made.');

                return $this->redirectToRoute('logout');

            }
        }



        return $this->render("admin/dashboard/reset-password.html.twig",[
            "profilMenu" => "resetPsw",
        ]);

    }

    /**
     * @Route("/edit-user", name="admin.editProfil")
     * @param Request $request
     * @param UserInterface $user
     * @return RedirectResponse|Response
     */
    public function editProfil(Request $request, UserInterface $user)
    {

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        return $this->render("admin/dashboard/edit-user.html.twig",[
            "user" => $form->createView(),
            "profilMenu" => "edit-user",

        ]);

    }

    /**
     * @Route("/edit/{slug}-{id}", name="admin.property.edit", methods="GET|POST", requirements={"slug": "[a-zA-Z0-9\-]*", "id": "[0-9]*"})
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
     * * @Route("/create", name="admin.property.create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request){
        $property = new Property();
        $form = $this->createForm(PropType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $property
                ->setAuthor($this->getUser())
                ->setUpdateAt()
            ;
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
     * * @Route("/delete/{id}", name="admin.property.delete", methods="DELETE")
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
     * @Route("/sold/{id}", name="admin.property.sold")
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

    /**
     * * @Route("/favoris/add/{id}", name="admin.favoris.add", methods="POST")
     * @param Property $property
     * @param Request $request
     * @return JsonResponse
     */
    public function addFavoris(Property $property, Request $request){

        $data = json_decode($request->getContent(),true);

        if($this->isCsrfTokenValid('add' . $property->getId(), $data['_token'])){
            $search = false;
            $favoris = $this->getUser()->getFavoris()->getValues();

            foreach ($favoris as $favori ){
                if ($favori->getId() == $property->getId()){
                    $search = true;
                }
            }

            if ($search){
                $property->removeFavori($this->getUser());
                $this->em->flush();
                return new JsonResponse([
                    'success' => true,
                    'action' => "remove"
                ]);
            }else{
                $property->addFavori($this->getUser());
                $this->em->flush();
                return new JsonResponse([
                    'success' => true,
                    'action' => "add"
                ]);
            }

        }

        return new JsonResponse(['error' => 'Token invalide'],400);
    }

    /**
     * * @Route("/favoris/remove/{id}", name="admin.favoris.remove", methods="POST")
     * @param Property $property
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFavoris(Property $property, Request $request){

        $data = json_decode($request->getContent(),true);


        if($this->isCsrfTokenValid('remove' . $property->getId(), $data['_token'])){
            $property->removeFavori($this->getUser());
            $this->em->flush();
            return new JsonResponse([
                'success' => true,
                'action' => "remove"
                ]);
        }

        return new JsonResponse(['error' => 'Token invalide'],400);
    }


}