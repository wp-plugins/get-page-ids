<?php
/*
Plugin Name: Get page IDs
Plugin URI: http://iamntz.com
Description: Because there are some dumb-asses out there who believe that's ok to let the user guess page the ID's, you will find a new page under tools menu that will help you with this. Enjoy!
Author: Ionut Staicu
Version: 1.0
Author URI: http://iamntz.com
*/

class Create_array_of_page_ids{
  function __construct(){
    add_action( 'admin_menu', array( &$this, 'add_menu_item' ) );
  }

  public function add_menu_item(){
    add_management_page( 'Get Page IDs', 'Get Page IDs', 'manage_options', 'ntz-get-page-ids', array( &$this, 'get_page_ids' ) );
  } // add_menu_item

  public function get_page_ids(){
    ?>
    <style type="text/css" media="screen">
      #pageIDs {
        height:100px;
      }
      .ntzPageSelector,
      .ntzPageSelector ul,
      .ntzPageSelector li {
        margin:0;
      }
      .ntzPageSelector li {
        margin-top:1em;
      }
      .ntzPageSelector ul {
        margin-left:2em;
        margin-bottom:1.5em;
      }
    </style>

    <script type="text/javascript">
      jQuery(document).ready(function($){
        $('.ntzPageSelector').on('change', 'input[type="checkbox"]', function(){
          $(this).closest('li').find('ul input[name="page[]"]').attr( 'checked', $(this).is(':checked') );
          var selectedPages = $('.ntzPageSelector input[name="page[]"]:checked').map( function(){
            return this.value;
          } ).get().join(", ");

          $('#pageIDs').val( selectedPages );
        });

        $('#pageIDs').on('blur', function(){
          var ids = this.value.split(',');
          $(ids).each(function(){
            var selectedID = $.trim( this );
            $('.ntzPageSelector input[name="page[]"]').filter(function(){
              return this.value == selectedID
            }).attr('checked', true)
          })
        })
      });
    </script>

    <div class="wrap">
      <div id="icon-tools" class="icon32"><br></div><h2>Get Page IDs</h2>
        <ul class="ntzPageSelector">
          <?php
          $all_parent_pages = get_pages(array( 'child_of' => 0, 'parent' => 0 ));

          foreach( (array) $all_parent_pages as $parent_page ) { 
            $this->display_page( $parent_page );
          } ?>
        </ul>
        <p><textarea id="pageIDs" class="widefat"></textarea></p>
      </div>
    <?php 
  } // get_page_ids

  private function display_page( $page ){
    printf( '<li><label><input type="checkbox" name="page[]" value="%d"> %s</label>', $page->ID, $page->post_title );

    $page_children = get_pages( array( 'child_of' => $page->ID, 'parent' => $page->ID ) );
    if( count( $page_children ) > 0 ){
      echo "<ul>";
        foreach( (array) $page_children as $page_child ) {
          $this->display_page( $page_child );
        }
      echo "</ul>";
    }
  } // display_page

}//Create_array_of_page_ids

new Create_array_of_page_ids();