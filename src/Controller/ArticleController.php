<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Type\ArticleType;
use App\Representation\Articles;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function create(Article $article, ValidatorInterface $validator) : View
    {
        $errors = $validator->validate($article);

        if(count($errors)){
            return $this->view(
                $errors,
                Response::HTTP_BAD_REQUEST
            );
        }

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
     *      path="/api/articles_fos",
     *      name="api_article_create_fos"
     * )
     * @Rest\View(statusCode="201")
     * @ParamConverter(
     *     "article",
     *     converter="fos_rest.request_body",
     *     options={
     *          "validator" = {"groups"="Create"}
     *     }
     * )
     *
     */
    public function createFOS(Article $article, ConstraintViolationList $validationErrors) : View
    {
        if(count($validationErrors) > 0){
            return $this->view(
                $validationErrors,
                Response::HTTP_BAD_REQUEST
            );
        }

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
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]+",
     *     default=null,
     *     nullable=true,
     *     description="Search query to look for article"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="5",
     *     description="Max item per page"
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The paginator offset"
     * )
     * @Rest\View(StatusCode = 200)
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $rm = $this->getDoctrine()->getRepository(Article::class);
        $pager = $rm->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Articles($pager);
    }
}
