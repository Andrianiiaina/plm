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
        $date=self::faker()->dateTime();
        return [
            'title' => self::faker()->unique()->sentence(),
            'contract_number' => self::faker()->text(15),
            'location' => self::faker()->address(),
            'description' => self::faker()->text(225),
            'min_budget' => self::faker()->randomFloat(),
            'start_date' => \DateTimeImmutable::createFromMutable($date),
            'end_date' => \DateTimeImmutable::createFromMutable($date->modify('+10 days')),
            'status' => self::faker()->numberBetween(0, 4),
            'tender_type' => self::faker()->numberBetween(0, 4),
            'responsable_id'=>UserFactory::new(),
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
