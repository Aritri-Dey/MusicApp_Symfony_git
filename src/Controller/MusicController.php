<?php

namespace App\Controller;

use App\Entity\Music;
use App\Entity\UploadTable;
use App\Entity\UserInfo;
use App\Entity\Favourite;
use App\Services\SendMail;
use App\Services\UploadValidation;
use App\Services\Validation;
use App\Repository\MusicRepository;
use App\Repository\UserInfoRepository;
use App\Repository\FavouriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class implements the main controller of the application
 * that handles all the functions related to routing.
 */
class MusicController extends AbstractController 
{

  /**
   *  Constant to store error message for empty field.
   */
  const EMPTYERROR = "Please fill this field";
  /**
   *  Constant to store error message for invalid email field.
   */
  const INVALIDEMAIL = "Invalid Email";
  /**
   * Constant to store error message for invalid phone number field.
   */
  const PHONERR = "Enter a valid phone number";
  /**
   * Constant to store error message for interest field.
   */
  const INTERESTERR = "Please enter atleast one interest";
  /**
   * Constant to store error message for non-existant account.
   */
  const ACCERROR = "Account does not exist";

  /**
   *  @var MusicRepository $musicRepository 
   *    Global variable that stores object of MusicRepository.
   */
  private $musicRepository;
  /**
   *  @var FavouriteRepository $favouriteRepository
   *    Global variable that stores object of FavouriteRepository.
   */
  private $favouriteRepository;
  /**
   *  @var UserInfoRepository $userInfoRepository
   *    Global variable that stores object of UserInfoRepository.
   */
  private $userInfoRepository;
  /**
   *  @var object $em
   *    Global variable that stores object of EntityManagerInterface class.
   */
  private $em;
  /**
   *  @var object $userInfoTable
   *    Global variable that stores object of UserInfo class.
   */
  private $userInfoTable;
  /**
   *  @var object $musicTable
   *    Global variable that stores object of Music class.
   */
  private $musicTable;
  /**
   *  @var object $favTable
   *    Global variable that stores object of Favourite class.
   */
  private $favTable;
  /**
   *  @var object $uploadTable
   *    Global variable that stores object of UploadTable class.
   */
  private $uploadTable;
  /**
   *  @var object $user
   *    Stores object of UserInfo class.
   */
  private $user;
  /**
   *  @var object $fav
   *    Stores object of Favourite class.
   */
  private $fav;
  /**
   *  @var object $upload
   *    Stores object of uploadTable class.
   */
  private $upload;
  /**
   *  @var object $upload
   *    Stores object of Validation class.
   */
  private $validation;

  /**
   * Constructor to initialize global variables.
   * 
   *  @param MusicRepository $musicRepository 
   *    Global variable that stores object of MusicRepository class. 
   *  @param UserInfoRepository $userInfoRepository 
   *    Global variable that stores object of userInfoRepository class.
   *  @param FavouriteRepository $favouriteRepository 
   *    Global variable that stores object of FavouriteRepository class.
   *  @param object $entityManager 
   *    Global variable that stores object of EntityManager class.
   */
  public function __construct(MusicRepository $musicRepository, UserInfoRepository $userInfoRepository , FavouriteRepository $favouriteRepository ,EntityManagerInterface $entityManager) {
    $this->musicRepository = $musicRepository;
    $this->em = $entityManager;
    $this->favouriteRepository = $favouriteRepository;
    $this->userInfoRepository = $userInfoRepository; 
    $this->userInfoTable = $this->em->getRepository(UserInfo::class);
    $this->musicTable = $this->em->getRepository(Music::class);
    $this->favTable = $this->em->getRepository(Favourite::class);
    $this->uploadTable = $this->em->getRepository(UploadTable::class);
    // Creating object of class UserInfo.
    $this->user = new UserInfo(); 
    // Creating object of class Favourite.
    $this->fav = new Favourite(); 
    // Creating object of class UploadTable.
    $this->upload = new UploadTable();
    // Creating object of class Validation.
    $this->validation = new Validation();
  }

  /**
   * Function to render index.html.twig
   * which is the landing page of the application.
   * 
   *  @return Response 
   *    Returns and renders index page. 
   **/
  #[Route('/index', name: 'index')]
  public function index(): Response {   
    return $this->render('music/index.html.twig');
  }

  /**
   * Function to render update form page and update user information.
   * 1.Can be accessed only if user is logged in
   * 2.User infomration is taken through upadte form and validated.
   * 3.If validation is successful, then inofrmation is updated in database,
   * else error message is returned, and page is redirected to update form. 
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response 
   *    Returns and renders pages according to satisfied condition.
   */
  #[Route('/music/update',name: 'update')]
  public function update(Request $rq): Response {   
    if ($rq->get('update_btn')) {

      // Getting input field values through Request.
      $emailOld = $rq->get("oldEmail");
      $emailNew = $rq->get("newEmail");
      $number = $rq->get("number");
      $genre = $rq->get("genre");
      // Validatiing all the form fields.
      // Empty validation, syntax validation.
      if ($this->validation->validateEmailEmpty($emailOld)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateEmailEmpty($emailOld),
          ]);
      }
      else if ($this->validation->validateEmailEmpty($emailNew)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateEmailEmpty($emailNew),
          ]);
      }
      else if ($this->validation->validEmail($emailNew)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validEmail($emailNew),
          ]);
      }
      else if ($this->validation->validateNumberEmpty($number)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateNumberEmpty($number),
          ]);
      }
      else if ($this->validation->validateGenreEmpty($genre)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateGenreEmpty($genre),
          ]);
      }
      // UserInfo entity is searched, if email id matches then that 
      //entire row is fetched.
      $rep = $this->userInfoTable->findOneBy(['email' => $emailOld]);
      if ($rep) {
        $session = $rq->getSession();
        // Checks if user is logged in.
        if($session->get('loggedin')) {
          // Setting value and saving in database.
          $rep->setEmail($emailNew); 
          $rep->setNumber($number);
          $rep->setGenre($genre);
          $this->em->persist($rep);
          $this->em->flush();
          return $this->render('music/update.html.twig',[
            "successMessage" => "Account updated successfully!"
          ]);
        }
        return $this->render('music/update.html.twig',[
          "errMessage" => "Please login first."
        ]);
      }
      return $this->render('music/update.html.twig',[
        "errMessage" => MusicController::ACCERROR
      ]);
    }
    return $this->render('music/update.html.twig'); 
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
  public function login(Request $rq): Response {  
    if ($rq->get('loginBtn')) {
      // Getting input field values through Request.
      $userNameForm = $rq->get("userName");
      $emailForm = $rq->get("email");
      $passwordForm = $rq->get("password");
      // Form field validation.
      if ($this->validation->validateNameEmpty($userNameForm)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateNameEmpty($userNameForm),
          ]);
      }
      if ($this->validation->validateEmailEmpty($emailForm)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateEmailEmpty($emailForm),
          ]);
      }
      if ($this->validation->validatePasswordEmpty($passwordForm)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validatePasswordEmpty($passwordForm),
          ]);
      }
      $rep = $this->userInfoTable->findOneBy(['userName' => $userNameForm]);
      if ($rep) {
        $userName = $rep->getUsername();
        $password = $rep->getPassword();
        $email = $rep->getEmail();
        // If correct credentails are filled, user is logged in, loggedin session 
        // variable is set to 1.
        if ($password == $passwordForm && $email == $emailForm) {
          $session = $rq->getSession();
          $session->set('loggedin', '1');
          $session->set('user',$userName);
          return $this->render('music/music_lib.html.twig',[
            'music' => $this->musicRepository->paginate($rq->query->getInt("page",1)),
            'loggedin' => '1',
            ]);
        }
        return $this->render('music/login.html.twig',[
          "errMessage" => "Wrong credentials"
        ]);
      }
      return $this->render('music/login.html.twig',[
        "errMessage" => "User does not exist"
      ]);
    }
    return $this->render('music/login.html.twig');
  }

  /**
   * Function to reset password of user.
   * 1.Gets username from user through form and finds the registered 
   * email id corresponding to that username.
   * 2.Sends a mail to the registered mail id if account exists, mail contains
   * link to the form where user can enter new password.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   */
  #[Route('/resetPassword', name: 'resetPassword')]
  public function resetPassword(Request $rq): Response {   
    if ($rq->get('resetBtn')) {
      $userName =$rq->get('userName');
      $rep = $this->userInfoTable->findOneBy(['userName' => $userName]);
      if ($rep) {
        $email = $rep->getEmail();
        // Object of SendMail class is created to send mail to user. 
        $mailObj = new SendMail($email);
        $flag = $mailObj->mailer();
        if ($flag) {
          return $this->render('music/resetPassword.html.twig',[
            'succmsg' => 'A mail has been sent to your registered email id.',
            ]);
        }
        return $this->render('music/resetPassword.html.twig',[
          'errmsg' => 'There was a problem sending the mail.',
          ]);
      }
      return $this->render('music/resetPassword.html.twig',[
        'errmsg' => MusicController::ACCERROR
        ]);
    }
    return $this->render('music/resetPassword.html.twig');
  }

  /**
   * Function to get new password from user and updating it in database.
   * User receives this link via mail.
   * On submitting form, first it is chekd whether user exist or not,
   * if yes, password is updated, else error is shown.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns and redners resetpassword.html.twig whcih contains a form to submit username.
   */
  #[Route('/newPassword', name: 'newPassword')]
  public function newPassword(Request $rq): Response {   
    if ($rq->get('submitBtn')) {
      // Getting input field values through Request.
      $userName = $rq->get("userName");
      $pass = $rq->get("password");
      $conPass = $rq->get("conPassword");
      // UserInfo class is fetched to update password in database
      $rep = $this->userInfoTable->findOneBy(['userName' => $userName]);
      if ($rep) {
        if($pass == $conPass) {
          $rep->setPassword($pass);
          $this->em->persist($rep);
          $this->em->flush();
          return $this->render('music/resetPasswordForm.html.twig',[
            "succmsg" => "Password updated successfully!"
          ]);
        }
        return $this->render('music/resetPasswordForm.html.twig',[
          "errmsg" => "Password field and confirm password does not match."
        ]);        
      }
      return $this->render('music/resetPasswordForm.html.twig',[
        'errmsg' => 'Wrong username',
        ]);
    }
    return $this->render('music/resetPasswordForm.html.twig');
  }

  /**
   * Function to get data from music repository and display it to user in the 
   * music library page only if logged in.
   * 
   *  @param Request
   *    Gets information from client request through form.
   * 
   *  @param object $paginatorInterface
   *    Stores object of PaginatorInterface class, used for pagination.
   * 
   *  @return Response
   *    Returns and renders the music libarry if logged in, else the 
   *    login page if user is not logged in.
   */
  #[Route('/library', name: 'library')]
  public function checkLoggedIn(Request $rq) :Response {
    $session = $rq->getSession();
    $logged = $session->get('loggedin');
    if ($logged) {
      return $this->render('music/music_lib.html.twig',[
      'music' => $this->musicTable->paginate($rq->query->getInt("page",1)),
      'loggedin' => $logged,
      ]);
    }
    return $this->render('music/login.html.twig');
  }

  /**
   * Function for pagination.
   * 
   *  @param Request
   *    Gets information from client request through form. 
   */
  #[Route('/lib2', name: 'lib2')]
  public function lib2(Request $rq) :Response {
    $session = $rq->getSession();
    $logged = $session->get('loggedin');
    return $this->render('music/music_lib.html.twig',[
      'music' => $this->musicTable->paginate($rq->query->getInt("page",2)),
      'loggedin' => $logged,
      ]);
  }

  /**
   * Function for pagination.
   * 
   *  @param Request
   *    Gets information from client request through form.
   */
  #[Route('/lib3', name: 'lib3')]
  public function lib3(Request $rq) :Response {
    $session = $rq->getSession();
    return $this->render('music/music_lib.html.twig',[
      'music' => $this->musicTable->paginate($rq->query->getInt("page",3)),
      'loggedin' => $session->get('loggedin'),
      ]);
  }
  
  /**
   * Function to display songs uploaded by users.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns the mysong.html.twig page if user is logged in, else
   *    renders the login page.
   */
  #[Route('/mySongs', name: 'mySongs')]
  public function mySongs(Request $rq) :Response {
    $session = $rq->getSession();
    if ($session->get('loggedin')) {
      $music = $this->uploadTable->findAll();
      return $this->render('music/mysong.html.twig',[
      'music' => $music,
      ]);
    }
    return $this->render('music/login.html.twig');
  }

  /**
   * Function to logout user.
   * When a user logs out of the applicayon, the 'loggedin' session 
   * variable is set to 0.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns the logged out page.
   */
  #[Route('/loggedOut', name: 'loggedOut')]
  public function loggedOut(Request $rq): Response {  
    $session = $rq->getSession();
    $session->set('loggedin', '0');
    return $this->render('music/logout.html.twig',[
      'loggedin' => '0',  
    ]);
  }

  /**
   * Function to check and validate data entered by user in the 
   * registration form.
   * If validation is successful, the data is pushed to databse and
   * login page is rendered.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Renders login page if form validation is successful, else 
   *    renders the login page.
   */
  #[Route('/music/register', name: 'checkRegister')]
  public function checkRegister( Request $rq): Response {  
    if ($rq->get('regBtn')) {
      // Getting input field values through Request.
      $userName = $rq->get("userName");
      $email = $rq->get("email");
      $number = $rq->get("number");
      $password = $rq->get("password");
      $genre = $rq->get('genre');
      // Register form field validation.
      if ($this->validation->validateNameEmpty($userName)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateNameEmpty($userName),
          ]);
      }
      if ($this->validation->validateEmailEmpty($email)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateEmailEmpty($email),
          ]);
      }
      else if ($this->validation->validEmail($email)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validEmail($email),
          ]);
      }
      else if ($this->validation->validateNumberEmpty($number)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateNumberEmpty($number),
          ]);
      }
      else if ($this->validation->validatePasswordEmpty($password)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validatePasswordEmpty($password),
          ]);
      }
      else if ($this->validation->validateGenreEmpty($genre)) {
        return $this->render('music/update.html.twig',[
          'msg' => $this->validation->validateGenreEmpty($genre),
          ]);
      }
      // Setting value and saving in database.
      $this->user->setUsername($userName);
      $this->user->setEmail($email);
      $this->user->setPassword($password);
      $this->user->setNumber($number);
      $this->user->setGenre($genre);
      $this->em->persist($this->user);
      // Executing crud operations.
      $this->em->flush(); 
      return $this->render('music/login.html.twig');
    }
    return $this->render('music/register.html.twig');
  }

  /**
   * Function to add a song to favourites.
   * When user adds a song to favourites song id, title, path is fetched 
   * and added to the entity, and favourite.html.twig is rendered which 
   * shows list of favourite songs of user.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Redirects to function which renders the favorite 
   *    page favourite.html.twig.
   */
  #[Route('/addToFav/{id}', name: 'addToFav')]
  public function addToFavourite( $id, Request $rq): Response {  
    $rep = $this->musicRepository->findOneBy(['uid' => $id]);
    $songId = $rep->getId();
    $songName= $rep->getTitle();
    $path = $rep->getSongPath();
    $session = $rq->getSession();
    $currentUser = $session->get('user');
    $checkExist = $this->favTable->findOneBy(['user' => $currentUser , 'songid' => $songId]);
    if ($checkExist) {
      return $this->redirectToRoute('favourite');
    }
    // Setting value and saving in database.
    $this->fav->setUser($currentUser); 
    $this->fav->setSongid($songId);
    $this->fav->setPath($path);
    $this->fav->setSongname($songName);
    $this->em->persist($this->fav);
    // Executing crud operations.
    $this->em->flush(); 
    return $this->redirectToRoute('favourite');
  }

  /**
   * Function to display all songs that user has marked as favourite.
   * The current user s fetched, and his/her corresponding favourite songs are 
   * fetched from the database and displayed.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns and renders page with currectuser and songlist, if user does
   *    not have any favourite song , then returns only currentuser.
   */
  #[Route('/favourite', name: 'favourite')]
  public function favourite(Request $rq): Response {  
    $session = $rq->getSession();
    if ($session->get('loggedin')) {
      $currentUser = $session->get('user');
      $rep = $this->favTable->findBy(['user' => $currentUser]);
      if ($rep) {
        return $this->render('/music/favourite.html.twig',[
          "list" => $rep,
          "user" => $currentUser,
        ]); 
      }
      return $this->render('/music/favourite.html.twig',[
        "user" => $currentUser,
      ]); 
    }
    return $this->render('music/login.html.twig');
  }

  /**
   * Function to remove a song from the favourite database.
   *  
   *  @param Request $rq
   *    Gets information from client request through form.
   *  @param int $id
   *    Stores id of the song to be deleted.
   * 
   *  @return Response
   *    Redirects route to function to show favourites page after deleting. 
   */
  #[Route('/deleteFav/{id}', name: 'deleteFav')]
  public function deleteFav(Request $rq, int $id): Response { 
    $session = $rq->getSession();
    $currentUser = $session->get('user');
    $rep = $this->favTable->findOneBY(['user' => $currentUser, 'songid'=>$id]);
    $this->em->remove($rep);
    $this->em->flush();
    return $this->redirectToRoute('favourite');
  }

  /**
   * Function to upload a song by user.
   * All data is accepted through form and validated. 
   * If validation is successful data is stored in the UploadTable entity, 
   * else error is shown.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Return and renders the upload page if user is logged in, 
   *    else renders the login page. 
   */
  #[Route('/music/upload', name: 'upload')]
  public function upload(Request $rq): Response {  
    if ($rq->get('uploadBtn')) {
      $session = $rq->getSession();
      if ($session->get('loggedin')) {
        // Getting input field values through Request.
        $title = $rq->get("audioName");
        $singer = $rq->get("singer");
        $genre = $rq->get("genre");
        $audioFile = $rq->files->get('audioFile');
        $newAudioFileName = uniqid() ;
        $imgFile = $rq->files->get("audioImg");
        $newImgFileName = uniqid();
        // Validation check for fileds.
        $valObj = new UploadValidation($title , $singer, $audioFile,$genre, $imgFile);
        $msg = $valObj->validateData();
        if ($msg) {
          return $this->render('music/register.html.twig',[
            'msg' => $msg,
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
        $this->upload->setUploadTitle($title);
        $this->upload->setUploadSinger($singer);
        $this->upload->setAudioPath($newAudioFileName . ".mp3");
        $this->upload->setImagePath($newImgFileName . '.jpg');
        $this->em->persist($this->upload);
        $this->em->flush();
        return $this->render('/music/upload.html.twig',[
          "successMessage" => "Song upload sucessful."
        ]);  
      }
      return $this->render('/music/upload.html.twig',[
        "errMessage" => "Please login first."
      ]); 
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
  #[Route('/music/{id}', methods: ['GET'] ,name: 'eachMusic')]
  public function show($id, Request $rq): Response {   
    $music = $this->musicTable->find($id);
    $session = $rq->getSession();
    $currentUser = $session->get('user');
    $rep = $this->favTable->findOneBY(['user' => $currentUser, 'songid'=>$id]);
    if ($rep) {
      return $this->render('music/show.html.twig',[
        'showMusic' => $music,
        'exist' => TRUE,
      ]);
    }
    return $this->render('music/show.html.twig',[
      'showMusic' => $music,
    ]);
  }
}
