<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Category;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank()]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]

    private ?string $description = null;

    #[ORM\Column(length: 255, unique: true)]
    // #[Assert\NotBlank()]

    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $illustration = '';

    #[ORM\Column(length: 255)]
    private ?string $video = '';

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: CommentTrick::class)]
    private Collection $commentTricks;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Illustration::class)]
    private Collection $illustrations;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Video::class)]
    private Collection $videos;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'trick')]
    private Collection $categories;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    
    public function __construct()
    {
        $this->commentTricks = new ArrayCollection();
        $this->illustrations = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->modified_at = new \DateTimeImmutable();
        // $this->user = new User();
    }

    // méthode pour update date d'un trick lors d'une modification 
    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->modified_at = new \DateTimeImmutable();
    }

    // convertir tableau en chaine de caractères 
    public function __toString()
    {
        return $this->name;
        // return $this->illustration;
        // return $this->author;
        // return $this->description;
        // return $this->slug;
    }
  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(string $illustration): self
    {
        $this->illustration = $illustration;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modified_at;
    }

    public function setModifiedAt(\DateTimeImmutable $modified_at): self
    {
        $this->modified_at = $modified_at;

        return $this;
    }

    /**
     * @return Collection<int, CommentTrick>
     */
    public function getCommentTricks(): Collection
    {
        return $this->commentTricks;
    }

    public function addCommentTrick(CommentTrick $commentTrick): self
    {
        if (!$this->commentTricks->contains($commentTrick)) {
            $this->commentTricks->add($commentTrick);
            $commentTrick->setTrick($this);
        }

        return $this;
    }

    public function removeCommentTrick(CommentTrick $commentTrick): self
    {
        if ($this->commentTricks->removeElement($commentTrick)) {
            // set the owning side to null (unless already changed)
            if ($commentTrick->getTrick() === $this) {
                $commentTrick->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Illustration>
     */
    public function getIllustrations(): Collection
    {
        return $this->illustrations;
    }

    public function addIllustration(Illustration $illustration): self
    {
        if (!$this->illustrations->contains($illustration)) {
            $this->illustrations->add($illustration);
            $illustration->setTrick($this);
        }

        return $this;
    }

    public function removeIllustration(Illustration $illustration): self
    {
        if ($this->illustrations->removeElement($illustration)) {
            // set the owning side to null (unless already changed)
            if ($illustration->getTrick() === $this) {
                $illustration->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setTrick($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->categories;
    }

    // public function setCategories(Collection $categories): Collection
    // {
    //     $this->categories = $categories;
    //     return $this;
    // }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addTrick($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeTrick($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    // public function addUser(User $username): self
    // {
    //     if (!$this->user->contains($username)) {
    //         $this->user->add($username);
    //         $username->addTrick($this);
    //     }

    //     return $this;
    // }
}
