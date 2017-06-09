<?php

if($_POST['command']=="add_contact"){
	
	$json = json_decode($_POST['info'], true);
	$_name = $json['name'];
	$_phone = $json['phone'];
	$_email = $json['email'];
	$_comm = $json['comm'];
	
	$user=array(
	  'USER_LOGIN'=>'koro-g@yandex.ru',
	  'USER_HASH'=>'6b324db66d346490c7f613be81824cf9'
	);
	 
	$subdomain='testforma01';
	$link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
	
	$curl=curl_init();
	
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
	curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
	curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	 
	$out=curl_exec($curl);
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	curl_close($curl);
	 
	$Response=json_decode($out,true);
	$Response=$Response['response'];
	if(isset($Response['auth'])){
	 	echo 'Авторизация прошла успешно';
	} else {
		echo 'Авторизация не удалась';
	}
	
	//------------------
	$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/accounts/current';
	$curl=curl_init();
	
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	 
	$out=curl_exec($curl);
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	curl_close($curl);
	
	$Response=json_decode($out,true);
	$account=$Response['response']['account'];
	
	//--------------------
	
	$need=array_flip(array('POSITION','PHONE','EMAIL'));
	if(isset($account['custom_fields'],$account['custom_fields']['contacts']))
	  do
	  {
		foreach($account['custom_fields']['contacts'] as $field)
		  if(is_array($field) && isset($field['id']))
		  {
			if(isset($field['code']) && isset($need[$field['code']]))
			  $fields[$field['code']]=(int)$field['id'];
		   
			$diff=array_diff_key($need,$fields);
			if(empty($diff))
			  break 2;
		  }
		  if(isset($diff)){
			echo 'В amoCRM отсутствуют следующие поля'.': '.join(', ',$diff);
		  }
		  else {
			echo 'Невозможно получить дополнительные поля';
		  }
		}
	  while(false);
	else {
	 echo 'Невозможно получить дополнительные поля';
	}
	$custom_fields=isset($fields) ? $fields : false;
	
	//---------------------
	// контакт
	//---------------------
	
	$contacts['request']['contacts']['add']=
	array(
	  array(
		'name'=>$_name,
		'custom_fields'=>array(
		  array(
			'id'=>$custom_fields['PHONE'],
			'values'=>array(
			  array(
				'value'=>$_phone,
				'enum'=>'MOB'
			  )
			)
		  ),
		  array(
			'id'=>$custom_fields['EMAIL'],
			'values'=>array(
			  array(
				'value'=>$_email,
				'enum'=>'WORK'
			  )
			)
		  )
		)
	  )
	);
	
	
	$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
	
	$curl=curl_init();
	
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
	curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($contacts));
	curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	 
	$out=curl_exec($curl);
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	
	//-----------------------------
	// задача
	//-----------------------------
	
	$Response=json_decode($out,true);
	$Response=$Response['response']['contacts']['add'];
	 
	foreach($Response as $v)
	  if(is_array($v))
		$contact_id = $v['id'];
		
	$date_task = date("U", mktime(23, 59, 0, date("m"), date("d"), date("Y")));
		
	$tasks['request']['tasks']['add']=array(
		array(
			'element_id'=>$contact_id,
			'element_type'=>1,
			'task_type'=>1,
			'text'=>$_type,
			'responsible_user_id'=>360038, // id профиля ответственного пользователя
			'complete_till'=>$date_task
		)
	);
	
	$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/tasks/set';
	
	$curl=curl_init();
	
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
	curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($tasks));
	curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	 
	$out=curl_exec($curl);
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	
	
	//-------------------------
	// сделка
	//-------------------------
	
	$leads['request']['leads']['add']=array(
		array(
			'name'=>'Тестовая сделка',
			'status_id'=>2747196,
			'price'=>399999,
			'responsible_user_id'=>360038,
			'tags' => 'TEST, FORMA'
		)
	);
	
	$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';
	
	$curl=curl_init();
	
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
	curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
	curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	 
	$out=curl_exec($curl);
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	
	//---------
	// примечание
	//---------
	
	$Response=json_decode($out,true);
	$Response=$Response['response']['leads']['add'];
	 
	foreach($Response as $v)
	  if(is_array($v))
		$lead_id = $v['id'];
	
	$notes['request']['notes']['add']=array(
		  #Привязываем к сделке
		  array(
			'element_id'=>$lead_id,
			'element_type'=>2,
			'note_type'=>4,
			'text'=>$_comm,
			'responsible_user_id'=>360038,
		  )
	);
	
	$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/notes/set';
	
	$curl=curl_init();
	
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
	curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($notes));
	curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	 
	$out=curl_exec($curl);
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
}

?>