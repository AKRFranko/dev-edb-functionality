<?php

add_action( 'show_user_profile', 'edb_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'edb_show_extra_profile_fields' );

function edb_show_extra_profile_fields( $user ) { 
?>
  <h3>Designer Informations</h3>
  <table class="form-table">
  <tr>
      <th>Designer level</th>
    <td>
      <label>none <input type="radio" name="edb_user_designer_level" id="edb_user_designer_level_1" value="none" checked="<?php echo esc_attr( get_the_author_meta( 'edb_user_designer_level', $user->ID ) ); ?>"></label>
      <label>vip <input type="radio" name="edb_user_designer_level" id="edb_user_designer_level_1" value="vip" checked="<?php echo esc_attr( get_the_author_meta( 'edb_user_designer_level', $user->ID ) ); ?>"></label>
      <label>vvip <input type="radio" name="edb_user_designer_level" id="edb_user_designer_level_2" value="vip" checked="<?php echo esc_attr( get_the_author_meta( 'edb_user_designer_level', $user->ID ) ); ?>"></label>
      <label>vvvip <input type="radio" name="edb_user_designer_level" id="edb_user_designer_level_3" value="vip" checked="<?php echo esc_attr( get_the_author_meta( 'edb_user_designer_level', $user->ID ) ); ?>"></label>
    </td>
    </tr>
  </table>
<?php 
};

add_action( 'personal_options_update', 'edb_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'edb_save_extra_profile_fields' );

function edb_save_extra_profile_fields( $user_id ) {
  if ( !current_user_can( 'edit_user', $user_id ) )
    return false;
  /* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
  $level = $_POST['edb_user_designer_level'];
  if(empty($level) || $level =='none'){
    update_usermeta( $user_id, 'edb_user_is_designer',  false );
  }else{
    update_usermeta( $user_id, 'edb_user_is_designer',  true );
  }
  update_usermeta( $user_id, 'edb_user_designer_level', $level );
};