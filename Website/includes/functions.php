<?php
	if($_SERVER["HTTPS"] != "on")
	{
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}

	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/includes/dsn.inc");
	include_once("$path/includes/password.php");
	include_once("$path/includes/phpmailer/class.phpmailer.php");
	require("$path/includes/fpdf.php");
	
	function DoCleanup()
	{
		global $db;
	
		// Delete expired links.
		$db->query("DELETE FROM ConfirmationLinks WHERE Expires IS NOT NULL AND Expires < NOW()");
		$db->query("DELETE FROM PendingAccounts WHERE Expires < NOW()");

		// Delete expired shopping cart entries.
		$db->query("DELETE FROM ShoppingCart WHERE DATE_ADD(Created, INTERVAL 24 HOUR) < NOW()");
		
		// Delete gift certificates 3 months after they were used up. (Maybe not yet.)
		//$db->query("DELETE FROM GiftCertificates WHERE CurrentValue = 0 AND Badges = 0 AND " . 
		//	"DATE_ADD(Redeemed, INTERVAL 3 MONTH) < NOW()");
		
		// Delete expired Permissions
		$db->query("DELETE FROM Permissions WHERE Expiration IS NOT NULL AND Expiration < NOW()");
	}

	function AuthenticateUser($email, $pass, $checkOnly = false)
	{
		global $db;
		$result = $db->query("SELECT PeopleID, FirstName, LastName, BadgeName, Password FROM People WHERE Email = '" .
			$db->real_escape_string($email) . "'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();
			$dbpw = $row['Password'];
			$id = $row['PeopleID'];
			$name = trim($row['FirstName'] . " " . $row['LastName']);
			$badge = trim($row['BadgeName']);
			$result->close();
			
			if(password_verify($pass, $dbpw))
			{
				if(!$checkOnly)
				{
					$db->query("UPDATE People SET LastChanged = NOW() WHERE PeopleID = $id");
					$_SESSION['PeopleID'] = $id;
					$_SESSION['FullName'] = $name;
					$_SESSION['BadgeName'] = $badge;
					$_SESSION['Email'] = $email;
				}
				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	
	function GetUserDescription($id)
	{
		global $db;
		$result = $db->query("SELECT pd.ShortName, pd.Description FROM PermissionDetails pd INNER JOIN Permissions p ON " . 
			"p.Permission = pd.Permission WHERE PeopleID = " . $id);
		if($result->num_rows > 0)
		{
			$desc = "";
			while($row = $result->fetch_array())
				$desc .= ", <span class=\"masterTooltip\" title=\"" . $row['Description'] . "\">" . $row['ShortName'] . "</span>";
			$result->close();
			return substr($desc, 2);
		}
		else
		{
			return "Convention Member";
		}
	}
	
	function DoesUserBelongHere($perm)
	{	
		$perms = UserPermissions();
		$perm = strtolower($perm);
		
		// If they have no permissions, they're just a con-goer and they probably shouldn't be here.
		if(count($perms) == 0) return false;		
		// The SuperAdmin permission gets all access.
		if(in_array("superadmin", $perms)) return true;
		// Check for the exact permission.
		if(in_array($perm, $perms)) return true;
		// If this is a "staff" permission, like "RegStaff", check for "RegLead" too.
		if(substr_count($perm, "staff"))
			if(in_array(str_replace("staff", "lead", $perm), $perms)) return true;
			
		return false;
	}
	
	function UserPermissions($id = -1, $actual = false)
	{
		global $db;
		$perms = array();		
		if($id == -1 && !isset($_SESSION['PeopleID'])) return $perms;
		if($id == -1) $id = $db->real_escape_string($_SESSION['PeopleID']);
		
		// Gather permissions for this user.
		$result = $db->query("SELECT Permission FROM Permissions WHERE PeopleID = $id");
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$perms[] = strtolower($row["Permission"]);
			$result->close();
		}
		
		if(!$actual)
		{
			// Certain permissions are special and imply a number of additional permissions. Add them here.
			if(in_array("conchair", $perms))
			{
				// The Conchair gets access to registration
				$perms[] = 'reglead';
			}
			
			if(in_array("treasurer", $perms))
			{
				$perms[] = 'reglead';
			}
		}
		
		return $perms;
	}
	
	function UserInterests()
	{
		global $db;
		$interests = array();
		if(!isset($_SESSION['PeopleID'])) return $interests;
		
		// Gather permissions for this user.
		$id = $db->real_escape_string($_SESSION['PeopleID']);
		$result = $db->query("SELECT Interest FROM PeopleInterests WHERE PeopleID = $id");
		while($row = $result->fetch_array())
			$interests[] = $row["Interest"];
		$result->close();
		
		return $interests;
	}
	
	function SendPasswordReset($email, $id = -1, $manuallyCreated = false)
	{
		global $db, $smtpPass;
		
		DoCleanup();

		$result = $db->query("SELECT PeopleID, FirstName, LastName FROM People WHERE Email = '" . 
			$db->real_escape_string($email) . "'");
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();			
			if($id == -1) $id = $row['PeopleID'];
			$name = $row['FirstName'] . ' ' . $row['LastName'];
			$result->close();
			
			// Generate reset link that expires in 72 hours.
			$data = uniqid('', true);
			$expire = new DateTime();
			if($manuallyCreated)
				$expire->add(new DateInterval('P28D'));
			else
				$expire->add(new DateInterval('PT72H'));			
			$db->query("INSERT INTO ConfirmationLinks (Code, PeopleID, Type, Expires) VALUES ('" . 
				$db->real_escape_string($data) . "', " . $id . ", 'PWReset', '" . DateToMySQL($expire) . "')");
			
			// Send email.
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->Port = 587;
			$mail->Host = "mail.capricon.org";
			$mail->Username = "outgoing@capricon.org";
			$mail->Password = $smtpPass;
			$mail->From = "registration@capricon.org";
			$mail->FromName = "Capricon Registration";
			$mail->AddAddress($email, $name);
			$mail->WordWrap = 70;
			if($manuallyCreated)
			{
				$mail->Subject = "Capricon Registration Account Creation";
				$mail->Body = "Hello! Your Capricon registration has been entered into the system, and an account " .
					"has been created for you at this email address. (A separate email will be sent confirming your " .
					"registration(s).)\r\n\r\nIn order to finish setting up your account, please click on the following " .
					"link to set your password reset: https://registration.capricon.org/resetpassword.php?id=" . $data .
					"\r\n\r\nThe above link will expire after 28 days. If this expires, you may request a new " . 
					"reset link at https://registration.capricon.org/forgotpassword.php.\r\n\r\nIf you have any " . 
					"questions, don't hesitate to contact Phandemonium's IT Director at it@phandemonium.org.\r\n\r\n" . 
					"Have a great day!";
			}
			else
			{
				$mail->Subject = "Capricon Registration Password Reset";
				$mail->Body = "Hello! A password reset has been requested for a Capricon Registration account " .
					"at this email address.\r\n\r\nIf this was requested by you, please click on the following " .
					"link to continue your password reset: https://registration.capricon.org/resetpassword.php?id=" . $data .
					"\r\n\r\nIf this was not initiated by you, there is nothing you need to do; the above link " .
					"will expire after 72 hours.\r\n\r\nIf you have any questions, don't hesitate to contact " .
					"Phandemonium's IT Director at it@phandemonium.org.\r\n\r\nHave a great day!";
			}
			$mail->Send();
			return true;
		}
		
		$result = $db->query("SELECT PendingID, FirstName, LastName FROM PendingAccounts WHERE Email = '" . 
			$db->real_escape_string($email) . "'");
			
		if($result->num_rows > 0)
		{
			$row = $result->fetch_array();			
			$data = $row["PendingID"];
			$name = $row['FirstName'] . ' ' . $row['LastName'];
			$result->close();

			// Send activation email.
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->Port = 587;
			$mail->Host = "mail.capricon.org";
			$mail->Username = "outgoing@capricon.org";
			$mail->Password = $smtpPass;
			$mail->From = "registration@capricon.org";
			$mail->FromName = "Capricon Registration";
			$mail->AddAddress($email, $fname . " " . $lname);
			$mail->WordWrap = 70;
			$mail->Subject = "Capricon Registration Account Activation";
			$mail->Body = "Hello! A Capricon Registration account was created for you at this email address." .
				"\r\n\r\nIf this was requested by you, please click on the following link to finish activating " . 
				"your account: https://registration.capricon.org/activate.php?id=" . $data . "\r\n\r\nIf this " . 
				"was not initiated by you, there is nothing you need to do; the above link will expire after " . 
				"72 hours.\r\n\r\nIf you have any questions, don't hesitate to contact Phandemonium's IT " . 
				"Director at it@phandemonium.org or Capricon Registration at registration@capricon.org.\r\n\r\n" . 
				"Have a great day!";
			$mail->Send();
			return true;
		}
		
		return false;
	}
	
	function DateToMySQL($date)
	{
		return $date->format('Y-m-d H:i:s');
	}
	
	function PostToURL($url, $data)
	{
		$fields = '';
		foreach($data as $key => $value) { 
		  $fields .= $key . '=' . $value . '&'; 
		}
		rtrim($fields, '&');

		$post = curl_init();

		curl_setopt($post, CURLOPT_URL, $url);
		curl_setopt($post, CURLOPT_POST, count($data));
		curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($post);

		curl_close($post);
		return $result;
	}
	
	function getShoppingCart()
	{
		global $db;		
		$entries = array();
		
		// Badges
		$result = $db->query("SELECT CartID, CONCAT(ab.Year, ' Badge: ', bt.Description) AS Item, sc.Price, CONCAT(p.FirstName, ' ', p.LastName) " . 
			"AS Name, CONCAT('Badge Name: ', sc.ItemDetail) AS Details, " . 
			"pc.Code AS PromoCode, pc.Discount, gc.CertificateCode, gc.CurrentValue, gc.Badges FROM ShoppingCart sc " . 
			"INNER JOIN AvailableBadges ab ON ab.AvailableBadgeID = sc.ItemTypeID " . 
			"INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = ab.BadgeTypeID " . 
			"INNER JOIN People p ON p.PeopleID = sc.PeopleID " . 
			"LEFT OUTER JOIN PromoCodes pc ON pc.CodeID = sc.PromoCodeID " . 
			"LEFT OUTER JOIN GiftCertificates gc ON gc.CertificateID = sc.CertificateID " . 
			"WHERE sc.PurchaserID = " . $_SESSION["PeopleID"] . " AND ItemTypeName = 'Badge'");
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$entries[] = $row;
			$result->close();
		}

		// Gift Certificates
		$result = $db->query("SELECT sc.CartID, 'Gift Certificate' AS Item, sc.Price, '' AS Details, " . 
			"CONCAT(p.FirstName, ' ', p.LastName) AS Name, '' AS PromoCode, 0.00 AS Discount, '' AS CertificateCode, 0.00 AS CurrentValue, 0 AS Badges " .
			"FROM ShoppingCart sc INNER JOIN People p ON p.PeopleID = sc.PurchaserID WHERE " . 
			"sc.PurchaserID = " . $_SESSION["PeopleID"] . " AND ItemTypeName = 'GiftCertificate'");
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$entries[] = $row;
			$result->close();
		}

		// Catan Pre-Registration
		$result = $db->query("SELECT sc.CartID, CONCAT(ab.Year, ' Badge: ', bt.Description) AS Item, 0.00, '' AS Details, " . 
			"CONCAT(p.FirstName, ' ', p.LastName) AS Name, '' AS PromoCode, 0.00 AS Discount, '' AS CertificateCode, 0.00 AS CurrentValue, 0 AS Badges " . 
			"FROM ShoppingCart sc " .			
			"INNER JOIN AvailableBadges ab ON ab.AvailableBadgeID = sc.ItemTypeID " . 
			"INNER JOIN BadgeTypes bt ON bt.BadgeTypeID = ab.BadgeTypeID " . 
			"INNER JOIN People p ON p.PeopleID = sc.PurchaserID WHERE " . 
			"sc.PurchaserID = " . $_SESSION["PeopleID"] . " AND ItemTypeName = 'Catan'");
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$entries[] = $row;
			$result->close();
		}

		// Art Show Hanging Fees
		$result = $db->query("SELECT sc.CartID, 'Art Show Hanging Fees' AS Item, sc.Price, ItemDetail AS Details, " . 
			"CONCAT(p.FirstName, ' ', p.LastName) AS Name, '' AS PromoCode, 0.00 AS Discount, '' AS CertificateCode, 0.00 AS CurrentValue, 0 AS Badges " .
			"FROM ShoppingCart sc INNER JOIN People p ON p.PeopleID = sc.PurchaserID WHERE " . 
			"sc.PurchaserID = " . $_SESSION["PeopleID"] . " AND ItemTypeName = 'HangingFees'");
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
				$entries[] = $row;
			$result->close();
		}

		// Add other handlers here, one for each type of cart item type.
		
		return $entries;
	}
	
	function produceGiftCertificatePDF($certIDs)
	{
		global $db;
		if(!is_array($certIDs))
		{
			$certID = $certIDs;
			$certIDs = array($certID);
		}
		$pdf = new FPDF('L', 'mm', 'Letter');
		foreach($certIDs as $certID)
		{
			$result = $db->query("SELECT CertificateCode, CurrentValue, Badges FROM GiftCertificates WHERE " . 
				"CertificateID = $certID AND (PurchaserID = " . $_SESSION["PeopleID"] . 
				" OR Recipient IS NULL OR Recipient = '" . $_SESSION["Email"] . "')");
			if($result->num_rows == 0)
			{
				echo '<html><body><h1>Invalid certificate ID: ' . $certID . '</h1></body></html>';
				return;
			}
			$row = $result->fetch_array();
			$result->close();
			
			$pdf->AddPage();
			$pdf->SetFont('Arial', '', 32);
			$pdf->Image('includes/certBorder.png', 1, 1, 277);
			$pdf->Ln(10);
			$pdf->Cell(0, 10, 'Gift Certificate', 0, 1, 'C');
			$pdf->SetFont('Arial', '', 24);
			$pdf->Cell(0, 15, 'for Capricon Registration', 0, 1, 'C');
			$pdf->SetFont('Arial', '', 16);
			$pdf->Cell(0, 5, 'https://registration.capricon.org', 0, 1, 'C');
			$pdf->Ln(20);
			$pdf->Cell(0, 10, 'You have been issued a gift certificate for the following amount:', 0, 1, 'C');
			$pdf->Ln(15);
			$pdf->SetFont('Arial', '', 36);
			if($row["Badges"] > 0)
			{
				$pdf->SetTextColor(240, 40, 40);
				$pdf->Cell(0, 10, $row["Badges"] . ' Free Badge' . ($row["Badges"] == 1 ? "" : "s"), 0, 1, 'C');
			}
			else
			{
				$pdf->SetTextColor(45, 156, 75);
				$pdf->Cell(0, 10, sprintf("$%01.2f", $row["CurrentValue"]), 0, 1, 'C');
			}
			$pdf->Ln(20);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial', '', 16);
			$pdf->Cell(0, 10, 'To use this certificate, enter the following code online at checkout:', 0, 1, 'C');
			$pdf->Ln(10);
			$pdf->SetFont('Arial', '', 40);
			$pdf->Cell(0, 10, $row["CertificateCode"], 0, 1, 'C');				
			$pdf->Ln(25);
			$pdf->SetFont('Arial', '', 12);
			$pdf->Cell(0, 6, 'If you have any questions, or need help using this certificate, send an email to Phandemonium IT at it@phandemonium.org.', 0, 1, 'C');
			$pdf->Cell(0, 6, 'This certificate has no cash value, can only be used at registration.capricon.org, ' . 
				'and does not expire.', 0, 1, 'C');
		}	
		
		return $pdf;
	}
	
	function produceOrderPDF($purchases, $originalTotal, $finalTotal)
	{
		global $db;		
		$result = $db->query("SELECT CONCAT(FirstName, ' ', LastName) AS Name, Address1, Address2, City, State, " .
			"ZipCode, Country, Phone1 FROM People WHERE PeopleID = " . $_SESSION["PeopleID"]);
		$row = $result->fetch_array();
		$result->close();
		$thisYear = date("n") >= 3 ? date("Y") + 1: date("Y");
		$capriconYear = $thisYear - 1980;
		
		$pdf = new FPDF('P', 'mm', 'Letter');
		$pdf->AddPage();
		$pdf->SetFont('Arial', '', 32);
		$pdf->Cell(0, 8, 'Capricon ' . $capriconYear, 0, 1, 'C');
		$pdf->SetFont('Arial', '', 24);
		$pdf->Cell(0, 12, 'Registration Form', 0, 1, 'C');
		$pdf->SetFont('Arial', '', 14);
		$pdf->Cell(0, 5, 'https://registration.capricon.org', 0, 1, 'C');
		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(0, 6, 'This letter acts as your registration form for the upcoming Capricon ' . $capriconYear .
			' convention. Your', 0, 1);
		$pdf->Cell(0, 6, 'order consists of the following items:', 0, 1);
		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 12);
		$pdf->SetTextColor(45, 156, 75);
		$line = 1;
		foreach($purchases as $purchase)
			$pdf->Cell(0, 6, $line++ . ". $purchase", 0, 1);
		$pdf->Ln(5);
		if($originalTotal != $finalTotal)
		{
			$pdf->Cell(0, 6, "Original Total: " . sprintf("$%01.2f", $originalTotal), 0, 1);
			$pdf->Cell(0, 6, "Discounts:      " . sprintf("-$%01.2f", ($originalTotal - $finalTotal)), 0, 1);
		}
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 6, "Total:           " . sprintf("$%01.2f", $finalTotal), 0, 1);
		$pdf->Ln(5);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(0, 6, 'Badges have already been entered into the system and will be confirmed once your payment has been', 0, 1);
		$pdf->Cell(0, 6, 'received by the Registration team and has been processed. This occurs in batches throughout the year.', 0, 1);
		$pdf->Cell(0, 6, 'If you have any questions about your pending registration at any time, feel free to send an email to', 0, 1);
		$pdf->Cell(0, 6, 'registration@capricon.org to request help.', 0, 1);
		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'U', 12);
		$pdf->Cell(0, 6, 'Your Contact Information', 0, 1);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(0, 6, 'Name: ' . $row["Name"], 0, 1);
		$pdf->Cell(0, 6, 'Address: ' . $row["Address1"], 0, 1);
		if(isset($row["Address2"])) $pdf->Cell(0, 6, '         ' . $row["Address2"], 0, 1);
		$pdf->Cell(0, 6, 'City: ' . $row["City"] . '    State: ' . $row["State"] . '    Zip: ' . $row["ZipCode"] .
			'    Country: ' . $row["Country"], 0, 1);
		$pdf->Cell(0, 6, 'Phone: ' . $row["Phone1"], 0, 1);
		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(0, 6, 'Please mail payments to:', 0, 1);
		$pdf->SetFont('Arial', '', 14);
		$pdf->Cell(0, 6, 'Capricon ' . $capriconYear, 0, 1);
		$pdf->Cell(0, 6, '126 E. Wing Street, #244', 0, 1);
		$pdf->Cell(0, 6, 'Arlington Heights, IL 60004', 0, 1);
		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(0, 6, 'If sending a Check or Money Order, make them to "CAPRICON". DO NOT MAIL CASH!', 0, 1);
		$pdf->Cell(0, 6, 'Paying with a Credit Card? Fill out the following information:', 0, 1);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(0, 12, 'Card Type (Check One):   ___ Visa   ___ Mastercard   ___ American Express   ___ Discover', 0, 1);
		$pdf->Cell(0, 8, 'Card # ___________________________________ Expiration (MM/YYYY): ____________ CVV/Security Code: ________', 0, 1);
		
		return $pdf;
	}
?>