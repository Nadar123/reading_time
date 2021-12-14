<?php 

class Reading_time_shortcode {
    public function __construct(){
        add_shortcode('reading_time', 'reading_time_plug');
    }

    function reading_time_plug() {
        the_reading_time();
    }
}
$Reading_time_shortcode = new Reading_time_shortcode();