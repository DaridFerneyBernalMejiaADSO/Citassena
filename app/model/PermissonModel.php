<?php

namespace Adso\model;

use Adso\libs\Model;

/**
 * Clase PermissonModel
 *
 * Esta clase representa un modelo de datos para la gestión de permisos en la aplicación.
 *
 * @package Adso\model
 */
class PermissonModel extends Model
{
  /**
   * @var string $tabla Nombre de la tabla de la base de datos asociada a esta clase.
   */
  private $tabla = "permissions";

  function __construct()
  {
    /**
     * Constructor de la clase PermissonModel.
     * Inicializa una instancia de la clase y llama al constructor de la clase base (Model).
     */
    parent::__construct();
  }
  /**
   * Obtiene todos los registros de permisos de la base de datos.
   *
   * @return array|bool Arreglo de registros de permisos o `false` en caso de error.
   */
  function getPermisson()
  { // Comentario descriptivo de la función
    $this->connection = $this->db->getConnection(); //Establece la conexión a la base de datos
    $data = $this->select($this->tabla); //Realiza una consulta a la base de datos y guarda los resultados en la variable $data
    $this->connection = $this->db->closConnection(); //Cierra la conexión a la base de datos
    return $data; //Devuelve los datos obtenidos de la consulta
  }
  /**
   * Obtiene un registro de permisos por su ID.
   *
   * @param int $permisos El ID del permiso que se desea obtener.
   * @return array|bool Registro del permiso o `false` en caso de error.
   */
  function getId($permisos)
  {
    $this->connection = $this->db->getConnection(); //Establece conexión a la base de datos
    $data = $this->getDataById($this->tabla, $permisos); // Obtiene datos según el ID y los permisos
    $this->connection = $this->db->closConnection(); //Cierra la conexión a la base de datos
    return $data; //Devuelve los datos obtenidos
  }
  /**
   * Almacena un nuevo registro de permisos en la base de datos.
   *
   * @param array $permisos Datos del permiso que se desea almacenar.
   */
  function storage($permisos)
  {
    $this->connection = $this->db->getConnection(); //Establece conexión a la base de datos
    $data = $this->insert($this->tabla, $permisos); //Inserta los datos proporcionados en la tabla especifica
    $this->connection = $this->db->closConnection(); //Cierra la conexión a la base de datos
  }
  /**
   * Actualiza un registro de permisos en la base de datos.
   *
   * @param array $permisos Datos del permiso que se desea actualizar.
   * @return bool `true` si la actualización fue exitosa, `false` en caso de error.
   */
  function updatePermisson($permisos)
  {
    $this->connection = $this->db->getConnection(); //Establece conexión a la base de datos
    $data = $this->update($this->tabla, $permisos); //Llama a la función actualizar
    $this->connection = $this->db->closConnection(); //Cierra la conexión a la base de datos
    return $data; //Devuelve los datos resultantes de la actualización
  }

  /**
   * Elimina un registro de permisos por su ID.
   *
   * @param int $id El ID del permiso que se desea eliminar.
   * @return bool `true` si la eliminación fue exitosa, `false` en caso de error.
   */
  function deletePermisson($id)
  {
    $this->connection = $this->db->getConnection(); //Establece conexión a la base de datos
    $data = $this->delete($this->tabla, $id); //Realiza la operación de eliminación utilizando el metodo delete
    $this->connection = $this->db->closConnection(); //Cierra la conexión a la base de datos
  }
}
