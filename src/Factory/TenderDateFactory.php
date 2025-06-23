<?php

namespace App\Factory;

use App\Entity\TenderDate;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<TenderDate>
 */
final class TenderDateFactory extends PersistentProxyObjectFactory
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
        return TenderDate::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $date=self::faker()->dateTimeBetween('-2 months','+1 months');
        return [
            'duration' => self::faker()->numberBetween(0, 4),
            'submissionDate' => \DateTimeImmutable::createFromMutable($date->modify('+5 days')),
            'negociationDate' => \DateTimeImmutable::createFromMutable($date->modify('+10 days')),
            'responseDate' => \DateTimeImmutable::createFromMutable($date->modify('+12 days')),
            'attributionDate' => \DateTimeImmutable::createFromMutable($date->modify('+15 days')),
            'start_date' => \DateTimeImmutable::createFromMutable($date->modify('+25 days')),
            'end_date' => \DateTimeImmutable::createFromMutable($date->modify('+30 days')),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(TenderDate $tenderDate): void {})
        ;
    }
}
