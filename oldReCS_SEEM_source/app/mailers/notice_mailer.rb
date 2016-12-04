#coding: utf-8

class NoticeMailer < ActionMailer::Base
  default from: 'schedule@al.kansai-u.ac.jp'

  # Subject can be set in your I18n file at config/locales/en.yml
  # with the following lookup:
  #
  #   en.notice_mailer.sendmail_confirm.subject
  #
  def sendmail_confirm(user, event)
    @user=user
    @event=event
    if event[:group] == 'Web班'
      mail(to: "web@ml.al.kansai-u.ac.jp", subject: "#{event[:name]}:#{event[:event]}")
    elsif event[:group] == 'Grid班'
      mail(to: "grid@ml.al.kansai-u.ac.jp", subject: "#{event[:name]}:#{event[:event]}")
    elsif event[:group] == 'Net班'
      mail(to: "network@ml.al.kansai-u.ac.jp", subject: "#{event[:name]}:#{event[:event]}")
    else
      mail(to: "Al-Lab@cm.kansai-u.ac.jp", subject: "#{event[:name]}:#{event[:event]}")
    end
  end
end
