$('#drosera_user_user_restore_action').bind('change', function() {
  if ($('#drosera_user_user_restore_action').val() == 2) {
      $('#user_groups_row').show();
  } else {
      $('#user_groups_row').hide();
  }
});