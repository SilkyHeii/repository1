# Place all the behaviors and hooks related to the matching controller here.
# All this logic will automatically be available in application.js.
# You can use CoffeeScript in this file: http://jashkenas.github.com/coffee-script/
# Place all the behaviors and hooks related to the matching controller here.
# All this logic will automatically be available in application.js.
# You can use CoffeeScript in this file: http://coffeescript.org/

class @ChatClass
  constructor: (url, useWebsocket) ->
    # これがソケットのディスパッチャー
    window.i = 0
    window.j = 0
    window.resstate=0
    window.resdestmsgnamearray = new Array()
    window.resdestmsgbodyarray = new Array()
    window.resdest = new Array()
    window.usmessagenamearray = new Array()
    window.usmessagebodyarray = new Array()
    user_name = $('#username').val()
    msg_body = $('#msgbody').val()    

    #@dispatcher = new WebSocketRails(url)
    
    if @dispatcher == undefined
       @dispatcher = new WebSocketRails(url)
       @dispatcher = new WebSocketRails(url)
    console.log(url) 
    console.log(@dispatcher)

    @dispatcher.trigger 'connect_user',{ name: user_name , body: msg_body}
   
    # イベントを監視
    @bindEvents()


  bindEvents: () =>
    #会話タブにて会話を表示ボタンが押されたら以下を起動
    $('#talk').on 'click', @loadtalk
    # 送信ボタンが押されたらサーバへメッセージを送信
    $('#send').on 'click', @sendMessage
    #検索ボタンが押されたら検索用メソッドを起動
    $('#findbtn').on 'click', @findMessage
    #クリアボタンが押されたら検索結果を削除
    $('#findclear').on 'click', @clearfindMessage
    #了解ボタンが押されたらリターンメッセージを送る.統一性がない…だと？
    $('#personalchat').delegate "#understand",'click', @returnMessage
    $('#allchat').delegate "#understand",'click', @returnMessage
    #次の20件を追加ボタンが押されたら以下のメソッドを起動
    $('#loadbtn').on 'click', @loadMessage
    #返信ボタンが押されたら以下のメソッドを起動
    $('#allchat').delegate "#response",'click', @responseMessage
    $('#personalchat').delegate "#response",'click', @responseMessage
    # サーバーからnew_messageを受け取ったらreceiveMessageを実行
    @dispatcher.bind 'new_message', @receiveMessage
    #ログを追加したいときは以下のメソッドで受信
    @dispatcher.bind 'load_message', @prependloadMessages
    #
    #@dispatcher.bind 'load_talk', @displaytalk


  sendMessage: (event) =>
    #Notification(デスクトップ通知)の許可、既存技術な上にブラウザ依存とかきっもーい
    Notification.requestPermission()
    # サーバ側にnew_messageという名のイベントを送信、bindEventでキャッチする
    # オブジェクトでデータを指定
    #宛先チェックボックスの状態を一つ一つ変数に入れてます。
    user_name = $('#username').val()
   # alert "#{user_name}"
    msg_body = $('#msgbody').val()
   # alert "#{msg_body}"
    membercount = $('#membercount').val()
    mem = membercount
   # alert "#{membercount}"
    mflag = 0
    rflag = 0
    resflag = 0
    findflag=0
    sflag = new Array()
    #users = new Array()
    check=0
    j=0
    while j<mem
      sflag[j] = $("##{j}c").prop('checked')
     # alert "#{sflag[j]}"
      if $("##{j}c").prop('checked')==true
        check++
      j++


    Nowymdhms = new Date
    NowYear = Nowymdhms.getYear() + 1900
    NowMon = Nowymdhms.getMonth() + 1
    NowDay = Nowymdhms.getDate()
    NowWeek = Nowymdhms.getDay()
    NowHour = Nowymdhms.getHours()
    NowMin = Nowymdhms.getMinutes()
    NowSec = Nowymdhms.getSeconds()
    Week = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')
    Now = NowYear + '年' + NowMon + '月' + NowDay + '日(' + Week[NowWeek] + ') ' + NowHour + ':' + NowMin + ':' + NowSec
   
    if check!=0
      @dispatcher.trigger 'new_message', { name: user_name , body: msg_body, mflag: mflag , sflag: sflag , rflag: rflag,Now: Now,resflag: resflag,findflag: findflag}
      $("#msgbody").val('')
    else
      alert "宛先を指定してください"

  returnMessage:() =>
   # alert "#{window.i}#{window.onnum}:#{window.usmessagenamearray[window.onnum]}:#{window.usmessagebodyarray[window.onnum]} #{rtdest}"
    user_name = $('#username').val()
    msg_body = window.usmessagebodyarray[window.onnum]
    group_id =$('#group_id').val()
    membercount = $('#membercount').val()
    mem = membercount+2
    mflag = 0
    rflag = 1
    resflag = 0
    findflag = 
    sflag = new Array()
    j=0
    while j<mem
      sflag[j] = false
      j++

    rtdest = document.getElementsByName("#{window.usmessagenamearray[window.onnum]}")[0].value
   # alert "#{rtdest}"
    sflag[rtdest] = true

    Nowymdhms = new Date
    NowYear = Nowymdhms.getYear() + 1900
    NowMon = Nowymdhms.getMonth() + 1
    NowDay = Nowymdhms.getDate()
    NowWeek = Nowymdhms.getDay()
    NowHour = Nowymdhms.getHours()
    NowMin = Nowymdhms.getMinutes()
    NowSec = Nowymdhms.getSeconds()
    Week = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')
    Now = NowYear + '年' + NowMon + '月' + NowDay + '日(' + Week[NowWeek] + ') ' + NowHour + ':' + NowMin + ':' + NowSec


    @dispatcher.trigger 'new_message', { name: user_name , body: msg_body, group_id: group_id , mflag: mflag , sflag: sflag, rflag: rflag,Now: Now,resflag: resflag,findflag: findflag }


  responseMessage:() =>
    if window.resstate==0
     # alert "返信ボタンが押されました"
      $("#ret#{window.onnum}").append "<input type=text id=restext name=restext size=30 onKeydown=\"return submitStop(event,'ret#{window.onnum}')\" >"
      window.resstate=1
      window.resnum=window.onnum
    else if window.onnum != window.resnum
      $('input').remove('#restext')
      window.resstate=0
      window.resnum=null
    else if window.resstate == 1
      if $('#restext').val() != ""
        user_name=$('#username').val()
        destmsgbody = window.usmessagebodyarray[window.onnum]
        resmsgbody = $('#restext').val()
        msg_body = resmsgbody + "<br>>" + destmsgbody
        group_id =$('#group_id').val()
        membercount = $('#membercount').val()
        mem = membercount+2
        #destination =""
        #users=new Array()
        mflag = 0
        rflag = 1
        resflag = 1
        findflag=0
        sflag = new Array()
        j=0
        while j<mem
          sflag[j] = false
          j++
        #alert window.resdest[window.onnum]
        if window.resdest[window.onnum]=="All"
          #alert "All宛の返信ボタンが二度押されました"
          sflag[0] = true
        else if window.resdest[window.onnum]=="grid"
          sflag[1] = true
        else if window.resdest[window.onnum]=="net"
          sflag[2] = true
        else if window.resdest[window.onnum]=="web"
          sflag[3] = true
        else
          rtdest = document.getElementsByName("#{window.usmessagenamearray[window.onnum]}")[0].value
          sflag[rtdest] = true

        #j=0
        #while j<membercount
        #  users[j] = $("##{j}c").attr("name")
          #alert users[j]
        #  j++

        #k=0
        #while k<membercount
        #  if sflag[k]==true
        #    destination = destnation + users[k]
        #  k++





        Nowymdhms = new Date
        NowYear = Nowymdhms.getYear() + 1900
        NowMon = Nowymdhms.getMonth() + 1
        NowDay = Nowymdhms.getDate()
        NowWeek = Nowymdhms.getDay()
        NowHour = Nowymdhms.getHours()
        NowMin = Nowymdhms.getMinutes()
        NowSec = Nowymdhms.getSeconds()
        Week = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')
        Now = NowYear + '年' + NowMon + '月' + NowDay + '日(' + Week[NowWeek] + ') ' + NowHour + ':' + NowMin + ':' + NowSec
        @dispatcher.trigger 'new_message', { name: user_name , body: msg_body , mflag: mflag , sflag: sflag, rflag: rflag,Now: Now,resflag: resflag,findflag: findflag}
        $('input').remove('#restext')
        window.resstate=0
      else
        alert "返信欄が空欄です"
        $('input').remove('#restext')
        window.resstate=0



  loadtalk:() =>
    alert "会話を表示ボタンが押されました."
    user_name=$('#username').val()
    talkdest = new Array()
    membercount = $('#membercount').val()
    talkflag=true
    checknow=""
    j=0
    while j < membercount
      talkdest[j] = $("##{j}c").prop('checked')
      if talkdest[j]==true
        checknow=checknow + $("##{j}c").val()
      j++
    #alert checknow

    @dispatcher.trigger 'new_message' , {name: user_name,talkdest: talkdest,talkflag: talkflag,checknow: checknow}
  
  displaytalk:() =>
    alert "displaytalk is called"


  talkprepend = ->
    #alert "talkprepend"
    user_name=$('#username').val()
    talkdest = new Array()
    membercount = $('#membercount').val()
    j=0
    while j < membercount
      talkdest[j] = $("##{j}c").prop('checked')
      j++

    
  findMessage:()=>
    #alert "検索ボタンが押されました"
    if $("#sender").prop('checked')==true
      findoption="sender"
      #alert "ごめんなさい、送信者名検索は未実装です"
    else if $("#time").prop('checked')==true
      findoption="time"
      #alert "ごめんなさい、日時検索は未実装です"
    else if $("#content").prop('checked')==true
      findoption="content"
    else
      alert "検索条件が指定されていません"
      findoption=""
    findword=$('#findword').val()
    #alert findword
    findflag = 1
    $('div').remove('.foundMessages')
    if findoption !=""
      @dispatcher.trigger 'new_message', {findword: findword,findflag: findflag,findoption: findoption }


  clearfindMessage:()=>
   # alert "クリアボタンが押されました"
    $('div').remove('.foundMessages')


  loadMessage:()=>
    alert "次の20件をロードします"
    loadflag=true
    @dispatcher.trigger 'new_message', {loadflag: loadflag}


  prependloadMessages:(message)=>
    #alert "prependloadMessages"
    users = new Array()
    membercount = $('#membercount').val()
    mem = membercount + 2
    userid = $('#user_id').val()
    gid = $('#group_id').val()
    username = $('#username').val()
    mygroupname = $('#group_name').val()
    destname=""
    body = message.body
    message.body=body.replace(/((http|https|ftp):\/\/[\w?=&.\/-;#~%-]+(?![\w\s?&.\/;#~%"=-]*>))/g, '<a href="$1">$1</a> ')
    j=0  
    while j<mem 
      users[j] = $("##{j}c").attr("name")
      #alert users[j]
      j++  
    k = 4
    while k<mem
      if message.sflag[k]==true
         destname = destname + users[k]
      k++


    #プリペんどの仕方を条件分岐
    if message.name == username
      if message.sflag[0] ==true
        $('#allchat').append "<div class=mysend><div class=dest> #{message.name} : my send to all : #{message.Now}</div> #{message.body}</div>"
      else if message.sflag[1] ==true
        $('#allchat').append "<div class=mysend><div class=dest> #{message.name} : my send to grid: #{message.Now}</div> #{message.body}</div>"
      else if message.sflag[2] ==true
        $('#allchat').append "<div class=mysend><div class=dest> #{message.name} : my send to net: #{message.Now}</div> #{message.body}</div>"
      else if message.sflag[3] ==true
        $('#allchat').append "<div class=mysend><div class=dest> #{message.name} : my send to web: #{message.Now}</div> #{message.body}</div>"
      else
        if message.resflag == 1
          $('#allchat').append "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div> >#{message.body}</div>"
        else if message.rflag == 1
          $('#allchat').append "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div>了解しました<br> >#{message.body}</div>"
        else
          $('#allchat').append "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div> #{message.body}</div>"
    else if message.sflag[0] == true
      $('#allchat').append ' <div class=all><div class=dest> ' + message.name + " : to All : #{message.Now}</div>" + message.body + '</div>'
    else if message.sflag[gid] == true
      $('#allchat').append "<div class=#{mygroupname}><div class=dest> #{message.name} : to #{mygroupname}: #{message.Now} </div> #{message.body} </div>"
    else if message.sflag[userid] == true
      if message.resflag == 1
        $('#allchat').append "<div class=tome><div class=dest> #{message.name} : to me return from #{message.name} : #{message.Now} </div> >#{message.body}</div>"
        window.usmessagenamearray[window.i] = message.name
        window.usmessagebodyarray[window.i] = message.body
      else if message.rflag == 1
        $('#allchat').append "<div class=tome><div class=dest> #{message.name} : to me return from #{message.name} : #{message.Now} </div>了解しました <br> >#{message.body}</div>"
        window.usmessagenamearray[window.i] = message.name
        window.usmessagebodyarray[window.i] = message.body
      else
        $('#allchat').append "<div class=tome><div class=dest> #{message.name} : to me : #{message.Now}</div>#{message.body}<div class=returnmessage ><form id=ret#{window.i} name=ret><input type=button id=understand name=understand value=了解 style=background-color:#cce3ff; onClick=setvalue(#{window.i},this)><input type = button id=response name=response value=返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this) ><input type=hidden id=onbtn name=onbtn ></form></div></div>" 
        window.usmessagenamearray[window.i] = message.name
        window.usmessagebodyarray[window.i] = message.body
      window.i +=1



    #$('#loadMessage').append "<div class=foundMessages><div class=dest> #{message.name} : #{message.Now}</div> #{message.body}</div>"  





  receiveMessage: (message) =>

    #kaiwatabunimessagewohuka
    talkprepend()
    # 受け取ったデータをprepend
    #各種変数に値をHTMLより取得したものを代入
    users = new Array()
    membercount = $('#membercount').val()
    mem = membercount + 2
    userid = $('#user_id').val()
    gid = $('#group_id').val()
    username = $('#username').val()
    mygroupname = $('#group_name').val()
    destname=""
    body = message.body
    #エスケープ処理（力技）
    if message.rflag == 0
      body = body.replace(/["&'<>]/g, (ch) ->
         {
           '"': '&quot;'
           '&': '&amp;'
           '\'': '&#39;'
           '<': '&lt;'
           '>': '&gt;'
           }[ch]
        )
    j=0
    while j<mem 
      users[j] = $("##{j}c").attr("name")
      #alert users[j]
      j++ 

    
    k = 4
    while k<mem
      if message.sflag[k]==true
         destname = destname + users[k]
      k++

    #URLリンク　チ・カ・ラ・ワ・ザ
    message.body=body.replace(/((http|https|ftp):\/\/[\w?=&.\/-;#~%-]+(?![\w\s?&.\/;#~%"=-]*>))/g, '<a href="$1">$1</a> ')

    
    #プリペんどの仕方を条件分岐
    if message.findflag == 1
      $('#foundMessages').prepend "<div class=foundMessages><div class=dest> #{message.name} : #{message.Now}</div> #{message.body}</div>"
    else if message.name == username
      if message.sflag[0] ==true
        $('#allchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to all : #{message.Now}</div> #{message.body}</div>"
      else if message.sflag[1] ==true
        $('#allchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to grid: #{message.Now}</div> #{message.body}</div>"
      else if message.sflag[2] ==true
        $('#allchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to net: #{message.Now}</div> #{message.body}</div>"
      else if message.sflag[3] ==true
        $('#allchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to web: #{message.Now}</div> #{message.body}</div>"
      else
        if message.resflag == 1
          $('#personalchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div> #{message.body}</div>"
          $('#allchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div> #{message.body}</div>"
        else if message.rflag == 1
          $('#personalchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div>了解しました<br> >#{message.body}</div>"
          $('#allchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div>了解しました<br> >#{message.body}</div>"
        else
          $('#personalchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div> #{message.body}</div>"
          $('#allchat').prepend "<div class=mysend><div class=dest> #{message.name} : my send to #{destname}: #{message.Now}</div> #{message.body}</div>"

      #デスクトップ通知を行う
      #if message.mflag == 1
        #notification = new Notification "#{message.name} : #{message.body}"
        #setTimeout (->
          #notification.close()
          #return
          #), 2500

    else if message.sflag[0] == true
      $('#allchat').prepend ' <div class=all><div class=dest> ' + message.name + " : to All : #{message.Now}</div>" + message.body + "<br><form id=ret#{window.i}><input type = button id=response value=全体へ返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this) ><input type=hidden id=onbtn name=onbtn ></form></div>"
      window.usmessagenamearray[window.i] = message.name
      window.usmessagebodyarray[window.i] = message.body
      window.resdest[window.i]="All"
      #デスクトップ通知を行う
      if  message.mflag == 1
        notification = new Notification "#{message.name} : #{message.body}"
        #setTimeout (->
          #notification.close()
          #return
          #), 2500
      window.i += 1
    else if message.sflag[gid] == true
      $('#allchat').prepend "<div class=#{mygroupname}><div class=dest> #{message.name} : to #{mygroupname}: #{message.Now} </div> #{message.body} <br><form id=ret#{window.i}><input type = button id=response value=#{mygroupname}班へ返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this) ><input type=hidden id=onbtn name=onbtn ></form></div>"
      window.usmessagenamearray[window.i] = message.name
      window.usmessagebodyarray[window.i] = message.body
      window.resdest[window.i]=mygroupname
      #デスクトップ通知を行う
      if  message.mflag == 1
        notification = new Notification "#{message.name} : #{message.body}"
        #setTimeout (->
         # notification.close()
         # return
         # ), 2500
      window.i += 1
    else if message.sflag[userid] == true
      if message.resflag == 1
        $('#allchat').prepend "<div class=tome><div class=dest> #{message.name} : to me return from #{message.name} : #{message.Now} </div> #{message.body}<div class=returnmessage ><form id=ret#{window.i} name=ret><input type=button id=understand name=understand value=了解 style=background-color:#cce3ff; onClick=setvalue(#{window.i},this)><input type = button id=response name=response value=返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this) ><input type=hidden id=onbtn name=onbtn ></form></div></div>"
        window.usmessagenamearray[window.i] = message.name
        window.usmessagebodyarray[window.i] = message.body

        $('#personalchat').prepend "<div class=tome><div class=dest> #{message.name} : to me return from #{message.name} : #{message.Now} </div> #{message.body}<div class=returnmessage ><form id=ret#{window.i} name=ret><input type=button id=understand name=understand value=了解 style=background-color:#cce3ff; onClick=setvalue(#{window.i},this)><input type = button id=response name=response value=返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this) ><input type=hidden id=onbtn name=onbtn ></form></div></div>"
      else if message.rflag == 1
       $('#allchat').prepend "<div class=tome><div class=dest> #{message.name} : to me return from #{message.name} : #{message.Now} </div>了解しました<br> >#{message.body}<div class=returnmessage ><form id=ret#{window.i} name=ret><input type=button id=understand name=understand value=了解 style=background-color:#cce3ff; onClick=setvalue(#{window.i},this)><input type = button id=response name=response value=返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this) ><input type=hidden id=onbtn name=onbtn ></form></div></div>"
       window.usmessagenamearray[window.i] = message.name
       window.usmessagebodyarray[window.i] = message.body
        
       $('#personalchat').prepend "<div class=tome><div class=dest> #{message.name} : to me return from #{message.name} : #{message.Now} </div>了解しました<br> >#{message.body}<div class=returnmessage ><form id=ret#{window.i} name=ret><input type=button id=understand name=understand value=了解 style=background-color:#cce3ff; onClick=setvalue(#{window.i},this)><input type = button id=response name=response value=返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this) ><input type=hidden id=onbtn name=onbtn ></form></div></div>"    


      else
        $('#allchat').prepend "<div class=tome><div class=dest> #{message.name} : to me : #{message.Now}</div>#{message.body}<div class=returnmessage ><form id=ret#{window.i} name=ret><input type=button id=understand name=understand value=了解 style=background-color:#cce3ff; onClick=setvalue(#{window.i},this)><input type = button id=response name=response value=返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this) ><input type=hidden id=onbtn name=onbtn ></form></div></div>"
        window.usmessagenamearray[window.i] = message.name
        window.usmessagebodyarray[window.i] = message.body

        $('#personalchat').prepend "<div class=tome><div class=dest> #{message.name} : to me : #{message.Now}</div> #{message.body}<div class=returnmessage ><form id=ret#{window.i} name=ret><input type=button id=understand name=understand value=了解 style=background-color:#cce3ff;  onClick=setvalue(#{window.i},this)><input type = button id=response name=response value=返信 style=background-color:#f4deee; onClick=setvalue(#{window.i},this)><input type=hidden id=onbtn name=onbtn ></form></div></div>"
     
      window.i +=1
      #デスクトップ通知を行う
      if  message.mflag == 1
        notification = new Notification "#{message.name} : #{message.body}"
        #setTimeout (->
          #notification.close()
          #return
          #), 2500



$ ->
  window.chatClass=new ChatClass($('#allchat').data('uri'),true)
