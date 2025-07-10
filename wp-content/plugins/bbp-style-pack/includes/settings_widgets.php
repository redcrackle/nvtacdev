<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


function bsp_widgets() {
 ?>
			
        <table class="form-table">	
                <tr valign="top">
                        <th colspan="2">

                        <h3>
                                <?php esc_html_e ('Widgets' , 'bbp-style-pack' ) ; ?>
                        </h3>
					
                        <h4><span style="color:blue">
                                <?php esc_html_e('Latest activity', 'bbp-style-pack' ) ; ?>
                        </span></h4>

                        <p>
                                <?php esc_html_e('This widget combines Recent Topics and Recent replies to give a single more clear latest activity widget', 'bbp-style-pack'); ?>
                        </p>
                        <p>
                                <?php esc_html_e('This widget is automaticlly available, so you will find it in Dashboard>Appearance>Widgets>(Style Pack) Latest Activity', 'bbp-style-pack'); ?>
                        </p>
                        
                        </th>
                </tr>
        </table>


        <table>
                <tr>
                        <th style="text-align:center"> <?php esc_html_e('FROM', 'bbp-style-pack' ) ; ?></th>
                        <th style="text-align:center"> <?php esc_html_e('TO', 'bbp-style-pack' ) ; ?></th>
                </tr>
                <tr>
                        <td><?php echo '<img src="' . esc_url(plugins_url( 'images/widgets1.JPG',dirname(__FILE__) ) ) . '"  > '; ?></td>
                        <td><?php echo '<img src="' . esc_url(plugins_url( 'images/widgets2.JPG',dirname(__FILE__))  ) . '" > '; ?></td>
                </tr>
        </table>
		
		<h4><span style="color:blue">
                <?php esc_html_e('Forums List', 'bbp-style-pack' ) ; ?>
        </span></h4>

        <p>
                <?php esc_html_e('This widget is automaticlly available, so you will find it in Dashboard>Appearance>Widgets>(Style Pack) Forums List', 'bbp-style-pack'); ?>
        </p>
       
        
        <table>
                <tr>
                        <td colspan=2><?php echo '<img src="' . esc_url(plugins_url( 'images/forums_list.png',dirname(__FILE__) ) ) . '"  > '; ?></td>
                </tr>
        </table>



        <h4><span style="color:blue">
                <?php esc_html_e('Single Forum Information', 'bbp-style-pack' ) ; ?>
        </span></h4>

        <p>
                <?php esc_html_e('This widget is automaticlly available, so you will find it in Dashboard>Appearance>Widgets>(Style Pack) Single Forum Information', 'bbp-style-pack'); ?>
        </p>
        <p>
                <?php esc_html_e('This widget will only show on single forum pages', 'bbp-style-pack'); ?>
        </p>

        
        <table>
                <tr>
                        <td colspan=2><?php echo '<img src="' . esc_url(plugins_url( 'images/forum-description.PNG',dirname(__FILE__)  )) . '"  > '; ?></td>
                </tr>
        </table>

        
        <h4><span style="color:blue">
                <?php esc_html_e('Single Topic Information', 'bbp-style-pack' ) ; ?>
        </span></h4>

        <p>
                <?php esc_html_e('This widget is automaticlly available, so you will find it in Dashboard>Appearance>Widgets>(Style Pack) Single Topic Information', 'bbp-style-pack'); ?>
        </p>
        <p>
                <?php esc_html_e('This widget will only show on single topic pages', 'bbp-style-pack'); ?>
        </p>

        
        <table>
                <tr>
                        <td colspan=2><?php echo '<img src="' . esc_url(plugins_url( 'images/topic-description.PNG',dirname(__FILE__) ) ) . '"  > '; ?></td>
                </tr>
        </table>
                        
 <?php
}
