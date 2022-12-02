<?php

namespace App\Controller;

use App\Entity\Race;
use App\Entity\Result;
use App\Form\RaceEditingType;
use App\Form\RaceType;
use App\Form\ResultType;
use App\Repository\RaceRepository;
use App\Repository\ResultRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use PhpParser\Node\Expr\Cast\String_;

#[Route('/race', name: 'race.')]
class RaceController extends AbstractController 
{
    #[Route('/', name: 'index')]
    public function index(Request $request,RaceRepository $raceRepository): Response
    {
        $post = $raceRepository->findAll();
        // dump($post[0]);
        $RaceName=$request->get(key:'RaceName');
        $id=$request->get(key:'id');

       // dump($post);
        return $this->render('race/index.html.twig', [
            'controller_name' => 'RaceController',
            'id'=>$id,
            'RaceName'=>$RaceName,
            'posts'=>$post,
        ]);
    }
    #[Route('/show/{id?}', name: 'show')]
    public function show($id,ResultRepository $resultRepository,EntityManagerInterface $em): Response
    {
        $post = $resultRepository->findByRaceId($id);
        $post2 = $resultRepository->findByRaceIdAndDistance($id,'medium');
        $post3 = $resultRepository->findByRaceIdAndDistance($id,'long');

        $temp_placement=1;
        foreach ($post2 as $post2) {
            $post2->setPlacement($temp_placement);
            $temp_placement=$temp_placement+1;
        }
        $temp_placement=1;
        foreach ($post3 as $post3) {
            $post3->setPlacement($temp_placement);
            $temp_placement=$temp_placement+1;
        }

        //dump($post2);
        //dd($post3);
        try {
            //code...
            $em->persist($post2);
            $em->persist($post3);
            $em->flush();
        } catch (\Throwable $th) {
            //throw $th;
            dd("empty race?");
        }
            
        //dump($post);

        $post2 = $resultRepository->findByRaceIdAndDistance($id,'medium');
        $post3 = $resultRepository->findByRaceIdAndDistance($id,'long');


//calculate the avrage time for long distance
        $sumH =0;
        $sumM =0;
        $sumS =0;
        $iterator=0;
        foreach ($post3 as $post) {
          $temp = new DateTime($post->getRaceTime());
          $temp = $temp->format('G');
         // dump($temp);
            $sumH = $temp + $sumH;
            $temp = new DateTime($post->getRaceTime());
            $temp = $temp->format('i');
            $sumM = $temp + $sumM;
            $temp = new DateTime($post->getRaceTime());
            $temp = $temp->format('s');
            $sumS = $temp + $sumS;
            $iterator=$iterator +1;
        }
        // dump($sumH);
        // dump($sumM);
        // dump($sumS);
    // $h=$sumH/$iterator;
    // $m=$sumM/$iterator;
    // $s=$sumS/$iterator;

    // $h = intval($h * ($p = pow(10, 0))) / $p;
    // $m = intval($m * ($p = pow(10, 0))) / $p;
    // $s = intval($s * ($p = pow(10, 0))) / $p;
    // $AVG_L=$h.':'.$m.':'.$s;

    $AVG_L = (($sumH*3600)+($sumM*60)+$sumS) / $iterator;
    $AVG_L = intval($AVG_L * ($p = pow(10, 0))) / $p;
    

    $seconds = $AVG_L ;
$hours = floor($seconds / 3600);
$seconds -= $hours * 3600;
$minutes = floor($seconds / 60);
$seconds -= $minutes * 60;

$AVG_L="$hours:$minutes:$seconds"; //24:0:1

   

//calculate the avrage time for medium distance
        $sumH =0;
        $sumM =0;
        $sumS =0;
        $iterator=0;
        foreach ($post2 as $post) {
          $temp = new DateTime($post->getRaceTime());
          $temp = $temp->format('G');
       //   dump($temp);
            $sumH = $temp + $sumH;
            $temp = new DateTime($post->getRaceTime());
            $temp = $temp->format('i');
            $sumM = $temp + $sumM;
            $temp = new DateTime($post->getRaceTime());
            $temp = $temp->format('s');
            $sumS = $temp + $sumS;
            $iterator=$iterator +1;
        }
    // $h=$sumH/$iterator;
    // $m=$sumM/$iterator;
    // $s=$sumS/$iterator;

    // $h = intval($h * ($p = pow(10, 0))) / $p;
    // $m = intval($m * ($p = pow(10, 0))) / $p;
    // $s = intval($s * ($p = pow(10, 0))) / $p;
    // $AVG_M=$h.':'.$m.':'.$s;
    $AVG_M = (($sumH*3600)+($sumM*60)+$sumS) / $iterator;
    $AVG_M = intval($AVG_M * ($p = pow(10, 0))) / $p;
    

    $seconds = $AVG_M ;
$hours = floor($seconds / 3600);
$seconds -= $hours * 3600;
$minutes = floor($seconds / 60);
$seconds -= $minutes * 60;

$AVG_M="$hours:$minutes:$seconds"; //24:0:1

// dd("fin");


        return $this->render('race/show.html.twig', [
            'controller_name' => 'RaceController',
          'id'=>$id,
            'posts'=>$post2,
            'posts2'=>$post3,
            'medium_time'=>$AVG_M,
            'long_time'=>$AVG_L
        ]);
    }
    
    #[Route('/create', name: 'create')]
    public function create(Request $request,RaceRepository $raceRepository,EntityManagerInterface $em): Response
    {
        $post = new Race();
        $form = $this->createForm(RaceType::class , $post);
        $form->handleRequest($request);


        if($form->isSubmitted()){
            
            
            $em->persist($post);
            $em->flush();
            $filename =  $this->uploadOftheFile($this,$request);
            try {
                //code...
                $reader = Reader::createFromPath( $this->getParameter('uploads_dir').$filename);
            
                $result = $reader->getRecords();
            } catch (\Throwable $th) {
                //throw $th;
                $this->addFlash('danger', 'Csv file not');
                $ID=$post->getId();
           
                $post = new Race();
                $post = $raceRepository->find($ID);
                $em->remove($post);
                foreach ($post as $element){
        
                    $em->remove($element);
                }
                $em->flush();
                return $this->redirect($this->generateUrl(route:'race.index'));
            }
           
            
             
             
             $ID=$post->getId();
           
             $post = new Race();
             $post = $raceRepository->find($ID);
             $raceName = $post->getRaceName();
             


             $temp=1;
             foreach ($result as $value) {
                
                
                // dd($value['fullName']);
                if ($temp) {    //skip once
                    // dump((!($value[0]=="fullName" && $value[1]=="distance" && $value[2]=="time")));
                    if (!($value[0]=="fullName" && $value[1]=="distance" && $value[2]=="time")) {
                        $this->addFlash('danger', 'Csv file not properly made first 3 columns are named: fullName distance time or it might not even be a csv file :(');
                        return $this->redirect($this->generateUrl(route:'race.index'));
                    }
                    $temp=0;
                    continue;
                }
                $postRecord = new Result();
                try {
                    //code...
                
                $postRecord->setFullName($value[0]); 
                $postRecord->setDistance($value[1]); 
                try {
                     new \DateTimeImmutable($value[2]);
                    $postRecord->setRaceTime($value[2]); 
                } catch (\Throwable $th) {
                    //throw $th;
                    $this->addFlash('danger', 'csv input error, time needs to be in a format h:m:s, from 23:59:59 to 0:00:0');
                    return $this->redirect($this->generateUrl(route:'race.index'));
                }
                
                $postRecord->setRace($post);
                
                 $em->persist($postRecord);
                 
                 echo($value[0]);
                 echo($value[1]);
                 echo($value[2]);
                 
                } catch (\Throwable $th) {
                    $this->addFlash('danger', "records not properly added, there must be an input error within the csv file");
                    return $this->redirect($this->generateUrl(route:'race.index'));
                }
                }
                $em->flush();
     
        $this->addFlash('notice', "Created a new Race: ". $raceName);
           return $this->redirect($this->generateUrl(route:'race.index'));
        }
        

        return $this->render('race/create.html.twig', [
            'controller_name' => 'RaceController',
            'form'=> $form->createView()
        ]);
    }
    function uploadOftheFile($current_object,Request $request): String{
    
        // /** @var UploadedFile $file */
        $file=$request->files->get('race')["csv_file"];
       
         if($file){
            if($file->guessClientExtension()!="csv"){
                $current_object->addFlash('danger', 'CSV file is used in this input, please make a csv file with column names... fullName distance time');
                return $current_object->redirect($current_object->generateUrl(route:'race.index'));
            }
             $filename = md5(uniqid()).'.'. $file->guessClientExtension();
         
            $file->move(
                $current_object->getParameter('uploads_dir'),
                $filename,
            );
        }


    return $filename;
}

    #[Route('/edit/result/{id?}', name: 'edit_result')]
    public function edit($id,Request $request,EntityManagerInterface $em,ResultRepository $resultRepository): Response
    {
        $post = new Result();
        $form = $this->createForm(ResultType::class , $post);
        $form->handleRequest($request);

        
       
        if ($form->isSubmitted()) {
           
       
            $name=$request->get(key:'result')["fullName"];
            $time=$request->get(key:'result')["RaceTime"]; ##################################################################################################################################################
            $post = $resultRepository->find($id);

            
            try {
                $temp = new \DateTimeImmutable($time);
            } catch (\Throwable $th) {
                //throw $th;
                $this->addFlash('danger', 'time needs to be in a format h:m:s, from 23:59:59 to 0:00:0');
                return $this->redirect($this->generateUrl(route:'race.index'));
            }
                # code...
                $post->setFullName($name);
                $post->setRaceTime($time);
            

            $em->persist($post);
            $em->flush();
            
            $this->addFlash('notice', 'selected result was edited within race: '.$post->getRace()->getRaceName());
            
           return $this->redirect($this->generateUrl(route:'race.index'));
        }
        

        return $this->render('race/editResult.html.twig', [
            'controller_name' => 'RaceController',
            'form'=> $form->createView()
        ]);
    }
    #[Route('/edit/race/{id?}', name: 'edit_race')]
    public function editRace($id,Request $request,RaceRepository $raceRepository,EntityManagerInterface $em): Response
    {
        $post = new Race();
        $form = $this->createForm(RaceEditingType::class , $post);
        $form->handleRequest($request);

        // dump($request);

        if($form->isSubmitted()){
            $post = $raceRepository->find($id);
            // dump($post);
            $name=$request->get(key:'race_editing')["RaceName"];
            $date=$request->get(key:'race_editing')['RaceDate'];
            
        // dump($name);
      
        $post->setRaceName($name);

        $date=new \DatetimeImmutable($date["day"].'-'.$date["month"].'-'.$date["year"]);
        $post->setRaceDate($date);
            // dump($post);

            $em->persist($post);
            $em->flush();

            $this->addFlash('notice', "Selected Race edited");

            return $this->redirect($this->generateUrl(route:'race.index'));
        }
        

        return $this->render('race/edit.html.twig', [
            'controller_name' => 'RaceController',
            'form'=> $form->createView()
        ]);
    }
    #[Route('/delete/{id?}', name: 'delete')]
    public function delete($id,RaceRepository $raceRepository,ResultRepository $resultRepository,EntityManagerInterface $em): Response
    {
        
        $post = new Race();
        $post = $raceRepository->findAll();
        $post2 = new Race();
        $post2 = $resultRepository->findAll();

        $post3 = $raceRepository->find($id);
        $post4_arr = $resultRepository->findByExampleField($id);
        // dump($post);
        // dump($post2);
        // dump($post3);
        // dump($post4_arr);
        // dd("stop");
       
        $this->addFlash('deletion', "Selected Race and all of it's results have been deleted");

        $em->remove($post3);
        foreach ($post4_arr as $element){

            $em->remove($element);
        }
        $em->flush();
        
        return $this->redirect('/race');
    }
}
function a(){
    dd('boob');
}
