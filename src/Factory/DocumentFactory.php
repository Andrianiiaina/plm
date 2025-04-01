<?php

namespace App\Factory;

use App\Entity\Document;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Document>
 */
final class DocumentFactory extends PersistentProxyObjectFactory
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
        return Document::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'filename' => "WHAT-IS-CLOUD-COMPUTING.pdf",
            'filepath' => "WHAT-IS-CLOUD-COMPUTING-67c83f1ee3599.pdf",
            'information' => self::faker()->text(),
            'name' => "Cahier de charge",
            'status' => self::faker()->numberBetween(0,4),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Document $document): void {})
        ;
    }
}
