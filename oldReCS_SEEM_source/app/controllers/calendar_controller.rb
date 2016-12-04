#coding: utf-8
require 'redis'
require 'json'

class CalendarController < ApplicationController

  before_filter :check_logined, :only => ['index','chat']
  
  @@iframecheck1=0
  @@iframecheck2=0

  @@redis = Redis.new(:host => "127.0.0.1" , :port => 6379)

  def index
    @month = (params[:month] || (Time.zone || Time).now.month).to_i
    @year = (params[:year] || (Time.zone || Time).now.year).to_i

    @shown_month = Date.civil(@year, @month)

    @event_strips = Event.event_strips_for_month(@shown_month)

    @users = User.all

    @Obj = {'name' => 'siruki'}

    @@iframecheck1 = 1

    @deviceWidth=session[:deviceWidth]
   
    keystate= Keystate.all
      keystate.each do |ks|
        if ks.id==1
          if ks.state=="true"
            @ks=1
          else
            @ks=0
          end
        end
      end
    
    if @deviceWidth.to_i > 650
      respond_to do |format|
        format.html # index.html.erb
        format.json { render json: @Obj }
      end
    else
      redirect_to :action=>"chat"
    end

  end

  #チャットに関することをここで管理
  def chat
    @data = User.new
    #ユーザ情報を取ってくる
    @usr = User.find(session[:usr])
    @users = User.all
    session[:user_id] = @usr.id
    session[:username] = @usr.username
    if @usr.roles == "grid班"
      session[:gn] = "grid"
      session[:gid] = 1
    elsif @usr.roles == "web班"
      session[:gn] = "web"
      session[:gid] = 3
    elsif @usr.roles =="net班"
      session[:gn] = "net"
      session[:gid] = 2
    end
    @iframecheck=0
    @@iframecheck2=1

    if @@iframecheck1==1 && @@iframecheck2==1
      @iframecheck=1
    end

    #宛先選択欄を作るためにデータを何とかする
    @member = Array.new
    @member[0] = "all"
    @member[1] = "grid"
    @member[2] = "net"
    @member[3] = "web"
    @num=3
    #メンバーの名前を取得
    i=4
    @users.each do |mem|
      @member[i]=mem.username
      i+=1
      @num += 1
    end
    


    @members = Array.new
    @members[0] = "all"
    @members[1] = "grid"
    @members[2] = "net"
    @members[3] = "web"
    @members[4] = "榎原"
    @members[5] = "松﨑"
    @members[6] = "于"
    i=7
    @users.each do |men|
 	if men.roles == "grid班"
 		@members[i] = men.username
 		i+=1
 	end
    end
    @users.each do |dou|
    	if dou.roles == "net班"
 		@members[i] = dou.username
 		i+=1
 	end	
    end
    @users.each do |kusee|
 	if kusee.roles == "web班"
 		@members[i] = kusee.username
 		i+=1
 	end
    end
    session[:users] = @members

    @@members=@members


    @tweetbody = Array.new
    @tweet = Array.new

    i=0
    endnum = @@redis.llen @usr.username
    @talks = @@redis.lrange @usr.username ,0 ,endnum
    
    @talks.each do |message|
	msg = ActiveSupport::HashWithIndifferentAccess.new(eval(message))
	if msg[:name] == @usr.username 	
		#@tweetbody[i] = msg[:body]
		@tweet[i] = msg
		i+=1
	end
	#@tweetsbody = @tweetbody.reverse
	@tweets = @tweet.reverse
    end

    #チャット画面を描画
    render 'calendar/chat'
  end


  def attend
    @usr = User.find(session[:usr])
    #@usr.attend = 0 if @usr.attend.blank?
    @usr.attend = @usr.attend.to_i + 1
    @usr.save
    client= Mysql2::Client.new(host: "localhost",username: 'syuu',password: 'shiang',database: 'mydb')
    id = @usr.ban_id
    attend = @usr.attend
    client.query("update ban set state=#{attend},total=total+1,access='web' where id=#{'"'+id+'"'}")
    client.query("select number,state, total from ban where id=#{'"'+id+'"'}").each do |elem|
      @n = elem["number"]
      @s = elem["state"]
      @t = elem["total"]
    end

logger.error("\n")
logger.error("##################################################")
logger.error(session[:usr])
logger.error("##################################################")
logger.error("\n")

    client.query("insert into tag_log (number,state,total,access) values (#{@n},#{@s},#{@t},'web')")
    redirect_to calendar_path, notice: '更新されました'
  end

  def removetweet
	@usr = User.find(session[:usr])
	@users = User.all
	session[:user_id] = @usr.id
	session[:username] = @usr.username
	username = @usr.username
	data=eval(params[:rmmsgbody])
	dest=data["sflag"]
	i=0
	already=false
	destnum=0
	dest.each do |sflag|
		if sflag == true
			if i==0
				@@members.each do |mem|	#すべての人間のDBから対象のメッセージを消去
					len = @@redis.llen mem
					@@redis.lrem mem,-len,data
				end
				already=true
			elsif i==1	#grid
				@users.each do |mem|
					if mem.roles=="grid班"
						len = @@redis.llen mem.username
						@@redis.lrem mem.username,-len,data
					end
				end
				if @usr.roles=="grid班"
					already=true
				end
			elsif i==2	#net
				 @users.each do |mem|
                                        if mem.roles=="net班"
                                                len = @@redis.llen mem.username
                                                @@redis.lrem mem.username,-len,data
                                        end
                                end
				if @usr.roles=="net班"
					already=true
				end
			elsif i==3	#web
				 @users.each do |mem|
                                        if mem.roles=="web班"
                                                len = @@redis.llen mem.username
                                                @@redis.lrem mem.username,-len,data
                                        end
                                end
				if @usr.roles="web班"
					already=true
				end
			elsif i >3
				len = @@redis.llen @@members[i]
				@@redis.lrem @@members[i],-len ,data
			end
		end
		i+=1
	end
	
	
	if already!=true
		len = @@redis.llen @usr.username
		@@redis.lrem username, -len ,data
	end
	#render :text => @@members[destnum] 
	redirect_to :controller => 'calendar' , :action => 'chat'
  end

  private
  def check_logined
    if session[:usr] then
      begin
        @usr = User.find(session[:usr])
      rescue ActiveRecord::RecordNotFound
        reset_session
      end
    end
    unless @usr
      flash[:referer] = request.fullpath
      redirect_to :controller => 'login', :action => 'index'
    end
  end




end
