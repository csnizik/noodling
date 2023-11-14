<?php
namespace Drupal\csv_import\Controller;
include 'csvImportFunctions.php';

use Drupal\Core\Controller\ControllerBase;
use Drupal\asset\Entity\Asset;
use Drupal\log\Entity\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Drupal\asset\Entity\AssetInterface;

/**
 * Provides route responses for the Example module.
 */
class CsvImportController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function upload() {
    return [
      '#children' => '
        import excel workbook (.xlsx, .xls):
        <form class="form-horizontal" action="/csv_import/upload_workbook" method="post"
        name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data" onsubmit="return validateFile()">
          <input type="file" name="file" id="file" class="file" accept=".xls,.xlsx">
          <input type="submit" id="submit" name="import" class="btn-submit" />
        </form>

        inputs:
        <form action="/csv_import/upload_inputs" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        
        operations:
        <form action="/csv_import/upload_operations" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>

        soil health:
        <form action="/csv_import/upload_soil_health_sample" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>

        combo:
        <form action="/csv_import/upload_combo" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>

        soil test results:
        <form action="/csv_import/upload_soil_test_results" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>

        awardee org + project:
        <form action="/csv_import/upload_awardee_org_project" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
    ',
    ];
  }

  public function process_awardee_org_project() {
    $awardee_org_count = 0;
    $project_count = 0;
    $out = [];      //output messages: imported sheets;
    $output = 'start<br />';     //output messages: skipped sheets;

    $allowedFileType = [
        'application/vnd.ms-excel',
        'text/xls',
        'text/xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];


    //temporarily save imported file
    $folderPath = realpath($_FILES['file']['tmp_name']);
    $targetPath = $folderPath . $_FILES['file']['name'];

    move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

    //get file extension
    $extension = ucfirst(strtolower(pathinfo($targetPath, PATHINFO_EXTENSION)));
    
    //read the workbook but only get the sheets that is relevent
    $sheetnames = ['Awardee Organizations', 'Projects'];
    $reader = IOFactory::createReader($extension);
    $reader->setReadDataOnly(TRUE);
    $reader->setLoadSheetsOnly($sheetnames);
    $spreadSheet = $reader->load($targetPath);
    $sheetCount = $spreadSheet->getSheetCount();
    

    // Process each sheet in the workbook.
    for ($i = 0; $i < $sheetCount; $i++) {
      $sheet = $spreadSheet->getSheet($i);
      $sheet_name = $sheet->getTitle();

      switch($sheet_name) {
        case "Awardee Organizations":
          $offset = 0;
          $done = false;

          while(!$done) {
            $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
            
            if($name == "") {
              $done = true;
              break;
            }

            $ao_name = $name;
            $ao_sn = $sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue();
            $ao_ac = $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue();
            $ao_st = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();

            $awardee_submission = [];
            $awardee_submission['name'] = $ao_name;
            $awardee_submission['organization_acronym'] = $ao_ac;
            $awardee_submission['organization_short_name'] = $ao_sn;
            $awardee_submission['organization_state_territory'] = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_state_territory', 'name' => $ao_st]);
            $awardee_submission['type'] = 'awardee';

            $awardee = Asset::create($awardee_submission);
            $awardee->save();
            $awardee_org_count++;

            $offset++;
            
          }

          break; 

        case 'Projects':
          $offset = 0;
          $done = false;

          while(!$done) {
            $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
            
            if($name == "") {
              $done = true;
              break;
            }

            $project_name = $name;
            $project_agreement_no = $sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue();
            $project_grant_type = $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue();
            $project_funding_amount = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();
            $project_prc = $sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue();
            $project_summary = $sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue();
            $project_contact_name = $sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue();
            $project_contact_type = $sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue();
            $project_contact_nametype = $sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue();
            

            $project_submission = [];
            $project_submission['type'] = 'project';
            $project_submission['name'] = $project_name;
            $project_submission['award'] = \Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'award', 'field_award_agreement_number' => $project_agreement_no]);
            $project_submission['field_funding_amount'] = $project_funding_amount;
            $project_submission['field_summary'] = $project_summary;
            $project_submission['field_grant_type'] = $project_contact_type;
            $project_submission['field_resource_concerns'] = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_resource_concern', 'name' => $project_prc]);
            
            $project = Asset::create($project_submission);
            $project->save();
            
            $project_count++;

            $offset++;
          }

          break;
        
        }
    }

    return [
      '#children' => "$awardee_org_count awardee orgs and $project_count projects added.",
    ];


  }



  public function process_awardee_org_project_iterator() {
    $out = [];      //output messages: imported sheets;
    $output = 'start<br />';     //output messages: skipped sheets;

   // if (isset($_POST["import"])) {
      $allowedFileType = [
          'application/vnd.ms-excel',
          'text/xls',
          'text/xlsx',
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
      ];

     // if (in_array($_FILES["file"]["type"], $allowedFileType)) {

          //temporarily save imported file
          $folderPath = realpath($_FILES['file']['tmp_name']);
          $targetPath = $folderPath . $_FILES['file']['name'];

          move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

          //get file extension
          $extension = ucfirst(strtolower(pathinfo($targetPath, PATHINFO_EXTENSION)));
          
          //read the workbook but only get the sheets that is relevent
          $sheetnames = ['Awardee Organizations', 'Projects'];
          $reader = IOFactory::createReader($extension);
          $reader->setReadDataOnly(TRUE);
          $reader->setLoadSheetsOnly($sheetnames);
          $spreadSheet = $reader->load($targetPath);
          $sheetCount = $spreadSheet->getSheetCount();
          

          // Process each sheet in the workbook.
          for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadSheet->getSheet($i);
            $sheet_name = $sheet->getTitle();

            switch($sheet_name) {
              case "Awardee Organizations":
                $first = 1;
                $skip = 2;
                $row_iter = $sheet->getRowIterator();
                foreach($row_iter as $row) {
                  if($first) { $first = 0; do {$row_iter->next();} while($skip--); continue;}
                  $ci = $row->getCellIterator();
                  foreach($ci as $cell) {
                    $cv = $cell->getValue();
                    if ($cv == "") continue;
                    $output .= "-- " . $cell->getValue(); $ci->next();
                    $output .= " : " . $cell->getValue(); $ci->next();
                    $output .= " : " . $cell->getValue(); $ci->next();
                    $output .= $cell->getValue() . "  --";

                  }
                  $output .= "<br />";
                }
                $output .= '';
                break;
              case 'Projects':
                $output .= "ppp";
                break;
            }

            $output = $output . $sheet_name . " <br />";
          }
       // }
     // }


    return [
      '#children' => $output,
    ];
  }

  public function process_workbook() {
    $out = [];      //output messages: imported sheets;
    $output = '';     //output messages: skipped sheets;

    if (isset($_POST["import"])) {
      $allowedFileType = [
          'application/vnd.ms-excel',
          'text/xls',
          'text/xlsx',
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
      ];

      if (in_array($_FILES["file"]["type"], $allowedFileType)) {

          //temporarily save imported file
          $folderPath = realpath($_FILES['file']['tmp_name']);
          $targetPath = $folderPath . $_FILES['file']['name'];
          move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

          //get file extension
          $extension = ucfirst(strtolower(pathinfo($targetPath, PATHINFO_EXTENSION)));
          
          //read the workbook but only get the sheets that is relevent
          $sheetnames = ['Producer', 'Methods'];
          $reader = IOFactory::createReader($extension);
          $reader->setReadDataOnly(TRUE);
          $reader->setLoadSheetsOnly($sheetnames);
          $spreadSheet = $reader->load($targetPath);
          $sheetCount = $spreadSheet->getSheetCount();
          

          // Process each sheet in the workbook.
          for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadSheet->getSheet($i);
            $sheet_name = $sheet->getTitle();
            
            // $csv = $spreadSheet->getSheet($i)->toArray();

            // // Skip sheets that don't have data.
            // if (empty($csv)) {
            //   continue;
            // }

            // Process the data in the sheet based on its name.
            switch ($sheet_name) {
              //import producer
              case $sheetnames[0]:
                $end_column = 6;
                $records = $this->processImport($sheet, 'import_producer', $end_column);

                //output message
                $out[] = array('name' => 'Producer', 'records' => $records);
                
                break;

              //import Methods
              case $sheetnames[1]:
                $end_column = 28;
                $records = $this->processImport($sheet, 'import_methods', $end_column);

                //output message
                $out[] = array('name' => 'Methods', 'records' => $records);

                break;
              
              default:
                // Unknown sheet name.
                $output .= "<p>Skipping unknown sheet \"$sheet_name\".</p>";
                break;
            }

          }

          //Purge the uploaded file after import is completed.
          unlink($targetPath);
          
      } else {    
          $output = "Invalid File Type. Upload Excel File.";
      }
    }

    $out_msg = "";
    foreach ($out as $it){
      $out_msg .= $it['name'] . ': ' . $it['records'] . ' records.' . '<br>';
    } 

    return [
      '#children' => 'Workbook has been imported:' . '<br><br>' . $out_msg . '<br>' . $output,
    ];


  }

  public function processImport($in_sheet, $importFunction, $end_column){
    $record_count = 0;
                
    $start_column = 3;

    $row = 5;

    // Starting from first column and row of data, retrieve each cell of the rows of data. 
    for($row; ; $row++){
      $dataArray = [];
      for($col = $start_column; $col != $end_column + 1; ++$col) {
        $curr_cell = $in_sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row);
        $cell_value = $curr_cell->getValue();
        if ($cell_value[0] === '=') {
          $cell_value = $curr_cell->getOldCalculatedValue();
        }
        array_push($dataArray, $cell_value);
      }

      //if the row is empty then we reach the end of rows and stop importing
      if(empty(array_filter($dataArray, function ($a) { return $a !== null;}))) {
        break;
      }
      
      //increment record count
      $record_count = $record_count + 1;

      //import new project summary record
      $importFunction($dataArray, $record_count);
      
    }

    return $record_count;
    
  }

  public function process_combo_operations($csv) {
    foreach($csv as $csv_line) {
      if($csv_line[0] === "Operation") {
        $shmu = \Drupal::entityTypeManager()->getStorage('asset')->load($csv_line[2]);
        $project = \Drupal::entityTypeManager()->getStorage('asset')->load($shmu->get('project')->target_id);

        // $field_input = \Drupal::entityTypeManager()->getStorage('asset')->load($csv_line[2]);

        // $operation_submission = [];
        // $operation_submission['type'] = 'operation';

        // $operation_submission['shmu'] = $shmu;
        // $operation_submission['field_operation_date'] = strtotime($csv_line[1]);
        // $operation_submission['field_operation'] = $csv_line[3];
        // $operation_submission['field_ownership_status'] = $csv_line[4];
        // $operation_submission['field_tractor_self_propelled_machine'] = $csv_line[5];
        // $operation_submission['field_row_number'] = $csv_line[6];
        // $operation_submission['field_width'] = $csv_line[7];
        // $operation_submission['field_horsepower'] = $csv_line[8];
        // $operation_submission['project'] = $project;

        // $operation_to_save = Asset::create($operation_submission);
        
        // $operation_to_save->save();
      }
    }

    return [
      "#children" => "saved " . nl2br(print_r($shmu, true)) . " operations.",
    ];
    
  }

  public function process_inputs() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {

      $operation = \Drupal::entityTypeManager()->getStorage('asset')->load($csv_line[0]);
      $project = \Drupal::entityTypeManager()->getStorage('asset')->load($operation->get('project')->target_id);

      $input_submission = [];
      $input_submission['type'] = 'input';
      $input_submission['field_input_date'] = strtotime($csv_line[1]);
      $input_submission['field_input_category'] = $csv_line[2];
      $input_submission['field_input'] = $csv_line[3];
      $input_submission['field_unit'] = $csv_line[4];
      $input_submission['field_rate_units'] = $csv_line[5];
      $input_submission['field_cost_per_unit'] = $csv_line[6];
      $input_submission['field_custom_application_unit'] = $csv_line[7];
      $input_submission['project'] = $project;

      $operation_taxonomy_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($operation->get('field_operation')->target_id);
      $input_taxonomy_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($csv_line[2]);
      $input_submission['name'] = $operation_taxonomy_name->getName() . "_" . $input_taxonomy_name->getName() . "_" . $csv_line[1];

      
      $input_to_save = Asset::create($input_submission);

      $cost_submission = [];
      $cost_submission ['type'] = 'cost_sequence';
      $cost_submission ['field_cost_type'] = $csv_line[8];
      $cost_submission ['field_cost'] = $csv_line[9];

      $other_cost = Asset::create($cost_submission);

      $input_to_save->set('field_input_cost_sequences', $other_cost);
      $input_to_save->save();
      
      $operation->get('field_input')[] = $input_to_save->id();
      $operation->save();

      $out = $out + 1;// . nl2br(print_r($csv_line, true)) . "\n";
    }

    return [
      "#children" => "added " . $out . " inputs.",
    ];
    
  }

  public function process_operations() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {

      $shmu = \Drupal::entityTypeManager()->getStorage('asset')->load($csv_line[0]);
      $project = \Drupal::entityTypeManager()->getStorage('asset')->load($shmu->get('project')->target_id);

      $field_input = \Drupal::entityTypeManager()->getStorage('asset')->load($csv_line[2]);

      $operation_submission = [];
      $operation_submission['type'] = 'operation';

      $operation_submission['shmu'] = $shmu;
      $operation_submission['field_operation_date'] = strtotime($csv_line[1]);
      $operation_submission['field_input'] = $field_input;
      $operation_submission['field_operation'] = $csv_line[3];
      $operation_submission['field_ownership_status'] = $csv_line[4];
      $operation_submission['field_tractor_self_propelled_machine'] = $csv_line[5];
      $operation_submission['field_row_number'] = $csv_line[6];
      $operation_submission['field_width'] = $csv_line[7];
      $operation_submission['field_horsepower'] = $csv_line[8];
      $operation_submission['project'] = $project;

      $operation_to_save = Asset::create($operation_submission);
      
      $operation_to_save->save();
      $out = $out + 1;
    }
    return [
      "#children" => "saved " . $out . " operations.",
    ];
    
  }

  public function process_soil_health_sample() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) { 

      $shmu = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_management_unit', 'name' => $csv_line[1]]));
      $project = \Drupal::entityTypeManager()->getStorage('asset')->load($shmu->get('project')->target_id);

      $soil_health_sample_submission = [];
      $soil_health_sample_submission['type'] = 'soil_health_sample';
      $soil_health_sample_submission['soil_id'] = $csv_line[0];
      $soil_health_sample_submission['shmu'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_management_unit', 'name' => $csv_line[1]]));
      $soil_health_sample_submission['field_soil_sample_collection_dat'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[2])->getTimestamp();
      $soil_health_sample_submission['field_equipment_used'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_equipment', 'name' => $csv_line[3]]));
      $soil_health_sample_submission['field_diameter'] = $csv_line[4];
      $soil_health_sample_submission['name'] = $csv_line[5];
      $soil_health_sample_submission['field_plant_stage_at_sampling'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_plant_stage', 'name' => $csv_line[6]]));
      $soil_health_sample_submission['field_sampling_depth'] = $csv_line[7];
      $soil_health_sample_submission['field_soil_sample_geofield'] = $csv_line[8];
      $soil_health_sample_submission['project'] = $project;
      
      $soil_health_sample_to_save = Asset::create($soil_health_sample_submission);
      
      $soil_health_sample_to_save->save();
      $out = $out + 1;

    }
    return [
      "#children" => "saved " . $out . " soil health sample.",
    ];
  }

  public function process_combo() {
    // grab the contents of the file and same some info
    $file = \Drupal::request()->files->get("file");
    $file_name = $file->getClientOriginalName();
    $item_count = 0;
    $file_loc = $file->getRealPath();
    
    $csv = array_map('str_getcsv', file($file_loc));
    array_shift($csv);

    // holds items being added by reference number
    $operation_ref_nums = [];
    $input_ref_nums = [];
    
    // break sheet down into sections
    $current_type = "";

    foreach($csv as $csv_line) {
      if ($csv_line[0] != "") { // header row
        $current_type = $csv_line[0];
      } else { // object
        $items[$current_type][] = $csv_line;
      }
    }

    // process each section in turn
    foreach($items["Operation"] as $csv_line) {
        $shmu = \Drupal::entityTypeManager()->getStorage('asset')->load($csv_line[2]);
        $project = \Drupal::entityTypeManager()->getStorage('asset')->load($shmu->get('project')->target_id);
      
        $operation_submission = [];
        $operation_submission['type'] = 'operation';
  
        $operation_submission['shmu'] = $shmu;
        $operation_submission['field_operation_date'] = strtotime($csv_line[3]);
        $operation_submission['field_operation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_operation_type', 'name' => $csv_line[9]]));
        $operation_submission['field_ownership_status'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_equipment_ownership', 'name' => $csv_line[4]]));
        $operation_submission['field_tractor_self_propelled_machine'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_tractor_self_propelled_machine', 'name' => $csv_line[5]]));;
        $operation_submission['field_row_number'] = $csv_line[6];
        $operation_submission['field_width'] = $csv_line[7];
        $operation_submission['field_horsepower'] = $csv_line[8];
        $operation_submission['project'] = $project;
        $operation_to_save = Asset::create($operation_submission);
        $operation_to_save->save();

        $operation_ref_nums[$csv_line[1]] = $operation_to_save;

        $item_count++;

    }

    foreach($items["Input"] as $csv_line) {
      $operation = $operation_ref_nums[$csv_line[2]];
      $project = $operation->id;

      $input_submission = [];
      $input_submission['type'] = 'input';
      $input_submission['field_input_date'] = strtotime($csv_line[3]);
      $input_submission['field_input_category'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_input', 'name' => $csv_line[4]]));
      $input_submission['field_input'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_input', 'name' => $csv_line[5]]));
      $input_submission['field_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_unit', 'name' => $csv_line[6]]));
      $input_submission['field_rate_units'] = $csv_line[7];
      $input_submission['field_cost_per_unit'] = $csv_line[8];
      $input_submission['field_custom_application_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_unit', 'name' => $csv_line[9]]));;

      $operation_taxonomy_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($operation->get('field_operation')->target_id);
      $input_taxonomy_name = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_input', 'name' => $csv_line[5]]));
      
      $input_submission['name'] = $operation_taxonomy_name->getName() . "_" . $input_taxonomy_name->getName() . "_" . $csv_line[3];
      $input_submission['project'] = $project;

      $input_to_save = Asset::create($input_submission);
      $input_to_save->save();
        
      $operation->get('field_input')[] = $input_to_save->id();
      $operation->save();

      $input_ref_nums[$csv_line[1]] = $input_to_save;

      $item_count++;

    }

    foreach($items["OpCosts"] as $csv_line) {
     $operation = $operation_ref_nums[$csv_line[1]];

      $cost_submission = [];
      $cost_submission ['type'] = 'cost_sequence';
      $cost_submission ['field_cost_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_operation_type', 'name' => $csv_line[2]]));;
      $cost_submission ['field_cost'] = $csv_line[3];

      $other_cost = Asset::create($cost_submission);
      $other_cost->save();


      $new_cost_id = $other_cost->id();
      $old_cost_sequence_target_ids = $operation->get('field_operation_cost_sequences');
      //dpm($cost_sequence_target_ids);
      $cost_sequence_target_ids = [];
      foreach ($old_cost_sequence_target_ids as $val) {
        $cost_sequence_target_ids[] = $val->target_id;
      }

      // add new cost_id to existing sequence and save it back
      $cost_sequence_target_ids[] = $new_cost_id;
      $operation->set('field_operation_cost_sequences', $cost_sequence_target_ids);
      $operation->save();

      $item_count++;
    }
    
    foreach($items["InputCosts"] as $csv_line) {
      $input = $input_ref_nums[$csv_line[1]];

      $cost_submission = [];
      $cost_submission ['type'] = 'cost_sequence';
      $cost_submission ['field_cost_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_operation_type', 'name' => $csv_line[2]]));
      $cost_submission ['field_cost'] = $csv_line[3];

      $other_cost = Asset::create($cost_submission);
      $other_cost->save();

      $new_cost_id = $other_cost->id();

      $old_cost_sequence_target_ids = $input->get('field_input_cost_sequences');
      
      $cost_sequence_target_ids = [];
      foreach ($old_cost_sequence_target_ids as $val) {
        $cost_sequence_target_ids[] = $val->target_id;
      }

      // add new cost_id to existing sequence and save it back. is this efficient? no.
      $cost_sequence_target_ids[] = $new_cost_id;
      $input->set('field_input_cost_sequences', $cost_sequence_target_ids);
      $input->save();

      $item_count++;
    }

    $out_str = "";

    $out_str .= "Processed " . "<b>" . $item_count . "</b>" . " items from " . "<b>" . $file_name . "</b>"  . ".";
    $out_str .= "<br /><br />";
    
    $out_str .= "<b>" . count($items["Operation"]) . "</b>" . " Operations.<br />";
    // foreach($operation_ref_nums as $op) {
    //   dpm($op);
    //   $out_str .= "<a href=\"/edit/operation/" . $op->id->target_id ."\">" . "id" . $op->id->target_id  . "</a>";
    // }
    // $out_str .= "<br />";

    $out_str .= "<b>" . count($items["Input"]) . "</b>" . " Inputs.<br />";
    $out_str .= "<b>" . count($items["InputCosts"]) . "</b>" . " Input costs.<br />";
    $out_str .= "<b>" . count($items["OpCosts"]) . "</b>" . " Operation costs.<br />";

    return [
      "#children" => $out_str,
    ];
    
  }
  
  public function process_operations_with_other_costs() {

    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));

    $oc_index =  array_search("other_costs",$csv[0]);

    $csv_oc = $csv[1][$oc_index];

    $result = str_replace( '"', '', $csv_oc);

    $exps = explode("|",$result);

    $csid = [];

    foreach( $exps as $exp){
      
      $cval = explode(",",$exp);
      $cost = $cval[0];
      $cost_type = $cval[1];
      
      //create cost sequence
      $cost_sequence = [];
      $cost_sequence['type'] = 'cost_sequence';
      $cost_sequence['field_cost_type'] = ['target_id' => $cost_type];
      $cost_sequence['field_cost'] = $cost;
      $cost_sequenceN = Asset::create($cost_sequence);

      $cost_sequenceN->save();

      $nid = $cost_sequenceN->id();

      $csid[] = $nid;

    }

    $shmu = \Drupal::entityTypeManager()->getStorage('asset')->load($csv[1][0]);
    $project = \Drupal::entityTypeManager()->getStorage('asset')->load($shmu->get('project')->target_id);

    $field_input = \Drupal::entityTypeManager()->getStorage('asset')->load($csv[1][2]);


    $operation_submission = [];
    $operation_submission['type'] = 'operation';

    $operation_submission['shmu'] = $shmu;
    $operation_submission['field_operation_date'] = strtotime($csv[1][1]);
    //$operation_submission['field_input'] = $field_input;
    $operation_submission['field_operation'] = $csv[1][3];
    $operation_submission['field_ownership_status'] = $csv[1][4];
    $operation_submission['field_tractor_self_propelled_machine'] = $csv[1][5];
    $operation_submission['field_row_number'] = $csv[1][6];
    $operation_submission['field_width'] = $csv[1][7];
    $operation_submission['field_horsepower'] = $csv[1][8];
    $operation_submission['project'] = $project;
    $operation_submission['field_operation_cost_sequences'] = $csid;
    $operation_to_save = Asset::create($operation_submission);


    $operation_to_save->save();

    return [
      "#children" => nl2br(print_r("saved", true)),
    ];
  }
  public function process_soil_test_results() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
  
    foreach($csv as $csv_line) {
  
      $soil_sample_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_sample', 'name' => $csv_line[0]]));
      $lab_method = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'lab_testing_method', 'name' => $csv_line[1]]));
      $project = \Drupal::entityTypeManager()->getStorage('asset')->load($lab_method->get('project')->target_id);
  
      $soil_test_results_submission = [];
      $soil_test_results_submission['name'] = 'Soil Test Results';
      $soil_test_results_submission['type'] = 'lab_result';
      $soil_test_results_submission['project'] = $project;
      
      $soil_test_results_submission['field_lab_result_soil_sample'] = $soil_sample_id;
      $soil_test_results_submission['field_lab_result_method'] = $lab_method;
      $soil_test_results_submission['field_lab_result_raw_soil_organic_carbon'] = $csv_line[2];
      $soil_test_results_submission['field_lab_result_raw_aggregate_stability'] = $csv_line[3];
      $soil_test_results_submission['field_lab_result_raw_respiration'] =  $csv_line[4];
      $soil_test_results_submission['field_lab_result_active_carbon'] = $csv_line[5];
      $soil_test_results_submission['field_lab_result_available_organic_nitrogen'] = $csv_line[6];
      $soil_test_results_submission['field_lab_result_sf_bulk_density_dry_weight'] = $csv_line[7];
      $soil_test_results_submission['field_lab_result_sf_infiltration_rate'] = $csv_line[8];
      $soil_test_results_submission['field_lab_result_sf_ph_value'] = $csv_line[9];
      $soil_test_results_submission['field_lab_result_sf_electroconductivity'] = $csv_line[10];
      $soil_test_results_submission['field_lab_result_sf_ec_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[11]]));
      $soil_test_results_submission['field_lab_result_sf_cation_exchange_capacity'] = $csv_line[12];
      $soil_test_results_submission['field_lab_result_sf_nitrate_n'] = $csv_line[13];
      $soil_test_results_submission['field_lab_result_sf_nitrate_n_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[14]]));
      $soil_test_results_submission['field_lab_result_sf_nitrogen_by_dry_combustion'] = $csv_line[15];
      $soil_test_results_submission['field_lab_result_sf_phosphorous'] = $csv_line[16];
      $soil_test_results_submission['field_lab_result_sf_phosphorous_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[17]]));
      $soil_test_results_submission['field_lab_result_sf_potassium'] = $csv_line[18];
      $soil_test_results_submission['field_lab_result_sf_potassium_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[19]]));
      $soil_test_results_submission['field_lab_result_sf_calcium'] = $csv_line[20];  
      $soil_test_results_submission['field_lab_result_sf_calcium_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[21]]));
      $soil_test_results_submission['field_lab_result_sf_magnesium'] = $csv_line[22];
      $soil_test_results_submission['field_lab_result_sf_magnesium_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[23]]));
      $soil_test_results_submission['field_lab_result_sf_sulfur'] = $csv_line[24];
      $soil_test_results_submission['field_lab_result_sf_sulfur_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[25]]));
      $soil_test_results_submission['field_lab_result_sf_iron'] = $csv_line[26];
      $soil_test_results_submission['field_lab_result_sf_iron_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[27]]));
      $soil_test_results_submission['field_lab_result_sf_manganese'] = $csv_line[28];
      $soil_test_results_submission['field_lab_result_sf_manganese_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[29]]));
      $soil_test_results_submission['field_lab_result_sf_copper'] = $csv_line[30];
      $soil_test_results_submission['field_lab_result_sf_copper_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[31]]));
      $soil_test_results_submission['field_lab_result_sf_zinc'] = $csv_line[32];
      $soil_test_results_submission['field_lab_result_sf_zinc_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[33]]));
      $soil_test_results_submission['field_lab_result_sf_boron'] = $csv_line[34];
      $soil_test_results_submission['field_lab_result_sf_boron_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[35]]));
      $soil_test_results_submission['field_lab_result_sf_aluminum'] = $csv_line[36];
      $soil_test_results_submission['field_lab_result_sf_aluminum_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[37]]));
      $soil_test_results_submission['field_lab_result_sf_molybdenum'] = $csv_line[38];
      $soil_test_results_submission['field_lab_result_sf_molybdenum_lab_interpretation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_lab_interpretation', 'name' => $csv_line[39]]));
  
      $soil_test_results_submission_to_save = Asset::create($soil_test_results_submission);
        
        $soil_test_results_submission_to_save->save();
        $out = $out + 1;
    }
      return [
        "#children" => "saved " . $out . " soil test results.",
      ];
  }

}