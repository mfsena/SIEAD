<div id="contactform">
<h1>Contact <span>Form</span></h1>
<form name="contactform" id="form">
<div id="result">< ?php if($result) echo "<div class="message">".$result."</div>"; ?></div>
<label>Department</label><br>
<select name="dept" class="text">
	<option value="sales">Sales</option>
	<option value="support">Support</option>
	<option value="billing">Billing</option>
</select><br>
<label class="name">Name<br>
<input class="text" name="name" type="text" value=""><br></label>
<label class="email">Email<br>
<input class="text" name="email" type="text" value=""><br></label>
<label class="phno">Telephone no<br>
<input class="text" name="phno" type="text" value=""><br></label>
<label class="subject">Subject<br>
<input class="text" name="subject" type="text" value=""><br></label>
<label class="msg">Message<br>
<textarea class="text" name="msg"></textarea><br></label>
<input type="checkbox" name="selfcopy" value="yes">
<label>Send a copy to yourself?</label>
<?php MathGuard::insertQuestion(); ?>
<br><br>
<input type="hidden" name="browser_check" value="true">
<input type="button" name="submit" value="Submit" id="submit">

</form>
</div>