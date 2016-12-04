#coding: utf-8


class LoginController < ApplicationController

  def index
    @users = User.all                       #2014/02/25
    keystate= Keystate.all
    keystate.each do |ks|
	if ks.id==1
		if ks.state=="true"
			@ks="true"
		else
			@ks="false"
		end
	end	
    end
  end

  def auth
    usr = User.authenticate(params[:userid], params[:password])
    session[:deviceWidth]=params[:deviceWidth]
    if usr then
      session[:usr] = usr.id
      redirect_to calendar_path#params[:referer] 大塚さんに変更してもらったところ
    else
      flash.now[:referer] = params[:referer]
      @error = 'ユーザ名／パスワードが間違っています。'
      @users = User.all
      render 'index'
    end
  end
 

  def logout
    reset_session
    redirect_to root_path
  end




end
