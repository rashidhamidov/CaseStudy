<?php

namespace App\Controller;

use App\Repository\DeveloperRepository;
use App\Repository\ProviderAPIRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{

    /**
     * @Route("/", name="developer")
     */
    public function index(DeveloperRepository $developerRepository, ProviderAPIRepository $providerAPIRepository): Response
    {
        $data = $developerRepository->findAll();
        $provider = $providerAPIRepository->findAll();
        return $this->render('developer/index.html.twig', [
            'controller_name' => 'DeveloperController',
            'data' => $data,
            'provider' => $provider,
        ]);
    }



    //get developers works hour with this function

    /**
     * @Route("/developer/{id}", name="getDeveloper",methods="GET")
     */
    public function getDevelopers($id, DeveloperRepository $developerRepository, ProviderRepository $providerRepository)
    {

        //get the developer workhor that spesific id
        $developer = $developerRepository->find($id);
        $developer = $providerRepository->findBy(['difficulty' => $developer->getDifficulty()]);


        if (empty($developer)) {
            $this->addFlash('error', "There is no any work hour for this developer");
            return $this->render('hata.html.twig', [
            ]);
        }

//create a works array and include it work array for week and hour
        $works = [];
        $work = [];
        $start = 0;
        $sum = 0;
        $max = 45;

        foreach ($developer as $res) {
            $sum += $res->getTime();

            if ($sum <= $max) {
                $work_data = array(
                    'name' => $res->getName(),
                    'title' => $res->getTitle(),
                    'time' => $res->getTime(),
                );
                array_push($work, $work_data);
            } else {
                $works[$start] = $work;
                $work = [];
                $sum = 0;
                $start++;
            }
        }

        return $this->json(json_encode($works));
    }




}
