<?php

namespace App\Factory;

use App\Entity\Document;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

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
        $file_type=self::faker()->boolean();
        return [
            'filename' => $file_type?"Cahier de charge.pdf":"Cahier de charge.docs",
            'filepath' => $file_type?"THE-KEY-TO-GROWTH-67dbd8c49f13a.pdf":"Exo-python-2025-67e40af6bfe77.docx",
            'information' => self::faker()->text(),
            'name' => "Cahier de charge",
            'limitDate'=>self::faker()->optional(0.8)->dateTimeBetween(startDate:'-1 weeks',endDate:'+1 weeks'),
            'status' => self::faker()->numberBetween(0,2),
            'isArchived'=>false,
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
