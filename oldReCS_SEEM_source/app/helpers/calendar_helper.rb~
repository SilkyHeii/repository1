# encoding: utf-8

module CalendarHelper
  def month_link(month_date)
    link_to(I18n.localize(month_date, :format => "%B"), {:month => month_date.month, :year => month_date.year})
  end
  
  # custom options for this calendar
  def event_calendar_opts
    { 
      :year => @year,
      :month => @month,
     # :width => 900,
      :link_to_day_action => "../events/new",
      :event_strips => @event_strips,
   #   :month_name_text => I18n.localize(@shown_month, :format => :yyyymm),
      :month_name_text => I18n.localize(@shown_month, :format => "%B %Y"),
      :previous_month_text => "<< " + month_link(@shown_month.prev_month),
      :next_month_text => month_link(@shown_month.next_month) + " >>"    }

  end

def day_link(text, date, day_action)
    #link_to(text,new_event_path)
    link_to(text, params.merge(:action => day_action, :year => date.year, :month => date.month, :day => date.day), :class => 'ec-day-link')
end

  def event_calendar
    # args is an argument hash containing :event, :day, and :options


    calendar event_calendar_opts do |args|
      event = args[:event]
unless event.start_at.strftime('%H:%M') == "00:00"
   
      %(<a href="/events/#{event.id}" title="時間：#{h(event.start_at.strftime('%H:%M'))}~#{h(event.end_at.strftime('%H:%M'))}\n#{h(event.group)}：#{h(event.event)}\n詳細：#{h(event.comment)}">#{h(event.start_at.strftime('%H:%M'))}:#{h(event.group)}:#{h(event.event)} </a>)
   else
      %(<a href="/events/#{event.id}" title="時間：#{h(event.start_at.strftime('%H:%M'))}~#{h(event.end_at.strftime('%H:%M'))}\n#{h(event.group)}：#{h(event.event)}\n詳細：#{h(event.comment)}">#{h(event.group)}:#{h(event.event)} </a>)
   end


 
   

    end
  end
end
