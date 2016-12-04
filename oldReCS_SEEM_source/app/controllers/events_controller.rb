# encoding: utf-8

class EventsController < ApplicationController
  # GET /events
  # GET /events.json
  before_filter :check_logined#, :only => ['index']

  def index
    @events = Event.all   #Eventは恐らくクラス（データベース

    #respond_toで形式を変えて出力
    respond_to do |format|          #フォーマット・・・型、体裁、書式
      format.html # index.html.erb
      format.json { render json: @events }
    end
  end

  # GET /events/1
  # GET /events/1.json
  def show
    @event = Event.find(params[:id])    #p290
    @events = Event.all
     # @hourevent = Event.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.json { render json: @event }
    end
  end

  # GET /events/new
  # GET /events/new.json
  def new
    @usr = User.find(session[:usr])   #自作部分
    @event = Event.new
    @event.name = @usr.username
  #自作部分
    @events = Event.all


      year = params[:year].to_i
      month = params[:month].to_i
      day = params[:day].to_i
      hour = 0    #Time.now.hour
      min = 0    #Time.now.min
    @time = Time.mktime(year,month,day,hour,min)
    #@event.comment = @usr.username + " : " +@time.strftime('%Y年%m月%d日 %H:%M:%S')
    #@event.comment = "#{@usr.username}\n#{@time.strftime('%Y年%m月%d日 %H:%M:%S')}"

    respond_to do |format| #p66
      format.html # new.html.erb
      format.json { render json: @event } #JavaScript Object Notation
    end
  end

  # GET /events/1/edit
  def edit
    @event = Event.find(params[:id])
  end

  # POST /events
  # POST /events.json
  def create
    @event = Event.new(params[:event])
    @event.group = @event.name       if @event.group == '個人'
    @event.event = @event.eventother if @event.event == 'その他'
    if @event.save
      if @event.mail == 'yes'
        #session[:event] = @event.id
        @usr = User.find(session[:usr])
        @event.eventother = @usr.username + ":" + @event.event
        if @event.start_at == @event.end_at
          if @event.start_at.hour == 0 && @event.start_at.min == 0
            @event.comment = "#{@event.start_at.strftime('%m月%d日')}\n#{@event.eventother}\n#{@event.comment}"
          else
            @event.comment = "#{@event.start_at.strftime('%m月%d日 %H:%M')}\n#{@event.eventother}\n#{@event.comment}"
          end
        else
          if @event.start_at.hour == 0 && @event.start_at.min == 0 && @event.end_at.hour == 0 && @event.end_at.min == 0
            @event.comment = "#{@event.start_at.strftime('%m月%d日')}～#{@event.end_at.strftime('%m月%d日')}\n#{@event.eventother}\n#{@event.comment}"
          else
            @event.comment = "#{@event.start_at.strftime('%m月%d日 %H:%M')}～#{@event.end_at.strftime('%m月%d日 %H:%M')}\n#{@event.eventother}\n#{@event.comment}"
        end
      end
        render create_mail_events_path
      else
        redirect_to calendar_path, notice: '予定は登録されました' #20140603
      end
    else
      render action: "new"
    end
  end

  def create_mail
    #@usr   = User.find(session[:usr])
    #@event = Event.find(session[:event])
    #@event.eventother = @usr.username + ":" + @event.event
    #@event.comment    = @event.group + ":" + @event.event + "\n" + @event.start_at.strftime('%Y年%m月%d日 %H:%M') + " ～　\n "+ @event.end_at.strftime('%Y年%m月%d日 %H:%M') + "\n\n" + @event.comment
  end

  # PUT /events/1
  # PUT /events/1.json


  def update
    @event = Event.find(params[:id])
    @event.attributes = params[:event]
    @event.group = @event.name       if @event.group == '個人'
    @event.event = @event.eventother if @event.event == 'その他'


    if @event.save
      if @event.mail == 'yes'
        #session[:event] = @event.id
        @usr = User.find(session[:usr])
        @event.eventother = @usr.username + ":" + @event.event
        if @event.start_at == @event.end_at
          if @event.start_at.hour == 0 && @event.start_at.min == 0
            @event.comment = "#{@event.start_at.strftime('%m月%d日')}\n#{@event.eventother}\n#{@event.comment}"
          else
            @event.comment = "#{@event.start_at.strftime('%m月%d日 %H:%M')}\n#{@event.eventother}\n#{@event.comment}"
          end
        else
          if @event.start_at.hour == 0 && @event.start_at.min == 0 && @event.end_at.hour == 0 && @event.end_at.min == 0
            @event.comment = "#{@event.start_at.strftime('%m月%d日')}～#{@event.end_at.strftime('%m月%d日')}\n#{@event.eventother}\n#{@event.comment}"
          else
            @event.comment = "#{@event.start_at.strftime('%m月%d日 %H:%M')}～#{@event.end_at.strftime('%m月%d日 %H:%M')}\n#{@event.eventother}\n#{@event.comment}"
        end
      end
        render create_mail_events_path
      else
        redirect_to calendar_path, notice: '予定は更新されました' #20140603
      end
    else
      render action: "new"
    end



#    respond_to do |format|
#      if @event.save   #@event.update_attributes(params[:event])
#        format.html { redirect_to @event, notice: '予定は正しく更新されました' }
#        format.json { head :no_content }
#      else
#        format.html { render action: "edit" }
#        format.json { render json: @event.errors, status: :unprocessable_entity }
#      end
#    end


  end

  def mail
    @usr = User.find(session[:usr])
    @event = Event.find(session[:event])


    @event.eventother = @usr.username + ":" + @event.event
    @event.comment = @event.group + ":" + @event.event + "\n" + @event.start_at.strftime('%Y年%m月%d日 %H:%M') + " ～　\n "+ @event.end_at.strftime('%Y年%m月%d日 %H:%M') + "\n\n" + @event.comment

    respond_to do |format|
      if @event.save
        format.html { redirect_to  :controller =>'extra',:action => 'sendmail' }
        format.json { render json: @event, status: :created, location: @event }


      else
        format.html { render action: "new" }
        format.json { render json: @event.errors, status: :unprocessable_entity }
      end
     end



  end


  # DELETE /events/1
  # DELETE /events/1.json
  def destroy
    @event = Event.find(params[:id])
    @event.destroy

    respond_to do |format|
      format.html { redirect_to calendar_path }
      format.json { head :no_content }
    end
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
