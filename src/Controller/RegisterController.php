<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegisterController extends AbstractController
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;
    private $encoder;

    /**
     * RegisterController constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/register", name="register", methods={"POST|GET"} )
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return RedirectResponse|Response
     */
    public function register(Request $request,AuthenticationUtils $authenticationUtils){

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {

                $user->setUsername($form->get('username')->getData())
                    ->setEmail($form->get('email')->getData())
                    ->setPassword(
                        $this->encoder->encodePassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    )
                    ->setCivility($form->get('civility')->getData())
                    ->setFirstname($form->get('firstname')->getData())
                    ->setLastname($form->get('lastname')->getData())
                    ->setTel($form->get('tel')->getData());

                $this->em->persist($user);
                $this->em->flush();
//                $this->addFlash("success", "Vous vous êtes bien enregistré.</br>Un mail de confirmation vous as été envoyé.<br>Vous pouvez vous identifié.");

                return $this->redirectToRoute('login');

        }

        return $this->render('pages/register.html.twig', [
            "profilMenu" => "Register",
            'registerForm' => $form->createView()
        ]);
    }

}