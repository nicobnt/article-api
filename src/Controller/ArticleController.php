<?php

namespace App\Controller;

use App\Entity\Article;
use App\Exception\RessourceValidationException;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     *      path="/api/articles/{id}",
     *      name = "api_article_get",
     *      requirements={"id"="\d+"}
     * )
     * @Rest\View()
     */
    public function getArticleAction(Article $article)
    {
        return $article;
    }

    /**
     * @Rest\Post(
     *      path="/api/articles_create",
     *      name="api_article_create_form"
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
                    'api_article_get',
                    ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL]
                ),
            ]
        );
    }

    /**
     * @Rest\Post(
     *      path="/api/articles",
     *      name="api_article_create"
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
     * @throws RessourceValidationException
     */
    public function postArticle(Article $article, ConstraintViolationList $validationErrors) : View
    {
        if(count($validationErrors) > 0){
            $message = 'This JSON sent contains invalid data :';
            foreach ($validationErrors as $validationError){
                $message .= sprintf(
                    "Field %s: %s",
                    $validationError->getPropertyPath(),
                    $validationError->getMessage()
                );
            }
            throw new RessourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return $this->view(
            $article,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'api_article_get',
                    ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL]
                ),
            ]
        );
    }

    /**
     * @Rest\Put(
     *     path="/api/articles/{id}",
     *     name="api_article_update",
     *     requirements={"id"="\d+"}
     * )
     *
     * @ParamConverter(
     *     "articleNew",
     *     converter="fos_rest.request_body",
     *     options={
     *          "validator" = {"groups"="Update"}
     *     }
     * )
     *
     * @Rest\View(statusCode="200")
     *
     * @throws RessourceValidationException
     */
    public function putArticleAction(Article $article, Article $articleNew, ConstraintViolationList $validationErrors) : View
    {
        dump($article);
        if(count($validationErrors) > 0){
            $message = 'This JSON sent contains invalid data :';
            foreach ($validationErrors as $validationError){
                $message .= sprintf(
                    "Field %s: %s",
                    $validationError->getPropertyPath(),
                    $validationError->getMessage()
                );
            }
            throw new RessourceValidationException($message);
        }

        $article->setTitle($articleNew->getTitle());
        $article->setContent($articleNew->getContent());
        $article->setAuthor($articleNew->getAuthor());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->view(
            $article,
            Response::HTTP_OK,
            [
                'Location' => $this->generateUrl(
                    'api_article_get',
                    ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL]
                ),
            ]
        );
    }

    /**
     * @Rest\Delete(
     *     path="/api/articles/{id}",
     *     name="api_article_delete",
     *     requirements={"id"="\d+"}
     * )
     *
     */
    public function deleteArticleAction(Article $article) : View
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
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
                    'api_article_get',
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
     *     default="2",
     *     description="Max item per page"
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="2",
     *     description="The paginator offset"
     * )
     * @Rest\View(StatusCode = 200)
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $rm = $this->getDoctrine()->getRepository(Article::class);
        $pager = $rm->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('page')
        );

        return new Articles($pager);
    }
}
