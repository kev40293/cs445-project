<?php

class BookingDate {

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

}

?>
