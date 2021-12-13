<?php

function get_reading_time(){
    $Reading_time_plugin = new ReadingTimePlugin();
    return $Reading_time_plugin->render_output_html();
}

function the_reading_time(){
    echo get_reading_time();
}