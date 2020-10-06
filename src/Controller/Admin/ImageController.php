<?php


namespace App\Controller\Admin;


use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{

    /**
     * * @Route("/{id}", name="admin.image.delete", methods="DELETE")
     * @param Image $image
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Image $image, Request $request){

        $data = json_decode($request->getContent(),true);

        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])){
            $em= $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['error' => 'Token invalide'],400);
    }

}