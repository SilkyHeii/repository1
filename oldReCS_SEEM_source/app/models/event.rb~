# encoding: utf-8

class Event < ActiveRecord::Base

  has_event_calendar
  attr_accessible :end_at, :name, :start_at, :group, :event, :comment, :attend, :color2, :eventother, :mail

  def color 
 
  if group == 'Web班'
      self[:color] ||  '#ddFFFF'

  elsif group == 'Net班'
      self[:color] ||  '#EEEE88'

  elsif group == 'Grid班'
      self[:color] ||  '#90EE90'

  elsif event == '遅刻'
      self[:color] ||  '#FFB066'

  elsif event == '欠席'
      self[:color] ||  '#FA8072'

  else
      self[:color] ||  '#F0A0F0'
  end
  


    #セルの色変更はここ。p227
  end

  validates :group,
    :presence => true
  

end
