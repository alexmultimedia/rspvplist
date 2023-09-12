<?php

/**
 * @file
 * A form to collect an email adress for RSVP details
 */

 namespace Drupal\rsvplist\Form;

 use Drupal\Core\Form\Formbase;
 use Drupal\Core\Form\FormStateInterface;

 class RSVPForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId(){
        return 'rsvlist_email_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        // Attemp to get the fully loaded node object of the viewed page.
        $node = \Drupal::routeMatch()->getParameter('node');

        // Some pages may not be nodes though and $node will be NULL on those pages.
        // If a node was loaded, get the node id.
        if ( !(is_null($node)) ) {
            $nid = $node->id();
        }
        else{
            // If a node could not be liaded, default to 0;
            $nid = 0;
        }

        // Estalish the $form render array. It has an email text field, 
        // a submit button, and a hidden field containing the node IP.
        $form['email'] = [
            '#type' => 'textfield',
            '#size' => t('Email adress'),
            '#size' => 25,
            '#description' => t("HEY: We will send updates to the email adress you provide."),
            '#required' => TRUE,
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('RSVP'),
        ];
        $form['nid'] = [
            '#type' => 'hidden',
            '#value' => $nid,
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state){
        $value = $form_state->getValue('email');
        if ( !(\Drupal::service('email.validator')->isValid($value))){
          $form_state->setErrorByname('email',
            $this->t('It apears that %mail is not valid email. Please try 
             again', ['%mail' => $value]));

        }
    }



    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      $submitter_email = $form_state->getValue('email');
        $this->messenger()->addMessage(t("You entered @entry.",
            ['@entry' => $submitter_email]));

 try {
    // Begin Phase 1: initiate variables to save.

    // Get current user ID.
    $uid = \Drupal::currentUser()->id();

    // Demonstration for how to load a full user object if the current user.
    // This $full_user variable is not needed for this code,
    // but is shown for demonstration purposes-
    $full_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    // Obtain values as entered into the form.
    $nid = $form_state->getValue('nid');
    $email = $form_state->getValue('email');

    $current_time = \Drupal::time()->getRequestTime();
    
    // End Phase 1

    // Begin Phase 2: Save the values to the database

    // Start to build a query builder object $query
    // https://www.drupal.org/docs/8/api/database-api/insert-queries
    $query = \Drupal::database()->insert('rsvplist');

    // Specify the fields that the query will insert into.
    $query->fields([
        'uid',
        'nid',
        'mail',
        'created',
    ]);

    // Set the values of the fields we selected
    // note that tehy must be in same order as we defined them
    // in the $query->fields([...]) above.
    $query->values([
        $uid,
        $nid,
        $email,
        $current_time,
    ]);

    // Execute the query!
    // Drupal handles the exact syntax of the query automatically
    $query->execute();
    
    // End Phase 2


    // Begin Phase 3: Display a succes message

    // Provide the form submitter a nice message
    \Drupal::messenger()->addMessage(
        t('Thanks you for your RSVP, you are in the list for the event! Your Email has been saved in the DB!')
    );
    
    // ENd Phase 3
 }
 // Pero si hay un error introduciendo los datos enla BBDD hace esto:

 catch (\Exception $e) {
    \Drupal::messenger()->addError(
        t('Unable to save RSVP settings at this time due to database error.
        Please try again.')
       );

        }

    }

}
