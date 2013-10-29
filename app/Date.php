<?php

class BookingDate {

   public static function dates_from_range($start, $end) {
      $range = array();
      $a = new DateTime($start, new DateTimeZone('UTC'));

      while ($a->format('Ymd') <= $end) {
         $range[] = $a;
         $a->add(new DateInterval('P1D'));
      }

      return $range;
   }

   public static function date_range($start, $number) {
      if ($number < 0)
         throw new InvalidArgumentException();
      $range = array();
      $a = new DateTime($start, new DateTimeZone('UTC'));

      foreach (range(1,$number) as $i) {
         $range[] = $a->format('Ymd');
         $a->add(new DateInterval('P1D'));
      }

      return $range;
   }

   public static function get_num_days($start, $end){
      $num = 0;
      $a = new DateTime($start, new DateTimeZone('UTC'));

      while ($a->format('Ymd') <= $end) {
         $a->add(new DateInterval('P1D'));
         $num++;
      }

      return $num;
   }

}

?>
