<?php

namespace App\Repository;

use App\Entity\UploadTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UploadTable>
 *
 * @method UploadTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadTable[]    findAll()
 * @method UploadTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadTable::class);
    }

    public function save(UploadTable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UploadTable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
