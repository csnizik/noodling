<?php
namespace Drupal\csc_csv_import\Controller;
include 'CscCsvImportFunctions.php';

use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\asset\Entity\Asset;
use Drupal\log\Entity\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\views\Views;
use Drupal\Core\Url;

/**
 * Provides route responses for the Example module.
 */
class CscCsvImportControllerBatch extends ControllerBase {

  #public $batch = [
  #  'operations' => [],
  #  'finished'   => 'importFinished',
  #  //'file'       => 'modules/custom/csc_csv_import/src/Controller/csvImportFunction.php'
  #];

  public function process_workbook(Request $request) {
    set_time_limit(500);
    $out = [];      //output messages: imported sheets;
    $output = '';     //output messages: skipped sheets;

    $quarter_options = [
      'Jan 1 - March 31', 
      'April 1 - June 30',
      'July 1 - September 30',
      'October 1 - December 31',
    ];

    $year_options = [
      '2022', 
      '2023',
    ];
    
    $year = $year_options[$request->request->get('year')];
    $quarter = $quarter_options[$request->request->get('quarter')];
    $file = $request->files->get('files')['file'];

    if ($file) {
      $allowedFileType = [
          'application/vnd.ms-excel',
          'text/xls',
          'text/xlsx',
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      ];

      if (in_array($file->getClientMimeType(), $allowedFileType)) {
          //get file extension
          $extension = ucfirst(strtolower($file->getClientOriginalExtension()));
          
          //read the workbook but only get the sheets that is relevent
          $sheets = [
            'Coversheet'                     => ['callback' => 'import_coversheet',                             'end_col' =>  2], 
            'Project Summary'                => ['callback' => 'import_project_summary',                        'end_col' => 35], 
            'Partner Activities'             => ['callback' => 'import_partner_activities',                     'end_col' => 32], 
            'Marketing Activities'           => ['callback' => 'import_market_activities',                      'end_col' => 31], 
            'Producer Enrollment'            => ['callback' => 'import_producer_enrollment',                    'end_col' => 31], 
            'Field Enrollment'               => ['callback' => 'import_field_enrollment',                       'end_col' => 72], 
            'Farm Summary'                   => ['callback' => 'import_farm_summary',                           'end_col' => 29], 
            'Field Summary'                  => ['callback' => 'import_field_summary',                          'end_col' => 49],
            'GHG Benefits - Alt Models'      => ['callback' => 'import_ghg_benefits_alt_models',                'end_col' => 28], 
            'GHG Benefits - Measured'        => ['callback' => 'import_ghg_benefits_measured',                  'end_col' => 20], 
            'Addl Envl Benefits'             => ['callback' => 'import_addl_envl_benefits',                     'end_col' => 57],
            'Alley Cropping'                 => ['callback' => 'import_alley_cropping',                         'end_col' =>  8], 
            'Combustion System Improvement'  => ['callback' => 'import_combustion_system_improvement',          'end_col' => 16], 
            'Conservation Cover'             => ['callback' => 'import_conservation_cover',                     'end_col' =>  7], 
            'Conservation Crop Rotation'     => ['callback' => 'import_conservation_crop_rotation',             'end_col' => 11], 
            'Contour Buffer Strips'          => ['callback' => 'import_contour_buffer_strips',                  'end_col' =>  8], 
            'Cover Crop'                     => ['callback' => 'import_cover_crop',                             'end_col' =>  9], 
            'Critical Area Planting'         => ['callback' => 'import_critical_area_planting',                 'end_col' =>  7], 
            'Feed Mgmt'                      => ['callback' => 'import_feed_management',                        'end_col' => 10], 
            'Field Border'                   => ['callback' => 'import_field_border',                           'end_col' =>  7], 
            'Filter Strip'                   => ['callback' => 'import_filter_strip',                           'end_col' =>  8],
            'Forest Farming'                 => ['callback' => 'import_forest_farming',                         'end_col' =>  7], 
            'Forest Stand Improvement'       => ['callback' => 'import_forest_stand_improvement',               'end_col' =>  7], 
            'Grassed Waterway'               => ['callback' => 'import_grassed_waterway',                       'end_col' =>  7], 
            'Hedgerow Planting'              => ['callback' => 'import_hedgerow_planting',                      'end_col' =>  8], 
            'Herbaceous Wind Barriers'       => ['callback' => 'import_herbaceous_wind_barriers',               'end_col' =>  9],
            'Mulching'                       => ['callback' => 'import_mulching',                               'end_col' =>  8], 
            'Nutrient Mgmt'                  => ['callback' => 'import_nutrient_management',                    'end_col' => 14], 
            'Pasture & Hay Planting'         => ['callback' => 'import_pasture_and_hay_planting',               'end_col' =>  9], 
            'Prescribed Grazing'             => ['callback' => 'import_prescribed_grazing',                     'end_col' =>  7], 
            'Range Planting'                 => ['callback' => 'import_range_planting',                         'end_col' =>  7],
            'Residue & Tillage Mgmt_NoTill'  => ['callback' => 'import_residue_and_tillage_management_notill',  'end_col' =>  7], 
            'Residue & Tillage Mgmt_RedTill' => ['callback' => 'import_residue_and_tillage_management_redtill', 'end_col' =>  7], 
            'Riparian Forest Buffer'         => ['callback' => 'import_riparian_forest_buffer',                 'end_col' =>  8], 
            'Riparian Herbaceous Cover'      => ['callback' => 'import_riparian_herbaceous_cover',              'end_col' =>  7],
            'Roofs & Covers'                 => ['callback' => 'import_roofs_and_covers',                       'end_col' =>  8], 
            'Silvopasture'                   => ['callback' => 'import_silvopasture',                           'end_col' =>  8], 
            'Stripcropping'                  => ['callback' => 'import_stripcropping',                          'end_col' =>  9], 
            'Tree Shrub Establishment'       => ['callback' => 'import_tree_shrub_establishment',               'end_col' =>  8], 
            'Vegetative Barrier'             => ['callback' => 'import_vegetative_barrier',                     'end_col' =>  8], 
            'Waste Separation Facility'      => ['callback' => 'import_waste_separation_facility',              'end_col' =>  9],
            'Waste Storage Facility'         => ['callback' => 'import_waste_storage_facility',                 'end_col' =>  7], 
            'Waste Treatment'                => ['callback' => 'import_waste_treatment',                        'end_col' =>  7], 
            'Waste Treatment Lagoon'         => ['callback' => 'import_waste_treatment_lagoon',                 'end_col' =>  9], 
            'WindShelter Est Reno'           => ['callback' => 'import_windshelter_est_reno',                   'end_col' =>  9], 
            'Anaerobic Digester'             => ['callback' => 'import_anaerobic_digester',                     'end_col' => 11],
          ];

          $reader = IOFactory::createReader($extension);
          $reader->setReadDataOnly(TRUE);
          $reader->setReadEmptyCells(FALSE);
          $reader->setLoadSheetsOnly(array_keys($sheets));
          $spreadSheet = $reader->load($file);
          $sheetCount = $spreadSheet->getSheetCount();

          #foreach ($spreadSheet->getWorksheetIterator() as $ws) {
          #  print('<br>' . $ws->getTitle() . "<br>");
          #  foreach ($ws->getRowIterator() as $row) {
          #    $cellIterator = $row->getCellIterator();
          #    $cellIterator->setIterateOnlyExistingCells(FALSE);

          #    print('=============================================<br>');
          #    foreach ($cellIterator as $cell) {
          #      $col = $cell->getColumn();
          #      $val = $cell->getValue();
          #      print("Cell " . $col . ": " . $val . "<br>");
          #      var_dump($val);
          #      print('<br>');
          #    }
          #  }
          #}
          
          // Temp variable for project ID
          $project_id_field = '';
          $new_entities = [];
          $new_violations = [];
          $new_details = [];

          // Process each sheet in the workbook.
          for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadSheet->getSheet($i);
            $sheet_name = $sheet->getTitle();
            $updated = 0;

            $callback = $sheets[$sheet_name]['callback'];
            $end_column = $sheets[$sheet_name]['end_col'];

            $records = $this->processImport($sheet, $callback, $end_column, $year, $quarter, $project_id_field, $new_entities);
            foreach ($records as $e) {
              array_push($new_entities, $e);

              if ($e['updated'] == TRUE) {
                $updated += 1;
              }

              foreach ($e['violations'] as $v) {
                $new_violations[] = '<br><b>' . $e['sheetname'] . ', Row ' . $e['row_num'] . ' - ' . $v->getPropertyPath() . ': </b><br>' . $v->getMessage() . '<br>';
              }
            }

            $details = $this->process_import_details($sheet_name, $records, $updated);
            array_push($new_details, $details);
            //$out[] = serialize(array($sheet_name, count($records), $updated, $entity_type, $machine_name, $total_before));

            if ($sheet_name == 'Coversheet') {
              $project_id_field = $records[0]['entity']->csc_project_id_field->value;
            }
          }

          $first_sheet = $spreadSheet->getSheet(0)->getTitle();
          $import_history = $this->process_import_history($year, $quarter, $file, $first_sheet);

          $import_history->save();
          $import_history_id = $import_history->id();

          foreach ($new_entities as $new_entity) {
            $new_entity['entity']->csc_import_history_reference[] = $import_history;
            $new_entity['entity']->save();
          }

          foreach ($new_details as $new_detail) {
            $new_detail->csc_import_history_reference = $import_history;
            $new_detail->save();
          }

          #batch_set($this->batch);
          #batch_process('/');


          //Purge the uploaded file after import is completed.
          #unlink($targetPath);
      } else {    
        $output = "Invalid File Type. Upload Excel File.";
      }
    }

    $url = Url::fromRoute('csc_csv_import.import_results', ['id' => $import_history_id]);
    $response = new RedirectResponse($url->toString());
    return $response;
  }

  public function process_import_history($year, $quarter, $file, $first_sheet){
    $import_history_submission = [];
    $import_history_submission['type'] = 'csc_import_history';

    $import_history_submission['csc_year_of_reporting'] = $year;
    $import_history_submission['csc_month_of_reporting'] = $quarter;
    $import_history_submission['csc_file_uploaded'] = $file->getClientOriginalName();
    $import_history_submission['csc_attempt_status'] = "Success";
    $import_history_submission['csc_time_submitted'] = (new \DateTime())->getTimestamp();
    $import_history_submission['csc_by_user'] = \Drupal::currentUser()->getAccountName();
    $import_history_submission['csc_workbook_type'] = ($first_sheet == "Coversheet") ? "Main" : "Supplemental";
    $import_history_submission['csc_submission_details'] = 'link_placeholder';

    $import_history = Asset::create($import_history_submission);
    return $import_history;

  }

  public function process_import_details($sheet_name, $records, $updated){
    $entity_type = $records[0]['type'][0];
    $machine_name = $records[0]['type'][1];
    $query = \Drupal::entityQuery($entity_type)->condition('type', $machine_name);
    $total_before = $query->count()->execute();

    $submission = [];
    $submission['type'] = 'csc_import_details';

    $submission['csc_import_sheetname'] = $sheet_name;
    $submission['csc_import_record_cnt'] = count($records);
    $submission['csc_import_updated_cnt'] = $updated;
    $submission['csc_import_entity_type'] = $entity_type;
    $submission['csc_import_machine_name'] = $machine_name;
    $submission['csc_import_records_before'] = $total_before;

    $import_details = Log::create($submission);
    return $import_details;
  }

  public function processImport($in_sheet, $importFunction, $end_column, $year, $quarter, $project_id_field, $new_entities){
    $record_count = 0;
    $start_column = 2;
    $row = 7;
    $processed_entities = Array();

    //the import template for field summary entity has its data starts on row 6
    //while all other sheets start on row 7. 
    //also the coversheet only uses the second column as a single entry,
    //while all other sheets use one full row per entry.
    //the following lines of code are created to adjust for this discrepancy. 
    if ($importFunction == 'import_coversheet') {
      $dataArray = [];
      $row = 6;
    
      for($row; $row <= 16; $row++){
        $cellValue = $in_sheet->getCellByColumnAndRow($start_column, $row)->getValue();
    
        array_push($dataArray, $cellValue);
      }

      $params = [
        'year' => $year,
        'quarter' => $quarter,
        'data_array' => array_map('base64_encode', $dataArray),
        'count' => $record_count,
        'project_id' => $project_id_field,
        'new_entities' => $new_entities,
        'sheetname' => $in_sheet->getTitle(),
      ];

      //import new coversheet
      $context = Array();
      $processed_entities[] = $importFunction($params, $context);
      return $processed_entities;
    }
    else {
      if($importFunction == 'import_field_summary'){
        $row = 6;
      }

      for($row; ; $row++){
        $startCell = Coordinate::stringFromColumnIndex($start_column) . $row;
        $endCell = Coordinate::stringFromColumnIndex($end_column) . $row;

        //read the entire row
        $dataArray = $in_sheet->rangeToArray($startCell . ':' . $endCell);

        //if the row is empty then we reach the end of rows and stop importing
        if(empty(array_filter($dataArray[0]))) {
          break;
        }
        
        //increment record count
        $record_count = $record_count + 1;

        //import new project summary record
        //do this in a batch
        $params = [
          'year' => $year,
          'quarter' => $quarter,
          'data_array' => array_map('base64_encode', $dataArray[0]),
          'count' => $record_count,
          'project_id' => $project_id_field,
          'new_entities' => $new_entities,
          'sheetname' => $in_sheet->getTitle(),
        ];

        #$this->batch['operations'][] = [$importFunction, [$params]];
        $context = Array();
        $processed_entities[] = $importFunction($params, $context);

      }
      return $processed_entities;
    }
  }
}