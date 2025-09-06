<?php
namespace App\Traits;

use Kreait\Firebase\Factory;

use App\Model\Hyvikk;

trait FirebasePassword
{

     public function createStaticUser($email,$password)
    {
        if (Hyvikk::api('firebase_url') != null) {

            $firebase = (new \Kreait\Firebase\Factory)
                ->withServiceAccount(storage_path('firebase/'.Hyvikk::api('firebase_url')))
                ->createAuth();

            
            $email = $email;
            $password = $password;

          

            try {
                // Check if user already exists
                $user = $firebase->getUserByEmail($email);
                return response()->json(['message' => 'User already exists.', 'uid' => $user->uid]);
            } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                // User does not exist, create a new one
                try {
                    $newUser = $firebase->createUser([
                        'email' => $email,
                        'password' => $password,
                    ]);

                    return response()->json([
                        'message' => 'User created successfully.',
                        'uid' => $newUser->uid,
                        'email' => $newUser->email
                    ]);
                } catch (\Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()], 500);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
       }

        
    }




    public function newpassword($email,$newpassword)
    {

        if(Hyvikk::api('firebase_url') != NULL)
        {
            $firebase = (new Factory)
            ->withServiceAccount(storage_path('firebase/'.Hyvikk::api('firebase_url'))) // Ensure this file is correct
            ->createAuth();
        
            $email = $email;
            $newPassword = $newpassword;
            try {
                // Get user by email
                $user = $firebase->getUserByEmail($email);
                // Update the password directly
                $firebase->changeUserPassword($user->uid, $newPassword);
                //  response()->json([
                //     'message' => 'Password updated successfully',
                //     'user_id' => $user->uid,
                // ]);
            } catch (\Exception $e) {
                //response()->json(['error' => $e->getMessage()], 400);
            }
        }

       
    }

    public function deleteUser($email)
    {

        if(Hyvikk::api('firebase_url') != NULL)
        {

                $firebase = (new Factory)
                ->withServiceAccount(storage_path('firebase/'.Hyvikk::api('firebase_url'))) // Ensure this file is correct
                    ->createAuth();

                try {
                    // Get user by email
                    $user = $firebase->getUserByEmail($email);

                    // Delete user by UID
                    $firebase->deleteUser($user->uid);

                    // return response()->json([
                    //     'message' => 'User deleted successfully',
                    //     'user_id' => $user->uid,
                    // ]);
                } catch (\Exception $e) {
                    //return response()->json(['error' => $e->getMessage()], 400);
                }

         }
    }
   

}