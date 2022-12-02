<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {

//             $arr= array("2:02:22","2:06:22","3:04:21");
            
//             for ($i=0; $i < 3; $i++) { 
//                 $arr2[$i] = new DateTime($arr[$i]);
//             }
            
//             sort($arr2);

//             // $date1 = new DateTime($string1);
//             // $date2 = new DateTime($string2);

//             // dump($string1);
//             // dump($string2);

//             // dump($date1);
//             // dump($date2);
//             // if($date1<$date2){
//             //     echo $string1;
//             // }
// dump($arr);
// dump($arr2);



        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
