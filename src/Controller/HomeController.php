<?php


namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @var PropertyController
     */
    private $propertyController;

    public function __construct(PropertyController $propertyController)
    {

        $this->propertyController = $propertyController;
    }

    /**
     * @Route("/", name="home")
     * @param PropertyRepository $repository
     * @param Request $request
     * @return Response
     */
    public function index(PropertyRepository $repository, Request $request) : Response
    { 

        $propertiesLast = $repository->findLast(4);
        $propertiesFeatured = $repository->findFeatured(3);

        return $this->render('pages/home.html.twig', [
            "proprietes" => $propertiesLast,
            "featured" => $propertiesFeatured,
            "formTier" => true,
            'current_menu' => 'home',
            "formSearch" => $this->propertyController->search($request)->createView()
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param ContactNotification $notification
     * @return Response
     */
    public function contact(Request $request, ContactNotification $notification){

        $contact = new Contact() ;
        $formContact = $this->createForm(ContactType::class, $contact);

        $formContact->handleRequest($request);

        if ($formContact->isSubmitted()){
            $notification->notify($contact);
            $this->addFlash('success','Message bien envoyé.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('pages/contact.html.twig',[
            'current_menu' => 'contact',
            'formContact' => $formContact->createView()
        ]);

    }

}