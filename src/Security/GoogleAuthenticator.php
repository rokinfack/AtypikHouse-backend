<?php

namespace App\Security;

use App\Entity\Media;
use App\Entity\User;
use App\Kernel;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\HttpFoundation\File\File;


class GoogleAuthenticator implements OAuthAwareUserProviderInterface
{
    private UserRepository $userRepository;
    private MediaRepository $mediaRepository;
    private $kernel;

    public function __construct(UserRepository $userRepository, MediaRepository $mediaRepository, Kernel $kernel)
    {
        $this->userRepository = $userRepository;
        $this->mediaRepository = $mediaRepository;
        $this->kernel = $kernel;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->userRepository->findOneByEmail($response->getEmail());
        if ($user == null) {
            $user = new User();
            $media = new Media();
            $newProfileImage = $this->kernel->getProjectDir() . "/public/uploads/images/newProfile12.jpg";

            copy($response->getProfilePicture(), $newProfileImage);

            $profileFile = new File($newProfileImage);
            $media->setFilePath($profileFile->getPath());
            $media->setFileName($profileFile->getFilename());
            $media->setFileSize($profileFile->getSize());


            $user->setEmail($response->getEmail());
            $user->setFirstName($response->getRealName());
            $user->setLastName($response->getLastName());
            $user->setPassword("connect with google");

            $this->mediaRepository->save($media, true);

            $user->setProfileImage($media);

            $this->userRepository->save($user, true);

        }
        return $user;
    }
}
