<?php

namespace App\Factory;

use App\Entity\Allotissement;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Allotissement>
 */
final class AllotissementFactory extends PersistentProxyObjectFactory
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
        return Allotissement::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $budget=self::faker()->randomFloat();
        return [
            'number' => self::faker()->numberBetween(0,10),
            'title' => self::faker()->unique()->text(40),
            'minBudget' => $budget,
            'maxBudget' => $budget+1000,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Allotissement $allotissement): void {})
        ;
    }
}
