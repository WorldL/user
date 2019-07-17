<?php

namespace App\Repository;

use App\Entity\College;
use App\Entity\Document;
use App\Entity\DocumentAuthor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use App\Entity\DocumentCol;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{

    //文书种类列表
    public static $docTypeList = [
        'Personal Statement' => '个人陈述',
        'Supplemental Essay' => '补充文章'
    ];
    //文书学位列表
    public static $docEducationList = [
        'und' => '学士'
    ];

    //专业顺序权重
//    private $documentMajor = [
//        '理科'=>0,
//        '商科'=>0,
//        '生命'=>
//    ];
    private $em;
    //college 表
    private $collegeRepo;
    //document_author 表
    private $documentAuthorRepo;
    private $documentColRepo;
    private $serializer;
    public function __construct(
        RegistryInterface $registry,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        parent::__construct($registry, Document::class);
        $this->em = $entityManager;
        $this->collegeRepo = $entityManager->getRepository(College::class);
        $this->documentColRepo = $entityManager->getRepository(DocumentCol::class);
        $this->serializer = $serializer;
        $this->documentAuthorRepo = $entityManager->getRepository(DocumentAuthor::class);
    }


    //文书详情
    public function details($id, $userId)
    {
        $doc = $this->em->createQueryBuilder()
            ->select('d.college_id,d.author_id,d.doc_type,d.doc_word_num,d.doc_content')
            ->from('App:Document', 'd')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        //文书作者信息
        $author = $this->documentAuthorRepo->findBy(['author_id' => $doc[0]['author_id']]);
        $author = $this->serializer->toArray($author);
        //录取学校
        $school = [];
        foreach ($author as $value) {
            //录取学校
            $school[] = $value['name_en'];
            //昵称
            $nickname = $value['nickname'];
            //哪一届学生
            $session = $value['session'];
            //专业
            $major = $value['major'];
        }

        //文书种类
        $docType = $doc[0]['doc_type'];
        //文书单词数
        $docWordNum = $doc[0]['doc_word_num'];
        //文书种类与文书单词数量拼接
        $docTypeAndWordNum = $docType . '(' . $docWordNum . ')';
        //目标学校
        $targetSchool = $this->collegeRepo->findOneBy(['id' => $doc[0]['college_id']]);
        $targetSchool = $this->serializer->toArray($targetSchool);

        $targetSchool = $targetSchool['name_en'];

        //目标专业
        $targetMajor = $major;
        //文书内容
        $docContent = $doc[0]['doc_content'];

        $colStatus = $this->documentColRepo->colStatus($userId, $id);

        $docDetails = [
            'authorPhoto' => 'http://cdn.xiaohailang.net/common/document_author/' . $nickname . '.jpeg',
            'nickname' => $nickname,
            'session' => $session,
            'major' => $major,
            'school' => $school,
            'docType' => $docTypeAndWordNum,
            'targetSchool' => $targetSchool,
            'targetMajor' => $targetMajor,
            'doc' => $docContent,
            'col_status' => $colStatus,
        ];

        return $docDetails;
    }

    //文书列表
    public function list($collegeId, $education, $majorEn, $docType, $page, $pageSize, $userId)
    {

        $document = $this->em->createQueryBuilder()
            ->select('d.id,d.college_id,d.author_id,d.education,d.major_cn,d.major_en,d.doc_type')
            ->from('App:Document', 'd');

        //添加学校筛选条件
        if (!empty($collegeId)) {
            $document->andwhere('d.college_id = :college_id')
                ->setParameter('college_id', $collegeId);
        }

        //添加学位筛选条件
        if (!empty($education)) {
            $document->andWhere('d.education = :education')
                ->setParameter('education', $education);
        }
        //添加专业筛选条件
        if (!empty($majorEn)) {
            $document->andWhere('d.major_en = :major_en')
                ->setParameter('major_en', $majorEn);
        }

        //添加文书类型筛选条件
        if (!empty($docType)) {
            $document->andWhere('d.doc_type = :doc_type')
                ->setParameter('doc_type', $docType);
        }

        //查询出的文书总数
        $totalDoc = count($document->getQuery()->getResult(Query::HYDRATE_ARRAY));
        //文书分页
        $document = $document
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);


        $documentList = [];
        $documentList['total_doc'] = $totalDoc;
        foreach ($document as $key => $value) {
            //文书种类
            $doc_type = self::$docTypeList[$value['doc_type']];
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
            $education = self::$docEducationList[$value['education']];
            //哪一届
            $session = $this->documentAuthorRepo->findOneBy(['author_id' => $value['author_id']]);

            $session = $this->serializer->toArray($session);
            $nickname = $session['nickname'];
            $session = explode(';', $session['session']);
            //            var_dump(rtrim($session[1]));
            $session = '20' . rtrim($session[1]) . '届';
            $session = $education . '-' . $session;

            $colStatus = empty($userId) ? 'no' : $this->documentColRepo->colStatus($userId, $value['id']);

            $tmp = [
                'id' => $value['id'],
                'doc_type' => $doc_type,
                'college_name' => $college_name,
                'major' => $major,
                'session' => $session,
                'nickname' => $nickname,
                'pv' => rand(100, 5000),
                'authorPhoto' => 'http://cdn.xiaohailang.net/common/document_author/' . $nickname . '.jpeg',
                'col_status' => $colStatus,
            ];
            $documentList['doc'][] = $tmp;
        }


        return $documentList;
    }


    //筛选条件 学校列表
    public function docCollegeList($majorEn)
    {
        $collegeId = $this->em->createQueryBuilder()
            ->select('d.college_id')
            ->from('App:Document', 'd');

        if (!empty($majorEn)) {
            $collegeId->andWhere('d.major_en=:majorEn')
                ->setParameter('majorEn', $majorEn);
        }

        $collegeId = $collegeId
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
        $collegeIdList = [];
        foreach ($collegeId as $value) {
            $collegeIdList[] = $value['college_id'];
        }


        return $collegeIdList;
    }

    //筛选条件 专业列表
    public function docMajorList($collegeId)
    {
        //专业列表分类
        $majorCategory = $this->em->createQueryBuilder()
            ->select('d.category')
            ->from('App:Document', 'd');

        if (!empty($collegeId)) {
            $majorCategory->where('d.college_id =:college_id')
                ->setParameter('college_id', $collegeId);
        }

        $majorCategory = $majorCategory
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
        $categoryList = [];
        foreach ($majorCategory as $value) {
            $categoryList[] = $value['category'];
        }
        $categoryList = array_unique($categoryList);

        $tmp = [];
        foreach ($categoryList as $value) {
            $categoryDetails  = $this->em->createQueryBuilder()
                ->select('d.major_en,d.major_cn')
                ->from('App:Document', 'd')
                ->where('d.category=:category')
                ->setParameter('category', $value);

            if (!empty($collegeId)) {
                $categoryDetails->andwhere('d.college_id =:college_id')
                    ->setParameter('college_id', $collegeId);
            }

            $categoryDetails = $categoryDetails
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
            $categoryDetails = array_unique($categoryDetails, SORT_REGULAR);
            $tmp[$value] = $categoryDetails;
        }
        $majorCategoryList = [];
        foreach ($tmp as $key => $value) {
            foreach ($value as $v) {
                $majorCategoryList[$key][] = [
                    'major_cn' => $v['major_cn'],
                    'major_en' => $v['major_en']
                ];
            }
        }


        //改变专业列表中 '其他' 元素所在的位置
        foreach ($majorCategoryList as $key=>$value){
           if($key == '其他'){

               $change['其他'] = $value;
               unset($majorCategoryList[$key]);

               $majorCategoryList = array_merge($majorCategoryList,$change);

           }
        }

        return $majorCategoryList;
    }



    // /**
    //  * @return Document[] Returns an array of Document objects
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
    public function findOneBySomeField($value): ?Document
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
