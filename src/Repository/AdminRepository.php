<?php

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Admin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admin[]    findAll()
 * @method Admin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminRepository extends ServiceEntityRepository
{
    /**
     * UserPasswordEncoder
     *
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoder $encoder
     */
    private $encoder;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(
        RegistryInterface $registry,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em
    ) {
        $this->encoder = $encoder;
        $this->em = $em;
        parent::__construct($registry, Admin::class);
    }

    public function create($username, $password)
    {
        $admin = new Admin();
        $admin->setUsername($username);
        $admin->setPassword($this->encoder->encodePassword($admin, $password));
        $this->getEntityManager()->persist($admin);
        $this->getEntityManager()->flush();

        return $admin;
    }

    public function checkPassword($username, $password)
    {
        $admin = $this->findOneBy(['username' => $username]);

        return $this->encoder->isPasswordValid($admin, $password);
    }

    public function remove($admin)
    {
        $admin = $this->findOneBy(['username' => $admin]);
        $this->em->remove($admin);
        $this->em->flush();
    }

    // /**
    //  * @return Admin[] Returns an array of Admin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Admin
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
