<?php

function default_address() {
   $add = new SimpleXMLElement("<address></address>");
   $add->addChild("street");
   $add->addChild("city");
   $add->addChild("country");
   $add->addChild("state");
   $add->addChild("postal_code");
   return $add;
}

function default_restrictions() {
   $rest = new SimpleXMLElement("<address></address>");
   $rest->addChild("check_in_time");
   $rest->addChild("check_out_time");
   $rest->addChild("smoking");
   $rest->addChild("alcohol");
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

function search_object() {
   return array(
      "start_date" => null,
      "end_date" => null,
      "num" => 0,
      "city" => null);
}

?>
