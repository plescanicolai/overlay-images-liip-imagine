<?php

namespace AppBundle\Controller;

use Imagine\Image\Palette\RGB;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Imagine\Gd\Image;
use Imagine\Image\Point;
use Imagine\Image\Metadata\MetadataBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("liip", name="liip")
     * @param Request $request
     * @return BinaryFileResponse|Response
     */
    public function liipAction(Request $request)
    {
        $horizontalForm = $this->createFormBuilder()->add('horizontal', FileType::class)->getForm();
        $verticalForm = $this->createFormBuilder()->add('vertical', FileType::class)->getForm();
        $horizontalForm->handleRequest($request);
        $verticalForm->handleRequest($request);
        if ($horizontalForm->isSubmitted() && $horizontalForm->isValid()) {
            $data = $horizontalForm->getData();
            /** @var UploadedFile $imageAdd */
            $imageAdd = $data['horizontal'];

            return $this->imageCover($imageAdd, 'rame3.png');
        }
        if ($verticalForm->isSubmitted() && $verticalForm->isValid()) {
            $data = $verticalForm->getData();
            /** @var UploadedFile $imageAdd */
            $imageAdd = $data['vertical'];

            return $this->imageCover($imageAdd, 'rame1.png');
        }

        return $this->render('default/image.html.twig', ['horizontalForma' => $horizontalForm->createView(),
            'verticalForm' => $verticalForm->createView(),
            'horizontalForm' => $horizontalForm->createView(),
        ]);
    }

    private function imageCover(UploadedFile $imageAdd, $coverImage)
    {
        $extension = $imageAdd->guessExtension();
        $fileName = md5(uniqid()).'.'.$extension;
        $imagePath = realpath($this->getParameter('kernel.root_dir').'/../web/images/');
        $imageAdd->move($imagePath, $fileName);

        $imageUploaded = $imagePath.'/'.$fileName;
        $ramaSize = getimagesize($imagePath.'/'.$coverImage);

        $temp = imagecreatefromjpeg($imageUploaded);
        $image = new Image($temp, new RGB(), new MetadataBag());

        $this->get('liip_imagine.filter.loader.resize')->load($image, ['size' => [$ramaSize[0], $ramaSize[1]]]);

        /** @var Image $imageUploaded1 */
        $imageUploaded1 = $this->get('liip_imagine.filter.loader.watermark')->load($image, ['image' => '../web/images/'.$coverImage]);

        imagejpeg($imageUploaded1->getGdResource(), $imageUploaded, 90);

        $response = new BinaryFileResponse($imageUploaded);
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);

        return $response;
    }
}
