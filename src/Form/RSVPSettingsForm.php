<?php

/**
 * @file
 * Contains th setting for administering the RSVP From
 * Nos conectamos con al APP de configuración de DRUPAL para cambiar la configuración de nuestro módulo
 */

 namespace Drupal\rsvplist\Form;

 use Drupal\Core\Form\ConfigFormBase; // Es una clase donde se puede cambiar la configuración de los módulos por un form
 use Drupal\Core\Form\FormStateInterface;

 class RSVPSettingsForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() { // Devuelve el ID del From que queramos
        return 'rsvplist_admin_settings';
    }

    /**
     *  {@inheritdoc}
     */
    protected function getEditableConfigNames() { // Devuelve un array de los nombre de configuración que serán 
        //configurables ( es un método de la clase C)
        return [
            'rsvplist.settings',
        ];
    }

    /**
     *  {@inheritdoc}
     */
    
    public function buildForm(array $form, FormStateInterface $form_state) {
        $types = node_type_get_names(); // Devuelve los tipos de nodos de contenidos que existe en el sitio como
        // Array de Strings que utilizamos como opciones de configuración
        $config = $this->config('rsvplist.settings'); // Método de la clase ConfigFormBase que extendemos, 
        //que devuelve el nombre el objeto configurable que declaramos anteiormente en getEditableConfigNames()
        $form['rsvplist_types'] = [
            '#type' => 'checkboxes',
            '#title' => $this->t('The content types to enable RSVP collection for'),
            '#default_value' => $config->get('allowed_types'), // Utilizamos el objeto creado anteriormente
            '#options' => $types,
            '#description' => $this->t('On the specified node types, an RSVP option will be avalable and 
            can be displayes while thenode is being edited.'),
        ];

        return parent::buildForm($form, $form_state); // Como extendemos de la clase ConfigFormBase que ya pone el btn
        //no tenemos que poner el btn SUBMIT, aqui le enviamos los datos del From a la clase buildForm y ella hace lo del btn Submit
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $selected_allowed_types = array_filter($form_state->getValue('rsvplist_types')); // Borra cadenas vacías de las opciones y las pone en un Array
        sort($selected_allowed_types); // Ordenamos las opciones orden alfabético

        $this->config('rsvplist.settings') // Cargamos el objeto de configuracion
          ->set('allowed_types', $selected_allowed_types) // Cargamos los typo permitidos
          ->save(); // Guardamos la configuración en DRUPAL

        parent::submitForm($form, $form_state); // Le enviamos los datos a la clase padre submitForm
    }
 }


