<?php
 
class DbOperation
{
    private $con;
 
    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }
    
    //Method to create a new user
    function registerUser($name, $email, $pass, $phone)
    {
        if (!$this->isUserExist($email)) {
            $pass = md5($pass);
            $query = $this->con->prepare("INSERT INTO tbl_user_authority (name, email, pass, phone) VALUES (?, ?, ?, ?)");

             $query->execute(array($name, $email, $pass, $phone));

            if ($query) {
                 return USER_CREATED;
            } else {
               return USER_CREATION_FAILED;
            }

        }
        return USER_EXIST;
    }
    
    
    //Method to create a new restaurant authority
    // function registerARestAuthority($email, $privilegeID, $restaurantID)
    // {
    //     if (!$this->isUserExist($email)) {
    //         $pass = md5('12345');
    //         $query = $this->con->prepare("INSERT INTO tbl_user_authority (email, pass, privilegeID, restaurantID) VALUES (?, ?, ?, ?)");

    //          $query->execute(array($email, $pass, $privilegeID, $restaurantID));

    //         if ($query) {
    //              return USER_CREATED;
    //         } else {
    //           return USER_CREATION_FAILED;
    //         }

    //     }
    //     return USER_EXIST;
    // }
    
    
    
      //Method to create a new restaurant authority
    function registerARestAuthority($email, $privilegeID, $restaurantID)
    {
        if (!$this->isUserExist($email)) {
            $pass = md5('321');
            $query = $this->con->prepare("INSERT INTO tbl_user_authority (email, pass, privilegeID, restaurantID) VALUES (?, ?, ?, ?)");

             $query->execute(array($email, $pass, $privilegeID, $restaurantID));

            if ($query) {
                 return USER_CREATED;
            } else {
              return USER_CREATION_FAILED;
            }

        }else{
              $query = $this->con->prepare("UPDATE tbl_user_authority SET 
                                            privilegeID = ?,
                                            restaurantID = ?
                                            WHERE email = ?");
              $query->execute(array($privilegeID, $restaurantID, $email));

              if ($query) {
                 return PRIVILEGE_UPDATED;
              }else{
                    return PRIVILEGE_UPDATED_FAILED;
              }
        }
       
    }

    
    
 
    //Method for user login
    function userLogin($email, $pass)
    {
        $pass = md5($pass);
        $query = $this->con->prepare("SELECT userID FROM tbl_user_authority WHERE email = ? AND pass = ?");
        $query->execute(array($email, $pass));

        $row = $query->rowCount();

        if ($row > 0) {
            return true;
        }else{
            return false;
        }
        
    }
 
    
    //Method to get user by email
    function getUserByEmail($email)
    {
        $jsonResult = array();

        $query = $this->con->prepare("SELECT userID, name, email, phone, privilegeID, restaurantID FROM tbl_user_authority WHERE email = ?");
        $query->execute(array($email));
        $user = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($user) {
            $jsonResult = $user;
        }
        return $jsonResult;
    }
 
 
 
    //Method to get user id only
    function getUserID($email, $phone)
    {

        $query = $this->con->prepare("SELECT * FROM tbl_user_authority WHERE email = ? AND phone = ?");
        $query->execute(array($email, $phone));
        $user = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($user) {
           foreach ($user as $item) {
      	 	$userID = $item['userID'];
      	 	return $userID;
      	  }
        }
        
    }
    
    
    
    // get all privileges 
    function getAllPrivileges(){

        $privileges = array();
        $query = $this->con->prepare("SELECT * FROM tbl_privilege");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $privileges = $data;
        }
        return $privileges;   
    }


    // get all cusines 
    function getAllCusines(){

        $cusines = array();
        $query = $this->con->prepare("SELECT * FROM tbl_cusine");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $cusines = $data;
        }
        return $cusines;   
    }


     // get all locations 
    function getAllLocations(){
        $locations = array();
        $query = $this->con->prepare("SELECT * FROM tbl_location");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $locations = $data;
        }
        return $locations;   
    }
    
   // add new location 
   function addNewLocationsName($locationName)
    {
         $query = $this->con->prepare("INSERT INTO tbl_location (locationName) VALUES (?)");
         $query->execute(array($locationName));
          if ($query) {return true;} else {return false;}
    }
    
    //Delete a location
    function deleteALocation($locationID)
    {
        $query = $this->con->prepare("DELETE FROM tbl_location WHERE locationID = ?");
        $query->execute(array($locationID));
        if ($query) {return true;} else {return false;}
    }
    
    

 //Method to create a new Restaurant
    function addNewRestaurantByAdmin($restaurant_Name, $phone, $locationName)
    {
        $temporary_image_link = "no_image_found";
        
        $query = $this->con->prepare("INSERT INTO tbl_restaurant (restaurant_Name, phone, locationName) VALUES (?, ?, ?)");
        $query->execute(array($restaurant_Name, $phone, $locationName));
        $last_id = $this->con->lastInsertId();
        if ($query) {
         
            $queryone = $this->con->prepare("INSERT INTO tbl_restaurant_image_profile (pimage, restaurantID) VALUES (?, ?)");
            
             $querytwo = $this->con->prepare("INSERT INTO tbl_restaurant_image_timeline (timage, restaurantID) VALUES (?, ?)");
             
            $queryone->execute(array($temporary_image_link, $last_id));
            $querytwo->execute(array($temporary_image_link, $last_id));
            
            if($queryone && $querytwo){
                 return true;
            }else{
                 return false;
            }
           
        } else {
            return false;
            
        }
    }
    
 
 // get Restaurant ID, Name, Phone, Location
    function getRestaurentNamPhoneLocation(){

        $locations = array();
        $query = $this->con->prepare("SELECT restaurantID, restaurant_Name, phone, locationName FROM tbl_restaurant ORDER BY restaurantID desc");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $locations = $data;
        }
        return $locations;   
    }
   
   
    //Method to delete a restaurant by authority
    function deleteRestByAuthority($id)
    {
        $query = $this->con->prepare("DELETE FROM tbl_restaurant WHERE restaurantID = ?");
        $query->execute(array($id));
        if ($query) {return true;} else {return false;}
    }
    

 // get  userID, privilegeID, restaurantID, Email, RestaurantName, PrivilegeName
    function getAllAuthorityData(){

        $authority = array();
        $query = $this->con->prepare("SELECT u.userID, p.privilegeID, r.restaurantID, u.email,r.restaurant_Name, p.privilege_name FROM tbl_user_authority AS u INNER JOIN  tbl_restaurant AS r ON u.restaurantID = r.restaurantID INNER JOIN tbl_privilege AS p ON u.privilegeID = p.privilegeID WHERE p.privilegeID = '3' or p.privilegeID = '4' ORDER BY u.userID desc");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $authority = $data;
        }
        return $authority;   
    }
    
    
    //Method to delete a User by authority
    function deleteUserByAuthority($id)
    {
        $query = $this->con->prepare("DELETE FROM tbl_user_authority WHERE userID = ?");
        $query->execute(array($id));
        if ($query) {return true;} else {return false;}
    }
    
   // Update User info By authority
    function updateUserByAuthority($email , $privilegeID, $restaurantID, $userID){
        
        $query = $this->con->prepare("UPDATE tbl_user_authority SET email = ?, privilegeID = ?, restaurantID = ? WHERE  userID = ?");
        $query->execute(array($email, $privilegeID, $restaurantID, $userID));
        if ($query) {return true;} else {return false;}
    }
    

    //Method to create a new foodie authority(admin, sub-admin)
    function registerAFoodieAuthority($name, $email,  $phone, $privilegeID)
    {
        if (!$this->isUserExist($email)) {
            $pass = md5('12345');
            $query = $this->con->prepare("INSERT INTO tbl_user_authority (name, email, pass, phone, privilegeID) VALUES (?, ?, ?, ?, ?)");

             $query->execute(array($name, $email, $pass,  $phone, $privilegeID));

            if ($query) {
                 return USER_CREATED;
            } else {
              return USER_CREATION_FAILED;
            }

        }else{
            
             return USER_EXIST;  
        }
       
    }
    
    
  // get  (userID, privilegeID, Name, Email, Phone, PrivilegeName)
    function getAllFoodieAuthoritysData(){

        $authority = array();
        $query = $this->con->prepare("SELECT u.userID, p.privilegeID, u.name, u.email, u.phone, p.privilege_name, u.image FROM tbl_user_authority AS u INNER JOIN tbl_privilege AS p ON u.privilegeID = p.privilegeID   WHERE p.privilegeID = '1' or p.privilegeID = '2' ORDER BY u.userID desc");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $authority = $data;
        }
        return $authority;   
    }
    


   // get restaurant full information by restaurant id
    function getsingleRestaurantFullInformation($restID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT * FROM tbl_restaurant WHERE restaurantID = ?");
        $query->execute(array($restID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    //  get restaurant Profile image 
    function getSingleRestProfileImage($restID){
        $jsonResult = array();
        
        $pquery = $this->con->prepare("SELECT pimage FROM tbl_restaurant_image_profile WHERE restaurantID = ? ");
        $pquery->execute(array($restID));
        $pdata = $pquery->fetchAll(PDO::FETCH_ASSOC); 
    
        if ($pdata) {
            $jsonResult = $pdata;
           
        }
        return $jsonResult;
    }
    
    
     //  get restaurant timeline image 
    function getSingleRestTimelineImage($restID){
        $jsonResult = array();
    
        $tquery = $this->con->prepare("SELECT timage FROM tbl_restaurant_image_timeline WHERE restaurantID = ? ");
        $tquery->execute(array($restID));
        $tdata = $tquery->fetchAll(PDO::FETCH_ASSOC);  

        if ($tdata) {
            $jsonResult = $tdata;
           
        }
        return $jsonResult;
    }
    
    
    
     // get single restaurant all cusine name
    function getSingleRestaurantAllCusine($restID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT c.* FROM  tbl_cusine AS c INNER JOIN  tbl_bridge_cusine_restaurant AS b ON c.cusineID = b.cusineID INNER JOIN tbl_restaurant AS r ON r.restaurantID = b.restaurantID WHERE b.restaurantID = ?");
        $query->execute(array($restID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
    
    //Method to check any restaurant already upload any pic or not
    function isAnyTimelinePicExist($restID)
    {
        $query = $this->con->prepare("SELECT id FROM tbl_restaurant_image_timeline WHERE restaurantID = ?");
        
        $query->execute(array($restID));
        $row = $query->rowCount();

        if ($row > 0) {
            return true;
        }else{
            return false;
        }
        
    }
    
    
     //Method to delete a timeline pic 
    // function deletePreviousImage($restID)
    // {
    //     $query = $this->con->prepare("SELECT * FROM tbl_restaurant_image_timeline WHERE restaurantID = ?");
        
    //     $query->execute(array($restID));
    //     $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
    //     return $result;
      
    // }
    
    
     //Method to delete a User by authority
    function deleteTimelineImagePreviousEntry($restID)
    {
        $query = $this->con->prepare("DELETE FROM tbl_restaurant_image_timeline WHERE restaurantID = ?");
        $query->execute(array($restID));
        if ($query) {return true;} else {return false;}
     }
    
    //insert timeline imagelink
    function uploadTimelineImage($uploaded_image,  $rest_id)
    {
            $query = $this->con->prepare("INSERT INTO tbl_restaurant_image_timeline (timage, restaurantID) VALUES (?, ?)");
            $query->execute(array($uploaded_image,  $rest_id));
            if ($query) {return true;} else {return false;}
    }
    

      //Method to check any restaurant profile image is  already upload or not
    function isAnyProfilePicExist($restID)
    {
        $query = $this->con->prepare("SELECT id FROM  tbl_restaurant_image_profile WHERE restaurantID = ?");
        
        $query->execute(array($restID));
        $row = $query->rowCount();

        if ($row > 0) {
            return true;
        }else{
            return false;
        }
        
    }
    
    
    //delete profile image prrevious row
    function deleteProfileImagePreviousEntry($restID)
    {
        $query = $this->con->prepare("DELETE FROM tbl_restaurant_image_profile WHERE restaurantID = ?");
        $query->execute(array($restID));
        if ($query) {return true;} else {return false;}
    }
    
    
    //upload profile image
    function uploadProfileImage($uploaded_image,  $rest_id)
    {
             $query = $this->con->prepare("INSERT INTO tbl_restaurant_image_profile (pimage, restaurantID) VALUES (?, ?)");
            $query->execute(array($uploaded_image,  $rest_id));
            if ($query) {return true;} else {return false;}
    }
    
    
    // Update rest info by rest authority
    function updateRestaurantInfoByAuthority($full_Address , $phone, $email, $web_Link, $fb_Link, $open_close_time, $ac, $parking, $wifi, $restID){
        
         $query = $this->con->prepare("UPDATE tbl_restaurant SET 
                                  full_Address = ?, 
                                  phone = ?, 
                                  email = ?, 
                                  web_Link = ?, 
                                  fb_Link = ?, 
                                  open_close_time = ?, 
                                  ac = ?, 
                                  parking = ?, 
                                  wifi = ?
                                 WHERE  restaurantID = ?");
        
        $query->execute(array($full_Address , $phone, $email, $web_Link, $fb_Link, $open_close_time, $ac, $parking, $wifi, $restID));
        if ($query) {return true;} else {return false;}
    }
    
    
    // Add Restaurnt Menu Item
    function addRestaurantMenu($menu_Name, $tk, $restaurantID)
    {
        $query = $this->con->prepare("INSERT INTO tbl_menu (menu_Name, tk, restaurantID) VALUES (?, ?, ?)");
        $query->execute(array($menu_Name, $tk, $restaurantID));
        if ($query) {return true;} else {return false;}

    }
    
    
    // get restaurant all menu item name
    function getRestaurantAllMenuItemName($restID){
        $jsonResult = array();
    
        $query = $this->con->prepare("SELECT * FROM tbl_menu WHERE restaurantID = ? ORDER BY menuID desc");
        $query->execute(array($restID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);  

        if ($data) {
            $jsonResult = $data;
           
        }
        return $jsonResult;
    }
    
    
     //delete restaurant menu item
    function deleteMeuItem($menuid)
    {
        $query = $this->con->prepare("DELETE FROM tbl_menu WHERE menuID = ?");
        $query->execute(array($menuid));
        if ($query) {return true;} else {return false;}
        
    }
    

    //Upload restauranat Gallery Image(image, restaurantID)
    function uploadGalleryImage($uploaded_image, $itemName, $price, $rest_id)
    {
            $query = $this->con->prepare("INSERT INTO tbl_restaurant_image_gallery (image, itemName, price,  restaurantID) VALUES (?,?,?,?)");
            $query->execute(array($uploaded_image, $itemName, $price, $rest_id));
            if ($query) {return true;} else {return false;}

    }
    
    
    // get single restaurant all gallery images
    function getRestaurantGalleryImages($restID){
        $jsonResult = array();
    
        $query = $this->con->prepare("SELECT id,image, itemName, price FROM tbl_restaurant_image_gallery WHERE restaurantID = ? ORDER BY id desc");
        $query->execute(array($restID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);  

        if ($data) {
            $jsonResult = $data;
           
        }
        return $jsonResult;
    }
    
    
    
    //delete restaurant gallery image
    function deleteGalleryImage($id)
    {
        $query = $this->con->prepare("DELETE FROM tbl_restaurant_image_gallery WHERE id = ?");
        $query->execute(array($id));
        if ($query) {return true;} else {return false;}
        
    }
    
     
    //upload authority profile image
    function uploadAuthorityProfileImage($uploaded_image,  $userID)
    {
        
            $query = $this->con->prepare("UPDATE tbl_user_authority SET 
                                  image = ?
                                  WHERE  userID = ?");

            $query->execute(array($uploaded_image,  $userID));

            if ($query) {
                 return true;
            } else {
               return false;
            }

    }
    
    
    
    // Update authority own profile information
    function updateAuthorityInfoByOwn($name , $phone, $address, $userID){
        
         $query = $this->con->prepare("UPDATE tbl_user_authority SET 
                                  name = ?, 
                                  phone = ?, 
                                  address = ?
                                 WHERE  userID = ?");
        
        $query->execute(array($name , $phone, $address, $userID));
        
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    
    

     // get single Restaurant authority info
    function getSingleRestAuthorityInfo($userID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT a.email, a.name, a.phone, a.address, a.image , p.privilege_name, r.restaurant_Name FROM  tbl_user_authority AS a  INNER JOIN  tbl_privilege AS p ON a.privilegeID = p.privilegeID
            INNER JOIN tbl_restaurant AS r ON a.restaurantID = r.restaurantID WHERE a.userID = ?");
        $query->execute(array($userID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
    
    // get single Foodie authority info
    function getSingleFoodieAuthorityInfo($userID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT a.email, a.name, a.phone, a.address  , a.image,  p.privilege_name FROM  tbl_user_authority AS a  INNER JOIN  tbl_privilege AS p ON a.privilegeID = p.privilegeID WHERE a.userID = ?");
        $query->execute(array($userID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    

    


    //advertise request By restaurant authority
    function addNewAdvertiseRequest($title, $details, $uploaded_image, $restaurantID)
    {
        
            $query = $this->con->prepare("INSERT INTO tbl_advertise (title, details, image, restaurantID ) VALUES (?, ?, ?, ?)");
        
            $query->execute(array($title, $details, $uploaded_image, $restaurantID));

            if ($query) {
                 return true;
            } else {
               return false;
            }

    }
    
    
     // get single restaurant advertise list
    function getSingleRestAdvertiselist($restID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT * FROM tbl_advertise WHERE restaurantID = ? ORDER BY add_ID DESC  ");
        $query->execute(array($restID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult; 
    }
    
    
    
      // get all new advertise request list
    function getOnlyNewAdvertiseRequestList()
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT a.*, r.restaurant_Name FROM tbl_advertise AS a INNER JOIN tbl_restaurant AS r ON a.restaurantID = r.restaurantID  WHERE check_status = 'no' ORDER BY add_ID DESC ");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult; 
    }
    
    

     // get all live/Enable advertise list
    function getOnlyLiveAdvertiseList()
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT a.*, r.restaurant_Name FROM tbl_advertise AS a INNER JOIN tbl_restaurant AS r ON a.restaurantID = r.restaurantID WHERE check_status = 'yes' AND live = 'yes' ORDER BY add_ID DESC ");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult; 
    }
    
    
     // get all Disable advertise  list
    function getOnlydisableAdvertiseList()
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT a.*, r.restaurant_Name FROM tbl_advertise AS a INNER JOIN tbl_restaurant AS r ON a.restaurantID = r.restaurantID WHERE check_status = 'yes' AND live = 'no' ORDER BY add_ID DESC ");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult; 
    }
    
    
    
    // get all advertise list
    function getAllAdvertiselist()
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT * FROM tbl_advertise");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
     // Update advertise Live status
    function updateAdvertiseLiveStatus($add_ID, $live){
        
         $query = $this->con->prepare("UPDATE tbl_advertise SET 
                                  live = ?
                                 WHERE  add_ID = ?");
        
        $query->execute(array($live, $add_ID));
        
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    
    
    // Update advertise check status
    function updateAdvertiseCheckStatus($add_ID, $check_status){
        
         $query = $this->con->prepare("UPDATE tbl_advertise SET 
                                  check_status = ?
                                 WHERE  add_ID = ?");
        
        $query->execute(array($check_status, $add_ID));
        
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
   //Method to delete single addvertise 
    function deleteSingleAdvertise($add_ID)
    {
        $query = $this->con->prepare("DELETE FROM tbl_advertise WHERE add_ID = ?");
        
        $query->execute(array($add_ID));
        
        if ($query) {
            return true;
        } else {
            return false;
        }
        
    }
    
    
    //Method to check if email already exist
    function isUserExist($email)
    {
        $query = $this->con->prepare("SELECT userID FROM tbl_user_authority WHERE email = ?");
        
        $query->execute(array($email));
        $row = $query->rowCount();

        if ($row > 0) {
            return true;
        }else{
            return false;
        }
        
    }
    
    
    
    
    
    
    
    
    

    
    
//  From Here Foodiez Main App Api
    
    
    
    
    
    
    
    
    
    //  register new user BY Facebook and Google Sign in 
    function registernewUserFromFbandGmail($name, $email)
    {
        if (!$this->isUserExistCheckedByEmail($email)) {
            $query = $this->con->prepare("INSERT INTO tbl_user (name, email) VALUES (?, ?)");

             $query->execute(array($name, $email));

            if ($query) {
                 return USER_CREATED;
            } else {
               return USER_CREATION_FAILED;
            }

        }
        return USER_EXIST;
    }
    
    
    
     // get user info by user email
    function getUserInfoByEmail($email)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT userID, email FROM tbl_user WHERE email  = ?");
        $query->execute(array($email));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
    
     // get user info by user phone
    function getUserInfoByPhone($phone)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT userID, phone FROM tbl_user WHERE phone  = ?");
        $query->execute(array($phone));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
   //  register new user BY Facebook Account Kit Sign in
    function registernewUserFromAccountKit($name, $phone)
    {
        if (!$this->isUserExistCheckedByPhone($phone)) {
            $query = $this->con->prepare("INSERT INTO tbl_user (name, phone) VALUES (?, ?)");

             $query->execute(array($name, $phone));

            if ($query) {
                 return USER_CREATED;
            } else {
               return USER_CREATION_FAILED;
            }

        }
        return USER_EXIST;
    }
    
    
    
// get Restaurant ID, Name, full address, location, rating(work remain)
    function getAllRestaurantInfoForMainApp(){

        $locations = array();
        $query = $this->con->prepare("SELECT r.restaurantID, r.restaurant_Name, r.full_Address, r.locationName, p.pimage  FROM tbl_restaurant AS r INNER JOIN  tbl_restaurant_image_profile p ON r.restaurantID = p.restaurantID");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $locations = $data;
        }
        return $locations;   
    }
    
    
    
    
    
    //  give review by user
    function giveReviewByUser($title, $details, $rate, $userID, $restaurantID)
    {
        $query = $this->con->prepare("INSERT INTO tbl_review (title, details, rate, userID, restaurantID) VALUES (?, ?, ?, ?, ?)");
        $query->execute(array($title, $details, $rate, $userID, $restaurantID));
        if ($query) {return true;} else {return false;}

    }
    
    
    
      //Method to check is user give any rating before
    function isUserGiveAnyRatingBeforeThisRestaurant($userID, $restaurantID)
    {
        $query = $this->con->prepare("SELECT ratingID FROM tbl_rating WHERE userID = ? AND restaurantID = ?");
        
        $query->execute(array($userID, $restaurantID));
        $row = $query->rowCount();

        if ($row > 0) {
            return true;
        }else{
            return false;
        }
        
    }
    
    
   // give rating to a restaurant
    function giveRatingByUser($rating, $food_rating, $customer_serv_rating, $value_for_mon_rating, $interior_rating,   $userID, $restaurantID)
    {
        $query = $this->con->prepare("INSERT INTO tbl_rating (rating, food_rating, customer_serv_rating, value_for_mon_rating,interior_rating, userID, restaurantID) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $query->execute(array($rating, $food_rating, $customer_serv_rating, $value_for_mon_rating, $interior_rating,   $userID, $restaurantID));
        if ($query) {return true;} else {return false;}

    }
    
    
    
     // update user rating to a restaurant
    function updateUserRating($rating, $food_rating, $customer_serv_rating, $value_for_mon_rating, $interior_rating,   $userID, $restaurantID){
        
         $query = $this->con->prepare("UPDATE tbl_rating SET 
                                  rating = ?,
                                  food_rating = ?,
                                  customer_serv_rating = ?,
                                  value_for_mon_rating = ?,
                                  interior_rating = ?
                                 WHERE userID = ? AND restaurantID = ?");
        
        $query->execute(array($rating, $food_rating, $customer_serv_rating, $value_for_mon_rating, $interior_rating,   $userID, $restaurantID));
        
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
    // get individual restaurant all reviews
    function  getIndividualRestAllReviews($restID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT r.*, u.name, u.profile_image FROM  tbl_review AS r  INNER JOIN  tbl_user AS u ON r.userID = u.userID WHERE r.restaurantID = ?");
        $query->execute(array($restID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
     // get individual restaurant avg Ratng, total rating
    function  getAvgRatingOfaIndividualRestaurant($restID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT restaurantID, AVG(rating) as avgRating, AVG(food_rating) as foodRating, AVG(customer_serv_rating) as customerSerRating, AVG(value_for_mon_rating) as moneyRating, AVG(interior_rating) as interiorRating, COUNT(rating) as totalRating FROM tbl_rating where restaurantID = ?  GROUP BY restaurantID ");
        $query->execute(array($restID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    // get individual restaurant total review
    function  getIndividualRestTotalreview($restID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT restaurantID, COUNT(title) as totalReview FROM tbl_review where restaurantID = ?  GROUP BY restaurantID ");
        $query->execute(array($restID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
    
    //   $query = $this->con->prepare("SELECT restaurantID, AVG(rating) as avgRating FROM tbl_rating GROUP BY restaurantID ");
    
    
    // get latest five Restaurant
    function getLatestFiveRestaurantinfo(){

        $locations = array();
        $query = $this->con->prepare("SELECT r.restaurantID, r.restaurant_Name, r.full_Address, r.locationName, p.pimage  FROM tbl_restaurant AS r INNER JOIN  tbl_restaurant_image_profile p ON r.restaurantID = p.restaurantID  ORDER BY r.restaurantID DESC LIMIT 10 ");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $locations = $data;
        }
        return $locations;   
    }
    
    
    
    // get latest five   Review 
    function  getLatestFiveReviews()
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT r.reviewID, r.details, u.name, u.profile_image, f.restaurant_Name FROM  tbl_review AS r  
		INNER JOIN  tbl_user AS u ON r.userID = u.userID INNER JOIN tbl_restaurant AS f ON r.restaurantID = f.restaurantID ORDER BY r.reviewID DESC LIMIT 10");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    

    
    // get individual user profile information
    function getIndividualUserProfileInfo($userID)
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT userID, name, email, phone, profile_image FROM tbl_user where userID = ?");
        $query->execute(array($userID));
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
          $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
    
     // Update user profile picture 
    function updateUserProfilePicture($uploaded_image, $userID){
        
         $query = $this->con->prepare("UPDATE tbl_user SET 
                                  profile_image = ?
                                 WHERE userID = ?");
        
        $query->execute(array($uploaded_image, $userID));
        
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
    // Restaurant Register Request 
    function restaurantRegisterRequest($restaurant_Name, $rest_Address, $rest_location, $rest_phone, $auth_name, $auth_email, $auth_phone, $auth_address, $auth_privilegeID, $uploaded_image)
    {
            $query = $this->con->prepare("INSERT INTO tbl_register_request (restaurant_Name, rest_Address, rest_location, rest_phone, auth_name, auth_email, auth_phone, auth_address, auth_privilegeID, cred_image) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $query->execute(array($restaurant_Name, $rest_Address, $rest_location, $rest_phone, $auth_name, $auth_email, $auth_phone, $auth_address, $auth_privilegeID, $uploaded_image));
            if ($query) {return true;} else {return false;}

    }
    
    
    
     //get all Restaurnt Registation Request for tab 1
    function getAllRestaurantRegistationRequest()
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT * FROM tbl_register_request  WHERE accept_status = 'No' ORDER BY reqID desc ");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
    //get all Restaurnt Registation Request for tab 2 
    function getAllRestaurantRegistationRequestAccepted()
    {
        $jsonResult = array();
        
        $query = $this->con->prepare("SELECT * FROM tbl_register_request  WHERE accept_status = 'Yes' ORDER BY reqID desc");
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);   

        if ($data) {
            $jsonResult = $data;
        }
        return $jsonResult;
    }
    
    
    
    
     //Method to create a new Restaurant
    function addNewRestAndRestAuthorityFromRequest($reqID, $restaurant_Name, $rest_Address, $rest_location, $rest_phone, $auth_name, $auth_email, $auth_phone, $auth_address, $auth_privilegeID)
    {
        $temporary_image_link = "no_image_found";
        
        $query = $this->con->prepare("INSERT INTO tbl_restaurant (restaurant_Name, full_Address, phone, locationName) VALUES (?, ?, ?, ?)");
        $query->execute(array($restaurant_Name, $rest_Address, $rest_phone,  $rest_location));
        $last_id = $this->con->lastInsertId();
        if ($query) {
            
            $pass = md5('321');
         
            $queryone   = $this->con->prepare("INSERT INTO tbl_restaurant_image_profile (pimage, restaurantID) VALUES (?, ?)");
            
            $querytwo   = $this->con->prepare("INSERT INTO tbl_restaurant_image_timeline (timage, restaurantID) VALUES (?, ?)");
            
            $querythree = $this->con->prepare("INSERT INTO tbl_user_authority (name, email, pass, phone, address, privilegeID, restaurantID) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $queryFour = $this->con->prepare("UPDATE tbl_register_request SET 
                                            accept_status = 'Yes' WHERE reqID = ?");
             
            
             
            $queryone->execute(array($temporary_image_link, $last_id));
            $querytwo->execute(array($temporary_image_link, $last_id));
            $querythree->execute(array($auth_name, $auth_email, $pass, $auth_phone, $auth_address, $auth_privilegeID, $last_id));
            $queryFour->execute(array($reqID));
            
            if($queryone && $querytwo && $querythree && $queryFour){
                 return true;
            }else{
                 return false;
            }
           
        } else {
            return false;
            
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //Method to check if email already exist
    function isUserExistCheckedByEmail($email)
    {
        $query = $this->con->prepare("SELECT userID FROM tbl_user WHERE email = ?");
        
        $query->execute(array($email));
        $row = $query->rowCount();

        if ($row > 0) {
            return true;
        }else{
            return false;
        }
        
    }
    
    
    //Method to check if phone already exist
    function isUserExistCheckedByPhone($phone)
    {
        $query = $this->con->prepare("SELECT userID FROM tbl_user WHERE phone = ?");
        
        $query->execute(array($phone));
        $row = $query->rowCount();

        if ($row > 0) {
            return true;
        }else{
            return false;
        }
        
    }
}