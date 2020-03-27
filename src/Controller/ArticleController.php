<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Type\ArticleType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleController extends AbstractFOSRestController
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
     *      name = "api_article_show",
     *      requirements={"id"="\d+"}
     * )
     * @Rest\View()
     */
    public function show(Article $article)
    {
        return $article;
    }

    /**
     * @Rest\Post(
     *      path="/api/articles",
     *      name="api_article_create"
     * )
     * @Rest\View(statusCode="201")
     * @ParamConverter("article", converter="fos_rest.request_body")
     *
     */
    public function create(Article $article) : View
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return $this->view(
            $article,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'api_article_show',
                    ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL]
                ),
            ]
        );
    }

    /**
     * @Rest\Post(
     *    path = "/api/articles_form",
     *    name = "api_article_create_form"
     * )
     * @Rest\View(StatusCode = 201)
     */
    public function createAction(Request $request, SerializerInterface $serializer, FormFactoryInterface $formFactory) : View
    {
        $data = $serializer->deserialize($request->getContent(), 'array', 'json');
        $article = new Article;
        $form = $formFactory->create(ArticleType::class, $article);
        $form->submit($data);

        $em = $this->getDoctrine()->getManager();

        $em->persist($article);
        $em->flush();

        return $this->view(
            $article,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'api_article_show',
                    [
                        'id' => $article->getId(),
                    ]
                ),
            ]
        );
    }

    /**
     * @Rest\Get("/api/articles", name="api_article_list")
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="desc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="search",
     *     requirements="[a-zA-Z0-9]+",
     *     default=null,
     *     nullable=true,
     *     description="Search query to look for article"
     * )
     * @Rest\View(StatusCode = 200)
     */
    public function listAction($search, $order)
    {
        $rm = $this->getDoctrine()->getRepository(Article::class);
        $criteria = empty($search) ? [] : ["title" => '*'. $search. "*"];
        $articles = $rm->findBy($criteria, array('title' => $order));

        return $articles;

    }
}
