<?php
session_start();
session_regenerate_id();


if (!isset($_SESSION['mail_count']))
{
	$_SESSION['mail_count'] = 1;
} elseif ($_SESSION['mail_count'] <= 5)
{
	$_SESSION['mail_count']++;	
} 
else 
{

	print <<<_ERROR_HTML_
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
	<title>Erorr with contact page</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="copyright" content="Copyright 2007 to me" />
	<meta name="description" content="A short description of the page" />
	<meta name="keywords" content="keywords describing this page, most search engines
	ignore this now due to abuse"/>
	</head>
	<body>
	<div>
	<p>You can only access this script 5 times per session to prevent spam attacks and email is only sent to the preset address</p>
	<p style="text-align:center;"><a href="index.html">Return to Home</a></p>
	</div>
	<div id="validator">
	<a href="http://validator.w3.org/check?uri=referer">
	<img src="valid-xhtml10.png" alt="W3C Button to test XHTML validation" /></a>
	</div>
	</body>
	</html>
_ERROR_HTML_;
	exit;
}



print <<<_START_HTML_
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>Contact Page</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="copyright" content="Copyright 2007 to me" />
<meta name="description" content="A short description of the page" />
<meta name="keywords" content="keywords describing this page, most search engines
ignore this now due to abuse"/>
</head>
<body>	
_START_HTML_;





if ( $_POST['submit'] )
{
 
      //Load Pear Mail package
     
    include 'Mail.php';
    include 'Mail/mime.php';
      // WARNING THIS IS VERY DANGEROUS WITHOUT FILTERING SO FILTER!!!!
    $from_email_tainted = $_POST['whofrom'];
    $subject_tainted = $_POST['subject'];
    $body_tainted = $_POST['msgbody'];
    $fullname_tainted = $_POST['fullname']; 
    //CLEAN UP
    $name_pattern ='/^(?:\w+[.\']? ?){1,7}$/';
    if ( preg_match($name_pattern, $fullname_tainted, $namematch))
    {
        $fullname_safe = $namematch[0];	
    } 
    else 
    {
        echo "Invalid Name";
        exit;	
    }
    
    $email_pattern='/^(?:\w+[.+-_]?){1,3}(?:\w+)@(?:\w+\.){1,5}\w{2,5}$/';
    if( preg_match($email_pattern, $from_email_tainted,$emailmatch))
    {
        $from_email_safe=$emailmatch[0];		
    } 
    else 
    {
        echo "Invalid 'From' Email address";
        exit;
    }
    
    $subject_pattern = '/(\w[.!?\'\" ]?){5,80}/';
    if (preg_match($subject_pattern, $subject_tainted, $subjectmatch))
    {
        $subject_safe = $subjectmatch[0];	
    }
    else 
    {
        echo "subject must be from 5-80 characters long and only contain
        a-z, A-Z, 0-9, !?\"_-' ";
        exit;	
    }
    
    $body_safe = strip_tags($body_tainted);
    $body_safe = str_replace('%','&#37;', $body_safe);
    
    
      $headers['From'] = "\"$fullname_safe\" <$from_email_safe>";
      
      /***************************************************************
      *     Replace the name and email adress in the next two lines  *
      *     with your own information                                *
      ***************************************************************/
      
      $headers['To'] = '"Matthew David Cummings" <matthewdavidcummings@gmail.com>';
      $recipients = 'Matthew David Cummings <matthewdavidcummings@gmail.com>';
      $headers['Subject'] = $subject_safe;
    
    $crlf = "\n";
    
    $mime = new Mail_mime($crlf);
    $mime->encodeRecipients($recipients);
    $mime->setTXTBody($body_safe);
    $mime->setHTMLBody($body_safe);
    //$mime->setHTMLBody($body_tainted);
    
    $body = $mime->get();
    $headers = $mime->headers($headers);

      
      if (! $mail_object = Mail::factory('mail'))
      {
        print "Mail Factory failed!";  
      }
    
      if (! $mail_object->send($recipients, $headers, $body))
      {
          print "Mail Send Erorr!";
      } 
      else 
      {
        
         /* ****************************************** 
         *  Put your own success status message here *
         *********************************************/
         
         print "Your email to Matthew David Cummings was sent";
      }

  
} 
else 
{
	 
print<<<_FORM_
<form method="post" action="$_SERVER[SCRIPT_NAME]">

  <table border="0">
    <tbody>
      <tr>
        <td style="text-align:right;">Your Name:</td>
        <td><input type="text" name="fullname" /></td>
      </tr>
      <tr>
        <td style="text-align:right;">Your Email:</td>
        <td><input type="text" name="whofrom" /></td>
      </tr>
      <tr>
        <td style="text-align:right;">To:</td>
        <td>Phil Waclawski: Felitaur Enterprises</td>
      </tr>
      <tr>
        <td style="text-align:right;">Subject:</td>
        <td><input type="text" name="subject" /></td>
      </tr>
      <tr>
        <td colspan="2"  style="text-align:center;">Please keep your messages relatively short, avoid using
          HTML and % characters.<br />
          Restrictions are in place to prevent spammers from abusing this
        page.</td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:center;"><textarea name="msgbody" rows="20"
        cols="70"></textarea></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:center;">
          <input type="submit" value="Send Email" name="submit" /></td>
      </tr>
    </tbody>
  </table>
</form>

_FORM_;

print<<<_END_HTML_
<div id="validator">
<a href="http://validator.w3.org/check?uri=referer">
<img src="valid-xhtml10.png" alt="W3C Button to test XHTML validation" /></a>
</div>
</body>
</html>
_END_HTML_;
	 
 }

?>

