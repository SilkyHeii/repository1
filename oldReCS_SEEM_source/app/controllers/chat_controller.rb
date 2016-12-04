# encoding: utf-8
require 'redis'
require 'json'


class ChatController < WebsocketRails::BaseController
    def initialize_session
      puts "session initialize\n" 
      @redis = Redis.new(:host => "127.0.0.1" , :port => 6379)
      controller_store[:redis] = @redis
    end

    #websocketのコネクションがはられたときに呼ばれるメソッド。DB名が個人名のものを読み込んで表示している＝再読み込み時履歴表示処理
    def connect_user
      puts"connected userrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr"
      gid = session[:username]
      @@loadtail = -120
      @@loadhead = -101
      puts gid
      talks = controller_store[:redis].lrange gid,-100,-1
      talks.each do |message|
        msg = ActiveSupport::HashWithIndifferentAccess.new(eval(message))
        send_message :new_message, msg
      end
    
    @@userslist = Array.new
    @@userslist = session[:users]
    puts @@userslist
    end

    #HTMLから投げられたフォームがJSを介してここに投げられる,ここでサーバにメッセージを送る.きったねぇソースだ、くそったれぇ！
    def new_message
      puts "1"
      #変数定義やら
      message[:mflag] = 0
      users = User.all
      loggid = session[:username] #自分の名前のDBに送信したデータを保存する
      permission = 0
      already=0
      puts "11"
      loadmessage = Array.new
      m=0

      #
      #if message[:talkflag]==true
	#loadtalkmessage(message)
      #end



      #ログロードなら以下で処理
      if message[:loadflag]==true
        talks = controller_store[:redis].lrange loggid,@@loadtail,@@loadhead
        talks.each do |message|
          msg = ActiveSupport::HashWithIndifferentAccess.new(eval(message))
          loadmessage[m] = msg
          m += 1
        end
       #loadmessage.reverse
        loadmessage.reverse_each do |message|
          send_message :load_message, message
        end
        @@loadtail -= 20
        @@loadhead -= 20
      end


      #検索かどうかを判断処理を分別(過去1億件の中から検索)
      if message[:findflag]==1
        findword = message[:findword]
        puts findword
        talks = controller_store[:redis].lrange loggid,-100000000,-1
        if message[:findoption]=="content"
          talks.each do |log|
            msg = ActiveSupport::HashWithIndifferentAccess.new(eval(log))
            puts "ログは↓"
            puts log
            puts "msgは↓"
            puts msg
            if msg[:body].index(findword) != nil
              msg[:findflag]=1
              send_message :new_message,msg
            end
          end
        elsif message[:findoption]=="sender"
          puts "sender"
          talks.each do |log|
            msg = ActiveSupport::HashWithIndifferentAccess.new(eval(log))
            puts "ログは↓"
            puts log
            puts "msgは↓"
            puts msg
            if msg[:name].index(findword) != nil
              msg[:findflag]=1
              send_message :new_message,msg
            end
          end
        elsif message[:findoption]=="time"
          puts "time"
          talks.each do |log|
            msg = ActiveSupport::HashWithIndifferentAccess.new(eval(log))
            puts "ログは↓"
            puts log
            puts "msgは↓"
            puts msg
            if msg[:Now].index(findword) != nil
              msg[:findflag]=1
              send_message :new_message,msg
            end
          end
        end
      end

      #宛先にチェックがあるか確認
      message[:sflag].each do |sflag|
        if sflag == true || sflag == "true"
          permission=1
        end
      end
      puts "destination checked"
      k=0
     #了解文および返信文かを識別からの処理
      if message[:rflag] == 1
        message[:sflag].each do |sflag|
          if sflag == true || sflag == "true"
            break
	  end
	  k += 1
        end
	puts k
        count=0
        if k==0
          users.each do |mem|
            controller_store[:redis].rpush mem.username,message  #ログを保存
          end
        elsif k==1
          users.each do |mem|
            if mem.roles == "grid班"
              controller_store[:redis].rpush mem.username,message  #ログを保存
            end
          end
        elsif k==2
          users.each do |mem|
            if mem.roles == "net班"
              controller_store[:redis].rpush mem.username,message  #ログを保存
            end
          end
        elsif k==3
          users.each do |mem|
            if mem.roles == "web班"
              controller_store[:redis].rpush mem.username,message  #ログを保存
            end
          end
        else
	  @@userslist.each do |mem|
            if count == k
              controller_store[:redis].rpush mem,message  #受信者側のDBにログを保存
            end
            count +=1
          end
        end
        controller_store[:redis].rpush loggid,message	#送信者のログDBに保存
        message[:mflag] = 1
        broadcast_message :new_message, message
      end
     
      
      puts permission



      puts "3"
      #空文かつ宛先無しかつ了解及び返信文は送れないようにする
	if message[:body] !="" && permission == 1 && message[:rflag]==0
		puts "send message: destination select"
		#保存に関する処理を以下に書く
		i = 0
		#each文で配列の中身を確認
		message[:sflag].each do |sflag|   
			if  i==0  #toall全員がログを保存 to all
				if sflag == true || sflag == "true"
					puts "truedesuyo-----"
					users.each do |mem|
						puts "store sitemasuyo-----"
						controller_store[:redis].rpush mem.username,message  #ログを保存
					end
					already=1
				end
			elsif i== 1 #grid
				if sflag == true || sflag == "true"
					users.each do |mem|
						if mem.roles == "grid班"
							controller_store[:redis].rpush mem.username,message  #ログを保存
						end
					end
					if session[:gid] ==1  #自分がgrid班だったら重複を避ける
						already=1
					end
				end
			elsif i==3  #web
				if sflag == true || sflag == "true"
					users.each do |mem|
						if mem.roles == "web班"
							controller_store[:redis].rpush mem.username,message  #ログを保存
						end
					end
					if session[:gid] ==3#自分がweb班だったら重複を避ける
						already=1
					end
				end
			elsif i==2  #net
				if sflag == true || sflag == "true"
					users.each do |mem|
						if mem.roles == "net班"
							controller_store[:redis].rpush mem.username,message  #ログを保存
						end
					end
					if session[:gid] == 2#自分がnet班だったら重複を避ける
						already=1
					end
				end
			elsif sflag == true || sflag == "true"  #個人宛をその人のログDBに保存
				savemember = @@userslist[i]
				puts "指定された宛先のidは↓"
				puts i
				puts "登録される宛先の名前は↓"
				puts savemember
				controller_store[:redis].rpush savemember,message  #ログを保存
				#if session[:user_id] == i #宛先が自分個人宛てだったら重複保存を避ける
				#	puts ""
				#	already = 1
				#end
			end
		i += 1
		end
		if already == 0
			puts "送信者のDBにログを保存しています"
			puts "保存される人は↓"
			puts loggid
			controller_store[:redis].rpush loggid,message
		end
		puts "4"
		message[:mflag] = 1
		broadcast_message :new_message, message
		end
	end



	#def loadtalkmessage
	#	talk =""
	#	send_message :load_talk,talk
	#end



end
