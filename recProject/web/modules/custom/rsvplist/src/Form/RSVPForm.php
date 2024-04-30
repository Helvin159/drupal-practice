<?php 

/**
 * @file
 * A form to collect an email address for RSVP details.
 */

 namespace Drupal\rsvplist\Form;

 use Drupal\Core\Form\FormBase;
 use Drupal\Core\Form\FormStateInterface;

 class RSVPForm extends FormBase{
     
  /**
    * {@inheritdoc}
  */
  public function getFormId(){
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state){
    // Attempt to get the fully loaded node object of the viewed page
    $node = \Drupal::routematch()->getParameter('node');

    // Some pages may not be nodes though and $node will be NULL on thos pages.
    // If a node was loaded, get the node id.
    if(!(is_null($node))){
      $nid = $node->id();
    }else{
      // If node could not be loaded, default to 0;
      $nid = 0;
    }

    // Establish the $form render array. It has an email text field,
    // a submit button, and a hidden field containing the node ID.
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => 'Email address',
      '#size' => '25',
      '#description' => 'We will send updates to the email address you provide.',
      '#required' => TRUE
    ];
    $form['submit'] = [
        '#type' => 'submit',
        '#value' => 'RSVP',
    ];
    $form['nid'] = [
        '#type' => 'hidden',
        '#value' => $nid,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state){
    $value = $form_state->getValue('email');
    if( !(\Drupal::service('email.validator')->isValid($value)) ){
      $form_state->setErrorByName('email',
        $this->t("It appears that %mail is not a valid email. Please try again", ['%mail'=>$value]));
    }
  }

  /** 
   * {@inheritdoc} 
  */
  public function submitForm(array &$form, FormStateInterface $form_state){
    // $submitted_email = $form_state->getValue('email');
    // $this->messenger()->addMessage($this->t('The form is working! You entered
    // @entry.',
    //  ['@entry' => $submitted_email]));

    try{
    //   Begin Phase 1: initiate variables to save

    // Get current user ID.
    $uid = \Drupal::currentUser()->id();

    // Demonsttration for how to load a full suer object of the current user. 
    // this $full_user variable is not needed for this code
    // but is show for demontration purposes. 
    $full_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    // Obtain values as entered into the Form. 
    $nid = $form_state->getValue('nid');
    $email = $form_state->getValue('email');

    $current_time = \Drupal::time()->getRequestTime();
    // End Phase 1

    // Begin phase 2

    // Start to build a query builder object $query. 
    // https://www.drupal.org/docs/8/api/database-api/insert-queries
    $query = \Drupal::database()->insert('rsvplist');

    // Specify the fields that the query will insert into.
    $query->fields([
      'uid',
      'nid',
      'mail',
      'created',
    ]);

    // Set the values of the fields we select. 
    // Note that they must be in the same order as we defined them
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
    // End phase 2

    // Phase 3
    // Provide form submitter a nice message
    \Drupal::messenger()->addMessage(
        $this->t('Thank you for your RSVP, you are on the lsit for the event!')
    );
    // End phase 3


    }catch( \Exception $e){
    //   Display error message to user. 
      \Drupal::messenger()->addError(
        $this->t('Unable to save your RSVP settings at this time due to a database error. 
        Please try again.')
      );
    }
  }
}