<?php
	//# 헤더 호출
	require dirname(__FILE__).'/include/header.html';
	
	//# 처리
	switch($_GET['mode'])
	{
		// 로그인
		case 'LOGIN' :
			require dirname(__FILE__).'/template/login.php';
			break;

		// 방 목록
		case 'LIST' :
			require dirname(__FILE__).'/template/list.php';
			break;

		// 방 내부
		case 'DETAIL' :
			require dirname(__FILE__).'/template/detail.php';
			break;

		default :
			require dirname(__FILE__).'/template/login.php';
			break;
	}

	//# 풋터 호출
	require dirname(__FILE__).'/include/footer.html';
?>