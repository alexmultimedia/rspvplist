<?php

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use PDO;

/**
 * An example controller.
 */
class ExampleController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */

       /**
     * Gets and return all RSVPs for all nodes
     * These are returned as an associateive array, with each row
     * containing the username, the node title, and email of RSVP
     * 
     * @return array[null
     */

   public function load(){
    
    try {
 
 // Realizar la consulta.
  $database = \Drupal::database();
  $select_query = $database->select('rsvplist','u');  // Hago una QUERY estática extendiendo desde DATABASE
  $select_query->fields('u', ['id','mail','created']);

  $results = $select_query->execute()->fetchAllAssoc('id',\PDO::FETCH_ASSOC);

  /*fetchAllAssoc() es un método de la clase Drupal\Core\Database\Statement que pertenece al sistema de 
  base de datos de Drupal. Este método se utiliza para recuperar todos los resultados de una consulta como 
  una matriz de matrices asociativas. Cada fila de la consulta se convierte en una matriz asociativa que 
  contiene los nombres de las columnas y sus valores correspondientes.

  La sintaxis del método es la siguiente:
  fetchAllAssoc($key_index = null, $fetch_options = array());

  El primer parámetro, $key_index, es opcional y se utiliza para especificar el nombre de la columna que se
  utilizará como clave para el arreglo asociativo. Si no se especifica ningún parámetro, el método devolverá un arreglo numerado.
  El segundo parámetro, $fetch_options, también es opcional y se utiliza para especificar opciones adicionales para la recuperación de resultados, como el modo de recuperación de datos y el estilo de conversión de cadenas. Este parámetro se pasa como una matriz de opciones con valores predeterminados.
  En resumen, fetchAllAssoc() es un método útil para recuperar los resultados de una consulta de base de datos y manipularlos de forma programática en PHP.

  En este caso devuelve:
  array(3) {
  [1]=>
  array(3) {
    ["id"]=>
    string(1) "1"
    ["mail"]=>
    string(22) "alexwebmedia@gmail.com"
    ["created"]=>
    string(10) "1676575428"
  }
  [2]=>
  array(3) {
    ["id"]=>
    string(1) "2"
    ["mail"]=>
    string(22) "info@alexmultimedia.es"
    ["created"]=>
    string(10) "1676575647"
  }
  [3]=>
  array(3) {
    ["id"]=>
    string(1) "3"
    ["mail"]=>
    string(18) "alex@tuflamenco.es"
    ["created"]=>
    string(10) "1676575657"
  }
}

  */

  foreach ($results as $record) {
    // DO something with each $record object
    // Display the name of every person in the result set
   \Drupal::messenger()->addMessage(t("@person in the list:",
                                       ['@person' => $record->mail])); // Lanza un mensaje con  las personas en la lista
                                      }
  
  /* CREA LA TABLA */

  $content = []; 

  $content['message'] = [
    '#markup' => t('Bellow is a list of all Event RSVPs including username,
      email adress and the name of the event they will be attending.'),
  ];


$headers = [ // Haaders de la tabla DRUPAL
    t('Id'),
    t('Mail'),
    t('Created'),
];

$content['table'] = [
  '#type' => 'table',
  '#header' => $headers,
  '#rows' => $results,
  '#empty' => t('No entries available'),
];

var_dump($results);

/*$propiedades = get_object_vars($objeto);
print_r($propiedades);*/

$content['#cache']['max-age'] = 0; // No lo mete en cache para que nos de los datos actualizados siempre

// Return the populate render array.
return $content;


}
catch (\Exception $e){
        // Display a user-friendly error
        \Drupal::messenger()->addStatus(
            t('Unable to acces the database at this time. PLease try again later')
        );
        return NULL;
}
}

/**
*  Creates the RSVOList report page.
*
* @return array
*  Render array for the RSVPList report output.
*/

/*


*/
}
