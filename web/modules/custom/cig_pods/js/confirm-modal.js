(function ($, Drupal) {
    'use strict';
	
	
  
    Drupal.behaviors.confirmModal = {

      attach: function() {
		  $('[data-drupal-selector="edit-cancel"], [data-drupal-selector="edit-actions-cancel"]').unbind();

        $('[data-drupal-selector="edit-cancel"], [data-drupal-selector="edit-actions-cancel"]').click(function(event){
            event.preventDefault();

            var ajaxSettings = {
            url: '/modals/confirm-modal'
            };
            var myAjaxObject = Drupal.ajax(ajaxSettings);
            myAjaxObject.execute();
			
			return false;

        });


		
		if($('[data-drupal-selector="edit-delete"], [data-drupal-selector="edit-actions-delete"]').is(":visible")){
			
          $('<input id="edit-delete" type="button" class="button delete-option-button" value="Delete"></input>').insertAfter('[data-drupal-selector="edit-delete"], [data-drupal-selector="edit-actions-delete"]');
  
          $('[data-drupal-selector="edit-delete"], [data-drupal-selector="edit-actions-delete"]').hide();
  
        }
		
		$('.delete-option-button').unbind();
          $('.delete-option-button').click(function(event){
  
            event.preventDefault();
			
            
            var aid = $('#asset_id').val();
  
            
            var deleteSettings = {

  
              url: '/modals/confirm-delete-modal/'+aid,
  
            };
  
              var deleteAjaxObject = Drupal.ajax(deleteSettings);
  
              deleteAjaxObject.execute();
  
              return false;
          });
  
  
          $('[data-drupal-selector="edit-confirmdeletepopup"]').click(function(event){
  
            event.preventDefault();
  
            $('[data-drupal-selector="edit-delete"], [data-drupal-selector="edit-actions-delete"]').click();
  
  
        });

        $('.popup-close-button').click(function(event){
            event.preventDefault();
            $('.ui-icon-closethick').click();
        });

      }
    };
  
  })(jQuery, Drupal);