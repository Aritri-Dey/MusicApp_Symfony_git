<?php

namespace App\Controller;

use App\Entity\Music;
use App\Entity\UploadTable;
use App\Entity\UserInfo;
use App\Entity\CheckMail;
use App\Entity\Favourite;
use App\Entity\SendMail;
use App\Form\UserType;
use App\Repository\MusicRepository;
use App\Repository\UserInfoRepository;
use App\Repository\UploadTableRepository;
use App\Repository\FavouriteRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Respect\Validation\Validator as v;
use Symfony\Component\HttpFoundation\JsonResponse;

use Knp\Component\Pager\PaginatorInterface;

class MusicController extends AbstractController
{
    private $musicRepository;
    private $favouriterepository;
    private $userinfoRepository;
    private $em;

    public function __construct(MusicRepository $musicRepository, UserInfoRepository $userInfoRepository , FavouriteRepository $favouriterepository ,EntityManagerInterface $entityManagery) {
        $this->musicRepository = $musicRepository;
        $this->em = $entityManagery;
        $this->favouriterepository = $favouriterepository;
        $this->userinfoRepository = $userInfoRepository; 

    }

    /**
     * Route to landing page of musicplayer 
     **/
    #[Route('/index', name: 'index')]
    public function index(): Response
    {   
        return $this->render('music/index.html.twig');
    }


    /**
     * Function to update user information.
     *  1.Can be accessed only if user is logged in
     *  2.User infomration is taken through form and validated.
     *  3.If validation is successful, then inofrmation is updatrs in database,
     *    else error message is returned, and page is redirected to update form. 
     */
    #[Route('/music/update',name: 'update')]
    public function update(Request $rq): Response
    {   
      
      if ($rq->get('update_btn')) {

        $femail_old = $rq->get("oldemail");
        $femail_new = $rq->get("newemail");
        $fnumber = $rq->get("number");
        $fgenre = $rq->get("genre");

        
        if(v::notEmpty()->validate($femail_old) == FALSE) {
          return $this->render('music/update.html.twig',[
            'oldemailErr' => "Please enter your old email",
            ]);
        }

        if(v::notEmpty()->validate($femail_new)){
          $mailObj = new CheckMail($femail_new);
          $flag = $mailObj->check();
          if($flag == FALSE) {
            return $this->render('/music/update.html.twig',[
              "newemailErr" => "Invalid email."
            ]);  
          }
        }
        else if(v::notEmpty()->validate($femail_new) == FALSE) {
          return $this->render('music/update.html.twig',[
            'newemailErr' => "Please enter your new email",
            ]);
        }

        if(v::notEmpty()->validate($fnumber)){
          if(v::regex('/^[0-9+]{13}+$/')->validate($fnumber) == FALSE) {
            return $this->render('/music/register.html.twig',[
              "phoneErr" => "Please enter a valid phone number starting with +91."
            ]); 
          }
        }
        else if(v::notEmpty()->validate($fnumber) == FALSE) {
          return $this->render('music/update.html.twig',[
            'phoneErr' => "Please enter contact number",
            ]);
        }

        if(v::notEmpty()->validate($fgenre) == FALSE) {
          return $this->render('music/update.html.twig',[
            'gErr' => "Please select atleast one interest",
            ]);
        }

        $rep = $this->em->getRepository(UserInfo::class)->findOneBy(['email' => $femail_old]);
        if($rep) {
          $session = $rq->getSession();
          $logged = $session->get('loggedin');
          if($logged == 1){
          
            //setting value and saving in database
            $rep->setEmail($femail_new); 
            $rep->setNumber($fnumber);
            $rep->setGenre($fgenre);
    
            $this->em->persist($rep);
            $this->em->flush();

            return $this->render('music/update.html.twig',[
              "successMessage" => "Account updated successfully!"
            ]);
          }
          
          else {
            return $this->render('music/update.html.twig',[
              "errMessage" => "Please login first."
            ]);
          }
        }
        return $this->render('music/update.html.twig',[
          "errMessage" => "Account does not exist."
        ]);
      }
      return $this->render('music/update.html.twig'); 
    }


    #[Route('/login_page', name: 'login_page')]
    /**
     * Function to render the login form page
     */
    public function log(): Response {  
      return $this->render('music/login.html.twig');
    }

    #[Route('/music/login', name: 'login')]
    public function login(Request $rq, MusicRepository $m): Response
    {  
        $fusername = $rq->get("username");
        $femail = $rq->get("email");
        $fpassword = $rq->get("password");

        if(v::notEmpty()->validate($fusername) == FALSE) {
          return $this->render('music/login.html.twig',[
            'nameErr' => "Please enter username",
            ]);
        }
        if(v::notEmpty()->validate($femail) == FALSE) {
          return $this->render('music/login.html.twig',[
            'emailErr' => "Please enter email",
            ]);
        }
        if(v::notEmpty()->validate($fpassword) == FALSE) {
          return $this->render('music/login.html.twig',[
            'passErr' => "Please enter password",
            ]);
        }



        $rep = $this->em->getRepository(UserInfo::class)->findOneBy(['username' => $fusername]);

        if($rep) {
          $username = $rep->getUsername();
          $password = $rep->getPassword();
          $email = $rep->getEmail();

          if($password==$fpassword && $email==$femail) {
            $session = $rq->getSession();
            $session->set('loggedin', '1');
            $session->set('user',$username);

            // $music = $this->musicRepository->findAll();
            return $this->render('music/music_lib.html.twig',[
              'music' => $m->paginate($rq->query->getInt("page",1)),
              ]);
          }
          else {
            return $this->render('music/login.html.twig',[
              "errMessage" => "Wrong credentials"
            ]);
          }
        }
        return $this->render('music/login.html.twig',[
          "errMessage" => "User does not exist"
        ]);
    }

    #[Route('/forgotpassword', name: 'forgotPassword')]
    public function forgotPassword(): Response
    {   
        return $this->render('music/resetPassword.html.twig');
    }

    #[Route('/resetpassword', name: 'resetPassword')]
    public function resetPassword(Request $rq): Response
    {   
      if ($rq->get('resetBtn')){
        $username =$rq->get('username');
      
        $rep = $this->em->getRepository(UserInfo::class)->findOneBy(['username' => $username]);

        if($rep) {
          $email = $rep->getEmail();

          $mailObj = new SendMail($email);
          $flag = $mailObj->mailer();
          if($flag == TRUE) {
            return $this->render('music/resetPassword.html.twig',[
              'succmsg' => 'A mail has been sent to your registered email id.',
              ]);
          }
          return $this->render('music/resetPassword.html.twig',[
            'errmsg' => 'There was a problem sending the mail.',
            ]);
        }
        else {
          return $this->render('music/resetPassword.html.twig',[
            'errmsg' => 'Account does not exist',
            ]);
        }

      }
      return $this->render('music/resetPassword.html.twig');
    }

    #[Route('/resetPasswordForm', name: 'resetPasswordForm')]
    public function resetPasswordForm(): Response
    {   
        return $this->render('music/resetPasswordForm.html.twig');
    }

    #[Route('/newpassword', name: 'newpassword')]
    public function newpassword(Request $rq): Response
    {   
      if ($rq->get('submitBtn')) {
        $username = $rq->get("username");
        $pass = $rq->get("password");
        $con_pass = $rq->get("conpassword");

        $rep = $this->em->getRepository(UserInfo::class)->findOneBy(['username' => $username]);

        if($rep) {
          if($pass == $con_pass) {
            $rep->setPassword($pass);

            $this->em->persist($rep);
            $this->em->flush();

            return $this->render('music/resetPasswordForm.html.twig',[
              "succmsg" => "Password updated successfully!"
            ]);
          }
          else {
            return $this->render('music/resetPasswordForm.html.twig',[
              "errmsg" => "Password field and confirm password does not match."
            ]);
          }
         
        }
        else {
          return $this->render('music/resetPasswordForm.html.twig',[
            'errmsg' => 'Wrong username',
            ]);
        }


      }

      return $this->render('music/resetPasswordForm.html.twig');
    }




    #[Route('/library', name: 'library')]
    public function checkLoggedIn(Request $rq,MusicRepository $m,PaginatorInterface $paginatorInterface) :Response {
      $session = $rq->getSession();
      $logged = $session->get('loggedin');
      if($logged == 1) {
        // $music = $this->musicRepository->findAll();

        // $page = $rq->get("page");
        // dd($page);
        return $this->render('music/music_lib.html.twig',[
        'music' => $m->paginate($rq->query->getInt("page",1)),
        ]);
      }
      return $this->render('music/login.html.twig');
    }

    #[Route('/lib2', name: 'lib2')]
    public function lib2(Request $rq,MusicRepository $m,PaginatorInterface $paginatorInterface) :Response {
      return $this->render('music/music_lib.html.twig',[
        'music' => $m->paginate($rq->query->getInt("page",2)),
        ]);
    }

    #[Route('/lib3', name: 'lib3')]
    public function lib3(Request $rq,MusicRepository $m,PaginatorInterface $paginatorInterface) :Response {
      return $this->render('music/music_lib.html.twig',[
        'music' => $m->paginate($rq->query->getInt("page",3)),
        ]);
    }
   

    #[Route('/mysongs', name: 'mysongs')]
    public function mysongs(Request $rq) :Response {
      $session = $rq->getSession();
      $logged = $session->get('loggedin');
      if($logged == 1) {
        $music = $this->em->getRepository(UploadTable::class)->findAll();
        // dd($music);
        return $this->render('music/mysong.html.twig',[
        'music' => $music,
        ]);
      }
      return $this->render('music/login.html.twig');
    }


    #[Route('/loggedout', name: 'loggedout')]
    public function loggedout(Request $rq): Response
    {  
      $session = $rq->getSession();
      $session->set('loggedin', '0');
      return $this->render('music/logout.html.twig');
    }


    #[Route('/register', name: 'register')]
    /**
     * Function to render the registration form page
     */
    public function register(EntityManagerInterface $entityManager, Request $rq): Response
    {  
        return $this->render('music/register.html.twig');
    }

    #[Route('/music/register', name: 'checkRegister')]
    /**
     * Function to push registration form data to database
     * and render the login form page
     */
    public function checkRegister( Request $rq): Response
    {  
      $username = $rq->get("username");
      $email = $rq->get("email");
      $number = $rq->get("number");
      $password = $rq->get("password");
      $garr = $rq->get('genre');

      //Username field validation.
      if(v::notEmpty()->validate($username)){ 
        if(v::alpha()->validate($username) == FALSE){
          return $this->render('/music/register.html.twig',[
            "nameErr" => "Username can only contain alphabets."
          ]);  
        }
      }
      else if(v::notEmpty()->validate($username) == FALSE) {
        return $this->render('/music/register.html.twig',[
          "nameErrEmpty" => "Please enter username."
        ]);  
      }

      //Email field validation.
      if(v::notEmpty()->validate($email)){
        $mailObj = new CheckMail($email);

        $flag = $mailObj->check();
        if($flag == FALSE) {
          return $this->render('/music/register.html.twig',[
            "emailErr" => "Invalid email."
          ]);  
        }
      }
      else if(v::notEmpty()->validate($email) == FALSE) {
        return $this->render('/music/register.html.twig',[
          "emailErrEmpty" => "Please enter email address."
        ]);  
      }

      //Contact number field validation.
      if(v::notEmpty()->validate($number)){
        if(v::regex('/^[0-9+]{13}+$/')->validate($number) == FALSE) {
          return $this->render('/music/register.html.twig',[
            "numErr" => "Please enter a valid phone number starting with +91."
          ]); 
        }
      }
      else if(v::notEmpty()->validate($number) == FALSE) {
        return $this->render('/music/register.html.twig',[
          "numErrEmpty" => "Please enter contact number."
        ]);  
      }

      //genre field validation.
      if(v::notEmpty()->validate($garr) == FALSE){
        return $this->render('/music/register.html.twig',[
          "gErr" => "Please select atleast one genre."
        ]); 
      }

      //Password field validation.
      if(v::notEmpty()->validate($password) == FALSE){
        return $this->render('/music/register.html.twig',[
          "passErr" => "Please set a password."
        ]); 
      }

      $user = new UserInfo(); //creating object of class UserInfo

      $user->setUsername($username); //setting value and saving in database
      $user->setEmail($email);
      $user->setPassword($password);
      $user->setNumber($number);
      $user->setGenre($garr);
    
      $this->em->persist($user);

      $this->em->flush(); //executing crud operations

      return $this->render('music/login.html.twig');
    }


    #[Route('/addtofav/{id}', name: 'addtofav')]
    public function addToFavourite( $id, Request $rq): Response
    {  
      $rep = $this->musicRepository->findOneBy(['uid' => $id]);

      $songId = $rep->getId();
      $songname= $rep->getTitle();
      $path = $rep->getSongPath();

      $session = $rq->getSession();
      $currentUser = $session->get('user');
      $checkExist = $this->em->getRepository(Favourite::class)->findOneBy(['user' => $currentUser , 'songid' => $songId]);
      if($checkExist) {
        return $this->redirectToRoute('favourite');
      }

      $fav = new Favourite(); //creating object of class UserInfo

      $fav->setUser($currentUser); //setting value and saving in database
      $fav->setSongid($songId);
      $fav->setPath($path);
      $fav->setSongname($songname);


      $this->em->persist($fav);

      $this->em->flush(); //executing crud operations

      return $this->redirectToRoute('favourite');
    }

    #[Route('/gotofavourite', name: 'gotofavourite')]
    public function gotofavourite(Request $rq): Response
    {  
      $session = $rq->getSession();
      $logged = $session->get('loggedin');
      if($logged == 1) {
        return $this->redirectToRoute('favourite');
      }
      return $this->render('music/login.html.twig');
    }

    #[Route('/favourite', name: 'favourite')]
    public function favourite(Request $rq): Response
    {  
      $session = $rq->getSession();
      $currentUser = $session->get('user');

      $rep = $this->em->getRepository(Favourite::class)->findBy(['user' => $currentUser]);

      if($rep) {
        return $this->render('/music/favourite.html.twig',[
          "list" => $rep,
          "user" => $currentUser,
        ]); 
      }

      return $this->render('/music/favourite.html.twig',[
        "user" => $currentUser,
      ]); 
    }

    #[Route('/deletefav/{id}', name: 'deletefav')]
    public function deletefav(Request $rq,$id): Response
    { 
      $session = $rq->getSession();
      $currentUser = $session->get('user');
      $rep = $this->em->getRepository(Favourite::class)->findOneBY(['user' => $currentUser, 'songid'=>$id]);
      $this->em->remove($rep);
      $this->em->flush();

      return $this->redirectToRoute('favourite');
    }


    #[Route('/music/upload', name: 'upload')]
    public function upload(Request $rq): Response {  

      if ($rq->get('upload_btn')) {
        $session = $rq->getSession();
        $logged = $session->get('loggedin');
        if($logged == 1){
        
          $title = $rq->get("audio-name");
          $singer = $rq->get("singer");
          $genre = $rq->get("genre");

          $audioFile = $rq->files->get('audio-file');
          $newAudioFileName = uniqid() ;
         

          $imgFile = $rq->files->get("audio-img");
          $newImgFileName = uniqid();

          if(v::notEmpty()->validate($title) == FALSE){
            return $this->render('/music/upload.html.twig',[
              "titleErr" => "Please set a title for the song."
            ]); 
          }

          if(v::notEmpty()->validate($singer) == FALSE){
            return $this->render('/music/upload.html.twig',[
              "singerErr" => "Please set a singer for the song."
            ]); 
          }

          if(v::notEmpty()->validate($audioFile) == FALSE){
            return $this->render('/music/upload.html.twig',[
              "audioErr" => "Please select an audio file to upload."
            ]); 
          }

           //genre field validation.
          if(v::notEmpty()->validate($genre) == FALSE){
            return $this->render('/music/upload.html.twig',[
              "gErr" => "Please select atleast one genre."
            ]); 
          }

          if(v::notEmpty()->validate($imgFile) == FALSE){
            return $this->render('/music/upload.html.twig',[
              "imgErr" => "Please select an image file to upload."
            ]); 
          }

          try {
                $audioFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/assets/upload_audio/',
                    $newAudioFileName . ".mp3"
                );
                $imgFile->move(
                  $this->getParameter('kernel.project_dir') . '/public/assets/upload_image/',
                  $newImgFileName . ".jpg"
              );
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }

            $upload = new UploadTable();
            
            $upload->setUploadTitle($title);
            $upload->setUploadSinger($singer);
            // $upload->set
            $upload->setAudioPath($newAudioFileName . ".mp3");
            $upload->setImagePath($newImgFileName . '.jpg');
            
            $this->em->persist($upload);
            $this->em->flush();
          
          return $this->render('/music/upload.html.twig',[
            "successMessage" => "Song upload sucessful."
          ]);  
        }
        
        else {
          return $this->render('/music/upload.html.twig',[
            "errMessage" => "Please login first."
          ]);
        }
        
      }

      return $this->render('/music/upload.html.twig');
    }


    #[Route('/music/{id}', methods: ['GET'] ,name: 'eachmusic')]
    public function show($id): Response
    {   
        $music = $this->musicRepository->find($id);
        return $this->render('music/show.html.twig',[
            'showmusic' => $music,
        ]);
    }

    // #[Route('/test', name: 'test')]
    // public function test(Request $rq): Response
    // {   
    //     $user = $rq->get("user");
    //     return new JsonResponse(["user" => $user]);
    // } 
  }