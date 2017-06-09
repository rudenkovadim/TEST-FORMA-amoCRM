<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Тестовая форма добавления клиента в amoCRM</title>
<style>
input {
	border:1px solid #000;
}

label {
  float: left;
  padding-right: 10px;
}
 
#contact_form {
  float: left;
  margin: 6px 10px;
}
 
.field {
  clear: both;
  text-align: right;
  line-height: 2em;
}

.forma {
	width:500px;
	padding:20px 20px;
	margin:50px 10px 0px 50px;
	border:1px solid #000;
	display:inline-block;
	vertical-align:top;
}

.task {
	width:500px;
	height:100px;
	padding:20px 20px;
	margin:50px 10px 0px 50px;
	border:1px solid #000;
	display:inline-block;
	vertical-align:top;
}

.trade {
	width:500px;
	padding:20px 20px;
	margin:50px 10px 0px 50px;
	border:1px solid #000;
	display:inline-block;
	vertical-align:top;
}

#modal_form {
	width: 300px; 
	height: 300px;
	border-radius: 5px;
	border: 3px #000 solid;
	background: #fff;
	position: fixed;
	top: 45%;
	left: 50%;
	margin-top: -150px;
	margin-left: -150px;
	display: none;
	opacity: 0;
	z-index: 5;
	padding: 20px 10px;
}

#modal_form #modal_close {
	width: 21px;
	height: 21px;
	position: absolute;
	top: 10px;
	right: 10px;
	cursor: pointer;
	display: block;
}

#overlay {
	z-index:3;
	position:fixed;
	background-color:#000;
	opacity:0.8;
	-moz-opacity:0.8;
	filter:alpha(opacity=80);
	width:100%; 
	height:100%;
	top:0;
	left:0;
	cursor:pointer;
	display:none;
}
</style>
</head>

<body>
	<div class="forma">
        <div class="field">
          <label for="contact_name">Имя</label><input id="contact_name" type="text" name="name">
        </div>
        <div class="field">
          <label for="contact_phone">Телефон</label><input id="contact_phone" type="tel" name="phone">
        </div>
        <div class="field">
          <label for="contact_email">E-mail</label><input id="contact_email" type="email" name="email">
        </div>
        <div class="field">
          <label for="contact_comm">Комментарий</label><input id="contact_comm" type="text" name="comm">
        </div>
        <div>
          <button onClick="add_contact();" type="submit">Создать контакт</button>
          <button type="reset">Очистить форму</button>
        </div>
	</div>
    <div id="trade" class="trade">
    	Информация по сделке...
    </div>
    <div class="task">
    	<button id="go" class="">Нажми на меня!</button>
    </div>
    <div id="modal_form">
    	<div id="modal_body">Текст формы</div>
    	<span id="modal_close">X</span>
    </div>
    <div id="overlay"></div>
</body>
<script src="jquery-3.1.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function add_contact()
	{
		var fName = $("#contact_name").val();
		var fPhone = $("#contact_phone").val();
		var fEmail = $("#contact_email").val();
		var fComm = $("#contact_comm").val();
		
		if(fName != "" && fPhone != "" && fEmail != "" && fComm != ""){
			var text = {
				'name': fName,
				'phone': fPhone,
				'email': fEmail,
				'comm': fComm
			};
			
			var info = JSON.stringify(text);
			
			$.ajax('handler.php',{
				cache: false,
				data: {
					"command":"add_contact",
					"info": info
				},
				type : "POST",
				success: function(msg){
					$("#trade").html(msg);
				}
			});
		}
		else
		{
			$("#contact_name").css({"border-color":"rgb(255,0,0)"});
			$("#contact_phone").css({"border-color":"rgb(255,0,0)"});
			$("#contact_email").css({"border-color":"rgb(255,0,0)"});
			$("#contact_comm").css({"border-color":"rgb(255,0,0)"});
			
			setTimeout(function(){
				$("#contact_name").css({"border-color":"rgb(0,0,0)"});
				$("#contact_phone").css({"border-color":"rgb(0,0,0)"});
				$("#contact_email").css({"border-color":"rgb(0,0,0)"});
				$("#contact_comm").css({"border-color":"rgb(0,0,0)"});			
			}, 2000);
		}
	}


	$(document).ready(function() {
		$('#go').click( function(event){
			event.preventDefault();
			$('#overlay').fadeIn(400,
				function(){
					$('#modal_form').css('display', 'block').animate({opacity: 1, top: '50%'}, 200);
			});
		});
		
		$('#modal_close, #overlay').click( function(){
			$('#modal_form')
				.animate({opacity: 0, top: '45%'}, 200,
					function(){
						$(this).css('display', 'none');
						$('#overlay').fadeOut(400);
					}
				);
		});
	});
</script>
</html>