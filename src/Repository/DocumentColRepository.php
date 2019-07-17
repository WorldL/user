<?php

namespace App\Repository;

use App\Entity\DocumentCol;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query;
use App\Entity\College;
use JMS\Serializer\SerializerInterface;
use App\Entity\DocumentAuthor;

/**
 * @method DocumentCol|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentCol|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentCol[]    findAll()
 * @method DocumentCol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentColRepository extends ServiceEntityRepository
{
    private $collegeRepo;
    private $documentAuthorRepo;
    private $serializer;
    public function __construct(RegistryInterface $registry, SerializerInterface $serializer)
    {
        parent::__construct($registry, DocumentCol::class);
        $this->collegeRepo = $this->_em->getRepository(College::class);
        $this->documentAuthorRepo = $this->_em->getRepository(DocumentAuthor::class);
        $this->serializer = $serializer;
    }

    public function col($userId, $docId)
    {
        $m = $this->findOneBy([
            'user_id' => $userId,
            'doc_id' => $docId,
            'deletedAt' => null,
        ]);
        if (!empty($m)) {
            return;
        }
        $m = (new DocumentCol())
            ->setUserId($userId)
            ->setDocId($docId);
        $this->_em->persist($m);
        $this->_em->flush();
        return;
    }

    public function unCol($userId, $docId)
    {
        $m = $this->findOneBy([
            'user_id' => $userId,
            'doc_id' => $docId,
            'deletedAt' => null,
        ]);
        if (empty($m)) {
            return;
        }
        $m->setDeletedAt(new \DateTime());
        $this->_em->merge($m);
        $this->_em->flush();
        return;
    }
    
    public function colStatus($userId, $docId)
    {
        $m = $this->findOneBy([
            'user_id' => $userId,
            'doc_id' => $docId,
            'deletedAt' => null,
        ]);

        return empty($m) ? 'no' : 'yes';
    }

    public function colList($userId, $page = 1, $pagesize = 10)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('d.id,d.college_id,d.author_id,d.education,d.major_cn,d.major_en,d.doc_type')
            ->from('App:DocumentCol', 'dc')
            ->innerJoin('App:Document', 'd', Join::WITH, 'dc.doc_id = d.id')
            ->where('dc.user_id = :user_id AND dc.deletedAt is null')
            ->orderBy('dc.id', 'DESC')
            ;
        
        $document = $query
            ->setFirstResult(($page - 1) * $pagesize)
            ->setMaxResults($pagesize)
            ->setParameters(['user_id' => $userId])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);


        $list = [];

        foreach ($document as $value) {
            //文书种类
            $docType = DocumentRepository::$docTypeList[$value['doc_type']];
            //学校名
            $college = $this->collegeRepo->findOneBy(['id' => $value['college_id']]);
            $college = $this->serializer->toArray($college);

            $nameEn = str_replace(' ', '', $college['name_en']);

            $crest = 'http://cdn.xiaohailang.net/common/crest/'.$nameEn.'.png?x-oss-process=style/thumbnail';

            $college_name = [
                'name_cn' => $college['name_cn'],
                'name_en' => $college['name_en'],
                'schoolBadge' => $crest
            ];
            //文书专业
            $major = $value['major_cn'];
            //文书介绍
            $education = DocumentRepository::$docEducationList[$value['education']];
            //哪一届
            $session = $this->documentAuthorRepo->findOneBy(['author_id' => $value['author_id']]);

            $session = $this->serializer->toArray($session);
            $nickname = $session['nickname'];
            $session = explode(';', $session['session']);
            //            var_dump(rtrim($session[1]));
            $session = '20' . rtrim($session[1]) . '届';
            $session = $education . '-' . $session;

            $colStatus = empty($userId) ? 'no' : $this->colStatus($userId, $value['id']);

            $tmp = [
                'id' => $value['id'],
                'doc_type' => $docType,
                'college_name' => $college_name,
                'major' => $major,
                'session' => $session,
                'nickname' => $nickname,
                'pv' => rand(100, 5000),
                'authorPhoto' => 'http://cdn.xiaohailang.net/common/document_author/' . $nickname . '.jpeg',
                'col_status' => $colStatus,
            ];
            $list[] = $tmp;
        }

        return $list;
    }

    // /**
    //  * @return DocumentCol[] Returns an array of DocumentCol objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentCol
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
