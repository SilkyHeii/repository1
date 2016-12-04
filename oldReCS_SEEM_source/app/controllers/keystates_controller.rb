require 'mysql2'

class KeystatesController < ApplicationController
  def show
	client = Mysql2::Client.new(host: 'localhost' , username: 'calendar',password: 'yqbb9cv3' ,database: 'calendar_production')
	raspID=params[:did]
	raspPass=params[:dpass]
	
	if raspID=="a" && raspPass=="a" then
		keystate=Keystate.all
		keystate.each do |ks|
			if ks.id==1
				#ks.state=params[:keystate]
				client.query("update keystate set state='#{params[:keystate]}' where id=1")
			end
		end
	end
	render :text=>"id = #{params[:keystate]}"
  end
end
