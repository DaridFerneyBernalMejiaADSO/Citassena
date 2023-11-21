<?php

// Declaración del espacio de nombres y uso de clases
namespace Adso\controllers;

use Adso\Libs\Controller; // Importa la clase Controller del espacio de nombres Adso\Libs
use Adso\libs\Helper; // Importa la clase Helper del espacio de nombres Adso\libs
use Adso\libs\Session; // Importa la clase Session del espacio de nombres Adso\libs
use Adso\libs\Email; // Importa la clase Email del espacio de nombres Adso\libs
use Adso\libs\DateHelper; // Importa la clase DateHelper del espacio de nombres Adso\libs

/**
 * @package controllers
 * @author Jarminthon Enrique Rueda Larrota
 */
class LoginController extends Controller
{
    /**
     * @access protected
     * @var string
     */
    protected $model;

    // Constructor de la clase


    /**
     * Método contructor de la clase
     *
     * Metodo contructor, primera funcion que se ejecuta en la clase, en ella inicializamos la variable protegida $model 
     * instanciamos el modelo de usuario en esta variable y lo asignamos.
     *
     * @return void No retorna ningún valor.
     */
    function __construct()
    {

        //aca colocar  el codigo if (ses)

        

        $this->model = $this->model("User"); // Crea una instancia del modelo "User"

        /**
         * condicion que mira si la sesion esta iniciada, si es verdadero lo debe redirigin directamente a la vista admin
         */
        if((session_start() == true )){
            header('Location: ' . URL . '/admin'); 
        }
    }


    /**
     * Método para mostrar el formulario de inicio de sesión
     *
     * Esta funcion es el metodo predefinido que se ejecuta en todos los controladores declarado en nuestro 
     * enrutador core el cual nos lleva al metodo index de cada controlador al llegar a dicha clase de cada controlador
     *
     * @return void No retorna ningún valor.
     */
    function index()
    {
        if (isset($_COOKIE['data'])) {
            // Comprueba si existe una cookie llamada 'data'
            $data = $_COOKIE['data']; // Obtiene el valor de la cookie 'data'
            $data = Helper::decrypt($data); // Descifra el valor de la cookie utilizando el método 'decrypt' de la clase Helper
            $value = explode('|', $data); // Divide el valor descifrado en un array utilizando '|' como separador
            $user = $value[0]; // Obtiene el nombre de usuario del array
            $password = $value[1]; // Obtiene la contraseña del array

            $arra_data = [
                'user' => $user,
                'password' => $password,
                'remember' => 'on'
            ];
        } else {
            $arra_data = [];
        }

        // Prepara los datos para la vista
        $data = [
            "titulo" => "Login",
            "subtitulo" => "Formulario login",
            'data' => $arra_data // Incluye los datos en un array asociativo
        ];

        // Carga la vista 'login' con los datos proporcionados y el contexto 'auth'
        $this->view('login', $data, 'auth');
    }


    /**
     * Método para validar el formulario de inicio de sesión 
     *
     * Esta funcion se encarga de validar si llegan datos por el metodo post para realizar la autenticacion 
     * del usuario y la contraseña para llevar a cabo la autenticacion y iniciar una session tambien hacemos el redireccionamiento según sea 
     * su rol y permiso.
     *
     * @return void No retorna ningún valor.
     */
    function validate()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verifica si la solicitud se realizó utilizando el método POST

            $errorres = array(); // Inicializa un array para almacenar errores
            $user = $_POST['user'] ?? ''; // Obtiene el valor del campo de usuario del formulario, si no se proporciona, se establece como cadena vacía
            $password = $_POST['password'] ?? ''; // Obtiene el valor del campo de contraseña del formulario, si no se proporciona, se establece como cadena vacía
            $remember = isset($_POST['remember']) ? 'on' : 'off'; // Verifica si se ha marcado la casilla "recordar usuario"

            if ($user == "") {
                $errorres['user_error'] = "El usuario es requerido"; // Valida si el campo de usuario está vacío
            }
            if ($password == "") {
                $errorres['password_error'] = "La contraseña es requerida"; // Valida si el campo de contraseña está vacío
            }
            if (strlen($user) > 50) {
                $errorres['user_error'] = "El usuario excede el límite de caracteres"; // Valida si el usuario excede el límite de 50 caracteres
            }
            if (strlen($password) > 50) {
                $errorres['password_error'] = "La contraseña excede el límite de caracteres"; // Valida si la contraseña excede el límite de 50 caracteres
            }

            $value = $user . '|' . $password; // Combina el nombre de usuario y la contraseña con un '|' como separador
            $value = Helper::encrypt($value); // Encripta el valor combinado

            if ($remember == 'on') {
                // Si se selecciona "recordar usuario," establece una fecha de expiración en 1 semana
                $date = time() + (60 * 60 * 24 * 7);
            } else {
                // Si no se selecciona "recordar usuario," establece una fecha de expiración en el pasado (se expirará inmediatamente)
                $date = time() - 1;
            }

            // Almacena el valor en una cookie llamada "data" con la fecha de expiración especificada
            setcookie("data", $value, $date, URL);

            if (empty($errorres)) {
                // Si no hay errores de validación, verifica la autenticación en el modelo de usuario
                $data = $this->model->validate($user, $password);

                if (empty($data)) {
                    // Si no se encuentra una coincidencia en el modelo, se establece un error
                    $errorres['password_incorrect'] = "El usuario o contraseña son incorrectas";
                    $data = [
                        "titulo" => "Login",
                        "subtitulo" => "Formulario login",
                        "errors" => $errorres
                    ];
                    $this->view('login', $data, 'auth'); // Muestra el formulario de inicio de sesión con errores

                } else {
                    //Tomamos el resultado de la consulta
                    $sesion = new Session();
                    $sesion->loginStar($data);
                    $role = $data['id_role_fk'];
                    print_r($role);

                     header('Location: ' . URL . '/admin'); //Redireccionamiento de 


                     




                }
            } else {
                $data = [
                    "titulo" => "Login",
                    "subtitulo" => "Formulario login",
                    "errors" => $errorres
                ];
                $this->view('login', $data, 'auth');
            }
        } else {
            die("!Te pille, ingreso no permitido¡"); // si no hay ningun dato que llegue por el metodo de solicitud POST se ejecuta el die que acaba con el proceso y muestra el mensaje a continuacion 
        }
    }

    /**
     * Muestra una vista para permitir a los usuarios recuperar su contraseña.
     *
     * Esta función muestra un formulario que permite a los usuarios solicitar la recuperación de su contraseña.
     *
     * @return void No retorna ningún valor.
     */
    function forgetpassword()
    {
        // Se crea un arreglo asociativo con datos que serán utilizados en la vista.
        $data = [
            "titulo" => "Recuperar contraseña",
            "subtitulo" => "Formulario recuperar contraseña"
        ];

        // Se llama a una función 'view' que recibe 3 parametros la vista la data y el para mostrar la vista 'forget'
        // pasando los datos del arreglo '$data' y especificando que pertenece al grupo 'auth'.
        $this->view('forget', $data, 'auth');
    }


    /**
     * Funcion timeStamp para simular token
     *
     * Esta función simula una restriccion de tiempo al enviar el correo en la base de datos
     *
     * @return void No retorna ningún valor.
     */
    function timestamp($email, $id_user, $userModel)
    {
        $correo = new Email();

        $correo->sendEmail($email, Helper::encrypt($id_user));

        $userModel->createtime($id_user);
    }



    /**
     * Función para enviar correo electrónico y manejar la recuperación de contraseña.
     *
     * Esta función verifica si la solicitud es un método POST y realiza validaciones en el correo electrónico proporcionado.
     * En caso de errores o éxito en la validación, redirige al usuario a las vistas correspondientes.
     *
     * @return void No retorna ningún valor.
     */
    function sendEmail()
    {
        // Verifica si la solicitud es un método POST.
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errorres = array(); // Inicializa un arreglo para almacenar errores.

            // Obtiene el valor del campo 'email' desde el formulario, o una cadena vacía si no se proporciona.
            $email = $_POST['email'] ?? '';

            // Comprueba si el campo de correo electrónico está vacío y registra el error correspondiente si lo está.
            if ($email == "") {
                $errorres['email_empty'] = "El correo es requerido";
            }

            // Comprueba si el correo electrónico proporcionado tiene un formato válido y registra el error correspondiente si no lo tiene.
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorres['email_error'] = "El correo no es válido";
            }

            // Si no hay errores de validación en el correo electrónico.
            if (empty($errorres)) {
                // Valida el correo electrónico en el modelo y obtiene datos relacionados.
                $data = $this->model->validateEmail($email);

                // Si se encuentra el correo electrónico en la base de datos, realiza alguna acción y redirige al usuario a una vista específica.
                if (!empty($data)) {
                    $email = $data['email'];
                    // Realiza alguna acción, como registrar una marca de tiempo, que no está presente en el código proporcionado.

                    // Redirige al usuario a una vista llamada 'email_send'.
                    header('location:' . URL . "/login/emailSend");
                } else {
                    // Si el correo electrónico no se encuentra en la base de datos, muestra un error y redirige a la vista 'forget'.
                    $errorres['email_dontexist'] = "El correo no existe";
                    $data = [
                        "titulo" => "Recuperar contraseña",
                        "subtitulo" => "Formulario recuperar contraseña",
                        "errors" => $errorres
                    ];
                    $this->view('forget', $data, 'auth');
                }
            } else {
                // Si hay errores de validación en el correo electrónico, muestra la vista 'forget' con los errores correspondientes.
                $data = [
                    "titulo" => "Recuperar contraseña",
                    "subtitulo" => "Formulario recuperar contraseña",
                    "errors" => $errorres
                ];

                $this->view('forget', $data, 'auth');
            }
        } else {
            // Si la solicitud no es un método POST, muestra un mensaje de error.
            die("!Te pillé, ingreso no permitido¡");
        }
    }


    /**
     * Carga la vista 'email_send' para mostrar un mensaje de confirmación después de enviar un correo electrónico.
     *
     * Esta función se encarga de cargar una vista específica llamada 'email_send'. Define un arreglo $data que contiene información
     * para la vista, como el título y el subtítulo que se mostrarán en la página.
     *
     * @return void No retorna ningún valor.
     */
    function emailSend()
    {
        // Se define un arreglo $data que contiene información para la vista, como el título y el subtítulo.
        $data = [
            "titulo" => "Login",
            "subtitulo" => "Formulario login",
        ];

        // Carga la vista 'email_send' y pasa el arreglo $data y la plantilla 'auth' como parámetros.
        $this->view('email_send', $data, 'auth');
    }


    /**
     * Verifica si ha pasado un tiempo determinado desde la última verificación de un usuario.
     *
     * Esta función obtiene la fecha de la última verificación del usuario a través de un modelo y realiza cálculos para determinar si ha pasado suficiente tiempo desde entonces.
     *
     * @param int|string $id_user El ID del usuario para verificar el tiempo. Puede ser un entero o una cadena.
     * @return mixed Devuelve el resultado de la comparación de tiempo para determinar si ha pasado suficiente tiempo desde la última verificación.
     */
    function compare($id_user)
    {
        // Obtener la fecha de la última verificación del usuario a través del modelo.
        $fecha = $this->model->chekear($id_user);

        // Realizar cálculos de tiempo utilizando el helper DateHelper y la fecha obtenida.
        $data = DateHelper::timestamp($fecha);

        // Devolver el resultado de la comparación de tiempo para determinar si ha pasado suficiente tiempo desde la última verificación.
        return $data;
    }



    /**
     * Función para manejar la actualización de contraseñas de usuario.
     *
     * Esta función verifica si la solicitud es un formulario POST y realiza validaciones en los campos de contraseña.
     * Maneja la lógica de actualización de contraseña y redirige al usuario a diferentes vistas según el resultado.
     *
     * @param string $id El ID del usuario para actualizar la contraseña. Valor predeterminado: cadena vacía.
     * @return void No retorna ningún valor.
     */
    function updatepassword($id = "")
    {
        // Verifica si la solicitud es un formulario POST.
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errorres = array();
            $id = $_POST['id'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Realiza validaciones de contraseñas y comprueba la caducidad del enlace de recuperación.
            if ($password == "") {
                $errorres['password_error'] = "La contraseña es requerida";
            }
            if ($confirm_password == "") {
                $errorres['confirm_password'] = "La confirmación de la contraseña es requerida";
            }
            if ($password != $confirm_password) {
                $errorres['password_error'] = "La confirmación no coincide con su contraseña";
            }
            if ($this->compare(Helper::decrypt($id)) > 180) {
                $errorres['expire_error'] = "El enlace de recuperación ha expirado";
            }

            // Si hay errores de validación, muestra la vista 'update' con los errores correspondientes.
            if (!empty($errorres)) {
                $data = [
                    "titulo" => "Modificar contraseña",
                    "subtitulo" => "Formulario modificar contraseña",
                    "errors" => $errorres,
                    "data" => $id
                ];
                $this->view('update', $data, 'auth');
            } else {
                // Actualiza la contraseña del usuario en el modelo y redirige al usuario a la vista 'admin' si la actualización tiene éxito.
                $id = Helper::decrypt($id);
                if ($this->model->updatePassword($id, $password)) {
                    // Inicia sesión con la nueva contraseña y redirige al usuario a la vista 'admin'.
                    $sesion = new Session();
                    $sesion->loginStar($id);
                    header("Location:" . URL . "/admin");
                }
            }
        } else {
            // Si la solicitud no es un formulario POST, muestra la vista 'update' sin errores.
            $data = [
                "titulo" => "Modificar contraseña",
                "subtitulo" => "Formulario modificar contraseña",
                "errors" => [],
                "data" => $id
            ];
            $this->view('update', $data, 'auth');
        }
    }


}
