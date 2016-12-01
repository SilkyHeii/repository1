
function GetMappedCookie(cookieStr){
	var delim=";";
	var cookies=cookieStr.split(delim);
	var result ={};
	for(it=0;it<cookies.length;it++){
		var temp=cookies[it].split("=");
		var key=temp[0];
		var value=temp[1];
		result[key]=value;
	}
	return result;
}
