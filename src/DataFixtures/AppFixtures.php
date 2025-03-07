<?php

namespace App\DataFixtures;

use App\Entity\ProjectStatus;
use App\Entity\TaskStatus;
use App\Factory\CalendarFactory;
use App\Factory\ContactFactory;
use App\Factory\ContactGroupFactory;
use App\Factory\DocumentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\TenderFactory;
use App\Factory\UserFactory;
use App\Factory\FileFactory;
use App\Service\ListService;

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

    FileFactory::createMany(10, function() { 
        return ['tender' => TenderFactory::random()]; 
    });
    DocumentFactory::createMany(20,function() { 
        return ['responsable'=>UserFactory::random(), 'tender' => TenderFactory::random()]; 
    });
    CalendarFactory::createMany(15,function(){
        return ['tender'=>TenderFactory::random()];
    });

    $rate=[0 =>0, 1=>20, 2=>80, 3=>100, 4=>0,5=>0];

    foreach (ListService::$task_status as $key=>$value) {
        $taskStatus = new TaskStatus();
        $taskStatus->setCode($value);
        $taskStatus->setLabel($key);
        $manager->persist($taskStatus);
    }
    foreach (ListService::$project_status as $key=>$value) {
        $projectStatus = new ProjectStatus();
        $projectStatus->setCode($value);
        $projectStatus->setLabel($key);
        $manager->persist($projectStatus);
    }

    $manager->flush();

        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
    }
}
