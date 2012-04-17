<?php
/**
 * Bff: Better form factory, Build that effing form, Bright fancy forms...
 *      It doesn't matter, it's your BFF!
 *
 * Copyright 2012, Cayenne Digital (http://www.cayenne.it)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version 0.1.0
 * @copyright Copyright 2012, Cayenne Digital (http://www.cayenne.it)
 * @link https://github.com/cayenne-digital/bff
 * @author Francesco Negri <francesco.negri@cayenne.it>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Cayenne\Bff;

class Bff {

  // A fallback hashing key, please override passing your key to the constructor
  const HASH_KEY = 'gotutobvonluolifokuavwyineotmik';

  private $display_form = true;
  private $display_errors = false;
  private $display_result = false;

  private $fields = Array();
  private $required = Array();
  private $validations = Array();
  private $errors = Array();


  public function __construct($config=Array()) {
    // Not very secure but we'll fix this another time
    foreach ($config as $k => $v) {
      $this->{$k} = $v;
    }
  }

  private function hash($string) {
    $hash_key = (!isset($this->hash_key)) ? $this->hash_key : self::HASH_KEY;
    return hash_hmac('sha256', $string, $hash_key);
  }

  /*
   * FIXME:
   * ===================
   * do this properly ;P
   * ===================
   */
  public function getDbConnection() {

    if (!$this->database) return false;

    // Lots of stuff from
    // https://github.com/cakephp/cakephp/blob/master/lib/Cake/Model/Datasource/Database/Mysql.php
    $options = array(
      'persistent' => false,
      'host' => $this->database['host'],
      'login' => $this->database['username'],
      'password' => $this->database['password'],
      'database' => $this->database['database'],
      'port' => '3306',
      'encoding' => 'utf8'
    );

    $flags = array(
      PDO::ATTR_PERSISTENT => $options['persistent'],
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $options['encoding']
    );

    $dsn = "mysql:host={$options['host']};port={$options['port']};dbname={$options['database']}";

    $this->db_connection = new PDO(
      $dsn,
      $options['login'],
      $options['password'],
      $flags
    );

    return $this->db_connection;
  }


  public function formTag() {
    return "<form class='bff' method='POST' action=''>";
  }

  public function endFormTag() {
    $whitelist = implode('|', $this->fields);
    $whitelist .= '|'.$this->hash($whitelist);

    $html = "<input type='hidden' name='bff_whitelist' value='$whitelist' />\n";
    $html .= "<input type='hidden' name='bff_submit' value='submit' />\n";
    $html .= '</form>';
    // $html .= '<script src="/assets/javascripts/bff.js"></script>';

    return $html;
  }

  public function errorMessage() {
    $html = '';
    if (count($this->errors)>0) {
      $html = "<div class='bff-errors'>";
      $html .= "<p class='error'>Controlla che tutti i campi obbligatori siano compilati</p>";
      $html .= "</div>";
    }
  return $html;
  }

  public function input($name, $label=null, $options=Array()) {
    array_push($this->fields, $name);
    // $class = (array_key_exists($name, $this->errors)) ? 'error ' : '';
    $class = (isset($options['class'])) ? $options['class'] : '';
    $error = (array_key_exists($name, $this->errors)) ? 'error' : '';
    if ($class) $class = " class='$class'";
    $value = (isset($this->data[$name])) ? $this->data[$name] : '';

    if (!$label) $label = ucwords(str_replace('_',' ',$name));
    if (in_array($name,$this->required)) $label .= "*";
    $html = "<label class='$error'>$label</label>\n";

    $html .= "<input name='$name' type='text' value='$value' $class></input>\n";
    return $html;
  }

  public function select($name, $label=null, $values, $options=Array()) {
    array_push($this->fields, $name);
    $error = (array_key_exists($name, $this->errors)) ? 'error' : '';

    if (!$label) $label = ucwords(str_replace('_',' ',$name));
    if (in_array($name,$this->required)) $label .= "*";
    $html = "<label class='$error'>$label</label>\n";

    // $html .= "<div class='$error'>\n";
    $html .= "<select name='$name'>\n";
    $html .= "<option value=''>Seleziona...</option>\n";
    $value = (isset($this->data[$name])) ? $this->data[$name] : '';

    foreach ($values as $v) {
      $selected = ($value==$v) ? "selected='selected'" : '';
      $html .= "<option value='$v' $selected>$v</option>\n";
    }
    $html .= "</select>";
    // $html .= "</div>";

    return $html;
  }

  public function textarea($name, $label=null) {
    array_push($this->fields, $name);
    $error = (array_key_exists($name, $this->errors)) ? 'error' : '';
    $value = (isset($this->data[$name])) ? $this->data[$name] : '';

    if (!$label) $label = ucwords(str_replace('_',' ',$name));
    if (in_array($name,$this->required)) $label .= "*";
    // $html = "<div class='$error'>\n";
    $html = "<label class='$error'>$label</label>\n";
    $html .= "<textarea name='$name'>$value</textarea>\n";
    // $html .= "</div>\n";
    return $html;
  }

  public function checkbox($name, $label=null) {
    array_push($this->fields, $name);
    $error = (array_key_exists($name, $this->errors)) ? 'error' : '';
    $value = (isset($this->data[$name])) ? "checked='checked'" : '';

    if (!$label) $label = ucwords(str_replace('_',' ',$name));
    if (in_array($name,$this->required)) $label .= "*";

    // $html = "<span class='$error'>\n";
    $html = "<input name='$name' type='checkbox' $value value='1' class='inline'></input>\n";
    $html .= "<label class='inline $error'>$label</label>\n";
    // $html .= "</span>\n";
    return $html;
  }

  public function validate() {
    //FIXME: implement a better validation engine!
    foreach($this->required as $f) {
      if (!isset($this->data[$f]) || $this->data[$f] == '') {
        $this->errors[$f] = "Campo obbligatorio";
      }
    }
    foreach($this->validations as $f => $v) {
      // Check validations only if there's not already an error on the field (required)
      if (!empty($this->data[$f])) {
        switch ($v) {
        case 'email':
          // Regexes shamelessly copied from https://github.com/cakephp/cakephp/blob/master/lib/Cake/Utility/Validation.php
          $hostname = '(?:[a-z0-9][-a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,4}|museum|travel)';
          $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@' . $hostname . '$/i';
          if (!preg_match($regex, $this->data[$f])) {
            $this->errors[$f] = "Email non valida";
          }
          break;
        default:
          break;
        }
      }
    }

    return (count($this->errors)==0) ? true : false;
  }

  public function submit() {
    if (!isset($_REQUEST['bff_submit'])) {
      return false;
    }
    try {
      $this->buildData();
      if (!$this->validate()) {
        return false;
      }
      $this->saveToDb();
      $this->sendViaEmail();
      return true;
    } catch (Exception $e) {
      trigger_error("Bff Exception: " . $e->getMessage(), E_USER_WARNING);
      return false;
    }
  }

  private function buildData() {
    if (!isset($_REQUEST['bff_whitelist'])) {
      throw new Exception("bff_whitelist not found in http request");
    }
    $this->whitelist = explode('|', $_REQUEST['bff_whitelist']);
    $hash = array_pop($this->whitelist);
    if ($hash != $this->hash(implode('|',$this->whitelist))) {
      throw new Exception("invalid hash");
    }
    foreach ($this->whitelist as $f) {
      $this->data[$f] = (isset($_REQUEST[$f]))?$_REQUEST[$f]:null;
    }
    $this->data['remote_ip'] = $_SERVER['REMOTE_ADDR'];
  }

  private function saveToDb() {
    $conn = $this->getDbConnection();
    if (!$conn) return false;

    foreach ($this->data as $k => $v) {
      $fields_arr[] = $k;
      $values_arr[] = $v;
    }

    $table = $this->database['table'];
    $fields = implode(', ', $fields_arr);
    $values = implode(', ', $values_arr);
    $holder = implode(',', array_fill(0, count($fields_arr), '?'));

    $fields .= ', created_at';
    $holder .= ', NOW()';

    $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$holder})";
    $statement = $this->db_connection->prepare($sql);
    $i = 1;
    foreach ($values_arr as $v) {
      $statement->bindValue($i, $v);
      $i += 1;
    }
    $statement->execute();
  }

  private function sendViaEmail() {
    if (empty($this->send_via_email)) return false;

    $m = $this->send_via_email;
    $headers = "From: {$m['from_name']} <{$m['from']}>\n";
    if (isset($m['bcc'])) $headers .= "Bcc: {$m['bcc']}";

    $message = $m['message'];
    $message .= "\n\n";

    foreach ($this->data as $k => $v) {
      $message .= "--- ";
      $message .= strtoupper(str_replace('_',' ',$k)).": ";
      $message .= "$v\n\n";
    }

    $accepted = @mail($m['to'], $m['subject'], $message, $headers);
    if (!$accepted) throw new Exception("Bff error sending email");
  }


}
