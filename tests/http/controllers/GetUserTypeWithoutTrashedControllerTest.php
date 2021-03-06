<?php

use App\UserType;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutEvents;
use Laravel\Lumen\Testing\WithoutMiddleware;


class GetUserTypeWithoutTrashedControllerTest extends TestCase
{
    use WithoutEvents;
    use WithoutMiddleware;
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $userType = new UserType();
        $userType->__set('name', 'I am not soft deleted');
        $userType->save();

        $userType = new UserType();
        $userType->__set('name', 'I am soft deleted');
        $userType->save();
        $userType->delete();
    }

    public function testGetUntrashedUser() : void
    {
        // given
        $id = 1;
        // when
        $result = $this->get('/user-type/without-trashed/'.$id);
        // then
        $result->seeStatusCode(Response::HTTP_OK);
        $result->seeJson(['name' => 'I am not soft deleted']);
    }

    public function testGetTrashedUser() : void
    {
        $id = 2;

        $response = ['content' => [], 'error_messages' => ['error' => 'The selected id is invalid.']];
    
        $result = $this->get('/user-type/without-trashed/'.$id); 
        $result->seeStatusCode(Response::HTTP_BAD_REQUEST);
        $result->seeJson($response);
    }

     public function testGetUserWhichDoesNotExist() : void
    {
        $id = 2999;

        $response = [
            'content' => [], 'error_messages' => [
                'id' => ['The selected id is invalid.']
            ],
        ];
    
        $result = $this->get('/user-type/without-trashed/'.$id); 
        $result->seeStatusCode(Response::HTTP_BAD_REQUEST);
        $result->seeJson($response);
    }

    public function testGetUserWhithIdThatIsNotInteger() : void
    {
        $id = "I am not integer";

        $response = [
            'content' => [], 'error_messages' => [
                'id' => ['The id must be an integer.']
            ],
        ];
    
        $result = $this->get('/user-type/without-trashed/'.$id); 
        $result->seeStatusCode(Response::HTTP_BAD_REQUEST);
        $result->seeJson($response);
    }

    
}
