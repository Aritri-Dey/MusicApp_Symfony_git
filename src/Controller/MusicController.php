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


/**
 * This class implements the main controller of the application
 * that handles all the functions related to routing.
 */
class MusicController extends AbstractController {

  /**
   *  @var MusicRepository $musicRepository 
   *    Global variable that stores object of MusicRepository class.
   */
  private $musicRepository;

  /**
   *  @var FavouriteRepository $favouriterepository
   *    Global variable that stores object of FavouriteRepository class.
   */
  private $favouriterepository;

  /**
   *  @var UserInfoRepository $userInfoRepository
   *    Global variable that stores object of UserInfoRepository class.
   */
  private $userinfoRepository;

  /**
   *  @var object $em
   *    Global variable that stores object of EntityManagerInterface class.
   */
  private $em;


  /**
   * Constructer to initialize global variables.
   */
  public function __construct(MusicRepository $musicRepository, UserInfoRepository $userInfoRepository , FavouriteRepository $favouriterepository ,EntityManagerInterface $entityManagery) {
    $this->musicRepository = $musicRepository;
    $this->em = $entityManagery;
    $this->favouriterepository = $favouriterepository;
    $this->userinfoRepository = $userInfoRepository; 

  }

  /**
   * Function to render index.html.twig
   * which is the landing page of the application.
   * 
   *  @return Response 
   *    Returns and renders index page. 
   **/
  #[Route('/index', name: 'index')]
  public function index(): Response
  {   
    return $this->render('music/index.html.twig');
  }


  /**
   * Function to render update form page and update user information.
   *  1.Can be accessed only if user is logged in
   *  2.User infomration is taken through upadte form and validated.
   *  3.If validation is successful, then inofrmation is updated in database,
   *    else error message is returned, and page is redirected to update form. 
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response 
   *    Returns and renders pages according to satisfied condition.
   */
  #[Route('/music/update',name: 'update')]
  public function update(Request $rq): Response
  {   
    
    if ($rq->get('update_btn')) {

      //Getting input field values through Request.
      $femail_old = $rq->get("oldemail");
      $femail_new = $rq->get("newemail");
      $fnumber = $rq->get("number");
      $fgenre = $rq->get("genre");

      /**
       * Validatiing all the form fields using respect package.
       * Empty validation, syntax validation.
       */
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
          return $this->render('/music/update.html.twig',[
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


      //UserInfo entity is searched, if email id matches then that entire row is fetched
      $rep = $this->em->getRepository(UserInfo::class)->findOneBy(['email' => $femail_old]);
      if($rep) {
        $session = $rq->getSession();
        $logged = $session->get('loggedin');
        //checkes if user is logged in
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


   /**
   * Function to render the login form page
   * 
   *  @return Response
   *    Returns and renders the page containing login form.
   */
  #[Route('/login_page', name: 'login_page')]
  public function log(): Response {  
    return $this->render('music/login.html.twig');
  }


  /**
   * Function to validate information enterd by user in the login form
   * and redirect to desired route.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns and renders page according to satisfied condition.
   */
  #[Route('/music/login', name: 'login')]
  public function login(Request $rq): Response
  {  
    //Getting input field values through Request.
    $fusername = $rq->get("username");
    $femail = $rq->get("email");
    $fpassword = $rq->get("password");

    //Form field validation.
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

      //If correct credentails are filled, user is logged in, loggedin session variable is set to 1.
      if($password==$fpassword && $email==$femail) {
        $session = $rq->getSession();
        $session->set('loggedin', '1');
        $session->set('user',$username);
        $loggedin = $session->get('loggedin');

        return $this->render('music/music_lib.html.twig',[
          'music' => $this->musicRepository->paginate($rq->query->getInt("page",1)),
          'loggedin' => $loggedin,
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


  /**
   * Function to render resetPassword.html.twig
   * where user can opt to reset their password.
   * 
   *  @return Response
   *    returns and redners resetpassword.html.twig whcih contains a form to submit username.
   */
  #[Route('/forgotpassword', name: 'forgotPassword')]
  public function forgotPassword(): Response
  {   
      return $this->render('music/resetPassword.html.twig');
  }


  /**
   * Function to reset password of user.
   * 1. Gets username from user through form and finds the registerd email id corresponding to
   * that username.
   * 2. Sends a mail to the registered mail id if account exists, mail contains link to the form
   * where user can enter new password.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   */
  #[Route('/resetpassword', name: 'resetPassword')]
  public function resetPassword(Request $rq): Response
  {   
    if ($rq->get('resetBtn')){
      $username =$rq->get('username');
    
      $rep = $this->em->getRepository(UserInfo::class)->findOneBy(['username' => $username]);

      if($rep) {
        $email = $rep->getEmail();

        //Object of SendMail class is created to send mail to user. 
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

  /**
   * Funtion to render reset password form, user receives this path via mail.
   */
  #[Route('/resetPasswordForm', name: 'resetPasswordForm')]
  public function resetPasswordForm(): Response
  {   
    return $this->render('music/resetPasswordForm.html.twig');
  }


  /**
   * Function to get new password from user and updating it in database.
   * User receives this link via mail.
   * On submitting form, first it is chekd whether user exist or not,
   *  if yes, password is updated, else error is shown.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    returns and redners resetpassword.html.twig whcih contains a form to submit username.
   */
  #[Route('/newpassword', name: 'newpassword')]
  public function newpassword(Request $rq): Response
  {   
    if ($rq->get('submitBtn')) {
      //Getting input field values through Request.
      $username = $rq->get("username");
      $pass = $rq->get("password");
      $con_pass = $rq->get("conpassword");

      //UserInfo class is fetched to update password in database
      $rep = $this->userinfoRepository->findOneBy(['username' => $username]);

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


  /**
   * Function to get data from music repository and display it to user in the music library page
   *  only if logged in.
   * 
   *  @param Request
   *    Gets information from client request through form.
   * 
   *  @param object $paginatorInterface
   *    Stores object of PaginatorInterface class, used for pagination.
   * 
   *  @return Response
   *    Returns and renders the music libarry if logged in, else the login page if user is not logged in.
   */
  #[Route('/library', name: 'library')]
  public function checkLoggedIn(Request $rq , PaginatorInterface $paginatorInterface) :Response {
    $session = $rq->getSession();
    $logged = $session->get('loggedin');
    if($logged == 1) {
      return $this->render('music/music_lib.html.twig',[
      'music' => $this->musicRepository->paginate($rq->query->getInt("page",1)),
      'loggedin' => $logged,
      ]);
    }
    return $this->render('music/login.html.twig');
  }

  /**
   * Function for pagination.
   */
  #[Route('/lib2', name: 'lib2')]
  public function lib2(Request $rq , PaginatorInterface $paginatorInterface) :Response {
    $session = $rq->getSession();
    $logged = $session->get('loggedin');
    return $this->render('music/music_lib.html.twig',[
      'music' => $this->musicRepository->paginate($rq->query->getInt("page",2)),
      'loggedin' => $logged,
      ]);
  }

  /**
   * Function for pagination.
   */
  #[Route('/lib3', name: 'lib3')]
  public function lib3(Request $rq , PaginatorInterface $paginatorInterface) :Response {
    $session = $rq->getSession();
    $logged = $session->get('loggedin');
    return $this->render('music/music_lib.html.twig',[
      'music' => $this->musicRepository->paginate($rq->query->getInt("page",3)),
      'loggedin' => $logged,
      ]);
  }
  

  /**
   * Function to display songs uploaded by users.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns the mysong.html.twig page if user is logged in, else renders the login page.
   */
  #[Route('/mysongs', name: 'mysongs')]
  public function mysongs(Request $rq) :Response {
    $session = $rq->getSession();
    $logged = $session->get('loggedin');
    if($logged == 1) {
      $music = $this->em->getRepository(UploadTable::class)->findAll();
      return $this->render('music/mysong.html.twig',[
      'music' => $music,
      ]);
    }
    return $this->render('music/login.html.twig');
  }



  /**
   * Function to logout user.
   *  When a user logs out of the applicayon, the 'loggedin' session variable is set to 0.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns the logged out page.
   */
  #[Route('/loggedout', name: 'loggedout')]
  public function loggedout(Request $rq): Response
  {  
    $session = $rq->getSession();
    $session->set('loggedin', '0');
    $logged = $session->get('loggedin');
    return $this->render('music/logout.html.twig',[
      'loggedin' => $logged,  
    ]);
  }


  /**
   * Function to render the registration form page.
   */
  #[Route('/register', name: 'register')]
  public function register(EntityManagerInterface $entityManager, Request $rq): Response
  {  
      return $this->render('music/register.html.twig');
  }


  /**
   * Function to check and validate data entered by user in the registration form.
   * If validation is successful, the data is pushed to databse and login page is rendered.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Renders login page if form validation is successful, else renders the login page.
   */
  #[Route('/music/register', name: 'checkRegister')]
  public function checkRegister( Request $rq): Response
  {  
    //Getting input field values through Request.
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
    //creating object of class UserInfo
    $user = new UserInfo(); 
    //setting value and saving in database
    $user->setUsername($username);
    $user->setEmail($email);
    $user->setPassword($password);
    $user->setNumber($number);
    $user->setGenre($garr);
  
    $this->em->persist($user);
    //executing crud operations
    $this->em->flush(); 

    return $this->render('music/login.html.twig');
  }


  /**
   * Function to add a song to favourites.
   * When user adds a song to favourites song id, title, path is fetched and ois added to the Favoutite entity,
   * and favourite.html.twig is rendered which shows list of favourite songs of user.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Redirects to function which renders the favorite page favourite.html.twig.
   */
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
    //creating object of class Favourite
    $fav = new Favourite(); 
    //setting value and saving in database
    $fav->setUser($currentUser); 
    $fav->setSongid($songId);
    $fav->setPath($path);
    $fav->setSongname($songname);


    $this->em->persist($fav);
    //executing crud operations
    $this->em->flush(); 
    return $this->redirectToRoute('favourite');
  }

  /**
   * Function to take user to the favourites page.
   * If user is logged in then route is redirected to function which renders the favourit.html.twig,
   * else login page is rendered.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Redirects to another function if user is logged in, else renders login page.
   */
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

  /**
   * Function to display all songs that user has marked as favourite.
   * The current user s fetched, and his/her corresponding favourite songs are fetched from the database and displayed.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns and renders page with currectuser and songlist, if user does not hve any favourite song , then returns
   *      only currentuser.
   */
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

  /**
   * Function to remove a song from the favourite database.
   *  
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @param int $id
   *    Stores id of the song to be deleted.
   * 
   *  @return Response
   *    Redirects route to function to show favourites page after deleting. 
   */
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


  /**
   * Function to upload a song by user.
   * All data is accepted through form and validated. 
   * If validation is successful data is stored in the UploadTable entity, else error is shown.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Return and renders the upload page if user is logged in, else renders the login page. 
   */
  #[Route('/music/upload', name: 'upload')]
  public function upload(Request $rq): Response {  

    if ($rq->get('upload_btn')) {
      $session = $rq->getSession();
      $logged = $session->get('loggedin');
      if($logged == 1){
      
        //Getting input field values through Request.
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
        } 
        catch (FileException $e) {
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


  /**
   * Function to display each song individually.
   *  Fetches id of the song and displays the song in a different page.
   * 
   *  @param int $id
   *    Stores id of the song to be displayed.
   * 
   *  @return Response
   *    Returns and renders show.html.twig
   */
  #[Route('/music/{id}', methods: ['GET'] ,name: 'eachmusic')]
  public function show($id, Request $rq): Response
  {   
    $music = $this->musicRepository->find($id);
    $session = $rq->getSession();
    $currentuser = $session->get('user');

    $rep = $this->em->getRepository(Favourite::class)->findOneBY(['user' => $currentuser, 'songid'=>$id]);
    if ($rep) {
      return $this->render('music/show.html.twig',[
        'showmusic' => $music,
        'exist' => TRUE,
      ]);
    }

    return $this->render('music/show.html.twig',[
      'showmusic' => $music,
    ]);
  }
}


