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

        Full sheet import:
        <form action="/csv_import/upload_all_assets" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>

    ',
    ];
  }
  
  public function term_ref( $vid, $tax_name){

    return array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => $vid, 'name' => $tax_name]));
  }

  public function pasture_severity ( $value){

    $severity = [
      5 => 'Extreme to Total',
      4 => 'Moderate to Extreme',
      3 => 'Moderate',
      2 => 'Slight to Moderate',
      1 => 'None to Slight',
    ];

    $key = array_search($value, $severity);

    return $key;

  }

  public function assessment_evaluations ( $value){

    $evaluation = [
      0 => 'Yes',
      1 => 'No',
      2 => 'N/A',
    ];

    $key = array_search($value, $evaluation);

    return $key;

  }

  public function process_all_assets() {
    $awardee_org_count = 0;
    $project_count = 0;
    $lab_test_count = 0;
    $lab_results_count = 0;
    $producers_count = 0;
    $shmus_count = 0;
    $cifshs_count = 0;
    $diphs_count = 0;
    $irrigation_count = 0;
    $soil_sample_count = 0;
    $iirh_count = 0;
    $pcs_count = 0;


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
    $sheetnames = ['Awardee Organizations', 'Projects', 'Lab Testing Methods', 'Soil Test Results', 'Agricultural Producer', 'Irrigation Samples', 'CIFSH', 'SHMU', 'DIPH', 'Soil Samples', 'IIRH', 'PCS', 'Operations'];
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

        case "Lab Testing Methods":
          $offset = 0;
          $done = false;

          while(!$done) {
            $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
            
            if($name == "") {
              $done = true;
              break;
            }

            $lt_name = $name;
            $lt_project = $sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue();
            $lt_soil_health_test_lab = $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue();
            $lt_aggregate_stability_md = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();
            $lt_aggregate_stability_unit = $sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue();
            $lt_respiration_incubaion_days = $sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue();
            $lt_respiration_detection_md = $sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue();
            $lt_bulk_density_core_diameter = $sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue();
            $lt_bulk_density_volume = $sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue();
            $lt_infilteration_md = $sheet->getCellByColumnAndRow(12, 5 + $offset)->getValue();
            $lt_electroconductivity_md = $sheet->getCellByColumnAndRow(13, 5 + $offset)->getValue();
            $lt_nitrate_n_md = $sheet->getCellByColumnAndRow(14, 5 + $offset)->getValue();
            $lt_soil_ph_md = $sheet->getCellByColumnAndRow(15, 5 + $offset)->getValue();
            $lt_phosphorus_md = $sheet->getCellByColumnAndRow(16, 5 + $offset)->getValue();
            $lt_potassium_md = $sheet->getCellByColumnAndRow(17, 5 + $offset)->getValue();
            $lt_calcium_md = $sheet->getCellByColumnAndRow(18, 5 + $offset)->getValue();
            $lt_magnesium_md = $sheet->getCellByColumnAndRow(19, 5 + $offset)->getValue();
            $lt_sulfur_md = $sheet->getCellByColumnAndRow(20, 5 + $offset)->getValue();
            $lt_iron_md = $sheet->getCellByColumnAndRow(21, 5 + $offset)->getValue();
            $lt_manganese_md = $sheet->getCellByColumnAndRow(22, 5 + $offset)->getValue();
            $lt_copper_md = $sheet->getCellByColumnAndRow(23, 5 + $offset)->getValue();
            $lt_zinc_md = $sheet->getCellByColumnAndRow(24, 5 + $offset)->getValue();
            $lt_boron_md = $sheet->getCellByColumnAndRow(25, 5 + $offset)->getValue();
            $lt_aluminum_md = $sheet->getCellByColumnAndRow(26, 5 + $offset)->getValue();
            $lt_molybdenum = $sheet->getCellByColumnAndRow(27, 5 + $offset)->getValue();

            $methods_submission = [];
            $methods_submission['type'] = 'lab_testing_method';
            $methods_submission['field_lab_method_name'] = $lt_name;
            $methods_submission['name'] = $lt_name;
            $methods_submission['field_lab_method_project'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'project', 'name' => $lt_project]));
            $methods_submission['field_lab_soil_test_laboratory'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_laboratory', 'name' => $lt_soil_health_test_lab]));
            $methods_submission['field_lab_method_aggregate_stability_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_aggregate_stability_me', 'name' => $lt_aggregate_stability_md]));
            $methods_submission['field_lab_method_aggregate_stability_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_aggregate_stability_un', 'name' => $lt_aggregate_stability_unit]));
            $methods_submission['field_lab_method_respiration_incubation_days'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_respiration_incubation', 'name' => $lt_respiration_incubaion_days]));
            $methods_submission['field_lab_method_respiration_detection_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_respiration_detection_', 'name' => $lt_respiration_detection_md]));
            $methods_submission['field_lab_method_bulk_density_core_diameter'] = $lt_bulk_density_core_diameter;
            $methods_submission['field_lab_method_bulk_density_volume'] = $lt_bulk_density_volume;
            $methods_submission['field_lab_method_infiltration_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_infiltration_method', 'name' => $lt_infilteration_md]));
            $methods_submission['field_lab_method_electroconductivity_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_ec_method', 'name' => $lt_electroconductivity_md]));
            $methods_submission['field_lab_method_nitrate_n_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_nitrate_n_method', 'name' => $lt_nitrate_n_md]));
            $methods_submission['field_lab_method_soil_ph_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_ph_method', 'name' => $lt_soil_ph_md]));
            $methods_submission['field_lab_method_phosphorus_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_phosphorus_md]));
            $methods_submission['field_lab_method_potassium_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_potassium_md]));
            $methods_submission['field_lab_method_calcium_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_calcium_md]));
            $methods_submission['field_lab_method_magnesium_method'] =array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_magnesium_md]));
            $methods_submission['field_lab_method_sulfur_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_sulfur_md]));
            $methods_submission['field_lab_method_iron_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_iron_md]));
            $methods_submission['field_lab_method_manganese_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_manganese_md]));
            $methods_submission['field_lab_method_copper_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_copper_md]));
            $methods_submission['field_lab_method_zinc_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_zinc_md]));
            $methods_submission['field_lab_method_boron_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_boron_md]));
            $methods_submission['field_lab_method_aluminum_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_aluminum_md]));
            $methods_submission['field_lab_method_molybdenum_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'd_soil_health_extraction', 'name' => $lt_molybdenum]));
        
            
            $lab_test_method_create = Asset::create($methods_submission);
                    
            $lab_test_method_create->save();
            
            $lab_test_count++;

            $offset++;
            
          }

          break;

          case "Soil Test Results":

          $offset = 0;
          $done = false;

          while(!$done) {
            $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
            
            if($name == "") {
              $done = true;
              break;
            }

            
            $result_soil_sample_default_id = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
            $result_lab_method_default = $sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue();


            $result_submission = [];
            $result_submission['type'] = 'lab_result';
            $result_submission['field_lab_result_soil_sample'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_sample', 'name' => $result_soil_sample_default_id]));

            $result_submission['field_lab_result_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'lab_testing_method', 'name' => $result_lab_method_default]));
            $result_submission['name'] = $lt_name;
            $result_submission['field_lab_result_raw_soil_organic_carbon'] = $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue();
            $result_submission['field_lab_result_raw_aggregate_stability'] = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();
            $result_submission['field_lab_result_raw_respiration'] = $sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue();
            $result_submission['field_lab_result_active_carbon'] = $sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue();
            $result_submission['field_lab_result_available_organic_nitrogen'] = $sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_bulk_density_dry_weight'] = $sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_infiltration_rate'] = $sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_ph_value'] = $sheet->getCellByColumnAndRow(12, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_electroconductivity'] = $sheet->getCellByColumnAndRow(13, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_ec_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(14, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_cation_exchange_capacity'] = $sheet->getCellByColumnAndRow(15, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_nitrate_n'] = $sheet->getCellByColumnAndRow(16, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_nitrate_n_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(17, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_nitrogen_by_dry_combustion'] = $sheet->getCellByColumnAndRow(18, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_phosphorous'] = $sheet->getCellByColumnAndRow(19, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_phosphorous_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(20, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_potassium'] = $sheet->getCellByColumnAndRow(21, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_potassium_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(22, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_calcium'] = $sheet->getCellByColumnAndRow(23, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_calcium_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(24, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_magnesium'] = $sheet->getCellByColumnAndRow(25, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_magnesium_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(26, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_sulfur'] = $sheet->getCellByColumnAndRow(27, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_sulfur_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(28, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_iron'] = $sheet->getCellByColumnAndRow(29, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_iron_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(30, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_manganese'] = $sheet->getCellByColumnAndRow(31, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_manganese_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(32, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_copper'] = $sheet->getCellByColumnAndRow(33, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_copper_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(34, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_zinc'] = $sheet->getCellByColumnAndRow(35, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_zinc_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(36, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_boron'] = $sheet->getCellByColumnAndRow(37, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_boron_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(38, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_aluminum'] = $sheet->getCellByColumnAndRow(39, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_aluminum_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(40, 5 + $offset)->getValue() );
            $result_submission['field_lab_result_sf_molybdenum'] = $sheet->getCellByColumnAndRow(41, 5 + $offset)->getValue();
            $result_submission['field_lab_result_sf_molybdenum_lab_interpretation'] = $this->term_ref('d_lab_interpretation',  $sheet->getCellByColumnAndRow(42, 5 + $offset)->getValue() );



            $lab_results_create = Asset::create($result_submission);
                    
            $lab_results_create->save();
            
            $lab_results_count++;

            $offset++;

          }

          break;

          
          case "Agricultural Producer":






            $offset = 0;
            $done = false;
  
            while(!$done) {
              $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
              
              if($name == "") {
                $done = true;
                break;
              }
  
              
              $producer_project_id = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
              
              $producer_submission = [];
              $producer_submission['type'] = 'producer';
              $producer_submission['project'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'project', 'name' => $producer_project_id]));
  
              $producer_submission['name'] = $sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue();
              $producer_submission['field_producer_first_name'] = $sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue();
              $producer_submission['field_producer_last_name'] = $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue();
              $producer_submission['field_producer_headquarter'] = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();
              

              $producers_create = Asset::create($producer_submission);
                    
              $producers_create->save();
              
              $producers_count++;
  
              $offset++;


            }

            break; 


            case "SHMU":


              $offset = 0;
              $done = false;
    
              while(!$done) {
                $name = $sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue();
                
                if($name == "") {
                  $done = true;
                  break;
                }
    
                
                $shmu_producer = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();

              
    
    
                $shmu_submission = [];
                $shmu_submission['type'] = 'soil_health_management_unit';
                $shmu_submission['field_shmu_involved_producer'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'producer', 'name' => $shmu_producer]));
    
                $shmu_submission['name'] = $name;

                $shmu_submission['field_shmu_type'] = $this->term_ref('d_shmu_type',  $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue() );


                $shmu_submission['field_shmu_replicate_number'] = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();
                $shmu_submission['field_shmu_treatment_narrative'] = $sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue();

                $shmu_submission['field_shmu_experimental_design'] = $this->term_ref('d_experimental_design',  $sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue() );

                $shmu_submission['field_shmu_experimental_duration_month'] = $sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue();
                $shmu_submission['field_shmu_experimental_duration_year'] = $sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue();
                $shmu_submission['field_shmu_experimental_frequency_day'] = $sheet->getCellByColumnAndRow(13, 5 + $offset)->getValue();
                $shmu_submission['field_shmu_experimental_frequency_month'] = $sheet->getCellByColumnAndRow(12, 5 + $offset)->getValue();
                $shmu_submission['field_shmu_experimental_frequency_year'] = $sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue();

                $shmu_submission['field_geofield'] = $sheet->getCellByColumnAndRow(14, 5 + $offset)->getValue();

                $shmu_submission['field_shmu_prev_land_use'] = $this->term_ref('d_land_use',  $sheet->getCellByColumnAndRow(15, 5 + $offset)->getValue() );

                $shmu_submission['field_shmu_prev_land_use_modifiers'] = $this->term_ref('d_land_use_modifiers',  $sheet->getCellByColumnAndRow(16, 5 + $offset)->getValue() );

                    $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCellByColumnAndRow(17, 5 + $offset)->getValue());

                $shmu_submission['field_shmu_date_land_use_changed'] = $val;

                $shmu_submission['field_shmu_current_land_use'] = $this->term_ref('d_land_use',  $sheet->getCellByColumnAndRow(18, 5 + $offset)->getValue() );

                $shmu_submission['field_shmu_current_land_use_modifiers'] = $this->term_ref('d_land_use_modifiers',  $sheet->getCellByColumnAndRow(19, 5 + $offset)->getValue() );

                $shmu_submission['field_shmu_initial_crops_planted'] = $this->term_ref('d_cover_crop',  $sheet->getCellByColumnAndRow(20, 5 + $offset)->getValue() );

                $shmu_submission['field_current_tillage_system'] = $this->term_ref('d_tillage_system',  $sheet->getCellByColumnAndRow(21, 5 + $offset)->getValue() );

                $shmu_submission['field_years_in_current_tillage_system'] = $sheet->getCellByColumnAndRow(22, 5 + $offset)->getValue();
                $shmu_submission['field_shmu_previous_tillage_system'] = $this->term_ref('d_tillage_system',  $sheet->getCellByColumnAndRow(23, 5 + $offset)->getValue() );
                
                $shmu_submission['field_years_in_prev_tillage_system'] = $sheet->getCellByColumnAndRow(24, 5 + $offset)->getValue();
                $shmu_submission['field_shmu_major_resource_concern'] = $this->term_ref('d_major_resource_concern',  $sheet->getCellByColumnAndRow(25, 5 + $offset)->getValue() );

                $shmu_submission['field_shmu_resource_concern'] = $this->term_ref('d_resource_concern',  $sheet->getCellByColumnAndRow(26, 5 + $offset)->getValue() );

                $shmu_submission['field_shmu_practices_addressed'] = $this->term_ref('d_practice',  $sheet->getCellByColumnAndRow(27, 5 + $offset)->getValue() );
               

                $shmus_create = Asset::create($shmu_submission);
                      
                $shmus_create->save();
                
                $shmus_count++;
    
                $offset++;
  
  
              }
  
              break;

              
            case "CIFSH":

  
              $offset = 0;
              $done = false;
    
              while(!$done) {
                $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                
                if($name == "") {
                  $done = true;
                  break;
                }
    
                
                $cifsh_shmu = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();

              
    
    
                $cifsh_submission = [];
                $cifsh_submission['type'] = 'field_assessment';
                $cifsh_submission['shmu'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_management_unit', 'name' => $cifsh_shmu]));
    
                $cifsh_submission['name'] = $name;

                

                $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue());

                $cifsh_submission['field_assessment_date'] = $val;


                $cifsh_submission['field_assessment_soil_cover'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_residue_breakdown'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_surface_crusts'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_ponding'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_penetration_resistance'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_water_stable_aggregates'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_soil_structure'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_soil_color'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(12, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_plant_roots'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(13, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_biological_diversity'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(14, 5 + $offset)->getValue());
                $cifsh_submission['field_assessment_biopores'] = $this->assessment_evaluations($sheet->getCellByColumnAndRow(15, 5 + $offset)->getValue());

                $cifsh_submission['field_assessment_rc_soil_organic_matter'] = $sheet->getCellByColumnAndRow(16, 5 + $offset)->getValue();
                $cifsh_submission['field_assessment_rc_aggregate_instability'] = $sheet->getCellByColumnAndRow(17, 5 + $offset)->getValue();
                $cifsh_submission['field_assessment_rc_compaction'] = $sheet->getCellByColumnAndRow(18, 5 + $offset)->getValue();
                $cifsh_submission['field_assessment_rc_soil_organism_habitat'] = $sheet->getCellByColumnAndRow(19, 5 + $offset)->getValue();
       
                $cifshs_create = Asset::create($cifsh_submission);
                      
                $cifshs_create->save();
                
                $cifshs_count++;
    
                $offset++;
  
  
              }
  
              break;



              case "DIPH":

                
                $offset = 0;
                $done = false;
      
                while(!$done) {

                  $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                  
                  if($name == "") {
                    $done = true;
                    break;
                  }
      
                  
                  $diph_shmu = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();

                  $diph_submission = [];
                  $diph_submission['type'] = 'pasture_health_assessment';
                  $diph_submission['shmu'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_management_unit', 'name' => $diph_shmu]));
      
                  $diph_submission['name'] = $name;
  
                  
  
                  $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue());
  
                  $diph_submission['pasture_health_assessment_date'] = $val;
  
  
                  $diph_submission['pasture_health_assessment_land_use'] = $this->term_ref('d_land_use',  $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue() );

                  $diph_submission['pasture_health_assessment_erosion_sheet'] = $this->pasture_severity($sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue());

                  
                 
                  $diph_submission['pasture_health_assessment_erosion_gullies'] = $this->pasture_severity($sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_erosion_wind_scoured'] = $this->pasture_severity($sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_erosion_streambank'] = $this->pasture_severity($sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_water_flow_patterns'] = $this->pasture_severity($sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_bare_ground'] = $this->pasture_severity($sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_padestals'] = $this->pasture_severity($sheet->getCellByColumnAndRow(12, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_litter_movement'] = $this->pasture_severity($sheet->getCellByColumnAndRow(13, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_composition'] = $this->pasture_severity($sheet->getCellByColumnAndRow(14, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_soil_surface'] = $this->pasture_severity($sheet->getCellByColumnAndRow(15, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_compaction_layer'] = $this->pasture_severity($sheet->getCellByColumnAndRow(16, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_live_plant'] = $this->pasture_severity($sheet->getCellByColumnAndRow(17, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_forage_plant'] = $this->pasture_severity($sheet->getCellByColumnAndRow(18, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_percent_desirable'] = $this->pasture_severity($sheet->getCellByColumnAndRow(19, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_invasive_plants'] = $this->pasture_severity($sheet->getCellByColumnAndRow(20, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_annual_production'] = $this->pasture_severity($sheet->getCellByColumnAndRow(21, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_plant_vigor'] = $this->pasture_severity($sheet->getCellByColumnAndRow(22, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_dying_plants'] = $this->pasture_severity($sheet->getCellByColumnAndRow(23, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_little_cover'] = $this->pasture_severity( $sheet->getCellByColumnAndRow(24, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_nontoxic_legumes'] = $this->pasture_severity($sheet->getCellByColumnAndRow(25, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_uniformity'] = $this->pasture_severity($sheet->getCellByColumnAndRow(26, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_livestock'] = $this->pasture_severity($sheet->getCellByColumnAndRow(27, 5 + $offset)->getValue());
                  
                  $diph_submission['pasture_health_assessment_soil_site_stab'] = $this->pasture_severity($sheet->getCellByColumnAndRow(28, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_soil_site_stab_just'] = $sheet->getCellByColumnAndRow(29, 5 + $offset)->getValue();

                  $diph_submission['pasture_health_assessment_hydro_func'] = $this->pasture_severity($sheet->getCellByColumnAndRow(30, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_hydro_func_just'] = $sheet->getCellByColumnAndRow(31, 5 + $offset)->getValue();

                  $diph_submission['pasture_health_assessment_bio_integ'] = $this->pasture_severity($sheet->getCellByColumnAndRow(32, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_bio_integ_just'] = $sheet->getCellByColumnAndRow(33, 5 + $offset)->getValue();

                  $diph_submission['pasture_health_assessment_bio_integ_qual'] = $this->pasture_severity($sheet->getCellByColumnAndRow(34, 5 + $offset)->getValue());
                  $diph_submission['pasture_health_assessment_bio_integ_qual_just'] = $sheet->getCellByColumnAndRow(35, 5 + $offset)->getValue();

                  
                  $diphs_create = Asset::create($diph_submission);
                        
                  $diphs_create->save();
                  
                  $diphs_count++;
      
                  $offset++;
    
    
                }
    
                break;


                case "Irrigation Samples":


                  $offset = 0;
                  $done = false;
        
                  while(!$done) {
                    $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                    
                    if($name == "") {
                      $done = true;
                      break;
                    }
        
                    
                    $irrigation_shmu = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                    
        
                    $irrigation_submission = [];
                    $irrigation_submission['type'] = 'irrigation';
                    $irrigation_submission['shmu'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_management_unit', 'name' => $irrigation_shmu]));
        
                    $irrigation_submission['name'] = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();

                      $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue());

                    $irrigation_submission['field_shmu_irrigation_sample_date'] = $val;
                    
                    $irrigation_submission['field_shmu_irrigation_water_ph'] = $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue();
                    $irrigation_submission['field_shmu_irrigation_sodium_absorption_ratio'] = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();
                    $irrigation_submission['field_shmu_irrigation_total_dissolved_solids'] = $sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue();
                    $irrigation_submission['field_shmu_irrigation_total_alkalinity'] = $sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue();
                    $irrigation_submission['field_shmu_irrigation_chlorides'] = $sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue();
                    $irrigation_submission['field_shmu_irrigation_sulfates'] = $sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue();
                    $irrigation_submission['field_shmu_irrigation_nitrates'] = $sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue();
                    
      
                    $irrigation_create = Asset::create($irrigation_submission);
                          
                    $irrigation_create->save();
                    
                    $irrigation_count++;
        
                    $offset++;
      
      
                  }
      
                  break; 

                  
                case "Soil Samples":


                  $offset = 0;
                  $done = false;
        
                  while(!$done) {
                    $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                    
                    if($name == "") {
                      $done = true;
                      break;
                    }


                    $latOne = $sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue();
                    $longOne = $sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue();

                    $latTwo = $sheet->getCellByColumnAndRow(12, 5 + $offset)->getValue();
                    $longTwo = $sheet->getCellByColumnAndRow(13, 5 + $offset)->getValue();

                    $latThree = $sheet->getCellByColumnAndRow(14, 5 + $offset)->getValue();
                    $longThree = $sheet->getCellByColumnAndRow(15, 5 + $offset)->getValue();

                    $latFour = $sheet->getCellByColumnAndRow(16, 5 + $offset)->getValue();
                    $longFour = $sheet->getCellByColumnAndRow(17, 5 + $offset)->getValue();

                    $latFive = $sheet->getCellByColumnAndRow(18, 5 + $offset)->getValue();
                    $longFive = $sheet->getCellByColumnAndRow(19, 5 + $offset)->getValue();
        
                    
                    $soil_sample_shmu = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                    
        
                    $soil_sample_submission = [];
                    $soil_sample_submission['type'] = 'soil_health_sample';

                    $soil_sample_submission['shmu'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_management_unit', 'name' => $soil_sample_shmu]));
        
                    $soil_sample_submission['name'] = $sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue();

                    $soil_sample_submission['field_diameter'] = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();
                    $soil_sample_submission['field_equipment_used'] = $this->term_ref('d_equipment', $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue());
                    $soil_sample_submission['field_plant_stage_at_sampling'] = $this->term_ref('d_plant_stage', $sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue());
                    $soil_sample_submission['field_sampling_depth'] = $sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue();

                        $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue());

                    $soil_sample_submission['field_soil_sample_collection_dat'] = $val;


                    $geo = 'POLYGON(('.$latOne.' '.$longOne.','.$latTwo.' '.$longTwo.','.$latThree.' '.$longThree.','.$latFour.' '.$longFour.','.$latFive.' '.$longFive.'))';

                    

                    $soil_sample_submission['field_soil_sample_geofield'] = $geo;
                    
                    $soil_sample_create = Asset::create($soil_sample_submission);
                          
                    $soil_sample_create->save();
                    
                    $soil_sample_count++;
        
                    $offset++;
      
      
                  }
      
                  break; 
              
                  case "IIRH":


                    $offset = 0;
                    $done = false;
          
                    while(!$done) {

                      $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                      
                      if($name == "") {
                        $done = true;
                        break;
                      }

                      $iirh_shmu = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                      
                      $iirh_submission = [];

                      $iirh_submission['type'] = 'range_assessment';

                      $iirh_submission['shmu'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_management_unit', 'name' => $iirh_shmu]));
          
                      $iirh_submission['name'] = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
  
                        $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue());
  
                      $iirh_submission['range_assessment_date'] = $val;
                      
                      $iirh_submission['range_assessment_rills'] = $this->pasture_severity($sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_water_flow'] = $this->pasture_severity($sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_pedestals'] = $this->pasture_severity($sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_bare_ground'] = $this->pasture_severity($sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_gullies'] = $this->pasture_severity($sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_wind_scoured'] = $this->pasture_severity($sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_litter_movement'] = $this->pasture_severity($sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_soil_surface_resistance'] = $this->pasture_severity($sheet->getCellByColumnAndRow(12, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_soil_surface_loss'] = $this->pasture_severity($sheet->getCellByColumnAndRow(13, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_effects_of_plants'] = $this->pasture_severity($sheet->getCellByColumnAndRow(14, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_compaction_layer'] = $this->pasture_severity($sheet->getCellByColumnAndRow(15, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_functional_structural'] = $this->pasture_severity($sheet->getCellByColumnAndRow(16, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_dead_plants'] = $this->pasture_severity($sheet->getCellByColumnAndRow(17, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_litter_cover'] = $this->pasture_severity($sheet->getCellByColumnAndRow(18, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_annual_production'] = $this->pasture_severity($sheet->getCellByColumnAndRow(19, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_invasive_plants'] = $this->pasture_severity($sheet->getCellByColumnAndRow(20, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_vigor_plants'] = $this->pasture_severity($sheet->getCellByColumnAndRow(21, 5 + $offset)->getValue());

                      $iirh_submission['range_assessment_rc_soil_site_stability'] = $this->pasture_severity($sheet->getCellByColumnAndRow(22, 5 + $offset)->getValue());
                      
                      $iirh_submission['range_assessment_rc_soil_site_stability_justification'] = $sheet->getCellByColumnAndRow(23, 5 + $offset)->getValue();

                      $iirh_submission['range_assessment_rc_hydrologic_function'] = $this->pasture_severity($sheet->getCellByColumnAndRow(24, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_rc_hydrologic_function_justification'] = $sheet->getCellByColumnAndRow(25, 5 + $offset)->getValue();

                      $iirh_submission['range_assessment_rc_biotic_integrity'] = $this->pasture_severity($sheet->getCellByColumnAndRow(26, 5 + $offset)->getValue());
                      $iirh_submission['range_assessment_rc_biotic_integrity_justification'] = $sheet->getCellByColumnAndRow(27, 5 + $offset)->getValue();

                    
                      $iirh_create = Asset::create($iirh_submission);
                            
                      $iirh_create->save();
                      
                      $iirh_count++;
          
                      $offset++;
        
        
                    }
        
                  break; 
              
                  case "PCS":


                    $offset = 0;
                    $done = false;
          
                    while(!$done) {

                      $name = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
                      
                      if($name == "") {
                        $done = true;
                        break;
                      }

                      $pcs_shmu = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();

                      $pcs_submission = [];

                      $pcs_submission['type'] = 'pasture_assessment';

                      $pcs_submission['shmu'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'soil_health_management_unit', 'name' => $iirh_shmu]));
          
                      $pcs_submission['name'] = $sheet->getCellByColumnAndRow(3, 5 + $offset)->getValue();
  
                        $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCellByColumnAndRow(4, 5 + $offset)->getValue());
  
                      $pcs_submission['pasture_assessment_date'] = $val;
                  
                      $pcs_submission['pasture_assessment_desirable_plants'] = $sheet->getCellByColumnAndRow(5, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_Legume_dry_weight'] = $sheet->getCellByColumnAndRow(6, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_live_plant_cover'] = $sheet->getCellByColumnAndRow(7, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_diversity_dry_weight'] = $sheet->getCellByColumnAndRow(8, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_litter_soil_cover'] = $sheet->getCellByColumnAndRow(9, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_grazing_utilization_severity'] = $sheet->getCellByColumnAndRow(10, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_livestock_concentration'] = $sheet->getCellByColumnAndRow(11, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_soil_compaction'] = $sheet->getCellByColumnAndRow(12, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_plant_rigor'] = $sheet->getCellByColumnAndRow(13, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_erosion'] = $sheet->getCellByColumnAndRow(14, 5 + $offset)->getValue();
                      $pcs_submission['pasture_assessment_condition_store'] = $sheet->getCellByColumnAndRow(15, 5 + $offset)->getValue();

                      $pcs_create = Asset::create($pcs_submission);
                            
                      $pcs_create->save();
                      
                      $pcs_count++;
          
                      $offset++;
        
        
                    }
        
                  break; 


        }
    }

    return [
      '#children' => "
      $awardee_org_count awardee orgs <br> 
      $project_count projects added<br>
      $lab_test_count Lab Testings are added<br>
      $lab_results_count Soil Test Results are added<br>
      $producers_count Producers are added<br>
      $shmus_count SHMUs are added<br>
      $cifshs_count Field Assessments are added<br>
      $diphs_count Pasture Health Assessments are added<br>
      $irrigation_count Irrigation Samples are added<br>
      $soil_sample_count Soil Samples are added<br>
      $iirh_count Range Assessments are added<br>
      $pcs_count Pasture Assessments are added<br>
      ",
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