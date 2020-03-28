<?php


    namespace App\Controller;

    use App\Service\Weather;
    use FOS\RestBundle\Controller\AbstractFOSRestController;
    use FOS\RestBundle\Controller\Annotations as Rest;

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
    }
