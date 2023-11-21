<?php

namespace Adso\controllers;

use Adso\libs\Controller; // Corregido "controller" a "Controller"
use Adso\libs\Helper;

class RolesController extends Controller
{

  protected $model;
  protected $model2;
  protected $model3;

  function __construct()
  {
    $this->model = $this->model("Role"); // Crear una instancia del modelo "Role"
    $this->model2 = $this->model("Permisson");
    $this->model3 = $this->model("Permisson_Role");
  }

  /**
   * Método para mostrar la lista de roles.
   */
  function index()
  {
    $roles = $this->model->getRoles(); // Obtener roles desde el modelo

    $data = [
      "titulo" => "Roles",
      "subtitulo" => "Lista de roles",
      "menu" => true,
      "roles" => $roles
    ];

    $this->view('rol/index', $data, 'app'); // Renderizar la vista de lista de roles
  }

  /**
   * Método para mostrar el formulario de creación de roles.
   */
  function create()
  {

    $data = [
      "titulo" => "Roles",
      "subtitulo" => "Creación de roles",
      "menu" => true
    ];

    $this->view("rol/create", $data, "app"); // Renderizar la vista de creación de roles
  }

  /**
   * Método para procesar el formulario de creación de roles.
   */
  function storage()
  {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $errores = [];
      $roles = $_POST['rol_name']; // Obtener el nombre del rol desde el formulario

      if ($roles == "") {
        $errores["rol_error"] = "El rol está vacío";
      }
      if (strlen($roles) > 50) {
        $errores["rol_error"] = "El rol supera el límite de caracteres";
      }

      if (empty($errores)) {

        $valores = [
          "name_role" => $roles
        ];

        $this->model->storage($valores); // Almacenar el nuevo rol en la base de datos

        header("Location: " . URL . "/roles"); // Redireccionar a la lista de roles

      } else {
        $data = [
          "titulo" => "Roles",
          "subtitulo" => "Creación de roles",
          "menu" => true,
          "errors" => $errores
        ];

        $this->view("rol/create", $data, "app"); // Renderizar la vista de creación de roles con errores
      }
    } else {
      // Código para manejar solicitudes GET
    }
  }

  /**
   * Método para mostrar el formulario de edición de un rol.
   *
   * @param string $id El ID del rol a editar.
   */
  function editar($id)
  {

    $save = $this->model->getRole(["id_role" => Helper::decrypt($id)]); // Obtener detalles del rol a editar

    $data = [
      "titulo" => "Roles",
      "subtitulo" => "Actualización de roles",
      "menu" => true,
      "data" => $save,
      "id" => $id
    ];

    $this->view("rol/update", $data, "app"); // Renderizar la vista de actualización de roles
  }

  /**
   * Método para procesar el formulario de edición de un rol.
   *
   * @param string $id El ID del rol a editar.
   */
  function update($id)
  {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $errores = [];
      $roles = $_POST['rol_name']; // Obtener el nombre del rol desde el formulario

      if ($roles == "") {
        $errores["rol_error"] = "El rol está vacío";
      }
      if (strlen($roles) > 50) {
        $errores["rol_error"] = "El rol supera el límite de caracteres";
      }

      if (empty($errores)) {

        $valores = [
          "name_role" => $roles,
          "id_role" => Helper::decrypt($id)
        ];

        $this->model->updateRole($valores); // Actualizar el rol en la base de datos

    function update($id)
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $errores = [];
            $roles = $_POST['rol_name'];

            if ($roles == "") {
                $errores["rol_error"] = "El rol esta vacio";
            }
            if (strlen($roles) > 50) {
                $errores["rol_error"] = "El rol supera el limite de caracteres";
            }

            if (empty($errores)) {

                $valores = [
                    "name_role" => $roles,
                    "id_role" => Helper::decrypt($id)
                ];

                $this->model->updateRole($valores);

                header("location:" . URL . "/roles");
            } else {
                $data = [
                    "titulo" => "Roles",
                    "subtitulo" => "Creacion de roles",
                    "menu" => true,
                    "errors" => $errores
                ];

                $this->view("rol/create", $data, "app");
            }
        } else {
        }
    }

    function delete($id)
    {

        $this->model->deleteRole(["id_role" => Helper::decrypt($id)]);
        header("Location: " . URL . "/roles");


        $data = [
            "titulo" => "Roles",
            "subtitulo" => "Eliminación de roles",
            "menu" => true,
            "id" => $id
        ];
    }

    /**
     * Este metodo es para administrar y asignar los permisos a cada rol
     * 
     * @access public
     * @param int $id
     * @return void
     */
    function manage($id)
    {
      //  $this->model = $this->model("Role");
      //   $this->model2 = $this->model("Permisson");
      //   $this->model3 = $this->model("Permisson_Role");
        /*Usa la el metodo getRole de RoleModel que a su vez usa el metodo getRowById 
        de Model que obtiene una fila por id
        */
        $role = $this->model->getRole(["id_role" => Helper::decrypt($id)]); 
        /**Usa el metodo getPermisson de PermissonModel que a su vez usa el metodo select de 
         * Model que obtiene todos los datos de una tabla en especifico
        */
        $permit = $this->model2->getPermisson();
        /*Usa el metodo selectPermits de Permisson_RoleModel que a su vez usa el metodo getRowById 
        de Model que obtiene una fila por id
        */
        $permit_role = $this->model3->selectPermits(["id_role_fk" => $role["id_role"]]);
        
        $data = [
            "titulo" => "Roles",
            "subtitulo" => "Administrar permisos",
            "menu" => true,
            "rol" => $role,
            "permit" => $permit,
            "permit_role" => $permit_role //array
        ];

        $this->view("rol/update", $data, "app"); // Renderizar la vista de actualización de roles con errores
      }
    } else {
      // Código para manejar solicitudes GET
    }
    /**
     * Este metodo es para asignarle los permisos a cada rol
     * 
     * @access public
     * @return void
     */
    function assing(){
      // $this->model = $this->model("Role");
      //   $this->model2 = $this->model("Permisson");
      //   $this->model3 = $this->model("Permisson_Role");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $role = $_POST['rol'];
            $permits = $_POST['permisos'];
            // print_r($permits);
            // die();
            $valores = [
                "id_role_fk" => $role,
                "id_permisson_fk" => $permits
            ];
            $this->model3->storage($valores);
            header("Location: " . URL . "/roles");
        }
    }
  }

}
}