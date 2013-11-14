<?php

class Search {
   private $options;
   public function __construct($opts) {
      $this->options = array("city" => null,
         "start_date" => null,
         "end_date" => null,
         "num" => 0);

      foreach ($opts as $key => $value) {
         if ($key == "beds") {
            $this->options["num"] = $value;
         }
         else
            $this->options[$key] = $value;
      }
      if ($this->options["end_date"] == null)
         $this->options["end_date"] = $this->options["start_date"];
   }

   public function get_results() {
      $db = open_database();
      return $db->search_availability($this->options);
   }
}
?>


