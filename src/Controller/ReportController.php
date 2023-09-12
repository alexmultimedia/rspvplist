<?php

/**
 * @file
 * Provide sit administratos with a list of all the RSVP LIst singups
 * so they know who is attending their events
 */

 namespace Drupal\rsvplist\Controller;

 use Drupal\Core\Controller\ControllerBase;
 use Drupal\Core\Database\Database;
 use PDO;


 class ReportController extends ControllerBase {

    /**
     * Gets and return all RSVPs for all nodes
     * These are returned as an associateive array, with each row
     * containing the username, the node title, and email of RSVP
     * 
     * @return array[null
     */
    public function load(){
        try {
          // ulr en drupal: introduction-to-dynamic-queries
          $database = \Drupal::database(); // Se obtiene una instancia del objeto BBDD utilizando el servicio global Drupal::database
          $select_query = $database->select('rsvplist','r'); // Hago un SELECT de la tabla rsvplist y le damos alias 'r'

          $select_query->fields('r', ['id','mail','created']); 
          // Join the user table, so we canget entry creators username.
          $select_query->join('users_field_data', 'u', 'r.uid = u.uid');
          //Join the node table, so we can get the events name
          $select_query->join('node_field_data', 'n', 'r.nid = n.nid');

          // Select these specific fields for the output
          $select_query->addField('u', 'name', 'username'); //  se está agregando un campo llamado "username" a la consulta SELECT y se está obteniendo de la tabla "users_field_data". El campo "username" está siendo nombrado así en la consulta pero en realidad se está obteniendo el valor del campo "name" de la tabla "users_field_data".
          $select_query->addField('n', 'title'); // agrega el campo title de la tabla node_field_data a la lista de campos seleccionados en la consulta. Este campo contiene el título del evento al que se ha inscrito un usuario en la tabla rsvplist.
          $select_query->addField('r', 'mail'); //  agrega el campo mail de la tabla rsvplist a la lista de campos seleccionados en la consulta. Este campo contiene la dirección de correo electrónico del usuario que se ha inscrito en el evento.

          /*En Drupal 9, la línea de código $select_query = $database->select('rsvplist','u'); se utiliza 
          para realizar una consulta SELECT a la tabla "rsvplist" de la base de datos. La consulta SELECT 
          se construye utilizando el objeto $select_query devuelto por el método select() del objeto $database.
        El primer argumento pasado al método select() es el nombre de la tabla que se va a consultar, 
        en este caso "rsvplist". El segundo argumento es un alias opcional que se utilizará para la tabla,
        en este caso "u". El alias se utiliza para simplificar la sintaxis de la consulta, y también se
        puede utilizar para unir la tabla con otras tablas en la misma consulta.
        Una vez que se ha creado el objeto de consulta, se pueden agregar campos adicionales utilizando 
        el método addField(), y se pueden agregar cláusulas WHERE y JOIN utilizando los métodos condition() y
        join() respectivamente. Finalmente, se ejecuta la consulta utilizando el método execute() para
        obtener los resultados.
*/
          // note that fetchAll() and fetchAssoc() will, by default, fetch using
          // whatever fetch node was set the query
          // (numeric array, associative array, or object).
          // Fetches can bemodified by passing in new fetch node constant.
          // For fetchAll(), it is the first parameter,
          // https://www.drupal.org/docs/8/api/database-api/result-sets
          //  https://www.php.net/manual/en/phestatement.fetch.php

          $entries = $select_query->execute()->fetchAll(\PDO::FETCH_ASSOC);
          //$entries = $select_query->execute()->fetchAllAssoc('id',\PDO::FETCH_ASSOC); // le dice que me de todos los resultados como Array Asociativo
          var_dump($entries);
          //Return the associative array of RSVPList entrie.
          //return $entries;

          
        // Construir un array renderizado para mostrar los datos en la pantalla
        $rows = [];
        foreach ($entries as $entry) {
            $rows[] = [
                'username' => $entry['username'],
                'title' => $entry['title'],
                'mail' => $entry['mail'],
            ];
        }

        // Construir una tabla renderizada para mostrar los datos
        $table = [
            '#theme' => 'table',
            '#header' => [
                'Username',
                'Title',
                'Email',
            ],
            '#rows' => $rows,
        ];

        // Devolver el array renderizado de la tabla
        return $table;
          
         // report($entries);

    }
    catch (\Exception $e){
            // Display a user-friendly error
            \Drupal::messenger()->addStatus(
                t('Unable to acces the datbase at this time. PLease try again later')
            );
            return NULL;
    }
 }


 



  }