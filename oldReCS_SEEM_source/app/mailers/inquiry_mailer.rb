class InquiryMailer < ActionMailer::Base
  default from: "schedule@al.kansai-u.ac.jp"   # 送信元アドレス
  default to: "k578104@kansai-u.ac.jp"     # 送信先アドレス
 
  def received_email(inquiry)
    @inquiry = inquiry
    mail(:subject => 'お問い合わせを承りました')
  end
 
end
