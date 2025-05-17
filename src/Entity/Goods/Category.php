<?php

namespace PinduoduoApiBundle\Entity\Goods;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '商品类目')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'ims_pdd_goods_category', options: ['comment' => '商品类目'])]
#[ORM\UniqueConstraint(name: 'ims_pdd_goods_category_idx_uniq', columns: ['name', 'parent_id'])]
class Category implements \Stringable
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Category $parent = null;

    /**
     * 下级分类列表.
     *
     * @var Collection<Category>
     */
    #[Groups(['restful_read', 'api_tree'])]
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Category::class)]
    private Collection $children;

    #[ORM\Column(length: 120, options: ['comment' => '分类名'])]
    private string $name;

    #[ORM\Column]
    private int $level;

    #[ORM\ManyToMany(targetEntity: Spec::class, mappedBy: 'categories', fetch: 'EXTRA_LAZY')]
    private Collection $specs;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Goods::class)]
    private Collection $goodsList;

    /**
     * @var array|null 其实就是 类目商品发布规则查询接口 的结果，我们缓存 cat_rule_get_response 起来
     * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.cat.rule.get
     */
    #[ORM\Column(nullable: true, options: ['comment' => '分类名'])]
    private ?array $catRule = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function __construct()
    {
        $this->specs = new ArrayCollection();
        $this->goodsList = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, Spec>
     */
    public function getSpecs(): Collection
    {
        return $this->specs;
    }

    public function addSpec(Spec $spec): static
    {
        if (!$this->specs->contains($spec)) {
            $this->specs->add($spec);
            $spec->addCategory($this);
        }

        return $this;
    }

    public function removeSpec(Spec $spec): static
    {
        if ($this->specs->removeElement($spec)) {
            $spec->removeCategory($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getName()} #{$this->getId()}";
    }

    /**
     * @return Collection<int, Goods>
     */
    public function getGoodsList(): Collection
    {
        return $this->goodsList;
    }

    public function addGoodsList(Goods $goodsList): static
    {
        if (!$this->goodsList->contains($goodsList)) {
            $this->goodsList->add($goodsList);
            $goodsList->setCategory($this);
        }

        return $this;
    }

    public function removeGoodsList(Goods $goodsList): static
    {
        if ($this->goodsList->removeElement($goodsList)) {
            // set the owning side to null (unless already changed)
            if ($goodsList->getCategory() === $this) {
                $goodsList->setCategory(null);
            }
        }

        return $this;
    }

    public function getCatRule(): ?array
    {
        return $this->catRule;
    }

    public function setCatRule(?array $catRule): static
    {
        $this->catRule = $catRule;

        return $this;
    }
}
