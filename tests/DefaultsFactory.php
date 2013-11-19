<?php

function default_address() {
   $add = new SimpleXMLElement("<address></address>");
   $add->addChild("street");
   $add->addChild("city", "Chicago");
   $add->addChild("country");
   $add->addChild("state");
   $add->addChild("postal_code");
   return $add;
}

function default_restrictions() {
   $rest = new SimpleXMLElement("<restrictions></restrictions>");
   $rest->addChild("check_in_time");
   $rest->addChild("check_out_time");
   $rest->addChild("smoking");
   $rest->addChild("alcohol");
   $rest->addChild("cancellation_deadline");
   $rest->addChild("cancellation_penalty");
   return $rest;
}

function default_contact () {
   $cont = new SimpleXMLElement("<address></address>");
   $cont->addChild("phone");
   $cont->addChild("email");
   $cont->addChild("facebook");
   $cont->addChild("web");
   return $cont;
}

function search_object($date = null) {
   return array(
      "start_date" => $date,
      "end_date" => $date,
      "num" => 0,
      "city" => null,
      "resv_id" => null
   );
}

?>
