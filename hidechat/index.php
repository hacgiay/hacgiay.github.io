<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
<title>Hide Talk!</title>
<link type="text/css" rel="stylesheet" href="style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
</head>
<body>

<?php
	session_start();
	 
	function loginForm(){
	    echo'
	    <div id="loginform">
	    <form action="index.php" method="post">
	        <p>Please enter <b>your name</b> to continue:</p>
	        <label for="name">Name:</label>
	        <input type="text" name="name" id="name" />
	        <input type="submit" name="enter" id="enter" value="Login" />
	    </form>
	    </div>
	    ';
	}
	 
	if (isset($_POST['enter'])){
	    if($_POST['name'] != ""){
	        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
	        
	    }
	    else {
	        echo '<span class="error">PLEASE TYPE IN A NAME</span>';
	    }
	}
?>

<?php
	if (!isset($_SESSION['name'])) {
	    loginForm();
	}
	else {
?>
	<div id="wrapper">
	    <div id="menu">
	        <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
	        <p class="logout"><a id="exit" href="#">Logout</a></p>
	        <div style="clear:both"></div>
	    </div>    
	    <div id="chatbox">
		    <?php
			if(file_exists("log.html") && filesize("log.html") > 0){
			    $handle = fopen("log.html", "r");
			    $contents = fread($handle, filesize("log.html"));
			    fclose($handle);
			    echo $contents;
			}
			?>
		</div>

	    <form name="message" action="">
	        <input name="usermsg" type="text" id="usermsg" size="63" />
	        <input name="submitmsg" type="submit"  id="submitmsg" value="Send" />
	    </form>
	</div>
	<script type="text/javascript">
	// jQuery Document
	$(document).ready(function(){
		//If user wants to end session
		$("#exit").click(function(){
			var exit = confirm("Are you sure you want to end the session?");
			if(exit==true) { 
				window.location = 'index.php?logout=true';
			}
		});
	});
	</script>

<?php
	}
?>

<?php
if(isset($_GET['logout'])){ 
     
    //Simple exit message
    $fp = fopen("log.html", 'a');
    fwrite($fp, "<div class='msgln'><i>User ". $_SESSION['name'] ." has left the chat session.</i><br></div>");
    fclose($fp);
     
    session_destroy();
    header("Location: index.php");
}
?>

<script type="text/javascript">
	var oldSize;
	var newSize;
	oldSize = $('#chatbox div').length;
	$("#submitmsg").click(function() {	
		var clientmsg = $("#usermsg").val();
		if (clientmsg != "" || clientmsg != null) {
			//console.log(clientmsg);
			if (clientmsg == "//clear") {
				newSize = 0;
				oldSize = newSize;
			}
			$.post("post.php", {text: clientmsg});				
			$("#usermsg").attr("value", "");
			
		}
		return false;
	});

	function loadLog() {
		newSize = $('#chatbox div').length;
		//console.log(oldSize);
		//console.log(newSize);
		if (newSize > oldSize) {
			oldSize = newSize;
			var audio = new Audio('notification/notification.mp3');
			audio.play();
		}
		oldSize = newSize;
		var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll height before the request
		$.ajax({
			url: "log.html",
			cache: false,
			success: function(html){		
				$("#chatbox").html(html); //Insert chat log into the #chatbox div	
				
				//Auto-scroll			
				var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll height after the request
				if(newscrollHeight > oldscrollHeight){
					$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
				}
		  	},
		});
	}

	
	//console.log(oldSize)

	if ($("#usermsg").val() != null) {	
		setInterval (loadLog, 1500);
	}

</script>
</body>
</html>
