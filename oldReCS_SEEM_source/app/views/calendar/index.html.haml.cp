= notice if notice
%h1 Calendar
%div{align: "right", style: "font-size:15px;"}
  = link_to 'ユーザー登録', new_user_path
%br
%div{align: "right", style: "font-size:15px;"}
  = button_to 'logout', {controller: 'login', action: 'logout'}
  = button_to '出席・退席', {action: 'attend'}
%br
%span 出席状況
%span
  %FONT{style: "background:#90EE90"} 　　出席　　
%span
  %FONT{style: "background:#FA8072"} 　欠席、未出席　
%span
  %FONT{style: "background:#EEEE88"} 　　一時退席　　
%br
#member_table
  %br
  %table.dropshadow{border: "1", cellpadding: "5"}
    %tr{height: "15px"}
      %td.midashi{align: "right"}
        %span.midashi2 Grid:
      - @users.each do |user|
        - if user.roles == "grid班"
          - if user.attend.to_i  == 0
            %td{width: "80", align: "center", style: "background:#FA8072;border:none;"}= user.username
          - elsif user.attend.to_i % 2 == 1
            %td{width: "80", align: "center", style: "background:#90EE90;border:none;"}= user.username
          - else
            %td{width: "80", align: "center", style: "background:#EEEE88;border:none;"}= user.username
  %br.space
  %table.dropshadow{border: "1", cellpadding: "5"}
    %tr{height: "15px"}
      %td.midashi{align: "right"}
        %span.midashi2 Net:
      - @users.each do |user|
        - if user.roles == "net班"
          - if user.attend.to_i == 0
            %td{width: "80", align: "center", style: "background:#FA8072;border:none;"}= user.username
          - elsif user.attend.to_i % 2 == 1
            %td{width: "80", align: "center", style: "background:#90EE90;border:none;"}= user.username
          - else
            %td{width: "80", align: "center", style: "background:#EEEE88;border:none;"}= user.username
  %br.space
  %table.dropshadow{border: "1", cellpadding: "5"}
    %tr{height: "15px"}
      %td.midashi{align: "right"}
        %span.midashi2 Web:
      - @users.each do |user|
        - if user.roles == "web班"
          - if user.attend.to_i == 0
            %td{width: "80", align: "center", style: "background:#FA8072;border:none;"}= user.username
          - elsif user.attend.to_i % 2 == 1
            %td{width: "80", align: "center", style: "background:#90EE90;border:none;"}= user.username
          - else
            %td{width: "80", align: "center", style: "background:#EEEE88;border:none;"}= user.username
  %br.space
%br
= raw(event_calendar)
