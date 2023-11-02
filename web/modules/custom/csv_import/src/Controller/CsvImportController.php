<?php
namespace Drupal\csv_import\Controller;
include 'csvImportFunctions.php';

use Drupal\Core\Controller\ControllerBase;
use Drupal\asset\Entity\Asset;
use Drupal\log\Entity\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
        project summary:
        <form action="/csv_import/upload_project_summary" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        market activities:
        <form action="/csv_import/upload_market_activities" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        environmental benefits:
        <form action="/csv_import/upload_environmental_benefits" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        farm summary:
        <form action="/csv_import/upload_farm_summary" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        field enrollment:
        <form action="/csv_import/upload_field_enrollment" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        partner activities:
        <form action="/csv_import/upload_partner_activities" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        producer enrollment:
        <form action="/csv_import/upload_producer_enrollment" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        field summary:
        <form action="/csv_import/upload_field_summary" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        ghg benefit - measured:
        <form action="/csv_import/upload_ghg_benefits_measured" enctype="multipart/form-data" method="post">
        <input type="file" id="file" name="file">
        <input type="submit">
        </form>
        ghg benefits - alternate modeled:
        <form action="/csv_import/upload_ghg_benefits_alternate_modeled" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - alley cropping:
        <form action="/csv_import/upload_alley_cropping" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - anaerobic digester:
        <form action="/csv_import/upload_anaerobic_digester" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - combustion system improvement:
        <form action="/csv_import/upload_combustion_system_improvement" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - conservation cover:
        <form action="/csv_import/upload_conservation_cover" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - conservation crop rotation:
        <form action="/csv_import/upload_conservation_crop_rotation" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - contour buffer strips:
        <form action="/csv_import/upload_contour_buffer_strips" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - cover crop:
        <form action="/csv_import/upload_cover_crop" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - critical area planting:
        <form action="/csv_import/upload_critical_area_planting" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - feed management:
        <form action="/csv_import/upload_feed_management" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - field_border:
        <form action="/csv_import/upload_field_border" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - filter strip:
        <form action="/csv_import/upload_filter_strip" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - forest_farming:
        <form action="/csv_import/upload_forest_farming" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - forest stand improvement:
        <form action="/csv_import/upload_forest_stand_improvement" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - grassed waterway:
        <form action="/csv_import/upload_grassed_waterway" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - hedgerow planting:
        <form action="/csv_import/upload_hedgerow_planting" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - herbaceous wind barriers:
        <form action="/csv_import/upload_herbaceous_wind_barriers" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - mulching:
        <form action="/csv_import/upload_mulching" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - nutrient management:
        <form action="/csv_import/upload_nutrient_management" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - pasture and hay planting:
        <form action="/csv_import/upload_pasture_and_hay_planting" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - prescribed grazing:
        <form action="/csv_import/upload_prescribed_grazing" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - range planting:
        <form action="/csv_import/upload_range_planting" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - residue and tillage management no till:
        <form action="/csv_import/upload_residue_and_tillage_management_notill" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - residue and tillage management reduced till:
        <form action="/csv_import/upload_residue_and_tillage_management_redtill" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - riparian forest buffer:
        <form action="/csv_import/upload_riparian_forest_buffer" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - riparian herbaceous cover:
        <form action="/csv_import/upload_riparian_herbaceous_cover" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - roofs and covers:
        <form action="/csv_import/upload_roofs_and_covers" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - silvopasture:
        <form action="/csv_import/upload_silvopasture" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - stripcropping:
        <form action="/csv_import/upload_stripcropping" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - tree shrub establishment:
        <form action="/csv_import/upload_tree_shrub_establishment" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - vegetative barrier:
        <form action="/csv_import/upload_vegetative_barrier" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - waste separation facility:
        <form action="/csv_import/upload_waste_separation_facility" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - waste storage facility:
        <form action="/csv_import/upload_waste_storage_facility" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - waste treatment:
        <form action="/csv_import/upload_waste_treatment" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - waste treatment lagoon:
        <form action="/csv_import/upload_waste_treatment_lagoon" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - windbreak/shelterbelt establishment and renovation:
        <form action="/csv_import/upload_windshelter_est_reno" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - alley cropping:
        <form action="/csv_import/upload_alley_cropping" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - anaerobic digester:
        <form action="/csv_import/upload_anaerobic_digester" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - combustion system improvement:
        <form action="/csv_import/upload_combustion_system_improvement" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - conservation cover:
        <form action="/csv_import/upload_conservation_cover" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - conservation crop rotation:
        <form action="/csv_import/upload_conservation_crop_rotation" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - contour buffer strips:
        <form action="/csv_import/upload_contour_buffer_strips" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - cover crop:
        <form action="/csv_import/upload_cover_crop" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - critical area planting:
        <form action="/csv_import/upload_critical_area_planting" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - feed management:
        <form action="/csv_import/upload_feed_management" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - field_border:
        <form action="/csv_import/upload_field_border" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - filter strip:
        <form action="/csv_import/upload_filter_strip" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - forest_farming:
        <form action="/csv_import/upload_forest_farming" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - forest stand improvement:
        <form action="/csv_import/upload_forest_stand_improvement" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - grassed waterway:
        <form action="/csv_import/upload_grassed_waterway" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - hedgerow planting:
        <form action="/csv_import/upload_hedgerow_planting" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - herbaceous wind barriers:
        <form action="/csv_import/upload_herbaceous_wind_barriers" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - mulching:
        <form action="/csv_import/upload_mulching" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - nutrient management:
        <form action="/csv_import/upload_nutrient_management" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - pasture and hay planting:
        <form action="/csv_import/upload_pasture_and_hay_planting" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - prescribed grazing:
        <form action="/csv_import/upload_prescribed_grazing" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - range planting:
        <form action="/csv_import/upload_range_planting" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - residue and tillage management no till:
        <form action="/csv_import/upload_residue_and_tillage_management_notill" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - residue and tillage management reduced till:
        <form action="/csv_import/upload_residue_and_tillage_management_redtill" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - riparian forest buffer:
        <form action="/csv_import/upload_riparian_forest_buffer" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - riparian herbaceous cover:
        <form action="/csv_import/upload_riparian_herbaceous_cover" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - roofs and covers:
        <form action="/csv_import/upload_roofs_and_covers" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - silvopasture:
        <form action="/csv_import/upload_silvopasture" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - stripcropping:
        <form action="/csv_import/upload_stripcropping" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - tree shrub establishment:
        <form action="/csv_import/upload_tree_shrub_establishment" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - vegetative barrier:
        <form action="/csv_import/upload_vegetative_barrier" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - waste separation facility:
        <form action="/csv_import/upload_waste_separation_facility" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - waste storage facility:
        <form action="/csv_import/upload_waste_storage_facility" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - waste treatment:
        <form action="/csv_import/upload_waste_treatment" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - waste treatment lagoon:
        <form action="/csv_import/upload_waste_treatment_lagoon" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        supplemental log - windbreak/shelterbelt establishment and renovation:
        <form action="/csv_import/upload_windshelter_est_reno" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
    ',
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
          $sheetnames = ['Coversheet', 'Project Summary', 'Partner Activities', 'Marketing Activities', 'Producer Enrollment', 'Field Enrollment', 
                          'Farm Summary', 'Field Summary', 'GHG Benefits - Alt Models', 'GHG Benefits - Measured', 'Addl Envl Benefits',
                          'Alley Cropping', 'Combustion System Improvement', 'Conservation Cover', 'Conservation Crop Rotation', 
                          'Contour Buffer Strips', 'Cover Crop', 'Critical Area Planting', 'Feed Mgmt', 'Field Border', 'Filter Strip',
                          'Forest Farming', 'Forest Stand Improvement', 'Grassed Waterway', 'Hedgerow Planting', 'Herbaceous Wind Barriers',
                          'Mulching', 'Nutrient Mgmt', 'Pasture & Hay Planting', 'Prescribed Grazing', 'Range Planting',
                          'Residue & Tillage Mgmt_NoTill', 'Residue & Tillage Mgmt_RedTill', 'Riparian Forest Buffer', 'Riparian Herbaceous Cover',
                          'Roofs & Covers', 'Silvopasture', 'Stripcropping', 'Tree Shrub Establishment', 'Vegetative Barrier', 'Waste Separation Facility',
                          'Waste Storage Facility', 'Waste Treatment', 'Waste Treatment Lagoon', 'WindShelter Est Reno', 'Anaerobic Digester'];
          $reader = IOFactory::createReader($extension);
          $reader->setReadDataOnly(TRUE);
          $reader->setLoadSheetsOnly($sheetnames);
          $spreadSheet = $reader->load($targetPath);
          $sheetCount = $spreadSheet->getSheetCount();
          
          // Temp variable for project ID
          $project_id_field = '';

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
              //import coversheet
              case $sheetnames[0]:
                $dataArray = $this->processCoversheet($sheet, 'import_coversheet');
                $project_id_field = strval($dataArray[1]);

                break;
                
              //import project summary
              case $sheetnames[1]:
                $end_column = 35;
                $records = $this->processImport($sheet, 'import_project_summary', $end_column);

                //output message
                $out[] = array('name' =>'Project Summary', 'records' => $records);
                
                break;

              //import partner activities
              case $sheetnames[2]:
                $end_column = 32;
                $records = $this->processImport($sheet, 'import_partner_activities', $end_column);

                //output message
                $out[] = array('name' =>'Partner Activities', 'records' => $records);

                break;
              
              //import market actitivies
              case $sheetnames[3]:
                $end_column = 31;
                $records = $this->processImport($sheet, 'import_market_activities', $end_column);

                //output message
                $out[] = array('name' =>'Market Actitivies', 'records' => $records);
                
                break;

              //import producer enrollment
              case $sheetnames[4]:
                $end_column = 31;
                $records = $this->processImport($sheet, 'import_producer_enrollment', $end_column, '', $project_id_field);

                //output message
                $out[] = array('name' =>'Producer Enrollment', 'records' => $records);

                break;

              //import field enrollment
              case $sheetnames[5]:
                $end_column = 72;
                $records = $this->processImport($sheet, 'import_field_enrollment', $end_column, '', $project_id_field);

                //output message
                $out[] = array('name' =>'Field Enrollment', 'records' => $records);

                break;

              //import farm summary
              case $sheetnames[6]:
                $end_column = 29;
                $records = $this->processImport($sheet, 'import_farm_summary', $end_column);

                //output message
                $out[] = array('name' =>'Farm Summary', 'records' => $records);

                break;

              //import field summary
              case $sheetnames[7]:
                $end_column = 49;
                $records = $this->processImport($sheet, 'import_field_summary', $end_column);

                //output message
                $out[] = array('name' =>'Field Summary', 'records' => $records);

                break;

              //import ghg benefits alt models
              case $sheetnames[8]:
                $end_column = 28;
                $records = $this->processImport($sheet, 'import_ghg_benefits_alt_models', $end_column);

                //output message
                $out[] = array('name' =>'GHG Benefits Alt Models', 'records' => $records);
  
                break;     

              //import ghg benefits measured
              case $sheetnames[9]:
                $end_column = 20;
                $records = $this->processImport($sheet, 'import_ghg_benefits_measured', $end_column);

                //output message
                $out[] = array('name' =>'GHG_Benefits_Measured', 'records' => $records);
  
                break;   

              //import addl envl benefits
              case $sheetnames[10]:
                $end_column = 57;
                $records = $this->processImport($sheet, 'import_addl_envl_benefits', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Addl Envl Benefits', 'records' => $records);

                break;   

              //import alley cropping
              case $sheetnames[11]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_alley_cropping', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Alley Cropping', 'records' => $records);

                break;
              
              //import combustion system improvement
              case $sheetnames[12]:
                $end_column = 16;
                $records = $this->processImport($sheet, 'import_combustion_system_improvement', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Combustion System Improvement', 'records' => $records);

                break;

              //import conservation cover
              case $sheetnames[13]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_conservation_cover', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Conservation Cover', 'records' => $records);

                break;
              
              //import conservation crop rotation
              case $sheetnames[14]:
                $end_column = 11;
                $records = $this->processImport($sheet, 'import_conservation_crop_rotation', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Conservation Crop Rotation', 'records' => $records);

                break;
              
              //import contour buffer strips
              case $sheetnames[15]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_contour_buffer_strips', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Contour Buffer Strips', 'records' => $records);

                break;

              //import cover crop
              case $sheetnames[16]:
                $end_column = 9;
                $records = $this->processImport($sheet, 'import_cover_crop', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Cover Crop', 'records' => $records);

                break;

              //import critical area planting
              case $sheetnames[17]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_critical_area_planting', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Critical Area Planting', 'records' => $records);

                break;

              //import feed management
              case $sheetnames[18]:
                $end_column = 10;
                $records = $this->processImport($sheet, 'import_feed_management', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Feed Management', 'records' => $records);

                break;

              //import field border
              case $sheetnames[19]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_field_border', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Field Border', 'records' => $records);

                break;

              //import filter strip
              case $sheetnames[20]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_filter_strip', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Filter Strip', 'records' => $records);

                break;

              //import forest farming
              case $sheetnames[21]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_forest_farming', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Forest Farming', 'records' => $records);

                break;

              //import forest stand improvement
              case $sheetnames[22]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_forest_stand_improvement', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Forest Stand Improvement', 'records' => $records);

                break;

              //import grassed waterway
              case $sheetnames[23]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_grassed_waterway', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Grassed Waterway', 'records' => $records);

                break;

              //import hedgerow planting
              case $sheetnames[24]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_hedgerow_planting', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Hedgerow Planting', 'records' => $records);

                break;

              //import herbaceous wind barriers
              case $sheetnames[25]:
                $end_column = 9;
                $records = $this->processImport($sheet, 'import_herbaceous_wind_barriers', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Herbaceous Wind Barriers', 'records' => $records);

                break;

              //import mulching
              case $sheetnames[26]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_mulching', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Mulching', 'records' => $records);

                break;

              //import nutrient management
              case $sheetnames[27]:
                $end_column = 14;
                $records = $this->processImport($sheet, 'import_nutrient_management', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Nutrient Management', 'records' => $records);

                break;

              //import pasture and hay planting
              case $sheetnames[28]:
                $end_column = 9;
                $records = $this->processImport($sheet, 'import_pasture_and_hay_planting', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Pasture and Hay Planting', 'records' => $records);

                break;

              //import Perscribed Grazing
              case $sheetnames[29]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_prescribed_grazing', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Prescribed Grazing', 'records' => $records);

                break;

              //import Range Planting
              case $sheetnames[30]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_range_planting', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Range Planting', 'records' => $records);

                break;

              //import Residue and Tillage Management - No-till
              case $sheetnames[31]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_residue_and_tillage_management_notill', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Residue & Tillage Management - No-till', 'records' => $records);

                break;

              //import Residue and Tillage Management - Reduced-till
              case $sheetnames[32]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_residue_and_tillage_management_redtill', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Residue & Tillage Management - Reduced-till', 'records' => $records);

                break;

              //import Riparian Forest Buffer
              case $sheetnames[33]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_riparian_forest_buffer', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Riparian Forest Buffer', 'records' => $records);

                break;

              //import Riparian Herbaceous Cover
              case $sheetnames[34]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_riparian_herbaceous_cover', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Riparian Herbaceous Cover', 'records' => $records);

                break;

              //import Roofs & Covers
              case $sheetnames[35]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_roofs_and_covers', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Roofs & Covers', 'records' => $records);

                break;

              //import Silvopasture
              case $sheetnames[36]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_silvopasture', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Silvopasture', 'records' => $records);

                break;

              //import Stripcropping
              case $sheetnames[37]:
                $end_column = 9;
                $records = $this->processImport($sheet, 'import_stripcropping', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Stripcropping', 'records' => $records);

                break;

              //import Tree Shrub Establishment
              case $sheetnames[38]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_tree_shrub_establishment', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Tree Shrub Establishment', 'records' => $records);

                break;


              //import Vegetative Barrier
              case $sheetnames[39]:
                $end_column = 8;
                $records = $this->processImport($sheet, 'import_vegetative_barrier', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Vegetative Barrier', 'records' => $records);

                break;

              //import Waste Separation Facility
              case $sheetnames[40]:
                $end_column = 9;
                $records = $this->processImport($sheet, 'import_waste_separation_facility', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Waste Separation Facility', 'records' => $records);

                break;

              //import Waste Storage Facility
              case $sheetnames[41]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_waste_storage_facility', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Waste Storage Facility', 'records' => $records);

                break;

              //import Waste Treatment
              case $sheetnames[42]:
                $end_column = 7;
                $records = $this->processImport($sheet, 'import_waste_treatment', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Waste Treatment', 'records' => $records);

                break;

              //import Waste Treatment Lagoon
              case $sheetnames[43]:
                $end_column = 9;
                $records = $this->processImport($sheet, 'import_waste_treatment_lagoon', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Waste Treatment Lagoon', 'records' => $records);

                break;

              //import Windbreak/Shelterbelt Establishment and Renovation
              case $sheetnames[44]:
                $end_column = 9;
                $records = $this->processImport($sheet, 'import_windshelter_est_reno', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Windbreak/Shelterbelt Establishment and Renovation', 'records' => $records);

                break;

                //import Anaerobic Digester
              case $sheetnames[45]:
                $end_column = 11;
                $records = $this->processImport($sheet, 'import_anaerobic_digester', $end_column);
  
                //output message
                $out[] = array('name' =>'Import Anaerobic Digester', 'records' => $records);

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

  public function process_market_activities() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $market_activities_submission = [];
      $market_activities_submission['csc_type'] = 'csc_market_activities';
      $market_activities_submission['csc_name'] = $csv_line[0];
      $market_activities_submission['csc_m_activities_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $csv_line[1]]));
      $market_activities_submission['csc_m_act_mktng_chnl_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_type', 'name' => $csv_line[2]]));
      $market_activities_submission['csc_m_act_mktng_chnl_type_otr'] = $csv_line[3];
      $market_activities_submission['csc_m_act_number_of_buyers'] = $csv_line[4];
      $market_activities_submission['csc_m_activities_buyer_names'] = array_map('trim', explode('|', $csv_line[5]));
      $market_activities_submission['csc_m_act_mktng_chnl_geography'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_geography', 'name' => $csv_line[6]]));
      $market_activities_submission['csc_m_activities_value_sold'] = $csv_line[7];
      $market_activities_submission['csc_m_activities_volume_sold'] = $csv_line[8];
      $market_activities_submission['csc_m_act_volume_sold_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'volume_sold_unit', 'name' => $csv_line[9]]));
      $market_activities_submission['csc_m_act_volume_unit_otr'] = $csv_line[10];
      $market_activities_submission['csc_m_activities_price_premium'] = $csv_line[11];
      $market_activities_submission['csc_m_act_price_premium_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'price_premium_unit', 'name' => $csv_line[12]]));
      $market_activities_submission['csc_m_act_price_premium_unit_otr'] = $csv_line[13];
      $market_activities_submission['csc_m_act_price_premium_to_prod'] = $csv_line[14];
      $product_differentiation_method_array = array_map('trim', explode('|', $csv_line[15]));
      $product_differentiation_method_results = [];
      foreach ($product_differentiation_method_array as $value) {
        $product_differentiation_method_results = array_merge($product_differentiation_method_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'product_differentiation_method', 'name' => $value]));
      }
      $market_activities_submission['csc_m_act_product_diff_mthd'] = $product_differentiation_method_results;
      $market_activities_submission['csc_m_act_product_diff_mthd_otr'] = $csv_line[16];
      $marketing_method_array = array_map('trim', explode('|', $csv_line[17]));
      $marketing_method_results = [];
      foreach ($marketing_method_array as $value) {
        $marketing_method_results = array_merge($marketing_method_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_method', 'name' => $value]));
      }
      $market_activities_submission['csc_m_act_mktng_mthd'] = $marketing_method_results;
      $market_activities_submission['csc_m_act_mktng_mthd_otr'] = $csv_line[18];
      $marketing_channel_identification_array = array_map('trim', explode('|', $csv_line[19]));
      $marketing_channel_identification_results = [];
      foreach ($marketing_channel_identification_array as $value) {
        $marketing_channel_identification_results = array_merge($marketing_channel_identification_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_identification', 'name' => $value]));
      }
      $market_activities_submission['csc_m_act_mktng_chnl_id'] = $marketing_channel_identification_results;
      $market_activities_submission['csc_m_act_mktng_chnl_id_mthd_otr'] = $csv_line[20];
      $traceability_method_array = array_map('trim', explode('|', $csv_line[21]));
      $traceability_method_results = [];
      foreach ($traceability_method_array as $value) {
        $traceability_method_results = array_merge($traceability_method_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'traceability_method', 'name' => $value]));
      }
      $market_activities_submission['csc_m_act_traceability_mthd'] = $traceability_method_results;
      $market_activities_submission['csc_m_act_traceability_mthd_otr'] = $csv_line[22];
      
      $ps_to_save = Log::create($market_activities_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " market activities.",
    ];
    
  }

  public function process_environmental_benefits() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $environmental_benefits_submission = [];
      $environmental_benefits_submission['type'] = 'environmental_benefits';
      $environmental_benefits_submission['name'] = $csv_line[0];
      $environmental_benefits_submission['fiscal_year'] = $csv_line[1];
      $environmental_benefits_submission['fiscal_quarter'] = $csv_line[2];
      $environmental_benefits_submission['field_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'name' => $csv_line[3]]));
      $environmental_benefits_submission['environmental_benefits'] = $csv_line[4];
      $environmental_benefits_submission['nitrogen_loss'] = $csv_line[5];
      $environmental_benefits_submission['nitrogen_loss_amount'] = $csv_line[6];
      $environmental_benefits_submission['nitrogen_loss_amount_unit'] = $csv_line[7];
      $environmental_benefits_submission['nitrogen_loss_amount_unit_other'] = $csv_line[8];
      $environmental_benefits_submission['nitrogen_loss_purpose'] = $csv_line[9];
      $environmental_benefits_submission['nitrogen_loss_purpose_other'] = $csv_line[10];
      $environmental_benefits_submission['phosphorus_loss'] = $csv_line[11];
      $environmental_benefits_submission['phosphorus_loss_amount'] = $csv_line[12];
      $environmental_benefits_submission['phosphorus_loss_amount_unit'] = $csv_line[13];
      $environmental_benefits_submission['phosphorus_loss_amount_unit_other'] = $csv_line[14];
      $environmental_benefits_submission['phosphorus_loss_purpose'] = $csv_line[15];
      $environmental_benefits_submission['phosphorus_loss_purpose_other'] = $csv_line[16];
      $environmental_benefits_submission['other_water_quality'] = $csv_line[17];
      $environmental_benefits_submission['other_water_quality_type'] = $csv_line[18];
      $environmental_benefits_submission['other_water_quality_type_other'] = $csv_line[19];
      $environmental_benefits_submission['other_water_quality_amount'] = $csv_line[20];
      $environmental_benefits_submission['other_water_quality_amount_unit'] = $csv_line[21];
      $environmental_benefits_submission['other_water_quality_amount_unit_other'] = $csv_line[22];
      $environmental_benefits_submission['other_water_quality_purpose'] = $csv_line[23];
      $environmental_benefits_submission['other_water_quality_purpose_other'] = $csv_line[24];
      $environmental_benefits_submission['water_quality'] = $csv_line[25];
      $environmental_benefits_submission['water_quality_amount'] = $csv_line[26];
      $environmental_benefits_submission['water_quality_amount_unit'] = $csv_line[27];
      $environmental_benefits_submission['water_quality_amount_unit_other'] = $csv_line[28];
      $environmental_benefits_submission['water_quality_purpose'] = $csv_line[29];
      $environmental_benefits_submission['water_quality_purpose_other'] = $csv_line[30];
      $environmental_benefits_submission['reduced_erosion'] = $csv_line[31];
      $environmental_benefits_submission['reduced_erosion_amount'] = $csv_line[32];
      $environmental_benefits_submission['reduced_erosion_amount_unit'] = $csv_line[33];
      $environmental_benefits_submission['reduced_erosion_amount_unit_other'] = $csv_line[34];
      $environmental_benefits_submission['reduced_erosion_purpose'] = $csv_line[35];
      $environmental_benefits_submission['reduced_erosion_purpose_other'] = $csv_line[36];
      $environmental_benefits_submission['reduced_energy_use'] = $csv_line[37];
      $environmental_benefits_submission['reduced_energy_use_amount'] = $csv_line[38];
      $environmental_benefits_submission['reduced_energy_use_amount_unit'] = $csv_line[39];
      $environmental_benefits_submission['reduced_energy_use_amount_unit_other'] = $csv_line[40];
      $environmental_benefits_submission['reduced_energy_use_purpose'] = $csv_line[41];
      $environmental_benefits_submission['reduced_energy_use_purpose_other'] = $csv_line[42];
      $environmental_benefits_submission['avoided_land_conversion'] = $csv_line[43];
      $environmental_benefits_submission['avoided_land_conversion_amount'] = $csv_line[44];
      $environmental_benefits_submission['avoided_land_conversion_unit'] = $csv_line[45];
      $environmental_benefits_submission['avoided_land_conversion_unit_other'] = $csv_line[46];
      $environmental_benefits_submission['avoided_land_conversion_purpose'] = $csv_line[47];
      $environmental_benefits_submission['avoided_land_conversion_purpose_other'] = $csv_line[48];
      $environmental_benefits_submission['improved_wildlife_habitat'] = $csv_line[49];
      $environmental_benefits_submission['improved_wildlife_habitat_amount'] = $csv_line[50];
      $environmental_benefits_submission['improved_wildlife_habitat_unit'] = $csv_line[51];
      $environmental_benefits_submission['improved_wildlife_habitat_amount_unit_other'] = $csv_line[52];
      $environmental_benefits_submission['improved_wildlife_habitat_purpose'] = $csv_line[53];
      $environmental_benefits_submission['improved_wildlife_habitat_purpose_other'] = $csv_line[54];
      
      $ps_to_save = Log::create($environmental_benefits_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " Additional Environmental Benefits.",
    ];
    
  }

  public function process_farm_summary() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $farm_summary_submission = [];
      $farm_summary_submission['type'] = 'farm_summary';
      $farm_summary_submission['name'] = $csv_line[0];
      $farm_summary_submission['farm_summary_fiscal_year'] = $csv_line[1];
      $farm_summary_submission['farm_summary_fiscal_quarter'] = $csv_line[2];
      $farm_summary_submission['farm_summary_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $csv_line[3]]));
      $farm_summary_submission['farm_summary_county'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'county', 'name' => $csv_line[4]]));
      $producer_ta_received_array = array_map('trim', explode('|', $csv_line[5]));
      $producer_ta_received_results = [];
      foreach ($producer_ta_received_array as $value) {
        $producer_ta_received_results = array_merge($producer_ta_received_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'producer_ta_received', 'name' => $value]));
      }
      $farm_summary_submission['farm_summary_producer_ta_received'] = $producer_ta_received_results;
      $farm_summary_submission['farm_summary_producer_ta_received_other'] = $csv_line[6];
      $farm_summary_submission['farm_summary_producer_incentive_amount'] = $csv_line[7];
      $incentive_reason_array = array_map('trim', explode('|', $csv_line[8]));
      $incentive_reason_results = [];
      foreach ($incentive_reason_array as $value) {
        $incentive_reason_results = array_merge($incentive_reason_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'incentive_reason', 'name' => $value]));
      }
      $farm_summary_submission['farm_summary_incentive_reason'] = $incentive_reason_results;
      $farm_summary_submission['farm_summary_incentive_reason_other'] = $csv_line[9];
      $incentive_structure_array = array_map('trim', explode('|', $csv_line[10]));
      $incentive_structure_results = [];
      foreach ($incentive_structure_array as $value) {
        $incentive_structure_results = array_merge($incentive_structure_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'incentive_structure', 'name' => $value]));
      }
      $farm_summary_submission['farm_summary_incentive_structure'] = $incentive_structure_results;
      $farm_summary_submission['farm_summary_incentive_structure_other'] = $csv_line[11];
      $incentive_type_array = array_map('trim', explode('|', $csv_line[12]));
      $incentive_type_results = [];
      foreach ($incentive_type_array as $value) {
        $incentive_type_results = array_merge($incentive_type_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'incentive_type', 'name' => $value]));
      }
      $farm_summary_submission['farm_summary_incentive_type'] = $incentive_type_results;
      $farm_summary_submission['farm_summary_incentive_type_other'] = $csv_line[13];
      $farm_summary_submission['farm_summary_payment_on_enrollment'] = $csv_line[14];
      $farm_summary_submission['farm_summary_payment_on_implementation'] = $csv_line[15];
      $farm_summary_submission['farm_summary_payment_on_harvest'] = $csv_line[16];
      $farm_summary_submission['farm_summary_payment_on_mmrv'] = $csv_line[17];
      $farm_summary_submission['farm_summary_payment_on_sale'] = $csv_line[18];
      
      $ps_to_save = Log::create($farm_summary_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " Farm Summary.",
    ];
    
  }

  public function process_field_enrollment() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $field_enrollment_submission = [];
      $field_enrollment_submission['type'] = 'csc_field_enrollment';
      $field_enrollment_submission['name'] = $csv_line[0];
      $field_enrollment_submission['csc_f_enrollment_tract_id'] = $csv_line[1];
      $field_enrollment_submission['csc_f_enrollment_field_id'] = $csv_line[2];
      $field_enrollment_submission['csc_f_enrollment_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $csv_line[3]]));
      $field_enrollment_submission['csc_f_enrollment_prior_field_id'] = $csv_line[4];
      $field_enrollment_submission['csc_f_enrollment_start_date'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[5])->getTimestamp();
      $field_enrollment_submission['csc_f_nrlmnt_total_field_area'] = $csv_line[6];
      $field_enrollment_submission['csc_f_nrlmnt_commodity_category'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_category', 'name' => $csv_line[7]]));
      $field_enrollment_submission['csc_f_enrollment_baseline_yield'] = $csv_line[8];
      $field_enrollment_submission['csc_f_nrlmnt_base_yield_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'baseline_yield_unit', 'name' => $csv_line[9]]));
      $field_enrollment_submission['csc_f_nrlmnt_base_yield_unit_otr'] = $csv_line[10];
      $field_enrollment_submission['csc_f_nrlmnt_base_yield_loc'] = $csv_line[11];
      $field_enrollment_submission['csc_f_nrlmnt_base_yield_loc_otr'] = $csv_line[12];
      $field_enrollment_submission['csc_f_enrollment_field_land_use'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_land_use', 'name' => $csv_line[13]]));
      $field_enrollment_submission['csc_f_nrlmnt_field_irrigated'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_irrigated', 'name' => $csv_line[14]]));
      $field_enrollment_submission['csc_f_enrollment_field_tillage'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_tillage', 'name' => $csv_line[15]]));
      $field_enrollment_submission['csc_f_nrlmnt_prac_pri_util_prcnt'] = $csv_line[16];
      $field_enrollment_submission['csc_f_nrlmnt_field_any_csaf_prac'] = $csv_line[17];
      $field_enrollment_submission['csc_f_nrlmnt_field_prac_pri_util'] = $csv_line[18];
      $field_enrollment_submission['csc_f_nrlmnt_prac_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[19]]));
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_1'] = $csv_line[20];
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_1'] = $csv_line[21];
      $field_enrollment_submission['csc_f_nrlmnt_prac_year_1'] = $csv_line[22];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_1'] = $csv_line[23];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_1'] = $csv_line[24];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_1'] = $csv_line[25];
      $field_enrollment_submission['csc_f_nrlmnt_prac_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[26]]));
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_2'] = $csv_line[27];
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_2'] = $csv_line[28];
      $field_enrollment_submission['csc_f_nrlmnt_prac_year_2'] = $csv_line[29];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_2'] = $csv_line[30];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_2'] = $csv_line[31];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_2'] = $csv_line[32];
      $field_enrollment_submission['csc_f_nrlmnt_prac_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[33]]));
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_3'] = $csv_line[34];
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_3'] = $csv_line[35];
      $field_enrollment_submission['csc_f_nrlmnt_prac_year_3'] = $csv_line[36];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_3'] = $csv_line[37];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_3'] = $csv_line[38];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_3'] = $csv_line[39];
      $field_enrollment_submission['csc_f_nrlmnt_prac_type_4'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[40]]));
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_4'] = $csv_line[41];
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_4'] = $csv_line[42];
      $field_enrollment_submission['csc_f_nrlmnt_prac_year_4'] = $csv_line[43];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_4'] = $csv_line[44];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_4'] = $csv_line[45];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_4'] = $csv_line[46];
      $field_enrollment_submission['csc_f_nrlmnt_prac_type_5'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[47]]));
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_5'] = $csv_line[48];
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_5'] = $csv_line[49];
      $field_enrollment_submission['csc_f_nrlmnt_prac_year_5'] = $csv_line[50];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_5'] = $csv_line[51];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_5'] = $csv_line[52];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_5'] = $csv_line[53];
      $field_enrollment_submission['csc_f_nrlmnt_prac_type_6'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[54]]));
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_6'] = $csv_line[55];
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_6'] = $csv_line[56];
      $field_enrollment_submission['csc_f_nrlmnt_prac_year_6'] = $csv_line[57];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_6'] = $csv_line[58];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_6'] = $csv_line[59];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_6'] = $csv_line[60];
      $field_enrollment_submission['csc_f_nrlmnt_prac_type_7'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[61]]));
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_7'] = $csv_line[62];
      $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_7'] = $csv_line[63];
      $field_enrollment_submission['csc_f_nrlmnt_prac_year_7'] = $csv_line[64];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_7'] = $csv_line[65];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_7'] = $csv_line[66];
      $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_7'] = $csv_line[67];
      
      $ps_to_save = Asset::create($field_enrollment_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " field enrollment.",
    ];
    
  }

  public function process_partner_activities() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $partner_activities_submission = [];
      $partner_activities_submission['type'] = 'csc_partner_activities';
      $partner_activities_submission['name'] = $csv_line[0];
      $partner_activities_submission['csc_prtnr_act_partner_ein'] = $csv_line[1];
      $partner_activities_submission['csc_prtnr_act_partner_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'partner_type', 'name' => $csv_line[2]]));
      $partner_activities_submission['csc_prtnr_act_partner_poc'] = $csv_line[3];
      $partner_activities_submission['csc_prtnr_act_partner_poc_email'] = $csv_line[4];
      $partner_activities_submission['csc_prtnr_act_partnership_start'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[5])->getTimestamp();
      $partner_activities_submission['csc_prtnr_act_partnership_end'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[6])->getTimestamp();
      $partner_activities_submission['csc_prtnr_act_partnership_init'] = filter_var($csv_line[7], FILTER_VALIDATE_BOOLEAN);
      $partner_activities_submission['csc_prtnr_act_partner_total_req'] = $csv_line[8];
      $partner_activities_submission['csc_prtnr_act_total_match_contrib'] = $csv_line[9];
      $partner_activities_submission['csc_prtnr_act_total_match_incent'] = $csv_line[10];
      $partner_activities_submission['csc_prtnr_act_match_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $csv_line[11]]));
      $partner_activities_submission['csc_prtnr_act_match_amount_1'] = $csv_line[12];
      $partner_activities_submission['csc_prtnr_act_match_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $csv_line[13]]));
      $partner_activities_submission['csc_prtnr_act_match_amount_2'] = $csv_line[14];
      $partner_activities_submission['csc_prtnr_act_match_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $csv_line[15]]));
      $partner_activities_submission['csc_prtnr_act_match_amount_3'] = $csv_line[16];
      $partner_activities_submission['csc_prtnr_act_match_type_other'] = $csv_line[17];
      $training_provided_array = array_map('trim', explode('|', $csv_line[18]));
      $training_provided_results = [];
      foreach ($training_provided_array as $value) {
        $training_provided_results = array_merge($training_provided_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'training_provided', 'name' => $value]));
      }
      $partner_activities_submission['csc_prtnr_act_training_provided'] = $training_provided_results;
      $partner_activities_submission['csc_prtnr_act_training_other'] = $csv_line[19];
      $partner_activities_submission['csc_partner_activity_activity1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $csv_line[20]]));
      $partner_activities_submission['csc_prtnr_act_activity1_cost'] = $csv_line[21];
      $partner_activities_submission['csc_partner_activity_activity2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $csv_line[22]]));
      $partner_activities_submission['csc_prtnr_act_activity2_cost'] = $csv_line[23];
      $partner_activities_submission['csc_partner_activity_activity3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $csv_line[24]]));
      $partner_activities_submission['csc_prtnr_act_activity3_cost'] = $csv_line[25];
      $partner_activities_submission['csc_prtnr_act_activity_other'] = $csv_line[26];
      $partner_activities_submission['csc_prtnr_act_products_supplied'] = $csv_line[27];
      $partner_activities_submission['csc_prtnr_act_product_source'] = $csv_line[28];
      
      $ps_to_save = Asset::create($partner_activities_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " partner activities.",
    ];
    
  }

  public function process_producer_enrollment() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $producer_enrollment_submission = [];
      $producer_enrollment_submission['type'] = 'csc_producer_enrollment';
      $producer_enrollment_submission['name'] = $csv_line[0];
      $producer_enrollment_submission['csc_project_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_project_summary', 'name' => $csv_line[1]]));
      $producer_enrollment_submission['csc_p_enrollment_farm_id'] = $csv_line[2];
      $producer_enrollment_submission['csc_p_enrollment_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $csv_line[3]]));
      $producer_enrollment_submission['csc_p_enrollment_county'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'county', 'name' => $csv_line[4]]));
      $producer_enrollment_submission['csc_p_enrollment_start_date'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[5])->getTimestamp();
      $producer_enrollment_submission['csc_p_enrlmnt_underserved_status'] = $csv_line[6];
      $producer_enrollment_submission['csc_p_enrollment_total_area'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'total_area', 'name' => $csv_line[7]]));
      $producer_enrollment_submission['csc_p_enrlmnt_total_crop_area'] = $csv_line[8];
      $producer_enrollment_submission['csc_p_enrlmnt_total_livstk_area'] = $csv_line[9];
      $producer_enrollment_submission['csc_p_enrlmnt_total_forest_area'] = $csv_line[10];
      $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $csv_line[11]]));
      $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_1_cnt'] = $csv_line[12];
      $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $csv_line[13]]));
      $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_2_cnt'] = $csv_line[14];
      $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $csv_line[15]]));
      $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_3_cnt'] = $csv_line[16];
      $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_otr'] = $csv_line[17];
      $producer_enrollment_submission['csc_p_enrollment_organic_farm'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'organic_farm', 'name' => $csv_line[18]]));
      $producer_enrollment_submission['csc_p_enrollment_organic_fields'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'organic_fields', 'name' => $csv_line[19]]));
      $producer_enrollment_submission['csc_p_enrlmnt_prod_motivation'] = $csv_line[20];
      $producer_outreach_array = array_map('trim', explode('|', $csv_line[21]));
      $producer_outreach_results = [];
      foreach ($producer_outreach_array as $value) {
        $producer_outreach_results = array_merge($producer_outreach_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'producer_outreach', 'name' => $value]));
      }
      $producer_enrollment_submission['csc_p_enrlmnt_prod_outreach'] = $producer_outreach_results;
      $producer_enrollment_submission['csc_p_enrlmnt_prod_outreach_otr'] = $csv_line[22];
      $producer_enrollment_submission['csc_p_enrlmnt_csaf_experience'] =array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_experience', 'name' => $csv_line[23]]));
      $producer_enrollment_submission['csc_p_enrlmnt_csaf_federal_fds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_federal_funds', 'name' => $csv_line[24]]));
      $producer_enrollment_submission['csc_p_enrlmnt_csaf_st_local_fds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_state_or_local_funds', 'name' => $csv_line[25]]));
      $producer_enrollment_submission['csc_p_enrlmnt_csaf_nonprofit_fds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_nonprofit_funds', 'name' => $csv_line[26]]));
      $producer_enrollment_submission['csc_p_enrlmnt_csaf_market_incent'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_market_incentives', 'name' => $csv_line[27]]));
      
      $ps_to_save = Asset::create($producer_enrollment_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " producer enrollment.",
    ];
    
  }

  public function process_project_summary() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $project_summary_submission = [];
      $project_summary_submission['type'] = 'project_summary';
      $project_summary_submission['name'] = $csv_line[0];
      $project_summary_submission['csc_p_summary_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_category', 'name' => $csv_line[5]]));
      $project_summary_submission['csc_p_summ_ghg_calculation_mthds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_calculation_methods', 'name' => $csv_line[6]]));
      $project_summary_submission['csc_p_summ_ghg_cum_calculation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_cumulative_calculation', 'name' => $csv_line[7]]));
      $project_summary_submission['csc_p_summary_ghg_benefits'] = $csv_line[8]; //strtotime($csv_line[1]);
      $project_summary_submission['csc_p_summ_cum_carbon_stack'] = $csv_line[9];
      $project_summary_submission['csc_p_summ_cum_co2_benefit'] = $csv_line[10];
      $project_summary_submission['csc_p_summ_cum_ch4_benefit'] = $csv_line[11];
      $project_summary_submission['csc_p_summ_cum_n2o_benefit'] = $csv_line[12];
      $project_summary_submission['csc_p_summary_offsets_produced'] = $csv_line[13];
      $project_summary_submission['csc_p_summary_offsets_sale'] = $csv_line[14];
      $project_summary_submission['csc_p_summary_offsets_price'] = $csv_line[15];
      $project_summary_submission['csc_p_summary_insets_produced'] = $csv_line[16];
      $project_summary_submission['csc_p_summary_cost_on_farm'] = $csv_line[17];
      $project_summary_submission['csc_p_summary_mmrv_cost'] = $csv_line[18];
      $project_summary_submission['csc_p_summ_ghg_monitoring_mthd'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_monitoring_method', 'name' => $csv_line[19]]));
      $project_summary_submission['csc_p_summ_ghg_reporting_mthd'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_reporting_method', 'name' => $csv_line[20]]));
      $project_summary_submission['csc_p_summ_ghg_verification_mthd'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_verification_method', 'name' => $csv_line[21]]));
      
      $ps_to_save = Asset::create($project_summary_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " project summary.",
    ];
    
  }

  public function process_ghg_benefits_measured() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $ghg_benefits_measured_submission = [];
      $ghg_benefits_measured_submission['type'] = 'ghg_benefits_measured';
      $ghg_benefits_measured_submission['name'] = $csv_line[0];
      $ghg_benefits_measured_submission['g_benefits_measured_field_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'name' => $csv_line[1]]));
      $ghg_benefits_measured_submission['g_benefits_measured_fiscal_quarter'] = $csv_line[2];
      $ghg_benefits_measured_submission['g_benefits_measured_fiscal_year'] = $csv_line[3];
      $ghg_benefits_measured_submission['g_benefits_measured_ghg_measurement_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_measurement_method', 'name' => $csv_line[4]]));
      $ghg_benefits_measured_submission['g_benefits_measured_ghg_measurement_method_other'] = $csv_line[5];
      $ghg_benefits_measured_submission['g_benefits_measured_lab_name'] = $csv_line[6];
      $ghg_benefits_measured_submission['g_benefits_measured_measurement_start_date'] = \DateTime::createFromFormat("Y-m-d", $csv_line[7])->getTimestamp();
      $ghg_benefits_measured_submission['g_benefits_measured_measurement_end_date'] = \DateTime::createFromFormat("Y-m-d", $csv_line[8])->getTimestamp();
      $ghg_benefits_measured_submission['g_benefits_measured_total_co2_reduction'] = $csv_line[9];
      $ghg_benefits_measured_submission['g_benefits_measured_total_field_carbon_stock'] = $csv_line[10];
      $ghg_benefits_measured_submission['g_benefits_measured_total_ch4_reduction'] = $csv_line[11];
      $ghg_benefits_measured_submission['g_benefits_measured_total_n2o_reduction'] = $csv_line[12];
      $ghg_benefits_measured_submission['g_benefits_measured_soil_sample_result'] = $csv_line[13];
      $ghg_benefits_measured_submission['g_benefits_measured_soil_sample_result_unit'] = $csv_line[14];
      $ghg_benefits_measured_submission['g_benefits_measured_soil_sample_result_unit_other'] = $csv_line[15];
      $ghg_benefits_measured_submission['g_benefits_measured_measurement_type'] = $csv_line[16];
      $ghg_benefits_measured_submission['g_benefits_measured_measurement_type_other'] = $csv_line[17];
      
      $ps_to_save = Log::create($ghg_benefits_measured_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " ghg benefit measured.",
    ];
    
  }
  
  public function process_field_summary() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));

    array_shift($csv);

    $out = 0;

    foreach($csv as $csv_line) {

      $field_summary_submission = [];
      $field_summary_submission['type'] = 'field_summary';
      $field_summary_submission['name'] = $csv_line[0];
      $field_summary_submission['status'] = $csv_line[38];
      $field_summary_submission['flag'] = $csv_line[36];
      $field_summary_submission['notes'] = $csv_line[37];
      $field_summary_submission['f_summary_contract_end_date'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[1])->getTimestamp();
      $field_summary_submission['f_summary_implementation_cost_coverage'] = $csv_line[2];
      $field_summary_submission['f_summary_implementation_cost'] = $csv_line[3];
      $field_summary_submission['f_summary_implementation_cost_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'cost_unit', 'name' => $csv_line[4]]));
      $field_summary_submission['f_summary_date_practice_complete'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[5])->getTimestamp();
      $field_summary_submission['f_summary_fiscal_quarter'] = $csv_line[6];
      $field_summary_submission['f_summary_fiscal_year'] = $csv_line[7];
      $field_summary_submission['f_summary_field_commodity_value'] = $csv_line[8];
      $field_summary_submission['f_summary_field_commodity_volume'] = $csv_line[9];
      $field_summary_submission['f_summary_field_commodity_volume_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_commodity_volume_unit', 'name' => $csv_line[10]]));
      $field_summary_submission['f_summary_field_ghg_calculation'] = $csv_line[11];

      $summary_field_ghg_monitoring_array = array_map('trim', explode('|', $csv_line[12]));
      $summary_field_ghg_monitoring_results = [];

      foreach ($summary_field_ghg_monitoring_array as $value) {
        $summary_field_ghg_monitoring_results = array_merge($summary_field_ghg_monitoring_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_ghg_monitoring', 'name' => $value]));
      }

      $field_summary_submission['f_summary_field_ghg_monitoring'] = $summary_field_ghg_monitoring_results;
      $summary_field_ghg_reporting_array = array_map('trim', explode('|', $csv_line[13]));
      $summary_field_ghg_reporting_results = [];

      foreach ($summary_field_ghg_reporting_array as $value) {
        $summary_field_ghg_reporting_results = array_merge($summary_field_ghg_reporting_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_ghg_reporting', 'name' => $value]));
      }

      $field_summary_submission['f_summary_field_ghg_reporting'] = $summary_field_ghg_reporting_results;
      $summary_field_ghg_verification_array = array_map('trim', explode('|', $csv_line[14]));
      $summary_field_ghg_verification_results = [];

      foreach ($summary_field_ghg_verification_array as $value) {
        $summary_field_ghg_verification_results = array_merge($summary_field_ghg_verification_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_ghg_verification', 'name' => $value]));
      }

      $field_summary_submission['f_summary_field_ghg_verification'] = $summary_field_ghg_verification_results;
      $field_summary_submission['f_summary_field_insets'] = $csv_line[15];
      $field_summary_submission['f_summary_field_carbon_stock'] = $csv_line[16];
      $field_summary_submission['f_summary_field_ch4_emission_reduction'] = $csv_line[17];
      $field_summary_submission['f_summary_field_co2_emission_reduction'] = $csv_line[18];
      $field_summary_submission['f_summary_field_ghg_emission_reduction'] = $csv_line[19];
      $field_summary_submission['f_summary_field_official_ghg_calculations'] = $csv_line[20];
      $field_summary_submission['f_summary_field_n2o_emission_reduction'] = $csv_line[21];
      $field_summary_submission['f_summary_field_offsets'] = $csv_line[22];
      $field_summary_submission['f_summary_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $csv_line[23]]));
      $field_summary_submission['f_summary_incentive_per_acre_or_head'] = $csv_line[24];
      $field_summary_submission['f_summary_marketing_assistance_provided'] = $csv_line[25];
      $field_summary_submission['f_summary_mmrv_assistance_provided'] = $csv_line[26];
      $field_summary_submission['f_summary_implementation_cost_unit_other'] = $csv_line[27];
      $field_summary_submission['f_summary_field_commodity_volume_unit_other'] = $csv_line[28];
      $field_summary_submission['f_summary_field_ghg_monitoring_other'] = $csv_line[29];
      $field_summary_submission['f_summary_field_ghg_reporting_other'] = $csv_line[30];
      $field_summary_submission['f_summary_field_ghg_verification_other'] = $csv_line[31];
      $field_summary_submission['f_summary_field_measurement_other'] = $csv_line[32];

      $summary_practice_type_array = array_map('trim', explode('|', $csv_line[33]));
      $summary_practice_type_results = [];

      foreach ($summary_practice_type_array as $value) {
        $summary_practice_type_results = array_merge($summary_practice_type_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $value]));
      }

      $field_summary_submission['f_summary_practice_type'] = $summary_practice_type_results;
      $field_summary_submission['f_summary_field_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'name' => $csv_line[34]]));
      
      $ps_to_save = Log::create($field_summary_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " field summary.",
    ];

  }  
  
  public function process_g_benefits_alternate_modeled() {

    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $g_benefits_alternate_modeledsubmission = [];
      $g_benefits_alternate_modeledsubmission['name'] = $csv_line[0];
      $g_benefits_alternate_modeledsubmission['type'] = 'ghg_benefits_alternate_modeled';
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_fiscal_year'] = $csv_line[1];
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_fiscal_quarter'] = $csv_line[2];
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_field_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'name' => $csv_line[4]]));
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $csv_line[5]]));

      $g_benefits_alternate_modeled_practice_type_array = array_map('trim', explode('|', $csv_line[6]));
      $g_benefits_alternate_modeled_practice_type_results = [];
      foreach ($g_benefits_alternate_modeled_practice_type_array as $value) {
        $g_benefits_alternate_modeled_practice_type_results = array_merge($g_benefits_alternate_modeled_practice_type_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $value]));
      }

      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_practice_type'] = $g_benefits_alternate_modeled_practice_type_results;
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_ghg_model'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_model', 'name' => $csv_line[7]]));
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_ghg_model_other'] = $csv_line[8];
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_model_start_date'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[9])->getTimestamp();
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_model_end_date'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[10])->getTimestamp();
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_ghg_benefits_estimated'] = $csv_line[11];
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_carbon_stock_estimated'] = $csv_line[12];
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_co2_estimated'] = $csv_line[13];
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_ch4_estimated'] = $csv_line[14];
      $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_n2o_estimated'] = $csv_line[15];

      $gbam_to_save = log::create($g_benefits_alternate_modeledsubmission);

      $gbam_to_save->save();

      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " GHG benefits alternate modeled.",
    ];

  }

  public function process_alley_cropping(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line_raw);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_alley_cropping';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p311_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p311_species_density'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      

    }

    return [
      "#children" => "added " . $out . " Alley Cropping.",
    ];
  }

  public function process_anaerobic_digester(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_anaerobic_digester';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p366_prior_waste_storage_sys'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'waste_storage_system', 'name' => $in_data_array[6]]));
      $supplemental_data_submission['csc_p366_digester_type'] = $in_data_array[7];
      $supplemental_data_submission['csc_p366_digester_type_other'] = $in_data_array[8];
      $supplemental_data_submission['csc_p366_addtl_feedback_source'] = $in_data_array[9];
      $supplemental_data_submission['csc_p366_addtl_fdbk_source_otr'] = $in_data_array[10];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Anaerobic Digester",
    ];
  }


  public function process_combustion_system_improvement(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_combustion_sys_improvement';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p372_prior_fuel_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'fuel_type', 'name' => $csv_line[6]]));
      $supplemental_data_submission['csc_p372_prior_fuel_type_other'] = $csv_line[7];
      $supplemental_data_submission['csc_p372_prior_fuel_amount'] = $csv_line[8];
      $supplemental_data_submission['csc_p372_prior_fuel_amount_unit'] = $csv_line[9];
      $supplemental_data_submission['csc_p372_pri_fuel_amnt_unit_otr'] = $csv_line[10];
      $supplemental_data_submission['csc_p372_fuel_type_after'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'fuel_type', 'name' => $csv_line[11]]));
      $supplemental_data_submission['csc_p372_fuel_type_after_other'] = $csv_line[12];
      $supplemental_data_submission['csc_p372_fuel_amount_after'] = $csv_line[13];
      $supplemental_data_submission['csc_p372_fuel_amount_unit_after'] = $csv_line[14];
      $supplemental_data_submission['csc_p372_fuel_amnt_unit_aft_otr'] = $csv_line[15];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Combustion System Improvement.",
    ];
  }

  public function process_conservation_cover(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_conservation_cover';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p327_species_category'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Conservation Cover.",
    ];
  }

  public function process_conservation_crop_rotation(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_conservation_crop_rotation';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p328_conservation_crop_type'] = $csv_line[6];
      $supplemental_data_submission['csc_p328_change_implemented'] = $csv_line[7];
      $supplemental_data_submission['csc_p328_rotation_tillage_type'] = $csv_line[8];
      $supplemental_data_submission['csc_p328_rotation_till_type_otr'] = $csv_line[9];
      $supplemental_data_submission['csc_p328_total_rotation_length'] = $csv_line[10];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Conservation Crop Rotation.",
    ];
  }

  public function process_contour_buffer_strips(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_contour_buffer_strips';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p332_strip_width'] = $csv_line[6];
      $supplemental_data_submission['csc_p332_species_category'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Contour Buffer Strips.",
    ];
  }

  public function process_cover_crop(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_cover_crop';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p340_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p340_planned_management'] = $csv_line[7];
      $supplemental_data_submission['csc_p340_termination_method'] = $csv_line[8];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Cover Crop.",
    ];
  }

  public function process_critical_area_planting(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_critical_area_planting';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p342_species_category'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Critical Area Planting.",
    ];
  }

  public function process_feed_management(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_feed_management';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p592_crude_protein_percent'] = $csv_line[6];
      $supplemental_data_submission['csc_p592_fat_percent'] = $csv_line[7];
      $supplemental_data_submission['csc_p592_feed_additives'] = $csv_line[8];
      $supplemental_data_submission['csc_p592_feed_additives_other'] = $csv_line[9];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Feed Management.",
    ];
  }

  public function process_field_border(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_field_border';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p386_species_category'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Field Border.",
    ];
  }

  public function process_filter_strip(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_filter_strip';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p393_strip_width'] = $csv_line[6];
      $supplemental_data_submission['csc_p393_species_category'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Filter Strip.",
    ];
  }

  public function process_forest_farming(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_forest_farming';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p379_land_use_prev_years'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Forest Farming.",
    ];
  }

  public function process_forest_stand_improvement(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_forest_stand_improvement';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p666_implementation_purpose'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => '666_implementation_purpose', 'name' => $csv_line[6]]));;

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Forest Stand Improvemet.",
    ];
  }

  public function process_grassed_waterway(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_grassed_waterway';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p412_species_category'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Grassed Waterway.",
    ];
  }

  public function process_hedgerow_planting(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_hedgerow_planting';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p422_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p422_species_density'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Hedgerow Planting.",
    ];
  }

  public function process_herbaceous_wind_barriers(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_herbaceous_wind_barriers';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p603_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p603_barrier_width'] = $csv_line[7];
      $supplemental_data_submission['csc_p603_number_of_rows'] = $csv_line[8];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Herbaceous Wind Barriers.",
    ];
  }

  public function process_mulching(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_mulching';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p484_mulch_type'] = $csv_line[6];
      $supplemental_data_submission['csc_p484_mulch_coverage'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Mulching.",
    ];
  }

  public function process_nutrient_management(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_nutrient_management';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p590_nutrient_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['csc_vid' => 'nutrient_type', 'name' => $csv_line[6]]));
      $supplemental_data_submission['csc_p590_application_method'] = $csv_line[7];
      $supplemental_data_submission['csc_p590_pri_aplctn_method'] = $csv_line[8];
      $supplemental_data_submission['csc_p590_pri_aplctn_timing'] = $csv_line[9];
      $supplemental_data_submission['csc_p590_prior_application_timing'] = $csv_line[10];
      $supplemental_data_submission['csc_p590_application_rate'] = $csv_line[11];
      $supplemental_data_submission['csc_p590_application_rate_unit'] = $csv_line[12];
      $supplemental_data_submission['csc_p590_aplctn_rate_change'] = $csv_line[13];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Nutrient Management.",
    ];
  }

  public function process_pasture_and_hay_planting(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_pasture_hay_planting';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p512_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p512_termination_process'] = $csv_line[7];
      $supplemental_data_submission['csc_p512_otr_term_process'] = $csv_line[8];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Pasture & Hay Planting.",
    ];
  }

  public function process_prescribed_grazing(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_prescribed_grazing';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p528_grazing_type'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Prescribed Grazing.",
    ];
  }

  public function process_range_planting(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_range_planting';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p550_species_category'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Range Planting.",
    ];
  }

  public function process_residue_and_tillage_management_notill(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_residue_tillage_no_till';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p329_surface_disturbance'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Residue & Tillage Management, No Till.",
    ];
  }

  public function process_residue_and_tillage_management_redtill(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_residue_till_reduced_till';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p345_surface_disturbance'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Residue & Tillage Management, Reduced Till.",
    ];
  }

  public function process_riparian_forest_buffer(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_riparian_forest_buffer';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p391_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p391_species_density'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Riparian Forest Buffer.",
    ];
  }


  public function process_riparian_herbaceous_cover(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_riparian_herbaceous_cover';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p390_species_category'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Riparean Herbaceous Cover.",
    ];
  }


  public function process_roofs_and_covers(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_roofs_and_covers';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p367_roof_cover_type'] = $csv_line[6];
      $supplemental_data_submission['csc_p367_roof_cover_type_other'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Roofs and Covers.",
    ];
  }

  public function process_silvopasture(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_silvopasture';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p381_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p381_species_density'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Silvopasture.",
    ];
  }


  public function process_stripcropping(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_stripcropping';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p585_strip_width'] = $csv_line[6];
      $supplemental_data_submission['csc_p585_crop_category'] = $csv_line[7];
      $supplemental_data_submission['csc_p585_number_of_strips'] = $csv_line[8];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Stripcropping.",
    ];
  }


  public function process_tree_shrub_establishment(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_tree_shrub_establishment';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p612_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p612_species_density'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Tree Shrub Establishment.",
    ];
  }

  public function process_vegetative_barrier(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_vegetative_barrier';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p601_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p601_barrier_width'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Vegetative Barrier.",
    ];
  }

  public function process_waste_separation_facility(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_waste_separation_facility';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p632_separation_type'] = $csv_line[6];
      $supplemental_data_submission['csc_p632_use_of_solids'] = $csv_line[7];
      $supplemental_data_submission['csc_p632_use_of_solids_other'] = $csv_line[8];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Waste Separation Facility.",
    ];
  }


  public function process_waste_storage_facility(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_waste_storage_facility';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p313_pri_waste_storage_sys'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'waste_storage_system', 'name' => $csv_line[6]]));

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Waste Storage Facility.",
    ];
  }

  public function process_waste_treatment(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_waste_treatment';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p629_treatment_type'] = $csv_line[6];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Waste Treatment.",
    ];
  }

  public function process_waste_treatment_lagoon(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_waste_treatment_lagoon';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p359_pri_waste_storage_sys'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['csc_vid' => 'waste_storage_system', 'name' => $csv_line[6]]));
      $supplemental_data_submission['csc_p359_lagoon_cover_or_crust'] = filter_var($csv_line[7], FILTER_VALIDATE_BOOLEAN);
      $supplemental_data_submission['csc_p359_lagoon_aeration'] = filter_var($csv_line[8], FILTER_VALIDATE_BOOLEAN);

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Waste Treatment Lagoon.",
    ];
  }

  public function process_windshelter_est_reno(){
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;
    
    foreach($csv as $csv_line_raw) {
      $csv_line = array_map('trim', $csv_line);

      $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'f_enrollment_field_id' => $csv_line[3]]));
      $producer_id = $field_id->f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
      $project_id = $producer_id->project_id->first()->get('entity')->getTarget()->getValue();

      $supplemental_data_submission = [];
      $supplemental_data_submission['type'] = 'csc_windbreak_shelterbelt';
      $supplemental_data_submission['name'] = $csv_line[0];
      $supplemental_data_submission['csc_field_id'] = $field_id;
      $supplemental_data_submission['csc_project_id'] = $project_id;
      $supplemental_data_submission['csc_p380_species_category'] = $csv_line[6];
      $supplemental_data_submission['csc_p380_species_density'] = $csv_line[7];

      $ps_to_save = Log::create($supplemental_data_submission);

      $ps_to_save->save();
      $out = $out + 1;      
    }

    return [
      "#children" => "added " . $out . " Windbreak/Shelterbelt Establishment and Renovation.",
    ];
  }

  public function processCoversheet($coversheet, $importFunction){
    $dataArray = [];
    $column = 2;
    $row = 6;
  
    for($row; $row <= 16; $row++){
      $cellValue = $coversheet->getCellByColumnAndRow($column, $row)->getValue();
  
      //read the cell
      array_push($dataArray, $cellValue);
    }
  
    //import new coversheet
    $importFunction($dataArray);

    return $dataArray;

  }

  public function processImport($in_sheet, $importFunction, $end_column, $log_name="", $fields=""){
    $record_count = 0;
                
    $start_column = 2;

    $row = 7;

    //the import template for field summary entity has its data starts on row 6
    //while all other sheets start on row 7. the follow 3 line of code is created
    //to adjust for this discrepancy. 
    if($importFunction == 'import_field_summary'){
      $row = 6;
    }

    for($row; ; $row++){
      $startCell = Coordinate::stringFromColumnIndex($start_column) . $row;
      $endCell = Coordinate::stringFromColumnIndex($end_column) . $row;

      //read the entire row
      $dataArray = $in_sheet
        ->rangeToArray($startCell . ':' . $endCell);

      //if the row is empty then we reach the end of rows and stop importing
      if(empty(array_filter($dataArray[0]))){
        break;
      }
      
      //increment record count
      $record_count = $record_count + 1;

      //import new project summary record
      $importFunction($dataArray[0], $record_count, $fields);
      
    }

    return $record_count;
    
  }
}