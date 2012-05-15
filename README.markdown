## Why Bff?

Better form factory, Build that effing form, Bright fancy forms...
It doesn't matter, it's your BFF!

## Example

```php
<?php

// Optional required fields
$required = Array('nome','cognome','via','civico','localita','provincia','telefono','email','privacy');

// Optional validations (only 'email' supported at the moment)
$validations = Array(
  'email' => 'email'
);

// Optional delivery via email (contact forms and the such)
$send_via_email = Array(
  'to' => 'something@example.com',
  'bcc' => 'bcc@example.com',
  'from' => 'noreply@mywebsite.it',
  'from_name' => 'Bff',
  'subject' => 'Bff has something for you',
  'message' => 'This goes in the email'
);

// If you want to save the data to a MySql DB (you should)
$database = Array(
  'host' => $host,
  'username' => $user,
  'password' => $pass,
  'database' => $db,
  'table' => $table
);

// Now you can call your BFF
$bff = new Bff(Array(
  'database' => $database,
  'send_via_email' => $send_via_email,
  'required' => $required,
  'validations' => $validations,
  'labels' => $custom_labels,
  'hash_key' => 'somefancystring'
));

?>

<?php if ($bff->submit()) { ?>

  <p class="thanks">Thanks, your form has been submitted.</p>

<?php } else { ?>

  <?= $bff->formTag(); ?>

  <p>Some text.</p>

  <?= $bff->errorList(); ?>

  <?= $bff->text('nome') ?>
  <?= $bff->text('cognome') ?>

  <div>You can put your fancy stuff in between inputs</div>

  <?= $bff->text('via','Via/Piazza') ?>
  <?= $bff->text('civico', null, Array('class' => 'inline')) ?>

  <?= $bff->text('localita','Località') ?>
  <?= $bff->text('provincia') ?>

  <?= $bff->text('telefono'); ?>
  <?= $bff->text('email'); ?>

  <?= $bff->checkbox('privacy','Autorizzazione al trattamento dati') ?>

  <button type="submit" class="submit">Submit</button>

  <?= $bff->endFormTag(); ?>

<? } ?>
```
