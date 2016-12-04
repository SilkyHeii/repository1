#coding: utf-8
require 'redis'
require 'json'

class NochatcalendarController < ApplicationController
  before_filter :check_logined, :only => ['index']
  @@iframecheck1=0
  @@iframecheck2=0
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
    respond_to do |format|
      format.html # index.html.erb
      format.json { render json: @Obj }
    end
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
    client.query("insert into tag_log (number,state,total,access) values (#{@n},#{@s},#{@t},'web')")
    redirect_to :action=>"index", notice: '更新されました'
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
