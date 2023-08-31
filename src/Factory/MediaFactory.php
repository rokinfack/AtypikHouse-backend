<?php

namespace App\Factory;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\KernelInterface;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Media>
 *
 * @method static Media|Proxy createOne(array $attributes = [])
 * @method static Media[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Media[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Media|Proxy find(object|array|mixed $criteria)
 * @method static Media|Proxy findOrCreate(array $attributes)
 * @method static Media|Proxy first(string $sortedField = 'id')
 * @method static Media|Proxy last(string $sortedField = 'id')
 * @method static Media|Proxy random(array $attributes = [])
 * @method static Media|Proxy randomOrCreate(array $attributes = [])
 * @method static Media[]|Proxy[] all()
 * @method static Media[]|Proxy[] findBy(array $attributes)
 * @method static Media[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Media[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MediaRepository|RepositoryProxy repository()
 * @method Media|Proxy create(array|callable $attributes = [])
 */
final class MediaFactory extends ModelFactory
{
    private KernelInterface $appKernel;
    public File $mediaFile;
    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $this->mediaFile =new File(self::faker()->file($this->appKernel->getProjectDir()."/public/uploads/test", $this->appKernel->getProjectDir()."/public/images"));
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'fileName' => $this->mediaFile->getFilename(),
            'file' =>$this->mediaFile,
            'fileSize' => $this->mediaFile->getSize(),
            'filePath'=>$this->mediaFile->getPath(),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this// ->afterInstantiate(function(Media $media): void {})
            ;
    }

    protected static function getClass(): string
    {
        return Media::class;
    }
}
