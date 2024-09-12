var currency = new Intl.NumberFormat();

function copy_to_clipboard() {
	var copyText = document.getElementById("url");
	copyText.select();
	document.execCommand("copy");
	alert("Copied the text: " + copyText.value);
}

function file_upload_fix(id, file_label) {
	var file_path = document.getElementById(id).value;
	document.getElementById(file_label).innerHTML = file_path.split("\\")[2];
}

function show_recipient() {
	recip_type = $("#recip_type").val();
	if (recip_type !== "custom") {
		$("#recipients-box").fadeOut("slow");
		return;
	}

	if (recip_type === "custom") {
		$("#recipients-box").fadeIn("slow");
		return;
	}
}

function show_statement_type() {
	statement_type = $("#statement_type").val();
	if (statement_type === "loan") {
		$("#savings_type_box").hide();
		$("#loan_type_box").show();
		return;
	}

	if (statement_type === "savings") {
		$("#loan_type_box").hide();
		$("#savings_type_box").show();
		return;
	}
}
function change_user_group() {
	user_group = $("#user_group").val();
	if (user_group == "1") {
		$("#role_id_box").fadeIn();
		return;
	}else{
		$("#role_id_box").fadeOut();
		return;
	}
}

$(document).ready(function () {
	var max_fields = 200; //maximum input boxes allowed
	var wrapper = $(".input_fields_wrap"); //Fields wrapper
	var add_button = $(".add_field_button"); //Add button ID

	var x = 1; //initlal text box count
	$(add_button).click(function (e) {
		//on add input button click
		e.preventDefault();
		if (x < max_fields) {
			//max input box allowed
			x++; //text box increment
			$(wrapper).append(
				'<div class="row mt-2">\n\
<div class="form-group col-md-3"><input placeholder="Next of Kin Full Name" class="form-control" type="text" name="kin_name[]"/></div> \n\
<div class="form-group col-md-3"><input placeholder="Next of Kin Phone" class="fee_val form-control" type="text" name="kin_phone[]"/></div>\n\
<div class="form-group col-md-5"><input placeholder="Next of Kin Address" class="fee_val form-control" type="text" name="kin_address[]"/></div>\n\
<div class="form-group col-md-1"><button class="btn btn-sm btn-danger remove_field">x</button></div>\n\
</div>'
			); //add input box
		}
	});

	$(wrapper).on("click", ".remove_field", function (e) {
		//user click on remove text
		e.preventDefault();

		var d = $(this).parent("div");
		d.parent("div").remove();
		x--;
	});
	$(".close").click(function () {
		$("#err_box").hide();
	});
});

const wait_loader = (hide, show) => {
	document.getElementById(hide).style.display = "none";
	document.getElementById(show).style.display = "inline";
};

function generate_savings_record() {
	$("#hide_generate_savin_btn").hide();
	$("#wait_generate_savin_btn").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/get_savings_type",
		//        data: {id: id},
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "error") {
					$("#saving_notice_triger").click();
					$("#hide_generate_savin_btn").show();
					$("#wait_generate_savin_btn").hide();
				} else {
					$.ajax({
						type: "get",
						url: base_url + "migration/generate_savings_template",
						dataType: "json",
						success: function (data) {
							if (data !== null) {
								if (data.status === "success") {
									$("#savings_template_download_btn").attr(
										"href",
										base_url + data.message
									);
									$("#savings_template_download").show();
									$("#wait_generate_savin_btn").hide();
									$("#hide_generate_savin_btn").show();
								} else {
									$("#wait_generate_savin_btn").hide();
									$("#hide_generate_savin_btn").show();
								}
							}
						},
					});
				}
			}
		},
	});
}

function generate_loan_record() {
	$("#hide_generate_loan_btn").hide();
	$("#wait_generate_loan_btn").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/get_loan_type",
		//        data: {id: id},
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "error") {
					$("#loan_notice_triger").click();
					$("#hide_generate_loan_btn").show();
					$("#wait_generate_loan_btn").hide();
				} else {
					$.ajax({
						type: "get",
						url: base_url + "migration/generate_loan_template",
						dataType: "json",
						success: function (data) {
							if (data !== null) {
								if (data.status === "success") {
									$("#loan_template_download_btn").attr(
										"href",
										base_url + data.message
									);
									$("#loan_template_download").show();
									$("#wait_generate_loan_btn").hide();
									$("#hide_generate_loan_btn").show();
								} else {
									$("#wait_generate_loan_btn").hide();
									$("#hide_generate_loan_btn").show();
								}
							}
						},
					});
				}
			}
		},
	});
}

function generate_credit_sales_record() {
	$("#hide_generate_credit_sales_btn").hide();
	$("#wait_generate_credit_sales_btn").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/get_product_type",
		//        data: {id: id},
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "error") {
					$("#credit_sales_notice_triger").click();
					$("#hide_generate_credit_sales_btn").show();
					$("#wait_generate_credit_sales_btn").hide();
				} else {
					$.ajax({
						type: "get",
						url: base_url + "migration/generate_credit_sales_template",
						dataType: "json",
						success: function (data) {
							if (data !== null) {
								if (data.status === "success") {
									$("#credit_sales_template_download_btn").attr(
										"href",
										base_url + data.message
									);
									$("#credit_sales_template_download").show();
									$("#wait_generate_credit_sales_btn").hide();
									$("#hide_generate_credit_sales_btn").show();
								} else {
									$("#wait_generate_credit_sales_btn").hide();
									$("#hide_generate_credit_sales_btn").show();
								}
							}
						},
					});
				}
			}
		},
	});
}

function generate_savings_template() {
	$("#hide_generate_savin_btn").hide();
	$("#wait_generate_savin_btn").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/get_savings_type",
		//        data: {id: id},
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "error") {
					$("#saving_notice_triger").click();
					$("#hide_generate_savin_btn").show();
					$("#wait_generate_savin_btn").hide();
				} else {
					$.ajax({
						type: "get",
						url: base_url + "savings/generate_savings_template",
						dataType: "json",
						success: function (data) {
							if (data !== null) {
								if (data.status === "success") {
									$("#savings_template_download_btn").attr(
										"href",
										base_url + data.message
									);
									$("#savings_template_download").show();
									$("#wait_generate_savin_btn").hide();
									$("#hide_generate_savin_btn").show();
								} else {
									$("#wait_generate_savin_btn").hide();
									$("#hide_generate_savin_btn").show();
								}
							}
						},
					});
				}
			}
		},
	});
}

function generate_credit_sales_repayment_template() {
	loan_type = $("#loan_type_1").val();
	if (loan_type == "") {
		$("#error_text").text("Please Select Product Type");
		return $("#error_notify").modal("show");
	}

	$("#hide_generate_savin_btn").hide();
	$("#wait_generate_savin_btn").show();
	$.ajax({
		type: "get",
		data: { loan_type: loan_type },
		url: base_url + "creditsalesrepayment/generate_creditsales_template",
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#savings_template_download_btn").attr(
						"href",
						base_url + data.message
					);
					$("#savings_template_download").show();
					$("#wait_generate_savin_btn").hide();
					$("#hide_generate_savin_btn").show();
				} else {
					$("#wait_generate_savin_btn").hide();
					$("#hide_generate_savin_btn").show();
				}
			}
		},
	});
}

function generate_loan_repayment_template() {
	loan_type = $("#loan_type_1").val();
	if (loan_type == "") {
		$("#error_text").text("Please Select Loan Type");
		return $("#error_notify").modal("show");
	}

	$("#hide_generate_savin_btn").hide();
	$("#wait_generate_savin_btn").show();
	$.ajax({
		type: "get",
		data: { loan_type: loan_type },
		url: base_url + "loanrepayment/generate_loan_template",
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#savings_template_download_btn").attr(
						"href",
						base_url + data.message
					);
					$("#savings_template_download").show();
					$("#wait_generate_savin_btn").hide();
					$("#hide_generate_savin_btn").show();
				} else {
					$("#wait_generate_savin_btn").hide();
					$("#hide_generate_savin_btn").show();
				}
			}
		},
	});
}

function generate_loan_disbursement_template() {
	loan_type = $("#loan_type_1").val();
	if (loan_type == "") {
		$("#error_text").text("Please Select Loan Type");
		return $("#error_notify").modal("show");
	}

	$("#hide_generate_savin_btn").hide();
	$("#wait_generate_savin_btn").show();
	$.ajax({
		type: "get",
		data: { loan_type: loan_type },
		url: base_url + "loan/generate_disbursement_template",
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#savings_template_download_btn").attr(
						"href",
						base_url + data.message
					);
					$("#savings_template_download").show();
					$("#wait_generate_savin_btn").hide();
					$("#hide_generate_savin_btn").show();
				} else {
					$("#wait_generate_savin_btn").hide();
					$("#hide_generate_savin_btn").show();
				}
			}
		},
	});
}

function change_theme(color) {
	$.ajax({
		type: "get",
		url: base_url + "commonapi/change_theme",
		data: { color: color },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					location.reload();
				}
			}
		},
	});
}

function change_side_bar(status) {
	$.ajax({
		type: "get",
		url: base_url + "commonapi/change_side_bar",
		data: { status: status },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
			}
		},
	});
}

function member_approval(status, user_id) {
	$.ajax({
		type: "get",
		url: base_url + "registration/ajax_member_approval",
		data: { status: status, user_id: user_id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				console.log("successful");
			}
		},
	});
}

function product_status_change(status, id) {
	$.ajax({
		type: "get",
		url: base_url + "products/ajax_product_status_change",
		data: { status: status, id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				console.log("successful");
			}
		},
	});
}

function edit_role(id) {
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "users/ajax_get_role",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#id").val(data.message.id);
					$("#name").val(data.message.name);
					$("#description").val(data.message.description);
				}
			}
		},
	});
}

function edit_loan_type(id) {
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "categories/ajax_get_loan_type",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#id").val(data.message.id);
					$("#name").val(data.message.name);
					$("#rate").val(data.message.rate);
					$("#guarantor").val(data.message.guarantor);
					$("#min_month").val(data.message.min_month);
					$("#max_month").val(data.message.max_month);
					$("#calc_method").val(data.message.calc_method).change();
					$("#description").val(data.message.description);
				}
			}
		},
	});
}

function edit_savings_type(id) {
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "categories/ajax_get_savings_type",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#id").val(data.message.id);
					$("#name").val(data.message.name);
					$("#max_withdrawal").val(data.message.max_withdrawal);
					$("#description").val(data.message.description);
				}
			}
		},
	});
}

function edit_vendor(id) {
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "products/ajax_get_vendor",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#id").val(data.message.id);
					$("#name").val(data.message.name);
					$("#description").val(data.message.description);
				}
			}
		},
	});
}

function edit_product(id) {
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "products/ajax_get_product",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#id").val(data.message.id);
					$("#name").val(data.message.name);
					$("#stock").val(data.message.stock);
					$("#price").val(data.message.price);
					$("#product_type_id").val(data.message.product_type_id).change();
					$("#vendor_id").val(data.message.vendor_id).change();
					$("#description").val(data.message.description);
				}
			}
		},
	});
}

function edit_investment(id) {
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "investment/ajax_get_investment",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#id").val(data.message.id);
					$("#investment_type").val(data.message.investment_type);
					$("#amount").val(data.message.amount);
					$("#roi").val(data.message.roi);
					$("#rate").val(data.message.rate);
					$("#maturity_year").val(data.message.maturity_year);
					$("#start_date").val(data.message.start_date);
					$("#end_date").val(data.message.end_date);
					$("#description").val(data.message.description);
				}
			}
		},
	});
}

function edit_investment_type(id) {
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "categories/ajax_get_investment_type",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#id").val(data.message.id);
					$("#name").val(data.message.name);
					$("#description").val(data.message.description);
				}
			}
		},
	});
}

function edit_product_type(id) {
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "categories/ajax_get_product_type",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#id").val(data.message.id);
					$("#name").val(data.message.name);
					$("#rate").val(data.message.rate);
					$("#guarantor").val(data.message.guarantor);
					$("#min_month").val(data.message.min_month);
					$("#max_month").val(data.message.max_month);
					$("#calc_method").val(data.message.calc_method).change();
					$("#description").val(data.message.description);
					$("#max_purchaseable").val(data.message.max_purchaseable);

					if (data.message.is_market_product === 'yes') {
						$("#is_market_product").prop("checked", true);
					} else {
						$("#is_market_product").prop("checked", false);
					}

					if (data.message.auto_approval === "yes") {
						$("#auto_approval").prop("checked", true);
					} else {
						$("#auto_approval").prop("checked", false);
					}
				}
			}
		},
	});
}

function get_savings_info() {
	member_id = $("#member_id").val();
	savings_type = $("#savings_type").val();
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_get_savings_info",
		data: { member_id: member_id, savings_type: savings_type },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#saving-history").fadeIn(1500);
					$("#full_name").text(data.message.full_name);
					$("#wallet_bal").text(data.message.wallet_bal);
					$("#month_1").text(data.message.month);
					$("#year_1").text(data.message.year);
					$("#amount_1").text(data.message.amount);
					$("#savings_type").text(data.message.savings_types);
				} else {
					$("#saving-history").hide();
					$("#error_text").text(data.message);
					$("#error_notify").modal("show");
				}
			}
		},
	});
}

function get_loan_info() {
	member_id = $("#member_id").val();
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_get_loan_info",
		data: { member_id: member_id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#exixting-loans").fadeIn(1500);
					$("#full_name").text(data.message.full_name);
					$("#bal").text(data.message.bal);
					$("#total").text(data.message.total);
					$("#paid").text(data.message.paid);
					$("#balance").text(data.message.balance);
				} else {
					$("#exixting-loans").hide();
					$("#error_text").text(data.message);
					$("#error_notify").click();
				}
			}
			$("#spinner").hide();
		},
	});
}

function get_credit_sales_info() {
	member_id = $("#member_id").val();
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_get_credit_sales_info",
		data: { member_id: member_id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#exixting-loans").fadeIn(1500);
					$("#full_name").text(data.message.full_name);
					$("#bal").text(data.message.bal);
					$("#total").text(data.message.total);
					$("#paid").text(data.message.paid);
					$("#balance").text(data.message.balance);
				} else {
					$("#exixting-loans").hide();
					$("#error_text").text(data.message);
					$("#error_notify").click();
				}
			}
		},
	});
}

function get_withdrawal_info() {
	member_id = $("#member_id").val();
	savings_type = $("#savings_type").val();
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_get_withdrawal_info",
		data: { member_id: member_id, savings_type: savings_type },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#exixting-loans").fadeIn(1500);
					$("#full_name").text(data.message.full_name);
					$("#bal").text(data.message.bal);
					$("#total").text(data.message.total);
					$("#paid").text(data.message.paid);
					$("#balance").text(data.message.balance);
				} else {
					$("#exixting-loans").hide();
					$("#error_text").text(data.message);
					$("#error_notify").modal("show");
					$("#spinner").hide();
				}
			}
		},
	});
}

function preview_loan_schedule() {
	amount = $("#amount").val();
	tenure = parseInt($("#tenure").val());
	loan_type = $("#loan_type").val();

	if (amount == "") {
		return alert("Amount field is required");
	}
	if (isNaN(tenure) == true || tenure < 1) {
		return alert("Tenure field is required");
	}
	if (loan_type == "") {
		return alert("Loan type field is required");
	}
	$("#preview_loan_schedule_hide").hide();
	$("#preview_loan_schedule_show").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_preview_loan_schedule",
		data: { amount: amount, tenure: tenure, loan_type: loan_type },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#preview_loan_schedule_hide").show();
					$("#preview_loan_schedule_show").hide();
					$("#loan_type_name").text(data.message.loan_type);
					$("#principal").text(data.message.principal);
					$("#interest").text(data.message.interest);
					$("#total_due").text(data.message.total_due);
					$("#principal_due").text(data.message.principal_due);
					$("#interest_due").text(data.message.interest_due);
					$("#monthly_due").text(data.message.monthly_due);
					$("#tenure1").text(data.message.tenure + " month(s)");
					$("#display_schedule").click();
				}
			}
		},
	});
}

function preview_credit_ssales_schedule() {
	amount = $("#amount").val();
	tenure = parseInt($("#tenure").val());
	product_type = $("#product_type").val();

	if (amount == "") {
		return alert("Amount field is required");
	}
	if (isNaN(tenure) == true || tenure < 1) {
		return alert("Tenure field is required");
	}
	if (product_type == "") {
		return alert("Product type field is required");
	}
	$("#preview_loan_schedule_hide").hide();
	$("#preview_loan_schedule_show").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_preview_credit_sales_schedule",
		data: { amount: amount, tenure: tenure, product_type: product_type },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#preview_loan_schedule_hide").show();
					$("#preview_loan_schedule_show").hide();
					$("#product_type_name").text(data.message.product_type_name);
					$("#principal").text(data.message.principal);
					$("#interest").text(data.message.interest);
					$("#total_due").text(data.message.total_due);
					$("#principal_due").text(data.message.principal_due);
					$("#interest_due").text(data.message.interest_due);
					$("#monthly_due").text(data.message.monthly_due);
					$("#tenure1").text(data.message.tenure + " month(s)");
					$("#display_schedule").click();
				}
			}
		},
	});
}

function generate_loan_guarantor_field() {
	loan_type = $("#loan_type").val();
	$("#spinner1").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_generate_loan_guarantor_field",
		data: { loan_type: loan_type },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner1").hide();
					$("#guarantors-wraper").fadeIn(1500);
					$("#tenure").val(data.message.tenure);
					fields = "";
					fields_header_one =
						'<div class="col-12 guarantor-header-one"><label class=" text-danger"> ' +
						data.message.guarantors +
						" Guarantor required, Please fill appropriately</label></div>";
					fields_header_two =
						'<div class="col-12 guarantor-header-two"><label class=" text-danger">Guarantor NOT required</label></div>';
					for (i = 1; i <= parseInt(data.message.guarantors); i++) {
						fields +=
							'<div class="form-group guarantor-fields col-md-6 mt-2">\n\
<div class="input-group">\n\
<div class="input-group-prepend">\n\
<span class="input-group-text bg-primary text-white" id="basic-addon1">' +
							i +
							'</span>\n\
</div>\n\
<input type="text" class="form-control" required placeholder="Enter Gurantor Member ID" name=guarantor[] aria-describedby="basic-addon1">\n\
</div>\n\
</div>';
					}
					if (fields !== "") {
						$("div").remove(".guarantor-header-two");
						$("div").remove(".guarantor-header-one");
						$("div").remove(".guarantor-fields");
						$("#guarantors-wraper").append(fields_header_one);
						$("#guarantors-wraper").append(fields);
					} else {
						$("#guarantors-wraper").append(fields_header_two);
						$("div").remove(".guarantor-header-one");
						$("div").remove(".guarantor-fields");
					}
				} else {
					$("guarantors-wraper").hide();
					$("#error_text").text(data.message);
					$("#error_notify").click();
				}
			}
		},
	});
}

function generate_credit_sales_guarantor_field() {
	product_type = $("#product_type").val();
	$("#spinner1").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_generate_credit_sales_guarantor_field",
		data: { product_type: product_type },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner1").hide();
					$("#guarantors-wraper").fadeIn(1500);
					$("#tenure").val(data.message.tenure);
					fields = "";
					fields_header_one =
						'<div class="col-12 guarantor-header-one"><label class=" text-danger"> ' +
						data.message.guarantors +
						" Guarantor required, Please fill appropriately</label></div>";
					fields_header_two =
						'<div class="col-12 guarantor-header-two"><label class=" text-danger">Guarantor NOT required</label></div>';
					for (i = 1; i <= parseInt(data.message.guarantors); i++) {
						fields +=
							'<div class="form-group guarantor-fields col-md-6 mt-2">\n\
<div class="input-group">\n\
<div class="input-group-prepend">\n\
<span class="input-group-text bg-primary text-white" id="basic-addon1">' +
							i +
							'</span>\n\
</div>\n\
<input type="text" class="form-control" required placeholder="Enter Gurantor Member ID" name=guarantor[] aria-describedby="basic-addon1">\n\
</div>\n\
</div>';
					}
					if (fields !== "") {
						$("div").remove(".guarantor-header-two");
						$("div").remove(".guarantor-header-one");
						$("div").remove(".guarantor-fields");
						$("#guarantors-wraper").append(fields_header_one);
						$("#guarantors-wraper").append(fields);
					} else {
						$("#guarantors-wraper").append(fields_header_two);
						$("div").remove(".guarantor-header-one");
						$("div").remove(".guarantor-fields");
					}
				} else {
					$("guarantors-wraper").hide();
					$("#error_text").text(data.message);
					$("#error_notify").click();
				}
			}
		},
	});
}

function disburse_option(id) {
	$.ajax({
		type: "get",
		url: base_url + "loan/ajax_disburse_option",
		data: { id: id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#manual_disbursement").attr("href", data.message.url);
					$("#disburse_option").modal("show");
				} else {
					$("#error_notify").click();
				}
			}
		},
	});
}

function get_payment_break_down(obj) {
	amount = $("#amount").val();
	gate_way = obj.value;
	if (amount == "" || amount <= 0) {
		$("#error_text").text("Invalid amount");
		return $("#error_notify").modal("show");
	}
	$("#spinner").show();
	$.ajax({
		type: "get",
		url: base_url + "member/wallet/ajax_get_payment_break_down",
		data: { amount: amount, gate_way: gate_way },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#spinner").hide();
					$("#payment_breakdown").fadeIn("slow");
					$("#fee").text(data.message.fee);
					$("#total").text(data.message.total);
					$("#amount-one").text(data.message.amount);
				} else {
					$("#spinner").hide();
					$("#payment_breakdown").hide();
					$("#error_text").text(data.message);
					$("#error_notify").click();
				}
			}
		},
	});
}

function change_country() {
	country_id = $("#country").val();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_get_states_and_banks",
		data: { country_id: country_id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#state").empty();
					var x;
					for (x in data.body) {
						
						$("#state").append(
							"<option value=" +
								data.body[x].id +
								">" +
								data.body[x].name +
								"</option>"
						);
					}
					$("#bank_id").empty();
					var y;
					for (y in data.banks) {
						
						$("#bank_id").append(
							"<option value=" +
								data.banks[y].id +
								">" +
								data.banks[y].bank_name +
								"</option>"
						);
					}
				}
			}
		},
	});
}

function change_state() {
	state_id = $("#state").val();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_get_city",
		data: { state_id: state_id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					var x;
					$("#city").empty();
					for (x in data.body) {
						$("#city").append(
							"<option value=" +
								data.body[x].id +
								">" +
								data.body[x].name +
								"</option>"
						);
					}
				}
			}
		},
	});
}
function licence_component(licence_cat_id) {
	$(".fancy-loader").modal("show");
	$.ajax({
		type: "get",
		url: base_url + "licence/ajax_licence_component",
		data: { licence_cat_id: licence_cat_id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#component").empty();
					$("#component").append(data.message);
					$("#payment_break_down").modal("show");
				}
				setTimeout(() => {
					$(".fancy-loader").modal("hide");
				},500)
			}
			
		},
	});
}

function savings_bal() {
	$(".spinner-border").show();
	$.ajax({
		type: "get",
		url: base_url + "dashboard/ajax_savings_bal",
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#component").empty();
					$("#component").append(data.message);
					$("#savings_bal").modal("show");
				}
			}
			$(".spinner-border").hide();
		},
	});
}

function loan_bal() {
	$(".spinner-border").show();
	$.ajax({
		type: "get",
		url: base_url + "dashboard/ajax_loan_bal",
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					$("#component").empty();
					$("#component").append(data.message);
					$("#loan_bal").modal("show");
				}
			}
			$(".spinner-border").hide();
		},
	});
}

function process_order_input(obj, element, products) {
	var val = $("#" + element).val();

	if (val > 0) {
		var product = products.split("}");
		const item = (param, val) => {
			return `<div class="shadow-sm rounded p-2 mb-2" id="${element}item">
                    <div>
                        <p class="text-body font-weight-semibold float-left">${
													param[2]
												}</p>
                        <a href="javascript:remove_order_item('${element}')" class="float-right text-danger" title="remove"> <i class="mdi mdi-trash-can"> </i> </a>
                            <span class="clearfix"></span>
                    </div>
                        <div>
                            <span class="float-left">${val} x ${currency.format(
				param[1]
			)}</span> 
                            <strong class="float-right total_val">${currency.format(
															parseInt(val) * parseFloat(param[1])
														)}</strong>
                            <span class="clearfix"></span>
                        </div>
                    </div>`;
		};

		obj.style.display = "none";
		$("#item-box").append(item(product, $("#" + element).val()));
		total_order_val();
		set_order_data(product[0] + "." + val + "|");
	} else {
		alert("Enter the quantity");
	}
}

function total_order_val() {
	let d = document.getElementsByClassName("total_val");
	var i;
	var amount = 0;
	for (i = 0; i < d.length; i++) {
		amount += parseFloat(d[i].innerHTML.replace(/,/g, ""));
	}

	$("#total_val").text(currency.format(amount));
}

function set_order_data(val) {
	let order_details = $("#order_details").val() + "" + val;
	$("#order_details").val(order_details);
}

function remove_order_item(element) {
	document.querySelector("#btn" + element).style.display = "inline";
	$("#" + element).val("");
	$("#" + element + "item").remove();
	total_order_val();
}

function set_renewal_total_amount() {
	let member = parseInt($("#member").val());
	let amount = parseFloat($("#amount").val());
	let total = member * amount;
	if (Number.isNaN(total)) {
		total = 0;
	}
    
	$("#total_amount").val(total);
}

function enable_2fa(status) {
	$.ajax({
		type: "get",
		url: base_url + "member/profile/ajax_enable_2fa",
		data: { status: status},
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				console.log("successful");
			}
		},
	});
}
function update_preferences(status, title) {
	$.ajax({
		type: "get",
		url: base_url + "member/profile/ajax_preferences",
		data: { status: status, title: title },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				console.log("successful");
			}
		},
	});
}

function ajax_enable_auto_posting(status, e) {
	$.ajax({
		type: "get",
		url: base_url + "accounting/ajax_enable_auto_posting",
		data: { status: status },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
				} else {
					e.checked = false;
					$("#error_text").text(data.message);
					$("#error_notify").modal("show");
				}
			}
		},
	});
}

function member_info() {
	let member_id = $("#member_id").val();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_member_info",
		data: { member_id: member_id },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status =='success') {
					$("#name").val(data.message.first_name + " " + data.message.last_name);
				} else {
					// $(".error").text(data.message);
				}
			} 
		},
	});
}

function CheckPassword(inputtxt) {
	var decimal =
		/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;
	if (inputtxt.value.match(decimal)) {
		alert("Correct, try another...");
		return true;
	} else {
		alert("Wrong...!");
		return false;
	}
} 

function member_info_live_search() {
	let input = $("#member_id").val();
	if (input.length < 2) {
		  $("#result").empty();
		return;
	}
	$(".spinner-border-sm").show();
	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_member_info_live_search",
		data: { input: input },
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status == "success") {
					$("#result").empty();
					$("#result").append(data.message);
				} else {
					$("#result").empty();
					$("#result").append(data.message);
				}
			}
			$(".spinner-border-sm").hide();
		},
	});
}

function set_search_value(e, callback) {
	$("#member_id").val(e.value);
	$("#result").empty();
	callback();
}

function setGuarantor(guarantor) {
	document.querySelector("#loan_guarantor_id").value = guarantor;
}

function refinance(loan_type, loan_id, e) {
	amount = $("#principal").val();
	tenure = parseInt($("#tenure").val());
	rate = parseFloat($("#rate").val());

	if (amount == "") {
		return alert("Principal field is required");
	}

	if (isNaN(rate) == true || rate < 1) {
		e.checked = false;
		$("#amount").val("");
		return alert("New rate field is required");
	}

	if (isNaN(tenure) == true || tenure < 1) {
		e.checked = false;
		$("#amount").val("");
		return alert("Tenure field is required");
	}

	$.ajax({
		type: "get",
		url: base_url + "commonapi/ajax_refinance_loan_schedule",
		data: {
			amount: amount,
			tenure: tenure,
			loan_type: loan_type,
			rate: rate,
			loan_id: loan_id,
		},
		dataType: "json",
		success: function (data) {
			if (data !== null) {
				if (data.status === "success") {
					if (e.id === "not-within-acc-year") {
						$("#refinance_data").val(JSON.stringify(data.message));
						$("#amount").val(data.message.old_balance);
					} else {
						$("#refinance_data").val(JSON.stringify(data.message));
						$("#amount").val(data.message.new_balance);
					}
				}
			}
		},
	});
}

//trigers migration error notification on the migration page
(function () {
	setTimeout(function () {
		$("#migration-error").modal("show");
	},1500)
})();

//fancy loader

(function () {
	let a = document.querySelectorAll('[data-fancy]')
	a.forEach((e) => {
		e.addEventListener('click', (e) => {
			if(e.defaultPrevented === false) {
				$(".fancy-loader").modal("show");
			};
			
		});
	})
})();

function page_search(elm) {
	const process = function () {
		if (elm.value === "" || elm.value === " ") {
			return {};
		}

		let a = document.querySelectorAll("[data-fancy]");
		let item = {};
		let result = {};
		a.forEach((e) => {
			item = { ...item, [e.href]: e.innerText.trim() };
		});

		for (let key in item) {
			if (item[key].toLowerCase().indexOf(elm.value.toLowerCase()) >= 0) {
				result = { ...result, [key]: item[key] };
			}
		}
		return result;
	}
	let result = process();
	let total = 0
	if (result) {
		let result_final = "";
		for (let key in result) {
			total++
			result_final += `<a href="${key}" class="dropdown-item notify-item">
					<i class="uil-notes font-16 mr-1"></i>
					<span>${result[key]}</span>
				</a>`;	
		}
		$("#page-search").empty();
		$("#page-search").append(result_final);
		$('#total-result').text(total)
	}
}

function extend_licence(e, calc_month, amount) {
	$('#total-two').text(e.value * calc_month * amount);
	$("#total").text(e.value + " x " + calc_month + " x " + amount);
}


