<?php

namespace App\Factory;

use App\Entity\Report;
use App\Repository\ReportRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Report>
 *
 * @method static Report|Proxy createOne(array $attributes = [])
 * @method static Report[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Report[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Report|Proxy find(object|array|mixed $criteria)
 * @method static Report|Proxy findOrCreate(array $attributes)
 * @method static Report|Proxy first(string $sortedField = 'id')
 * @method static Report|Proxy last(string $sortedField = 'id')
 * @method static Report|Proxy random(array $attributes = [])
 * @method static Report|Proxy randomOrCreate(array $attributes = [])
 * @method static Report[]|Proxy[] all()
 * @method static Report[]|Proxy[] findBy(array $attributes)
 * @method static Report[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Report[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ReportRepository|RepositoryProxy repository()
 * @method Report|Proxy create(array|callable $attributes = [])
 */
final class ReportFactory extends ModelFactory
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
            'content' => self::faker()->realText,
            'type' => self::faker()->word(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this// ->afterInstantiate(function(Report $report): void {})
            ;
    }

    protected static function getClass(): string
    {
        return Report::class;
    }
}
