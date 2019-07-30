<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require_once '../includes/DbOperation.php';

//Creating a new app with the config to show errors
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);


$app->post('/registerAsAUser', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('name', 'email', 'pass', 'phone'))) {
        $requestData = $request->getParsedBody();

        $name = $requestData['name'];
        $email = $requestData['email'];
        $pass = $requestData['pass'];
        $phone = $requestData['phone'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db->registerUser($name, $email, $pass, $phone);

        if ($result == USER_CREATED) {
          
            $responseData['error'] = false;
            $responseData['message'] = 'Registered successfully';
        } elseif ($result == USER_CREATION_FAILED) {
            $responseData['error'] = true;
            $responseData['message'] = 'Some error occurred';
        } elseif ($result == USER_EXIST) {
            $responseData['error'] = true;
            $responseData['message'] = 'This email already exist, please login';
        }

        $response->getBody()->write(json_encode($responseData));
    }
});



//user login route
$app->post('/login', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('email', 'pass'))) {
        $requestData = $request->getParsedBody();
        $email = $requestData['email'];
        $pass = $requestData['pass'];

        $db = new DbOperation();

        $responseData = array();

        if ($db->userLogin($email, $pass)) {
            $responseData['error'] = false;
            $responseData['user'] = $db->getUserByEmail($email);
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Invalid email or password';
        }

        $response->getBody()->write(json_encode($responseData));
    }
});


//getting all privileges
$app->get('/privileges', function (Request $request, Response $response) {
    $db = new DbOperation();
    $data = $db->getAllPrivileges();
    $response->getBody()->write(json_encode(array("privileges" => $data)));
});

//getting all cusines
$app->get('/cusines', function (Request $request, Response $response) {
    $db = new DbOperation();
    $data = $db->getAllCusines();
    $response->getBody()->write(json_encode(array("cusines" => $data)));
});


//get all locations
$app->get('/locations', function (Request $request, Response $response) {
    $db = new DbOperation();
    $data = $db->getAllLocations();
    $response->getBody()->write(json_encode(array("locations" => $data)));
});

// add new location 
$app->post('/add_new_locations', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('locationName'))) {
        $requestData = $request->getParsedBody();

        $locationName = $requestData['locationName'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db-> addNewLocationsName($locationName);

        if ($result) {
            $responseData['error'] = false;
            $responseData['message'] = 'New Location Added Successfully';
        }else{
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        } 

        $response->getBody()->write(json_encode($responseData));
    }
});


//Delete a location
$app->delete('/delete_a_location/{locationID}', function (Request $request, Response $response) {
    $locationID = $request->getAttribute('locationID');
	if(!empty($locationID )){
	$db = new DbOperation();
	$responseData = array();

        if ($db->deleteALocation($locationID)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Delete successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        }
        $response->getBody()->write(json_encode($responseData));
	}
});



// add a new restaurant by admin
$app->post('/restaurantAdd', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('restaurant_Name', 'phone', 'locationName'))) {
        $requestData = $request->getParsedBody();

        $restaurant_Name = $requestData['restaurant_Name'];
        $phone = $requestData['phone'];
        $locationName = $requestData['locationName'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db-> addNewRestaurantByAdmin($restaurant_Name, $phone, $locationName);

        if ($result) {
            $responseData['error'] = false;
            $responseData['message'] = 'Restaurant Add successfully';
        }else{
             $responseData['error'] = true;
            $responseData['message'] = 'Something Problem';
        } 
        
        $response->getBody()->write(json_encode($responseData));
    }
});



//getting restaurant  Name,phone,Location from admin
$app->get('/restaurantThreeField', function (Request $request, Response $response) {
    $db = new DbOperation();
    $data = $db->getRestaurentNamPhoneLocation();
    $response->getBody()->write(json_encode(array("restaurants" => $data)));
});


//Method to delete a restaurant by authority
$app->delete('/deleteRestByAuthority/{restaurantID}', function (Request $request, Response $response) {
   
        $id = $request->getAttribute('restaurantID');
	
	if(!empty($id )){
	
	$db = new DbOperation();
	$responseData = array();

        if ($db->deleteRestByAuthority($id)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Delete successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        }

        $response->getBody()->write(json_encode($responseData));
	}
     
});




//Method to create a new restaurant authority
$app->post('/register_a_restaurant_authority', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('email', 'privilegeID', 'restaurantID'))) {
        $requestData = $request->getParsedBody();

        $email  = $requestData['email'];
        $privilegeID  = $requestData['privilegeID'];
        $restaurantID = $requestData['restaurantID'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db->registerARestAuthority($email, $privilegeID, $restaurantID);

        if ($result == USER_CREATED) {
            $responseData['error'] = false;
            $responseData['message'] = 'Registered successfully';
        } elseif ($result == USER_CREATION_FAILED) {
            $responseData['error'] = true;
            $responseData['message'] = 'Some error occurred';
        } elseif ($result == PRIVILEGE_UPDATED) {
            $responseData['error'] = false;
            $responseData['message'] = 'Email already exist, just Restaurant Id and Privilege Id is updated !';
        } elseif ($result == PRIVILEGE_UPDATED_FAILED){
             $responseData['error'] = true;
            $responseData['message'] = 'Restaurant and Privilege Update Failed !';
        }

        $response->getBody()->write(json_encode($responseData));
    }
});



 // get  userID, privilegeID, restaurantID, Email, RestaurantName, PrivilegeName
$app->get('/all_authoritys', function (Request $request, Response $response) {
    $db = new DbOperation();
    $data = $db->getAllAuthorityData();
    $response->getBody()->write(json_encode(array("authoritys" => $data)));
});




//Method to delete a User by authority
$app->delete('/delete_user_by_authority/{userID}', function (Request $request, Response $response) {
   
        $id = $request->getAttribute('userID');
	
	if(!empty($id)){
	
	$db = new DbOperation();
	$responseData = array();

        if ($db->deleteUserByAuthority($id)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Delete successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        }

        $response->getBody()->write(json_encode($responseData));
	}
     
});



// Update User info By authority
$app->post('/update_user_info_by_authority/{userID}', function (Request $request, Response $response) {
    
    if (isTheseParametersAvailable(array('email', 'privilegeID','restaurantID'))) {
        $userID = $request->getAttribute('userID');

        $requestData = $request->getParsedBody();

        $email  = $requestData['email'];
        $privilegeID  = $requestData['privilegeID'];
        $restaurantID = $requestData['restaurantID'];

        
        $db = new DbOperation();
	    $responseData = array();

        if ($db->updateUserByAuthority($email , $privilegeID, $restaurantID, $userID)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Updated successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Not updated';
        }

        $response->getBody()->write(json_encode($responseData));
    }
});


//Method to create a new foodie authority(admin, sub-admin)
$app->post('/register_a_foodie_authority', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('name','email','phone', 'privilegeID'))) {
        $requestData = $request->getParsedBody();

        $name  = $requestData['name'];
        $email  = $requestData['email'];
        $phone = $requestData['phone'];
        $privilegeID  = $requestData['privilegeID'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db->registerAFoodieAuthority($name, $email,  $phone, $privilegeID);

        if ($result == USER_CREATED) {
            
            $responseData['error'] = false;
            $responseData['message'] = 'Registered successfully';
        } elseif ($result == USER_CREATION_FAILED) {
            $responseData['error'] = true;
            $responseData['message'] = 'Some error occurred';
        } elseif ($result == USER_EXIST) {
            $responseData['error'] = false;
            $responseData['message'] = 'Email already exist, just change the privilege Id only !';
        } 

        $response->getBody()->write(json_encode($responseData));
    }
});



 // get  (userID, privilegeID, Name, Email, Phone, PrivilegeName)
$app->get('/all_foodie_authoritys', function (Request $request, Response $response) {
    $db = new DbOperation();
    $responseData = array();
    
    $responseData['authoritys'] = $db->getAllFoodieAuthoritysData();
    $response->getBody()->write(json_encode($responseData));
});



// get restaurant full information by restaurant id
$app->get('/restaurant_full_details_by_id/{restaurantID}', function (Request $request, Response $response) {
   
    $restID = $request->getAttribute('restaurantID');
    
	$responseData = array();
	$db = new DbOperation();
	  
	if(!empty($restID)){
	
    $responseData['info'] = $db->getsingleRestaurantFullInformation($restID);
    $responseData['profileimg'] = $db->getSingleRestProfileImage($restID);
    $responseData['timelineimg'] = $db->getSingleRestTimelineImage($restID);
    $responseData['cusine'] = $db->getSingleRestaurantAllCusine($restID);

    $response->getBody()->write(json_encode($responseData));
    
	} 
     
});



//Upload restauranat timeline Image(image, restaurantID)
$app->post('/upload_restaurant_timeline_image', function (Request $request, Response $response) {
    
         $requestData = $request->getParsedBody();
         $restID  = $requestData['restaurantID'];
        
         $db = new DbOperation();
         $permited  = array('jpg', 'jpeg', 'png', 'gif');
                            $file_name = $_FILES['timage']['name'];
                             $file_size = $_FILES['timage']['size'];
                            $file_temp = $_FILES['timage']['tmp_name'];

                            $div = explode('.', $file_name); // The explode() function breaks a string into an array.//explode(separator,string,limit) 3 param..
                            $file_ext = strtolower(end($div));
                            $unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
                            $uploaded_image = "timeline_image/".$unique_image;
                            
                            
                if ($file_size >1048567) {
                    
                 echo json_encode(array('message' => "Image Size should be less then 1MB!"));

                 }elseif (in_array($file_ext, $permited) === false) {
                    
                  echo json_encode(array('message' => "You can upload only:-"
                 .implode(', ', $permited).""));

                } 
                else{
                    
                    $beforeUpload = $db-> isAnyTimelinePicExist($restID);
                    
                    if($beforeUpload){
                        
                        // $deletePreviousPic = $db->deletePreviousImage($restID);
                        // if ($deletePreviousPic) {
                      	 //foreach ($deletePreviousPic as $item) {
                      	 //	$dellink = $item['image'];
                      	 //	unlink($dellink);
                      	 //}
                      	 
                        // }
                        
                        $deleteRow = $db-> deleteTimelineImagePreviousEntry($restID);
                        
                        
                         move_uploaded_file($file_temp, $uploaded_image);
                         $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
                          
                         $result = $db-> uploadTimelineImage($uploaded_image,  $restID);
                        if ($result) {
                            
                         echo json_encode(array('message' => "Timeline Image Update Successfuly !."));
                         
                        }else {
                            
                         echo json_encode(array('message' => "Timeline Image Update Failed!"));
                         
                        }
                        
                    }else{
                        move_uploaded_file($file_temp, $uploaded_image);
                        
                        $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
    
    
                        $result = $db-> uploadTimelineImage($uploaded_image,  $restID);
                        if ($result) {
                            
                         echo json_encode(array('message' => "Timeline Image Upload Successfully."));
                         
                        }else {
                            
                         echo json_encode(array('message' => "Timeline Image Upload Failed !"));
                         
                        }
                        
                    }
                        
           }
    

        //$response->getBody()->write(json_encode($responseData));
    
});




//Upload restauranat Profile Image(image, restaurantID)
$app->post('/upload_restaurant_profile_image', function (Request $request, Response $response) {
    
         $requestData = $request->getParsedBody();
         $restID  = $requestData['restaurantID'];
        
         $db = new DbOperation();
         $permited  = array('jpg', 'jpeg', 'png', 'gif');
                            $file_name = $_FILES['pimage']['name'];
                             $file_size = $_FILES['pimage']['size'];
                            $file_temp = $_FILES['pimage']['tmp_name'];

                            $div = explode('.', $file_name); // The explode() function breaks a string into an array.//explode(separator,string,limit) 3 param..
                            $file_ext = strtolower(end($div));
                            $unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
                            $uploaded_image = "profile_image/".$unique_image;
                            
                            
                if ($file_size >1048567) {
                    
                 echo json_encode(array('message' => "Image Size should be less then 1MB!"));

                 }elseif (in_array($file_ext, $permited) === false) {
                    
                  echo json_encode(array('message' => "You can upload only:-"
                 .implode(', ', $permited).""));

                } 
                else{
                    
                    $beforeUpload = $db-> isAnyProfilePicExist($restID);
                    
                    if($beforeUpload){
                        
                        // $deletePreviousPic = $db->deletePreviousImage($restID);
                        // if ($deletePreviousPic) {
                      	 //foreach ($deletePreviousPic as $item) {
                      	 //	$dellink = $item['image'];
                      	 //	unlink($dellink);
                      	 //}
                      	 
                        // }
                        
                        $deleteRow = $db-> deleteProfileImagePreviousEntry($restID);
                        
                        
                         move_uploaded_file($file_temp, $uploaded_image);
                         $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
                          
                         $result = $db-> uploadProfileImage($uploaded_image,  $restID);
                        if ($result) {
                            
                         echo json_encode(array('message' => "Profile Image Update Successfuly !."));
                         
                        }else {
                            
                         echo json_encode(array('message' => "Profile Image Update Failed!"));
                         
                        }
                        
                    }else{
                        move_uploaded_file($file_temp, $uploaded_image);
                        
                        $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
    
    
                        $result = $db-> uploadProfileImage($uploaded_image,  $restID);
                        if ($result) {
                            
                         echo json_encode(array('message' => "Profile Image Upload Successfully."));
                         
                        }else {
                            
                         echo json_encode(array('message' => "Profile Image Upload Failed !"));
                         
                        }
                        
                    }
                        
           }
    

        //$response->getBody()->write(json_encode($responseData));
    
});



// Update Restaurant info by Restaurant owner or manager
$app->post('/update_rest_info_by_rest_authority/{restaurantID}', function (Request $request, Response $response) {
    
    if (isTheseParametersAvailable(array('full_Address', 'phone','email', 'web_Link', 'fb_Link', 'open_close_time', 'ac', 'parking', 'wifi'))) {
        $restID = $request->getAttribute('restaurantID');

        $requestData = $request->getParsedBody();

        $full_Address  = $requestData['full_Address'];
        $phone  = $requestData['phone'];
        $email = $requestData['email'];
        $web_Link = $requestData['web_Link'];
        $fb_Link = $requestData['fb_Link'];
        $open_close_time = $requestData['open_close_time'];
        $ac = $requestData['ac'];
        $parking = $requestData['parking'];
        $wifi = $requestData['wifi'];

        
        $db = new DbOperation();
	    $responseData = array();

        if ($db->updateRestaurantInfoByAuthority($full_Address , $phone, $email, $web_Link, $fb_Link, $open_close_time, $ac, $parking, $wifi, $restID)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Updated successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Not updated';
        }

        $response->getBody()->write(json_encode($responseData));
    }
});



// add restaurant menu 
$app->post('/add_restaurant_menu', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('menu_Name', 'tk', 'restaurantID'))) {
        $requestData = $request->getParsedBody();

        $menu_Name = $requestData['menu_Name'];
        $tk = $requestData['tk'];
        $restaurantID = $requestData['restaurantID'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db-> addRestaurantMenu($menu_Name, $tk, $restaurantID);

        if ($result) {
            $responseData['error'] = false;
            $responseData['message'] = 'Menu Item Added successfully';
        }else{
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        } 

        $response->getBody()->write(json_encode($responseData));
    }
});


 // get restaurant all menu item name
$app->get('/get_rest_all_menu_item_name/{restaurantID}', function (Request $request, Response $response) {
    $restID = $request->getAttribute('restaurantID');
    $db = new DbOperation();
    $data = $db->getRestaurantAllMenuItemName($restID);
    $response->getBody()->write(json_encode(array("menuitem" => $data)));
});


//delete restaurant menu item
$app->delete('/deleteRestMenuItem/{menuID}', function (Request $request, Response $response) {
   $menuid = $request->getAttribute('menuID');
	if(!empty($menuid )){
	$db = new DbOperation();
	$responseData = array();

        if ($db->deleteMeuItem($menuid)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Delete successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        }
        $response->getBody()->write(json_encode($responseData));
	}
});





//Upload restauranat Gallery Image(image, restaurantID)
$app->post('/upload_restaurant_gallery_image', function (Request $request, Response $response) {
    
         $requestData = $request->getParsedBody();
         $restID  = $requestData['restaurantID'];
         $itemName  = $requestData['itemName'];
         $price  = $requestData['price'];
        
         $db = new DbOperation();
         $responseData = array();
         
         $permited  = array('jpg', 'jpeg', 'png', 'gif');
                            $file_name = $_FILES['image']['name'];
                             $file_size = $_FILES['image']['size'];
                            $file_temp = $_FILES['image']['tmp_name'];

                            $div = explode('.', $file_name); // The explode() function breaks a string into an array.//explode(separator,string,limit) 3 param..
                            $file_ext = strtolower(end($div));
                            $unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
                            $uploaded_image = "gallery_image/".$unique_image;
                            
                            
                if ($file_size >1048567) {
                    
                   $responseData['error'] = true;
                   $responseData['message'] = 'Image Size should be less then 1MB!';

                 }elseif (in_array($file_ext, $permited) === false) {
                     
                  $msg = "You can upload only:-".implode(', ', $permited)."";
                     
                  $responseData['error'] = true;
                  $responseData['message'] = $msg;

                } 
                else{
                
                        move_uploaded_file($file_temp, $uploaded_image);
                         $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
                          
                         $result = $db-> uploadGalleryImage($uploaded_image, $itemName, $price, $restID);
                        if ($result) {
                         
                           $responseData['error'] = false;
                           $responseData['message'] = 'Image Uploaded Successfuly !';
                         
                        }else {
                           $responseData['error'] = true;
                           $responseData['message'] = 'Image Uploaded Failed!';
                         
                       }
                        
             }
    
        $response->getBody()->write(json_encode($responseData));
    
});



// get single restaurant all gallery images
$app->get('/get_single_rest_gallery_images/{restaurantID}', function (Request $request, Response $response) {
    $restID = $request->getAttribute('restaurantID');
    $db = new DbOperation();
    $data = $db->getRestaurantGalleryImages($restID);
    $response->getBody()->write(json_encode(array("gallery" => $data)));
});



//delete restaurant gallery image
$app->delete('/delete_restaurnt_gallery_image/{id}', function (Request $request, Response $response) {
   $id = $request->getAttribute('id');
	if(!empty($id )){
	
	$db = new DbOperation();
	$responseData = array();

        if ($db->deleteGalleryImage($id)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Deleted Successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        }

        $response->getBody()->write(json_encode($responseData));
	}
     
});




//Upload authority profile picture
$app->post('/upload_authority_profile_image', function (Request $request, Response $response) {
    
         $requestData = $request->getParsedBody();
         $userID  = $requestData['userID'];
        
         $db = new DbOperation();
         $responseData = array();
         $permited  = array('jpg', 'jpeg', 'png', 'gif');
                            $file_name = $_FILES['image']['name'];
                             $file_size = $_FILES['image']['size'];
                            $file_temp = $_FILES['image']['tmp_name'];

                            $div = explode('.', $file_name); // The explode() function breaks a string into an array.//explode(separator,string,limit) 3 param..
                            $file_ext = strtolower(end($div));
                            $unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
                            $uploaded_image = "authority_profile_image/".$unique_image;
                            
                            
                if ($file_size >1048567) {
                    
                   $responseData['error'] = true;
                   $responseData['message'] = 'Image Size should be less then 1MB!';

                 }elseif (in_array($file_ext, $permited) === false) {
                    
                 $msg = "You can upload only:-".implode(', ', $permited)."";
                     
                  $responseData['error'] = true;
                  $responseData['message'] = $msg;

                } 
                else{
                    
                         move_uploaded_file($file_temp, $uploaded_image);
                         $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
                         
                
                    $result = $db-> uploadAuthorityProfileImage($uploaded_image, $userID);
                        if ($result) {
                                $responseData['error'] = false;
                                $responseData['message'] = 'Profile Image Upload Successfully.';
                         
                        }else {
                              $responseData['error'] = true;
                              $responseData['message'] = 'Profile Image Upload Failed !';
                          }
                }
                        
          
        $response->getBody()->write(json_encode($responseData));
    
});




// Update authority own profile information
$app->post('/update_authority_own_profile/{userID}', function (Request $request, Response $response) {
    
    if (isTheseParametersAvailable(array('name', 'phone','address'))) {
        $userID = $request->getAttribute('userID');

        $requestData = $request->getParsedBody();

        $name = $requestData['name'];
        $phone  = $requestData['phone'];
        $address = $requestData['address'];

        
        $db = new DbOperation();
	    $responseData = array();

        if ($db->updateAuthorityInfoByOwn($name , $phone, $address, $userID)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Updated successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Not updated';
        }

        $response->getBody()->write(json_encode($responseData));
    }
});




 // get single authority info
$app->get('/get_single_authority_full_information/{userID}/{activityOneOrTwo}', function (Request $request, Response $response) {
    $userID = $request->getAttribute('userID');
    $oneOrTwo = $request->getAttribute('activityOneOrTwo');
    
    $responseData = array();
	$db = new DbOperation();
	
	
	if($oneOrTwo == 1){ // 1 = Foodie Authority 
	
	     $responseData['data'] = $db->getSingleFoodieAuthorityInfo($userID); 
	     //$responseData['picture'] = $db->getSingleAuthorityImage($userID);
	     
	}else if($oneOrTwo == 2){ // 2 = Restaurant Authority
	
	     $responseData['data'] = $db->getSingleRestAuthorityInfo($userID);
         //$responseData['picture'] = $db->getSingleAuthorityImage($userID);
	}
	
   
    $response->getBody()->write(json_encode($responseData));
});



//advertise request By restaurant authority
$app->post('/advertise_request_by_restaurant_authority', function (Request $request, Response $response) {
    
       if (isTheseParametersAvailable(array('title', 'details', 'restaurantID'))) {
    
         $requestData = $request->getParsedBody();
         
         $title  = $requestData['title'];
         $details  = $requestData['details'];
         $restaurantID  = $requestData['restaurantID'];
        
         $db = new DbOperation();
         $responseData = array();
         $permited  = array('jpg', 'jpeg', 'png', 'gif');
                            $file_name = $_FILES['image']['name'];
                             $file_size = $_FILES['image']['size'];
                            $file_temp = $_FILES['image']['tmp_name'];

                            $div = explode('.', $file_name); // The explode() function breaks a string into an array.//explode(separator,string,limit) 3 param..
                            $file_ext = strtolower(end($div));
                            $unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
                            $uploaded_image = "advertise_image/".$unique_image;
                            
                            
                if ($file_size >1048567) {
                    
                   $responseData['error'] = true;
                   $responseData['message'] = 'Image Size should be less then 1MB!';

                 }elseif (in_array($file_ext, $permited) === false) {
                    
                 $msg = "You can upload only:-".implode(', ', $permited)."";
                     
                  $responseData['error'] = true;
                  $responseData['message'] = $msg;

                } 
                else{
                         move_uploaded_file($file_temp, $uploaded_image);
                         $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
                         
                    
                        $result = $db-> addNewAdvertiseRequest($title, $details, $uploaded_image, $restaurantID);
                        if ($result) {
                            
                              $responseData['error'] = false;
                              $responseData['message'] = 'Advertise Request Successful !';
                         
                        }else {
                            
                              $responseData['error'] = true;
                              $responseData['message'] = 'Request Failed !';
                          }
                }
    
            $response->getBody()->write(json_encode($responseData));
    }
    
});



 // get single restaurant advertise list
$app->get('/get_single_rest_advertise_list/{restaurantID}', function (Request $request, Response $response) {
    
    $restID = $request->getAttribute('restaurantID');
    $responseData = array();
	$db = new DbOperation();

    $data = $db->getSingleRestAdvertiselist($restID);
    $response->getBody()->write(json_encode(array("advertises" => $data)));
});





 // get all advertise list
$app->get('/get_all_advertise_list', function (Request $request, Response $response) {
   
    $responseData = array();
	$db = new DbOperation();

    $data = $db->getAllAdvertiselist();
    $response->getBody()->write(json_encode(array("advertises" => $data)));
});



// get all kind of advertise data
$app->get('/get_three_tab_advertise_data/{tabNumber}', function (Request $request, Response $response) {
  
    //$restID = $request->getAttribute('restaurantID');
    $tabNumber = $request->getAttribute('tabNumber');
    
    $responseData = array();
	$db = new DbOperation();
	
	
    if($tabNumber == 1){
       $data = $db->getOnlyNewAdvertiseRequestList();
        
    }else if($tabNumber == 2){
         $data = $db->getOnlyLiveAdvertiseList();
      
    }else if($tabNumber == 3){
         $data = $db->getOnlydisableAdvertiseList();
       
    }else{
        //   $responseData['message'] =  'Give proper Tab number ';
        //   $data = $responseData;
    }

    $response->write(json_encode(array("advertises" => $data)));
    
});


// Update advertise Live and Check status
$app->post('/update_advertise_live_or_check_status/{liveorcheck}', function (Request $request, Response $response) {
   
        $LiveorCheck = $request->getAttribute('liveorcheck');
        $requestData = $request->getParsedBody();
        $add_ID  = $requestData['add_ID'];
        
        $db = new DbOperation();
	    $responseData = array();
        
        if($LiveorCheck == 1){ // update live_status here
        
               $live  = $requestData['live'];
                
            if ($db->updateAdvertiseLiveStatus($add_ID, $live)) {
                $responseData['error'] = false;
                $responseData['message'] = 'Updated successfully';
            } else {
                $responseData['error'] = true;
                $responseData['message'] = 'Not updated';
            }
            
        }else if($LiveorCheck == 2){ // update check_status_here
        
               $check_status  = $requestData['check_status'];
               
               if ($db->updateAdvertiseCheckStatus($add_ID, $check_status)) {
                $responseData['error'] = false;
                $responseData['message'] = 'Updated successfully';
            } else {
                $responseData['error'] = true;
                $responseData['message'] = 'Not updated';
            }
               
               
        }else{
             $responseData['message'] = 'Plz give parameter as 1 or 2';
        }
        
        $response->getBody()->write(json_encode($responseData));

});


//Method to delete single addvertise 
$app->delete('/deleteSingelAdvertise/{add_ID}', function (Request $request, Response $response) {
   
    $add_ID = $request->getAttribute('add_ID');
	
	if(!empty($add_ID)){
	
	$db = new DbOperation();
	$responseData = array();

        if ($db->deleteSingleAdvertise($add_ID)) {
            $responseData['error'] = false;
            $responseData['message'] = 'Delete successfully';
        } else {
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        }

        $response->getBody()->write(json_encode($responseData));
	}
     
});











//  From Here Foodiez Main App Api















 //  register new user BY Facebook and Google Sign in 
$app->post('/register_new_user_by_fb_google_sign_in', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('name', 'email'))) {
        $requestData = $request->getParsedBody();

        $name = $requestData['name'];
        $email = $requestData['email'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db->registernewUserFromFbandGmail($name, $email);

        if ($result == USER_CREATED) {
            
            $responseData['error'] = false;
            $responseData['message'] = 'Registered successfully';
            $responseData['user'] =  $db->getUserInfoByEmail($email);
        } elseif ($result == USER_CREATION_FAILED) {
            $responseData['error'] = true;
            $responseData['message'] = 'Some error occurred';
        } elseif ($result == USER_EXIST) {
            $responseData['error'] = true;
            $responseData['message'] = 'This email already exist, please login';
            $responseData['user'] =  $db->getUserInfoByEmail($email);
        }

        $response->getBody()->write(json_encode($responseData));
    }
});




//  register new user BY Facebook Account Kit Sign in 
$app->post('/register_new_user_by_ac_kit', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('name', 'phone'))) {
        $requestData = $request->getParsedBody();

        $name = $requestData['name'];
        $phone = $requestData['phone'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db->registernewUserFromAccountKit($name, $phone);

        if ($result == USER_CREATED) {
            
            $responseData['error'] = false;
            $responseData['message'] = 'Registered successfully';
            $responseData['user'] =  $db->getUserInfoByPhone($phone);
        } elseif ($result == USER_CREATION_FAILED) {
            $responseData['error'] = true;
            $responseData['message'] = 'Some error occurred';
        } elseif ($result == USER_EXIST) {
            $responseData['error'] = true;
            $responseData['message'] = 'This Phone Number is Already Registered, Please login !';
             $responseData['user'] =  $db->getUserInfoByPhone($phone);
        }

        $response->getBody()->write(json_encode($responseData));
    }
});



// check phone number exists or not
$app->post('/is_the_phone_number_exists', function (Request $request, Response $response) {
   if (isTheseParametersAvailable(array('phone'))) {
        $requestData = $request->getParsedBody();

        $phone = $requestData['phone'];
	
	    $db = new DbOperation();
	    $responseData = array();

        if ($db->isUserExistCheckedByPhone($phone)) {
            $responseData['error'] = true;
            $responseData['message'] = 'This Phone Number is Already Registered !';
            $responseData['user'] =  $db->getUserInfoByPhone($phone);
        } else {
            $responseData['error'] = false;
            $responseData['message'] = 'This Phone Number is Not Registered !';
        }

        $response->getBody()->write(json_encode($responseData));
	}
     
});


 // get new offers 
$app->get('/get_new_offers_list', function (Request $request, Response $response) {
   
    $responseData = array();
	$db = new DbOperation();

    $data = $db->getOnlyLiveAdvertiseList();
    $response->getBody()->write(json_encode(array("advertises" => $data)));
});



// getting all restaurant information 
$app->get('/get_all_restaurant_information', function (Request $request, Response $response) {
  
    $db = new DbOperation();
    
    $data = $db->getAllRestaurantInfoForMainApp();
    $response->getBody()->write(json_encode(array("restaurants" => $data)));
    
});


//  give review by user
$app->post('/give_review_by_user', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('title', 'details', 'rate', 'userID', 'restaurantID'))) {
        $requestData = $request->getParsedBody();

        $title = $requestData['title'];
        $details = $requestData['details'];
        $rate = $requestData['rate'];
        $userID = $requestData['userID'];
        $restaurantID = $requestData['restaurantID'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db-> giveReviewByUser($title, $details, $rate, $userID, $restaurantID);

        if ($result) {
            $responseData['error'] = false;
            $responseData['message'] = 'Thank You, For Your Review !';
        }else{
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        } 

        $response->getBody()->write(json_encode($responseData));
    }
});



//  give rating to a restaurant
$app->post('/give_rating_to_a_restaurant', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('rating', 'food_rating', 'customer_serv_rating', 'value_for_mon_rating', 'interior_rating', 'userID', 'restaurantID'))) {
        $requestData = $request->getParsedBody();

        $rating                 = $requestData['rating'];
        $food_rating            = $requestData['food_rating'];
        $customer_serv_rating   = $requestData['customer_serv_rating'];
        $value_for_mon_rating   = $requestData['value_for_mon_rating'];
        $interior_rating       = $requestData['interior_rating'];
        $userID                 = $requestData['userID'];
        $restaurantID           = $requestData['restaurantID'];

        $db = new DbOperation();
        $responseData = array();
        
        
        if(!$db->isUserGiveAnyRatingBeforeThisRestaurant($userID, $restaurantID)){
            
            $result = $db-> giveRatingByUser($rating, $food_rating, $customer_serv_rating, $value_for_mon_rating, $interior_rating,   $userID, $restaurantID);

            if ($result) {
                $responseData['error'] = false;
                $responseData['message'] = 'Thank You, For Rating us !';
            }else{
                $responseData['error'] = true;
                $responseData['message'] = 'Something Wrong !';
            }     
        }else{
            
                $result = $db-> updateUserRating($rating, $food_rating, $customer_serv_rating, $value_for_mon_rating, $interior_rating,   $userID, $restaurantID);
    
                if ($result) {
                    $responseData['error'] = false;
                    $responseData['message'] = 'Your Previous Rating is Updated !';
                }else{
                    $responseData['error'] = true;
                    $responseData['message'] = 'Something Wrong !';
                }  
            
        }


        $response->getBody()->write(json_encode($responseData));
    }
});



 // get individual restaurant all reviews
$app->get('/get_individual_restaurant_all_reviews/{restaurantID}', function (Request $request, Response $response) {
    $restID = $request->getAttribute('restaurantID');
    $db = new DbOperation();
    $data = $db-> getIndividualRestAllReviews($restID);
    $response->getBody()->write(json_encode(array("reviews" => $data)));
});




 // get individual restaurant avg rating, total rating, total Review
$app->get('/get_individual_restaurant_review_and_rating_info/{restaurantID}', function (Request $request, Response $response) {
    
     $restID = $request->getAttribute('restaurantID');
     
     $db = new DbOperation();
     $responseData = array();
    
  
    $responseData['rate'] =  $db-> getAvgRatingOfaIndividualRestaurant($restID);
    $responseData['review'] =  $db-> getIndividualRestTotalreview($restID);
    
    $response->getBody()->write(json_encode($responseData));
});



 // get latest five Restaurant and Review 
$app->get('/get_latest_five_restaurant_and_review', function (Request $request, Response $response) {
    
     $db = new DbOperation();
     $responseData = array();
    
    $responseData['latestRestaurants'] =  $db-> getLatestFiveRestaurantinfo();
    $responseData['latestReview'] =  $db-> getLatestFiveReviews();
    
    $response->getBody()->write(json_encode($responseData));
});





 // get individual user profile information
$app->get('/get_individual_user_profile_information/{userID}', function (Request $request, Response $response) {
    
     $userID = $request->getAttribute('userID');
     
     $db = new DbOperation();
     $responseData = array();

    $data = $db-> getIndividualUserProfileInfo($userID);
    $response->getBody()->write(json_encode(array("info" => $data)));
});



//Upload user profile picture 
$app->post('/upload_user_profile_picture', function (Request $request, Response $response) {
    
         $requestData = $request->getParsedBody();
         $userID  = $requestData['userID'];
        
         $db = new DbOperation();
         $responseData = array();
         
         $permited  = array('jpg', 'jpeg', 'png', 'gif');
                            $file_name = $_FILES['profile_image']['name'];
                             $file_size = $_FILES['profile_image']['size'];
                            $file_temp = $_FILES['profile_image']['tmp_name'];

                            $div = explode('.', $file_name); // The explode() function breaks a string into an array.//explode(separator,string,limit) 3 param..
                            $file_ext = strtolower(end($div));
                            $unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
                            $uploaded_image = "user_profile/".$unique_image;
                            
                            
                if ($file_size >1048567) {
                    
                   $responseData['error'] = true;
                   $responseData['message'] = 'Image Size should be less then 1MB!';

                 }elseif (in_array($file_ext, $permited) === false) {
                     
                  $msg = "You can upload only:-".implode(', ', $permited)."";
                     
                  $responseData['error'] = true;
                  $responseData['message'] = $msg;

                } 
                else{
                
                        move_uploaded_file($file_temp, $uploaded_image);
                         $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
                          
                         $result = $db-> updateUserProfilePicture($uploaded_image,  $userID);
                        if ($result) {
                         
                           $responseData['error'] = false;
                           $responseData['message'] = 'Image Uploaded Successfuly !';
                         
                        }else {
                           $responseData['error'] = true;
                           $responseData['message'] = 'Image Uploaded Failed!';
                         
                       }
                        
             }
    
        $response->getBody()->write(json_encode($responseData));
    
});




// Restaurant Register Request 
$app->post('/restaurant_register_request', function (Request $request, Response $response) {
    
         $requestData = $request->getParsedBody();
         
         $restaurant_Name  = $requestData['restaurant_Name'];
         $rest_Address  = $requestData['rest_Address'];
         $rest_location  = $requestData['rest_location'];
         $rest_phone  = $requestData['rest_phone'];
         
         $auth_name  = $requestData['auth_name'];
         $auth_email  = $requestData['auth_email'];
         $auth_phone  = $requestData['auth_phone'];
         $auth_address  = $requestData['auth_address'];
         $auth_privilegeID  = $requestData['auth_privilegeID'];
         
        
         $db = new DbOperation();
         $responseData = array();
         
         $permited  = array('jpg', 'jpeg', 'png', 'gif');
                            $file_name = $_FILES['cred_image']['name'];
                             $file_size = $_FILES['cred_image']['size'];
                            $file_temp = $_FILES['cred_image']['tmp_name'];

                            $div = explode('.', $file_name); // The explode() function breaks a string into an array.//explode(separator,string,limit) 3 param..
                            $file_ext = strtolower(end($div));
                            $unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
                            $uploaded_image = "credentials_image/".$unique_image;
                            
                            
                if ($file_size >1048567) {
                    
                   $responseData['error'] = true;
                   $responseData['message'] = 'Image Size should be less then 1MB!';

                 }elseif (in_array($file_ext, $permited) === false) {
                     
                  $msg = "You can upload only:-".implode(', ', $permited)."";
                     
                  $responseData['error'] = true;
                  $responseData['message'] = $msg;

                } 
                else{
                    
                        move_uploaded_file($file_temp, $uploaded_image);
                         $uploaded_image = "http://www.capsulestudio.net/Juman/FRestApi/public/".$uploaded_image;
                          
                         $result = $db-> restaurantRegisterRequest($restaurant_Name, $rest_Address, $rest_location, $rest_phone, $auth_name, $auth_email, $auth_phone, $auth_address, $auth_privilegeID, $uploaded_image);
                        if ($result) {
                         
                           $responseData['error'] = false;
                           $responseData['message'] = 'Request Send Successfuly, We will Contact you soon  !';
                         
                        }else {
                           $responseData['error'] = true;
                           $responseData['message'] = 'Request Failed!';
                         
                       }
                        
             }
    
        $response->getBody()->write(json_encode($responseData));
    
});


//get all Restaurnt Registation Request 
$app->get('/get_all_restaurnt_registation_request/{tabNumber}', function (Request $request, Response $response) {
    
     $tabNumber = $request->getAttribute('tabNumber');
     $db = new DbOperation();
     
     
     if($tabNumber == 1){
         $data = $db->getAllRestaurantRegistationRequest();
     }else if($tabNumber == 2){
         $data = $db->getAllRestaurantRegistationRequestAccepted();
     }
    
    $response->getBody()->write(json_encode(array("requests" => $data)));
});



//  Register a Restaurant and Authority From Request
$app->post('/add_restaurant_and_authority_from_register_request', function (Request $request, Response $response) {
    if (isTheseParametersAvailable(array('reqID','restaurant_Name', 'rest_Address', 'rest_location', 'rest_phone', 'auth_name', 'auth_email', 'auth_phone', 'auth_address', 'auth_privilegeID'))) {
        
        $requestData = $request->getParsedBody();

         $reqID  = $requestData['reqID'];
         $restaurant_Name  = $requestData['restaurant_Name'];
         $rest_Address  = $requestData['rest_Address'];
         $rest_location  = $requestData['rest_location'];
         $rest_phone  = $requestData['rest_phone'];
         
         $auth_name  = $requestData['auth_name'];
         $auth_email  = $requestData['auth_email'];
         $auth_phone  = $requestData['auth_phone'];
         $auth_address  = $requestData['auth_address'];
         $auth_privilegeID  = $requestData['auth_privilegeID'];

        $db = new DbOperation();
        $responseData = array();

        $result = $db-> addNewRestAndRestAuthorityFromRequest($reqID, $restaurant_Name, $rest_Address, $rest_location, $rest_phone, $auth_name, $auth_email, $auth_phone, $auth_address, $auth_privilegeID);

        if ($result) {
            $responseData['error'] = false;
            $responseData['message'] = 'Restaurant And Authority Has Successfully Register !';
        }else{
            $responseData['error'] = true;
            $responseData['message'] = 'Something Wrong !';
        } 

        $response->getBody()->write(json_encode($responseData));
    }
});













//function to check parameters
function isTheseParametersAvailable($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echo json_encode($response);
        return false;
    }
    return true;
}


$app->run();