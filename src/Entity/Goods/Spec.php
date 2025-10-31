<?php

namespace PinduoduoApiBundle\Entity\Goods;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PinduoduoApiBundle\Repository\Goods\SpecRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @see https://open.pinduoduo.com/application/document/api?id=pdd.goods.spec.get
 */
#[ORM\Entity(repositoryClass: SpecRepository::class)]
#[ORM\Table(name: 'ims_pdd_spec', options: ['comment' => '商品属性类目'])]
class Spec implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, options: ['comment' => '规格名称'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'specs', fetch: 'EXTRA_LAZY')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId() ?? '';
    }
}
