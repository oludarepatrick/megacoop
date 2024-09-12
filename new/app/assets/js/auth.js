function auth_start() {
	$("#login").hide();
	$("#auth").show();
	let identity = $("#identity").val();
	let action = $("#action").val();
	$.ajax({
		type: "get",
		url: base_url + "auth/ajax_member_coops",
		data: { identity: identity, action: action },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status == "success") {
					$("#auth_root").empty();
					$("#auth_root").append(data.message);
				} else {
					$("#err").empty();
					$("#err").append(data.message);
					setTimeout(() => {
						$("#err").empty();
					}, 2000);
				}
				$("#login").show();
				$("#auth").hide();
			}
		},
	});
}

function select_coop_handle(username, action) {
	$("#auth_root").empty();
	let url = "";
	if (action === 'login') {
		url = "auth/ajax_member_password";
	} else if (action === "forget_pass") {
		url = "auth/ajax_send_password_rest_lik";
	} else {
		return;
	}

    $.ajax({
			type: "get",
			url: base_url + url,
			data: { identity: username },
			dataType: "json",
			success: function (data) {
				if (data !== null) {
					if (data.status == "success") {
						$("#auth_root").empty();
						$("#auth_root").append(data.message);
					} 
				}
			},
		});
}
