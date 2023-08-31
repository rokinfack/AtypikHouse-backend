<?php

namespace App\Factory;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Habitat>
 *
 * @method static Habitat|Proxy createOne(array $attributes = [])
 * @method static Habitat[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Habitat[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Habitat|Proxy find(object|array|mixed $criteria)
 * @method static Habitat|Proxy findOrCreate(array $attributes)
 * @method static Habitat|Proxy first(string $sortedField = 'id')
 * @method static Habitat|Proxy last(string $sortedField = 'id')
 * @method static Habitat|Proxy random(array $attributes = [])
 * @method static Habitat|Proxy randomOrCreate(array $attributes = [])
 * @method static Habitat[]|Proxy[] all()
 * @method static Habitat[]|Proxy[] findBy(array $attributes)
 * @method static Habitat[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Habitat[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static HabitatRepository|RepositoryProxy repository()
 * @method Habitat|Proxy create(array|callable $attributes = [])
 */
final class HabitatFactory extends ModelFactory
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
            'name' => self::faker()->text(),
            'description' => self::faker()->text(),
            'price' => self::faker()->randomNumber(),
            'notes' => self::faker()->randomNumber(),
            'location' => self::faker()->address(),
            'latitude' => self::faker()->latitude,
            'longitude' => self::faker()->longitude,
            'coverImage' => MediaFactory::random()->object(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this// ->afterInstantiate(function(Habitat $habitat): void {})
            ;
    }

    protected static function getClass(): string
    {
        return Habitat::class;
    }
}
