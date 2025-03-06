<?php

namespace App\DataFixtures;

use App\Factory\ContactFactory;
use App\Factory\ContactGroupFactory;
use App\Factory\DocumentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\TenderFactory;
use App\Factory\UserFactory;
use App\Factory\FileFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       UserFactory::createMany(5);

       ContactFactory::createMany(5);
       ContactGroupFactory::createMany(5,function() { 
           return ['contacts' => ContactFactory::createMany(4)]; 
       });

    TenderFactory::createMany(15,function() { 
        return ['responsable_id' => UserFactory::random()]; 
    });

    FileFactory::createMany(10, function() { 
        return ['tender_id' => TenderFactory::random()]; 
    });
    DocumentFactory::createMany(20,function() { 
        return ['responsable'=>UserFactory::random(), 'tender' => TenderFactory::random()]; 
    });
        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
    }
}
