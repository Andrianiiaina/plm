<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\AllotissementFactory;
use App\Factory\CalendarFactory;
use App\Factory\ContactFactory;
use App\Factory\ContactGroupFactory;
use App\Factory\DocumentFactory;
use App\Factory\FileFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\TenderFactory;
use App\Factory\TenderDateFactory;

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
    $admin->setEmail("raymond@ritec.mg");
    $admin->setRoles(['ROLE_ADMIN']);
    $password = 'password'; 
    $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
    $manager->persist($admin);

    $respo = new User();
    $respo->setEmail("bid1@ritec.mg");
    $respo->setRoles(['ROLE_RESPO']);
    $password = 'password'; 
    $respo->setPassword($this->passwordHasher->hashPassword($respo, $password));
    $manager->persist($respo);
    $this->addReference('user_1', $respo);


    $respo1 = new User();
    $respo1->setEmail("bid2@ritec.mg");
    $respo1->setRoles(['ROLE_RESPO']);
    $password = 'password'; 
    $respo1->setPassword($this->passwordHasher->hashPassword($respo1, $password));
    $manager->persist($respo1);
    $this->addReference('user_2', $respo1);

    $manager->flush();


    TenderFactory::createMany(7,function(){
        return ['responsable' => $this->getReference('user_1',User::class),'contact'=>ContactFactory::createOne(),'tenderDate'=>TenderDateFactory::createOne()];
    });

    TenderFactory::createMany(3,function(){
        return ['responsable' => $this->getReference('user_2',User::class),'contact'=>ContactFactory::createOne(),'tenderDate'=>TenderDateFactory::createOne()];
    });
    
    DocumentFactory::createMany(20,function() { 
        $user1 = $this->getReference('user_1',User::class);
        return ['responsable'=>$user1,'tender' => TenderFactory::random()]; 
    });
    DocumentFactory::createMany(10,function() { 
        $user2 = $this->getReference('user_2',User::class);
        return ['responsable'=>$user2,'tender' => TenderFactory::random()]; 
    });


    FileFactory::createMany(100,function() { 
        return ['tender'=>TenderFactory::random()];
    });
    AllotissementFactory::createMany(50,function() { 
        return ['tender'=>TenderFactory::random()];
    });
    CalendarFactory::createMany(20,function(){
        return ['tender'=>TenderFactory::random()];
    });

    $manager->flush();
    }
}
