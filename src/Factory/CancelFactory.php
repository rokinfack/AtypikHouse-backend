<?php

namespace App\Factory;

use App\Entity\Cancel;
use App\Repository\CancelRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Cancel>
 *
 * @method static Cancel|Proxy createOne(array $attributes = [])
 * @method static Cancel[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Cancel[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Cancel|Proxy find(object|array|mixed $criteria)
 * @method static Cancel|Proxy findOrCreate(array $attributes)
 * @method static Cancel|Proxy first(string $sortedField = 'id')
 * @method static Cancel|Proxy last(string $sortedField = 'id')
 * @method static Cancel|Proxy random(array $attributes = [])
 * @method static Cancel|Proxy randomOrCreate(array $attributes = [])
 * @method static Cancel[]|Proxy[] all()
 * @method static Cancel[]|Proxy[] findBy(array $attributes)
 * @method static Cancel[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Cancel[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CancelRepository|RepositoryProxy repository()
 * @method Cancel|Proxy create(array|callable $attributes = [])
 */
final class CancelFactory extends ModelFactory
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
            'reason' => self::faker()->text,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this->afterInstantiate(function (Cancel $cancel): void {
            $cancel->setReservation(ReservationFactory::createOne()->object());
        });
    }

    protected static function getClass(): string
    {
        return Cancel::class;
    }
}
