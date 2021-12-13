<?php 

function reading_time_plug() {
    the_reading_time();
    print_r(reading_time_plug());
    die();
}
add_shortcode('reading_time', 'reading_time_plug');