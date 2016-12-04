class Keystate < ActiveRecord::Base
  	self.table_name = "keystate"
	attr_accessible :id, :state
end
