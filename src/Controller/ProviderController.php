<?php

namespace App\Controller;

use App\Entity\Developer;
use App\Entity\Provider;
use App\Entity\ProviderAPI;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProviderController extends AbstractController
{
    private $provider_data = [];
    private $working_time=45;

// you can add the new provider api_url and parameters into providers
//    it will add to database

    private $providers = [
        [
            "provider_name" => "Provider http://www.mocky.io/v2/5d47f24c330000623fa3ebfa",
            "url" => "http://www.mocky.io/v2/5d47f24c330000623fa3ebfa",
            "defines" => array("title" => "id", "time" => "sure", "difficulty" => "zorluk")
        ],

        [
            "provider_name" => "Provider https://run.mocky.io/v3/815f2d5c-a253-47bb-8819-9535f82baf34",
            "url" => "https://run.mocky.io/v3/815f2d5c-a253-47bb-8819-9535f82baf34",
            "defines" => array("title" => "title", "time" => "estimated_duration", "difficulty" => "level")
        ],


        //example provider informations
//        [
//        "provider_name" => "Provider 3",
//        "url" => "https://next.json-generator.com/api/json/get/VJHAErvhY?indent=2",
//        "defines" => array("title" => "work_title","time" => "work_time","difficulty" => "work_difficulty")
//        ]
    ];


    /**
     * @param $provider_url
     * @return mixed
     */

    public function curl($provider_url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $provider_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }


    //this function gets data of providers
    // from getApiData() function and inserts to database
    public function insertToProviders($data)
    {
        try {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($data as $rs) {

                $provider = new Provider();
                $provider->setDifficulty($rs["difficulty"]);
                $provider->setTitle($rs["title"]);
                $provider->setTime($rs["time"]);
                $provider->setName($rs["name"]);
                $provider->setProviderapi($rs["providerapi"]);
                $entityManager->persist($provider);
                $entityManager->flush();

            }
            $this->addFlash('success', "Providers added to database");
        } catch (\Exception $e) {
            $this->addFlash('error', $e);
        }
    }


    //get the providers data from api_url
    public function getApiData()
    {

        try {
            $providerApiRepository = $this->getDoctrine()->getRepository(ProviderAPI::class);
            foreach ($this->providers as $key => $provider) {
                $response = ($this->curl($provider["url"]));

                //if provider added query
                if ($providerApiRepository->findOneBy(["name" => $provider["provider_name"]])) {
                    $this->addFlash('error', $provider["provider_name"] . " also added to database");
                    continue;
                } else {
                    $entityManager = $this->getDoctrine()->getManager();
                    $new_provider=new ProviderAPI();
                    $new_provider->setName($provider["provider_name"]);
                    $new_provider->setLink($provider["url"]);
                    $new_provider->setFinishTime(0);
                    $entityManager->persist($new_provider);
                    $entityManager->flush();

                    foreach ($response as $key => $res) {

                        $difficulty = $provider["defines"]["difficulty"];
                        $time = $provider["defines"]["time"];
                        $title = $provider["defines"]["title"];

                        //get datas with specific keys that we need
                        $api_data = array(
                            "difficulty" => $res->$difficulty,
                            "title" => $res->$title,
                            "time" => $res->$time,
                            "name" => $provider["provider_name"],
                            "providerapi"=>$new_provider
                        );


                        array_push($this->provider_data, $api_data);
                    }
                }


            }

            //Call the flush function for insert providers to database
            if($this->provider_data){
                $this->insertToProviders($this->provider_data);
            }

        } catch (\Exception $e) {
            $this->addFlash('error', $e);
            return $this->render('hata.html.twig');
        }
    }


    public function isProvider(){
        $providerRepository = $this->getDoctrine()->getRepository(Provider::class);
        if($providerRepository->findAll()){
            return true;
        }
        else{
            return false;
        }
    }




    ///Calculate the Provider Time

    /**
     * @Route("/provider/{id}", name="provider_calc")
     */
    public function ProviderTime($id)
    {
        $providerrepository = $this->getDoctrine()->getRepository(Provider::class);
        $providerapirepository = $this->getDoctrine()->getRepository(ProviderAPI::class);
        $developerepository = $this->getDoctrine()->getRepository(Developer::class);

        $task_total = 0;
        $developer_total = 0;
        $providerapi=$providerapirepository->find($id);
        $providers = $providerrepository->findBy(["providerapi"=>$providerapi]);

        if (empty($providers)) {
            echo "false";
            exit();
        }

        $developers = $developerepository->findAll();
        foreach ($providers as$pr) {
            $task_total += $pr->getDifficulty()*$pr->getTime();
        }

        foreach ($developers as $developer) {
            $developer_total += $developer->getDifficulty()*$developer->getTime();
        }
        $week=ceil($task_total / $developer_total / $this->working_time);
//if the hour is spend 45 hour the week is added because it is the next week

        return new Response(
            $week
        );

    }



    /**
     * @Route("/provider", name="provider")
     */
    public function index(): Response
    {
        $this->getApiData();
        return $this->redirect('/');
    }



    //you can also add a provider from front end.
    /**
     * @Route("/api_add", name="api_add")
     */
    public function api_add(Request $request,ProviderRepository $providerRepository): Response
    {
        if($request){
            $new_provider=[

                        "provider_name" => "Provider ".$request->get("api_url"),
                        "url" => $request->get("api_url"),
                        "defines" => array("title" => $request->get("title"), "time" => $request->get("time"), "difficulty" => $request->get("difficulty"))
            ];
            array_push($this->providers,$new_provider);
            $this->getApiData();
            return $this->redirect('/');
        }
    }
}
