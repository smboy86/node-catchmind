# node-catchmind

출처 : https://www.phpschool.com/gnuboard4/bbs/board.php?bo_table=download&wr_id=17533&page=1


지원 OS : 상관없음 

개발환경 : Node.js, Javascript 

사용제한 : 자유롭게 쓰세요 

자료설명 : 

[구조] 
Node.js 
  ㄴcanvas.js: 캐치마인드 Node.js 

Source 
  ㄴinclude 
    ㄴfooter.html: 풋터 
    ㄴheader.html: 헤더 

  ㄴtemplate: 템플릿 
    ㄴdetail.php: 방 
    ㄴlist.php: 방 리스트 
    ㄴlogin.php: 로그인 

  ㄴ.htaccess : rewritre mode 
  ㄴindex.php: 페이지 라우팅 

Etc 
  ㄴ javascript: 자바스크립트 라이브러리 
  ㄴ stylesheet: 스타일시트 


[사용방법] 
1. Node.js/canvas.js 맨 하단의 서버 포트는 쓰시던 걸로 바꿔주세요. 
2. Etc/javascript, stylesheet 는 적당한 곳에 옮겨서 쓰세요. 
3. Source/include/header.html에서 호출하는 파일들의 경로 및 포트를 확인해주세요. 
4. Source/include/header.html에서 socket.io 연결관련 인자값을 확인해주세요. 


닉네임 입력, 방선택, 채팅, 문제출제, 정답을 맞추면 방장이 변경됨 등의 기능까지 만들었습니다. 
부족하지만 많이 써봐주세요 ㅎ
