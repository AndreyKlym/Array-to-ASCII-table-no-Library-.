<?php
// Array to ASCII table (no Library)
// The application displays data from an array to ASCII table on a web page without Library.

// The keys and data in the resulting table sorted in alphabetical order.
// The application is able to take any kind and number of keys.
// The application is able to take every number of rows.
// The number of keys can vary for every row of data. 
// Implemented the ability to enter an array without the presence of keys.
// And the data must still be shown correctly in the table with the missing cell kept blank.
// The text in every cell is right alligned.
// The keys and values can be of any length.
// The libraries or frameworks are not used.

// Author Andrey Klymenko



echo "<pre>";

// get array
$array = array(
    array(
        'House' => 'Baratheon',
        'Sigil' => 'A crowned stag',
        'Motto' => 'Ours is the Fury',
        ),
    array(
        'Leader' => 'Eddard Stark',
        'House' => 'Stark',
        'Motto' => 'Winter is Coming',
        'Sigil' => 'A grey direwolf',
        'Q' => 'WEW'
        ),
    array(
        'House' => 'Lannister',
        'Leader' => 'Tywin Lannister',
        'ABS' => 'Santa Clause',
        '' => 'Didzo Clause',
        'Sigil' => 'A golden lion'
        ),
    array(
	      'Q' => 'Z'
    )
);


// get list of unique keys of array
$headItems = [];
foreach($array as $key=>$item){
  uksort($item, "cmp");
  foreach($item as $id => $value){
    $headItems[] = $id;
  }
}
$headItemsList = array_unique($headItems);
asort($headItemsList);
function cmp($a, $b){
    return strcasecmp($a, $b);
}


// get json of array
class StringTools
{
  public static function convertForLog($variable) {
    if ($variable === null) {
      // return 'null';
      return '';
    }
    if ($variable === false) {
      return 'false';
    }
    if ($variable === true) {
      return 'true';
    }
    if (is_array($variable)) {
      return json_encode($variable);
    }
    return $variable ? $variable : "";
  }


  public static function toAsciiTable($array, $fields, $wrapLength) {
    // get max length of fields
    $fieldLengthMap = [];
    foreach ($fields as $field) {
      $fieldMaxLength = 0;
      foreach ($array as $item) {
        $value = self::convertForLog($item[$field]);
        $length = strlen($value);
        $fieldMaxLength = $length > $fieldMaxLength ? $length : $fieldMaxLength;
      }
      $fieldMaxLength = $fieldMaxLength > $wrapLength ? $wrapLength : $fieldMaxLength;
      $fieldLengthMap[$field] = $fieldMaxLength;
    }

    // create table
    $asciiTableHead = "";
    $asciiTable = "";
    $totalLength = 0;
    foreach ($array as $item) {
      // prepare next line
      $valuesToLog = [];
      foreach ($fieldLengthMap as $field => $maxLength) {
        $valuesToLog[$field] = self::convertForLog($item[$field]);
      }
      
      // write next line
      $lineIsWrapped = true;
      while ($lineIsWrapped) {
        $lineIsWrapped = false;
        foreach ($fieldLengthMap as $field => $maxLength) {
          $valueLeft = $valuesToLog[$field];
          $valuesToLog[$field] = "";
          if (strlen($valueLeft) > $maxLength) {
            $valuesToLog[$field] = substr($valueLeft, $maxLength);
            $valueLeft = substr($valueLeft, 0, $maxLength);
            $lineIsWrapped = true;
          }
          $asciiTable .= "|" . str_repeat(" ", $maxLength - strlen($valueLeft)) . " {$valueLeft} ";
        }

        $totalLength = $totalLength === 0 ? strlen($asciiTable) + 1 : $totalLength;
        $asciiTable .= "|\n";
      }
    }
    
    // write head line
    foreach ($fieldLengthMap as $field => $maxLength) {
    $asciiTableHead .= "|" . str_repeat(" ", $maxLength - strlen($field)) . " {$field} ";
    }
    $asciiTableHead .= "|\n";
    
    // add lines before and after
    $horizontalLine = str_repeat("=", $totalLength);
    $horizontalLineAdd = str_repeat("-", $totalLength);
    $asciiTable = "{$horizontalLine}\n{$asciiTableHead}{$horizontalLineAdd}\n{$asciiTable}{$horizontalLine}\n";
    return $asciiTable;
  }
}

// output table
$table = StringTools::toAsciiTable($array, $headItemsList, 50);
print_r($table);
