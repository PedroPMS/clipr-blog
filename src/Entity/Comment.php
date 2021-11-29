<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{
    use Timestamps;

    /**
     * @Groups({"comment"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"comment"})
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @Groups({"post_detail"})
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    /**
     * @Groups({"comment"})
     * @ORM\Column(type="text")
     */
    private $userId;

    /**
     * @Groups({"comment"})
     * @ORM\Column(type="text")
     */
    private $postId;

    /**
     * @Groups({"user_detail"})
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        $user = $this->getUser();

        return $user ? $user->getId() : null;
    }

    public function getPostId(): ?int
    {
        return $this->getPost()->getId();
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

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
}
