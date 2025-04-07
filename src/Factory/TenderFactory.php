<?php

namespace App\Factory;

use App\Entity\Tender;
use DateTime;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Tender>
 */
final class TenderFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Tender::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $date=self::faker()->dateTimeBetween('-2 months','+1 months');
        $budget=self::faker()->randomFloat(nbMaxDecimals:2,min:0, max:100000);
        return [
            'title' => self::faker()->unique()->text(40),
            'contract_number' => self::faker()->text(15),
            'location' => self::faker()->address(),
            'description' => self::faker()->text(225),
            'min_budget' => $budget,
            'max_budget' => $budget+1000,
            'duration' => self::faker()->numberBetween(0, 4),
            'submissionDate' => \DateTimeImmutable::createFromMutable($date->modify('+5 days')),
            'negociationDate' => \DateTimeImmutable::createFromMutable($date->modify('+10 days')),
            'responseDate' => \DateTimeImmutable::createFromMutable($date->modify('+12 days')),
            'attributionDate' => \DateTimeImmutable::createFromMutable($date->modify('+15 days')),
            'start_date' => \DateTimeImmutable::createFromMutable($date->modify('+25 days')),
            'end_date' => \DateTimeImmutable::createFromMutable($date->modify('+30 days')),
            'status' => self::faker()->numberBetween(0, 4),
            'tender_type' => self::faker()->numberBetween(0, 1),
            'isArchived'=>false,
            'url'=>self::faker()->url(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Tender $tender): void {})
        ;
    }
}
