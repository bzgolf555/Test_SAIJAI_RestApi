<?php defined('BASEPATH') OR exit('No direct script access allowed');


require_once APPPATH . 'libraries/API_Controller.php';

class Api_Request extends API_Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function register(){
        header("Access-Control-Allow-Origin: *");
        
        $this->_apiConfig([
            'methods' => ['POST'],
        ]);

        $params = json_decode(file_get_contents('php://input'), TRUE);

        $userName = $params['userName'];
        $passWord = $params['passWord'];

        if ($userName && $passWord) {
            $res = $this->useful_model->get_where('user', 'userName', $userName)->result_array();
            if (count($res) == 0) {
                    $data = array(
                    'userName' => $userName ,
                    'passWord' => $passWord
                );
                $this->useful_model->_insert('user',$data);
                $this->api_return(
                    [
                        'status' => true,
                        "result" => [
                            'Message' => "Register Successful",
                        ],
                        
                    ],
                200);
            } else {
                $this->api_return(['status' => false,"result" => ['Message' => "Duplicate Username",],],400);
            }
        } else {
            $this->api_return(['status' => false,"result" => ['Message' => "Invalid Username Or Password",],],400);
        }
    }

    public function login()
    {
        header("Access-Control-Allow-Origin: *");

        $this->_apiConfig([
            'methods' => ['POST'],
        ]);

        $params = json_decode(file_get_contents('php://input'), TRUE);

        $userName = $params['userName'];
        $passWord = $params['passWord'];

        if ($userName && $passWord) {
            $res = $this->useful_model->get_where_custom('user', 'userName', $userName, 'passWord', $passWord)->result_array();
            if (count($res) != 0) {
                $payload = [
                    'userName' => $userName,
                    'passWord' => $passWord
                ];

                $this->load->library('authorization_token');

                $token = $this->authorization_token->generateToken($payload);

                $this->api_return(
                    [
                        'status' => true,
                        'userName' => $userName,
                        "result" => [
                            'token' => $token,
                            'imageProfile' => $res[0]['imageProfile'],
                        ],
                        
                    ],
                200);
            } else {
                $this->api_return(['status' => false,"result" => ['Message' => "Invalid Username Or Password",],],400);
            }
        }
    }

    public function uploadImage(){
        header("Access-Control-Allow-Origin: *");

        $user_data = $this->_apiConfig([
            'methods' => ['POST'],
            'requireAuthorization' => true,
        ]);

        if ($user_data) {
            $params = json_decode(file_get_contents('php://input'), TRUE);
            $image = $params['imageBase64'];

            $data = array(
                'imageProfile' => $image
            );
            $this->useful_model->_update('user','userName', $user_data['token_data']['userName'], $data);
            $this->api_return(
                [
                    'status' => true,
                    "result" => [
                        'imageProfile' => $image,
                        'Message' => "Upload Successful",
                    ],
                    
                ],
            200);
        }
    }
}