<?php

class SendToMobile_IndexController extends Omeka_Controller_Action
{
    public function indexAction() {}

	public function responseAction() 
	{
		$from = $_POST['from'];
		$to = $_POST['to'];
		$carrier = $_POST['carrier'];
		$message = stripslashes($_POST['message']);

		if ((empty($from)) || (empty($to)) || (empty($message))) {
			echo $this->failAction();
		}

		else if ($carrier == "verizon") {
		$formatted_number = $to."@vtext.com";
		mail("$formatted_number", "SMS", "$message"); 
		// Currently, the subject is set to "SMS". Feel free to change this.

			echo $this->successAction();
		}

		else if ($carrier == "tmobile") {
		$formatted_number = $to."@tomomail.net";
		mail("$formatted_number", "SMS", "$message");

			echo $this->successAction();
		}

		else if ($carrier == "sprint") {
		$formatted_number = $to."@messaging.sprintpcs.com";
		mail("$formatted_number", "SMS", "$message");

			echo $this->successAction();
		}

		else if ($carrier == "att") {
		$formatted_number = $to."@txt.att.net";
		mail("$formatted_number", null, "$message");

			echo $this->successAction();
		}

		else if ($carrier == "virgin") {
		$formatted_number = $to."@vmobl.com";
		mail("$formatted_number", "SMS", "$message");

			echo $this->successAction();
		}
	}
	
	private function failAction() {
		return "there was an unexpected error while attempting to send your message";
	}
	
	private function successAction() {
		return "message properly sent";
	}
}