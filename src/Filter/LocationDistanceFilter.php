<?php

namespace App\Filter;


use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class LocationDistanceFilter extends AbstractFilter
{
    const DISTANCE = "distance";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";

    private $appliedAlready = false;

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        // otherwise filter is applied to order and page as well
        if (
            !$this->isPropertyEnabled($property, $resourceClass) ||
            !$this->isPropertyMapped($property, $resourceClass)
        ) {
            return;
        }

        //make sure latitude and longitude are part of specs
        if (!($this->isPropertyMapped(self::LATITUDE, $resourceClass)
            && $this->isPropertyMapped(self::LONGITUDE, $resourceClass))) {
            return;
        }


        if ($this->properties[self::LATITUDE] != null && $this->properties[self::LONGITUDE] != null) {
            if ($this->properties['distance'] == null)
                $this->properties['distance'] = 30;
        } else {
            //may be we should raise exception
            return;
        }

        $this->appliedAlready = True;

        $latParam = $queryNameGenerator->generateParameterName(self::LATITUDE);
        $lonParam = $queryNameGenerator->generateParameterName(self::LONGITUDE);
        $distParam = $queryNameGenerator->generateParameterName(self::DISTANCE);

        $locationWithinXKmDistance = "(
            6371.0 * acos (
                cos ( radians(:$latParam) )
                * cos( radians(o.latitude) )
                * cos( radians(o.longitude) - radians(:$lonParam) )
                + sin ( radians(:$latParam) )
                * sin( radians(o.latitude) )
           )
        )<=:$distParam";

        $queryBuilder
            ->andWhere($locationWithinXKmDistance)
            ->setParameter($latParam, $this->properties[self::LATITUDE])
            ->setParameter($lonParam, $this->properties[self::LONGITUDE])
            ->setParameter($distParam, $this->properties[self::DISTANCE]);
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }
        $type = [
            self::DISTANCE => Type::BUILTIN_TYPE_INT,
            self::LONGITUDE => Type::BUILTIN_TYPE_STRING,
            self::LATITUDE => Type::BUILTIN_TYPE_STRING
        ];

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["distance_$property"] = [
                'property' => $property,
                'type' => $type[$property] ?? Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'swagger' => [
                    'description' => 'Find locations within given radius',
                    'name' => 'distance_filter',
                    'type' => 'filter',
                ],
            ];
        }

        return $description;
    }
}