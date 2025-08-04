<?php

namespace App\Tests\Repository;

use App\Entity\Tender;
use App\Entity\User;
use App\Repository\TenderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TenderRepositoryTest extends KernelTestCase
{
 
    private TenderRepository $tenderRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->tenderRepository = static::getContainer()->get(TenderRepository::class);
    }


    public function testfindTendersByRespoReturnsEmptyArrayForUserWithoutTenders(): void
    {
        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->find(1); // Un user sans tenders

        $this->assertNotNull($user, "L'utilisateur de test n'a pas été trouvé en base.");

        $tenders = $this->tenderRepository->findTendersByRespo($user, false);

        $this->assertIsArray($tenders, "La méthode findTendersByRespo doit retourner un tableau.");
        $this->assertEmpty($tenders, "La méthode findTendersByRespo devrait retourner un tableau vide si aucun tender n'est associé à cet utilisateur.");
    }
    
    public function testfindTendersByRespo(): void
    {
        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->find(2);  
        $this->assertNotNull($user, "L'utilisateur de test n'a pas été trouvé en base.");
        $tenders = $this->tenderRepository->findTendersByRespo($user, false);  
        $this->assertIsArray($tenders, "La méthode findTendersByRespo doit retourner un tableau.");
    
        // Vérifier que le tableau n'est pas vide (si on s'attend à avoir des résultats)
        $this->assertNotEmpty($tenders, "Aucun tender trouvé pour cet utilisateur.");
    
        // Assurer que chaque tender est bien associé à l'utilisateur testé
        foreach ($tenders as $tender) {
            $this->assertInstanceOf(Tender::class, $tender, "L'élément retourné n'est pas une instance de Tender.");
            $this->assertEquals($user->getId(), $tender->getResponsable()->getId(), "Le tender retourné ne correspond pas à l'utilisateur demandé.");
        }
    }
    
  


}
