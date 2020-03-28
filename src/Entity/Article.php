<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table()
 *
 * @ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "get",
 *      href = @Hateoas\Route(
 *          "api_article_get",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *      )
 * )
 *
 * @Hateoas\Relation(
 *      "cget",
 *      href = @Hateoas\Route(
 *          "api_article_list",
 *          absolute=true
 *      )
 * )
 *
 * @Hateoas\Relation(
 *      "put",
 *      href = @Hateoas\Route(
 *          "api_article_update",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *      )
 * )
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "api_article_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *      )
 * )
 *
 * @Hateoas\Relation(
 *      "post",
 *      href = @Hateoas\Route(
 *          "api_article_create",
 *          absolute=true
 *      )
 * )
 *
 * @Hateoas\Relation(
 *     "author",
 *     embedded = @Hateoas\Embedded("expr(object.getAuthor())"),
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.getAuthor() === null)")
 * )
 *
 * @Hateoas\Relation(
 *     "weather",
 *     embedded = @Hateoas\Embedded("expr(service('app.weather').getCurrent())")
 * )
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"Create"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(groups={"Create"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="articles", cascade={"persist"})
     *
     */
    private $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Since("2.0")
     */
    private $shortDescription;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }
}
