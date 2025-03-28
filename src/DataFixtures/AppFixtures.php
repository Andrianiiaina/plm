<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\CalendarFactory;
use App\Factory\ContactFactory;
use App\Factory\ContactGroupFactory;
use App\Factory\DocumentFactory;
use App\Factory\FileFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\TenderFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
    
    $admin = new User();
    $admin->setEmail("admin@gmail.com");
    $admin->setRoles(['ROLE_ADMIN']);
    $password = 'admin'; 
    $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
    $manager->persist($admin);

    $respo = new User();
    $respo->setEmail("respo@gmail.com");
    $respo->setRoles(['ROLE_RESPO']);
    $password = 'admin'; 
    $respo->setPassword($this->passwordHasher->hashPassword($respo, $password));
    $manager->persist($respo);
    $this->addReference('user_1', $respo);


    $respo1 = new User();
    $respo1->setEmail("respo1@gmail.com");
    $respo1->setRoles(['ROLE_RESPO']);
    $password = 'admin'; 
    $respo1->setPassword($this->passwordHasher->hashPassword($respo1, $password));
    $manager->persist($respo1);
    $this->addReference('user_2', $respo1);

    $manager->flush();

    ContactFactory::createMany(5);
    ContactGroupFactory::createMany(3,function() { 
           return ['contacts' => ContactFactory::createMany(4)]; 
    });

    TenderFactory::createMany(15,['responsable' => $this->getReference('user_1',User::class)]);
    TenderFactory::createMany(8,['responsable' => $this->getReference('user_2',User::class)]);


    DocumentFactory::createMany(50,function() { 
        $user1 = $this->getReference('user_1',User::class);
        return ['responsable'=>$user1,'tender' => TenderFactory::random()]; 
    });
    DocumentFactory::createMany(50,function() { 
        $user2 = $this->getReference('user_2',User::class);
        return ['responsable'=>$user2,'tender' => TenderFactory::random()]; 
    });


    FileFactory::createMany(100,function() { 
        return ['tender'=>TenderFactory::random()];
    });
    CalendarFactory::createMany(30,function(){
        return ['tender'=>TenderFactory::random()];
    });

    $manager->flush();
    }
}
