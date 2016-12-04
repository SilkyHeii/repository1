class Inquiry
  include ActiveModel::Conversion
  include ActiveModel::Validations
  extend ActiveModel::Naming
  extend ActiveModel::Translation
 
  attr_accessor :name
  attr_accessor :email
  attr_accessor :message
 
  validates :name,     :presence => true
  validates :email,    :presence => true,
                       :confirmation => true,
                       :format => { :with => /^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i }
  validates :message,  :presence => true
 
  def persisted?; false; end
 
  def initialize(attributes = {})
    self.attributes = attributes
  end
 
  def attributes=(attributes = {})
    if attributes
      attributes.each do |name, value|
        send "#{name}=", value
      end
    end
  end
 
  def attributes
    Hash[instance_variable_names.map{|v| [v[1..-1], instance_variable_get(v)]}]
  end
end
