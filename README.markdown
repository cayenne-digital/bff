# Bff

Better form factory, Build that effing form, Bright fancy forms...
It doesn't matter, it's your BFF!

## Example

    <?php
    $required = Array('nome','cognome','via','civico','localita','provincia','telefono','email','privacy');
    $validations = Array(
      'email' => 'email'
    );
    $send_via_email = Array(
      'to' => 'something@example.com',
      'bcc' => 'bcc@example.com',
      'from' => 'noreply@mywebsite.it',
      'from_name' => 'Bff',
      'subject' => 'Bff has something for you',
      'message' => 'This goes in the email'
    );
    $database = Array(
      'host' => $host,
      'username' => $user,
      'password' => $pass,
      'database' => $db,
      'table' => $table
    );
    $bff = new Bff(Array(
      'database' => $database,
      'send_via_email' => $send_via_email,
      'required' => $required,
      'validations' => $validations,
      'hash_key' => 'somefancystring'
    ));
    ?>

    <?php if ($bff->submit()) { ?>

      <p class="thanks">Thanks, your form has been submitted.</p>

    <?php } else { ?>

      <?= $bff->formTag(); ?>

      <p>Some text.</p>

      <?= $bff->errorMessage(); ?>

      <?= $bff->input('nome') ?>
      <?= $bff->input('cognome') ?>

      <?= $bff->input('via','Via/Piazza') ?>
      <?= $bff->input('civico', null, Array('class' => 'inline')) ?>

      <?= $bff->input('localita','Località') ?>
      <?= $bff->input('provincia') ?>

      <?= $bff->input('telefono'); ?>
      <?= $bff->input('email'); ?>

      <?= $bff->checkbox('privacy','Autorizzazione al trattamento dati (D.Lgs 196/03)') ?>
      <button type="submit" class="submit">Submit</button>

      <?= $bff->endFormTag(); ?>

    <? } ?>
