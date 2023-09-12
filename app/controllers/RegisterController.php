<?php

namespace Adso\controllers;

use Adso\Libs\controller;
use Adso\servicios\Transacciones;

class RegisterController extends controller
{
    protected $model;
    protected $servicio;

    function __construct()
    {
        $this->model = $this->model("User");
        $this->servicio = new Transacciones();
    }

    function index()
    {
        $data = [
            "titulo" => "Registro",
            "subtitulo" => "Formulario de registro"
        ];

        $this->view('register', $data, 'auth');
    }

    function validate()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //regitrar el nuevousuario en el sistema
            $errores = [];

            $name = $_POST['first_name'] ?? '';
            $last = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $pass = $_POST['password'] ?? '';
            $pass2 = $_POST['password_confirm'] ?? '';

            if ($name == "") {
                $errores['name_error'] = "El nombre no esta definido";
            }
            if ($last == "") {
                $errores['last_error'] = "El apellido no esta definido";
            }
            if ($email == "") {
                $errores['mail_error'] = "El correo no esta definido";
            }
            if ($phone == "") {
                $errores['phone_error'] = "El ceular no esta definido";
            }
            if ($pass == "") {
                $errores['pass_error'] = "La contraseña no esta definida";
            }
            if ($pass != $pass2) {
                $errores['verify_error'] = "La contraseña no coincide";
            }
            if (strlen($name) > 50) {
                $errores['name_error'] = "El nombre excede el limite de caracteres";
            }
            if (strlen($last) > 50) {
                $errores['last_error'] = "El apellido excede el limite de caracteres";
            }
            if (strlen($email) > 50) {
                $errores['mail_error'] = "El correo excede el limite de caracteres";
            }
            if (strlen($phone) > 15) {
                $errores['phone_error'] = "El ceular excede el limite de caracteres";
            }
            if (strlen($pass) > 50) {
                $errores['pass_error'] = "La contraseña excede el limite de caracteres";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores['mail_error'] =  "El correo no es valido";
            }

            if (empty($errores)) {
                $valores = [
                    "user" => [
                        "user_name" => $name,
                        "email" => $email,
                        "password" => $pass
                    ],
                    "profile" =>[
                        "first_name" => $name,
                        "last_name" => $last,
                        "phone" => $phone,
                        "user_id" => null
                    ]
                ];

                $transaccion =  $this->servicio->trsRegistro($valores);
                
            } else {
                $data = [
                    "errors" => $errores
                ];
                $this->view("register", $data, "auth");
            }
        }
    }

    function email()
    {
        $response = array(
            'status'    => false,
            'data'      => array(),
            'message'   => 'Esta intentando acceder a informaión privada'
        );
        //Validamos que la solicitud sea por POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $request = json_decode(file_get_contents("php://input"));
            //Tomamos el atributo correo que se envio codificado
            //De igual forma pudo llegar otro atribuito o varios atributos
            $email = $request->email;
            //Consultamos con el modelo y pasamos el correo
            $data = $this->model->getEmail($email);
            //Preguntamos si nos llega algun dato de la consulta

            if ($data) {
                $response['status']  = true;
                $response['data']    = array(
                    'email' => $data
                );
                $response['message'] = 'el correo se encuentra registrado';
            } else {
                $response['status']  = false;
                $response['data']    = array(
                    'email' => $data
                );
                $response['message'] = 'el correo se encuentra disponible';
            }
        }
        //Codificamos la respuesta al cliente
        echo json_encode($response);
    }
}
