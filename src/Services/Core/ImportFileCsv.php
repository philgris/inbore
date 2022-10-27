<?php

/*
 *
 * Authors : see information concerning authors of InBORe project in file AUTHORS.md
 *
 * InBORE is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * InBORE is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 *
 */

namespace App\Services\Core;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Service ImportFileCsv
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ImportFileCsv {

  private $entityManager;

  public function __construct(EntityManagerInterface $manager, String $root_dir) {
    $this->entityManager = $manager;
    // $this->assetsManager = $assetsManager;
    $this->root_dir = $root_dir;
    // $this->assetsManager = new Package(new JsonManifestVersionStrategy(
    //   $root_dir . '/public//build/manifest.json'
    // ));
  }

  /**
   * getCsvPath
   * returns the path of CSV file given its type
   */
  public function getCsvPath(string $type_csv) {
    return $this->root_dir . '/assets/imports/' . $type_csv . '.csv';
    // return $this->assetsManager->getUrl('build/imports/' . $type_csv . '.csv');
  }

  /**
   *  readCSV($path)
   * read CSV file with path $path
   * return array : {array / line}  : ex. array([NomCol1] => ValCol1L1, [NomCol2] => ValCol2L1...)
   */
  public function readCSV($path) {
    $result = array();
    $name   = array();
    if (file_exists($path)) {
      $handle = fopen($path, "r");
      if (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num         = count($data);
        $row         = 0;
        $name        = $data;
        $flag_record = 0;
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
          // return one array containing the all {column => value} by record(s) or line
          if ($num == count($data)) {
            $flag_record = 1;
            for ($c = 0; $c < $num; $c++) {
              $result[$row][$name[$c]] = $data[$c];
            }
            $row++;
          }
        }
        if (!$flag_record) {
          // return the column name to NULL if there is no record
          for ($c = 0 ; $c < $num; $c++) {
            $result[0][$name[$c]] = $name[$c];
          }
        }
      }
      fclose($handle);
    }
    return ($result);
  }

  /**
   *  explodeCSV($path, $maxLine)
   * read CSV file with path $path
   * return array : {array / line} : ex. array([NomCol1] => ValCol1L1, [NomCol2] => ValCol2L1...)
   */
  public function explodeCSV($path, $index, $maxLine = 100) {
    $result = array();
    $name   = array();
    $handle = fopen($path, "r");
    if (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
      $num    = count($data); // nombre de colonne
      $row    = 0;
      $name   = $data;
      $nbline = 0;
      $tab    = array();
      while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $nbline++;
        if ($num == count($data)) {
          for ($c = 0; $c < $num; $c++) {
            $tab[$row][$name[$c]] = $data[$c];
          }
          $row++;
        }
        if ($nbline == $maxLine) {
          $nbline   = 0;
          $result[] = $tab;
          $tab      = array();
        }
      }
      $result[] = $tab;
    }
    fclose($handle);
    return ($result);
  }

  /**
   *  readColumnByTableSV($csvData)
   *  return an array [nomTable1 => array(nomTable1.NomField1, nomTable1.NomField2, ...), nomTable2 => array(nomTable2.NomField1, nomTable2.NomField2, ...), ...]
   */
  public function readColumnByTableSV($csvData) {
    # Recovery of the names of columns to be named of the form: TableName.FieldName
    $column_name = [];
    reset($csvData);
    foreach (current($csvData) as $k => $v) {
      $column_name[] = $k;
    }
    # Recovering column names for each table
    $columnByTable = [];
    foreach ($column_name as $v) {
      $ent = explode('.', $v)[0];
      if (array_key_exists($ent, $columnByTable)) { // add the name of the column "TableName.FieldName ..." to the table
        $columnByTable[$ent][] = $v;
      } else { // add a table with the title of the column "TableName.FieldName ..."
        $columnByTable[$ent] = [$v];
      }
    }
    return ($columnByTable);
  }

  /**
   *  testNomColumnCSV($columnByTable)
   *  test the name of CSV field like : NameOfDatabaseTable.FieldName
   */
  public function testNameColumnCSV($columnByTable, $nameTable, $nameField = NULL) {

    # Test
    if (array_key_exists($nameTable, $columnByTable)) {
      $output = 1;
      if ($nameField !== NULL) {
        if (!in_array($nameField, $columnByTable[$nameTable])) {
          $output = 0;
        }

      }
    } else {
      $output = 0;
    }
    return ($output);
  }

  /**
   *  checkNameCSVfile2Template($pathToTemplate, $pathToFileImport)
   * $pathToTemplate : path to template file
   * $pathToFileImport : path To CSV File Imported (tmp)
   *  compare the name of CSV field between the file imported and the template
   *  return text : ERROR message if exist OR '' if not
   *
   */
  public function checkNameCSVfile2Template($pathToTemplate, $pathToFileImport) {
    $output           = '';
    $arrayTemplateCsv = $this->readCSV($pathToTemplate);
    $arrayFileImport  = $this->readCSV($pathToFileImport);

    # Recovery of the names of columns to be named of the form: TableName.FieldName
    if ($arrayTemplateCsv !== array()) {
      $column_name_template = [];
      reset($arrayTemplateCsv);
      foreach (current($arrayTemplateCsv) as $k => $v) {
        $column_name_template[] = $k;
      }
    } else {
      $output = 'importfileService.ERROR Unknown template file';
    }

    # Recovery of the names of columns to be named of the form: TableName.FieldName
    if ($arrayFileImport !== array()) {
      $column_name = [];
      reset($arrayFileImport);
      foreach (current($arrayFileImport) as $k => $v) {
        $column_name[] = $k;
      }
    } else {
      $output = 'importfileService.ERROR Unknown file imported';
    }

    # Compare both file name columns
    $nbColumnTemplate   = count($arrayTemplateCsv[0]);
    $nbColumnFileImport = count($arrayFileImport[0]);
    if ($nbColumnFileImport > 0 && $nbColumnTemplate == $nbColumnFileImport) {
      for ($c = 0; $c < $nbColumnTemplate; $c++) {
        if ($column_name_template[$c] != $column_name[$c]) {
          $output = 'importfileService.ERROR bad column in CSV file';
        }
      }
    } else {
      $output = 'importfileService.ERROR bad number of columns in CSV file';
    }

    return ($output);
  }

  /**
   *  suppCharSpeciaux($field, $type='')
   * deleting special characters
   */
  public function suppCharSpeciaux($data, $type = 'all') {
    // the type can be 'field' or 'table'
    switch ($type) {
    case " ":
      // space
      $data_corrected = str_replace(" ", "", $data);
      break;
    case "t":
      // tabulation
      $data_corrected = str_replace("\t", "", $data);
      break;
    case "n":
      // line break
      $data_corrected = str_replace("\n", "", $data);
      break;
    case "r":
      // carriage return
      $data_corrected = str_replace("\r", "", $data);
      break;
    case "O":
      // NULL
      $data_corrected = str_replace("\0", "", $data);
      break;
    case "x":
      // vertical tabulation
      $data_corrected = str_replace("\x0B", "", $data);
      break;
    case "tnrOx":
      $data_corrected = str_replace("\t", "", $data);
      $data_corrected = str_replace("\n", "", $data_corrected);
      $data_corrected = str_replace("\r", "", $data_corrected);
      $data_corrected = str_replace("\0", "", $data_corrected);
      $data_corrected = str_replace("\x0B", "", $data_corrected);
      break;
    case "all":
      $data_corrected = str_replace(" ", "", $data);
      $data_corrected = str_replace("\t", "", $data_corrected);
      $data_corrected = str_replace("\n", "", $data_corrected);
      $data_corrected = str_replace("\r", "", $data_corrected);
      $data_corrected = str_replace("\0", "", $data_corrected);
      $data_corrected = str_replace("\x0B", "", $data_corrected);
      break;
    default:
      $data_corrected = $data;
      echo "</br> le type de correction de la fonction suppCharSpeciaux n existe pas : type = " . $type;
      exit;
    }
    return $data_corrected;
  }

  /**
   *  GetCurrentTimestamp()
   * return the objet timestamp current
   */
  public function GetCurrentTimestamp() {
    // get the TIMESTAMP of import
    $dateImport = date("Y-m-d H:i:s");
    $DateImport = new \DateTime($dateImport);
    return $DateImport;
  }
}
