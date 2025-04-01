<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }
    public function search(string $q): array
    {
        return $this->createQueryBuilder('c')
        ->where('c.email LIKE :term OR c.name LIKE :term OR c.organisation LIKE :term OR c.function LIKE :term')
        ->setParameter('term', '%' . $q . '%')
        ->orderBy('c.name', 'ASC')
        ->getQuery()
        ->getResult();
    }

}
