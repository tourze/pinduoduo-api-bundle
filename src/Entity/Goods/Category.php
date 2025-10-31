<?php

namespace PinduoduoApiBundle\Entity\Goods;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Goods\CategoryRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'ims_pdd_goods_category', options: ['comment' => '商品类目'])]
#[ORM\UniqueConstraint(name: 'ims_pdd_goods_category_idx_uniq', columns: ['name', 'parent_id'])]
class Category implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Category $parent = null;

    /**
     * 下级分类列表.
     *
     * @var Collection<int, Category>
     */
    #[Groups(groups: ['restful_read', 'api_tree'])]
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Category::class)]
    private Collection $children;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, options: ['comment' => '分类名'])]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[ORM\Column(options: ['comment' => '分类级别'])]
    private int $level;

    /**
     * @var Collection<int, Spec>
     */
    #[ORM\ManyToMany(targetEntity: Spec::class, mappedBy: 'categories', fetch: 'EXTRA_LAZY')]
    private Collection $specs;

    /**
     * @var Collection<int, Goods>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Goods::class)]
    private Collection $goodsList;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '类目商品发布规则'])]
    private ?array $catRule = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->specs = new ArrayCollection();
        $this->goodsList = new ArrayCollection();
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
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
        if (null === $this->getId() || '' === $this->getId()) {
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

    /**
     * @return array<string, mixed>|null
     */
    public function getCatRule(): ?array
    {
        return $this->catRule;
    }

    /**
     * @param array<string, mixed>|null $catRule
     */
    public function setCatRule(?array $catRule): void
    {
        $this->catRule = $catRule;
    }
}
