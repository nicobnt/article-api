<?php


    namespace App\Controller;

    use App\Entity\User;
    use App\Service\Weather;
    use FOS\RestBundle\Controller\AbstractFOSRestController;
    use FOS\RestBundle\Controller\Annotations as Rest;
    use Symfony\Component\HttpFoundation\Response;

    class WeatherController extends AbstractFOSRestController
    {

        /**
         * @Rest\Get(
         *      path="/api/weather",
         *      name = "api_weather",
         * )
         * @Rest\View()
         */
        public function getWeatherAction(Weather $weather)
        {
            return $weather->getCurrent();
        }

        /**
         * @Rest\Get(
         *      path="/api/users",
         *      name = "api_user",
         * )
         * @Rest\View()
         */
        public function createUser()
        {
            $user = new User();
            $user->setRoles(["ROLE_API", "ROLE_USER"]);
            $user->setApiToken("token_test");
            $user->setEmail("api_user@gmail.com");
            $user->setPassword("mdp");

            $em =  $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->view(
                $user,
                Response::HTTP_CREATED
            );
        }
    }
