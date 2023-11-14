<?php

namespace Drupal\cig_pods;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\entity\UncacheableEntityAccessControlHandler;
use Drupal\views\Plugin\views\ViewsHandlerInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * PODS project access control handler.
 */
class ProjectAccessControlHandler extends UncacheableEntityAccessControlHandler {

  /**
   * Define admin Roles.
   */
  const ADMIN_ROLES = [
    'NRCS_PODS_SH-AdminUser',
  ];

  /**
   * Define awardee Roles.
   */
  const AWARDEE_ROLES = [
    'NRCS_PODS_SH-User-Awardee',
  ];

  /**
   * Helper method for getting the current session Roles.
   *
   * @return string
   *   The Roles.
   */
  protected static function getRole() {
          $session = \Drupal::request()->getSession();
            \Drupal::logger('auth_rewrite')->notice("getRole: " . print_r($session->get('ApplicationRoleEnumeration'), True));
    return $session->get('ApplicationRoleEnumeration');
  }

  /**
   * Helper method for getting the current session eAuth ID.
   *
   * @return string
   *   The Roles.
   */
  protected static function getEauthId() {
    $session = \Drupal::request()->getSession();
    return $session->get('eAuthId');
  }

  /**
   * Checks to see if the user is an admin.
   *
   * @return bool
   *   Returns TRUE if the user has an admin Roles. FALSE otherwise.
   */
  public static function isAdmin() {
    foreach(self::getRole() as $role) {
        if(in_array($role, self::ADMIN_ROLES)) {
                return True;
        }
    }

    return False;
  }

  /**
   * Checks to see if the user is an awardee.
   *
   * @return bool
   *   Returns TRUE if the user has an awardee Roles. FALSE otherwise.
   */
  public static function isAwardee() {
      foreach(self::getRole() as $role) {
        if(in_array($role, self::AWARDEE_ROLES)) {
           return True;
        }
      }

    return False;
  }

    /**
   * Checks to see if the user is a Project Manager.
   *
   * @return bool
   *   Returns TRUE if the user has an awardee Role and is marked as a project administrator on an award. 
   *   FALSE otherwise.
   */
  public static function isProjectManager() {
    if(self::isAwardee()){
      if(!empty(self::awardeeManagerAwardAccess(self::getEauthId()))){
        return TRUE;
      }

    }

    return False;
  }

   /**
   * Returns the count of the Project or Award entities that exist for a Project Manager.
   *
   * @return int
   */
  public static function projectManagerEntityCounts(bool $isProject) {
    if($isProject){
      return count(self::awardeeManagerProjectAccess(self::getEauthId()));
    }else{
      return count(self::awardeeManagerAwardAccess(self::getEauthId()));
    }
  }

  /**
   * Returns the an array of award ID's that a project Manager has access to.
   *
   * @return array
   */
  public static function getProjectManagerAwardEntities() {
    return self::awardeeManagerAwardAccess(self::getEauthId());
  }


  /**
   * {@inheritdoc}
   *
   * Link the activities to the permissions. checkAccess is called with the
   * $operation as defined in the routing.yml file.
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    // First delegate to the parent method.
    $result = parent::checkAccess($entity, $operation, $account);

    // Only proceed if access was not determined. We will use eAuth ID + Roles
    // to check if the user should have access.
    if (!$result->isNeutral()) {
      return $result;
    }

    // Get the user's eAuthID and Roles.
    $eauth_id = $this->getEauthId();

    // Admins can create any asset.
    if (self::isAdmin()) {
      $result = AccessResult::allowed();
    }

    // Project Managers have access to awards and projects they are marked an Awardee Administrative for
    // as well as all the normal Awardee Entities.
    elseif (self::isProjectManager()) {
      if($entity->bundle() === 'project' && in_array($entity->id(), $this->awardeeManagerProjectAccess($eauth_id))){
        $result = AccessResult::allowed();
      }
      elseif($entity->bundle() === 'award' &&  in_array($entity->id(), $this->awardeeManagerAwardAccess($eauth_id))){
        $result = AccessResult::allowed();
      }
      elseif (in_array($entity->id(), $this->eAuthIdAssets($eauth_id, $entity->bundle())) && $entity->bundle() <> 'project') {
        $result = AccessResult::allowed();
      }
    }

    // Awardees only have access to assets in a project that their eAuth ID
    // is associated with.
    elseif (self::isAwardee()) {
      if (in_array($entity->id(), $this->eAuthIdAssets($eauth_id, $entity->bundle()))) {
        $result = AccessResult::allowed();
      }
    }

    // Ensure that access is evaluated again when the entity changes.
    return $result->addCacheableDependency($entity);
  }

  /**
   * {@inheritdoc}
   *
   * Separate from the checkAccess because the entity does not yet exist, it
   * will be created during the 'add' process.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {

    // First delegate to the parent method.
    $result = parent::checkCreateAccess($account, $context, $entity_bundle);

    // Only proceed if access was not determined. We will use Roles to check if
    // the user should have access.
    if (!$result->isNeutral()) {
      return $result;
    }

    // Admins can create any asset.
    if (self::isAdmin()) {
      $result = AccessResult::allowed();
    }

    // Project Managers can only create certain asset types.
    elseif (self::isProjectManager()) {
      $allowed_types = [
        'producer',
        'soil_health_management_unit',
        'soil_health_sample',
        'field_assessment',
        'range_assessment',
        'pasture_assessment',
        'pasture_health_assessment',
        'lab_result',
        'lab_testing_method',
        'operation',
        'irrigation',
        'input',
        'project',
      ];
      if (in_array($entity_bundle, $allowed_types)) {
        $result = AccessResult::allowed();
      }
    }

    // Awardees can only create certain asset types.
    elseif (self::isAwardee()) {
      $allowed_types = [
        'producer',
        'soil_health_management_unit',
        'soil_health_sample',
        'field_assessment',
        'range_assessment',
        'pasture_assessment',
        'pasture_health_assessment',
        'lab_result',
        'lab_testing_method',
        'operation',
        'irrigation',
        'input',
      ];
      if (in_array($entity_bundle, $allowed_types)) {
        $result = AccessResult::allowed();
      }
    }

    // Return the result.
    return $result;
  }
   /**
   * Query to find the Awards that an Project Admin has access too.
   *
   * @param string $eauth_id
   *   The eAuth ID.
   * @param string|null $asset_type
   *   The asset type to filter by.
   *
   * @return array
   *   Returns an array of asset IDs.
   */
  public static function awardeeManagerAwardAccess($eauth_id) {

    $term_arr = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->
    loadByProperties(['name' => 'Awardee Administrative']);
    $administrative_taxonomy_id = array_keys($term_arr)[0];

    // Query the asset__award table, where each row is a relationship between
    // an asset and a award.
    $query = \Drupal::database()->select('asset', 'at');

    // Select the asset entity IDs.
    $query->addField('at', 'id');

    // Filter by asset type.
    $query->condition('at.type', 'award');

    // Join the asset__award table again, this time to get contacts that
    // reference the same award.
    $query->join('asset__award', 'aac', "at.id = aac.award_target_id AND aac.bundle = 'contact' AND aac.deleted != 1");

    // Select the asset entity IDs.
    $query->addField('aac', 'entity_id');

    // Join the asset__eauth_id table, which assigns eAuth IDs to contact
    // assets.
    $query->join('asset__eauth_id', 'aei', "aac.entity_id = aei.entity_id AND aei.deleted != 1");

    // Join the asset__field_contact_type table, which keeps track of what type of contact they are.
    // the "Awardee Administrative" Contact type being the one we need.
    $query->join('asset__field_contact_type', 'fct', 'aac.entity_id = fct.entity_id AND fct.deleted !=1');

    // Filter by the eAuth ID field on the contact asset.
    $query->condition('aei.eauth_id_value', $eauth_id);

    //Filter by the contact type target ID on each contact asset.
    $query->condition('fct.field_contact_type_target_id', $administrative_taxonomy_id);

    // Execute the query.
    $result = $query->execute();

    // Return an array of unique asset IDs.
    $asset_ids = [];
    foreach ($result as $row) {
      if (!empty($row->id)) {
        $asset_ids[] = $row->id;
      }
    }
    return $asset_ids;
  }


  /**
   * Query to find the projectthat an Project Manager has access too.
   *
   * @param string $eauth_id
   *   The eAuth ID.
   * @param string|null $asset_type
   *   The asset type to filter by.
   *
   * @return array
   *   Returns an array of asset IDs.
   */
  public static function awardeeManagerProjectAccess($eauth_id) {

    $term_arr = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->
    loadByProperties(['name' => 'Awardee Administrative']);
    $administrative_taxonomy_id = array_keys($term_arr)[0];

    // Query the asset__award table, where each row is a relationship between
    // an asset and a award.
    $query = \Drupal::database()->select('asset__award', 'aa');

    // Select the asset entity IDs.
    $query->addField('aa', 'entity_id', 'id');

    // Exclude deleted fields.
    $query->condition('aa.deleted', 1, '!=');

    // Filter by asset type.
    $query->condition('aa.bundle', 'project');

    // Join the asset__award table again, this time to get contacts that
    // reference the same award.
    $query->join('asset__award', 'aac', "aa.award_target_id = aac.award_target_id AND aac.bundle = 'contact' AND aac.deleted != 1");

    // Join the asset__eauth_id table, which assigns eAuth IDs to contact
    // assets.
    $query->join('asset__eauth_id', 'aei', "aac.entity_id = aei.entity_id AND aei.deleted != 1");

    // Join the asset__field_contact_type table, which keeps track of what type of contact they are.
    // the "Awardee Administrative" Contact type being the one we need.
    $query->join('asset__field_contact_type', 'fct', 'aac.entity_id = fct.entity_id AND fct.deleted !=1');

    // Filter by the eAuth ID field on the contact asset.
    $query->condition('aei.eauth_id_value', $eauth_id);

    //Filter by the contact type target ID on each contact asset.
    $query->condition('fct.field_contact_type_target_id', $administrative_taxonomy_id);

    // Execute the query.
    $result = $query->execute();

    // Return an array of unique asset IDs.
    $asset_ids = [];
    foreach ($result as $row) {
      if (!empty($row->id)) {
        $asset_ids[] = $row->id;
      }
    }
    return $asset_ids;
  }

  /**
   * Query to find assets that an eAuth ID has access to.
   *
   * @param string $eauth_id
   *   The eAuth ID.
   * @param string|null $asset_type
   *   The asset type to filter by.
   *
   * @return array
   *   Returns an array of asset IDs.
   */
  public static function eAuthIdAssets($eauth_id, string $asset_type = NULL) {

    // Query the asset__award table, where each row is a relationship between
    // an asset and a award.
    $query = \Drupal::database()->select('asset__award', 'aa');

    // Select the asset entity IDs.
    $query->addField('aa', 'entity_id', 'id');

    // Exclude deleted fields.
    $query->condition('aa.deleted', 1, '!=');

    // Optionally filter by asset type.
    if (!empty($asset_type)) {
      $query->condition('aa.bundle', $asset_type);
    }

    // Join the asset__award table again, this time to get contacts that
    // reference the same award.
    $query->join('asset__award', 'aac', "aa.award_target_id = aac.award_target_id AND aac.bundle = 'contact' AND aac.deleted != 1");

    // Join the asset__eauth_id table, which assigns eAuth IDs to contact
    // assets.
    $query->join('asset__eauth_id', 'aei', "aac.entity_id = aei.entity_id AND aei.deleted != 1");

    // Filter by the eAuth ID field on the contact asset.
    $query->condition('aei.eauth_id_value', $eauth_id);

    // Execute the query.
    $result = $query->execute();

    // Return an array of unique asset IDs.
    $asset_ids = [];
    foreach ($result as $row) {
      if (!empty($row->id)) {
        $asset_ids[] = $row->id;
      }
    }
    return $asset_ids;
  }

  /**
   * Helper method for Views argument query modification.
   *
   * This is used by the pods_project_access view argument plugin.
   *
   * @param \Drupal\views\Plugin\views\ViewsHandlerInterface $handler
   *   The Views handler.
   */
  public static function viewsArgumentQueryAlter(ViewsHandlerInterface $handler, bool $isManager, bool $isProject) {

    // Get the user's eAuthID and Roles.
    $eauth_id = ProjectAccessControlHandler::getEauthId();

    // If this is an admin, don't apply any additional filters.
    if (self::isAdmin()) {
      return;
    }

    // If this is an awardee, filter out assets that they do not have access to.
    elseif (self::isAwardee()) {

      // Try to determine the asset type from arguments.
      // The pods_asset_er View uses asset type as the first argument, so we
      // look for a non-numeric argument there. If one is not found, we default
      // to NULL so that no additional asset type filtering is performed. This
      // is fine in most cases because downstream code will filter it (eg: the
      // pods_asset_lists View).
      // The only time this could cause brittleness is when you are trying to
      // get project assets, which require a slightly different query JOIN.
      // @see ProjectAccessControlHandler::eAuthIdAssets()
      $asset_type = NULL;
      if (!empty($handler->view->args[0]) && !is_numeric($handler->view->args[0])) {
        $asset_type = $handler->view->args[0];
      }

      // First query for a list of asset IDs that the awardee has access to
      // (based on their eAuth ID), then use this list to filter the current
      // View.
      // We do this in two separate queries to keep this argument handler's
      // query modifications very simple. It only needs the condition:
      // "WHERE asset.id IN (:asset_ids)", rather than add a bunch of extra
      // JOINs (and duplicate the logic in the helper method).
      $asset_ids = [];
      if($isManager && $isProject){
        $asset_ids = ProjectAccessControlHandler::awardeeManagerProjectAccess($eauth_id, $asset_type);
      }elseif($isManager && !$isProject){
        $asset_ids = ProjectAccessControlHandler::awardeeManagerAwardAccess($eauth_id, $asset_type);
      }
      else{
        $asset_ids = ProjectAccessControlHandler::eAuthIdAssets($eauth_id, $asset_type);
      }

      // If there are no asset IDs, add 0 to ensure the array is not empty.
      if (empty($asset_ids)) {
        $asset_ids[] = 0;
      }

      // Filter to only include assets with those IDs.
      $handler->ensureMyTable();
      $handler->query->addWhere(0, "$handler->tableAlias.id", $asset_ids, 'IN');
    }
  }

}