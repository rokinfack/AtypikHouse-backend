<?php

namespace App\Controller;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(
    path: "/api/media",
    defaults: ['_api_resource_class' => Media::class,]
)]
class MediaController extends AbstractController
{
    private ?File $file = null;

    #[Route(
        path: '',
        name: 'post_media',
        defaults: [
            '_api_operation_name' => 'post_media',
        ],
        methods: ['POST'],
    )]
    public function post(Request $request): Media
    {
        $this->file = $request->files->get('file');
        if (!$this->file) {
            throw new BadRequestHttpException('"file" is required');
        }

        $media = new Media();
        $media->setFile($this->file);
        $media->setFilePath($this->file->getRealPath());
        $media->setFileName($this->file->getFilename());
        $media->setFileSize($this->file->getSize());
        $media->setFileUrl("");
        $media->setUpdatedAt(new \DateTimeImmutable());

        return $media;
    }
}
