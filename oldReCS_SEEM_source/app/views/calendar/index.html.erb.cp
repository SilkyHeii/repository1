<!-- Probably move the stylesheet to you layout. Also make sure you include the javascript. -->
<%= stylesheet_link_tag "event_calendar" %>
<%= notice if notice %>
<h1> Calendar </h1> 
<div align="right" style="font-size:15px;">
  <%= link_to '新規登録', new_user_path %>
  <%= button_to 'logout', {controller: 'login', action: 'logout'} %>
</div align="right">
<br>

<span>出席状況　　</span>
<span><FONT style="background:#90EE90">　　出席　　</FONT></span>
<span><FONT style="background:#FA8072">　欠席、未出席　</FONT></span>
<span><FONT style="background:#EEEE88">　　帰宅　　</FONT></span>

<br>


<div id="member_table"><br>
<table  border="1" cellpadding="5" class="dropshadow">
  <tr height="15px">
	<td class="midashi" align="right"><span class="midashi2">Grid : </span></td>
    <% @users.each do |user| %>
    <% if user.roles == "grid班" %>
    <% if user.attend == "1" %>
    <td  width="80" align="center" style="background:#EEEE88;border:none;"><%= user.username %></td>
    <% elsif user.attend == "2" %>
    <td  width="80" align="center" style="background:#90EE90;border:none"><%= user.username %></td>
    <% else  %>
    <td  width="80" align="center" style="background:#FA8072;border:none"><%= user.username %></td>
    <% end %>
    <% end %>
   <% end %>
  </tr>
</table>
<br class="space">

<table  border="1" cellpadding="5"  class="dropshadow">
  <tr>
	<td class="midashi" align="right"><span class="midashi2">Network: </span></td>
    <% @users.each do |user| %>
    <% if user.roles == "net班" %>
    <% if user.attend == "1" %>
    <td  width="80" align="center" style="background:#EEEE88;border:none;"><%= user.username %></td>
    <% elsif user.attend == "2" %>
    <td  width="80" align="center" style="background:#90EE90;border:none;"><%= user.username %></td>
    <% else  %>
    <td  width="80" align="center" style="background:#FA8072;border:none;"><%= user.username %></td>
    <% end %>
    <% end %>
   <% end %>
  </tr>
</table><br class="space">


<table  border="1" cellpadding="5"  class="dropshadow">
  <tr>
	<td class="midashi" align="right"><span class="midashi2">Web : </span></td>
    <% @users.each do |user|%>
    <% if user.roles == "web班" %>
    <% if user.attend == "1" %>
    <td  width="80" align="center" style="background:#EEEE88;border:none;"><%= user.username %></td>
    <% elsif user.attend == "2" %>
    <td  width="80" align="center" style="background:#90EE90;border:none;"><%= user.username %></td>
    <% else  %>
    <td  width="80" align="center" style="background:#FA8072;border:none;"><%= user.username %></td>
    <% end %>
    <% end %>
   <% end %>
  </tr>
</table>
</div>



<% if false %>
<!--  <tr>
    <% for i in 12..15 %>
    <td style="background:<%= @users[i].color2 %>"><%= @users[i].username %></td>
    <% end %>
  </tr>  -->

<!--<% @users.each do |user| %>

  <tr>
    <td><%= user.username %></td>
  </tr>

<% end %> -->
<% end %>
<!-- </table> -->
<br>





<%= raw(event_calendar) %>

<!-- <%= link_to 'New Event', new_event_path %> -->
