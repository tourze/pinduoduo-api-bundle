<?php

namespace PinduoduoApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PinduoduoApiBundle\Entity\LogisticsTemplate;
use PinduoduoApiBundle\Entity\Mall;
use PinduoduoApiBundle\Enum\CostType;
use PinduoduoApiBundle\Repository\MallRepository;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class LogisticsTemplateFixtures extends Fixture
{
    private MallRepository $mallRepository;

    public function __construct(MallRepository $mallRepository)
    {
        $this->mallRepository = $mallRepository;
    }

    public function load(ObjectManager $manager): void
    {
        // 获取Mall测试数据
        $mall = $this->mallRepository->findOneBy([]);

        if (!$mall) {
            // 如果没有Mall数据，创建一个默认的
            $mall = new Mall();
            $mall->setName('Default Test Mall');
            $mall->setDescription('Default mall for testing');
            $manager->persist($mall);
            $manager->flush();
        }

        // 创建测试用的物流模板数据
        $template1 = new LogisticsTemplate();
        $template1->setMall($mall);
        $template1->setName('标准快递模板');
        $template1->setCostType(CostType::ByWeight);
        $manager->persist($template1);

        $template2 = new LogisticsTemplate();
        $template2->setMall($mall);
        $template2->setName('EMS包邮模板');
        $template2->setCostType(CostType::ByAmount);
        $manager->persist($template2);

        $template3 = new LogisticsTemplate();
        $template3->setMall($mall);
        $template3->setName('江浙沪沪模板');
        $template3->setCostType(CostType::ByWeight);
        $manager->persist($template3);

        $manager->flush();
    }
}
