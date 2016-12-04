class User < ActiveRecord::Base
  attr_accessible :dm, :email, :password, :roles, :username, :userid, :password_confirmation,:ban_id,:attend

  has_one :author
  has_many :reviews
  has_many :books, :through => :reviews

  validates :agreement, :acceptance => { :accept => 'yes' }
  validates :password, :confirmation => true

  # attr_protected :roles
  # attr_accessible :username, :password, :email, :dm


  # validates :agreement, :acceptance => { :on => :create }
  # validates :email, :presence => { :unless => 'dm.blank?' }
  # validates :email, :presence => { :if => '!dm.blank?' }

  # validates :email, :presence => { :unless => :sendmail? }

  # def sendmail?
  #   dm.blank?
  # end

  # validates :email,
  #   :presence => { :unless => Proc.new { |u| u.dm.blank? } }

  def self.authenticate(userid, password)
    where(:userid => userid,
      :password => Digest::SHA1.hexdigest(password)).first
  end




end
