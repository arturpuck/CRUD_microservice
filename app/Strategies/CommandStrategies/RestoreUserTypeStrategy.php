<?php

namespace App\Strategies\CommandStrategies;

use App\UserType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class RestoreUserTypeStrategy 
{
    private $rules = [
      'id' => 'required|int|exists:user_types'
    ];

    public function command(Request $request): JsonResponse
    {
          try 
          {
              return $this->tryTorestoreUserType($request);    
          } 
          catch (\Exception $e) 
          {
             Log::debug($e->getMessage());
            return response()->json(
                ['content' => [], 'error_messages' => ['error' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);    
          }  
    }

    private function tryToRestoreUserType(Request $request) : JsonResponse
    {
       
       $validator = Validator::make($request->all(), $this->rules);

       if($validator->fails())
       {
            Log::debug($validator->errors()->toJson());
            return response()->json(
                ['content' => [], 'error_messages' => $validator->errors()], Response::HTTP_BAD_REQUEST
            );
        } 
        else 
        {
            $userType = $this->restoreUserType($request);
            return response()->json(
                ['content' => ['user_type' => $userType], 'error_messages' => []], Response::HTTP_OK
            );
        } 
    }

    private function restoreUserType(Request $request)
    {
        $userType = UserType::onlyTrashed()->where('id',$request->get('id'))->get()->first();

        if($userType === null)
        {
          throw new \Exception("The user is not soft deleted");
        }
        
        $userType->restore();
        return $userType;
    }
   
}