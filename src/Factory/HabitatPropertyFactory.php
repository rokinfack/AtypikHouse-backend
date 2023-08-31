<?php

namespace App\Factory;

use App\Entity\HabitatProperty;
use App\Repository\HabitatPropertyRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<HabitatProperty>
 *
 * @method static HabitatProperty|Proxy createOne(array $attributes = [])
 * @method static HabitatProperty[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static HabitatProperty[]|Proxy[] createSequence(array|callable $sequence)
 * @method static HabitatProperty|Proxy find(object|array|mixed $criteria)
 * @method static HabitatProperty|Proxy findOrCreate(array $attributes)
 * @method static HabitatProperty|Proxy first(string $sortedField = 'id')
 * @method static HabitatProperty|Proxy last(string $sortedField = 'id')
 * @method static HabitatProperty|Proxy random(array $attributes = [])
 * @method static HabitatProperty|Proxy randomOrCreate(array $attributes = [])
 * @method static HabitatProperty[]|Proxy[] all()
 * @method static HabitatProperty[]|Proxy[] findBy(array $attributes)
 * @method static HabitatProperty[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static HabitatProperty[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static HabitatPropertyRepository|RepositoryProxy repository()
 * @method HabitatProperty|Proxy create(array|callable $attributes = [])
 */
final class HabitatPropertyFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'value' => [],
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(HabitatProperty $habitatProperty): void {})
        ;
    }

    protected static function getClass(): string
    {
        return HabitatProperty::class;
    }
}
