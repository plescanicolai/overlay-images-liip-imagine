<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("image", name="image")
     * @param Request $request
     * @return array|BinaryFileResponse
     */
    public function imageAction(Request $request)
    {
        $horizontalForm = $this->createFormBuilder()->add('horizontal', FileType::class)->add('submit', SubmitType::class)->getForm();
        $verticalForm = $this->createFormBuilder()->add('vertical', FileType::class)->add('submit', SubmitType::class)->getForm();
        $horizontalForm->handleRequest($request);
        $verticalForm->handleRequest($request);
        if ($horizontalForm->isSubmitted() && $horizontalForm->isValid()) {
            $data = $horizontalForm->getData();
            /** @var UploadedFile $image1 */
            $image1 = $data['horizontal'];
            $extension = $image1->guessExtension();
            $fileName = md5(uniqid()).'.'.$extension;
            $image1->move(realpath($this->getParameter('kernel.root_dir').'/../web/images/'), $fileName);

            $imageUploaded = realpath($this->getParameter('kernel.root_dir').'/../web/images/'.$fileName);
            $rama = imagecreatefrompng(realpath($this->getParameter('kernel.root_dir').'/../web/images/rame3.png'));

            $ramaWidth = imagesx($rama);
            $ramaHeight = imagesy($rama);
            $this->resizeImage($imageUploaded, realpath($this->getParameter('kernel.root_dir').'/../web/images/'), $fileName, $ramaHeight, $ramaWidth);

            if ($extension == 'gif') {
                $image = imagecreatefromgif($imageUploaded);
            } elseif ($extension == "jpeg" or $extension == "jpg") {
                $image = imagecreatefromjpeg($imageUploaded);
            } elseif ($extension == 'png') {
                $image = imagecreatefrompng($imageUploaded);
            } else {
                die("wrong extension");
            }

            imagecopyresampled($image, $rama, 0, 0, 0, 0, $ramaWidth, $ramaHeight, $ramaWidth, $ramaHeight);

            switch ($extension) {
                case 'png':
                    imagepng($image, $imageUploaded);
                    break;
                case 'jpeg':
                case 'jpg':
                    imagejpeg($image, $imageUploaded, 90);
                    break;
                case 'gif':
                    imagegif($image, $imageUploaded);
                    break;
                default:
                    break;
            }

            $response = new BinaryFileResponse($imageUploaded);
            $response->headers->set('Content-Type', 'image/png');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);

            return $response;
        }
        if ($verticalForm->isSubmitted() && $verticalForm->isValid()) {
            $data = $verticalForm->getData();
            /** @var UploadedFile $image1 */
            $image1 = $data['vertical'];
            $extension = $image1->guessExtension();
            $fileName = md5(uniqid()).'.'.$extension;
            $image1->move(realpath($this->getParameter('kernel.root_dir').'/../web/images/'), $fileName);

            $imageUploaded = realpath($this->getParameter('kernel.root_dir').'/../web/images/'.$fileName);
            $rama = imagecreatefrompng(realpath($this->getParameter('kernel.root_dir').'/../web/images/rame2.png'));

            $ramaWidth = imagesx($rama);
            $ramaHeight = imagesy($rama);
            $this->resizeImage($imageUploaded, realpath($this->getParameter('kernel.root_dir').'/../web/images/'), $fileName, $ramaHeight, $ramaWidth);

            if ($extension == 'gif') {
                $image = imagecreatefromgif($imageUploaded);
            } elseif ($extension == "jpeg" or $extension == "jpg") {
                $image = imagecreatefromjpeg($imageUploaded);
            } elseif ($extension == 'png') {
                $image = imagecreatefrompng($imageUploaded);
            } else {
                die("wrong extension");
            }

            imagecopyresampled($image, $rama, 0, 0, 0, 0, $ramaWidth, $ramaHeight, $ramaWidth, $ramaHeight);

            switch ($extension) {
                case 'png':
                    imagepng($image, $imageUploaded);
                    break;
                case 'jpeg':
                case 'jpg':
                    imagejpeg($image, $imageUploaded, 90);
                    break;
                case 'gif':
                    imagegif($image, $imageUploaded);
                    break;
                default:
                    break;
            }

            $response = new BinaryFileResponse($imageUploaded);
            $response->headers->set('Content-Type', 'image/png');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);

            return $response;
        }

        return $this->render('default/image.html.twig', ['horizontalForma' => $horizontalForm->createView(),
                'verticalForm' => $verticalForm->createView(),
                'horizontalForm' => $horizontalForm->createView(),
        ]);
    }

    private function resizeImage($image, $newPath, $name, $height = 0, $width = 0)
    {
        $size = getimagesize($image);
        $heightOrig = $size[1];
        $widthOrig = $size[0];

        $fileExtension = 'jpg';
        $jpegQuality = 75;
        $width = round($width);
        $height = round($height);

        $gdImageDest = imagecreatetruecolor($width, $height);
        $gdImageSrc = null;
        switch ($fileExtension) {
            case 'png':
                $gdImageSrc = imagecreatefrompng($image);
                imagealphablending($gdImageDest, false);
                imagesavealpha($gdImageDest, true);
                break;
            case 'jpeg':
            case 'jpg':
                $gdImageSrc = imagecreatefromjpeg($image);
                break;
            case 'gif':
                $gdImageSrc = imagecreatefromgif($image);
                break;
            default:
                break;
        }

        imagecopyresampled($gdImageDest, $gdImageSrc, 0, 0, 0, 0, $width, $height, $widthOrig, $heightOrig);

        $newFileName = $newPath.'/'.$name;

        switch ($fileExtension) {
            case 'png':
                imagepng($gdImageDest, $newFileName);
                break;
            case 'jpeg':
            case 'jpg':
                imagejpeg($gdImageDest, $newFileName, $jpegQuality);
                break;
            case 'gif':
                imagegif($gdImageDest, $newFileName);
                break;
            default:
                break;
        }

        return $newPath;
    }
}
