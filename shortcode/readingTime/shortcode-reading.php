<?php 

class Reading_time_shortcode {
    public function __construct(){
        add_shortcode('reading_time',  array($this, 'reading_time_plug'));
    }

    function reading_time_plug() {
        ob_start();
        the_reading_time();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}

$Reading_time_shortcode = new Reading_time_shortcode();