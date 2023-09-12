<?php

// Protection contre les injections SQL: on retire tous les '\' pour en rajouter là où c'est nécessaire après...
foreach ($_POST as $key => $value) {
  // Si la valeur est un tableau, alors on ajoute des '\' au valeurs dudit tableau
  if (is_array($value))
  {
    foreach ($value as $key2 => $value2) {
      $_POST[$key][$key2] = addslashes(stripslashes($value2));
    }
  }
  else
  {
    if (strpos($key, '_ckeditor') !== false)
    {
      $_POST[$key] = str_replace("<style", "", str_replace("<script", "", addslashes(stripslashes($value))));
    }
    else
    {
      $_POST[$key] = htmlspecialchars(addslashes(stripslashes($value)));
    }
  }

}

foreach ($_GET as $key => $value) {
  // Si la valeur est un tableau, alors on ajoute des '\' au valeurs dudit tableau
  if (is_array($value))
  {
    foreach ($value as $key2 => $value2) {
      $_GET[$key][$key2] = addslashes(stripslashes($value2));
    }
  }
  else
  {

    if (strpos($key, '_ckeditor') !== false)
    {
      $_GET[$key] = str_replace("<style", "", str_replace("<script", "", addslashes(stripslashes($value))));
    }
    else
    {
      $_GET[$key] = htmlspecialchars(addslashes(stripslashes($value)));
    }
  }


}

foreach ($_REQUEST as $key => $value) {
  // Si la valeur est un tableau, alors on ajoute des '\' au valeurs dudit tableau
  if (is_array($value))
  {
    foreach ($value as $key2 => $value2) {
      $_REQUEST[$key][$key2] = addslashes(stripslashes($value2));
    }
  }
  else
  {
    if (strpos($key, '_ckeditor') !== false)
    {
      $_REQUEST[$key] = str_replace("<style", "", str_replace("<script", "", addslashes(stripslashes($value))));
    }
    else
    {
      $_REQUEST[$key] = htmlspecialchars(addslashes(stripslashes($value)));
    }
  }
}


?>
