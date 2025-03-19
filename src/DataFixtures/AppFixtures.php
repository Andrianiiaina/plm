<?php

namespace App\DataFixtures;

use App\Factory\CalendarFactory;
use App\Factory\ContactFactory;
use App\Factory\ContactGroupFactory;
use App\Factory\DocumentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\TenderFactory;
use App\Factory\UserFactory;

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
        return ['responsable' => UserFactory::random()]; 
    });

    DocumentFactory::createMany(20,function() { 
        return ['responsable'=>UserFactory::random(), 'tender' => TenderFactory::random()]; 
    });
    CalendarFactory::createMany(15,function(){
        return ['tender'=>TenderFactory::random()];
    });

    $rate=[0 =>0, 1=>20, 2=>80, 3=>100, 4=>0,5=>0];

    $manager->flush();

        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
    }
}
