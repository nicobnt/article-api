<?php

    namespace App\Service;

    use JMS\Serializer\SerializerInterface;
    use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
    use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
    use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
    use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
    use Symfony\Contracts\HttpClient\HttpClientInterface;

    class Weather
    {
        private $weatherClient;
        private $serializer;
        private $apiKey;

        public function __construct(HttpClientInterface $weatherClient, SerializerInterface $serializer)
        {
            $this->weatherClient = $weatherClient;
            $this->serializer = $serializer;
            $this->apiKey = '04f3ecaa61866ac151eee1a4af5b22c1';
        }

        public function getCurrent()
        {
            $uri = '/data/2.5/weather?q=Paris&APPID='.$this->apiKey;

            try{
                $response = $this->weatherClient->request('GET', $uri);
                $content = $response->getContent();
            }catch (TransportExceptionInterface $e){
                return ['error' => 'Erreur lors de la récupération du weather. (Transport)'];
            }catch (ClientExceptionInterface $e){
                return ['error' => 'Erreur lors de la récupération du weather. (Client)'];
            }catch (RedirectionExceptionInterface $e){
                return ['error' => 'Erreur lors de la récupération du weather. (Redirection)'];
            }catch (ServerExceptionInterface $e){
                return ['error' => 'Erreur lors de la récupération du weather. (Server)'];
            }

            $data = $this->serializer->deserialize($content, 'array', 'json');

            return [
                'city' => $data['name'],
                'description' => $data['weather'][0]['main']
            ];
        }
    }