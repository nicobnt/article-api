<?php

namespace App\Controller;

use App\Entity\Article;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/api/article", name="article")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArticleController.php',
        ]);
    }

    /**
     * @Rest\Get(
     *      path="/api/articles/{id}",
     *      name = "article_show",
     *      requirements={"id"="\d+"}
     * )
     * @Rest\View()
     */
    public function show(Article $article)
    {
        return $article;
    }
}
